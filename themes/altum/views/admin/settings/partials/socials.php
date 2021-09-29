<?php defined('ALTUMCODE') || die() ?>

<div>
    <p class="text-muted"><?= language()->admin_settings->socials->help ?></p>

    <?php foreach(require APP_PATH . 'includes/admin_socials.php' AS $key => $value): ?>
        <label for="<?= $key ?>"><i class="<?= $value['icon'] ?> fa-fw fa-sm mr-1 text-muted"></i> <?= $value['name'] ?></label>
        <div class="input-group mb-3">
            <?php if($value['input_display_format']): ?>
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= str_replace('%s', '', $value['format']) ?></span>
                </div>
            <?php endif ?>
            <input id="<?= $key ?>" type="text" name="<?= $key ?>" class="form-control form-control-lg" value="<?= settings()->socials->{$key} ?>" placeholder="<?= $value['placeholder'] ?>" />
        </div>
    <?php endforeach ?>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
