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
use Altum\Models\Plan;

class AdminUserUpdate extends Controller {

    public function index() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if user exists */
        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        /* Get current plan proper details */
        $user->plan = (new Plan())->get_plan_by_id($user->plan_id);

        /* Check if its a custom plan */
        if($user->plan->plan_id == 'custom') {
            $user->plan->settings = json_decode($user->plan_settings);
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $_POST['is_enabled'] = (int) $_POST['is_enabled'];
            $_POST['type'] = (int) $_POST['type'];
            $_POST['plan_trial_done'] = (int) $_POST['plan_trial_done'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            switch($_POST['plan_id']) {
                case 'free':

                    $plan_settings = json_encode(settings()->plan_free->settings);

                    break;

                case 'custom':

                    /* Determine the enabled biolink blocks */
                    $enabled_biolink_blocks = [];

                    foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key => $value) {
                        $enabled_biolink_blocks[$key] = (bool) isset($_POST['enabled_biolink_blocks']) && in_array($key, $_POST['enabled_biolink_blocks']);
                    }

                    $plan_settings = json_encode([
                        'additional_global_domains' => (bool) isset($_POST['additional_global_domains']),
                        'custom_url' => (bool) isset($_POST['custom_url']),
                        'deep_links' => (bool) isset($_POST['deep_links']),
                        'no_ads' => (bool) isset($_POST['no_ads']),
                        'removable_branding' => (bool) isset($_POST['removable_branding']),
                        'custom_branding' => (bool) isset($_POST['custom_branding']),
                        'custom_colored_links' => (bool) isset($_POST['custom_colored_links']),
                        'statistics' => (bool) isset($_POST['statistics']),
                        'custom_backgrounds' => (bool) isset($_POST['custom_backgrounds']),
                        'verified' => (bool) isset($_POST['verified']),
                        'temporary_url_is_enabled' => (bool) isset($_POST['temporary_url_is_enabled']),
                        'seo' => (bool) isset($_POST['seo']),
                        'utm' => (bool) isset($_POST['utm']),
                        'socials' => (bool) isset($_POST['socials']),
                        'fonts' => (bool) isset($_POST['fonts']),
                        'password' => (bool) isset($_POST['password']),
                        'sensitive_content' => (bool) isset($_POST['sensitive_content']),
                        'leap_link' => (bool) isset($_POST['leap_link']),
                        'api_is_enabled' => (bool) isset($_POST['api_is_enabled']),
                        'affiliate_is_enabled' => (bool) isset($_POST['affiliate_is_enabled']),
                        'biolink_blocks_limit' => (int) $_POST['biolink_blocks_limit'],
                        'projects_limit' => (int) $_POST['projects_limit'],
                        'pixels_limit' => (int) $_POST['pixels_limit'],
                        'biolinks_limit' => (int) $_POST['biolinks_limit'],
                        'links_limit' => (int) $_POST['links_limit'],
                        'domains_limit' => (int) $_POST['domains_limit'],
                        'track_links_retention' => (int) $_POST['track_links_retention'],
                        'enabled_biolink_blocks' => $enabled_biolink_blocks,
                    ]);

                    break;

                default:

                    $_POST['plan_id'] = (int) $_POST['plan_id'];

                    /* Make sure this plan exists */
                    if(!$plan_settings = db()->where('plan_id', $_POST['plan_id'])->getValue('plans', 'settings')) {
                        redirect('admin/user-update/' . $user->user_id);
                    }

                    break;
            }

            $_POST['plan_expiration_date'] = (new \DateTime($_POST['plan_expiration_date']))->format('Y-m-d H:i:s');

            /* Check for any errors */
            $required_fields = ['name', 'email'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                    Alerts::add_field_error($field, language()->global->error_message->empty_field);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }
            if(mb_strlen($_POST['name']) < 3 || mb_strlen($_POST['name']) > 32) {
                Alerts::add_field_error('name', language()->admin_users->error_message->name_length);
            }
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
                Alerts::add_field_error('email', language()->admin_users->error_message->invalid_email);
            }
            if(db()->where('email', $_POST['email'])->has('users') && $_POST['email'] !== $user->email) {
                Alerts::add_field_error('email', language()->admin_users->error_message->email_exists);
            }

            if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                if(mb_strlen(trim($_POST['new_password'])) < 6) {
                    Alerts::add_field_error('new_password', language()->admin_users->error_message->short_password);
                }
                if($_POST['new_password'] !== $_POST['repeat_password']) {
                    Alerts::add_field_error('repeat_password', language()->admin_users->error_message->passwords_not_matching);
                }
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the basic user settings */
                db()->where('user_id', $user->user_id)->update('users', [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'active' => $_POST['is_enabled'],
                    'type' => $_POST['type'],
                    'plan_id' => $_POST['plan_id'],
                    'plan_expiration_date' => $_POST['plan_expiration_date'],
                    'plan_expiry_reminder' => $user->plan_expiration_date != $_POST['plan_expiration_date'] ? 0 : 1,
                    'plan_settings' => $plan_settings,
                    'plan_trial_done' => $_POST['plan_trial_done']
                ]);

                /* Update the password if set */
                if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    /* Database query */
                    db()->where('user_id', $user->user_id)->update('users', ['password' => $new_password]);
                }

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->update1, '<strong>' . htmlspecialchars($_POST['name']) . '</strong>'));

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

                redirect('admin/user-update/' . $user->user_id);
            }

        }

        /* Get all the plans available */
        $plans = db()->where('status', 0, '<>')->get('plans');

        /* Main View */
        $data = [
            'user' => $user,
            'plans' => $plans,
        ];

        $view = new \Altum\Views\View('admin/user-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
