<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="is_enabled"><i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= language()->admin_settings->payment->is_enabled ?></label>
            <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->payment->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->payment->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
            <small class="form-text text-muted"><?= language()->admin_settings->payment->is_enabled_help ?></small>
        </div>

        <div class="form-group">
            <label for="type"><i class="fa fa-fw fa-sm fa-credit-card text-muted mr-1"></i> <?= language()->admin_settings->payment->type ?></label>
            <select id="type" name="type" class="form-control form-control-lg">
                <option value="one_time" <?= settings()->payment->type == 'one_time' ? 'selected="selected"' : null ?>><?= language()->admin_settings->payment->type_one_time ?></option>
                <option value="recurring" <?= settings()->payment->type == 'recurring' ? 'selected="selected"' : null ?>><?= language()->admin_settings->payment->type_recurring ?></option>
                <option value="both" <?= settings()->payment->type == 'both' ? 'selected="selected"' : null ?>><?= language()->admin_settings->payment->type_both ?></option>
            </select>
            <small class="form-text text-muted"><?= language()->admin_settings->payment->type_help ?></small>
        </div>

        <div class="form-group">
            <label for="brand_name"><i class="fa fa-fw fa-sm fa-copyright text-muted mr-1"></i> <?= language()->admin_settings->payment->brand_name ?></label>
            <input id="brand_name" type="text" name="brand_name" class="form-control form-control-lg" value="<?= settings()->payment->brand_name ?>" />
            <small class="form-text text-muted"><?= language()->admin_settings->payment->brand_name_help ?></small>
        </div>

        <div class="form-group">
            <label for="currency"><i class="fa fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= language()->admin_settings->payment->currency ?></label>
            <input id="currency" type="text" name="currency" class="form-control form-control-lg" value="<?= settings()->payment->currency ?>" />
            <small class="form-text text-muted"><?= language()->admin_settings->payment->currency_help ?></small>
        </div>

        <div class="form-group">
            <label for="codes_is_enabled"><i class="fa fa-fw fa-sm fa-tags text-muted mr-1"></i> <?= language()->admin_settings->payment->codes_is_enabled ?></label>
            <select id="codes_is_enabled" name="codes_is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->payment->codes_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->payment->codes_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
            <small class="form-text text-muted"><?= language()->admin_settings->payment->codes_is_enabled_help ?></small>
        </div>

        <div class="form-group">
            <label for="taxes_and_billing_is_enabled"><i class="fa fa-fw fa-sm fa-receipt text-muted mr-1"></i> <?= language()->admin_settings->payment->taxes_and_billing_is_enabled ?></label>
            <select id="taxes_and_billing_is_enabled" name="taxes_and_billing_is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->payment->taxes_and_billing_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                <option value="0" <?= !settings()->payment->taxes_and_billing_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
            </select>
            <small class="form-text text-muted"><?= language()->admin_settings->payment->taxes_and_billing_is_enabled_help ?></small>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
