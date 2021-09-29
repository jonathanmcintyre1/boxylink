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

class WebhookPaypal extends Controller {

    public function index() {

        $payload = @file_get_contents('php://input');
        $data = json_decode($payload);

        if($payload && $data) {

            try {
                $paypal_api_url = \Altum\PaymentGateways\Paypal::get_api_url();
                $headers = \Altum\PaymentGateways\Paypal::get_headers();
            } catch (\Exception $exception) {
                if(DEBUG) {
                    error_log($exception->getMessage());
                }
                echo $exception->getMessage();
                http_response_code(400); die();
            }

            /* Approve one time payment order and process it */
            if($data->event_type == 'CHECKOUT.ORDER.APPROVED') {
                $response = \Unirest\Request::post($paypal_api_url . 'v2/checkout/orders/' . $data->resource->id . '/capture', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        error_log($response->body->name . ':' . $response->body->message);
                    }
                    echo $response->body->name . ':' . $response->body->message;
                    http_response_code(400); die();
                }

                /* Start getting the payment details */
                $payment_id = $response->body->id;
                $payment_total = $response->body->purchase_units[0]->payments->captures[0]->amount->value;
                $payment_currency = $response->body->purchase_units[0]->payments->captures[0]->amount->currency_code;
                $payment_type = 'one_time';

                /* Payment payer details */
                $payer_email = $response->body->payer->email_address;
                $payer_name = $response->body->payer->name->given_name . $response->body->payer->name->surname;
                $payer_id = $response->body->payer->payer_id;

                /* Parse metadata */
                $metadata = explode('&', $response->body->purchase_units[0]->payments->captures[0]->custom_id);
                $user_id = (int) $metadata[0];
                $plan_id = (int) $metadata[1];
                $payment_frequency = $metadata[2];
                $base_amount = $metadata[3];
                $code = $metadata[4];
                $discount_amount = $metadata[5] ? $metadata[5] : 0;
                $taxes_ids = $metadata[6];

                /* Get the plan details */
                $plan = db()->where('plan_id', $plan_id)->getOne('plans');

                /* Just make sure the plan is still existing */
                if(!$plan) {
                    http_response_code(400); die();
                }

                /* Make sure the transaction is not already existing */
                if(db()->where('payment_id', $payment_id)->where('processor', 'paypal')->has('payments')) {
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

                /* Add a log into the database */
                $payment_id = db()->insert('payments', [
                    'user_id' => $user_id,
                    'plan_id' => $plan_id,
                    'processor' => 'paypal',
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
                            '{{PROCESSOR}}' => 'paypal',
                            '{{TOTAL_AMOUNT}}' => $payment_total,
                            '{{CURRENCY}}' => $payment_currency,
                        ],
                        language()->global->emails->admin_new_payment_notification->subject,
                        [
                            '{{PROCESSOR}}' => 'paypal',
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

            /* Handle received payments by subscriptions */
            if($data->event_type == 'PAYMENT.SALE.COMPLETED') {

                $response = \Unirest\Request::get($paypal_api_url . 'v1/billing/subscriptions/' . $data->resource->billing_agreement_id . '?fields=plan', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        error_log($response->body->name . ':' . $response->body->message);
                    }
                    echo $response->body->name . ':' . $response->body->message;
                    http_response_code(400); die();
                }

                /* Start getting the payment details */
                $payment_id = $data->resource->id;
                $payment_total = $data->resource->amount->total;
                $payment_currency = $data->resource->amount->currency;
                $payment_type = 'recurring';
                $payment_subscription_id = 'paypal###' . $data->resource->billing_agreement_id;

                /* Payment payer details */
                $payer_email = $response->body->subscriber->email_address;
                $payer_name = $response->body->subscriber->name->given_name . $response->body->subscriber->name->surname;
                $payer_id = $response->body->subscriber->payer_id;

                if(isset($response->body->custom_id)) {
                    /* Parse metadata */
                    $metadata = explode('&', $response->body->custom_id);
                    $user_id = (int) $metadata[0];
                    $plan_id = (int) $metadata[1];
                    $payment_frequency = $metadata[2];
                    $base_amount = $metadata[3];
                    $code = $metadata[4];
                    $discount_amount = $metadata[5] ? $metadata[5] : 0;
                    $taxes_ids = $metadata[6];
                } else {

                    /* Check for old subscriptions meta data */
                    $extra = explode('###', $response->body->plan->name);

                    if(isset($extra[0], $extra[1], $extra[2])) {
                        $user_id = (int) $extra[0];
                        $plan_id = (int) $extra[1];
                        $payment_frequency = $extra[2];
                        $code = $extra[3];
                        $discount_amount = 0;
                        $base_amount = 0;
                    } else {
                        $extra = explode('!!', $response->body->plan->name);

                        $user_id = (int) $extra[0];
                        $plan_id = (int) $extra[1];
                        $base_amount = $extra[2];
                        $payment_frequency = $extra[3];
                        $code = $extra[4];
                        $discount_amount = $extra[5] ? $extra[5] : 0;
                        $taxes_ids = $extra[6];
                    }
                }

                /* Get the plan details */
                $plan = db()->where('plan_id', $plan_id)->getOne('plans');

                /* Just make sure the plan is still existing */
                if(!$plan) {
                    http_response_code(400); die();
                }

                /* Make sure the transaction is not already existing */
                if(db()->where('payment_id', $payment_id)->where('processor', 'paypal')->has('payments')) {
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

                /* Add a log into the database */
                $payment_id = db()->insert('payments', [
                    'user_id' => $user_id,
                    'plan_id' => $plan_id,
                    'processor' => 'paypal',
                    'type' => $payment_type,
                    'frequency' => $payment_frequency,
                    'code' => $code,
                    'discount_amount' => $discount_amount,
                    'base_amount' => $base_amount,
                    'email' => $payer_email,
                    'payment_id' => $payment_id,
                    'subscription_id' => $payment_subscription_id,
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
                    'payment_subscription_id' => $payment_subscription_id
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
                            '{{PROCESSOR}}' => 'paypal',
                            '{{TOTAL_AMOUNT}}' => $payment_total,
                            '{{CURRENCY}}' => $payment_currency,
                        ],
                        language()->global->emails->admin_new_payment_notification->subject,
                        [
                            '{{PROCESSOR}}' => 'paypal',
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

        }

        die('');

    }

}
