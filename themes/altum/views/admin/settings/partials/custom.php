<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="head_js"><i class="fab fa-fw fa-sm fa-js text-muted mr-1"></i> <?= language()->admin_settings->custom->head_js ?></label>
        <textarea id="head_js" name="head_js" class="form-control form-control-lg"><?= settings()->custom->head_js ?></textarea>
        <small class="form-text text-muted"><?= language()->admin_settings->custom->head_js_help ?></small>
    </div>

    <div class="form-group">
        <label for="head_css"><i class="fab fa-fw fa-sm fa-css3 text-muted mr-1"></i> <?= language()->admin_settings->custom->head_css ?></label>
        <textarea id="head_css" name="head_css" class="form-control form-control-lg"><?= settings()->custom->head_css ?></textarea>
        <small class="form-text text-muted"><?= language()->admin_settings->custom->head_css_help ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
