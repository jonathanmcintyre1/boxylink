<?php

namespace Altum\Models;

use Altum\Database\Database;
use Altum\Logger;
use MaxMind\Db\Reader;

class User extends Model {

    public function get_user_by_user_id($user_id) {

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('user?user_id=' . $user_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $data = Database::get('*', 'users', ['user_id' => $user_id]);

            if($data) {

                /* Parse the users plan settings */
                $data->plan_settings = json_decode($data->plan_settings);

                /* Parse billing details if existing */
                $data->billing = json_decode($data->billing);

                /* Save to cache */
                \Altum\Cache::$adapter->save(
                    $cache_instance->set($data)->expiresAfter(86400)->addTag('users')->addTag('user_id=' . $data->user_id)
                );
            }

        } else {

            /* Get cache */
            $data = $cache_instance->get();

        }

        return $data;
    }

    public function get_user_by_email_and_token_code($email, $token_code) {

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('user?email=' . md5($email) . '&token_code=' . $token_code);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $data = Database::get('*', 'users', ['email' => $email, 'token_code' => $token_code]);

            if($data) {

                /* Parse the users plan settings */
                $data->plan_settings = json_decode($data->plan_settings);

                /* Parse billing details if existing */
                $data->billing = json_decode($data->billing);

                /* Save to cache */
                \Altum\Cache::$adapter->save(
                    $cache_instance->set($data)->expiresAfter(86400)->addTag('users')->addTag('user_id=' . $data->user_id)
                );
            }

        } else {

            /* Get cache */
            $data = $cache_instance->get();

        }

        return $data;
    }

    public function delete($user_id) {

        /* Cancel his active subscriptions if active */
        $this->cancel_subscription($user_id);

        /* Send webhook notification if needed */
        if($this->settings->webhooks->user_delete) {

            $user = Database::get(['user_id', 'email', 'name'], 'users', ['user_id' => $user_id]);

            \Unirest\Request::post($this->settings->webhooks->user_delete, [], [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'name' => $user->name
            ]);

        }

        /* Get all the available biolinks and iterate over them to delete the stored images */
        $result = Database::$database->query("SELECT `settings` FROM `links` WHERE `user_id` = {$user_id} AND `type` = 'biolink' AND `subtype` = 'base'");
        while($row = $result->fetch_object()) {
            $row->settings = json_decode($row->settings);

            /* Delete current avatar */
            if(!empty($row->settings->image) && file_exists(UPLOADS_PATH . 'avatars/' . $row->settings->image)) {
                unlink(UPLOADS_PATH . 'avatars/' . $row->settings->image);
            }

            /* Delete current background */
            if(is_string($row->settings->background) && !empty($row->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $row->settings->background)) {
                unlink(UPLOADS_PATH . 'backgrounds/' . $row->settings->background);
            }

        }

        /* Get all the available biolinks and iterate over them to delete the stored images */
        $result = Database::$database->query("SELECT `subtype`, `settings` FROM `links` WHERE `user_id` = {$user_id} AND `type` = 'biolink' AND `subtype` IN ('image', 'image_grid', 'link')");
        while($row = $result->fetch_object()) {
            $row->settings = json_decode($row->settings);

            /* Delete current image */
            if(in_array($row->subtype, ['image', 'image_grid'])) {
                if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'block_images/' . $link->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $link->settings->image);
                }
            }

            if(in_array($row->subtype, ['link'])) {
                if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $link->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $link->settings->image);
                }
            }
        }

        /* Delete the record from the database */
        Database::$database->query("DELETE FROM `users` WHERE `user_id` = {$user_id}");

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user_id);

    }

    public function update_last_activity($user_id) {

        Database::update('users', ['last_activity' => \Altum\Date::$date], ['user_id' => $user_id]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user_id);

    }

    public function create(
        $email = '',
        $raw_password = '',
        $name = '',
        $active = 0,
        $email_activation_code = null,
        $facebook_id = null,
        $plan_id = 'free',
        $plan_settings = '',
        $plan_expiration_date = null,
        $timezone = 'UTC',
        $is_admin_created = false
    ) {

        /* Define some needed variables */
        $password = password_hash($raw_password, PASSWORD_DEFAULT);
        $total_logins = $active == '1' && !$is_admin_created ? 1 : 0;
        $plan_expiration_date = $plan_expiration_date ?? \Altum\Date::$date;
        $plan_trial_done = $plan_id == 'trial' ? 1 : 0;
        $language = \Altum\Language::$default_language;
        $billing = json_encode(['type' => 'personal', 'name' => '', 'address' => '', 'city' => '', 'county' => '', 'zip' => '', 'country' => '', 'phone' => '', 'tax_id' => '',]);
        $api_key = md5($email . microtime() . microtime());
        $ip = $is_admin_created ? null : get_ip();
        $maxmind = $is_admin_created ? null : (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get($ip);
        $country = $maxmind ? $maxmind['country']['iso_code'] : null;
        $user_agent = $is_admin_created ? null : Database::clean_string($_SERVER['HTTP_USER_AGENT']);

        /* Add the user to the database */
        $stmt = Database::$database->prepare("
            INSERT INTO 
                `users` 
                (
                    `password`,
                    `email`,
                    `name`,
                    `billing`,
                    `api_key`,
                    `facebook_id`,
                    `email_activation_code`,
                    `plan_id`,
                    `plan_expiration_date`,
                    `plan_settings`,
                    `plan_trial_done`,
                    `language`,
                    `timezone`,
                    `active`,
                    `date`,
                    `ip`,
                    `country`,
                    `last_user_agent`,
                    `total_logins`
                ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ");
        $stmt->bind_param(
            'sssssssssssssssssss',
            $password,
            $email,
            $name,
            $billing,
            $api_key,
            $facebook_id,
            $email_activation_code,
            $plan_id,
            $plan_expiration_date,
            $plan_settings,
            $plan_trial_done,
            $language,
            $timezone,
            $active,
            \Altum\Date::$date,
            $ip,
            $country,
            $user_agent,
            $total_logins
        );
        $stmt->execute();
        $registered_user_id = $stmt->insert_id;
        $stmt->close();

        return $registered_user_id;
    }

    /*
    * Function to update a user with more details on a login action
    */
    public function login_aftermath_update($user_id) {

        $ip = get_ip();
        $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get($ip);
        $country = $maxmind ? $maxmind['country']['iso_code'] : null;
        $user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);

        $stmt = Database::$database->prepare("UPDATE `users` SET `ip` = ?, `country` = ?, `last_user_agent` = ?, `total_logins` = `total_logins` + 1 WHERE `user_id` = {$user_id}");
        $stmt->bind_param('sss', $ip, $country, $user_agent);
        $stmt->execute();
        $stmt->close();

        Logger::users($user_id, 'login.success');

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user_id);

    }

    /*
     * Needs to have access to the Settings and the User variable, or pass in the user_id variable
     */
    public function cancel_subscription($user_id = false) {

        if(!isset($this->settings)) {
            throw new \Exception('Model needs to have access to the "settings" variable.');
        }

        if(!isset($this->user) && !$user_id) {
            throw new \Exception('Model needs to have access to the "user" variable or pass in the $user_in.');
        }

        if($user_id) {
            $this->user = Database::get(['user_id', 'payment_subscription_id'], 'users', ['user_id' => $user_id]);
        }

        if(empty($this->user->payment_subscription_id)) {
            return true;
        }

        $data = explode('###', $this->user->payment_subscription_id);
        $type = strtolower($data[0]);
        $subscription_id = $data[1];

        switch($type) {
            case 'stripe':

                /* Initiate Stripe */
                \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

                /* Cancel the Stripe Subscription */
                $subscription = \Stripe\Subscription::retrieve($subscription_id);
                $subscription->cancel();

                break;

            case 'paypal':

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

                /* Create an Agreement State Descriptor, explaining the reason to suspend. */
                $agreement_state_descriptior = new \PayPal\Api\AgreementStateDescriptor();
                $agreement_state_descriptior->setNote('Suspending the agreement');

                /* Get details about the executed agreement */
                $agreement = \PayPal\Api\Agreement::get($subscription_id, $paypal);

                /* Suspend */
                $agreement->suspend($agreement_state_descriptior, $paypal);


                break;
        }

        Database::$database->query("UPDATE `users` SET `payment_subscription_id` = '' WHERE `user_id` = {$this->user->user_id}");

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user_id);

    }

}
