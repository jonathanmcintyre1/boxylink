<?php

namespace Altum\Middlewares;

use Altum\Database\Database;
use Altum\Models\User;

class Authentication extends Middleware {

    public static $is_logged_in = null;
    public static $user_id = null;
    public static $user = null;

    public static function check() {

        /* Verify if the current route allows use to do the check */
        if(\Altum\Routing\Router::$controller_settings['no_authentication_check']) {
            return false;
        }

        /* Already logged in from previous checks */
        if(self::$is_logged_in) {
            return self::$user_id;
        }

        /* Check the cookies first */
        if(
            isset($_COOKIE['email'])
            && isset($_COOKIE['token_code'])
            && strlen($_COOKIE['token_code']) > 0
            && $user = (new User())->get_user_by_email_and_token_code($_COOKIE['email'], $_COOKIE['token_code'])
        ) {
            self::$is_logged_in = true;
            self::$user_id = $user->user_id;

            self::$user = $user;

            return true;
        }

        /* Check the Session */
        if(
            isset($_SESSION['user_id'])
            && !empty($_SESSION['user_id'])
            && $user = (new User())->get_user_by_user_id($_SESSION['user_id'])
        ) {
            self::$is_logged_in = true;
            self::$user_id = $user->user_id;

            self::$user = $user;

            return true;
        }

        return false;
    }


    public static function is_admin() {

        if(!self::check()) {
            return false;
        }

        return self::$user->type > 0;
    }


    public static function guard($permission = 'user') {

        switch ($permission) {
            case 'guest':

                if(self::check()) {
                    redirect(isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard');
                }

                break;

            case 'user':

                if(!self::check() || (self::check() && !self::$user->active)) {
                    redirect();
                }

                break;

            case 'admin':

                if(!self::check() || (self::check() && (!self::$user->active || self::$user->type != '1'))) {
                    redirect();
                }

                break;
        }

    }


    public static function logout($page = '') {

        if(self::check()) {
            Database::update('users', ['token_code' => ''], ['user_id' => self::$user_id]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . self::$user_id);
        }

        session_destroy();
        setcookie('email', '', time()-30);
        setcookie('token_code', '', time()-30);

        if($page !== false) {
            redirect($page);
        }
    }
}
