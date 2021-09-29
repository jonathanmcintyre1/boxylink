<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

class ApiDocumentation extends Controller {

    public function index() {

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }

}

