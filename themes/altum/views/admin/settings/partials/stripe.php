<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= language()->admin_settings->stripe->is_enabled ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->stripe->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->stripe->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="publishable_key"><?= language()->admin_settings->stripe->publishable_key ?></label>
            <input id="publishable_key" type="text" name="publishable_key" class="form-control form-control-lg" value="<?= settings()->stripe->publishable_key ?>" />
        </div>

        <div class="form-group">
            <label for="secret_key"><?= language()->admin_settings->stripe->secret_key ?></label>
            <input id="secret_key" type="text" name="secret_key" class="form-control form-control-lg" value="<?= settings()->stripe->secret_key ?>" />
        </div>

        <div class="form-group">
            <label for="webhook_secret"><?= language()->admin_settings->stripe->webhook_secret ?></label>
            <input id="webhook_secret" type="text" name="webhook_secret" class="form-control form-control-lg" value="<?= settings()->stripe->webhook_secret ?>" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
