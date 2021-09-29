<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-receipt text-primary-900 mr-2"></i> <?= language()->admin_tax_create->header ?></h1>
</div>

<div class="alert alert-info" role="alert">
    <?= language()->admin_tax_create->subheader ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">
        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="internal_name"><?= language()->admin_taxes->main->internal_name ?></label>
                <input type="text" id="internal_name" name="internal_name" class="form-control form-control-lg" required="required" />
                <small class="form-text text-muted"><?= language()->admin_taxes->main->internal_name_help ?></small>
            </div>

            <div class="form-group">
                <label for="name"><?= language()->admin_taxes->main->name ?></label>
                <input type="text" id="name" name="name" class="form-control form-control-lg" required="required" />
            </div>

            <div class="form-group">
                <label for="description"><?= language()->admin_taxes->main->description ?></label>
                <input type="text" id="description" name="description" class="form-control form-control-lg" required="required" />
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="value"><?= language()->admin_taxes->main->value ?></label>
                        <input type="number" min="0" id="value" name="value" class="form-control form-control-lg" value="1" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="value_type"><?= language()->admin_taxes->main->value_type ?></label>
                        <select id="value_type" name="value_type" class="form-control form-control-lg">
                            <option value="percentage"><?= language()->admin_taxes->main->value_type_percentage ?></option>
                            <option value="fixed"><?= language()->admin_taxes->main->value_type_fixed ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="type"><?= language()->admin_taxes->main->type ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="inclusive"><?= language()->admin_taxes->main->type_inclusive ?></option>
                    <option value="exclusive"><?= language()->admin_taxes->main->type_exclusive ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="billing_type"><?= language()->admin_taxes->main->billing_type ?></label>
                <select id="billing_type" name="billing_type" class="form-control form-control-lg">
                    <option value="personal"><?= language()->admin_taxes->main->billing_type_personal ?></option>
                    <option value="business"><?= language()->admin_taxes->main->billing_type_business ?></option>
                    <option value="both"><?= language()->admin_taxes->main->billing_type_both ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="countries"><?= language()->admin_taxes->main->countries ?></label>
                <select id="countries" name="countries[]" class="form-control form-control-lg" multiple="multiple">
                    <?php foreach(get_countries_array() as $key => $value): ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach ?>
                </select>
                <small class="form-text text-muted"><?= language()->admin_taxes->main->countries_help ?></small>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?= language()->global->create ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    let checker = () => {
        let value_type = document.querySelector('select[name="value_type"]').value;

        switch(value_type) {
            case 'percentage':

                document.querySelector('select[name="type"] option[value="inclusive"]').removeAttribute('disabled');
                document.querySelector('select[name="type"] option[value="exclusive"]').removeAttribute('selected');

                break;

            case 'fixed':

                document.querySelector('select[name="type"] option[value="inclusive"]').setAttribute('disabled', 'disabled');
                document.querySelector('select[name="type"] option[value="exclusive"]').setAttribute('selected', 'selected');

                break;
        }
    };

    checker();

    document.querySelector('select[name="value_type"]').addEventListener('change', checker);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
