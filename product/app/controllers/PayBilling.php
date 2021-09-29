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
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;

class PayBilling extends Controller {

    public function index() {

        Authentication::guard();

        $plan_id = isset($this->params[0]) ? $this->params[0] : null;

        if(!settings()->payment->is_enabled) {
            redirect();
        }

        if(!settings()->payment->taxes_and_billing_is_enabled) {
            redirect('pay/' . $plan_id);
        }

        if(in_array($plan_id, ['free', 'custom'])) {
            redirect('pay/' . $plan_id);
        }

        $plan_id = (int) $plan_id;

        /* Check if plan exists */
        $plan = (new \Altum\Models\Plan())->get_plan_by_id($plan_id);

        /* Make sure the plan is enabled */
        if(!$plan->status) {
            redirect('plan');
        }

        if(!empty($_POST)) {
            $_POST['billing_type'] = in_array($_POST['billing_type'], ['personal', 'business']) ? Database::clean_string($_POST['billing_type']) : 'personal';
            $_POST['billing_name'] = trim(Database::clean_string($_POST['billing_name']));
            $_POST['billing_address'] = trim(Database::clean_string($_POST['billing_address']));
            $_POST['billing_city'] = trim(Database::clean_string($_POST['billing_city']));
            $_POST['billing_county'] = trim(Database::clean_string($_POST['billing_county']));
            $_POST['billing_zip'] = trim(Database::clean_string($_POST['billing_zip']));
            $_POST['billing_country'] = array_key_exists($_POST['billing_country'], get_countries_array()) ? Database::clean_string($_POST['billing_country']) : 'US';
            $_POST['billing_phone'] = trim(Database::clean_string($_POST['billing_phone']));
            $_POST['billing_tax_id'] = $_POST['billing_type'] == 'business' ? trim(Database::clean_string($_POST['billing_tax_id'])) : '';
            $_POST['billing'] = json_encode([
                'type' => $_POST['billing_type'],
                'name' => $_POST['billing_name'],
                'address' => $_POST['billing_address'],
                'city' => $_POST['billing_city'],
                'county' => $_POST['billing_county'],
                'zip' => $_POST['billing_zip'],
                'country' => $_POST['billing_country'],
                'phone' => $_POST['billing_phone'],
                'tax_id' => $_POST['billing_tax_id'],
            ]);

            $required_fields = ['billing_name', 'billing_address', 'billing_city', 'billing_county', 'billing_zip'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, language()->global->error_message->empty_field);
                }
            }

            /* Check for any errors */
            if(!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('user_id', $this->user->user_id)->update('users', ['billing' => $_POST['billing']]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

                /* Redirect to the checkout page */
                redirect('pay/' . $plan_id);

            }
        }

        /* Prepare the View */
        $data = [
            'plan_id' > $plan_id,
            'plan' => $plan,
        ];

        $view = new \Altum\Views\View('pay-billing/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
