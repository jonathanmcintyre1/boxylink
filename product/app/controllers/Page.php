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
use Altum\Title;

class Page extends Controller {

    public function index() {

        $url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : null;

        /* If the custom page url is set then try to get data from the database */
        $page = $url ? database()->query("
            SELECT
                `pages`.*,
                `pages_categories`.`url` AS `pages_category_url`,
                `pages_categories`.`title` AS `pages_category_title`
            FROM `pages`
            LEFT JOIN `pages_categories` ON `pages_categories`.`pages_category_id` = `pages`.`pages_category_id`
            WHERE
                `pages`.`url` = '{$url}' 
        ")->fetch_object() ?? null : null;

        /* Redirect if the page does not exist */
        if(!$page) {
            redirect('pages');
        }

        /* Add a new view to the page */
        db()->where('page_id', $page->page_id)->update('pages', ['total_views' => db()->inc()]);

        /* Prepare the View */
        $data = [
            'page'  => $page
        ];

        $view = new \Altum\Views\View('page/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

        /* Set a custom title */
        Title::set($page->title);

    }

}


