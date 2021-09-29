<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\Settings;
use Altum\Models\User;
use Altum\Routing\Router;

class App {

    protected $database;

    public function __construct() {

        /* Connect to the database */
        Database\Database::initialize();

        /* Initialize caching system */
        Cache::initialize();

        /* Initiate the Language system */
        Language::initialize(APP_PATH . 'languages/');

        /* Initiate the plugin system */
        Plugin::initialize();

        /* Parse the URL parameters */
        Router::parse_url();

        /* Parse the potential language url */
        Router::parse_language();

        /* Handle the controller */
        Router::parse_controller();

        /* Create a new instance of the controller */
        $controller = Router::get_controller(Router::$controller, Router::$path);

        /* Process the method and get it */
        $method = Router::parse_method($controller);

        /* Get the remaining params */
        $params = Router::get_params();

        /* Check for Preflight requests for the tracking of submissions from biolink pages */
        if(in_array(Router::$controller, ['Link'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            /* Check if preflight request */
            if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') die();
        }

        /* Get the website settings */
        $settings = (new Settings())->get();
        \Altum\Settings::initialize($settings);

        /* Initiate the Language system with the default language */
        Language::set_default($settings->default_language);

        /* Set the default theme style */
        ThemeStyle::set_default($settings->default_theme_style);

        /* Initiate the Title system */
        Title::initialize($settings->title);
        Meta::initialize();

        /* Set the date timezone */
        date_default_timezone_set(Date::$default_timezone);
        Date::$timezone = date_default_timezone_get();

        /* Setting the datetime for backend usages ( insertions in database..etc ) */
        Date::$date = Date::get();

        /* Check for a potential logged in account and do some extra checks */
        if(Authentication::check()) {

            $user = Authentication::$user;

            if(!$user) {
                Authentication::logout();
            }

            /* Determine if the current plan is expired or disabled */
            $user->plan_is_expired = false;

            /* Get current plan proper details */
            $user->plan = (new Plan(['settings' => $settings]))->get_plan_by_id($user->plan_id);

            /* Check if its a custom plan */
            if($user->plan->plan_id == 'custom') {
                $user->plan->settings = $user->plan_settings;
            }

            if(!$user->plan || ($user->plan && ((new \DateTime()) > (new \DateTime($user->plan_expiration_date)) && $user->plan_id != 'free') || !$user->plan->status)) {
                $user->plan_is_expired = true;

                /* Switch the user to the default plan */
                db()->where('user_id', $user->user_id)->update('users', [
                    'plan_id' => 'free',
                    'plan_settings' => json_encode(settings()->plan_free->settings),
                    'payment_subscription_id' => ''
                ]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' .  Authentication::$user_id);

                /* Make sure to redirect the person to the payment page and only let the person access the following pages */
                if(!in_array(Router::$controller_key, ['plan', 'pay', 'pay-billing', 'account', 'account-plan', 'account-payments', 'account-logs', 'logout',]) && Router::$path != 'admin') {
                    redirect('plan/new');
                }
            }

            /* Update last activity */
            if(!$user->last_activity || (new \DateTime($user->last_activity))->modify('+5 minutes') < (new \DateTime())) {
                (new User())->update_last_activity(Authentication::$user_id);
            }

            /* Update the language of the site for next page use if the current language (default) is different than the one the user has */
            if(!isset($_GET['set_language']) && Language::$language != $user->language) {
                Language::set_by_name($user->language);
            }

            /* Update the language of the user if needed */
            if(isset($_GET['set_language']) && in_array($_GET['set_language'], Language::$languages) && Language::$language != $user->language) {
                db()->where('user_id', Authentication::$user_id)->update('users', ['language' => $_GET['set_language']]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . Authentication::$user_id);
            }

            /* Set the timezone to be used for displaying */
            Date::$timezone = $user->timezone;

            /* Store all the details of the user in the Authentication static class as well */
            Authentication::$user = $user;
        }

        /* Set a CSRF Token */
        Csrf::set('token');
        Csrf::set('global_token');

        /* If the language code is the default one, redirect to index */
        if(Router::$language_code == Language::$default_language_code) {
            redirect(Router::$original_request);
        }

        /* Redirect based on browser language if needed */
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        if(Router::$controller_settings['no_browser_language_detection'] == false && !Router::$language_code && !Authentication::check() && $browser_language && Language::$default_language_code != $browser_language && array_key_exists($browser_language, Language::$languages)) {
            if(!isset($_SERVER['HTTP_REFERER']) || (isset($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'])['host'] != parse_url(SITE_URL)['host'])) {
                header('Location: ' . SITE_URL . $browser_language . '/' . Router::$original_request);
            }
        }

        /* Add main vars inside of the controller */
        $controller->add_params([
            /* Extra params available from the URL */
            'params' => $params,

            /* Potential logged in user */
            'user' => Authentication::$user
        ]);

        /* Check for authentication checks */
        if(!is_null(Router::$controller_settings['authentication'])) {
            Authentication::guard(Router::$controller_settings['authentication']);
        }

        /* Call the controller method */
        call_user_func_array([ $controller, $method ], []);

        /* Render and output everything */
        $controller->run();

        /* Close database */
        Database\Database::close();
    }

}
