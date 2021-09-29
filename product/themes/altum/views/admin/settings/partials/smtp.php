<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="from_name"><?= language()->admin_settings->smtp->from_name ?></label>
        <input type="text" name="from_name" class="form-control form-control-lg" value="<?= settings()->smtp->from_name ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->smtp->from_name_help ?></small>
    </div>

    <div class="form-group">
        <label for="from"><?= language()->admin_settings->smtp->from ?></label>
        <input type="text" name="from" class="form-control form-control-lg" value="<?= settings()->smtp->from ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->smtp->from_help ?></small>
    </div>

    <div class="form-group">
        <label for="host"><?= language()->admin_settings->smtp->host ?></label>
        <input type="text" name="host" class="form-control form-control-lg" value="<?= settings()->smtp->host ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->smtp->host_help ?></small>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="encryption"><?= language()->admin_settings->smtp->encryption ?></label>
                <select id="encryption" name="encryption" class="form-control form-control-lg">
                    <option value="0" <?= settings()->smtp->encryption == '0' ? 'selected="selected"' : null ?>>None</option>
                    <option value="ssl" <?= settings()->smtp->encryption == 'ssl' ? 'selected="selected"' : null ?>>SSL</option>
                    <option value="tls" <?= settings()->smtp->encryption == 'tls' ? 'selected="selected"' : null ?>>TLS</option>
                </select>
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-group">
                <label for="port"><?= language()->admin_settings->smtp->port ?></label>
                <input id="port" type="text" name="port" class="form-control form-control-lg" value="<?= settings()->smtp->port ?>" />
            </div>
        </div>
    </div>

    <div class="custom-control custom-switch mb-3">
        <input id="auth" name="auth" type="checkbox" class="custom-control-input" <?= settings()->smtp->auth ? 'checked="checked"' : null ?>>
        <label class="custom-control-label" for="auth"><?= language()->admin_settings->smtp->auth ?></label>
    </div>

    <div class="form-group">
        <label for="username"><?= language()->admin_settings->smtp->username ?></label>
        <input id="username" type="text" name="username" class="form-control form-control-lg" value="<?= settings()->smtp->username ?>" />
    </div>

    <div class="form-group">
        <label for="password"><?= language()->admin_settings->smtp->password ?></label>
        <input id="password" type="password" name="password" class="form-control form-control-lg" value="<?= settings()->smtp->password ?>" />
    </div>

    <div class="my-3">
        <button type="button" class="btn btn-block btn-outline-info" data-toggle="modal" data-target="#settings_send_test_email_modal"><?= language()->admin_settings_send_test_email_modal->header ?></button>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>

<?php ob_start() ?>
<script>
    'use strict';

    /* SMTP */
    let auth_handler = () => {
        if(document.querySelector('input[name="auth"]').checked) {
            document.querySelector('input[name="username"]').removeAttribute('readonly');
            document.querySelector('input[name="password"]').removeAttribute('readonly');
        } else {
            document.querySelector('input[name="username"]').setAttribute('readonly', 'readonly');
            document.querySelector('input[name="password"]').setAttribute('readonly', 'readonly');
        }
    }

    auth_handler();
    document.querySelector('input[name="auth"]').addEventListener('change', auth_handler);

    /* Disable send test email if the smtp fields change & are not saved */
    document.querySelectorAll('input').forEach(element => {
        element.addEventListener('change', event => {
            document.querySelector('[data-target="#settings_send_test_email_modal"]').classList.add('disabled');
            document.querySelector('[data-target="#settings_send_test_email_modal"]').setAttribute('disabled', 'disabled');
        }) ;
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/settings/settings_send_test_email_modal.php'), 'modals'); ?>
