<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminDomainCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Default variables */
        $values = [
            'scheme' => '',
            'host' => '',
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
            $_POST['host'] = trim(Database::clean_string($_POST['host']));
            $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));
            $_POST['is_enabled'] = (int) (bool) $_POST['is_enabled'];

            /* Default variables */
            $values['scheme'] = $_POST['scheme'];
            $values['host'] = $_POST['host'];
            $values['custom_index_url'] = $_POST['custom_index_url'];

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

            /* If there are no errors continue the registering process */
            if(empty($_SESSION['error'])) {
                /* Define some needed variables */
                $type = 1;

                /* Add the row to the database */
                $stmt = Database::$database->prepare("INSERT INTO `domains` (`user_id`, `scheme`, `host`, `custom_index_url`, `type`, `is_enabled`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssss', $this->user->user_id, $_POST['scheme'], $_POST['host'], $_POST['custom_index_url'], $type, $_POST['is_enabled'], \Altum\Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                /* Redirect */
                redirect('admin/domains');
            }

        }

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/domains/domain_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = ['values' => $values];

        $view = new \Altum\Views\View('admin/domain-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
