<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="facebook" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'facebook_url_' . $row->biolink_block_id ?>"><i class="fab fa-fw fa-facebook fa-sm mr-1"></i> <?= language()->link->create_biolink_facebook_modal->location_url ?></label>
        <input id="<?= 'facebook_url_' . $row->biolink_block_id ?>" type="url" class="form-control" name="location_url" value="<?= $row->location_url ?>" required="required" />
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
