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
use Altum\Models\Domain;
use Altum\Models\Model;

class AdminDomains extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'user_id', 'type'], ['host'], ['datetime', 'host']));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/domains?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $domains = [];
        $domains_result = database()->query("
            SELECT
                `domains`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `domains`
            LEFT JOIN
                `users` ON `domains`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('domains')}
                {$filters->get_sql_order_by('domains')}
            
            {$paginator->get_sql_limit()}
        ");
        while($row = $domains_result->fetch_object()) {
            $domains[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'domains' => $domains,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('admin/domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/domains');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/domains');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/domains');
        }

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $domain_id) {
                        (new Domain())->delete($domain_id);
                    }

                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(language()->admin_bulk_delete_modal->success_message);

        }

        redirect('admin/domains');
    }

    public function delete() {

        $domain_id = (isset($this->params[0])) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$domain = db()->where('domain_id', $domain_id)->getOne('domains')) {
            redirect('admin/domains');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new Domain())->delete($domain->domain_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $domain->host . '</strong>'));

        }

        redirect('admin/domains');
    }

}
