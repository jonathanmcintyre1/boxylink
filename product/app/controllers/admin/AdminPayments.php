<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Models\Plan;
use Altum\Response;

class AdminPayments extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['status', 'plan_id', 'user_id', 'type', 'processor', 'frequency'], ['name', 'email'], ['total_amount', 'email', 'date', 'name']));

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `payments` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/payments?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $payments = [];
        $payments_result = Database::$database->query("
            SELECT
                `payments`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `payments`
            LEFT JOIN
                `users` ON `payments`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('payments')}
                {$filters->get_sql_order_by('payments')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $payments_result->fetch_object()) {
            $payments[] = $row;
        }

        /* Export handler */
        process_export_json($payments, 'include', ['id', 'user_id', 'plan_id', 'payment_id', 'subscription_id', 'payer_id', 'email', 'name', 'processor', 'type', 'frequency', 'billing', 'taxes_ids', 'base_amount', 'code', 'discount_amount', 'total_amount', 'currency', 'status', 'date']);
        process_export_csv($payments, 'include', ['id', 'user_id', 'plan_id', 'payment_id', 'subscription_id', 'payer_id', 'email', 'name', 'processor', 'type', 'frequency', 'base_amount', 'code', 'discount_amount', 'total_amount', 'currency', 'status', 'date']);

        /* Requested plan details */
        $plans = [];
        $plans_result = Database::$database->query("SELECT `plan_id`, `name` FROM `plans`");
        while($row = $plans_result->fetch_object()) {
            $plans[$row->plan_id] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/payments/payment_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Approve Modal */
        $view = new \Altum\Views\View('admin/payments/payment_approve_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'payments' => $payments,
            'plans' => $plans,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\Views\View('admin/payments/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


    public function delete() {

        Authentication::guard();

        $payment_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/users');
        }

        if(empty($_SESSION['error'])) {
            $payment = Database::get(['payment_proof'], 'payments', ['id' => $payment_id]);

            /* Delete the saved proof, if any */
            if($payment->payment_proof) {
                unlink(UPLOADS_PATH . 'offline_payment_proofs/' . $payment->payment_proof);
            }

            /* Delete the payment */
            Database::$database->query("DELETE FROM `payments` WHERE `id` = {$payment_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_payment_delete_modal->success_message;

        }

        redirect('admin/payments');
    }

    public function approve() {

        Authentication::guard();

        $payment_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/users');
        }

        if(empty($_SESSION['error'])) {
            $payment = Database::get(['plan_id', 'user_id', 'frequency', 'email', 'code', 'payment_proof', 'payer_id'], 'payments', ['id' => $payment_id]);
            $plan = (new \Altum\Models\Plan(['settings' => $this->settings]))->get_plan_by_id($payment->plan_id);

            /* Make sure the code that was potentially used exists */
            $codes_code = Database::get('*', 'codes', ['code' => $payment->code, 'type' => 'discount']);

            if($codes_code) {
                /* Check if we should insert the usage of the code or not */
                if(!Database::exists('id', 'redeemed_codes', ['user_id' => $payment->user_id, 'code_id' => $codes_code->code_id])) {
                    /* Update the code usage */
                    $this->database->query("UPDATE `codes` SET `redeemed` = `redeemed` + 1 WHERE `code_id` = {$codes_code->code_id}");

                    /* Add log for the redeemed code */
                    Database::insert('redeemed_codes', [
                        'code_id'   => $codes_code->code_id,
                        'user_id'   => $payment->user_id,
                        'date'      => \Altum\Date::$date
                    ]);

                    Logger::users($payment->user_id, 'codes.redeemed_code=' . $codes_code->code);
                }
            }

            /* Give the plan to the user */
            switch($payment->frequency) {
                case 'monthly':
                    $plan_expiration_date = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');
                    break;

                case 'annual':
                    $plan_expiration_date = (new \DateTime())->modify('+12 months')->format('Y-m-d H:i:s');
                    break;

                case 'lifetime':
                    $plan_expiration_date = (new \DateTime())->modify('+100 years')->format('Y-m-d H:i:s');
                    break;
            }

            Database::update(
                'users',
                [
                    'plan_id' => $payment->plan_id,
                    'plan_settings' => json_encode($plan->settings),
                    'plan_expiration_date' => $plan_expiration_date
                ],
                [
                    'user_id' => $payment->payer_id
                ]
            );

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $payment->payer_id);

            /* Send notification to the user */
            /* Prepare the email */
            $email_template = get_email_template(
                [],
                $this->language->global->emails->user_payment->subject,
                [
                    '{{PLAN_EXPIRATION_DATE}}' => Date::get($plan_expiration_date, 2),
                    '{{USER_PLAN_LINK}}' => url('account-plan'),
                    '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                ],
                $this->language->global->emails->user_payment->body
            );

            send_mail(
                $this->settings,
                $payment->email,
                $email_template->subject,
                $email_template->body
            );

            /* Update the payment */
            Database::$database->query("UPDATE `payments` SET `status` = 1 WHERE `id` = {$payment_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_payment_approve_modal->success_message;

        }

        redirect('admin/payments');
    }
}
