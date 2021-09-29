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
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Models\User;

class AdminUserCreate extends Controller {

    public function index() {

        /* Default variables */
        $values = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            /* Default variables */
            $values['name'] = $_POST['name'];
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name', 'email' ,'password'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, language()->global->error_message->empty_field);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }
            if(mb_strlen($_POST['name']) < 3 || mb_strlen($_POST['name']) > 32) {
                Alerts::add_field_error('name', language()->admin_users->error_message->name_length);
            }
            if(db()->where('email', $_POST['email'])->has('users')) {
                Alerts::add_field_error('email', language()->admin_users->error_message->email_exists);
            }
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                Alerts::add_field_error('email', language()->admin_users->error_message->invalid_email);
            }
            if(mb_strlen(trim($_POST['password'])) < 6) {
                Alerts::add_field_error('password', language()->admin_users->error_message->short_password);
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $registered_user_id = (new User())->create(
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['name'],
                    1,
                    null,
                    null,
                    null,
                    'free',
                    json_encode(settings()->plan_free->settings),
                    null,
                    settings()->default_timezone,
                    true
                );

                /* Log the action */
                Logger::users($registered_user_id, 'register.success');

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->create1, '<strong>' . htmlspecialchars($_POST['name']) . '</strong>'));

                /* Redirect */
                redirect('admin/user-update/' . $registered_user_id);
            }

        }

        /* Main View */
        $data = [
            'values' => $values
        ];

        $view = new \Altum\Views\View('admin/user-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
