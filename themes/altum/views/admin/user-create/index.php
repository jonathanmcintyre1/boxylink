<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= language()->admin_user_create->header ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><?= language()->admin_user_create->form->name ?></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->values['name'] ?>" placeholder="<?= language()->admin_user_create->form->name_placeholder ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="email"><?= language()->admin_user_create->form->email ?></label>
                <input id="email" type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" placeholder="<?= language()->admin_user_create->form->email_placeholder ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('email') ?>
            </div>

            <div class="form-group">
                <label for="password"><?= language()->admin_user_create->form->password ?></label>
                <input id="password" type="password" name="password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" value="<?= $data->values['password'] ?>" placeholder="<?= language()->admin_user_create->form->password_placeholder ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('password') ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->create ?></button>
        </form>

    </div>
</div>

