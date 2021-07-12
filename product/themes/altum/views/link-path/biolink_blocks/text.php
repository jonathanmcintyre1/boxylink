<?php defined('ALTUMCODE') || die() ?>

<div data-link-id="<?= $data->link->link_id ?>" class="col-12 my-2">
    <h2 class="h4 text-break" style="color: <?= $data->link->settings->title_text_color ?>"><?= $data->link->settings->title ?></h2>
    <p class="text-break" style="color: <?= $data->link->settings->description_text_color ?>"><?= nl2br($data->link->settings->description) ?></p>
</div>

