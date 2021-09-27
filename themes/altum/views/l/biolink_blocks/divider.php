<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 mt-<?= $data->link->settings->margin_top ?> mb-<?= $data->link->settings->margin_bottom ?>">
    <div class="d-flex justify-content-center align-items-center">
        <hr class="w-100" style="border-color: <?= $data->link->settings->background_color ?>;" />

        <span class="mx-4">
            <i class="<?= $data->link->settings->icon ?> fa-fw" style="color: <?= $data->link->settings->background_color ?>;"></i>
        </span>

        <hr class="w-100" style="border-color: <?= $data->link->settings->background_color ?>;" />
    </div>
</div>
