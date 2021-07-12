<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminUsers extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['active', 'plan_id', 'country'], ['name', 'email'], ['email', 'date', 'last_activity', 'name', 'total_logins']));

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/users?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $users = [];
        $users_result = Database::$database->query("
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
        $plans['free'] = (new Plan(['settings' => $this->settings]))->get_plan_by_id('free');
        $plans['trial'] = (new Plan(['settings' => $this->settings]))->get_plan_by_id('trial');
        $plans['custom'] = (new Plan(['settings' => $this->settings]))->get_plan_by_id('custom');
        $plans_result = Database::$database->query("SELECT `plan_id`, `name` FROM `plans`");
        while($row = $plans_result->fetch_object()) {
            $plans[$row->plan_id] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/users/user_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Login Modal */
        $view = new \Altum\Views\View('admin/users/user_login_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'users' => $users,
            'plans' => $plans,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\Views\View('admin/users/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function login() {

        Authentication::guard();

        $user_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/users');
        }

        if($user_id == $this->user->user_id) {
            redirect('admin/users');
        }

        /* Check if user exists */
        if(!$user = Database::get('*', 'users', ['user_id' => $user_id])) {
            redirect('admin/users');
        }

        if(empty($_SESSION['error'])) {

            /* Logout of the admin */
            Authentication::logout(false);

            /* Login as the new user */
            session_start();
            $_SESSION['user_id'] = $user->user_id;

            /* Success message */
            $_SESSION['success'][] = sprintf($this->language->admin_user_login_modal->success_message, $user->name);

            redirect('dashboard');

        }

        redirect('admin/users');
    }

    public function delete() {

        Authentication::guard();

        $user_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/users');
        }

        if($user_id == $this->user->user_id) {
            $_SESSION['error'][] = $this->language->admin_users->error_message->self_delete;
            redirect('admin/users');
        }

        if(empty($_SESSION['error'])) {

            /* Delete the user */
            (new User(['settings' => $this->settings]))->delete($user_id);

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_user_delete_modal->success_message;

        }

        redirect('admin/users');
    }

}
