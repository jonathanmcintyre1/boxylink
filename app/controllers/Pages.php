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

class Pages extends Controller {

    public function index() {

        /* Check if the category url is set */
        $pages_category_url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : null;

        /* If the category url is set, get it*/
        if($pages_category_url) {

            /* Pages category index */
            $pages_category = $pages_category_url ? db()->where('url', $pages_category_url)->getOne('pages_categories') : null;

            /* Redirect to pages if the category is not found */
            if(!$pages_category) {
                redirect('pages');
            }

            /* Get the pages for this category */
            $pages_result = database()->query("SELECT `url`, `title`, `description`, `total_views`, `type` FROM `pages` WHERE `pages_category_id` = {$pages_category->pages_category_id} ORDER BY `total_views` DESC");

            /* Prepare the View */
            $data = [
                'pages_category' => $pages_category,
                'pages_result' => $pages_result
            ];

            $view = new \Altum\Views\View('pages/pages_category', (array) $this);

            /* Set a custom title */
            Title::set($pages_category->title);

        } else {

            /* Pages index */

            /* Get the popular pages */
            $popular_pages_result = database()->query("SELECT `url`, `title`, `description`, `total_views`, `type` FROM `pages` ORDER BY `total_views` DESC LIMIT 6");

            /* Get all the pages categories */
            $pages_categories_result = database()->query("
                SELECT 
                    `pages_categories`.`url`,
                    `pages_categories`.`title`,
                    `pages_categories`.`icon`,
                    COUNT(`pages`.`page_id`) AS `total_pages`
                FROM `pages_categories`
                LEFT JOIN `pages` ON `pages`.`pages_category_id` = `pages_categories`.`pages_category_id`
                GROUP BY `pages_categories`.`pages_category_id`
                ORDER BY `pages_categories`.`order` ASC
            ");

            /* Prepare the View */
            $data = [
                'popular_pages_result' => $popular_pages_result,
                'pages_categories_result' => $pages_categories_result
            ];

            $view = new \Altum\Views\View('pages/index', (array) $this);
        }

        $this->add_view_content('content', $view->run($data));



    }

}
