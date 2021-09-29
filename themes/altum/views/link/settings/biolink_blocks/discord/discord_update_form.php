<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="discord" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'discord_server_id_' . $row->biolink_block_id ?>"><i class="fab fa-fw fa-discord fa-sm mr-1"></i> <?= language()->create_biolink_discord_modal->server_id ?></label>
        <input id="<?= 'discord_server_id_' . $row->biolink_block_id ?>" type="text" class="form-control" name="server_id" value="<?= $row->settings->server_id ?>" required="required" />
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
