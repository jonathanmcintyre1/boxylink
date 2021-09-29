<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="d-flex flex-column align-items-center">
        <img src="<?= $data->link->settings->image ? UPLOADS_FULL_URL . 'avatars/' . $data->link->settings->image : null ?>" alt="<?= language()->link->biolink->image_alt ?>" class="link-image <?= 'link-avatar-' . $data->link->settings->border_radius ?>" style="width: <?= $data->link->settings->size ?>px; height: <?= $data->link->settings->size ?>px; border-width: <?= $data->link->settings->border_width ?>px; border-color: <?= $data->link->settings->border_color ?>; border-style: <?= $data->link->settings->border_style ?>;" />
    </div>
</div>
