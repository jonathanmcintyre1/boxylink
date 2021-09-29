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

class AdminTaxes extends Controller {

    public function index() {

        $taxes = db()->get('taxes');

        /* Main View */
        $data = [
            'taxes' => $taxes
        ];

        $view = new \Altum\Views\View('admin/taxes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        $tax_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$tax = db()->where('tax_id', $tax_id)->getOne('taxes', ['tax_id', 'name'])) {
            redirect('admin/taxes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the tax */
            db()->where('tax_id', $tax_id)->delete('taxes');

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $tax->name . '</strong>'));

        }

        redirect('admin/taxes');
    }

}
