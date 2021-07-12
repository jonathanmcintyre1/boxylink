<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminLinks extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'user_id', 'domain_id', 'type'], ['url'], ['date', 'url', 'clicks']));

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `links` WHERE ((`type` = 'biolink' and `subtype` = 'base') OR `type` = 'link') {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/links?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $links = [];
        $links_result = Database::$database->query("
            SELECT
                `links`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `domains`.`scheme`, `domains`.`host`
            FROM
                `links`
            LEFT JOIN
                `users` ON `links`.`user_id` = `users`.`user_id`
            LEFT JOIN
                `domains` ON `links`.`domain_id` = `domains`.`domain_id`
            WHERE
                ((`links`.`type` = 'biolink' and `links`.`subtype` = 'base') OR `links`.`type` = 'link')
                {$filters->get_sql_where('links')}
                {$filters->get_sql_order_by('links')}
                
                {$paginator->get_sql_limit()}
        ");
        while($row = $links_result->fetch_object()) {
            $links[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/links/link_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'links' => $links,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('admin/links/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $link_id = (isset($this->params[0])) ? (int) $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(!$link = Database::get(['link_id', 'type', 'subtype', 'settings'], 'links', ['link_id' => $link_id])) {
            redirect('admin/links');
        }

        if(empty($_SESSION['error'])) {

            /* Delete the stored files of the biolink */
            if($link->type == 'biolink' && $link->subtype == 'base') {

                $link->settings = json_decode($link->settings);

                /* Delete current avatar */
                if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'avatars/' . $link->settings->image)) {
                    unlink(UPLOADS_PATH . 'avatars/' . $link->settings->image);
                }

                /* Delete current background */
                if(is_string($link->settings->background) && !empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                    unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                }

                /* Get all the available biolink link and iterate over them to delete the stored images */
                $result = Database::$database->query("SELECT `subtype`, `settings` FROM `links` WHERE `biolink_id` = {$link->link_id} AND `type` = 'biolink' AND `subtype` IN ('link', 'image', 'image_grid')");
                while($row = $result->fetch_object()) {
                    $row->settings = json_decode($row->settings);

                    /* Delete current image */
                    if(in_array($row->subtype, ['image', 'image_grid'])) {
                        if(!empty($row->settings->image) && file_exists(UPLOADS_PATH . 'block_images/' . $row->settings->image)) {
                            unlink(UPLOADS_PATH . 'block_images/' . $row->settings->image);
                        }
                    }

                    if(in_array($row->subtype, ['link'])) {
                        if(!empty($row->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $row->settings->image)) {
                            unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $row->settings->image);
                        }
                    }
                }

            }

            /* Delete the stored files of the link, if any */
            if($link->type == 'biolink' && in_array($link->subtype, ['image', 'image_grid'])) {
                $link->settings = json_decode($link->settings);

                /* Delete current image */
                if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'block_images/' . $link->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $link->settings->image);
                }
            }

            /* Delete the stored files of the link, if any */
            if($link->type == 'biolink' && in_array($link->subtype, ['link'])) {
                $link->settings = json_decode($link->settings);

                /* Delete current image */
                if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $link->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $link->settings->image);
                }
            }

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('biolink_links_' . $link->link_id);

            /* Delete the link */
            $this->database->query("DELETE FROM `links` WHERE `link_id` = {$link->link_id} OR `biolink_id` = {$link->link_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_link_delete_modal->success_message;

        }

        redirect('admin/links');
    }

}
