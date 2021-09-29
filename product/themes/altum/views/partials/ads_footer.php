<?php
use Altum\Middlewares\Authentication;

if(
    !empty(settings()->ads->footer)
    && (
        !Authentication::check() ||
        (Authentication::check() && !$this->user->plan_settings->no_ads)
    )
    && !\Altum\Routing\Router::$controller_settings['no_ads']
): ?>
    <div class="container my-3"><?= settings()->ads->footer ?></div>
<?php endif ?>
