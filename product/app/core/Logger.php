<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

use MaxMind\Db\Reader;

class Logger {

    public static function users($user_id, $type) {

        $ip = get_ip();

        /* Detect the location */
        try {
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get($ip);
        } catch(\Exception $exception) {
            /* :) */
        }
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Detect extra details about the user */
        $os_name = $whichbrowser->os->name ?? null;

        db()->insert('users_logs', [
            'user_id'       => $user_id,
            'type'          => $type,
            'ip'            => $ip,
            'device_type'   => $device_type,
            'os_name'       => $os_name,
            'country_code'  => $country_code,
            'datetime'      => \Altum\Date::$date,
        ]);

    }

}
