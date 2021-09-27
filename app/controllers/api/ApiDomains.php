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
use Altum\Models\Domain;
use Altum\Response;
use Altum\Traits\Apiable;

class ApiDomains extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                } else {
                    $this->get_all();
                }

            break;

            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0])) {
                    $this->patch();
                } else {
                    $this->post();
                }

            break;

            case 'DELETE':
                $this->delete();
            break;
        }

        $this->return_404();
    }

    private function get_all() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/domains?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `domains`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->domain_id,
                'scheme' => $row->scheme,
                'host' => $row->host,
                'custom_index_url' => $row->custom_index_url,
                'is_enabled' => (bool) $row->is_enabled,
                'last_datetime' => $row->last_datetime,
                'datetime' => $row->datetime,
            ];

            $data[] = $row;
        }

        /* Prepare the data */
        $meta = [
            'page' => $_GET['page'] ?? 1,
            'total_pages' => $paginator->getNumPages(),
            'results_per_page' => $filters->get_results_per_page(),
            'total_results' => (int) $total_rows,
        ];

        /* Prepare the pagination links */
        $others = ['links' => [
            'first' => $paginator->getPageUrl(1),
            'last' => $paginator->getNumPages() ? $paginator->getPageUrl($paginator->getNumPages()) : null,
            'next' => $paginator->getNextUrl(),
            'prev' => $paginator->getPrevUrl(),
            'self' => $paginator->getPageUrl($_GET['page'] ?? 1)
        ]];

        Response::jsonapi_success($data, $meta, 200, $others);
    }

    private function get() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $domain = db()->where('domain_id', $domain_id)->where('user_id', $this->api_user->user_id)->getOne('domains');

        /* We haven't found the resource */
        if(!$domain) {
            Response::jsonapi_error([[
                'title' => language()->api->error_message->not_found,
                'status' => '404'
            ]], null, 404);
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $domain->domain_id,
            'scheme' => $domain->scheme,
            'host' => $domain->host,
            'custom_index_url' => $domain->custom_index_url,
            'is_enabled' => (bool) $domain->is_enabled,
            'last_datetime' => $domain->last_datetime,
            'datetime' => $domain->datetime,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;

        if($this->api_user->plan_settings->domains_limit != -1 && $total_rows >= $this->api_user->plan_settings->domains_limit) {
            $this->response_error(language()->domains->error_message->domains_limit, 401);
        }

        /* Check for any errors */
        $required_fields = ['host'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                $this->response_error(language()->global->error_message->empty_fields, 401);
                break 1;
            }
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? $_POST['scheme'] : 'https://';
        $_POST['host'] = mb_strtolower(trim($_POST['host'] ?? null));
        $_POST['custom_index_url'] = trim($_POST['custom_index_url'] ?? null);
        $_POST['custom_not_found_url'] = trim($_POST['custom_not_found_url'] ?? null);
        $type = 0;

        /* Prepare the statement and execute query */
        $domain_id = db()->insert('domains', [
            'user_id' => $this->api_user->user_id,
            'scheme' => $_POST['scheme'],
            'host' => $_POST['host'],
            'custom_index_url' => $_POST['custom_index_url'],
            'custom_not_found_url' => $_POST['custom_not_found_url'],
            'type' => $type,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('domains?user_id=' . $this->api_user->user_id);

        /* Send notification to admin if needed */
        if(settings()->email_notifications->new_domain && !empty(settings()->email_notifications->emails)) {

            /* Prepare the email */
            $email_template = get_email_template(
                [],
                language()->global->emails->admin_new_domain_notification->subject,
                [
                    '{{ADMIN_DOMAIN_UPDATE_LINK}}' => url('admin/domain-update/' . $domain_id),
                    '{{DOMAIN_HOST}}' => $_POST['host'],
                    '{{NAME}}' => $this->api_user->name,
                    '{{EMAIL}}' => $this->api_user->email,
                ],
                language()->global->emails->admin_new_domain_notification->body
            );

            send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

        }

        /* Prepare the data */
        $data = [
            'id' => $domain_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $domain = db()->where('domain_id', $domain_id)->where('user_id', $this->api_user->user_id)->getOne('domains');

        /* We haven't found the resource */
        if(!$domain) {
            $this->response_error(language()->api->error_message->not_found, 404);
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? $_POST['scheme'] : $domain->scheme;
        $_POST['host'] = mb_strtolower(trim($_POST['host'] ?? null));
        $_POST['custom_index_url'] = trim($_POST['custom_index_url'] ?? $domain->custom_index_url);
        $_POST['custom_not_found_url'] = trim($_POST['custom_not_found_url'] ?? $domain->custom_not_found_url);
        $is_enabled = $domain->is_enabled;

        /* Set the domain to pending if domain has changed */
        if($domain->host != $_POST['host']) {
            $is_enabled = 0;
        }

        /* Database query */
        db()->where('domain_id', $domain->domain_id)->update('domains', [
            'scheme' => $_POST['scheme'],
            'host' => $_POST['host'],
            'custom_index_url' => $_POST['custom_index_url'],
            'custom_not_found_url' => $_POST['custom_not_found_url'],
            'is_enabled' => $is_enabled,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $domain->domain_id);

        /* Send notification to admin if needed */
        if(!$is_enabled && settings()->email_notifications->new_domain && !empty(settings()->email_notifications->emails)) {

            /* Prepare the email */
            $email_template = get_email_template(
                [],
                language()->global->emails->admin_new_domain_notification->subject,
                [
                    '{{ADMIN_DOMAIN_UPDATE_LINK}}' => url('admin/domain-update/' . $domain->domain_id),
                    '{{DOMAIN_HOST}}' => $_POST['host'],
                    '{{NAME}}' => $this->api_user->name,
                    '{{EMAIL}}' => $this->api_user->email,
                ],
                language()->global->emails->admin_new_domain_notification->body
            );

            send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

        }

        /* Prepare the data */
        $data = [
            'id' => $domain->domain_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $domain = db()->where('domain_id', $domain_id)->where('user_id', $this->api_user->user_id)->getOne('domains');

        /* We haven't found the resource */
        if(!$domain) {
            $this->response_error(language()->api->error_message->not_found, 404);
        }

        (new Domain())->delete($domain->domain_id);

        http_response_code(200);
        die();

    }

}
