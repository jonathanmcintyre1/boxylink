<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

$pro_blocks = \Altum\Plugin::is_active('pro-blocks') && file_exists(\Altum\Plugin::get('pro-blocks')->path . 'pro_blocks.php') ? include \Altum\Plugin::get('pro-blocks')->path . 'pro_blocks.php' : [];
$ultimate_blocks = \Altum\Plugin::is_active('ultimate-blocks') && file_exists(\Altum\Plugin::get('ultimate-blocks')->path . 'ultimate_blocks.php') ? include \Altum\Plugin::get('ultimate-blocks')->path . 'ultimate_blocks.php' : [];

return array_merge(
    [
        'link' => [
            'type' => 'default',
            'icon' => 'fa fa-fw fa-link',
            'color' => '#00526b',
            'has_statistics' => true,
            'display_dynamic_name' => 'name',
        ],
        'heading' => [
            'type' => 'default',
            'icon' => 'fa fa-fw fa-heading',
            'color' => '#000',
            'has_statistics' => false,
            'display_dynamic_name' => 'text',
        ],
        'paragraph' => [
            'type' => 'default',
            'icon' => 'fa fa-fw fa-paragraph',
            'color' => '#494949',
            'has_statistics' => false,
            'display_dynamic_name' => false,
        ],
        'avatar' => [
            'type' => 'default',
            'icon' => 'fa fa-fw fa-user',
            'color' => '#8b2abf',
            'has_statistics' => false,
            'display_dynamic_name' => false,
        ],
        'image' => [
            'type' => 'default',
            'icon' => 'fa fa-fw fa-image',
            'color' => '#0682FF',
            'has_statistics' => true,
            'display_dynamic_name' => false,
        ],
        'socials' => [
            'type' => 'default',
            'icon' => 'fa fa-fw fa-users',
            'color' => '#63d2ff',
            'has_statistics' => false,
            'display_dynamic_name' => false,
        ],
        'mail' => [
            'type' => 'default',
            'icon' => 'fa fa-envelope',
            'color' => '#c91685',
            'has_statistics' => false,
            'display_dynamic_name' => 'name',
        ],
        'soundcloud' => [
            'type' => 'default',
            'icon' => 'fab fa-soundcloud',
            'color' => '#ff8800',
            'has_statistics' => false,
            'display_dynamic_name' => false,
            'whitelisted_hosts' => ['soundcloud.com']
        ],
        'spotify' => [
            'type' => 'default',
            'icon' => 'fab fa-spotify',
            'color' => '#1db954',
            'has_statistics' => false,
            'display_dynamic_name' => false,
            'whitelisted_hosts' => ['open.spotify.com']
        ],
        'youtube' => [
            'type' => 'default',
            'icon' => 'fab fa-youtube',
            'color' => '#ff0000',
            'has_statistics' => false,
            'display_dynamic_name' => false,
            'whitelisted_hosts' => ['www.youtube.com', 'youtu.be']
        ],
        'twitch' => [
            'type' => 'default',
            'icon' => 'fab fa-twitch',
            'color' => '#6441a5',
            'has_statistics' => false,
            'display_dynamic_name' => false,
            'whitelisted_hosts' => ['www.twitch.tv']
        ],
        'vimeo' => [
            'type' => 'default',
            'icon' => 'fab fa-vimeo',
            'color' => '#1ab7ea',
            'has_statistics' => false,
            'display_dynamic_name' => false,
            'whitelisted_hosts' => ['vimeo.com']
        ],
        'tiktok' => [
            'type' => 'default',
            'icon' => 'fab fa-tiktok',
            'color' => '#FD3E3E',
            'has_statistics' => false,
            'display_dynamic_name' => false,
            'whitelisted_hosts' => ['www.tiktok.com']
        ],
    ],
    $pro_blocks,
    $ultimate_blocks
);

