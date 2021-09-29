<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= language()->admin_code_create->header ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="type"><?= language()->admin_codes->main->type ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="discount"><?= language()->admin_codes->main->type_discount ?></option>
                    <option value="redeemable"><?= language()->admin_codes->main->type_redeemable ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="code"><?= language()->admin_codes->main->code ?></label>
                <input type="text" id="code" name="code" class="form-control form-control-lg" required="required" />
            </div>

            <div class="form-group">
                <label for="plan_id"><?= language()->admin_codes->main->plan_id ?></label>
                <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                    <?php foreach($data->plans as $row): ?>
                        <option value="<?= $row->plan_id ?>"><?= $row->name ?></option>
                    <?php endforeach ?>

                    <option value=""><?= language()->admin_codes->main->plan_id_null ?></option>
                </select>
                <small class="form-text text-muted"><?= language()->admin_codes->main->plan_id_help ?></small>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div id="discount_container" class="form-group">
                        <label for="discount"><?= language()->admin_codes->main->discount ?></label>
                        <input type="number" min="1" max="99" id="discount" name="discount" class="form-control form-control-lg" value="1" />
                        <small class="form-text text-muted"><?= language()->admin_codes->main->discount_help ?></small>
                    </div>

                    <div id="days_container" class="form-group">
                        <label for="days"><?= language()->admin_codes->main->days ?></label>
                        <input type="number" min="1" max="999999" id="days" name="days" class="form-control form-control-lg" value="1" />
                        <small class="form-text text-muted"><?= language()->admin_codes->main->days_help ?></small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="quantity"><?= language()->admin_codes->main->quantity ?></label>
                        <input type="number" min="1" id="quantity" name="quantity" class="form-control form-control-lg" value="1" />
                        <small class="form-text text-muted"><?= language()->admin_codes->main->quantity_help ?></small>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->create ?></button>
        </form>

    </div>
</div>

<?php ob_start() ?>
<script>
    let checker = () => {
        let type = document.querySelector('select[name="type"]').value;

        switch(type) {
            case 'discount':

                document.querySelector('#discount_container').style.display = 'block';
                document.querySelector('#days_container').style.display = 'none';
                document.querySelector('select[name="plan_id"] option[value=""]').style.display = 'block';
                document.querySelector('select[name="plan_id"] option[value=""]').removeAttribute('disabled');
                break;

            case 'redeemable':

                document.querySelector('#discount_container').style.display = 'none';
                document.querySelector('#days_container').style.display = 'block';
                document.querySelector('select[name="plan_id"] option[value=""]').style.display = 'none';
                document.querySelector('select[name="plan_id"] option[value=""]').setAttribute('disabled', 'disabled');

                break;
        }
    };

    checker();

    document.querySelector('select[name="type"]').addEventListener('change', checker);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
