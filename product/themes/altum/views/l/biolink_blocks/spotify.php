<?php defined('ALTUMCODE') || die() ?>

<?php if(in_array($data->embed_type, ['show', 'episode'])): ?>
    <div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
        <iframe src="https://open.spotify.com/embed/<?= $data->embed_type ?>/<?= $data->embed_value ?>?theme=0" width="100%" height="232" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>
    </div>
<?php elseif(in_array($data->embed_type, ['track', 'album'])): ?>
    <div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
        <div class="link-iframe-round" <?= $data->embed_type == 'track' ? 'style="height: 80px;"' : 'style="height: 380px;"' ?>>
            <iframe  scrolling="no" frameborder="no" src="https://open.spotify.com/embed/<?= $data->embed_type ?>/<?= $data->embed_value ?>" width="100%" <?= $data->embed_type == 'track' ? 'height="80"' : 'height="380"' ?>></iframe>
        </div>
    </div>
<?php endif ?>


