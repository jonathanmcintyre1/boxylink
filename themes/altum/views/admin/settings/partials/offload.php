<?php defined('ALTUMCODE') || die() ?>

<div>
    <div <?= !\Altum\Plugin::is_active('offload') ? 'data-toggle="tooltip" title="' . sprintf(language()->admin_plugins->no_access, \Altum\Plugin::get('offload')->name ?? 'offload') . '"' : null ?>>
        <div class="<?= !\Altum\Plugin::is_active('offload') ? 'container-disabled' : null ?>">
            <div class="form-group">
                <label for="assets_url"><?= language()->admin_settings->offload->assets_url ?></label>
                <input id="assets_url" type="url" name="assets_url" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->assets_url : null ?>" />
                <small class="form-text text-muted"><?= language()->admin_settings->offload->assets_url_help ?></small>
            </div>

            <div class="form-group">
                <label for="provider"><?= language()->admin_settings->offload->provider ?></label>
                <select id="provider" name="provider" class="form-control form-control-lg">
                    <option value="aws-s3" <?= \Altum\Plugin::is_active('offload') && settings()->offload->provider == 'aws-s3' ? 'selected="selected"' : null ?>>AWS S3</option>
                    <option value="digitalocean-spaces" <?= \Altum\Plugin::is_active('offload') && settings()->offload->provider == 'digitalocean-spaces' ? 'selected="selected"' : null ?>>DigitalOcean Spaces</option>
                    <option value="vultr-objects" <?= \Altum\Plugin::is_active('offload') && settings()->offload->provider == 'vultr-objects' ? 'selected="selected"' : null ?>>Vultr Objects</option>
                    <option value="wasabi" <?= \Altum\Plugin::is_active('offload') && settings()->offload->provider == 'wasabi' ? 'selected="selected"' : null ?>>Wasabi</option>
                    <option value="other-s3" <?= \Altum\Plugin::is_active('offload') && settings()->offload->provider == 'other-s3' ? 'selected="selected"' : null ?>>Other storage compatible with S3 SDK</option>
                </select>
            </div>

            <div id="provider_others" class="form-group">
                <label for="endpoint_url"><?= language()->admin_settings->offload->endpoint_url ?></label>
                <input id="endpoint_url" type="url" name="endpoint_url" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->endpoint_url : null ?>" />
            </div>

            <div class="form-group">
                <label for="uploads_url"><?= language()->admin_settings->offload->uploads_url ?></label>
                <input id="uploads_url" type="url" name="uploads_url" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->uploads_url : null ?>" />
                <small class="form-text text-muted"><?= language()->admin_settings->offload->uploads_url_help ?></small>
            </div>

            <div class="form-group">
                <label for="access_key"><?= language()->admin_settings->offload->access_key ?></label>
                <input id="access_key" type="text" name="access_key" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->access_key : null ?>" />
            </div>

            <div class="form-group">
                <label for="secret_access_key"><?= language()->admin_settings->offload->secret_access_key ?></label>
                <input id="secret_access_key" type="text" name="secret_access_key" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->secret_access_key : null ?>" />
            </div>

            <div class="form-group">
                <label for="storage_name"><?= language()->admin_settings->offload->storage_name ?></label>
                <input id="storage_name" type="text" name="storage_name" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->storage_name : null ?>" />
            </div>

            <div class="form-group" id="provider_aws_s3">
                <label for="region"><?= language()->admin_settings->offload->region ?></label>
                <input id="region" type="text" name="region" class="form-control form-control-lg" value="<?= \Altum\Plugin::is_active('offload') ? settings()->offload->region : null ?>" />
            </div>
        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('offload')): ?>
<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
<?php endif ?>

<?php ob_start() ?>
<script>
    'use strict';

    /* Offload */
    let initiate_offload_provider = () => {
        switch(document.querySelector('select[name="provider"]').value) {
            case 'aws-s3':
                document.querySelector('#provider_others').classList.add('d-none');
                document.querySelector('#provider_aws_s3').classList.remove('d-none');
                break;

            /* Other providers */
            default:
                document.querySelector('#provider_others').classList.remove('d-none');
                document.querySelector('#provider_aws_s3').classList.add('d-none');
                break;
        }
    }

    initiate_offload_provider();
    document.querySelector('select[name="provider"]').addEventListener('change', initiate_offload_provider);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/settings/settings_send_test_email_modal.php'), 'modals'); ?>
