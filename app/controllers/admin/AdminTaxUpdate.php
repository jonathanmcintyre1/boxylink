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

class AdminTaxUpdate extends Controller {

    public function index() {

        $tax_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$tax = db()->where('tax_id', $tax_id)->getOne('taxes')) {
            redirect('admin/taxes');
        }

        $tax->countries = json_decode($tax->countries);

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['internal_name'] = Database::clean_string($_POST['internal_name']);
            $_POST['name'] = Database::clean_string($_POST['name']);
            $_POST['description'] = Database::clean_string($_POST['description']);

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('tax_id', $tax_id)->update('taxes', [
                    'internal_name' => $_POST['internal_name'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description']
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->update1, '<strong>' . htmlspecialchars($_POST['name']) . '</strong>'));

                /* Refresh the page */
                redirect('admin/tax-update/' . $tax_id);

            }

        }

        /* Main View */
        $data = [
            'tax_id'       => $tax_id,
            'tax'          => $tax,
        ];

        $view = new \Altum\Views\View('admin/tax-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
