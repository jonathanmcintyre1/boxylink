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
use Altum\Date;
use Altum\Meta;
use Altum\Middlewares\Csrf;
use Altum\Models\User;
use Altum\Response;
use Altum\Routing\Router;
use Altum\Title;
use MaxMind\Db\Reader;

class Link extends Controller {
    public $link = null;
    public $type;
    public $user;
    public $is_preview = false;

    public function index() {

        /* Detect if access to url comes from id linking or url alias */
        if(isset(Router::$data['link'])) {
            $this->link = Router::$data['link'];
            $this->type = 'link';
        } else {

            if(isset($_GET['link_id'])) {
                $link_id = (int) $_GET['link_id'];
                $this->link = db()->where('link_id', $link_id)->getOne('links');
                $this->type = 'link';
            }

            if(isset($_GET['biolink_block_id'])) {
                $biolink_block_id = (int) $_GET['biolink_block_id'];
                $this->link = db()->where('biolink_block_id', $biolink_block_id)->getOne('biolinks_blocks');
                $this->type = 'biolink_block';
            }

        }
        $domain_id = isset(Router::$data['domain']) ? Router::$data['domain']->domain_id : 0;

        if(!$this->link) {
            redirect();
        }

        /* If a preview is asked for, make sure it is correct */
        if(isset($_GET['preview']) && $_GET['preview'] == md5($this->link->user_id)) {
            $this->is_preview = true;
        }

        /* Make sure the link is enabled */
        if(!$this->link->is_enabled && !$this->is_preview) {
            redirect();
        }

        /* Get the owner details */
        $this->user = (new User())->get_user_by_user_id($this->link->user_id);

        /* Make sure to check if the user is active */
        if($this->user->active != 1) {
            redirect();
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($this->user);

        /* Parse the settings */
        $this->link->settings = json_decode($this->link->settings);
        $this->link->pixels_ids = json_decode($this->link->pixels_ids ?? '[]');

        /* Check if its an expired link based on scheduling / total link clicks */
        if($this->user->plan_settings->temporary_url_is_enabled) {

            /* Check for temporary clicks */
            if(isset($this->link->settings->clicks_limit) && $this->link->settings->clicks_limit) {
                $current_clicks = db()->where('link_id', $this->link->link_id)->getValue('links', 'clicks');
            }

            if (
                (
                    !empty($this->link->start_date) && !empty($this->link->end_date) &&
                    (
                        \Altum\Date::get('', null) < \Altum\Date::get($this->link->start_date, null, \Altum\Date::$default_timezone) ||
                        \Altum\Date::get('', null) > \Altum\Date::get($this->link->end_date, null, \Altum\Date::$default_timezone)
                    )
                )
                || (isset($current_clicks) && $current_clicks >= $this->link->settings->clicks_limit)
            ) {
                if($this->link->settings->expiration_url) {
                    header('Location: ' . $this->link->settings->expiration_url, true, 301);
                    die();
                } else {
                    redirect();
                }
            }
        }

        /* Determine the actual full url */
        if($this->type == 'link') {
            $this->link->full_url = $domain_id && !isset($_GET['link_id']) ? Router::$data['domain']->scheme . Router::$data['domain']->host . '/' . $this->link->url : SITE_URL . $this->link->url;
        } else {
            $this->link->full_url = SITE_URL . 'l/link?biolink_block_id=' . $this->link->biolink_block_id;
        }

        /* Check for vcard download link */
        if($this->link->type == 'vcard') {
            $vcard = new \JeroenDesloovere\VCard\VCard();

            $vcard->addName($this->link->settings->last_name, $this->link->settings->first_name);
            $vcard->addAddress(null, null, $this->link->settings->street, $this->link->settings->city, $this->link->settings->region, $this->link->settings->zip, $this->link->settings->country);
            $vcard->addPhoneNumber($this->link->settings->phone);
            $vcard->addEmail($this->link->settings->email);
            $vcard->addURL($this->link->settings->website);
            $vcard->addCompany($this->link->settings->company);
            $vcard->addNote($this->link->settings->note);

            $vcard->download();

            die();
        }

        /* Check if the user has access to the link */
        $has_access = !$this->link->settings->password || ($this->link->settings->password && isset($_COOKIE['link_password_' . $this->link->link_id]) && $_COOKIE['link_password_' . $this->link->link_id] == $this->link->settings->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->password) {
            $has_access = true;
        }

        /* Check if the password form is submitted */
        if(!$has_access && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'password') {

            /* Check for any errors */
            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!password_verify($_POST['password'], $this->link->settings->password)) {
                Alerts::add_field_error('password', language()->link->password->error_message);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Set a cookie */
                setcookie('link_password_' . $this->link->link_id, $this->link->settings->password, time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();

            }

        }

        /* Check if the user has access to the link */
        $can_see_content = !$this->link->settings->sensitive_content || ($this->link->settings->sensitive_content && isset($_COOKIE['link_sensitive_content_' . $this->link->link_id]));

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->sensitive_content) {
            $can_see_content = true;
        }

        /* Check if the password form is submitted */
        if(!$can_see_content && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'sensitive_content') {

            /* Check for any errors */
            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Set a cookie */
                setcookie('link_sensitive_content_' . $this->link->link_id, 'true', time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();

            }

        }

        /* Display the password form */
        if(!$has_access && !isset($_GET['preview'])) {

            /* Set a custom title */
            Title::set(language()->link->password->title);

            /* Main View */
            $view = new \Altum\Views\View('l/partials/password', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else if(!$can_see_content && !isset($_GET['preview'])) {

            /* Set a custom title */
            Title::set(language()->link->sensitive_content->title);

            /* Main View */
            $view = new \Altum\Views\View('l/partials/sensitive_content', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else {

            $this->create_statistics();

            /* Check what to do next */
            if($this->link->type == 'biolink') {
                $this->process_biolink();
            } else {
                $this->process_redirect();
            }

        }

    }

    private function create_statistics() {

        $cookie_name = 's_statistics_' . ($this->type == 'link' ? $this->link->link_id : $this->link->biolink_block_id);

        if(isset($_COOKIE[$cookie_name]) && (int) $_COOKIE[$cookie_name] >= 3) {
            return;
        }

        if(isset($_GET['preview'])) {
            return;
        }

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        /* Detect extra details about the user */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
        $is_unique = isset($_COOKIE[$cookie_name]) ? 0 : 1;

        /* Detect the location */
        try {
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
        } catch(\Exception $exception) {
            /* :) */
        }
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

        /* Process referrer */
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

        if(!isset($referrer)) {
            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if the referrer comes from the same location */
        if($this->type == 'link' && isset($referrer) && isset($referrer['host']) && $referrer['host'] == parse_url($this->link->full_url)['host']) {
            $is_unique = 0;

            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if referrer actually comes from the QR code */
        if(isset($_GET['referrer']) && $_GET['referrer'] == 'qr') {
            $referrer = [
                'host' => 'qr',
                'path' => null
            ];
        }

        $utm_source = $_GET['utm_source'] ?? null;
        $utm_medium = $_GET['utm_medium'] ?? null;
        $utm_campaign = $_GET['utm_campaign'] ?? null;

        /* Insert the log */
        db()->insert('track_links', [
            'user_id' => $this->user->user_id,
            'link_id' => $this->type == 'link' ? $this->link->link_id : null,
            'biolink_block_id' => $this->type == 'biolink_block' ? $this->link->biolink_block_id : null,
            'country_code' => $country_code,
            'city_name' => $city_name,
            'os_name' => $os_name,
            'browser_name' => $browser_name,
            'referrer_host' => $referrer['host'],
            'referrer_path' => $referrer['path'],
            'device_type' => $device_type,
            'browser_language' => $browser_language,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'is_unique' => $is_unique,
            'datetime' => Date::$date
        ]);

        /* Add the unique hit to the link table as well */
        if($this->type == 'link') {
            db()->where('link_id', $this->link->link_id)->update('links', ['clicks' => db()->inc()]);
        } else {
            db()->where('biolink_block_id', $this->link->biolink_block_id)->update('biolinks_blocks', ['clicks' => db()->inc()]);
        }

        /* Set cookie to try and avoid multiple entrances */
        $cookie_new_value = isset($_COOKIE[$cookie_name]) ? (int) $_COOKIE[$cookie_name] + 1 : 0;
        setcookie($cookie_name, (int) $cookie_new_value, time()+60*60*24*1);
    }

    public function process_biolink() {

        /* Check for a leap link */
        if($this->link->settings->leap_link && $this->user->plan_settings->leap_link && !isset($_GET['preview'])) {
            $this->redirect_to($this->link->settings->leap_link);
            return;
        }

        /* Get all the links inside of the biolink */
        $cache_instance = \Altum\Cache::$adapter->getItem('biolink_links_' . $this->link->link_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            $result = database()->query("SELECT * FROM `biolinks_blocks` WHERE `link_id` = {$this->link->link_id} AND `is_enabled` = 1 ORDER BY `order` ASC");
            $biolink_blocks = [];

            while($row = $result->fetch_object()) {
                $biolink_blocks[] = $row;
            }

            \Altum\Cache::$adapter->save($cache_instance->set($biolink_blocks)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('biolinks_links_user_' . $this->link->user_id));

        } else {

            /* Get cache */
            $biolink_blocks = $cache_instance->get();

        }

        /* Default basic title */
        Title::set($this->link->url);

        /* Set the meta tags */
        if($this->user->plan_settings->seo) {
            if($this->link->settings->seo->title) Title::set($this->link->settings->seo->title, true);
            Meta::set_description(string_truncate($this->link->settings->seo->meta_description, 200));
            Meta::set_social_url($this->link->full_url);
            Meta::set_social_title($this->link->settings->seo->title);
            Meta::set_social_description(string_truncate($this->link->settings->seo->meta_description, 200));
            Meta::set_social_image(!empty($this->link->settings->seo->image) ? UPLOADS_FULL_URL . 'block_images/' . $this->link->settings->seo->image : null);
        }

        if(count($this->link->pixels_ids)) {
            /* Get the needed pixels */
            $pixels = (new \Altum\Models\Pixel())->get_pixels_by_pixels_ids($this->link->pixels_ids);

            /* Prepare the pixels view */
            $pixels_view = new \Altum\Views\View('l/partials/pixels');
            $this->add_view_content('pixels', $pixels_view->run(['pixels' => $pixels]));
        }

        /* Prepare the View */
        $view_content = \Altum\Link::get_biolink($this, $this->link, $this->user, $biolink_blocks);

        $this->add_view_content('content', $view_content);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        /* Check for targeting */
        if($this->link->settings->targeting_type == 'country_code') {
            /* Detect the location */
            try {
                $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get(get_ip());
            } catch(\Exception $exception) {
                /* :) */
            }
            $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($country_code == $value->key) {
                    $this->redirect_to($value->value);
                }
            }
        }

        if($this->link->settings->targeting_type == 'device_type') {
            $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($device_type == $value->key) {
                    $this->redirect_to($value->value);
                }
            }
        }

        if($this->link->settings->targeting_type == 'browser_language') {
            $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                if($browser_language == $value->key) {
                    $this->redirect_to($value->value);
                }
            }
        }

        if($this->link->settings->targeting_type == 'rotation') {
            $total_chances = 0;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                $total_chances += $value->key;
            }

            $chosen_winner = rand(0, $total_chances);

            $start = 0;
            $end = 0;

            foreach($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                $end += $value->key;

                if($chosen_winner >= $start && $chosen_winner <= $end) {
                    $this->redirect_to($value->value);
                }

                $start += $value->key;
            }
        }

        /* :) */
        $this->redirect_to($this->link->location_url);
    }

    private function redirect_to($location_url) {
        if(count($this->link->pixels_ids)) {

            /* Get the needed pixels */
            $pixels = (new \Altum\Models\Pixel())->get_pixels_by_pixels_ids($this->link->pixels_ids);

            /* Prepare the pixels view */
            $pixels_view = new \Altum\Views\View('l/partials/pixels');
            $this->add_view_content('pixels', $pixels_view->run(['pixels' => $pixels]));

            /* Prepare the view */
            $pixels_redirect_wrapper = new \Altum\Views\View('l/pixels_redirect_wrapper', (array) $this);
            echo $pixels_redirect_wrapper->run(['location_url' => $location_url]);
            die();

        } else {
            header('Location: ' . $location_url, true, 301); die();
        }
    }

    public function mail() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['email'] = mb_substr(trim(Database::clean_string($_POST['email'])), 0, 320);
        $_POST['name'] = mb_substr(trim(Database::clean_string($_POST['name'])), 0, 256);

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('type', 'mail')->getOne('biolinks_blocks');

        if($biolink_block) {
            $biolink_block->settings = json_decode($biolink_block->settings);

            /* Send the webhook */
            if($biolink_block->settings->webhook_url) {
                $body = \Unirest\Request\Body::form([
                    'email' => $_POST['email'],
                    'name' => $_POST['name'],
                ]);

                $response = \Unirest\Request::post($biolink_block->settings->webhook_url, [], $body);
            }

            /* Send the email to mailchimp */
            if($biolink_block->settings->mailchimp_api && $biolink_block->settings->mailchimp_api_list) {

                /* Check the mailchimp api list and get data */
                $explode = explode('-', $biolink_block->settings->mailchimp_api);

                if(count($explode) < 2) {
                    die();
                }

                $dc = $explode[1];
                $url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $biolink_block->settings->mailchimp_api_list . '/members';

                /* Try to subscribe the user to mailchimp list */
                \Unirest\Request::auth('altum', $biolink_block->settings->mailchimp_api);

                $body = \Unirest\Request\Body::json([
                    'email_address' => $_POST['email'],
                    'status' => 'subscribed',
                    'merge_fields' => [
                        'FNAME' => $_POST['name']
                    ],
                ]);

                \Unirest\Request::post(
                    $url,
                    [],
                    $body
                );

            }

            Response::json($biolink_block->settings->success_text, 'success', ['thank_you_url' => $biolink_block->settings->thank_you_url]);
        }
    }

}
