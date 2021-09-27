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
use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\PaymentGateways\Coinbase;
use Altum\Response;

class Pay extends Controller {
    public $plan_id;
    public $return_type;
    public $payment_processor;
    public $plan;
    public $plan_taxes;
    public $applied_taxes_ids = [];
    public $code;

    public function index() {

        Authentication::guard();

        if(!settings()->payment->is_enabled) {
            redirect();
        }

        $this->plan_id = isset($this->params[0]) ? $this->params[0] : null;
        $this->return_type = isset($_GET['return_type']) && in_array($_GET['return_type'], ['success', 'cancel']) ? $_GET['return_type'] : null;
        $this->payment_processor = isset($_GET['payment_processor']) && in_array($_GET['payment_processor'], ['paypal', 'stripe', 'offline_payment', 'coinbase']) ? $_GET['payment_processor'] : null;

        /* Make sure it is either the trial / free plan or normal plans */
        switch($this->plan_id) {

            case 'custom':
                redirect('plan');
                break;

            case 'free':
                $this->plan = settings()->plan_free;
                break;

            default:

                $this->plan_id = (int) $this->plan_id;

                /* Check if plan exists */
                $this->plan = (new \Altum\Models\Plan())->get_plan_by_id($this->plan_id);

                /* Check for potential taxes */
                $this->plan_taxes = (new \Altum\Models\Plan())->get_plan_taxes_by_taxes_ids($this->plan->taxes_ids);

                /* Filter them out */
                if($this->plan_taxes) {
                    foreach ($this->plan_taxes as $key => $value) {

                        /* Type */
                        if ($value->billing_type != $this->user->billing->type && $value->billing_type != 'both') {
                            unset($this->plan_taxes[$key]);
                        }

                        /* Countries */
                        if ($value->countries && !in_array($this->user->billing->country, $value->countries)) {
                            unset($this->plan_taxes[$key]);
                        }

                        if (isset($this->plan_taxes[$key])) {
                            $this->applied_taxes_ids[] = $value->tax_id;
                        }

                    }

                    $this->plan_taxes = array_values($this->plan_taxes);
                }

                break;
        }

        /* Make sure the plan is enabled */
        if(!$this->plan->status) {
            redirect('plan');
        }

        if(
            settings()->payment->taxes_and_billing_is_enabled
            && !in_array($this->plan_id, ['free'])
            && ($this->user->plan_trial_done || !$this->plan->trial_days)
            && (empty($this->user->billing->name) || empty($this->user->billing->address) || empty($this->user->billing->city) || empty($this->user->billing->county) || empty($this->user->billing->zip))
        ) {
            redirect('pay-billing/' . $this->plan_id);
        }

        /* More checks depending on the user plan and what it has been chosen */
        if($this->plan_id == 'free') {
            if($this->user->plan_id == 'free') {
                Alerts::add_info(language()->pay->free->free_already);
            } else {
                Alerts::add_info(language()->pay->free->other_plan_not_expired);
            }

            redirect('plan');
        }

        /* Form submission processing */
        /* Make sure that this only runs on user click submit post and not on callbacks / webhooks */
        if(!empty($_POST) && !$this->return_type) {

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');
            //ALTUMCODE:DEMO if(DEMO) redirect('pay/' . $this->plan_id);

            /* Check for any errors */
            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            switch($this->plan_id) {
                case 'free':
                    redirect('pay/' . $this->plan_id);
                    break;

                default:

                    /* Check if we should start the trial or not */
                    if($this->plan->trial_days && !$this->user->plan_trial_done) {
                        // :)
                    } else {

                        $_POST['payment_frequency'] = Database::clean_string($_POST['payment_frequency']);
                        $_POST['payment_processor'] = Database::clean_string($_POST['payment_processor']);
                        $_POST['payment_type'] = Database::clean_string($_POST['payment_type']);

                        /* Make sure the chosen option comply */
                        if(!in_array($_POST['payment_frequency'], ['monthly', 'annual', 'lifetime'])) {
                            redirect('pay/' . $this->plan_id);
                        }

                        if(!in_array($_POST['payment_processor'], ['paypal', 'stripe', 'offline_payment', 'coinbase'])) {
                            redirect('pay/' . $this->plan_id);
                        } else {

                            /* Make sure the payment processor is active */
                            if(!settings()->{$_POST['payment_processor']}->is_enabled) {
                                redirect('pay/' . $this->plan_id);
                            }

                        }

                        if(!in_array($_POST['payment_type'], ['one_time', 'recurring'])) {
                            redirect('pay/' . $this->plan_id);
                        }

                        /* Lifetime */
                        if($_POST['payment_frequency'] == 'lifetime') {
                            $_POST['payment_type'] = 'one_time';
                        }

                        /* Offline / Coinbase payment */
                        if(in_array($_POST['payment_processor'], ['offline_payment', 'coinbase'])) {
                            $_POST['payment_type'] = 'one_time';
                        }

                    }

                    break;
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Check if we should start the trial or not */
                if($this->plan->trial_days && !$this->user->plan_trial_done) {

                    /* Determine the expiration date of the plan */
                    $plan_expiration_date = (new \DateTime())->modify('+' . $this->plan->trial_days . ' days')->format('Y-m-d H:i:s');
                    $plan_settings = json_encode($this->plan->settings);

                    /* Database query */
                    db()->where('user_id', $this->user->user_id)->update('users', [
                        'plan_id' => $this->plan_id,
                        'plan_settings' => $plan_settings,
                        'plan_expiration_date' => $plan_expiration_date,
                        'plan_trial_done' => 1,
                    ]);

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

                    /* Success message and redirect */
                    $this->redirect_pay_thank_you();
                } else {

                    /* Check for code usage */
                    $this->code = false;

                    if(settings()->payment->codes_is_enabled && isset($_POST['code'])) {

                        $_POST['code'] = Database::clean_string($_POST['code']);

                        $this->code = database()->query("SELECT `code_id`, `code`, `discount` FROM `codes` WHERE (`plan_id` IS NULL OR `plan_id` = '{$this->plan_id}') AND `code` = '{$_POST['code']}' AND `redeemed` < `quantity` AND `type` = 'discount'")->fetch_object();

                        if($this->code && db()->where('user_id', $this->user->user_id)->where('code_id', $this->code->code_id)->has('redeemed_codes')) {
                            redirect('pay/' . $this->plan_id);
                        }
                    }

                    switch($_POST['payment_processor']) {
                        case 'paypal':
                            $this->paypal_create();
                            break;

                        case 'stripe':
                            $stripe_session = $this->stripe_create();
                            break;

                        case 'offline_payment':
                            $this->offline_payment_process();
                            break;

                        case 'coinbase':
                            $this->coinbase_create();
                            break;
                    }

                }
            }
        }

        /* Include the detection of callbacks processing */
        $this->payment_return_process();

        /* Prepare the View */
        $data = [
            'plan_id'           => $this->plan_id,
            'plan'              => $this->plan,
            'plan_taxes'        => $this->plan_taxes,
            'stripe_session'    => $stripe_session ?? false
        ];

        $view = new \Altum\Views\View('pay/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function paypal_create() {

        /* Payment details */
        $price = $base_amount = (float) $this->plan->{$_POST['payment_frequency'] . '_price'};
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {
            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;
        }

        /* Taxes */
        $price = $this->calculate_price_with_taxes($price);

        /* Make sure the price is right depending on the currency */
        $price = in_array(settings()->payment->currency, ['JPY', 'TWD', 'HUF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '');

        try {
            $paypal_api_url = \Altum\PaymentGateways\Paypal::get_api_url();
            $headers = \Altum\PaymentGateways\Paypal::get_headers();
        } catch (\Exception $exception) {
            Alerts::add_error($exception->getMessage());
            redirect('pay/' . $this->plan_id);
        }

        $custom_id = $this->user->user_id . '&' . $this->plan_id . '&' . $_POST['payment_frequency'] . '&' . $base_amount . '&' . $code . '&' . $discount_amount . '&' . json_encode($this->applied_taxes_ids);

        switch($_POST['payment_type']) {
            case 'one_time':

                /* Create an order */
                $response = \Unirest\Request::post($paypal_api_url . 'v2/checkout/orders', $headers, \Unirest\Request\Body::json([
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => settings()->payment->currency,
                            'value' => $price,
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => settings()->payment->currency,
                                    'value' => $price
                                ]
                            ]
                        ],
                        'description' => '',
                        'custom_id' => $custom_id,
                        'items' => [[
                            'name' => settings()->payment->brand_name . ' - ' . $this->plan->name,
                            'description' => '',
                            'quantity' => 1,
                            'unit_amount' => [
                                'currency_code' => settings()->payment->currency,
                                'value' => $price
                            ]
                        ]]
                    ]],
                    'application_context' => [
                        'brand_name' => settings()->payment->brand_name,
                        'landing_page' => 'NO_PREFERENCE',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                        'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount))
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        Alerts::add_error($response->body->name . ':' . $response->body->message);
                    } else {
                        Alerts::add_error(language()->pay->error_message->failed_payment);
                    }
                    redirect('pay/' . $this->plan_id);
                }

                $paypal_payment_url = $response->body->links[1]->href;

                header('Location: ' . $paypal_payment_url); die();

                break;

            case 'recurring':

                /* Generate the plan id with the proper parameters */
                $paypal_plan_id = $this->plan_id . '_' . $_POST['payment_frequency'] . '_' . $price . '_' . settings()->payment->currency;

                /* Product */
                $response = \Unirest\Request::get($paypal_api_url . 'v1/catalogs/products/' . $paypal_plan_id, $headers);

                /* Check against errors */
                if($response->code == 404) {
                    /* Create the product if not existing */
                    $response = \Unirest\Request::post($paypal_api_url . 'v1/catalogs/products', $headers, \Unirest\Request\Body::json([
                        'id' => $paypal_plan_id,
                        'name' => settings()->payment->brand_name . ' - ' . $this->plan->name,
                        'type' => 'DIGITAL',
                    ]));

                    /* Check against errors */
                    if($response->code >= 400) {
                        if(DEBUG) {
                            Alerts::add_error($response->body->name . ':' . $response->body->message);
                        } else {
                            Alerts::add_error(language()->pay->error_message->failed_payment);
                        }
                        redirect('pay/' . $this->plan_id);
                    }
                }

                /* Create a new plan */
                $response = \Unirest\Request::post($paypal_api_url . 'v1/billing/plans', $headers, \Unirest\Request\Body::json([
                    'product_id' => $paypal_plan_id,
                    'name' => settings()->payment->brand_name . ' - ' . $this->plan->name . ' - ' . $_POST['payment_frequency'],
                    'description' => $_POST['payment_frequency'],
                    'status' => 'ACTIVE',
                    'billing_cycles' => [[
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'currency_code' => settings()->payment->currency,
                                'value' => $price
                            ]
                        ],
                        'frequency' => [
                            'interval_unit' => $_POST['payment_frequency'] == 'monthly' ? 'MONTH' : 'YEAR',
                            'interval_count' => 1
                        ],
                        'tenure_type' => 'REGULAR',
                        'sequence' => 1,
                        'total_cycles' => $_POST['payment_frequency'] == 'monthly' ? 60 : 5,
                    ]],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee' => [
                            'currency_code' => settings()->payment->currency,
                            'value' => $price
                        ],
                        'setup_fee_failure_action' => 'CANCEL',
                        'payment_failure_threshold' => 0
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        Alerts::add_error($response->body->name . ':' . $response->body->message);
                    } else {
                        Alerts::add_error(language()->pay->error_message->failed_payment);
                    }
                    redirect('pay/' . $this->plan_id);
                }

                /* Create a new subscription */
                $response = \Unirest\Request::post($paypal_api_url . 'v1/billing/subscriptions', $headers, \Unirest\Request\Body::json([
                    'plan_id' => $response->body->id,
//                    'start_time' => (new \DateTime())->modify('+30 seconds')->format(DATE_ISO8601),
                    'start_time' => (new \DateTime())->modify($_POST['payment_frequency'] == 'monthly' ? '+30 days' : '+1 year')->format(DATE_ISO8601),
                    'quantity' => 1,
                    'custom_id' => $custom_id,
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                    ],
                    'application_context' => [
                        'brand_name' => settings()->payment->brand_name,
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                        'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount))
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        Alerts::add_error($response->body->name . ':' . $response->body->message);
                    } else {
                        Alerts::add_error(language()->pay->error_message->failed_payment);
                    }
                    redirect('pay/' . $this->plan_id);
                }

                $paypal_payment_url = $response->body->links[0]->href;

                header('Location: ' . $paypal_payment_url); die();

                break;
        }


    }

    private function stripe_create() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey(settings()->stripe->secret_key);

        /* Payment details */
        $product = $this->plan->name;
        $price = $base_amount = $this->plan->{$_POST['payment_frequency'] . '_price'};
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {

            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;

        }

        /* Taxes */
        $price = $this->calculate_price_with_taxes($price);

        /* Final price */
        $stripe_formatted_price = in_array(settings()->payment->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '') * 100;

        $price = number_format($price, 2, '.', '');

        switch($_POST['payment_type']) {
            case 'one_time':

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'name' => settings()->payment->brand_name . ' - ' . $product,
                        'description' => $_POST['payment_frequency'],
                        'amount' => $stripe_formatted_price,
                        'currency' => settings()->payment->currency,
                        'quantity' => 1,
                    ]],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'success_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                    'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)),
                ]);

                break;

            case 'recurring':

                /* Try to get the product related to the plan */
                try {
                    $stripe_product = \Stripe\Product::retrieve($this->plan_id);
                } catch (\Exception $exception) {
                    /* The product probably does not exist */
                }

                if(!isset($stripe_product)) {
                    /* Create the product if not already created */
                    $stripe_product = \Stripe\Product::create([
                        'id'    => $this->plan_id,
                        'name'  => settings()->payment->brand_name . ' - ' . $product,
                    ]);
                }

                /* Generate the plan id with the proper parameters */
                $stripe_plan_id = $this->plan_id . '_' . $_POST['payment_frequency'] . '_' . $stripe_formatted_price . '_' . settings()->payment->currency;

                /* Check if we already have a payment plan created and try to get it */
                try {
                    $stripe_plan = \Stripe\Plan::retrieve($stripe_plan_id);
                } catch (\Exception $exception) {
                    /* The plan probably does not exist */
                }

                /* Create the plan if it doesnt exist already */
                if(!isset($stripe_plan)) {
                    try {
                        $stripe_plan = \Stripe\Plan::create([
                            'amount' => $stripe_formatted_price,
                            'interval' => $_POST['payment_frequency'] == 'monthly' ? 'month' : 'year',
                            'product' => $stripe_product->id,
                            'currency' => settings()->payment->currency,
                            'id' => $stripe_plan_id
                        ]);
                    } catch (\Exception $exception) {
                        Alerts::add_error($exception->getMessage());
                        redirect('pay/' . $this->plan_id);
                    }
                }

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'subscription_data' => [
                        'items' => [
                            ['plan' => $stripe_plan->id]
                        ],
                        'metadata' => [
                            'user_id' => $this->user->user_id,
                            'plan_id' => $this->plan_id,
                            'payment_frequency' => $_POST['payment_frequency'],
                            'base_amount' => $base_amount,
                            'code' => $code,
                            'discount_amount' => $discount_amount,
                            'taxes_ids' => json_encode($this->applied_taxes_ids)
                        ],
                    ],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'success_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                    'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)),
                ]);

                break;
        }

        return $stripe_session;

    }

    private function coinbase_create() {

        /* Payment details */
        $product = $this->plan->name;
        $price = $base_amount = $this->plan->{$_POST['payment_frequency'] . '_price'};
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {
            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;
        }

        /* Taxes */
        $price = $this->calculate_price_with_taxes($price);

        /* Final price */
        $price = number_format($price, 2, '.', '');

        $response = \Unirest\Request::post(
            Coinbase::get_api_url() . 'charges',
            Coinbase::get_headers(),
            \Unirest\Request\Body::json([
                'name' => settings()->payment->brand_name . ' - ' . $this->plan->name,
                'description' => '',
                'local_price' => [
                    'amount' => $price,
                    'currency' => settings()->payment->currency
                ],
                'pricing_type' => 'fixed_price',
                'metadata' => [
                    'user_id' => $this->user->user_id,
                    'plan_id' => $this->plan_id,
                    'payment_frequency' => $_POST['payment_frequency'],
                    'base_amount' => $base_amount,
                    'code' => $code,
                    'discount_amount' => $discount_amount,
                    'taxes_ids' => json_encode($this->applied_taxes_ids)
                ],
                'redirect_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)),
            ])
        );

        /* Check against errors */
        if($response->code >= 400) {
            if(DEBUG) {
                Alerts::add_error($response->body->error->type . ':' . $response->body->error->message);
            } else {
                Alerts::add_error(language()->pay->error_message->failed_payment);
            }
            redirect('pay/' . $this->plan_id);
        }

        header('Location: ' . $response->body->data->hosted_url); die();
    }

    private function payment_return_process() {

        /* Return confirmation processing if successfully */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Return confirmation processing if failed */
        if($this->return_type && $this->payment_processor && $this->return_type == 'cancel') {
            Alerts::add_error(language()->pay->error_message->canceled_payment);
            redirect('pay/' . $this->plan_id);
        }

    }

    private function offline_payment_process() {

        /* Return confirmation processing if successfully */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor == 'offline_payment') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Payment details */
        $price = $base_amount = $this->plan->{$_POST['payment_frequency'] . '_price'};
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {

            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;

        }

        /* Taxes */
        $price = number_format($this->calculate_price_with_taxes($price), 2, '.', '');

        /* Other vars */
        $payment_id = md5($this->user->user_id . $this->plan_id . $_POST['payment_type'] . $_POST['payment_frequency'] . $this->user->email . Date::$date);
        $file_allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $offline_payment_proof = (!empty($_FILES['offline_payment_proof']['name']));

        /* Error checks */
        if(!$offline_payment_proof) {
            Alerts::add_error(language()->pay->error_message->offline_payment_proof_missing);
            redirect('pay/' . $this->plan_id);
        }

        $offline_payment_proof_file_name = $_FILES['offline_payment_proof']['name'];
        $offline_payment_proof_file_extension = explode('.', $offline_payment_proof_file_name);
        $offline_payment_proof_file_extension = mb_strtolower(end($offline_payment_proof_file_extension));
        $offline_payment_proof_file_temp = $_FILES['offline_payment_proof']['tmp_name'];

        if(!in_array($offline_payment_proof_file_extension, $file_allowed_extensions)) {
            Alerts::add_error(language()->global->error_message->invalid_file_type);
            redirect('pay/' . $this->plan_id);
        }

        if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
            if(!is_writable(UPLOADS_PATH . 'offline_payment_proofs/')) {
                Alerts::add_error(sprintf(language()->global->error_message->directory_not_writable, UPLOADS_PATH . 'offline_payment_proofs/'));
                redirect('pay/' . $this->plan_id);
            }
        }

        /* Generate new name for offline_payment_proof */
        $offline_payment_proof_new_name = $payment_id . '.' . $offline_payment_proof_file_extension;

        /* Offload uploading */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Upload image */
                $result = $s3->putObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/offline_payment_proofs/' . $offline_payment_proof_new_name,
                    'ContentType' => mime_content_type($offline_payment_proof_file_temp),
                    'SourceFile' => $offline_payment_proof_file_temp,
                    'ACL' => 'public-read'
                ]);
            } catch (\Exception $exception) {
                Alerts::add_error($exception->getMessage());
            }
        }

        /* Local uploading */
        else {
            /* Upload the original */
            move_uploaded_file($offline_payment_proof_file_temp, UPLOADS_PATH . 'offline_payment_proofs/' . $offline_payment_proof_new_name);
        }

        /* Add a log into the database */
        db()->insert('payments', [
            'user_id' => $this->user->user_id,
            'plan_id' => $this->plan_id,
            'processor' => 'offline_payment',
            'type' => $_POST['payment_type'],
            'frequency' => $_POST['payment_frequency'],
            'code' => $code,
            'discount_amount' => $discount_amount,
            'base_amount' => $base_amount,
            'email' => $this->user->email,
            'payment_id' => $payment_id,
            'subscription_id' => '',
            'payer_id' => $this->user->user_id,
            'name' => $this->user->name,
            'billing' => settings()->payment->taxes_and_billing_is_enabled && $this->user->billing ? json_encode($this->user->billing) : null,
            'taxes_ids' => !empty($this->applied_taxes_ids) ? json_encode($this->applied_taxes_ids) : null,
            'total_amount' => $price,
            'currency' => settings()->payment->currency,
            'payment_proof' => $offline_payment_proof_new_name,
            'status' => 0,
            'date' => Date::$date
        ]);

        /* Send notification to admin if needed */
        if(settings()->email_notifications->new_payment && !empty(settings()->email_notifications->emails)) {

            $email_template = get_email_template(
                [
                    '{{PROCESSOR}}' => 'offline_payment',
                    '{{TOTAL_AMOUNT}}' => $price,
                    '{{CURRENCY}}' => settings()->payment->currency,
                ],
                language()->global->emails->admin_new_payment_notification->subject,
                [
                    '{{PROCESSOR}}' => 'offline_payment',
                    '{{TOTAL_AMOUNT}}' => $price,
                    '{{CURRENCY}}' => settings()->payment->currency,
                    '{{NAME}}' => $this->user->name,
                    '{{EMAIL}}' => $this->user->email,
                ],
                language()->global->emails->admin_new_payment_notification->body
            );

            send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

        }

        redirect('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount));

    }

    /* Ajax to check if discount codes are available */
    public function code() {
        Authentication::guard();

        $_POST = json_decode(file_get_contents('php://input'), true);

        if(!Csrf::check('global_token')) {
            die();
        }

        if(!settings()->payment->is_enabled || !settings()->payment->codes_is_enabled) {
            die();
        }

        if(empty($_POST)) {
            die();
        }

        $_POST['plan_id'] = !$_POST['plan_id'] ? null : (int) $_POST['plan_id'];
        $_POST['code'] = Database::clean_string($_POST['code']);

        /* Make sure the discount code exists */
        $code = database()->query("SELECT * FROM `codes` WHERE (`plan_id` IS NULL OR `plan_id` = '{$_POST['plan_id']}') AND `code` = '{$_POST['code']}' AND `redeemed` < `quantity` AND `type` = 'discount'")->fetch_object();

        if(!$code) {
            Response::json(language()->pay->error_message->code_invalid, 'error');
        }

        if(db()->where('user_id', $this->user->user_id)->where('code_id', $code->code_id)->has('redeemed_codes')) {
            Response::json(language()->pay->error_message->code_used, 'error');
        }


        Response::json(sprintf(language()->pay->success_message->code, '<strong>' . $code->discount . '%</strong>'), 'success', ['discount' => $code->discount]);
    }

    /* Generate the generic return url parameters */
    private function return_url_parameters($return_type, $base_amount, $total_amount, $code, $discount_amount) {
        return
            '&return_type=' . $return_type
            . '&payment_processor=' . $_POST['payment_processor']
            . '&payment_frequency=' . $_POST['payment_frequency']
            . '&payment_type=' . $_POST['payment_type']
            . '&code=' . $code
            . '&discount_amount=' . $discount_amount
            . '&base_amount=' . $base_amount
            . '&total_amount=' . $total_amount;
    }

    /* Simple url generator to return the thank you page */
    private function redirect_pay_thank_you() {
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return $key != 'altum';
        }, ARRAY_FILTER_USE_KEY);

        $thank_you_url_parameters = '&plan_id=' . $this->plan_id;
        $thank_you_url_parameters .= '&user_id=' . $this->user->user_id;
        if($this->plan->trial_days && !$this->user->plan_trial_done) {
            $thank_you_url_parameters .= '&trial_days=' . $this->plan->trial_days;
        }

        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $thank_you_url_parameters .= '&unique_transaction_identifier=' . md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        redirect('pay-thank-you?' . $thank_you_url_parameters);
    }

    private function calculate_price_with_taxes($discounted_price) {

        $price = $discounted_price;

        if($this->plan_taxes) {

            /* Check for the inclusives */
            $inclusive_taxes_total_percentage = 0;

            foreach($this->plan_taxes as $row) {
                if($row->type == 'exclusive') continue;

                $inclusive_taxes_total_percentage += $row->value;
            }

            $total_inclusive_tax = $price - ($price / (1 + $inclusive_taxes_total_percentage / 100));

            $price_without_inclusive_taxes = $price - $total_inclusive_tax;

            /* Check for the exclusives */
            $exclusive_taxes_array = [];

            foreach($this->plan_taxes as $row) {

                if($row->type == 'inclusive') {
                    continue;
                }

                $exclusive_tax = $row->value_type == 'percentage' ? $price_without_inclusive_taxes * ($row->value / 100) : $row->value;

                $exclusive_taxes_array[] = $exclusive_tax;

            }

            $exclusive_taxes = array_sum($exclusive_taxes_array);

            /* Price with all the taxes */
            $price_with_taxes = $price + $exclusive_taxes;

            $price = $price_with_taxes;
        }

        return $price;

    }
}
