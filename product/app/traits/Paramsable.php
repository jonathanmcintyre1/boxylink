<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Traits;

trait Paramsable {

    /* Function used by the base model, controller and view */
    public function add_params(Array $params = []) {

        /* Make the params available to the Controller */
        foreach($params as $key => $value) {
            $this->{$key} = $value;
        }

    }

}
