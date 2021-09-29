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

class Meta {
    public static $description = null;
    public static $keywords = null;
    public static $open_graph = [
        'type' => 'website',
        'url' => null,
        'title' => null,
        'description' => null,
        'image' => null
    ];
    public static $twitter = [
        'card' => 'summary_large_image',
        'url' => null,
        'title' => null,
        'description' => null,
        'image' => null
    ];

    public static function initialize() {

        /* Add the prefix if needed */
        $language_key = preg_replace('/-/', '_', Router::$controller_key);

        if(Router::$path != '') {
            $language_key = Router::$path . '_' . $language_key;
        }

        /* Check if the default is viable and use it */
        self::$description = language()->{$language_key}->meta_description ?? null;
        self::$keywords = language()->{$language_key}->meta_keywords ?? null;

    }

    public static function set_description($value) {
        self::$description = $value;
    }

    public static function set_keywords($value) {
        self::$keywords = $value;
    }

    public static function set_social_url($value) {
        self::$open_graph['url'] = $value;
        self::$twitter['url'] = $value;
    }

    public static function set_social_title($value) {
        self::$open_graph['title'] = $value;
        self::$twitter['title'] = $value;
    }

    public static function set_social_description($value) {
        self::$open_graph['description'] = $value;
        self::$twitter['description'] = $value;
    }

    public static function set_social_image($value) {
        self::$open_graph['image'] = $value;
        self::$twitter['image'] = $value;
    }

}
