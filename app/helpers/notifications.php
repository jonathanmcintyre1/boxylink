<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

function display_notifications($dismissable = true) {
    $types = ['error', 'success', 'info'];

    foreach($types as $type) {
        if(isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
            if(!is_array($_SESSION[$type])) $_SESSION[$type] = [$_SESSION[$type]];

            foreach($_SESSION[$type] as $message) {

                echo output_alert($type, $message, true, $dismissable);

            }

            unset($_SESSION[$type]);
        }
    }

}

function output_alert($type, $message, $icon = true, $dismissable = true) {

    switch($type) {
        case 'error':
            $alert_type = 'danger';
            $icon = $icon ? '<i class="fa fa-fw fa-times-circle text-' . $alert_type . ' mr-1"></i>' : null;
            break;

        case 'success':
            $alert_type = 'success';
            $icon = $icon ? '<i class="fa fa-fw fa-check-circle text-' . $alert_type . ' mr-1"></i>' : null;
            break;

        case 'info':
            $alert_type = 'info';
            $icon = $icon ? '<i class="fa fa-fw fa-info-circle text-' . $alert_type . ' mr-1"></i>' : null;
            break;
    }

    $dismiss_button = $dismissable ? '<button type="button" class="close" data-dismiss="alert"><i class="fa fa-fw fa-sm fa-times text-' . $alert_type . '"></i></button>' : null;

    return '
        <div class="alert alert-' . $alert_type . ' altum-animate altum-animate-fill-both altum-animate-fade-in">
            ' . $icon . '
            ' . $dismiss_button . '
            ' . $message . '
        </div>
    ';
}
