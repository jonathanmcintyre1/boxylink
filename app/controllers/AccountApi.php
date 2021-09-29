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

class AccountApi extends Controller {

    public function index() {

        Authentication::guard();

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $api_key = md5($_POST['email'] . microtime() . microtime());

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('user_id', $this->user->user_id)->update('users', ['api_key' => $api_key]);

                /* Set a nice success message */
                Alerts::add_success(language()->account_api->success_message);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

                redirect('account-api');
            }

        }

        /* Establish the account sub menu view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $view = new \Altum\Views\View('account-api/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }

}
