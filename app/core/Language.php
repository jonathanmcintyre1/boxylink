<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

class Language {
    /* Currently selected language */
    public static $language;
    public static $language_code;

    /* Available languages found in the languages directory */
    /* en => english */
    public static $languages = [];

    /* Defaults */
    public static $default_language;
    public static $default_language_code;

    /* Already processed language objects */
    /* english => english object */
    public static $language_objects = [];

    /* Languages directory path */
    public static $path;

    public static function initialize($path) {

        self::$path = $path;

        /* Determine all the languages available in the directory */
        foreach(glob(self::$path . '*.json') as $file) {
            $file = explode('/', $file);
            $file_name = str_replace('.json', '', trim(end($file)));

            /* Language name & code */
            list($language_name, $language_code) = explode('#', $file_name);

            self::$languages[$language_code] = $language_name;
        }

    }

    public static function get($language = null) {

        if(!$language) {
            $language = self::$language;

            /* Check if we already processed the language file */
            if(isset(self::$language_objects[$language])) {
                return self::$language_objects[$language];
            }
        }

        /* Make sure we have access to the requested language */
        if(!in_array($language, self::$languages)) {

            /* Try and use the default one if available */
            if(in_array(self::$default_language, self::$languages)) {
                $language = self::$default_language;
            } else {
                die('Requested language "' . $language . '" does not exist and the default language "' . self::$default_language . '" does not exist as well.');
            }

        }

        /* Check if we already processed the language file */
        if(isset(self::$language_objects[$language])) {
            return self::$language_objects[$language];
        }

        /* Include the language file */
        $language_code = array_search($language, self::$languages);
        self::$language_objects[$language] = json_decode(file_get_contents(self::$path . $language . '#' . $language_code . '.json'));

        /* Check the language file */
        if(is_null(self::$language_objects[$language])) {
            die('The language file is corrupted. Please make sure your JSON Language file is JSON Validated ( you can do that with an online JSON Validator by searching on Google ).');
        }

        /* Include the admin language file if needed */
        if(\Altum\Routing\Router::$path == 'admin') {
            $admin_language = json_decode(file_get_contents(self::$path . 'admin/' . $language . '#' . $language_code . '.json'));

            /* Merge */
            self::$language_objects[$language] = (object) (array_merge((array) self::$language_objects[$language], (array) $admin_language));
        }

        return self::$language_objects[$language];
    }

    public static function set_by_name($language) {

        if(in_array($language, self::$languages)) {
            self::$language = $language;
            self::$language_code = array_search($language, self::$languages);
        }

    }

    public static function set_by_code($language_code) {

        if(array_key_exists($language_code, self::$languages)) {
            self::$language = self::$languages[$language_code];
            self::$language_code = $language_code;
        }

    }

    public static function set_default($language) {
        self::$default_language = $language;
        self::$default_language_code = array_search($language, self::$languages);

        if(!isset(self::$language)) {
            self::$language = $language;
            self::$language_code = self::$default_language_code;
        }
    }
}
