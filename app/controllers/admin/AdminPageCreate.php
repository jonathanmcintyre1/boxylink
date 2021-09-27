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

class AdminPageCreate extends Controller {

    public function index() {

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['title'] = Database::clean_string($_POST['title']);
            $_POST['description'] = Database::clean_string($_POST['description']);
            $_POST['type'] = in_array($_POST['type'], ['internal', 'external']) ? Database::clean_string($_POST['type']) : 'internal';
            $_POST['position'] = in_array($_POST['position'], ['hidden', 'top', 'bottom']) ? $_POST['position'] : 'top';
            $_POST['pages_category_id'] = empty($_POST['pages_category_id']) ? null : (int) $_POST['pages_category_id'];
            $_POST['order'] = (int) $_POST['order'];

            switch($_POST['type']) {
                case 'internal':
                    $_POST['url'] = get_slug(Database::clean_string($_POST['url']));
                    break;


                case 'external':
                    $_POST['url'] = Database::clean_string($_POST['url']);
                    break;
            }

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

            if(db()->where('url', $_POST['url'])->getValue('pages', 'page_id')) {
                Alerts::add_field_error('url', language()->admin_pages->error_message->url_exists);
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('pages', [
                    'pages_category_id' => $_POST['pages_category_id'],
                    'url' => $_POST['url'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'content' => $_POST['content'],
                    'type' => $_POST['type'],
                    'position' => $_POST['position'],
                    'order' => $_POST['order'],
                    'date' => \Altum\Date::$date,
                    'last_date' => \Altum\Date::$date,
                ]);

                /* Clear cache */
                \Altum\Cache::$adapter->deleteItem('pages_' . $_POST['position']);

                /* Set a nice success message */
                Alerts::add_success(sprintf(language()->global->success_message->create1, '<strong>' . htmlspecialchars($_POST['title']) . '</strong>'));

                redirect('admin/pages');
            }

        }

        /* Get the pages categories available */
        $pages_categories = db()->get('pages_categories', null, ['pages_category_id', 'title']);

        /* Set default values */
        $values = [
            'pages_category_id' => $_POST['pages_category_id'] ?? '',
            'title' => $_POST['title'] ?? '',
            'url' => $_POST['url'] ?? '',
            'description' => $_POST['description'] ?? '',
            'editor' => $_POST['editor'] ?? 'wysiwyg',
            'content' => $_POST['content'] ?? '',
            'type' => $_POST['type'] ?? '',
            'position' => $_POST['position'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'order' => $_POST['order'] ?? 0,
        ];

        $data = [
            'values' => $values,
            'pages_categories' => $pages_categories
        ];

        /* Main View */
        $view = new \Altum\Views\View('admin/page-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

}
