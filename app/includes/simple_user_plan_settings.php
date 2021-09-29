<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

$features = [
    'additional_global_domains',
    'custom_url',
    'deep_links',
    'removable_branding',
    'custom_branding',
    'custom_colored_links',
    'statistics',
    'custom_backgrounds',
    'verified',
    'temporary_url_is_enabled',
    'seo',
    'utm',
    'fonts',
    'password',
    'sensitive_content',
    'leap_link',
    'no_ads',
    'api_is_enabled'
];

if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled) {
    $features[] = 'affiliate_is_enabled';
}

return $features;

