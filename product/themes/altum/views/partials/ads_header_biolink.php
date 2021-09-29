<?php
use Altum\Middlewares\Authentication;

if(
    !empty(settings()->ads->header_biolink)
    && !$data->user->plan_settings->no_ads
): ?>
    <div class="container my-3"><?= settings()->ads->header_biolink ?></div>
<?php endif ?>
