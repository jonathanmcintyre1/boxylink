<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <p class="text-break m-0" style="color: <?= $data->link->settings->text_color ?>">
        <?= nl2br($data->link->settings->text) ?>
    </p>
</div>

