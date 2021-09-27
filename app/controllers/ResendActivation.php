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
use Altum\Captcha;
use Altum\Middlewares\Authentication;

class ResendActivation extends Controller {

    public function index() {

        Authentication::guard('guest');

        /* Default values */
        $values = [
            'email' => ''
        ];

        /* Initiate captcha */
        $captcha = new Captcha();

        if(!empty($_POST)) {
            /* Clean the posted variable */
            $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $values['email'] = $_POST['email'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            if(settings()->captcha->resend_activation_is_enabled && !$captcha->is_valid()) {
                Alerts::add_field_error('captcha', language()->global->error_message->invalid_captcha);
            }

            /* If there are no errors, resend the activation link */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'active', 'name', 'email', 'language']);

                if($user && !$user->active) {
                    /* Generate new email code */
                    $email_code = md5($_POST['email'] . microtime());

                    /* Update the current activation email */
                    db()->where('user_id', $user->user_id)->update('users', ['email_activation_code' => $email_code]);

                    /* Get the language for the user */
                    $language = language($user->language);

                    /* Prepare the email */
                    $email_template = get_email_template(
                        [
                            '{{NAME}}' => $user->name,
                        ],
                        $language->global->emails->user_activation->subject,
                        [
                            '{{ACTIVATION_LINK}}' => url('activate-user?email=' . md5($_POST['email']) . '&email_activation_code=' . $email_code . '&type=user_activation'),
                            '{{NAME}}' => $user->name,
                        ],
                        $language->global->emails->user_activation->body
                    );

                    /* Send the email */
                    send_mail($_POST['email'], $email_template->subject, $email_template->body);

                }

                /* Set a nice success message */
                Alerts::add_success(language()->resend_activation->success_message);
            }
        }

        /* Prepare the View */
        $data = [
            'values'    => $values,
            'captcha'   => $captcha
        ];

        $view = new \Altum\Views\View('resend-activation/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
