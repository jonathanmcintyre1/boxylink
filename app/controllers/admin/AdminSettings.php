<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Middlewares\Csrf;
use Altum\Routing\Router;

class AdminSettings extends Controller {

    public function index() {
        redirect('admin/settings/main');
    }

    private function process() {
        $method	= (isset(Router::$method) && file_exists(THEME_PATH . 'views/admin/settings/partials/' . Router::$method . '.php')) ? Router::$method : 'main';

        /* Method View */
        $view = new \Altum\Views\View('admin/settings/partials/' . $method, (array) $this);
        $this->add_view_content('method', $view->run());

        /* Main View */
        $view = new \Altum\Views\View('admin/settings/index', (array) $this);
        $this->add_view_content('content', $view->run(['method' => $method]));
    }

    private function update_settings($key, $value) {
        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Update the database */
            db()->where('`key`', $key)->update('settings', ['value' => $value]);

            $this->after_update_settings($key);
        }

        redirect('admin/settings/' . $key);
    }

    private function after_update_settings($key) {

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('settings');

        /* Set a nice success message */
        Alerts::add_success(language()->global->success_message->update2);

        /* Refresh the page */
        redirect('admin/settings/' . $key);

    }

    public function main() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :):) */
            $_POST['se_indexing'] = (bool) $_POST['se_indexing'];

            $value = json_encode([
                'se_indexing' => $_POST['se_indexing'],
            ]);

            db()->where('`key`', 'main')->update('settings', ['value' => $value]);

            /* :) */
            $_POST['title'] = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $_POST['default_timezone'] = filter_var($_POST['default_timezone'], FILTER_SANITIZE_STRING);
            $_POST['default_theme_style'] = filter_var($_POST['default_theme_style'], FILTER_SANITIZE_STRING);
            $_POST['email_confirmation'] = (bool) $_POST['email_confirmation'];
            $_POST['register_is_enabled'] = (bool) $_POST['register_is_enabled'];
            $_POST['terms_and_conditions_url'] = filter_var($_POST['terms_and_conditions_url'], FILTER_SANITIZE_STRING);
            $_POST['privacy_policy_url'] = filter_var($_POST['privacy_policy_url'], FILTER_SANITIZE_STRING);

            /* Check for errors & process  potential uploads */
            $image_allowed_extensions = [
                'logo' => ['jpg', 'jpeg', 'png', 'svg', 'gif'],
                'favicon' => ['png', 'ico', 'gif'],
                'opengraph' => ['jpg', 'jpeg', 'png', 'gif'],
            ];
            $image = [
                'logo' => !empty($_FILES['logo']['name']) && !isset($_POST['logo_remove']),
                'favicon' => !empty($_FILES['favicon']['name']) && !isset($_POST['favicon_remove']),
                'opengraph' => !empty($_FILES['opengraph']['name']) && !isset($_POST['opengraph_remove']),
            ];

            foreach(['logo', 'favicon', 'opengraph'] as $image_key) {
                if($image[$image_key]) {
                    $file_name = $_FILES[$image_key]['name'];
                    $file_extension = explode('.', $file_name);
                    $file_extension = mb_strtolower(end($file_extension));
                    $file_temp = $_FILES[$image_key]['tmp_name'];

                    if(!in_array($file_extension, $image_allowed_extensions[$image_key])) {
                        Alerts::add_error(language()->global->error_message->invalid_file_type);
                    }

                    if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                        if(!is_writable(UPLOADS_PATH . $image_key . '/')) {
                            Alerts::add_error(sprintf(language()->global->error_message->directory_not_writable, UPLOADS_PATH . $image_key . '/'));
                        }
                    }

                    if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                        /* Generate new name for image */
                        $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                        /* Offload uploading */
                        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                            try {
                                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                                /* Delete current image */
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/' . $image_key . '/' . settings()->{$image_key},
                                ]);

                                /* Upload image */
                                $result = $s3->putObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/' . $image_key . '/' . $image_new_name,
                                    'ContentType' => mime_content_type($file_temp),
                                    'SourceFile' => $file_temp,
                                    'ACL' => 'public-read'
                                ]);
                            } catch (\Exception $exception) {
                                Alerts::add_error($exception->getMessage());
                            }
                        }

                        /* Local uploading */
                        else {
                            /* Delete current image */
                            if(!empty(settings()->{$image_key}) && file_exists(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key})) {
                                unlink(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key});
                            }

                            /* Upload the original */
                            move_uploaded_file($file_temp, UPLOADS_PATH . $image_key . '/' . $image_new_name);
                        }

                        /* Database query */
                        db()->where('`key`', $image_key)->update('settings', ['value' => $image_new_name]);

                    }
                }

                /* Check for the removal of the already uploaded file */
                if(isset($_POST[$image_key . '_remove'])) {

                    /* Offload deleting */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_key . '/' . settings()->{$image_key},
                        ]);
                    }

                    /* Local deleting */
                    else {
                        /* Delete current file */
                        if(!empty(settings()->{$image_key}) && file_exists(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key})) {
                            unlink(UPLOADS_PATH . $image_key . '/' . settings()->{$image_key});
                        }
                    }

                    /* Database query */
                    db()->where('`key`', $image_key)->update('settings', ['value' => '']);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $keys = [
                    'title',
                    'default_language',
                    'default_theme_style',
                    'default_timezone',
                    'email_confirmation',
                    'register_is_enabled',
                    'index_url',
                    'terms_and_conditions_url',
                    'privacy_policy_url',
                ];

                /* Update the database */
                foreach($keys as $key) {
                    if(settings()->{$key} != $_POST[$key]) {
                        db()->where('`key`', $key)->update('settings', ['value' => $_POST[$key]]);
                    }
                }

                $this->after_update_settings('main');
            }

            redirect('admin/settings/main');
        }
    }

    public function payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['type'] = in_array($_POST['type'], ['one_time', 'recurring', 'both']) ? filter_var($_POST['type'], FILTER_SANITIZE_STRING) : 'both';
            $_POST['codes_is_enabled'] = (bool)$_POST['codes_is_enabled'];
            $_POST['brand_name'] = filter_var($_POST['brand_name'], FILTER_SANITIZE_STRING);
            $_POST['taxes_and_billing_is_enabled'] = (bool)$_POST['taxes_and_billing_is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'type' => $_POST['type'],
                'brand_name' => $_POST['brand_name'],
                'currency' => $_POST['currency'],
                'codes_is_enabled' => $_POST['codes_is_enabled'],
                'taxes_and_billing_is_enabled' => $_POST['taxes_and_billing_is_enabled'],
            ]);

            $this->update_settings('payment', $value);
        }
    }

    public function paypal() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? filter_var($_POST['mode'], FILTER_SANITIZE_STRING) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'client_id' => $_POST['client_id'],
                'secret' => $_POST['secret'],
            ]);

            $this->update_settings('paypal', $value);
        }
    }

    public function stripe() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('stripe', $value);
        }
    }

    public function offline_payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'instructions' => $_POST['instructions'],
            ]);

            $this->update_settings('offline_payment', $value);
        }
    }

    public function coinbase() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('coinbase', $value);
        }
    }

    public function affiliate() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('affiliate')) {
                redirect('admin/settings/affiliate');
            }

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['commission_type'] = in_array($_POST['commission_type'], ['once', 'forever']) ? filter_var($_POST['commission_type'], FILTER_SANITIZE_STRING) : 'once';
            $_POST['minimum_withdrawal_amount'] = (float)$_POST['minimum_withdrawal_amount'];
            $_POST['commission_percentage'] = $_POST['commission_percentage'] < 1 || $_POST['commission_percentage'] > 99 ? 10 : (int)$_POST['commission_percentage'];
            $_POST['withdrawal_notes'] = trim(filter_var($_POST['withdrawal_notes'], FILTER_SANITIZE_STRING));

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'commission_type' => $_POST['commission_type'],
                'minimum_withdrawal_amount' => $_POST['minimum_withdrawal_amount'],
                'commission_percentage' => $_POST['commission_percentage'],
                'withdrawal_notes' => $_POST['withdrawal_notes'],
            ]);

            $this->update_settings('affiliate', $value);
        }
    }

    public function business() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['invoice_is_enabled'] = (bool) $_POST['invoice_is_enabled'];

            $value = json_encode([
                'invoice_is_enabled' => $_POST['invoice_is_enabled'],
                'invoice_nr_prefix' => $_POST['invoice_nr_prefix'],
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'county' => $_POST['county'],
                'zip' => $_POST['zip'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'tax_type' => $_POST['tax_type'],
                'tax_id' => $_POST['tax_id'],
                'custom_key_one' => $_POST['custom_key_one'],
                'custom_value_one' => $_POST['custom_value_one'],
                'custom_key_two' => $_POST['custom_key_two'],
                'custom_value_two' => $_POST['custom_value_two'],
            ]);

            $this->update_settings('business', $value);
        }
    }

    public function captcha() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['type'] = in_array($_POST['type'], ['basic', 'recaptcha', 'hcaptcha']) ? $_POST['type'] : 'basic';
            foreach(['login', 'register', 'lost_password', 'resend_activation'] as $key) {
                $_POST['' . $key . '_is_enabled'] = (bool) $_POST['' . $key . '_is_enabled'];
            }

            $value = json_encode([
                'type' => $_POST['type'],
                'recaptcha_public_key' => $_POST['recaptcha_public_key'],
                'recaptcha_private_key' => $_POST['recaptcha_private_key'],
                'hcaptcha_site_key' => $_POST['hcaptcha_site_key'],
                'hcaptcha_secret_key' => $_POST['hcaptcha_secret_key'],
                'login_is_enabled' => $_POST['login_is_enabled'],
                'register_is_enabled' => $_POST['register_is_enabled'],
                'lost_password_is_enabled' => $_POST['lost_password_is_enabled'],
                'resend_activation_is_enabled' => $_POST['resend_activation_is_enabled'],
            ]);

            $this->update_settings('captcha', $value);
        }
    }

    public function facebook() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'app_id' => $_POST['app_id'],
                'app_secret' => $_POST['app_secret'],
            ]);

            $this->update_settings('facebook', $value);
        }
    }

    public function google() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('google', $value);
        }
    }

    public function twitter() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'consumer_api_key' => $_POST['consumer_api_key'],
                'consumer_api_secret' => $_POST['consumer_api_secret'],
            ]);

            $this->update_settings('twitter', $value);
        }
    }

    public function ads() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'header' => $_POST['header'],
                'footer' => $_POST['footer'],
                'header_biolink' => $_POST['header_biolink'],
                'footer_biolink' => $_POST['footer_biolink'],
            ]);

            $this->update_settings('ads', $value);
        }
    }

    public function socials() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = [];
            foreach(require APP_PATH . 'includes/admin_socials.php' as $key => $social) {
                $value[$key] = $_POST[$key];
            }
            $value = json_encode($value);

            $this->update_settings('socials', $value);
        }
    }

    public function smtp() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['auth'] = (bool) isset($_POST['auth']);
            $_POST['username'] = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);
            $_POST['password'] = $_POST['password'] ?? '';

            $value = json_encode([
                'from_name' => $_POST['from_name'],
                'from' => $_POST['from'],
                'host' => $_POST['host'],
                'encryption' => $_POST['encryption'],
                'port' => $_POST['port'],
                'auth' => $_POST['auth'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ]);

            $this->update_settings('smtp', $value);
        }
    }

    public function custom() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'head_js' => $_POST['head_js'],
                'head_css' => $_POST['head_css'],
            ]);

            $this->update_settings('custom', $value);
        }
    }

    public function announcements() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['id'] = md5($_POST['content']);
            $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
            $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
            $_POST['show_logged_in'] = (bool) isset($_POST['show_logged_in']);
            $_POST['show_logged_out'] = (bool) isset($_POST['show_logged_out']);

            $value = json_encode([
                'id' => $_POST['id'],
                'content' => $_POST['content'],
                'text_color' => $_POST['text_color'],
                'background_color' => $_POST['background_color'],
                'show_logged_in' => $_POST['show_logged_in'],
                'show_logged_out' => $_POST['show_logged_out'],
            ]);

            $this->update_settings('announcements', $value);
        }
    }

    public function email_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['emails'] = str_replace(' ', '', $_POST['emails']);
            $_POST['new_user'] = (bool) isset($_POST['new_user']);
            $_POST['new_payment'] = (bool) isset($_POST['new_payment']);
            $_POST['new_domain'] = (bool) isset($_POST['new_domain']);
            $_POST['new_affiliate_withdrawal'] = (bool) isset($_POST['new_affiliate_withdrawal']);

            $value = json_encode([
                'emails' => $_POST['emails'],
                'new_user' => $_POST['new_user'],
                'new_payment' => $_POST['new_payment'],
                'new_domain' => $_POST['new_domain'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
            ]);

            $this->update_settings('email_notifications', $value);
        }
    }

    public function webhooks() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['user_new'] = trim(filter_var($_POST['user_new'], FILTER_SANITIZE_STRING));
            $_POST['user_delete'] = trim(filter_var($_POST['user_delete'], FILTER_SANITIZE_STRING));

            $value = json_encode([
                'user_new' => $_POST['user_new'],
                'user_delete' => $_POST['user_delete'],
            ]);

            $this->update_settings('webhooks', $value);
        }
    }

    public function offload() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('offload')) {
                redirect('admin/settings/offload');
            }

            /* :) */
            $_POST['assets_url'] = trim(filter_var($_POST['assets_url'], FILTER_SANITIZE_STRING));

            $value = json_encode([
                'assets_url' => $_POST['assets_url'],
                'provider' => $_POST['provider'],
                'endpoint_url' => $_POST['endpoint_url'],
                'uploads_url' => $_POST['uploads_url'],
                'access_key' => $_POST['access_key'],
                'secret_access_key' => $_POST['secret_access_key'],
                'storage_name' => $_POST['storage_name'],
                'region' => $_POST['region'],
            ]);

            $this->update_settings('offload', $value);
        }
    }

    public function cron() {
        /* Get the latest cronjob details */
        settings()->cron = json_decode(db()->where('`key`', 'cron')->getValue('settings', '`value`'));

        $this->process();
    }

    public function license() {
        $this->process();

        if(!empty($_POST) && !empty($_POST['new_license'])) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');


            $altumcode_api = 'https://api.altumcode.com/validate';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'type'              => 'update',
                'license'           => $_POST['new_license'],
                'url'               => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        $dump = explode('-- SEPARATOR --', $response->body->sql);

                        foreach ($dump as $query) {
                            database()->query($query);
                        }
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('license');
                }
            }

            redirect('admin/settings/license');
        }
    }

    public function links() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['branding'] = trim($_POST['branding']);
            $_POST['shortener_is_enabled'] = (bool) $_POST['shortener_is_enabled'];
            $_POST['domains_is_enabled'] = (bool) $_POST['domains_is_enabled'];
            $_POST['main_domain_is_enabled'] = (bool) $_POST['main_domain_is_enabled'];
            $_POST['blacklisted_domains'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_keywords'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_keywords'])));
            $_POST['google_safe_browsing_is_enabled'] = (bool) $_POST['google_safe_browsing_is_enabled'];
            $_POST['avatar_size_limit'] = $_POST['avatar_size_limit'] > get_max_upload() || $_POST['avatar_size_limit'] < 0 ? get_max_upload() : (float) $_POST['avatar_size_limit'];
            $_POST['background_size_limit'] = $_POST['background_size_limit'] > get_max_upload() || $_POST['background_size_limit'] < 0 ? get_max_upload() : (float) $_POST['background_size_limit'];
            $_POST['favicon_size_limit'] = $_POST['favicon_size_limit'] > get_max_upload() || $_POST['favicon_size_limit'] < 0 ? get_max_upload() : (float) $_POST['favicon_size_limit'];
            $_POST['seo_image_size_limit'] = $_POST['seo_image_size_limit'] > get_max_upload() || $_POST['seo_image_size_limit'] < 0 ? get_max_upload() : (float) $_POST['seo_image_size_limit'];
            $_POST['thumbnail_image_size_limit'] = $_POST['thumbnail_image_size_limit'] > get_max_upload() || $_POST['thumbnail_image_size_limit'] < 0 ? get_max_upload() : (float) $_POST['thumbnail_image_size_limit'];
            $_POST['image_size_limit'] = $_POST['image_size_limit'] > get_max_upload() || $_POST['image_size_limit'] < 0 ? get_max_upload() : (float) $_POST['image_size_limit'];
            $_POST['audio_size_limit'] = $_POST['audio_size_limit'] > get_max_upload() || $_POST['audio_size_limit'] < 0 ? get_max_upload() : (float) $_POST['audio_size_limit'];
            $_POST['video_size_limit'] = $_POST['video_size_limit'] > get_max_upload() || $_POST['video_size_limit'] < 0 ? get_max_upload() : (float) $_POST['video_size_limit'];
            $_POST['file_size_limit'] = $_POST['file_size_limit'] > get_max_upload() || $_POST['file_size_limit'] < 0 ? get_max_upload() : (float) $_POST['file_size_limit'];


            $value = json_encode([
                'branding' => $_POST['branding'],
                'shortener_is_enabled' => $_POST['shortener_is_enabled'],
                'domains_is_enabled' => $_POST['domains_is_enabled'],
                'main_domain_is_enabled' => $_POST['main_domain_is_enabled'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_keywords' => $_POST['blacklisted_keywords'],
                'google_safe_browsing_is_enabled' => $_POST['google_safe_browsing_is_enabled'],
                'google_safe_browsing_api_key' => $_POST['google_safe_browsing_api_key'],
                'avatar_size_limit' => $_POST['avatar_size_limit'],
                'background_size_limit' => $_POST['background_size_limit'],
                'favicon_size_limit' => $_POST['favicon_size_limit'],
                'seo_image_size_limit' => $_POST['seo_image_size_limit'],
                'thumbnail_image_size_limit' => $_POST['thumbnail_image_size_limit'],
                'image_size_limit' => $_POST['image_size_limit'],
                'audio_size_limit' => $_POST['audio_size_limit'],
                'video_size_limit' => $_POST['video_size_limit'],
                'file_size_limit' => $_POST['file_size_limit'],
            ]);

            $this->update_settings('links', $value);
        }
    }

    public function send_test_email() {

        if(empty($_POST)) {
            redirect('admin/settings/smtp');
        }

        /* Check for any errors */
        $required_fields = ['email'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                Alerts::add_field_error($field, language()->global->error_message->empty_field);
            }
        }

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        /* If there are no errors, continue */
        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $result = send_mail($_POST['email'], settings()->title . ' - Test Email', 'This is just a test email to confirm that the smtp email settings are properly working!', true);

            if($result->ErrorInfo == '') {
                Alerts::add_success(language()->admin_settings_send_test_email_modal->success_message);
            } else {
                Alerts::add_error(sprintf(language()->admin_settings_send_test_email_modal->error_message, $result->ErrorInfo));
                Alerts::add_info(implode('<br />', $result->errors));
            }

        }

        redirect('admin/settings/smtp');
    }

}
