<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Meta;

class Index extends Controller {

    public function index() {

        /* Custom index redirect if set */
        if(!empty(settings()->index_url)) {
            header('Location: ' . settings()->index_url);
            die();
        }

        /* Plans View */
        $data = [];

        $view = new \Altum\Views\View('partials/plans', (array) $this);

        $this->add_view_content('plans', $view->run($data));

        /* Opengraph image */
        if(settings()->opengraph) {
            Meta::set_social_url(SITE_URL);
            Meta::set_social_description(language()->index->meta_description);
            Meta::set_social_image(UPLOADS_FULL_URL . 'opengraph/' .settings()->opengraph);
        }

        /* Main View */
        $data = [];

        $view = new \Altum\Views\View('index/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
