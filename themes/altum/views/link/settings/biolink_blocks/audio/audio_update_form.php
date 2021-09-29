<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="audio" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'audio_file_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-volume-up fa-sm mr-1"></i> <?= language()->create_biolink_audio_modal->file ?></label>
        <input id="<?= 'audio_file_' . $row->biolink_block_id ?>" type="file" name="file" accept="<?= implode(', ', array_map(function($value) { return '.' . $value; }, $data->biolink_blocks['audio']['whitelisted_file_extensions'])) ?>" class="form-control-file" />
        <small class="form-text text-muted"><?= language()->create_biolink_audio_modal->file_help ?></small>
    </div>

    <div class="form-group">
        <label for="<?= 'audio_name_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
        <input id="<?= 'audio_name_' . $row->biolink_block_id ?>" type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" required="required" />
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
