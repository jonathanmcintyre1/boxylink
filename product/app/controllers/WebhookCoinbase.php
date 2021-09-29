<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Date;
use Altum\Models\Model;
use Altum\Models\Payments;
use Altum\Models\User;
use Altum\PaymentGateways\Coinbase;

class WebhookCoinbase extends Controller {

    public function index() {

        /* Verify the source of the webhook event */
        $headers = getallheaders();
        $signature_header = isset($headers['X-Cc-Webhook-Signature']) ? $headers['X-Cc-Webhook-Signature'] : null;
        $payload = trim(@file_get_contents('php://input'));

        try {
            $data = Coinbase::verify_webhook_signature($payload, $signature_header);
        } catch (\Exception $exception) {
            if(DEBUG) {
                error_log($exception->getMessage());
            }
            echo $exception->getMessage();
            http_response_code(400); die();
        }

        if($data->event->type == 'charge:confirmed') {

            /* Start getting the payment details */
            $payment_id = $data->event->data->id;
            $payment_total = $data->event->data->pricing->local->amount;
            $payment_currency = $data->event->data->pricing->local->currency;
            $payment_type = 'one_time';

            /* Process meta data */
            $metadata = $data->event->data->metadata;
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            /* Get the plan details */
            $plan = db()->where('plan_id', $plan_id)->getOne('plans');

            /* Just make sure the plan is still existing */
            if(!$plan) {
                http_response_code(400); die();
            }

            /* Make sure the transaction is not already existing */
            if(db()->where('payment_id', $payment_id)->where('processor', 'coinbase')->has('payments')) {
                http_response_code(400); die();
            }

            /* Make sure the account still exists */
            $user = db()->where('user_id', $user_id)->getOne('users');

            if(!$user) {
                http_response_code(400); die();
            }

            /* Unsubscribe from the previous plan if needed */
            if(!empty($user->payment_subscription_id)) {
                try {
                    (new User())->cancel_subscription($user_id);
                } catch (\Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode() . ':' . $exception->getMessage();

                        die();
                    }
                }
            }

            /* Codes */
            $code = (new Payments())->codes_payment_check($code, $user);

            /* Payment payer details */
            $payer_email = $user->email;
            $payer_name = $user->name;
            $payer_id = $user->user_id;

            /* Add a log into the database */
            $payment_id = db()->insert('payments', [
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'processor' => 'coinbase',
                'type' => $payment_type,
                'frequency' => $payment_frequency,
                'code' => $code,
                'discount_amount' => $discount_amount,
                'base_amount' => $base_amount,
                'email' => $payer_email,
                'payment_id' => $payment_id,
                'subscription_id' => '',
                'payer_id' => $payer_id,
                'name' => $payer_name,
                'billing' => settings()->payment->taxes_and_billing_is_enabled && $user->billing ? $user->billing : null,
                'taxes_ids' => !empty($taxes_ids) ? $taxes_ids : null,
                'total_amount' => $payment_total,
                'currency' => $payment_currency,
                'date' => \Altum\Date::$date
            ]);

            /* Update the user with the new plan */
            $current_plan_expiration_date = $plan_id == $user->plan_id ? $user->plan_expiration_date : '';
            switch($payment_frequency) {
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
            db()->where('user_id', $user_id)->update('users', [
                'plan_id' => $plan_id,
                'plan_settings' => $plan->settings,
                'plan_expiration_date' => $plan_expiration_date,
                'plan_expiry_reminder' => 0,
                'payment_subscription_id' => ''
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user_id);

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

            /* Send notification to admin if needed */
            if(settings()->email_notifications->new_payment && !empty(settings()->email_notifications->emails)) {

                $email_template = get_email_template(
                    [
                        '{{PROCESSOR}}' => 'coinbase',
                        '{{TOTAL_AMOUNT}}' => $payment_total,
                        '{{CURRENCY}}' => $payment_currency,
                    ],
                    language()->global->emails->admin_new_payment_notification->subject,
                    [
                        '{{PROCESSOR}}' => 'coinbase',
                        '{{TOTAL_AMOUNT}}' => $payment_total,
                        '{{CURRENCY}}' => $payment_currency,
                        '{{NAME}}' => $user->email,
                        '{{EMAIL}}' => $user->email,
                    ],
                    language()->global->emails->admin_new_payment_notification->body
                );

                send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

            }

            /* Affiliate */
            (new Payments())->affiliate_payment_check($payment_id, $payment_total, $user);

            die('successful');
        }

        die();
    }

}
