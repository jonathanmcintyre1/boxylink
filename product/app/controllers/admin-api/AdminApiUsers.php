<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;

use Altum\Models\User;
use Altum\Response;
use Altum\Traits\Apiable;

class AdminApiUsers extends Controller {
    use Apiable;

    public $user = null;

    public function index() {

        $this->verify_request(true);

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->get();
            break;

            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0])) {

                    if(isset($this->params[1]) && $this->params[1] == 'one-time-login-code') {
                        $this->one_time_login_code();
                    } else {
                        $this->patch();
                    }


                } else {
                    $this->post();
                }

            break;

            case 'DELETE':
                $this->delete();
            break;
        }

        $this->return_404();

    }

    private function get() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $this->user = Database::get('*', 'users', ['user_id' => $user_id]);

        /* We haven't found the resource */
        if(!$this->user) {
            Response::jsonapi_error([[
                'title' => $this->language->api->error_message->not_found,
                'status' => '404'
            ]], null, 404);
        }

        /* Prepare the data */
        $data = [
            'type' => 'users',
            'id' => $this->user->user_id,

            'email' => $this->user->email,
            'api_key' => $this->user->api_key,
            'billing' => json_decode($this->user->billing),
            'is_enabled' => (bool) $this->user->active,
            'plan_id' => $this->user->plan_id,
            'plan_expiration_date' => $this->user->plan_expiration_date,
            'plan_settings' => json_decode($this->user->plan_settings),
            'plan_trial_done' => (bool) $this->user->plan_trial_done,
            'language' => $this->user->language,
            'timezone' => $this->user->timezone,
            'ip' => $this->user->ip,
            'country' => $this->user->country,
            'date' => $this->user->date,
            'last_activity' => $this->user->last_activity,
            'total_logins' => (int) $this->user->total_logins,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        $required_fields = ['name', 'email' ,'password'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                $this->response_error($this->language->global->error_message->empty_fields, 401);
                break 1;
            }
        }

        if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
            $this->response_error($this->language->admin_user_create->error_message->name_length, 401);
        }
        if(Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
            $this->response_error($this->language->admin_user_create->error_message->email_exists, 401);
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response_error($this->language->admin_user_create->error_message->invalid_email, 401);
        }
        if(strlen(trim($_POST['password'])) < 6) {
            $this->response_error($this->language->admin_user_create->error_message->short_password, 401);
        }

        /* Define some needed variables */
        $_POST['name'] = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
        $_POST['email'] = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

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

        /* Prepare the data */
        $data = [
            'type' => 'users',
            'id' => $registered_user_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $this->user = Database::get('*', 'users', ['user_id' => $user_id]);

        /* We haven't found the resource */
        if(!$this->user) {
            $this->response_error($this->language->api->error_message->not_found, 404);
        }

        if(isset($_POST['name']) && (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32)) {
            $this->response_error($this->language->admin_user_create->error_message->name_length, 401);
        }
        if(isset($_POST['email']) && $this->user->email != $_POST['email'] && Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
            $this->response_error($this->language->admin_user_create->error_message->email_exists, 401);
        }
        if(isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response_error($this->language->admin_user_create->error_message->invalid_email, 401);
        }
        if(isset($_POST['password']) && strlen(trim($_POST['password'])) < 6) {
            $this->response_error($this->language->admin_user_create->error_message->short_password, 401);
        }

        /* Define some needed variables */
        $name = isset($_POST['name']) ? trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING)) : $this->user->name;
        $email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_STRING)) : $this->user->email;
        $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $this->user->password;
        $is_enabled = isset($_POST['is_enabled']) ? (int) $_POST['is_enabled'] : $this->user->active;
        $type = isset($_POST['type']) ? (int) $_POST['type'] : $this->user->type;

        $plan_id = $this->user->plan_id;
        $plan_settings = $this->user->plan_settings;

        if(isset($_POST['plan_id'])) {
            switch($_POST['plan_id']) {
                case 'free':

                    $plan_settings = json_encode($this->settings->plan_free->settings);

                    break;

                case 'trial':

                    $plan_settings = json_encode($this->settings->plan_trial->settings);

                    break;

                default:

                    $_POST['plan_id'] = (int) $_POST['plan_id'];

                    /* Make sure this plan exists */
                    if(!$plan_settings = Database::simple_get('settings', 'plans', ['plan_id' => $_POST['plan_id']])) {
                        $this->response_error();
                    }

                    break;
            }
        }

        $plan_expiration_date = isset($_POST['plan_expiration_date']) ? (new \DateTime($_POST['plan_expiration_date']))->format('Y-m-d H:i:s') : $this->user->plan_expiration_date;
        $plan_trial_done = isset($_POST['plan_trial_done']) ? (int) $_POST['plan_trial_done'] : $this->user->plan_trial_done;

        /* Update the basic user settings */
        $stmt = Database::$database->prepare("
            UPDATE
                `users`
            SET
                `name` = ?,
                `email` = ?,
                `password` = ?,
                `active` = ?,
                `type` = ?,
                `plan_id` = ?,
                `plan_expiration_date` = ?,
                `plan_settings` = ?,
                `plan_trial_done` = ?
            WHERE
                `user_id` = ?
        ");
        $stmt->bind_param(
            'ssssssssss',
            $name,
            $email,
            $password,
            $is_enabled,
            $type,
            $plan_id,
            $plan_expiration_date,
            $plan_settings,
            $plan_trial_done,
            $this->user->user_id
        );
        $stmt->execute();
        $stmt->close();

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

        /* Prepare the data */
        $data = [
            'type' => 'users',
            'id' => $this->user->user_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function one_time_login_code() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $this->user = Database::get('*', 'users', ['user_id' => $user_id]);

        /* We haven't found the resource */
        if(!$this->user) {
            $this->response_error($this->language->api->error_message->not_found, 404);
        }

        /* Define some needed variables */
        $one_time_login_code = md5($this->user->email . $this->user->date . time());

        /* Update the basic user settings */
        $stmt = Database::$database->prepare("
            UPDATE
                `users`
            SET
                `one_time_login_code` = ?
            WHERE
                `user_id` = ?
        ");
        $stmt->bind_param(
            'ss',
            $one_time_login_code,
            $this->user->user_id
        );
        $stmt->execute();
        $stmt->close();

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

        /* Prepare the data */
        $data = [
            'type' => 'users',
            'one_time_login_code' => $one_time_login_code,
            'url' => url('login/one-time-login-code/' . $one_time_login_code),
            'id' => $this->user->user_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $this->user = Database::get('*', 'users', ['user_id' => $user_id]);

        /* We haven't found the resource */
        if(!$this->user) {
            $this->response_error($this->language->api->error_message->not_found, 404);
        }

        if($this->user->user_id == $this->api_user->user_id) {
            $this->response_error($this->language->admin_users->error_message->self_delete, 401);
        }

        /* Delete the user */
        (new User(['settings' => $this->settings]))->delete($this->user->user_id);

        http_response_code(200);
        die();

    }

}
