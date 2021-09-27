<?php defined('ALTUMCODE') || die() ?>

<div>
    <p class="text-muted"><?= language()->admin_settings->business->subheader ?></p>

    <div class="form-group">
        <label for="invoice_is_enabled"><?= language()->admin_settings->business->invoice_is_enabled ?></label>
        <select id="invoice_is_enabled" name="invoice_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->business->invoice_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->business->invoice_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->business->invoice_is_enabled_help ?></small>
    </div>

    <div class="form-group">
        <label for="invoice_nr_prefix"><?= language()->admin_settings->business->invoice_nr_prefix ?></label>
        <input type="text" name="invoice_nr_prefix" class="form-control form-control-lg" value="<?= settings()->business->invoice_nr_prefix ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->business->invoice_nr_prefix_help ?></small>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="name"><?= language()->admin_settings->business->name ?></label>
                <input type="text" name="name" class="form-control form-control-lg" value="<?= settings()->business->name ?>" />
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <label for="address"><?= language()->admin_settings->business->address ?></label>
                <input type="text" name="address" class="form-control form-control-lg" value="<?= settings()->business->address ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="city"><?= language()->admin_settings->business->city ?></label>
                <input type="text" name="city" class="form-control form-control-lg" value="<?= settings()->business->city ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="form-group">
                <label for="county"><?= language()->admin_settings->business->county ?></label>
                <input type="text" name="county" class="form-control form-control-lg" value="<?= settings()->business->county ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-2">
            <div class="form-group">
                <label for="zip"><?= language()->admin_settings->business->zip ?></label>
                <input type="text" name="zip" class="form-control form-control-lg" value="<?= settings()->business->zip ?>" />
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                <label for="country"><?= language()->admin_settings->business->country ?></label>
                <select id="country" name="country" class="form-control form-control-lg">
                    <?php foreach(get_countries_array() as $key => $value): ?>
                        <option value="<?= $key ?>" <?= settings()->business->country == $key ? 'selected="selected"' : null ?>><?= $value ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="email"><?= language()->admin_settings->business->email ?></label>
                <input type="text" name="email" class="form-control form-control-lg" value="<?= settings()->business->email ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="phone"><?= language()->admin_settings->business->phone ?></label>
                <input type="text" name="phone" class="form-control form-control-lg" value="<?= settings()->business->phone ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="tax_type"><?= language()->admin_settings->business->tax_type ?></label>
                <input type="text" name="tax_type" class="form-control form-control-lg" value="<?= settings()->business->tax_type ?>" placeholder="<?= language()->admin_settings->business->tax_type_placeholder ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="tax_id"><?= language()->admin_settings->business->tax_id ?></label>
                <input type="text" name="tax_id" class="form-control form-control-lg" value="<?= settings()->business->tax_id ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_key_one"><?= language()->admin_settings->business->custom_key_one ?></label>
                <input type="text" name="custom_key_one" class="form-control form-control-lg" value="<?= settings()->business->custom_key_one ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_value_one"><?= language()->admin_settings->business->custom_value_one ?></label>
                <input type="text" name="custom_value_one" class="form-control form-control-lg" value="<?= settings()->business->custom_value_one ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_key_two"><?= language()->admin_settings->business->custom_key_two ?></label>
                <input type="text" name="custom_key_two" class="form-control form-control-lg" value="<?= settings()->business->custom_key_two ?>" />
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label for="custom_value_two"><?= language()->admin_settings->business->custom_value_two ?></label>
                <input type="text" name="custom_value_two" class="form-control form-control-lg" value="<?= settings()->business->custom_value_two ?>" />
            </div>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
