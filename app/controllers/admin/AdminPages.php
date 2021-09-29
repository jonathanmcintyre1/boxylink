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

class AdminPages extends Controller {

    public function index() {

        /* Get all the pages categories */
        $pages_categories_result = database()->query("
            SELECT 
                `pages_categories`.*,
                COUNT(`pages`.`page_id`) AS `total_pages`
            FROM `pages_categories`
            LEFT JOIN `pages` ON `pages`.`pages_category_id` = `pages_categories`.`pages_category_id`
            GROUP BY `pages_categories`.`pages_category_id`
            ORDER BY `pages_categories`.`order` ASC
        ");

        $pages_result = database()->query("
            SELECT 
                `pages`.*,
                `pages_categories`.`icon` AS `pages_category_icon`,
                `pages_categories`.`title` AS `pages_category_title`
            FROM `pages`
            LEFT JOIN `pages_categories` ON `pages_categories`.`pages_category_id` = `pages`.`pages_category_id`
            ORDER BY `pages`.`order` ASC
        ");

        /* Main View */
        $data = [
            'pages_categories_result' => $pages_categories_result,
            'pages_result' => $pages_result
        ];

        $view = new \Altum\Views\View('admin/pages/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        $page_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$page = db()->where('page_id', $page_id)->getOne('pages', ['page_id', 'title'])) {
            redirect('admin/pages');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the page */
            db()->where('page_id', $page_id)->delete('pages');

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItems(['pages_top', 'pages_bottom', 'pages_hidden']);

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $page->title . '</strong>'));

        }

        redirect('admin/pages');
    }

}
