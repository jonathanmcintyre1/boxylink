<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="embed-responsive embed-responsive-16by9 link-iframe-round">
        <iframe
                class="embed-responsive-item"
                scrolling="no"
                frameborder="no"
                src="https://player.twitch.tv/?channel=<?= $data->embed ?>&autoplay=false&parent=<?= \Altum\Database\Database::clean_string($_SERVER['HTTP_HOST']) ?>"
        ></iframe>
    </div>
</div>

