<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

use Altum\Routing\Router;

class Title {
    public static $full_title;
    public static $site_title;
    public static $page_title;

    public static function initialize($site_title) {

        self::$site_title = $site_title;

        /* Add the prefix if needed */
        $language_key = preg_replace('/-/', '_', Router::$controller_key);

        if(Router::$path != '') {
            $language_key = Router::$path . '_' . $language_key;
        }

        /* Check if the default is viable and use it */
        $page_title = (isset(language()->{$language_key}->title)) ? language()->{$language_key}->title : Router::$controller;

        self::set($page_title);
    }

    public static function set($page_title, $full = false) {

        self::$page_title = $page_title;

        self::$full_title = self::$page_title . ($full ? null : ' - ' . self::$site_title);

    }


    public static function get() {

        return self::$full_title;

    }

}
