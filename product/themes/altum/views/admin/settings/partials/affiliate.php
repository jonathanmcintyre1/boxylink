<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the affiliate plugin system.
        </div>
    <?php endif ?>

    <div <?= !\Altum\Plugin::is_active('affiliate') ? 'data-toggle="tooltip" title="' . sprintf(language()->admin_plugins->no_access, \Altum\Plugin::get('affiliate')->name ?? 'affiliate') . '"' : null ?>>
        <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) || !\Altum\Plugin::is_active('affiliate') ? 'container-disabled' : null ?>">
            <div class="form-group">
                <label for="is_enabled"><?= language()->admin_settings->affiliate->is_enabled ?></label>
                <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                    <option value="1" <?= \Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                    <option value="0" <?= \Altum\Plugin::is_active('affiliate') && !settings()->affiliate->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
                </select>
                <small class="form-text text-muted"><?= language()->admin_settings->affiliate->is_enabled_help ?></small>
            </div>

            <div class="form-group">
                <label for="commission_type"><?= language()->admin_settings->affiliate->commission_type ?></label>
                <select id="commission_type" name="commission_type" class="form-control form-control-lg">
                    <option value="once" <?= \Altum\Plugin::is_active('affiliate') && settings()->affiliate->commission_type == 'once' ? 'selected="selected"' : null ?>><?= language()->admin_settings->affiliate->commission_type_once ?></option>
                    <option value="forever" <?= \Altum\Plugin::is_active('affiliate') && settings()->affiliate->commission_type == 'forever' ? 'selected="selected"' : null ?>><?= language()->admin_settings->affiliate->commission_type_forever ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="minimum_withdrawal_amount"><?= language()->admin_settings->affiliate->minimum_withdrawal_amount ?></label>
                <input id="minimum_withdrawal_amount" type="number" min="1" name="minimum_withdrawal_amount" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('affiliate') ? settings()->affiliate->minimum_withdrawal_amount : 1 ?>" />
                <small class="form-text text-muted"><?= language()->admin_settings->affiliate->minimum_withdrawal_amount_help ?></small>
            </div>

            <div class="form-group">
                <label for="commission_percentage"><?= language()->admin_settings->affiliate->commission_percentage ?></label>
                <input id="commission_percentage" type="number" min="1" max="99" step="1" name="commission_percentage" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('affiliate') ? settings()->affiliate->commission_percentage : 1 ?>" />
                <small class="form-text text-muted"><?= language()->admin_settings->affiliate->commission_percentage_help ?></small>
            </div>

            <div class="form-group">
                <label for="withdrawal_notes"><?= language()->admin_settings->affiliate->withdrawal_notes ?></label>
                <textarea id="withdrawal_notes" name="withdrawal_notes" class="form-control form-control-lg"><?= \Altum\Plugin::is_active('affiliate') ? settings()->affiliate->withdrawal_notes : null ?></textarea>
                <small class="form-text text-muted"><?= language()->admin_settings->affiliate->withdrawal_notes_help ?></small>
            </div>
        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('affiliate')): ?>
    <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
<?php endif ?>
