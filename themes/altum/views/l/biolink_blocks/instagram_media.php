<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2 d-flex justify-content-center">
    <blockquote class="instagram-media" data-instgrm-permalink="<?= $data->link->location_url ?>" data-instgrm-version="13"></blockquote>
    <script src="https://www.instagram.com/embed.js"></script>
    <script>
        setTimeout(() => {
            document.querySelector('div[data-biolink-block-id="<?= $data->link->biolink_block_id ?>"] iframe').style.width = '100%';
        }, 2000);
    </script>
</div>
