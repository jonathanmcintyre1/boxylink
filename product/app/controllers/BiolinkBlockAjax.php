<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Response;
use Altum\Routing\Router;
use Unirest\Request;

class BiolinkBlockAjax extends Controller {
    public $biolink_blocks = null;

    public function index() {
        Authentication::guard();

        if(!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

                /* Duplicate link */
                case 'duplicate': $this->duplicate(); break;

                /* Order links */
                case 'order': $this->order(); break;

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

            }

        }

        die($_POST['request_type']);
    }

    private function is_enabled_toggle() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Get the current status */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks', ['biolink_block_id', 'is_enabled']);

        if($biolink_block) {
            $new_is_enabled = (int) !$biolink_block->is_enabled;

            db()->where('biolink_block_id', $biolink_block->biolink_block_id)->update('biolinks_blocks', ['is_enabled' => $new_is_enabled]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

            Response::json('', 'success');
        }
    }

    private function duplicate() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks');

        if($biolink_block) {
            /* Make sure that the user didn't exceed the limit */
            $total_biolink_blocks = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_blocks` WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$biolink_block->link_id}")->fetch_object()->total;
            if($this->user->plan_settings->biolink_blocks_limit != -1 && $total_biolink_blocks >= $this->user->plan_settings->biolink_blocks_limit) {
                Response::json(language()->link->biolink_blocks->biolink_blocks_limit, 'error');
            }

            $biolink_block->settings = json_decode($biolink_block->settings);

            $settings = json_encode([
                'name' => $biolink_block->settings->name,
                'image' => $biolink_block->settings->image,
                'text_color' => $biolink_block->settings->text_color,
                'background_color' => $biolink_block->settings->background_color,
                'outline' => $biolink_block->settings->outline,
                'border_radius' => $biolink_block->settings->border_radius,
                'animation' => $biolink_block->settings->animation,
                'animation_runs' => $biolink_block->settings->animation_runs,
                'icon' => $biolink_block->settings->icon
            ]);

            /* Database query */
            db()->insert('biolinks_blocks', [
                'user_id' => $this->user->user_id,
                'link_id' => $biolink_block->link_id,
                'type' => $biolink_block->type,
                'location_url' => $biolink_block->location_url,
                'settings' => $settings,
                'start_date' => $biolink_block->start_date,
                'end_date' => $biolink_block->end_date,
                'is_enabled' => $biolink_block->is_enabled,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

            Response::json('', 'success', ['url' => url('link/' . $biolink_block->link_id . '?tab=links')]);

        }
    }

    private function order() {
        if(isset($_POST['biolink_blocks']) && is_array($_POST['biolink_blocks'])) {
            foreach($_POST['biolink_blocks'] as $link) {
                if(!isset($link['biolink_block_id']) || !isset($link['order'])) {
                    continue;
                }
                $link['biolink_block_id'] = (int) $link['biolink_block_id'];
                $link['order'] = (int) $link['order'];

                /* Update the link order */
                db()->where('biolink_block_id', $link['biolink_block_id'])->where('user_id', $this->user->user_id)->update('biolinks_blocks', ['order' => $link['order']]);
            }
        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success');
    }

    private function create() {
        $this->biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        /* Check for available biolink blocks */
        if(isset($_POST['block_type']) && array_key_exists($_POST['block_type'], $this->biolink_blocks)) {
            $_POST['block_type'] = trim(Database::clean_string($_POST['block_type']));
            $_POST['link_id'] = (int) $_POST['link_id'];

            /* Make sure that the user didn't exceed the limit */
            $total_biolink_blocks = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_blocks` WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$_POST['link_id']}")->fetch_object()->total;
            if($this->user->plan_settings->biolink_blocks_limit != -1 && $total_biolink_blocks >= $this->user->plan_settings->biolink_blocks_limit) {
                Response::json(language()->link->biolink_blocks->biolink_blocks_limit, 'error');
            }

            $individual_blocks = ['link', 'heading', 'paragraph', 'avatar', 'socials', 'mail', 'rss_feed', 'custom_html', 'vcard', 'image', 'image_grid', 'divider', 'faq', 'discord', 'countdown', 'cta', 'external_item', 'share'];
            $embeddable_blocks = ['anchor', 'applemusic', 'soundcloud', 'spotify', 'tidal', 'tiktok', 'twitch', 'twitter_tweet', 'vimeo', 'youtube', 'instagram_media', 'facebook', 'reddit'];
            $file_blocks = ['audio', 'video', 'file'];

            if(in_array($_POST['block_type'], $individual_blocks)) {
                $this->{'create_biolink_' . $_POST['block_type']}();
            }

            else if(in_array($_POST['block_type'], $file_blocks)) {
                $this->create_biolink_file($_POST['block_type']);
            }

            else if(in_array($_POST['block_type'], $embeddable_blocks)) {
                $this->create_biolink_embeddable($_POST['block_type']);
            }

        }

        die(':)');
    }

    private function create_biolink_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'link';
        $settings = json_encode([
            'name' => $_POST['name'],
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_heading() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = trim(Database::clean_string($_POST['text']));
        $_POST['heading_type'] = in_array($_POST['heading_type'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? Database::clean_string($_POST['heading_type']) : 'h1';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'heading';
        $settings = json_encode([
            'heading_type' => $_POST['heading_type'],
            'text' => $_POST['text'],
            'text_color' => 'white',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_paragraph() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'paragraph';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => 'white',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_avatar() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['size'] = in_array($_POST['size'], ['75', '100', '125', '150']) ? (int) $_POST['size'] : 125;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'avatars/', settings()->links->avatar_size_limit);

        $type = 'avatar';
        $settings = json_encode([
            'image' => $db_image,
            'size' => $_POST['size'],
            'border_radius' => $_POST['border_radius'],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_socials() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#ffffff' : $_POST['color'];

        /* Make sure the socials sent are proper */
        $biolink_socials = require APP_PATH . 'includes/biolink_socials.php';

        foreach($_POST['socials'] as $key => $value) {
            if(!array_key_exists($key, $biolink_socials)) {
                unset($_POST['socials'][$key]);
            } else {
                $_POST['socials'][$key] = Database::clean_string($_POST['socials'][$key]);
            }
        }

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'socials';
        $settings = json_encode([
            'color' => $_POST['color'],
            'socials' => $_POST['socials'],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_mail() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'mail';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'email_placeholder' => language()->create_biolink_mail_modal->email_placeholder_default,
            'name_placeholder' => language()->create_biolink_mail_modal->name_placeholder_default,
            'button_text' => language()->create_biolink_mail_modal->button_text_default,
            'success_text' => language()->create_biolink_mail_modal->success_text_default,
            'thank_you_url' => '',
            'show_agreement' => false,
            'agreement_url' => '',
            'agreement_text' => '',
            'mailchimp_api' => '',
            'mailchimp_api_list' => '',
            'webhook_url' => ''
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_rss_feed() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'rss_feed';
        $settings = json_encode([
            'amount' => 5,
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_custom_html() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['html'] = trim($_POST['html']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'custom_html';
        $settings = json_encode([
            'html' => $_POST['html']
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_vcard() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'vcard';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'first_name' => '',
            'last_name' => '',
            'text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
        ];
        foreach(['first_name', 'last_name', 'phone', 'street', 'city', 'zip', 'region', 'country', 'email', 'website', 'company', 'note'] as $key) {
            $settings[$key] = '';
        }
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_image() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'image';
        $settings = json_encode([
            'image' => $db_image,
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_image_grid() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'image_grid';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_divider() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['margin_top'] = $_POST['margin_top'] > 7 || $_POST['margin_top'] < 0 ? 3 : (int) $_POST['margin_top'];
        $_POST['margin_bottom'] = $_POST['margin_bottom'] > 7 || $_POST['margin_bottom'] < 0 ? 3 : (int) $_POST['margin_bottom'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'divider';
        $settings = json_encode([
            'margin_top' => $_POST['margin_top'],
            'margin_bottom' => $_POST['margin_bottom'],
            'background_color' => 'white',
            'icon' => 'fa fa-infinity'
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_faq() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'faq';
        $settings = json_encode([
            'items' => []
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_discord() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['server_id'] = (int) $_POST['server_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'discord';
        $settings = json_encode([
            'server_id' => $_POST['server_id']
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_countdown() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        $_POST['theme'] = in_array($_POST['theme'], ['light', 'dark']) ? trim(Database::clean_string($_POST['theme'])) : 'light';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'countdown';
        $settings = json_encode([
            'end_date' => $_POST['end_date'],
            'theme' => $_POST['theme'],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_file($type) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* File upload */
        $db_file = $this->handle_file_upload(null, 'file', 'file_remove', $this->biolink_blocks[$type]['whitelisted_file_extensions'], 'files/', settings()->links->{$type . '_size_limit'});

        $settings = [
            'file' => $db_file,
            'name' => $_POST['name'],
        ];

        if($type == 'file') {
            $settings = array_merge($settings, [
                'text_color' => 'black',
                'background_color' => 'white',
                'border_width' => 0,
                'border_style' => 'solid',
                'border_color' => 'white',
                'border_radius' => 'rounded',
                'animation' => false,
                'animation_runs' => 'repeat-1',
                'icon' => '',
                'image' => '',
            ]);
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_cta() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['type'] = in_array($_POST['type'], ['email', 'call', 'sms', 'facetime']) ? Database::clean_string($_POST['type']) : 'email';
        $_POST['value'] = trim(Database::clean_string($_POST['value']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'cta';
        $settings = json_encode([
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'name' => $_POST['name'],
            'text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_external_item() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['description'] = trim(Database::clean_string($_POST['description']));
        $_POST['price'] = trim(Database::clean_string($_POST['price']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'external_item';
        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'name_text_color' => 'black',
            'description_text_color' => 'black',
            'price_text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'image' => '',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_share() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'share';
        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => 'black',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function create_biolink_embeddable($type) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $settings = [];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Check for any errors */
        $required_fields = ['location_url'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Response::json(language()->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Make sure the location url is valid & get needed details */
        $host = parse_url($_POST['location_url'], PHP_URL_HOST);

        if(isset($this->biolink_blocks[$type]['whitelisted_hosts']) && !in_array($host, $this->biolink_blocks[$type]['whitelisted_hosts'])) {
            Response::json(language()->link->error_message->invalid_location_url_embed, 'error');
        }

        switch($type) {
            case 'reddit':
                $response = Request::get('https://www.reddit.com/oembed?url=' . $_POST['location_url']);

                if($response->code >= 400) {
                    Response::json(language()->link->error_message->invalid_location_url_embed, 'error');
                }

                $settings = [
                    'content' => $response->body->html
                ];
                break;
        }


        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => json_encode($settings),
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update() {
        $this->biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        if(!empty($_POST)) {
            /* Check for available biolink blocks */
            if(isset($_POST['block_type']) && array_key_exists($_POST['block_type'], $this->biolink_blocks)) {
                $_POST['block_type'] = trim(Database::clean_string($_POST['block_type']));

                $individual_blocks = ['link', 'heading', 'paragraph', 'avatar', 'socials', 'mail', 'rss_feed', 'custom_html', 'vcard', 'image', 'image_grid', 'divider', 'faq', 'discord', 'countdown', 'cta', 'external_item', 'share'];
                $embeddable_blocks = ['anchor', 'applemusic', 'soundcloud', 'spotify', 'tidal', 'tiktok', 'twitch', 'twitter_tweet', 'vimeo', 'youtube', 'instagram_media', 'facebook', 'reddit'];
                $file_blocks = ['audio', 'video', 'file'];

                if(in_array($_POST['block_type'], $individual_blocks)) {
                    $this->{'update_biolink_' . $_POST['block_type']}();
                }

                else if(in_array($_POST['block_type'], $file_blocks)) {
                    $this->update_biolink_file($_POST['block_type']);
                }

                else if(in_array($_POST['block_type'], $embeddable_blocks)) {
                    $this->update_biolink_embeddable($_POST['block_type']);
                }

            }
        }

        die();
    }

    private function update_biolink_link() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Response::json(language()->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url, 'location_url' => $_POST['location_url']]);
    }

    private function update_biolink_heading() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['heading_type'] = in_array($_POST['heading_type'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? Database::clean_string($_POST['heading_type']) : 'h1';
        $_POST['text'] = trim(Database::clean_string($_POST['text']));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'heading_type' => $_POST['heading_type'],
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_paragraph() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = trim(filter_var($_POST['text'], FILTER_SANITIZE_STRING));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_avatar() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['size'] = in_array($_POST['size'], ['75', '100', '125', '150']) ? (int) $_POST['size'] : 125;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'avatars/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'avatars/' . $db_image : null;

        $settings = json_encode([
            'image' => $db_image,
            'size' => $_POST['size'],
            'border_radius' => $_POST['border_radius'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function update_biolink_socials() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#ffffff' : $_POST['color'];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Make sure the socials sent are proper */
        $biolink_socials = require APP_PATH . 'includes/biolink_socials.php';

        foreach($_POST['socials'] as $key => $value) {
            if(!array_key_exists($key, $biolink_socials)) {
                unset($_POST['socials'][$key]);
            } else {
                $_POST['socials'][$key] = Database::clean_string($_POST['socials'][$key]);
            }
        }

        $settings = json_encode([
            'color' => $_POST['color'],
            'socials' => $_POST['socials'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_mail() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['email_placeholder'] = trim(Database::clean_string($_POST['email_placeholder']));
        $_POST['name_placeholder'] = trim(Database::clean_string($_POST['name_placeholder']));
        $_POST['button_text'] = trim(Database::clean_string($_POST['button_text']));
        $_POST['success_text'] = trim(Database::clean_string($_POST['success_text']));
        $_POST['show_agreement'] = (bool) isset($_POST['show_agreement']);
        $_POST['agreement_url'] = trim(Database::clean_string($_POST['agreement_url']));
        $_POST['agreement_text'] = trim(Database::clean_string($_POST['agreement_text']));
        $_POST['mailchimp_api'] = trim(Database::clean_string($_POST['mailchimp_api']));
        $_POST['mailchimp_api_list'] = trim(Database::clean_string($_POST['mailchimp_api_list']));
        $_POST['webhook_url'] = trim(Database::clean_string($_POST['webhook_url']));
        $_POST['thank_you_url'] = trim(Database::clean_string($_POST['thank_you_url']));

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'email_placeholder' => $_POST['email_placeholder'],
            'name_placeholder' => $_POST['name_placeholder'],
            'button_text' => $_POST['button_text'],
            'success_text' => $_POST['success_text'],
            'thank_you_url' => $_POST['thank_you_url'],
            'show_agreement' => $_POST['show_agreement'],
            'agreement_url' => $_POST['agreement_url'],
            'agreement_text' => $_POST['agreement_text'],
            'mailchimp_api' => $_POST['mailchimp_api'],
            'mailchimp_api_list' => $_POST['mailchimp_api_list'],
            'webhook_url' => $_POST['webhook_url']
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', ['settings' => $settings]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function update_biolink_rss_feed() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['amount'] = (int) Database::clean_string($_POST['amount']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $settings = json_encode([
            'amount' => $_POST['amount'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_custom_html() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['html'] = trim($_POST['html']);

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'html' => $_POST['html'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_vcard() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        foreach(['first_name', 'last_name', 'phone', 'street', 'city', 'zip', 'region', 'country', 'email', 'website', 'company', 'note'] as $key) {
            $_POST[$key] = trim(Database::clean_string($_POST[$key]));
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = [
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
        ];

        foreach(['first_name', 'last_name', 'phone', 'street', 'city', 'zip', 'region', 'country', 'email', 'website', 'company', 'note'] as $key) {
            $settings[$key] = $_POST[$key];
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function update_biolink_image() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_images/' . $db_image : null;

        $settings = json_encode([
            'image' => $db_image,
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function update_biolink_image_grid() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function update_biolink_divider() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['margin_top'] = $_POST['margin_top'] > 7 || $_POST['margin_top'] < 0 ? 3 : (int) $_POST['margin_top'];
        $_POST['margin_bottom'] = $_POST['margin_bottom'] > 7 || $_POST['margin_bottom'] < 0 ? 3 : (int) $_POST['margin_bottom'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'margin_top' => $_POST['margin_top'],
            'margin_bottom' => $_POST['margin_bottom'],
            'background_color' => $_POST['background_color'],
            'icon' => $_POST['icon'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_faq() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
        }

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;

            $items[] = [
                'title' => trim(Database::clean_string($value)),
                'content' => trim(Database::clean_string($_POST['item_content'][$key])),
            ];
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_discord() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['server_id'] = (int) $_POST['server_id'];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'server_id' => $_POST['server_id'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_countdown() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        $_POST['theme'] = in_array($_POST['theme'], ['light', 'dark']) ? trim(Database::clean_string($_POST['theme'])) : 'light';

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'end_date' => $_POST['end_date'],
            'theme' => $_POST['theme'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_file($type) {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* File upload */
        $db_file = $this->handle_file_upload($biolink_block->settings->file, 'file', 'file_remove', $this->biolink_blocks[$type]['whitelisted_file_extensions'], 'files/', settings()->links->{$type . '_size_limit'});

        $settings = [
            'file' => $db_file,
            'name' => $_POST['name']
        ];

        if($type == 'file') {
            /* Image upload */
            $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_remove'])) {
                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                        unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                    }
                }
            }

            $settings = array_merge($settings, [
                'text_color' => $_POST['text_color'],
                'background_color' => $_POST['background_color'],
                'border_radius' => $_POST['border_radius'],
                'border_width' => $_POST['border_width'],
                'border_style' => $_POST['border_style'],
                'border_color' => $_POST['border_color'],
                'animation' => $_POST['animation'],
                'animation_runs' => $_POST['animation_runs'],
                'icon' => $_POST['icon'],
                'image' => $db_image,
            ]);
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_cta() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['type'] = in_array($_POST['type'], ['email', 'call', 'sms', 'facetime']) ? Database::clean_string($_POST['type']) : 'email';
        $_POST['value'] = trim(Database::clean_string($_POST['value']));
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'name' => $_POST['name'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function update_biolink_external_item() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['description'] = trim(Database::clean_string($_POST['description']));
        $_POST['price'] = trim(Database::clean_string($_POST['price']));
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['name_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['name_text_color']) ? '#000' : $_POST['name_text_color'];
        $_POST['description_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_text_color']) ? '#000' : $_POST['description_text_color'];
        $_POST['price_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['price_text_color']) ? '#000' : $_POST['price_text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Response::json(language()->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'name_text_color' => $_POST['name_text_color'],
            'description_text_color' => $_POST['description_text_color'],
            'price_text_color' => $_POST['price_text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'image' => $db_image,
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url, 'location_url' => $_POST['location_url']]);
    }

    private function update_biolink_share() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['name'] = trim(Database::clean_string($_POST['name']));
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? Database::clean_string($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? Database::clean_string($_POST['border_width']) : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? Database::clean_string($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? Database::clean_string($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? Database::clean_string($_POST['animation_runs']) : false;
        $_POST['icon'] = trim(Database::clean_string($_POST['icon']));
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Response::json(language()->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => $_POST['text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['image_prop' => true, 'image_url' => $image_url, 'location_url' => $_POST['location_url']]);
    }

    private function update_biolink_embeddable($type) {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $settings = [];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        /* Check for any errors */
        $required_fields = ['location_url'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Response::json(language()->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Make sure the location url is valid & get needed details */
        $host = parse_url($_POST['location_url'], PHP_URL_HOST);

        if(isset($this->biolink_blocks[$type]['whitelisted_hosts']) && !in_array($host, $this->biolink_blocks[$type]['whitelisted_hosts'])) {
            Response::json(language()->link->error_message->invalid_location_url_embed, 'error');
        }

        switch($type) {
            case 'reddit':
                $response = Request::get('https://www.reddit.com/oembed?url=' . $_POST['location_url']);

                if($response->code >= 400) {
                    Response::json(language()->link->error_message->invalid_location_url_embed, 'error');
                }

                $settings = [
                    'content' => $response->body->html
                ];
                break;
        }

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => json_encode($settings),
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $biolink_block->link_id);

        Response::json(language()->global->success_message->update2, 'success');
    }

    private function delete() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Check for possible errors */
        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        (new \Altum\Models\BiolinkBlock())->delete($biolink_block->biolink_block_id);

        Response::json('', 'success', ['url' => url('link/' . $biolink_block->link_id . '?tab=links')]);
    }

    private function handle_file_upload($already_existing_file, $file_name, $file_name_remove, $allowed_extensions, $upload_folder, $size_limit) {
        /* File upload */
        $file = (bool) !empty($_FILES[$file_name]['name']) && !isset($_POST[$file_name_remove]);
        $db_file = $already_existing_file;

        if($file) {
            $file_extension = explode('.', $_FILES[$file_name]['name']);
            $file_extension = mb_strtolower(end($file_extension));
            $file_temp = $_FILES[$file_name]['tmp_name'];

            if(!is_writable(UPLOADS_URL_PATH . $upload_folder)) {
                Response::json(sprintf(language()->global->error_message->directory_not_writable, UPLOADS_URL_PATH . $upload_folder), 'error');
            }

            if($_FILES[$file_name]['error']) {
                Response::json(language()->global->error_message->file_upload, 'error');
            }

            if(!in_array($file_extension, $allowed_extensions)) {
                Response::json(language()->global->error_message->invalid_file_type, 'error');
            }

            if($_FILES[$file_name]['size'] > $size_limit * 1000000) {
                Response::json(sprintf(language()->global->error_message->file_size_limit, $size_limit), 'error');
            }

            /* Generate new name for the image */
            $image_new_name = md5(time() . rand()) . '.' . $file_extension;

            /* Offload uploading */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                try {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                    /* Delete current image */
                    if(!empty($already_existing_file)) {
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => UPLOADS_URL_PATH . $upload_folder . $already_existing_file,
                        ]);
                    }

                    /* Upload image */
                    $result = $s3->putObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . $upload_folder . $image_new_name,
                        'ContentType' => mime_content_type($file_temp),
                        'SourceFile' => $file_temp,
                        'ACL' => 'public-read'
                    ]);
                } catch (\Exception $exception) {
                    Response::json($exception->getMessage(), 'error');
                }
            }

            /* Local uploading */
            else {
                /* Delete current file */
                if(!empty($already_existing_file) && file_exists(UPLOADS_URL_PATH . $upload_folder . $already_existing_file)) {
                    unlink(UPLOADS_URL_PATH . $upload_folder . $already_existing_file);
                }

                /* Upload the original */
                move_uploaded_file($file_temp, UPLOADS_URL_PATH . $upload_folder . $image_new_name);
            }

            $db_file = $image_new_name;
        }

        return $db_file;
    }

    private function handle_image_upload($uploaded_image, $upload_folder, $size_limit) {
        return $this->handle_file_upload($uploaded_image, 'image', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], $upload_folder, $size_limit);
    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url)) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            Response::json(language()->global->error_message->empty_fields, 'error');
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            Response::json(language()->link->error_message->invalid_location_url, 'error');
        }

        if(!$this->user->plan_settings->deep_links && !in_array($url_details['scheme'], ['http', 'https'])) {
            Response::json(language()->link->error_message->invalid_location_url, 'error');
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain($url);

        if($domain && in_array(mb_strtolower($domain), explode(',', settings()->links->blacklisted_domains))) {
            Response::json(language()->link->error_message->blacklisted_domain, 'error');
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                Response::json(language()->link->error_message->blacklisted_location_url, 'error');
            }
        }
    }

}
