<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Middlewares\Authentication;

class AdminDomainUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $domain_id = (isset($this->params[0])) ? $this->params[0] : false;

        /* Check if user exists */
        if(!$domain = Database::get('*', 'domains', ['domain_id' => $domain_id])) {
            redirect('admin/domains');
        }

        /* Get some user details of the domain owner */
        $user = Database::get(['user_id', 'email', 'name'], 'users', ['user_id' => $domain->user_id]);

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
            $_POST['host'] = trim(Database::clean_string($_POST['host']));
            $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));
            $_POST['is_enabled'] = (int) (bool) $_POST['is_enabled'];

            /* Must have fields */
            $required_fields = ['scheme', 'host'];

            /* Check for the required fields */
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {

                /* Update the row of the database */
                $stmt = Database::$database->prepare("UPDATE `domains` SET `scheme` = ?, `host` = ?, `custom_index_url` = ?, `is_enabled` = ? WHERE `domain_id` = ?");
                $stmt->bind_param('sssss', $_POST['scheme'], $_POST['host'], $_POST['custom_index_url'], $_POST['is_enabled'], $domain->domain_id);
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'][] = $this->language->global->success_message->basic;

                redirect('admin/domain-update/' . $domain->domain_id);
            }

        }

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/domains/domain_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'user' => $user,
            'domain' => $domain
        ];

        $view = new \Altum\Views\View('admin/domain-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
