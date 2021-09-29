<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="socials" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'socials_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= language()->create_biolink_socials_modal->color ?></label>
        <input type="color" id="<?= 'socials_color_' . $row->biolink_block_id ?>" name="color" class="form-control" value="<?= $row->settings->color ?>" required="required" />
    </div>

    <?php $biolink_socials = require APP_PATH . 'includes/biolink_socials.php'; ?>
    <?php foreach($biolink_socials as $key => $value): ?>
        <?php if($value['input_group']): ?>
            <div class="form-group">
                <label for="<?= 'socials_' . $key . '_' . $row->biolink_block_id ?>"><i class="<?= language()->create_biolink_socials_modal->socials->{$key}->icon ?> fa-fw fa-sm mr-1"></i> <?= language()->create_biolink_socials_modal->socials->{$key}->name ?></label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?= str_replace('%s', '', $value['format']) ?></span>
                    </div>
                    <input id="<?= 'socials_' . $key . '_' . $row->biolink_block_id ?>" type="text" class="form-control" name="socials[<?= $key ?>]" placeholder="<?= language()->create_biolink_socials_modal->socials->{$key}->placeholder ?>" value="<?= $row->settings->socials->{$key} ?? '' ?>" />
                </div>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label for="<?= 'socials_' . $key . '_' . $row->biolink_block_id ?>"><i class="<?= language()->create_biolink_socials_modal->socials->{$key}->icon ?> fa-fw fa-sm mr-1"></i> <?= language()->create_biolink_socials_modal->socials->{$key}->name ?></label>
                <input id="<?= 'socials_' . $key . '_' . $row->biolink_block_id ?>" type="text" class="form-control" name="socials[<?= $key ?>]" placeholder="<?= language()->create_biolink_socials_modal->socials->{$key}->placeholder ?>" value="<?= $row->settings->socials->{$key} ?? '' ?>" />
            </div>
        <?php endif ?>
    <?php endforeach ?>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
