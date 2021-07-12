<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Middlewares\Authentication;
use Altum\Models\User;

class AdminUserCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

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

            /* Define some variables */
            $required_fields = ['name', 'email' ,'password'];

            /* Check for the required fields */
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->name_length;
            }
            if(Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->email_exists;
            }
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->invalid_email;
            }
            if(strlen(trim($_POST['password'])) < 6) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->short_password;
            }

            /* If there are no errors continue the registering process */
            if(empty($_SESSION['error'])) {

                $registered_user_id = (new User())->create(
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['name'],
                    1,
                    null,
                    null,
                    'free',
                    json_encode($this->settings->plan_free->settings),
                    null,
                    $this->settings->default_timezone,
                    true
                );

                /* Log the action */
                Logger::users($registered_user_id, 'register.admin_register');

                /* Success message */
                $_SESSION['success'][] = $this->language->admin_user_create->success_message->created;

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
