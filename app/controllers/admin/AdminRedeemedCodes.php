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

class AdminRedeemedCodes extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id'], [], ['date']));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `redeemed_codes` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/redeemed-codes?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $redeemed_codes = [];
        $redeemed_codes_result = database()->query("
            SELECT
                `redeemed_codes`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`,
                `codes`.`code` AS `code_code`
            FROM
                `redeemed_codes`
            LEFT JOIN
                `users` ON `redeemed_codes`.`user_id` = `users`.`user_id`
            LEFT JOIN
                `codes` ON `redeemed_codes`.`code_id` = `codes`.`code_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('redeemed_codes')}
                {$filters->get_sql_order_by('redeemed_codes')}

            {$paginator->get_sql_limit()}
        ");

        while($row = $redeemed_codes_result->fetch_object()) {
            $redeemed_codes[] = $row;
        }

        /* Export handler */
        process_export_json($redeemed_codes, 'include', ['user_id', 'code_id', 'date']);
        process_export_csv($redeemed_codes, 'include', ['user_id', 'code_id', 'date']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'redeemed_codes' => $redeemed_codes,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\Views\View('admin/redeemed-codes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/redeemed-codes');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/redeemed-codes');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/redeemed-codes');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $id) {
                        db()->where('id', $id)->delete('redeemed_codes');
                    }
                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(language()->admin_bulk_delete_modal->success_message);

        }

        redirect('admin/redeemed-codes');
    }

    public function delete() {

        $id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$redeemed_code = db()->where('id', $id)->getOne('redeemed_codes')) {
            redirect('admin/redeemed-codes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the user log */
            db()->where('id', $id)->delete('redeemed_codes');

            /* Set a nice success message */
            Alerts::add_success(language()->global->success_message->delete2);

        }

        redirect('admin/redeemed-codes');
    }

}
