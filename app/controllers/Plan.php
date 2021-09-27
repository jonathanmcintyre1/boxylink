<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Middlewares\Authentication;

class Plan extends Controller {

    public function index() {

        if(!settings()->payment->is_enabled) {
            redirect();
        }

        $type = isset($this->params[0]) && in_array($this->params[0], ['renew', 'upgrade', 'new']) ? $this->params[0] : 'new';

        /* If the user is not logged in when trying to upgrade or renew, make sure to redirect them */
        if(in_array($type, ['renew', 'upgrade']) && !Authentication::check()) {
            redirect('plan/new');
        }

        /* Plans View */
        $data = [];

        $view = new \Altum\Views\View('partials/plans', (array) $this);

        $this->add_view_content('plans', $view->run($data));


        /* Prepare the View */
        $data = [
            'type' => $type
        ];

        $view = new \Altum\Views\View('plan/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
