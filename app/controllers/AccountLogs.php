<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;

class AccountLogs extends Controller {

    public function index() {

        Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id'], ['type', 'ip', 'country_code', 'device_type'], ['datetime']));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users_logs` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('account-logs?' . $filters->get_get() . 'page=%d')));

        /* Get the logs list for the user */
        $logs = [];
        $logs_result = database()->query("SELECT * FROM `users_logs` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $logs_result->fetch_object()) $logs[] = $row;

        /* Export handler */
        process_export_json($logs, 'include', ['user_id', 'type', 'ip', 'country_code', 'device_type', 'datetime']);
        process_export_csv($logs, 'include', ['user_id', 'type', 'ip', 'country_code', 'device_type', 'datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Establish the account header view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $data = [
            'logs' => $logs,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('account-logs/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
