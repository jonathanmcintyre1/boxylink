<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Domain extends Model {

    public function get_domains($user) {

        if($user->plan_settings->additional_global_domains) {
            $where = "(user_id = {$user->user_id} OR `type` = 1)";
        } else {
            $where = "user_id = {$user->user_id}";
        }

        $where .= " AND `is_enabled` = 1";

        $result = database()->query("SELECT * FROM `domains` WHERE {$where}");
        $data = [];

        while($row = $result->fetch_object()) {

            /* Build the url */
            $row->url = $row->scheme . $row->host . '/';

            $data[] = $row;
        }

        return $data;
    }

    public function get_domain($domain_id) {

        /* Get the domain */
        $domain = null;

        /* Try to check if the domain posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('domain?domain_id=' . md5($domain_id));

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $domain = db()->where('domain_id', $domain_id)->getOne('domains');

            if($domain) {
                /* Build the url */
                $domain->url = $domain->scheme . $domain->host . '/';

                \Altum\Cache::$adapter->save(
                    $cache_instance->set($domain)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('domain_id=' . $domain->domain_id)
                );
            }

        } else {

            /* Get cache */
            $domain = $cache_instance->get();

        }

        return $domain;

    }

    public function get_domain_by_host($host) {

        /* Get the domain */
        $domain = null;

        /* Try to check if the domain posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('domain?host=' . md5($host));

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $domain = db()->where('host', $host)->getOne('domains');

            if($domain) {
                /* Build the url */
                $domain->url = $domain->scheme . $domain->host . '/';

                \Altum\Cache::$adapter->save(
                    $cache_instance->set($domain)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('domain_id=' . $domain->domain_id)
                );
            }

        } else {

            /* Get cache */
            $domain = $cache_instance->get();

        }

        return $domain;

    }

    public function delete($domain_id) {

        /* Delete everything related to the domain that the user owns */
        $result = database()->query("SELECT `link_id` FROM `links` WHERE `domain_id` = {$domain_id} AND `type` = 'biolink'");

        while($link = $result->fetch_object()) {

            (new \Altum\Models\Link())->delete($link->link_id);

        }

        /* Delete the domain */
        db()->where('domain_id', $domain_id)->delete('domains');

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $domain_id);

    }
}
