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

class AdminUsersLogs extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id'], ['type', 'ip', 'country_code', 'device_type'], ['datetime']));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users_logs` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/users-logs?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $users_logs = [];
        $users_logs_result = database()->query("
            SELECT
                `users_logs`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `users_logs`
            LEFT JOIN
                `users` ON `users_logs`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('users_logs')}
                {$filters->get_sql_order_by('users_logs')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $users_logs_result->fetch_object()) {
            $users_logs[] = $row;
        }

        /* Export handler */
        process_export_json($users_logs, 'include', ['user_id', 'type', 'ip', 'country_code', 'device_type', 'datetime']);
        process_export_csv($users_logs, 'include', ['user_id', 'type', 'ip', 'country_code', 'device_type', 'datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'users_logs' => $users_logs,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\Views\View('admin/users-logs/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/users-logs');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/users-logs');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/users-logs');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $id) {
                        db()->where('id', $id)->delete('users_logs');
                    }
                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(language()->admin_bulk_delete_modal->success_message);

        }

        redirect('admin/users-logs');
    }

    public function delete() {

        $id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$user_log = db()->where('id', $id)->getOne('users_logs')) {
            redirect('admin/users-logs');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the user log */
            db()->where('id', $id)->delete('users_logs');

            /* Set a nice success message */
            Alerts::add_success(language()->global->success_message->delete2);

        }

        redirect('admin/users-logs');
    }

}
