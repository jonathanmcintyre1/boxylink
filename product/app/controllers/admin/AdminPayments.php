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
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Models\Payments;

class AdminPayments extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['status', 'plan_id', 'user_id', 'type', 'processor', 'frequency'], ['name', 'email'], ['total_amount', 'email', 'date', 'name']));

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `payments` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/payments?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $payments = [];
        $payments_result = database()->query("
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
        $plans_result = database()->query("SELECT `plan_id`, `name` FROM `plans`");
        while($row = $plans_result->fetch_object()) {
            $plans[$row->plan_id] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

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

        $payment_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            redirect('admin/users');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $payment = db()->where('id', $payment_id)->getOne('payments', ['payment_proof']);

            /* Delete the saved proof, if any */
            if($payment->payment_proof) {
                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/offline_payment_proofs/' . $payment->payment_proof,
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($payment->payment_proof) && file_exists(UPLOADS_PATH . 'offline_payment_proofs/' . $payment->payment_proof)) {
                        unlink(UPLOADS_PATH . 'offline_payment_proofs/' . $payment->payment_proof);
                    }
                }
            }

            /* Delete the payment */
            db()->where('id', $payment_id)->delete('payments');

            /* Set a nice success message */
            Alerts::add_success(language()->global->success_message->delete2);

        }

        redirect('admin/payments');
    }

    public function approve() {

        $payment_id = (isset($this->params[0])) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            redirect('admin/users');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* details about the payment */
            $payment = db()->where('id', $payment_id)->getOne('payments', ['plan_id', 'user_id', 'frequency', 'email', 'code', 'payment_proof', 'payer_id', 'total_amount']);

            /* details about the user who paid */
            $user = db()->where('user_id', $payment->user_id)->getOne('users');

            /* plan that the user has paid for */
            $plan = (new \Altum\Models\Plan())->get_plan_by_id($payment->plan_id);

            /* Make sure the code that was potentially used exists */
            $codes_code = db()->where('code', $payment->code)->where('type', 'discount')->getOne('codes');

            if($codes_code) {
                /* Check if we should insert the usage of the code or not */
                if(!db()->where('user_id', $payment->user_id)->where('code_id', $codes_code->code_id)->has('redeemed_codes')) {

                    /* Update the code usage */
                    db()->where('code_id', $codes_code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                    /* Add log for the redeemed code */
                    db()->insert('redeemed_codes', [
                        'code_id'   => $codes_code->code_id,
                        'user_id'   => $user->user_id,
                        'date'      => \Altum\Date::$date
                    ]);
                }
            }

            /* Give the plan to the user */
            $current_plan_expiration_date = $payment->plan_id == $user->user_id ? $user->plan_expiration_date : '';
            switch($payment->frequency) {
                case 'monthly':
                    $plan_expiration_date = (new \DateTime($current_plan_expiration_date))->modify('+30 days')->format('Y-m-d H:i:s');
                    break;

                case 'annual':
                    $plan_expiration_date = (new \DateTime($current_plan_expiration_date))->modify('+12 months')->format('Y-m-d H:i:s');
                    break;

                case 'lifetime':
                    $plan_expiration_date = (new \DateTime($current_plan_expiration_date))->modify('+100 years')->format('Y-m-d H:i:s');
                    break;
            }

            /* Database query */
            db()->where('user_id', $user->user_id)->update('users', [
                'plan_id' => $payment->plan_id,
                'plan_settings' => json_encode($plan->settings),
                'plan_expiration_date' => $plan_expiration_date,
                'plan_expiry_reminder' => 0,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

            /* Send notification to the user */
            $email_template = get_email_template(
                [],
                language()->global->emails->user_payment->subject,
                [
                    '{{NAME}}' => $user->name,
                    '{{PLAN_EXPIRATION_DATE}}' => Date::get($plan_expiration_date, 2),
                    '{{USER_PLAN_LINK}}' => url('account-plan'),
                    '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                ],
                language()->global->emails->user_payment->body
            );

            send_mail($user->email, $email_template->subject, $email_template->body);

            /* Update the payment */
            db()->where('id', $payment_id)->update('payments', ['status' => 1]);

            /* Affiliate */
            (new Payments())->affiliate_payment_check($payment_id, $payment->total_amount, $user);

            /* Set a nice success message */
            Alerts::add_success(language()->admin_payment_approve_modal->success_message);

        }

        redirect('admin/payments');
    }
}
