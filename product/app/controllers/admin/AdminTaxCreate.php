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
use Altum\Database\Database;
use Altum\Middlewares\Csrf;

class AdminTaxCreate extends Controller {

    public function index() {

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['internal_name'] = Database::clean_string($_POST['internal_name']);
            $_POST['name'] = Database::clean_string($_POST['name']);
            $_POST['description'] = Database::clean_string($_POST['description']);
            $_POST['value'] = (int) $_POST['value'];
            $_POST['value_type'] = in_array($_POST['value_type'], ['percentage', 'fixed']) ? Database::clean_string($_POST['value_type']) : 'fixed';
            $_POST['type'] = in_array($_POST['type'], ['inclusive', 'exclusive']) ? Database::clean_string($_POST['type']) : 'inclusive';
            $_POST['billing_type'] = in_array($_POST['billing_type'], ['personal', 'business', 'both']) ? Database::clean_string($_POST['billing_type']) : 'both';
            $_POST['countries'] = isset($_POST['countries']) ? Database::clean_array($_POST['countries']) : null;

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('taxes', [
                    'internal_name' => $_POST['internal_name'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'value' => $_POST['value'],
                    'value_type' => $_POST['value_type'],
                    'type' => $_POST['type'],
                    'billing_type' => $_POST['billing_type'],
                    'countries' => json_encode($_POST['countries']),
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->create1, '<strong>' . htmlspecialchars($_POST['name']) . '</strong>'));

                redirect('admin/taxes');
            }
        }

        /* Main View */
        $data = [];

        $view = new \Altum\Views\View('admin/tax-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
