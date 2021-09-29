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

class LinkAjax extends Controller {

    public function index() {
        Authentication::guard();

        if(!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

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
        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Get the current status */
        $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id', 'is_enabled']);

        if($link) {
            $new_is_enabled = (int) !$link->is_enabled;

            db()->where('link_id', $link->link_id)->update('links', ['is_enabled' => $new_is_enabled]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
            \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $_POST['link_id']);

            Response::json('', 'success');
        }
    }

    private function create() {
        $_POST['type'] = trim(Database::clean_string($_POST['type']));

        /* Check for possible errors */
        if(!in_array($_POST['type'], ['link', 'biolink'])) {
            die();
        }

        switch($_POST['type']) {
            case 'link':
                $this->create_link();
                break;

            case 'biolink':
                $this->create_biolink();
                break;
        }

        die();
    }

    private function create_link() {
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            die();
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        if(empty($_POST['location_url'])) {
            Response::json(language()->global->error_message->empty_fields, 'error');
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        /* Make sure that the user didn't exceed the limit */
        $user_total_links = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
        if($this->user->plan_settings->links_limit != -1 && $user_total_links >= $this->user->plan_settings->links_limit) {
            Response::json(language()->create_link_modal->error_message->links_limit, 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(language()->create_link_modal->error_message->url_exists, 'error');
            }

        }

        if(empty($errors)) {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);
            $type = 'link';
            $settings = json_encode([
                'clicks_limit' => null,
                'expiration_url' => null,
                'password' => null,
                'sensitive_content' => false,
                'targeting_type' => null,
            ]);

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

            /* Insert to database */
            $link_id = db()->insert('links', [
                'user_id' => $this->user->user_id,
                'domain_id' => $domain_id,
                'type' => $type,
                'url' => $url,
                'location_url' => $_POST['location_url'],
                'settings' => $settings,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

            Response::json('', 'success', ['url' => url('link/' . $link_id)]);
        }
    }

    private function create_biolink() {
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            die();
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Make sure that the user didn't exceed the limit */
        $user_total_biolinks = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink'")->fetch_object()->total;
        if($this->user->plan_settings->biolinks_limit != -1 && $user_total_biolinks >= $this->user->plan_settings->biolinks_limit) {
            Response::json(language()->create_biolink_modal->error_message->biolinks_limit, 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(language()->create_biolink_modal->error_message->url_exists, 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ? $_POST['url'] : string_generate(10);
        $type = 'biolink';
        $settings = json_encode([
            'display_verified' => true,
            'favicon' => null,
            'background_type' => 'preset',
            'background' => 'one',
            'text_color' => 'white',
            'display_branding' => true,
            'branding' => [
                'url' => '',
                'name' => ''
            ],
            'seo' => [
                'block' => false,
                'title' => '',
                'meta_description' => '',
                'image' => '',
            ],
            'utm' => [
                'medium' => '',
                'source' => '',
            ],
            'font' => null,
            'password' => null,
            'sensitive_content' => false,
            'leap_link' => null
        ]);

        /* Generate random url if not specified */
        while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
            $url = string_generate(10);
        }

        $this->check_url($_POST['url']);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => $type,
            'url' => $url,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);

        Response::json('', 'success', ['url' => url('link/' . $link_id)]);
    }

    private function update() {

        if(!empty($_POST)) {
            $_POST['type'] = trim(Database::clean_string($_POST['type']));

            /* Check for possible errors */
            if(!in_array($_POST['type'], ['link', 'biolink'])) {
                die();
            }

            switch($_POST['type']) {
                case 'link':
                    $this->update_link();
                    break;

                case 'biolink':
                    $this->update_biolink();
                    break;
            }
        }

        die();
    }

    private function update_biolink() {
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            die();
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Check for any errors */
        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        $link->settings = json_decode($link->settings);


        if($_POST['url'] == $link->url) {
            $url = $link->url;

            if($link->domain_id != $domain_id) {
                if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                    Response::json(language()->create_biolink_modal->error_message->url_exists, 'error');
                }
            }

        } else {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(language()->create_biolink_modal->error_message->url_exists, 'error');
            }

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

            $this->check_url($_POST['url']);
        }

        /* Image uploads */
        $image_allowed_extensions = [
            'seo_image' => ['jpg', 'jpeg', 'png', 'gif'],
            'favicon' => ['png', 'gif', 'ico'],
            'background' => ['jpg', 'jpeg', 'png', 'svg', 'gif']
        ];
        $image = [
            'seo_image' => !empty($_FILES['seo_image']['name']) && !isset($_POST['seo_image_remove']),
            'favicon' => !empty($_FILES['favicon']['name']) && !isset($_POST['favicon_remove']),
        ];
        $image_upload_path = [
            'seo_image' => 'block_images',
            'favicon' => 'favicons',
        ];
        $image_uploaded_file = [
            'seo_image' => $link->settings->seo->image,
            'favicon' => $link->settings->favicon
        ];
        $image_url = [
            'seo_image' => null,
            'favicon' => null
        ];

        foreach(['favicon', 'seo_image'] as $image_key) {
            if($image[$image_key]) {
                $file_name = $_FILES[$image_key]['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES[$image_key]['tmp_name'];

                if(!in_array($file_extension, $image_allowed_extensions[$image_key])) {
                    Response::json(language()->global->error_message->invalid_file_type, 'error');
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . $image_upload_path[$image_key] . '/')) {
                        Response::json(sprintf(language()->global->error_message->directory_not_writable, UPLOADS_PATH . $image_upload_path[$image_key] . '/'), 'error');
                    }
                }

                if($_FILES[$image_key]['size'] > settings()->links->{$image_key . '_size_limit'} * 1000000) {
                    Response::json(sprintf(language()->global->error_message->file_size_limit, settings()->links->{$image_key . '_size_limit'}), 'error');
                }

                /* Generate new name for image */
                $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                /* Offload uploading */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    try {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                        /* Delete current image */
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key],
                        ]);

                        /* Upload image */
                        $result = $s3->putObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_upload_path[$image_key] . '/' . $image_new_name,
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
                    /* Delete current image */
                    if(!empty($image_uploaded_file[$image_key]) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key])) {
                        unlink(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key]);
                    }

                    /* Upload the original */
                    move_uploaded_file($file_temp, UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_new_name);
                }

                $image_uploaded_file[$image_key] = $image_new_name;
            }

            /* Check for the removal of the already uploaded file */
            if(isset($_POST[$image_key . '_remove'])) {

                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/' . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key],
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($image_uploaded_file[$image_key]) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key])) {
                        unlink(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key]);
                    }
                }

                $image_uploaded_file[$image_key] = null;
            }

            $image_url[$image_key] = $image_uploaded_file[$image_key] ? UPLOADS_FULL_URL . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key] : null;
        }

        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';
        $_POST['background_type'] = array_key_exists($_POST['background_type'], $biolink_backgrounds) ? $_POST['background_type'] : 'preset';
        $background = 'one';

        switch($_POST['background_type']) {
            case 'preset':
                $background = in_array($_POST['background'], $biolink_backgrounds['preset']) ? $_POST['background'] : 'one';
                break;

            case 'color':

                $background = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background']) ? '#000' : $_POST['background'];

                break;

            case 'gradient':

                $color_one = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background'][0]) ? '#000' : $_POST['background'][0];
                $color_two = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background'][1]) ? '#000' : $_POST['background'][1];

                $background = [
                    'color_one' => $color_one,
                    'color_two' => $color_two
                ];

                break;

            case 'image':

                $background = (bool) !empty($_FILES['background']['name']);

                /* Check for any errors on the logo image */
                if($background) {
                    $background_file_extension = explode('.', $_FILES['background']['name']);
                    $background_file_extension = mb_strtolower(end($background_file_extension));
                    $background_file_temp = $_FILES['background']['tmp_name'];

                    if(!is_writable(UPLOADS_PATH . 'backgrounds/')) {
                        Response::json(sprintf(language()->global->error_message->directory_not_writable, UPLOADS_PATH . 'backgrounds/'), 'error');
                    }

                    if($_FILES['background']['error']) {
                        Response::json(language()->global->error_message->file_upload, 'error');
                    }

                    if(!in_array($background_file_extension, $image_allowed_extensions['background'])) {
                        Response::json(language()->global->error_message->invalid_file_type, 'error');
                    }

                    if($_FILES['background']['size'] > settings()->links->background_size_limit * 1000000) {
                        Response::json(sprintf(language()->global->error_message->file_size_limit, settings()->links->background_size_limit), 'error');
                    }

                    /* Generate new name */
                    $background_new_name = md5(time() . rand()) . '.' . $background_file_extension;

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            if(is_string($link->settings->background)) {
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/backgrounds/' . $link->settings->background,
                                ]);
                            }

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/backgrounds/' . $background_new_name,
                                'ContentType' => mime_content_type($background_file_temp),
                                'SourceFile' => $background_file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Response::json($exception->getMessage(), 'error');
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current file */
                        if(is_string($link->settings->background) && !empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                            unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                        }

                        /* Upload the original */
                        move_uploaded_file($background_file_temp, UPLOADS_PATH . 'backgrounds/' . $background_new_name);
                    }

                    $background = $background_new_name;
                }

                break;
        }

        /* Delete existing background file if needed */
        if($_POST['background_type'] != 'image' && is_string($link->settings->background)) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/backgrounds/' . $link->settings->background,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                    unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                }
            }
        }

        $_POST['display_branding'] = (bool) isset($_POST['display_branding']);
        $_POST['display_verified'] = (bool) isset($_POST['display_verified']);
        $_POST['branding_name'] = trim(Database::clean_string($_POST['branding_name']));
        $_POST['branding_url'] = trim(Database::clean_string($_POST['branding_url']));
        $_POST['seo_block'] = (bool) isset($_POST['seo_block']);
        $_POST['seo_title'] = trim(Database::clean_string(mb_substr($_POST['seo_title'], 0, 70)));
        $_POST['seo_meta_description'] = trim(Database::clean_string(mb_substr($_POST['seo_meta_description'], 0, 160)));
        $_POST['utm_medium'] = trim(Database::clean_string($_POST['utm_medium']));
        $_POST['utm_source'] = trim(Database::clean_string($_POST['utm_source']));
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);
        $_POST['leap_link'] = trim(Database::clean_string($_POST['leap_link'] ?? null));
        $this->check_location_url($_POST['leap_link'], true);

        /* Make sure the font is ok */
        $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php';
        $_POST['font'] = !array_key_exists($_POST['font'], $biolink_fonts) ? false : Database::clean_string($_POST['font']);

        /* Set the new settings variable */
        $settings = json_encode([
            'display_verified' => $_POST['display_verified'],
            'background_type' => $_POST['background_type'],
            'background' => $background ? $background : $link->settings->background,
            'favicon' => $image_uploaded_file['favicon'],
            'text_color' => $_POST['text_color'],
            'display_branding' => $_POST['display_branding'],
            'branding' => [
                'name' => $_POST['branding_name'],
                'url' => $_POST['branding_url'],
            ],
            'seo' => [
                'block' => $_POST['seo_block'],
                'title' => $_POST['seo_title'],
                'meta_description' => $_POST['seo_meta_description'],
                'image' => $image_uploaded_file['seo_image'],
            ],
            'utm' => [
                'medium' => $_POST['utm_medium'],
                'source' => $_POST['utm_source'],
            ],
            'font' => $_POST['font'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'leap_link' => $_POST['leap_link'],
        ]);

        /* Update the record */
        db()->where('link_id', $link->link_id)->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'settings' => $settings,
        ]);

        /* Update the biolink page blocks if needed */
        if($link->project_id != $_POST['project_id']) {
            db()->where('biolink_id', $link->link_id)->update('links', ['project_id' => $_POST['project_id']]);
        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(language()->global->success_message->update2, 'success', [
            'url' => $url,
            'image_prop' => true,
            'seo_image_url' => $image_url['seo_image'],
            'favicon_url' => $image_url['favicon']
        ]);

    }

    private function update_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;
        $_POST['location_url'] = trim(Database::clean_string($_POST['location_url']));
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }
        $_POST['expiration_url'] = trim(Database::clean_string($_POST['expiration_url'] ?? null));
        $_POST['clicks_limit'] = empty($_POST['clicks_limit']) ? null : (int) $_POST['clicks_limit'];
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            die();
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Check for any errors */
        $required_fields = ['location_url'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Response::json(language()->global->error_message->empty_fields, 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;


        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(language()->create_link_modal->error_message->url_exists, 'error');
            }

        }

        $url = $_POST['url'];

        if(empty($_POST['url'])) {
            /* Generate random url if not specified */
            $url = string_generate(10);

            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

        }

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation'];
        $_POST['targeting_type'] = in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? Database::clean_string($_POST['targeting_type']) : 'false';

        $settings = [
            'clicks_limit' => $_POST['clicks_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'targeting_type' => $_POST['targeting_type'],
        ];

        /* Process the targeting */
        foreach($targeting_types as $targeting_type) {
            ${'targeting_' . $targeting_type} = [];

            if(isset($_POST['targeting_' . $targeting_type . '_key'])) {
                foreach ($_POST['targeting_' . $targeting_type . '_key'] as $key => $value) {
                    if (empty(trim($value))) continue;

                    ${'targeting_' . $targeting_type}[] = [
                        'key' => trim(Database::clean_string($value)),
                        'value' => trim(Database::clean_string($_POST['targeting_' . $targeting_type . '_value'][$key])),
                    ];
                }

                $settings['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type};
            }
        }

        $settings = json_encode($settings);

        db()->where('link_id', $_POST['link_id'])->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'location_url' => $_POST['location_url'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $this->user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(language()->global->success_message->update2, 'success', ['url' => $url]);
    }

    private function delete() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Check for possible errors */
        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id'])) {
            die();
        }

        (new \Altum\Models\Link())->delete($link->link_id);

        Response::json('', 'success', ['url' => url('dashboard')]);

    }

    /* Function to bundle together all the checks of a custom url */
    private function check_url($url) {

        if($url) {
            /* Make sure the url alias is not blocked by a route of the product */
            if(array_key_exists($url, Router::$routes[''])) {
                Response::json(language()->link->error_message->blacklisted_url, 'error');
            }

            /* Make sure the custom url is not blacklisted */
            if(in_array(mb_strtolower($url), explode(',', settings()->links->blacklisted_keywords))) {
                Response::json(language()->link->error_message->blacklisted_keyword, 'error');
            }

        }

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

    /* Check if custom domain is set and return the proper value */
    private function get_domain_id($posted_domain_id) {

        $domain_id = 0;

        if(isset($posted_domain_id)) {
            $domain_id = (int) Database::clean_string($posted_domain_id);

            /* Make sure the user has access to global additional domains */
            if($this->user->plan_settings->additional_global_domains) {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND (`user_id` = {$this->user->user_id} OR `type` = 1)")->fetch_object()->domain_id ?? 0;
            } else {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND `user_id` = {$this->user->user_id}")->fetch_object()->domain_id ?? 0;
            }

        }

        return $domain_id;
    }
}
