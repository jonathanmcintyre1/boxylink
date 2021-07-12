<?php

namespace Altum\Controllers;

use Altum\Captcha;
use Altum\Database\Database;
use Altum\Language;
use Altum\Logger;
use Altum\Middlewares\Authentication;
use Altum\Models\User;
use MaxMind\Db\Reader;

class Register extends Controller {

    public function index() {

        /* Check if Registration is enabled first */
        if(!$this->settings->register_is_enabled) {
            redirect();
        }

        Authentication::guard('guest');

        $redirect = isset($_GET['redirect']) ? Database::clean_string($_GET['redirect']) : 'dashboard';

        /* Default variables */
        $values = [
            'name' => $_GET['name'] ?? '',
            'email' => $_GET['email'] ?? '',
            'password' => ''
        ];

        /* Initiate captcha */
        $captcha = new Captcha([
            'type' => $this->settings->captcha->type,
            'recaptcha_public_key' => $this->settings->captcha->recaptcha_public_key,
            'recaptcha_private_key' => $this->settings->captcha->recaptcha_private_key
        ]);

        /* Facebook Login / Register */
        if($this->settings->facebook->is_enabled && !empty($this->settings->facebook->app_id) && !empty($this->settings->facebook->app_secret)) {
            $facebook = new \Facebook\Facebook([
                'app_id' => $this->settings->facebook->app_id,
                'app_secret' => $this->settings->facebook->app_secret,
                'default_graph_version' => 'v3.2',
            ]);

            $facebook_login_url = $facebook->getRedirectLoginHelper()->getLoginUrl(SITE_URL . 'login/facebook', ['email', 'public_profile']);
        }

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['name'] = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
            $_POST['email'] = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

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

            if($this->settings->captcha->register_is_enabled && !$captcha->is_valid()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_captcha;
            }
            if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
                $_SESSION['error'][] = $this->language->register->error_message->name_length;
            }
            if(Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
                $_SESSION['error'][] = $this->language->register->error_message->email_exists;
            }
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'][] = $this->language->register->error_message->invalid_email;
            }
            if(strlen(trim($_POST['password'])) < 6) {
                $_SESSION['error'][] = $this->language->register->error_message->short_password;
            }

            /* If there are no errors continue the registering process */
            if(empty($_SESSION['error'])) {
                /* Define some needed variables */
                $active 	                = (int) !$this->settings->email_confirmation;
                $email_code                 = md5($_POST['email'] . microtime());

                /* Determine what plan is set by default */
                $plan_id                    = 'free';
                $plan_settings              = json_encode($this->settings->plan_free->settings);
                $plan_expiration_date       = \Altum\Date::$date;

                /* When only the trial package is available make that the default one */
                if(!$this->settings->plan_free->status && $this->settings->plan_trial->status) {
                    $plan_id                = 'trial';
                    $plan_settings          = json_encode($this->settings->plan_trial->settings);
                    $plan_expiration_date   = (new \DateTime())->modify('+' . $this->settings->plan_trial->days . ' days')->format('Y-m-d H:i:s');
                }

                $registered_user_id = (new User())->create(
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['name'],
                    (int) !$this->settings->email_confirmation,
                    $email_code,
                    null,
                    $plan_id,
                    $plan_settings,
                    $plan_expiration_date,
                    $this->settings->default_timezone
                );

                /* Log the action */
                Logger::users($registered_user_id, 'register.register');

                /* Send notification to admin if needed */
                if($this->settings->email_notifications->new_user && !empty($this->settings->email_notifications->emails)) {

                    send_mail(
                        $this->settings,
                        explode(',', $this->settings->email_notifications->emails),
                        $this->language->global->emails->admin_new_user_notification->subject,
                        sprintf($this->language->global->emails->admin_new_user_notification->body, $_POST['name'], $_POST['email'])
                    );


                }

                /* If active = 1 then login the user, else send the user an activation email */
                if($active == '1') {

                    /* Send webhook notification if needed */
                    if($this->settings->webhooks->user_new) {

                        \Unirest\Request::post($this->settings->webhooks->user_new, [], [
                            'user_id' => $registered_user_id,
                            'email' => $_POST['email'],
                            'name' => $_POST['name']
                        ]);

                    }

                    $_SESSION['user_id'] = $registered_user_id;
                    $_SESSION['success'] = $this->language->register->success_message->login;

                    Logger::users($registered_user_id, 'login.success');

                    redirect($redirect);
                } else {

                    /* Prepare the email */
                    $email_template = get_email_template(
                        [
                            '{{NAME}}' => $_POST['name'],
                        ],
                        $this->language->global->emails->user_activation->subject,
                        [
                            '{{ACTIVATION_LINK}}' => url('activate-user?email=' . md5($_POST['email']) . '&email_activation_code=' . $email_code . '&type=user_activation' . '&redirect=' . $redirect),
                            '{{NAME}}' => $_POST['name'],
                        ],
                        $this->language->global->emails->user_activation->body
                    );

                    send_mail($this->settings, $_POST['email'], $email_template->subject, $email_template->body);

                    $_SESSION['success'][] = $this->language->register->success_message->registration;
                }

            }
        }

        /* Main View */
        $data = [
            'values' => $values,
            'captcha' => $captcha,
            'facebook_login_url' => $facebook_login_url ?? null,
        ];

        $view = new \Altum\Views\View('register/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
