<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="custom_html" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'custom_html_html_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-code fa-sm mr-1"></i> <?= language()->create_biolink_custom_html_modal->html ?></label>
        <textarea id="<?= 'custom_html_html_' . $row->biolink_block_id ?>" name="html" class="form-control"><?= $row->settings->html ?></textarea>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
