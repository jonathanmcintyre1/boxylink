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
use Altum\Middlewares\Csrf;
use Altum\Models\Model;
use Altum\Models\User;

class AdminPlans extends Controller {

    public function index() {

        $plans = db()->orderBy('`order`', 'ASC')->get('plans');

        /* Main View */
        $data = [
            'plans' => $plans
        ];

        $view = new \Altum\Views\View('admin/plans/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        $plan_id = isset($this->params[0]) ? $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$plan = db()->where('plan_id', $plan_id)->getOne('plans', ['plan_id', 'name'])) {
            redirect('admin/plans');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Get all the users with this plan that have subscriptions and cancel them */
            $result = database()->query("SELECT `user_id`, `payment_subscription_id` FROM `users` WHERE `plan_id` = {$plan_id} AND `payment_subscription_id` <> ''");

            while($row = $result->fetch_object()) {
                try {
                    (new User())->cancel_subscription($row->user_id);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getCode() . ':' . $exception->getMessage());
                    redirect('admin/plans');
                }

                /* Change the user plan to custom and leave their current features they paid for on */
                db()->where('user_id', $row->user_id)->update('users', ['plan_id' => 'custom']);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $row->user_id);
            }

            /* Delete the plan */
            db()->where('plan_id', $plan_id)->delete('plans');

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $plan->name . '</strong>'));

        }

        redirect('admin/plans');
    }

}
