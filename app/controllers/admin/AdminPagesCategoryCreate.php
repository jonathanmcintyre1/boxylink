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

class AdminPagesCategoryCreate extends Controller {

    public function index() {

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['url'] = get_slug(Database::clean_string($_POST['url']));
            $_POST['title'] = Database::clean_string($_POST['title']);
            $_POST['description'] = Database::clean_string($_POST['description']);
            $_POST['icon'] = Database::clean_string($_POST['icon']);
            $_POST['order'] = (int) $_POST['order'] ?? 0;

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['title', 'url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                    Alerts::add_field_error($field, language()->global->error_message->empty_field);
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(db()->where('url', $_POST['url'])->getValue('pages_categories', 'pages_category_id')) {
                Alerts::add_field_error('url', language()->admin_pages_categories->error_message->url_exists);
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('pages_categories', [
                    'url' => $_POST['url'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'icon' => $_POST['icon'],
                    'order' => $_POST['order'],
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->create1, '<strong>' . htmlspecialchars($_POST['title']) . '</strong>'));

                redirect('admin/pages');
            }

        }

        /* Set default values */
        $values = [
            'title' => $_POST['title'] ?? '',
            'url' => $_POST['url'] ?? '',
            'description' => $_POST['description'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'order' => $_POST['order'] ?? 0,
        ];

        $data = [
            'values' => $values
        ];

        /* Main View */
        $view = new \Altum\Views\View('admin/pages-category-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

}
