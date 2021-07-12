<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Response;

class AdminDomains extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'user_id', 'type'], ['host'], ['datetime', 'host']));

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/domains?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $domains = [];
        $domains_result = Database::$database->query("
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

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/domains/domain_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'domains' => $domains,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('admin/domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $domain_id = (isset($this->params[0])) ? (int) $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(!$domain = Database::get(['domain_id'], 'domains', ['domain_id' => $domain_id])) {
            redirect('admin/domains');
        }

        if(empty($_SESSION['error'])) {

            /* Get all the available biolinks and iterate over them to delete the stored images */
            $result = Database::$database->query("SELECT `link_id`, `settings` FROM `links` WHERE `domain_id` = {$domain->domain_id} AND `type` = 'biolink' AND `subtype` = 'base'");

            while($row = $result->fetch_object()) {

                $row->settings = json_decode($row->settings);

                /* Delete current avatar */
                if(!empty($row->settings->image) && file_exists(UPLOADS_PATH . 'avatars/' . $row->settings->image)) {
                    unlink(UPLOADS_PATH . 'avatars/' . $row->settings->image);
                }

                /* Delete current background */
                if(is_string($row->settings->background) && !empty($row->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $row->settings->background)) {
                    unlink(UPLOADS_PATH . 'backgrounds/' . $row->settings->background);
                }

                /* Delete the record from the database */
                Database::$database->query("DELETE FROM `links` WHERE `link_id` = {$row->link_id}");

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('biolink_links_' . $row->link_id);

            }

            /* Delete the domain */
            $this->database->query("DELETE FROM `domains` WHERE `domain_id` = {$domain->domain_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_domain_delete_modal->success_message;

        }

        redirect('admin/domains');
    }

}
