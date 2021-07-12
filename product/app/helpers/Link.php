<?php

namespace Altum;

class Link {

    public static function get_biolink($tthis, $link, $user = null, $links = null) {

        /* Determine the background of the biolink */
        $link->design = new \StdClass();
        $link->design->background_class = '';
        $link->design->background_style = '';

        /* Check if the user has the access needed from the plan */
        if(!$user->plan_settings->custom_backgrounds && in_array($link->settings->background_type, ['image', 'gradient', 'color'])) {

            /* Revert to a default if no access */
            $link->settings->background_type = 'preset';
            $link->settings->background = 'one';

        }

        switch($link->settings->background_type) {
            case 'image':

                $link->design->background_style = 'background: url(\'' . SITE_URL . UPLOADS_URL_PATH . 'backgrounds/' . $link->settings->background . '\');';

                break;

            case 'gradient':

                $link->design->background_style = 'background-image: linear-gradient(135deg, ' . $link->settings->background->color_one . ' 10%, ' . $link->settings->background->color_two . ' 100%);';

                break;

            case 'color':

                $link->design->background_style = 'background: ' . $link->settings->background . ';';

                break;

            case 'preset':

                $link->design->background_class = 'link-body-background-' . $link->settings->background;

                break;
        }

        /* Determine the color of the header text */
        $link->design->text_style = 'color: ' . $link->settings->text_color;

        /* Determine the socials text */
        $link->design->socials_style = 'color: ' . $link->settings->socials_color;

        /* Determine the notification branding settings */
        if($user && !$user->plan_settings->removable_branding && !$link->settings->display_branding) {
            $link->settings->display_branding = true;
        }

        if($user && $user->plan_settings->removable_branding && !$link->settings->display_branding) {
            $link->settings->display_branding = false;
        }

        /* Check if we can show the custom branding if available */
        if(isset($link->settings->branding, $link->settings->branding->name, $link->settings->branding->url) && !$user->plan_settings->custom_branding) {
            $link->settings->branding = false;
        }

        /* Prepare the View */
        $data = [
            'link'  => $link,
            'user'  => $user,
            'links' => $links
        ];

        $view = new \Altum\Views\View('link-path/partials/biolink', (array) $tthis);

        return $view->run($data);

    }

    public static function get_biolink_link($link, $user = null) {

        $data = [];

        /* Require different files for different types of links available */
        switch($link->subtype) {
            case 'link':
            case 'mail':
            case 'rss_feed':
            case 'vcard':

                $link->settings = json_decode($link->settings);

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {

                    /* Revert to a default if no access */
                    $link->settings->background_color = 'white';
                    $link->settings->text_color = 'black';

                    if($link->settings->outline) {
                        $link->settings->background_color = 'white';
                        $link->settings->text_color = 'white';
                    }
                }

                /* Determine the css and styling of the button */
                $link->design = new \StdClass();
                $link->design->link_class = '';
                $link->design->link_style = 'background: ' . $link->settings->background_color . ';color: ' . $link->settings->text_color;

                /* Type of button */
                if($link->settings->outline) {
                    $link->design->link_style = 'color: ' . $link->settings->text_color . '; background: transparent; border: .1rem solid ' . $link->settings->background_color;
                }

                /* Border radius */
                $border_radius_class = '';

                switch($link->settings->border_radius) {
                    case 'straight':
                        break;

                    case 'round':
                        $border_radius_class = 'link-btn-round';
                        break;

                    case 'rounded':
                        $border_radius_class = 'link-btn-rounded';
                        break;
                }

                $link->design->border_class = $border_radius_class;
                $link->design->link_class = $border_radius_class;

                /* Animation */
                if($link->settings->animation) {
                    $link->design->link_class .= ' animate__animated animate__infinite animate__' . $link->settings->animation . ' animate__delay-2s';
                }

                /* UTM Parameters */
                $link->utm_query = null;
                if($user->plan_settings->utm && $link->utm->medium && $link->utm->source) {
                    $link->utm_query = '?utm_medium=' . $link->utm->medium . '&utm_source=' . $link->utm->source . '&utm_campaign=' . $link->settings->name;
                }

                $view_path = 'link-path/biolink_blocks/' . $link->subtype;

            break;

            case 'text':

                $link->settings = json_decode($link->settings);

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {

                    /* Revert to a default if no access */
                    $link->settings->title_text_color = 'white';
                    $link->settings->description_text_color = 'white';

                }

                $view_path = 'link-path/biolink_blocks/' . $link->subtype;

                break;

            case 'image':
            case 'image_grid':

                $link->settings = json_decode($link->settings);

                /* UTM Parameters */
                $link->utm_query = null;
                if($user->plan_settings->utm && $link->utm->medium && $link->utm->source) {
                    $link->utm_query = '?utm_medium=' . $link->utm->medium . '&utm_source=' . $link->utm->source . '&utm_campaign=' . $link->settings->name;
                }

                $view_path = 'link-path/biolink_blocks/' . $link->subtype;

                break;

            case 'youtube':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/', $link->location_url, $match)) {
                    $data['embed'] = $match[1];

                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'soundcloud':

                if(preg_match('/(soundcloud\.com)/', $link->location_url)) {
                    $data['embed'] = $link->location_url;

                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'vimeo':

                if(preg_match('/https:\/\/(player\.)?vimeo\.com(\/video)?\/(\d+)/', $link->location_url, $match)) {
                    $data['embed'] = $match[3];

                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'twitch':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:twitch\.tv\/)(.+)$/', $link->location_url, $match)) {
                    $data['embed'] = $match[1];

                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'spotify':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:open\.)?(?:spotify\.com\/)(album|track|show|episode)+\/(.+)$/', $link->location_url, $match)) {
                    $data['embed_type'] = $match[1];
                    $data['embed_value'] = $match[2];

                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'tiktok':


                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:tiktok\.com\/.+\/)(.+)$/', $link->location_url, $match)) {
                    $data['embed'] = $match[1];

                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'applemusic':


                if(preg_match('/(https:\/\/music\.apple\.com)/', $link->location_url)) {

                    $position = strpos($link->location_url, 'music.apple.com');

                    if($position !== false) {
                        $link->location_url = str_replace('music.apple.com', 'embed.music.apple.com', $link->location_url);

                        $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                    }

                }

                break;

            case 'tidal':

                if(preg_match('/(https:\/\/tidal\.com)/', $link->location_url)) {

                    $position = strpos($link->location_url, 'tidal.com');

                    if($position !== false) {
                        $link->location_url = str_replace('tidal.com', 'embed.tidal.com', $link->location_url) . '?disableAnalytics=true';
                        $link->location_url = str_replace('browse/', '', $link->location_url);
                        $link->location_url = str_replace('track/', 'tracks/', $link->location_url);
                        $link->location_url = str_replace('album/', 'albums/', $link->location_url);

                        $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                    }

                }

                break;

            case 'anchor':

                if(preg_match('/(https:\/\/anchor\.fm)/', $link->location_url)) {

                    $position = strpos($link->location_url, '/', 18);

                    if($position !== false) {

                        $link->location_url = substr_replace($link->location_url, '/embed', $position, 0);

                        $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                    }

                }

                break;

            case 'twitter_tweet':

                if(preg_match('/(https:\/\/twitter\.com)/', $link->location_url)) {
                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'instagram_media':

                if(preg_match('/(https:\/\/www.instagram\.com)/', $link->location_url)) {
                    $view_path = 'link-path/biolink_blocks/' . $link->subtype;
                }

                break;

            case 'custom_html':

                $view_path = 'link-path/biolink_blocks/' . $link->subtype;

                break;

            case 'divider':

                $link->settings = json_decode($link->settings);

                $view_path = 'link-path/biolink_blocks/' . $link->subtype;
        }

        if(!isset($view_path)) return null;

        /* Prepare the View */
        $data = array_merge($data, [
            'link'  => $link,
            'user'  => $user
        ]);

        $view = new \Altum\Views\View($view_path);

        return $view->run($data);

    }
}
