<?php defined('ALTUMCODE') || die() ?>

<div data-link-id="<?= $data->link->link_id ?>" class="col-12 my-2">
    <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-link-url="<?= $data->link->url ?>" class="btn btn-block btn-primary link-btn <?= $data->link->design->link_class ?>" style="<?= $data->link->design->link_style ?>">
        <div class="link-btn-image-wrapper <?= $data->link->design->border_class ?>" <?= $data->link->settings->image ? null : 'style="display: none;"' ?>>
            <img src="<?= SITE_URL . UPLOADS_URL_PATH . 'block_thumbnail_images/' . $data->link->settings->image ?>" class="link-btn-image" loading="lazy" />
        </div>

        <?php if($data->link->settings->icon): ?>
            <i class="<?= $data->link->settings->icon ?> mr-1"></i>
        <?php endif ?>

        <?= $data->link->settings->name ?>
    </a>
</div>


