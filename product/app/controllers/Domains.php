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
use Altum\Middlewares\Csrf;
use Altum\Models\Domain;
use Altum\Response;

class Domains extends Controller {

    public function index() {

        Authentication::guard();

        if(!settings()->links->domains_is_enabled) {
            redirect('dashboard');
        }

        /* Create Modal */
        $view = new \Altum\Views\View('domains/domain_create_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Update Modal */
        $view = new \Altum\Views\View('domains/domain_update_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('domains/domain_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('domains?page=%d')));

        /* Get the domains list for the user */
        $domains = [];
        $domains_result = database()->query("SELECT * FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0 {$paginator->get_sql_limit()}");
        while($row = $domains_result->fetch_object()) $domains[] = $row;

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the View */
        $data = [
            'domains'       => $domains,
            'total_domains' => $total_rows,
            'pagination'    => $pagination
        ];

        $view = new \Altum\Views\View('domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    /* Ajax method */
    public function create() {
        Authentication::guard();

        if(!settings()->links->domains_is_enabled) {
            die();
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
        $_POST['host'] = mb_strtolower(trim($_POST['host']));
        $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));
        $_POST['custom_not_found_url'] = trim(Database::clean_string($_POST['custom_not_found_url']));

        /* Make sure that the user didn't exceed the limit */
        $user_total_domains = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0")->fetch_object()->total;
        if($this->user->plan_settings->domains_limit != -1 && $user_total_domains >= $this->user->plan_settings->domains_limit) {
            Response::json(language()->domains->error_message->domains_limit, 'error');
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(empty($errors)) {

            /* Define some needed variables */
            $type = 0;

            /* Add the row to the database */
            $domain_id = db()->insert('domains', [
                'user_id' => $this->user->user_id,
                'scheme' => $_POST['scheme'],
                'host' => $_POST['host'],
                'custom_index_url' => $_POST['custom_index_url'],
                'custom_not_found_url' => $_POST['custom_not_found_url'],
                'type' => $type,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Send notification to admin if needed */
            if(settings()->email_notifications->new_domain && !empty(settings()->email_notifications->emails)) {

                /* Prepare the email */
                $email_template = get_email_template(
                    [],
                    language()->global->emails->admin_new_domain_notification->subject,
                    [
                        '{{ADMIN_DOMAIN_UPDATE_LINK}}' => url('admin/domain-update/' . $domain_id),
                        '{{DOMAIN_HOST}}' => $_POST['host'],
                        '{{NAME}}' => $this->user->name,
                        '{{EMAIL}}' => $this->user->email,
                    ],
                    language()->global->emails->admin_new_domain_notification->body
                );

                send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

            }

            Response::json(language()->domain_create_modal->success_message, 'success');

        }
    }

    /* Ajax method */
    public function update() {
        Authentication::guard();

        if(!settings()->links->domains_is_enabled) {
            die();
        }

        $_POST['domain_id'] = (int) $_POST['domain_id'];
        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
        $_POST['host'] = mb_strtolower(trim($_POST['host']));
        $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));
        $_POST['custom_not_found_url'] = trim(Database::clean_string($_POST['custom_not_found_url']));

        if(!$domain = db()->where('domain_id', $_POST['domain_id'])->where('user_id', $this->user->user_id)->getOne('domains')) {
            die();
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(empty($errors)) {

            $is_enabled = $domain->is_enabled;

            /* Add the domain on pending if the host has changed */
            if($_POST['host'] != $domain->host) {
                $is_enabled = 0;
            }

            /* Update the database */
            db()->where('domain_id', $domain->domain_id)->update('domains', [
                'scheme' => $_POST['scheme'],
                'host' => $_POST['host'],
                'custom_index_url' => $_POST['custom_index_url'],
                'custom_not_found_url' => $_POST['custom_not_found_url'],
                'is_enabled' => $is_enabled,
                'last_datetime' => \Altum\Date::$date,
            ]);

            /* Send notification to admin if needed */
            if(!$is_enabled && settings()->email_notifications->new_domain && !empty(settings()->email_notifications->emails)) {

                /* Prepare the email */
                $email_template = get_email_template(
                    [],
                    language()->global->emails->admin_new_domain_notification->subject,
                    [
                        '{{ADMIN_DOMAIN_UPDATE_LINK}}' => url('admin/domain-update/' . $domain->domain_id),
                        '{{DOMAIN_HOST}}' => $_POST['host'],
                        '{{NAME}}' => $this->user->name,
                        '{{EMAIL}}' => $this->user->email,
                    ],
                    language()->global->emails->admin_new_domain_notification->body
                );

                send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

            }

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $domain->domain_id);

            Response::json(language()->domain_update_modal->success_message, 'success');

        }
    }

    /* Ajax method */
    public function delete() {
        Authentication::guard();

        if(!settings()->links->domains_is_enabled) {
            die();
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token'))) {

            $_POST['domain_id'] = (int) $_POST['domain_id'];

            /* Check for possible errors */
            if(!$domain = db()->where('domain_id', $_POST['domain_id'])->where('user_id', $this->user->user_id)->getOne('domains', ['domain_id', 'host'])) {
                die();
            }

            (new Domain())->delete($_POST['domain_id']);

            /* Set a nice success message */
            Response::json(sprintf(language()->global->success_message->delete1, '<strong>' . $domain->host . '</strong>'));

        }

    }

}
