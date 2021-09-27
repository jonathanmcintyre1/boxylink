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
use Altum\Database\Database;
use Altum\Middlewares\Csrf;

class AdminDomainUpdate extends Controller {

    public function index() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if user exists */
        if(!$domain = db()->where('domain_id', $domain_id)->getOne('domains')) {
            redirect('admin/domains');
        }

        /* Get some user details of the domain owner */
        $user = db()->where('user_id', $domain->user_id)->getOne('users', ['user_id', 'email', 'name']);

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? Database::clean_string($_POST['scheme']) : 'https://';
            $_POST['host'] = mb_strtolower(trim($_POST['host']));
            $_POST['custom_index_url'] = trim(Database::clean_string($_POST['custom_index_url']));
            $_POST['custom_not_found_url'] = trim(Database::clean_string($_POST['custom_not_found_url']));
            $_POST['is_enabled'] = (int) (bool) $_POST['is_enabled'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['host'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                    Alerts::add_field_error($field, language()->global->error_message->empty_field);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the row of the database */
                db()->where('domain_id', $domain->domain_id)->update('domains', [
                    'scheme' => $_POST['scheme'],
                    'host' => $_POST['host'],
                    'custom_index_url' => $_POST['custom_index_url'],
                    'custom_not_found_url' => $_POST['custom_not_found_url'],
                    'is_enabled' => $_POST['is_enabled'],
                ]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $domain->domain_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->update1, '<strong>' . htmlspecialchars($_POST['host']) . '</strong>'));

                redirect('admin/domain-update/' . $domain->domain_id);
            }

        }

        /* Main View */
        $data = [
            'user' => $user,
            'domain' => $domain
        ];

        $view = new \Altum\Views\View('admin/domain-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
