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
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\User;

class AccountDelete extends Controller {

    public function index() {

        Authentication::guard();

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!password_verify($_POST['current_password'], $this->user->password)) {
                Alerts::add_field_error('current_password', language()->account->error_message->invalid_current_password);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Delete the user */
                (new User())->delete($this->user->user_id);

                /* Logout of the user */
                Authentication::logout(false);

                /* Start a new session to set a deletion message */
                session_start();

                /* Set a nice success message */
                Alerts::add_success(language()->account_delete->success_message);

                redirect();

            }

        }

        /* Establish the account sidebar menu view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $view = new \Altum\Views\View('account-delete/index', (array) $this);

        $this->add_view_content('content', $view->run([]));

    }

}
