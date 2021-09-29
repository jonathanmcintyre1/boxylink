<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Views;

use Altum\Traits\Paramsable;

class View {
    use Paramsable;

    public $view;
    public $view_path;

    public function __construct($view, Array $params = []) {

        $this->view = $view;
        $this->view_path = THEME_PATH . 'views/' . $view . '.php';

        $this->add_params($params);

    }

    public function run($data = []) {

        $data = (object) $data;

        ob_start();

        require $this->view_path;

        return ob_get_clean();
    }

}
