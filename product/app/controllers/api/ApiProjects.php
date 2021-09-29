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
use Altum\Response;
use Altum\Traits\Apiable;

class ApiProjects extends Controller {
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
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `projects` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/projects?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `projects`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->project_id,
                'name' => $row->name,
                'color' => $row->color,
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

        $project_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $project = db()->where('project_id', $project_id)->where('user_id', $this->api_user->user_id)->getOne('projects');

        /* We haven't found the resource */
        if(!$project) {
            Response::jsonapi_error([[
                'title' => language()->api->error_message->not_found,
                'status' => '404'
            ]], null, 404);
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $project->project_id,
            'name' => $project->name,
            'color' => $project->color,
            'last_datetime' => $project->last_datetime,
            'datetime' => $project->datetime,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = db()->where('user_id', $this->api_user->user_id)->getValue('projects', 'count(`project_id`)');

        if($this->api_user->plan_settings->projects_limit != -1 && $total_rows >= $this->api_user->plan_settings->projects_limit) {
            $this->response_error(language()->projects->error_message->projects_limit, 401);
        }

        /* Check for any errors */
        $required_fields = ['name'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]))) {
                $this->response_error(language()->global->error_message->empty_fields, 401);
                break 1;
            }
        }

        $_POST['name'] = trim($_POST['name']);
        $_POST['color'] = !isset($_POST['color']) || !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#000' : $_POST['color'];

        /* Prepare the statement and execute query */
        $project_id = db()->insert('projects', [
            'user_id' => $this->api_user->user_id,
            'name' => $_POST['name'],
            'color' => $_POST['color'],
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('projects?user_id=' . $this->api_user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $project_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $project_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $project = db()->where('project_id', $project_id)->where('user_id', $this->api_user->user_id)->getOne('projects');

        /* We haven't found the resource */
        if(!$project) {
            $this->response_error(language()->api->error_message->not_found, 404);
        }

        $_POST['name'] = trim($_POST['name'] ?? $project->name);
        $_POST['color'] = isset($_POST['color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? $_POST['color'] : $project->color;

        /* Database query */
        db()->where('project_id', $project->project_id)->update('projects', [
            'name' => $_POST['name'],
            'color' => $_POST['color'],
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('projects?user_id=' . $this->api_user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $project->project_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $project_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $project = db()->where('project_id', $project_id)->where('user_id', $this->api_user->user_id)->getOne('projects');

        /* We haven't found the resource */
        if(!$project) {
            $this->response_error(language()->api->error_message->not_found, 404);
        }

        /* Delete the project */
        db()->where('project_id', $project_id)->delete('projects');

        http_response_code(200);
        die();

    }

}
