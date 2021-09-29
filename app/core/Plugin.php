<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

class Plugin {
    public static $plugins = [];

    public static function initialize() {

        /* Determine all the plugins available in the directory */
        foreach(glob(PLUGINS_PATH . '*', GLOB_ONLYDIR) as $plugin_directory) {

            /* Make sure the plugin has a config.json file */
            if(!file_exists($plugin_directory . '/config.json')) {
                continue;
            }

            /* Parse the config.json file */
            $config = json_decode(file_get_contents($plugin_directory . '/config.json'));

            /* Make sure the json has been parsed properly */
            if(is_null($config)) {
                continue;
            }

            /* Make sure the config.json file has the required props */
            if(!isset($config->plugin_id, $config->name, $config->description, $config->version, $config->url, $config->author, $config->author_url, $config->status)) {
                continue;
            }

            if(!isset($config->actions)) {
                $config->actions = true;
            }

            /* Save the route to the plugin */
            $config->path = $plugin_directory . '/';

            /* Save the plugin */
            self::$plugins[$config->plugin_id] = $config;

            /* Load the init file */
            if($config->status == 1) {
                require_once $config->path . 'init.php';
            }

        }

    }

    public static function get($plugin_id) {
        return self::$plugins[$plugin_id] ?? null;
    }

    /* Plugin status = 1 */
    public static function is_active($plugin_id) {
        return self::get($plugin_id) && self::get($plugin_id)->status === 1;
    }

    /* Plugin status = 0 */
    public static function is_installed($plugin_id) {
        return self::get($plugin_id) && self::get($plugin_id)->status === 0;
    }

    /* Plugin status = -1 */
    public static function is_uninstalled($plugin_id) {
        return self::get($plugin_id) && self::get($plugin_id)->status === -1;
    }

    /* Plugin status = -2 */
    public static function is_inexistent($plugin_id) {
        return self::get($plugin_id) && self::get($plugin_id)->status === -2;
    }
}
