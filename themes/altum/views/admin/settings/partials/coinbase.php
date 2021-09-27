<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= language()->admin_settings->coinbase->is_enabled ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->coinbase->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->coinbase->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="api_key"><?= language()->admin_settings->coinbase->api_key ?></label>
            <input id="api_key" type="text" name="api_key" class="form-control form-control-lg" value="<?= settings()->coinbase->api_key ?>" />
        </div>

        <div class="form-group">
            <label for="webhook_secret"><?= language()->admin_settings->coinbase->webhook_secret ?></label>
            <input id="webhook_secret" type="text" name="webhook_secret" class="form-control form-control-lg" value="<?= settings()->coinbase->webhook_secret ?>" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
