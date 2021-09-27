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

class AdminCodes extends Controller {

    public function index() {

        $codes_result = database()->query("
            SELECT `codes`.*, `plans`.`name` AS `plan_name`
            FROM `codes`
            LEFT JOIN `plans` ON `codes`.`plan_id` = `plans`.`plan_id`
        ");

        /* Main View */
        $data = [
            'codes_result' => $codes_result
        ];

        $view = new \Altum\Views\View('admin/codes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        $code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$code = db()->where('code_id', $code_id)->getOne('codes', ['code_id', 'code'])) {
            redirect('admin/codes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the code */
            db()->where('code_id', $code_id)->delete('codes');

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $code->code . '</strong>'));

        }

        redirect('admin/codes');
    }

}
