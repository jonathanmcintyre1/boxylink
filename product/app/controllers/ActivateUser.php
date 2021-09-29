<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Database\Database;
use Altum\Logger;

class ActivateUser extends Controller {

    public function index() {

        $md5email = isset($_GET['email']) ? $_GET['email'] : null;
        $email_activation_code = isset($_GET['email_activation_code']) ? $_GET['email_activation_code'] : null;
        $type = isset($_GET['type']) && in_array($_GET['type'], ['user_activation', 'user_pending_email']) ? $_GET['type'] : 'user_activation';

        $redirect = 'dashboard';
        if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
            $redirect = Database::clean_string($redirect);
        }

        if(!$md5email || !$email_activation_code) redirect();

        /* Check if the activation code is correct */
        switch($type) {
            case 'user_activation':

                if(!$user = db()->where('email_activation_code', $email_activation_code)->getOne('users', ['user_id', 'email', 'name'])) {
                    redirect();
                }

                if(md5($user->email) != $md5email) {
                    redirect();
                }

                $last_user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);

                /* Activate the account and reset the email_activation_code */
                db()->where('user_id', $user->user_id)->update('users', [
                    'active' => 1,
                    'email_activation_code' => null,
                    'last_user_agent' => $last_user_agent,
                    'total_logins' => db()->inc()
                ]);

                /* Send webhook notification if needed */
                if(settings()->webhooks->user_new) {

                    \Unirest\Request::post(settings()->webhooks->user_new, [], [
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'name' => $user->name
                    ]);

                }

                Logger::users($user->user_id, 'activate.success');

                /* Login and set a successful message */
                $_SESSION['user_id'] = $user->user_id;

                /* Set a nice success message */
                Alerts::add_success(language()->activate_user->user_activation);

                Logger::users($user->user_id, 'login.success');

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

                redirect($redirect);

                break;

            case 'user_pending_email':

                if(!$user = db()->where('email_activation_code', $email_activation_code)->getOne('users', ['user_id', 'pending_email'])) {
                    redirect();
                }

                if(md5($user->pending_email) != $md5email) {
                    redirect();
                }

                /* Confirm the new email address and reset the email_activation_code */
                db()->where('user_id', $user->user_id)->update('users', [
                    'email' => $user->pending_email,
                    'pending_email' => null,
                    'email_activation_code' => null,
                ]);

                Logger::users($user->user_id, 'email_change.success');

                /* Set a nice success message */
                Alerts::add_success(language()->activate_user->user_pending_email);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

                redirect('account');

                break;
        }

    }

}
