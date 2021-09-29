<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="license"><?= language()->admin_settings->license->license ?></label>
        <input id="license" name="license" type="text" class="form-control form-control-lg disabled" value="<?= settings()->license->license ?>" readonly="readonly" />
        <small class="form-text text-muted"><?= language()->admin_settings->license->license_help ?></small>
    </div>

    <div class="form-group">
        <label for="type"><?= language()->admin_settings->license->type ?></label>
        <input id="type" name="type" type="text" class="form-control form-control-lg disabled" value="<?= settings()->license->type ?>" readonly="readonly" />
    </div>

    <div class="form-group">
        <label for="new_license"><?= language()->admin_settings->license->new_license ?></label>
        <input id="new_license" name="new_license" type="text" class="form-control form-control-lg" required="required" />
        <small class="form-text text-muted"><?= language()->admin_settings->license->new_license_help ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
