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
use Altum\Middlewares\Csrf;

class AdminPlugins extends Controller {

    public function index() {

        /* Main View */
        $view = new \Altum\Views\View('admin/plugins/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }


    public function install() {

        $plugin_id = isset($this->params[0]) ? Database::clean_string($this->params[0]) : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!\Altum\Plugin::is_uninstalled($plugin_id)) {
            redirect('admin/plugins');
        }

        if(!is_writable(\Altum\Plugin::get($plugin_id)->path . 'config.json')) {
            Alerts::add_error(sprintf(language()->global->error_message->file_not_writable, \Altum\Plugin::get($plugin_id)->path . 'config.json'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Load all the related plugin files */
            require \Altum\Plugin::get($plugin_id)->path . 'init.php';

            $class_name = preg_replace('/[^A-Za-z0-9]/', '', $plugin_id);
            $class = '\Altum\Plugin\\' . $class_name;
            $class::install();

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->admin_plugins->install_message, '<strong>' . \Altum\Plugin::get($plugin_id)->name . '</strong>'));

        }

        redirect('admin/plugins');
    }

    public function uninstall() {

        $plugin_id = isset($this->params[0]) ? Database::clean_string($this->params[0]) : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!\Altum\Plugin::is_installed($plugin_id)) {
            redirect('admin/plugins');
        }

        if(!is_writable(\Altum\Plugin::get($plugin_id)->path . 'config.json')) {
            Alerts::add_error(sprintf(language()->global->error_message->file_not_writable, \Altum\Plugin::get($plugin_id)->path . 'config.json'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Load all the related plugin files */
            require \Altum\Plugin::get($plugin_id)->path . 'init.php';

            $class_name = preg_replace('/[^A-Za-z0-9]/', '', $plugin_id);
            $class = '\Altum\Plugin\\' . $class_name;
            $class::uninstall();

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->admin_plugins->uninstall_message, '<strong>' . \Altum\Plugin::get($plugin_id)->name . '</strong>'));

        }

        redirect('admin/plugins');
    }

    public function activate() {

        $plugin_id = isset($this->params[0]) ? Database::clean_string($this->params[0]) : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!\Altum\Plugin::is_installed($plugin_id)) {
            redirect('admin/plugins');
        }

        if(!is_writable(\Altum\Plugin::get($plugin_id)->path . 'config.json')) {
            Alerts::add_error(sprintf(language()->global->error_message->file_not_writable, \Altum\Plugin::get($plugin_id)->path . 'config.json'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Load all the related plugin files */
            require \Altum\Plugin::get($plugin_id)->path . 'init.php';

            $class_name = preg_replace('/[^A-Za-z0-9]/', '', $plugin_id);
            $class = '\Altum\Plugin\\' . $class_name;
            $class::activate();

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->admin_plugins->activate_message, '<strong>' . \Altum\Plugin::get($plugin_id)->name . '</strong>'));

        }

        redirect('admin/plugins');
    }

    public function disable() {

        $plugin_id = isset($this->params[0]) ? Database::clean_string($this->params[0]) : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!\Altum\Plugin::is_active($plugin_id)) {
            redirect('admin/plugins');
        }

        if(!is_writable(\Altum\Plugin::get($plugin_id)->path . 'config.json')) {
            Alerts::add_error(sprintf(language()->global->error_message->file_not_writable, \Altum\Plugin::get($plugin_id)->path . 'config.json'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Load all the related plugin files */
            require \Altum\Plugin::get($plugin_id)->path . 'init.php';

            $class_name = preg_replace('/[^A-Za-z0-9]/', '', $plugin_id);
            $class = '\Altum\Plugin\\' . $class_name;
            $class::disable();

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->admin_plugins->disable_message, '<strong>' . \Altum\Plugin::get($plugin_id)->name . '</strong>'));

        }

        redirect('admin/plugins');
    }

}
