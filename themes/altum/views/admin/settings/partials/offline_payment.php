<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><?= language()->admin_settings->offline_payment->is_enabled ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->offline_payment->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->offline_payment->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="instructions"><?= language()->admin_settings->offline_payment->instructions ?></label>
            <textarea id="instructions" name="instructions" class="form-control form-control-lg"><?= settings()->offline_payment->instructions ?></textarea>
            <small class="form-text text-muted"><?= language()->admin_settings->offline_payment->instructions_help ?></small>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
