<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= language()->admin_settings->paypal->is_enabled ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->paypal->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->paypal->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="mode"><?= language()->admin_settings->paypal->mode ?></label>
            <select id="mode" name="mode" class="form-control form-control-lg">
                <option value="live" <?= settings()->paypal->mode == 'live' ? 'selected="selected"' : null ?>>live</option>
                <option value="sandbox" <?= settings()->paypal->mode == 'sandbox' ? 'selected="selected"' : null ?>>sandbox</option>
            </select>
            <small class="form-text text-muted"><?= language()->admin_settings->paypal->mode_help ?></small>
        </div>

        <div class="form-group">
            <label for="client_id"><?= language()->admin_settings->paypal->client_id ?></label>
            <input id="client_id" type="text" name="client_id" class="form-control form-control-lg" value="<?= settings()->paypal->client_id ?>" />
        </div>

        <div class="form-group">
            <label for="secret"><?= language()->admin_settings->paypal->secret ?></label>
            <input id="secret" type="text" name="secret" class="form-control form-control-lg" value="<?= settings()->paypal->secret ?>" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
