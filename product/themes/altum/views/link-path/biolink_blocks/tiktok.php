<?php defined('ALTUMCODE') || die() ?>

<div data-link-id="<?= $data->link->link_id ?>" class="col-12 my-2">
    <div class="link-iframe-round">
        <blockquote class="tiktok-embed" data-video-id="<?= $data->embed ?>">
            <section></section>
        </blockquote>

        <script defer src="https://www.tiktok.com/embed.js"></script>
    </div>
</div>
