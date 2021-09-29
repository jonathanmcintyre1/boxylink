<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-receipt text-primary-900 mr-2"></i> <?= language()->admin_tax_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/taxes/admin_tax_dropdown_button.php', ['id' => $data->tax->tax_id]) ?>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="internal_name"><?= language()->admin_taxes->main->internal_name ?></label>
                <input type="text" id="internal_name" name="internal_name" class="form-control form-control-lg" value="<?= $data->tax->internal_name ?>" required="required" />
                <small class="form-text text-muted"><?= language()->admin_taxes->main->internal_name_help ?></small>
            </div>

            <div class="form-group">
                <label for="name"><?= language()->admin_taxes->main->name ?></label>
                <input type="text" id="name" name="name" class="form-control form-control-lg" value="<?= $data->tax->name ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="description"><?= language()->admin_taxes->main->description ?></label>
                <input type="text" id="description" name="description" class="form-control form-control-lg" value="<?= $data->tax->description ?>" required="required" />
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="value"><?= language()->admin_taxes->main->value ?></label>
                        <input type="number" min="0" id="value" name="value" class="form-control form-control-lg" value="<?= $data->tax->value ?>" disabled="disabled" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="value_type"><?= language()->admin_taxes->main->value_type ?></label>
                        <select id="value_type" name="value_type" class="form-control form-control-lg" disabled="disabled">
                            <option value="percentage" <?= $data->tax->value_type == 'percentage' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->value_type_percentage ?></option>
                            <option value="fixed" <?= $data->tax->value_type == 'fixed' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->value_type_fixed ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="type"><?= language()->admin_taxes->main->type ?></label>
                <select id="type" name="type" class="form-control form-control-lg" disabled="disabled">
                    <option value="inclusive" <?= $data->tax->type == 'inclusive' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->type_inclusive ?></option>
                    <option value="exclusive" <?= $data->tax->type == 'exclusive' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->type_exclusive ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="billing_type"><?= language()->admin_taxes->main->billing_type ?></label>
                <select id="billing_type" name="billing_type" class="form-control form-control-lg" disabled="disabled">
                    <option value="personal" <?= $data->tax->billing_type == 'personal' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->billing_type_personal ?></option>
                    <option value="business" <?= $data->tax->billing_type == 'business' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->billing_type_business ?></option>
                    <option value="both" <?= $data->tax->billing_type == 'both' ? 'selected="selected"' : null ?>><?= language()->admin_taxes->main->billing_type_both ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="countries"><?= language()->admin_taxes->main->countries ?></label>
                <select id="countries" name="countries[]" class="form-control form-control-lg" multiple="multiple" disabled="disabled">
                    <?php foreach(get_countries_array() as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $data->tax->countries && in_array($key, $data->tax->countries)  ? 'selected="selected"' : null ?>><?= $value ?></option>
                    <?php endforeach ?>
                </select>
                <small class="form-text text-muted"><?= language()->admin_taxes->main->countries_help ?></small>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
        </form>

    </div>
</div>

