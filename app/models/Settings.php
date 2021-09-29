<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Settings extends Model {

    public function get() {

        $cache_instance = \Altum\Cache::$adapter->getItem('settings');

        /* Set cache if not existing */
        if(!$cache_instance->get()) {

            $result = database()->query("SELECT * FROM `settings`");
            $data = new \StdClass();

            while($row = $result->fetch_object()) {

                /* Put the value in a variable so we can check if its json or not */
                $value = json_decode($row->value);

                $data->{$row->key} = is_null($value) ? $row->value : $value;

            }

            \Altum\Cache::$adapter->save($cache_instance->set($data)->expiresAfter(CACHE_DEFAULT_SECONDS));

        } else {

            /* Get cache */
            $data = $cache_instance->get('settings');

        }

        /* Define some stuff from the database */
        if(!defined('PRODUCT_VERSION')) define('PRODUCT_VERSION', $data->product_info->version);
        if(!defined('PRODUCT_CODE')) define('PRODUCT_CODE', $data->product_info->code);

        /* Set the full url for assets */
        define('ASSETS_FULL_URL', \Altum\Plugin::is_active('offload') && isset($data->offload) && isset($data->offload->assets_url) && !empty($data->offload->assets_url) ? $data->offload->assets_url : SITE_URL . ASSETS_URL_PATH);
        define('UPLOADS_FULL_URL', \Altum\Plugin::is_active('offload') && isset($data->offload) && isset($data->offload->uploads_url) && !empty($data->offload->uploads_url) ? $data->offload->uploads_url : SITE_URL . UPLOADS_URL_PATH);

        return $data;
    }

}
