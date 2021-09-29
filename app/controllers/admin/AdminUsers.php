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
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Model;
use Altum\Models\Plan;
use Altum\Models\User;

class AdminUsers extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['active', 'plan_id', 'country'], ['name', 'email'], ['email', 'date', 'last_activity', 'name', 'total_logins']));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/users?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $users = [];
        $users_result = database()->query("
            SELECT
                *
            FROM
                `users`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $users_result->fetch_object()) {
            $users[] = $row;
        }

        /* Export handler */
        process_export_json($users, 'include', ['user_id', 'email', 'name', 'facebook_id', 'billing', 'plan_id', 'plan_settings', 'plan_expiration_date', 'plan_trial_done', 'active', 'language', 'timezone', 'country', 'date', 'last_activity', 'total_logins']);
        process_export_csv($users, 'include', ['user_id', 'email', 'name', 'facebook_id', 'plan_id', 'plan_expiration_date', 'plan_trial_done', 'active', 'language', 'timezone', 'country', 'date', 'last_activity', 'total_logins']);

        /* Requested plan details */
        $plans = [];
        $plans['free'] = (new Plan())->get_plan_by_id('free');
        $plans['custom'] = (new Plan())->get_plan_by_id('custom');
        $plans_result = database()->query("SELECT `plan_id`, `name` FROM `plans`");
        while($row = $plans_result->fetch_object()) {
            $plans[$row->plan_id] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'users' => $users,
            'plans' => $plans,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\Views\View('admin/users/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function login() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            redirect('admin/users');
        }

        if($user_id == $this->user->user_id) {
            redirect('admin/users');
        }

        /* Check if user exists */
        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Logout of the admin */
            Authentication::logout(false);

            /* Login as the new user */
            session_start();
            $_SESSION['user_id'] = $user->user_id;

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->admin_user_login_modal->success_message, $user->name));

            redirect('dashboard');

        }

        redirect('admin/users');
    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/users');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/users');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/users');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $user_id) {
                        (new User())->delete((int) $user_id);
                    }
                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(language()->admin_bulk_delete_modal->success_message);

        }

        redirect('admin/users');
    }

    public function delete() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if($user_id == $this->user->user_id) {
            Alerts::add_error(language()->admin_users->error_message->self_delete);
        }

        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the user */
            (new User())->delete($user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $user->name . '</strong>'));

        }

        redirect('admin/users');
    }

}
