<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Page extends Model {

    public function get_pages($position) {

        $data = [];

        $cache_instance = \Altum\Cache::$adapter->getItem('pages_' . $position);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            $result = database()->query("SELECT `url`, `title`, `type` FROM `pages` WHERE `position` = '{$position}' ORDER BY `order`");

            while($row = $result->fetch_object()) {

                if($row->type == 'internal') {

                    $row->target = '_self';
                    $row->url = url('page/' . $row->url);

                } else {

                    $row->target = '_blank';

                }

                $data[] = $row;
            }

            \Altum\Cache::$adapter->save($cache_instance->set($data)->expiresAfter(CACHE_DEFAULT_SECONDS));

        } else {

            /* Get cache */
            $data = $cache_instance->get();

        }

        return $data;
    }

}
