<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="is_enabled"><?= language()->admin_settings->google->is_enabled ?></label>
        <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->google->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->google->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="client_id"><?= language()->admin_settings->google->client_id ?></label>
        <input id="client_id" type="text" name="client_id" class="form-control form-control-lg" value="<?= settings()->google->client_id ?>" />
    </div>

    <div class="form-group">
        <label for="client_secret"><?= language()->admin_settings->google->client_secret ?></label>
        <input id="client_secret" type="text" name="client_secret" class="form-control form-control-lg" value="<?= settings()->google->client_secret ?>" />
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
