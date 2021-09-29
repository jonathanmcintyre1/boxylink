<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-6 my-2">
    <?php if($data->link->location_url): ?>
    <a href="<?= $data->link->location_url . $data->link->utm_query ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" target="_blank">
    <?php endif ?>

        <div class="link-grid-image-wrapper" style="background-image: url('<?= mb_substr($data->link->settings->image, 0, 4) === "http" ? $data->link->settings->image : UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->image ?>')">

            <?php if($data->link->settings->name): ?>
                <div class="link-grid-image-overlay">
                    <span class="link-grid-image-overlay-text text-truncate"><?= $data->link->settings->name ?></span>
                </div>
            <?php endif ?>

        </div>

    <?php if($data->link->location_url): ?>
    </a>
    <?php endif ?>
</div>
