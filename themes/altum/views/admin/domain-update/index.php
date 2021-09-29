<?php defined('ALTUMCODE') || die() ?>

<div class="mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-globe text-primary-900 mr-2"></i> <?= language()->admin_domain_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/domains/admin_domain_dropdown_button.php', ['id' => $data->domain->domain_id]) ?>
    </div>

    <p class="text-muted"><?= language()->admin_domain_create->subheader ?></p>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <?php $url = parse_url(SITE_URL); $host = $url['host'] . (mb_strlen($url['path']) > 1 ? $url['path'] : null); ?>

            <div class="form-group">
                <div class="d-flex">
                    <img src="<?= get_gravatar($data->user->email) ?>" class="user-avatar rounded-circle mr-3" alt="" />

                    <div class="d-flex flex-column">
                        <div>
                            <a href="<?= url('admin/user-view/' . $data->user->user_id) ?>"><?= $data->user->name ?></a>
                        </div>

                        <span class="text-muted"><?= $data->user->email ?></span>
                    </div>
                </div>
            </div>

            <p class="text-muted"><?= sprintf(language()->admin_domains->main->helper, '<strong>' . $_SERVER['SERVER_ADDR'] . '</strong>', '<strong>' . $host . '</strong>') ?></p>

            <div class="form-group">
                <label for="host"><?= language()->admin_domains->main->host ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select name="scheme" class="appearance-none select-custom-altum form-control form-control-lg input-group-text">
                            <option value="https://" <?= $data->domain->scheme == 'https://' ? 'selected="selected"' : null ?>>https://</option>
                            <option value="http://" <?= $data->domain->scheme == 'http://' ? 'selected="selected"' : null ?>>http://</option>
                        </select>
                    </div>
                    <input id="host" type="text" name="host" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('host') ? 'is-invalid' : null ?>" placeholder="<?= language()->admin_domains->main->host_placeholder ?>" value="<?= $data->domain->host ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('host') ?>
                </div>
                <small class="form-text text-muted"><?= language()->admin_domains->main->host_help ?></small>
            </div>

            <div class="form-group">
                <label for="custom_index_url"><?= language()->admin_domains->main->custom_index_url ?></label>
                <input id="custom_index_url" type="text" name="custom_index_url" class="form-control form-control-lg" value="<?= $data->domain->custom_index_url ?>" placeholder="<?= language()->admin_domains->main->custom_index_url_placeholder ?>" />
                <small class="form-text text-muted"><?= language()->admin_domains->main->custom_index_url_help ?></small>
            </div>

            <div class="form-group">
                <label for="custom_not_found_url"><?= language()->admin_domains->main->custom_not_found_url ?></label>
                <input id="custom_not_found_url" type="text" name="custom_not_found_url" class="form-control form-control-lg" value="<?= $data->domain->custom_not_found_url ?>" placeholder="<?= language()->admin_domains->main->custom_not_found_url_placeholder ?>" />
                <small class="form-text text-muted"><?= language()->admin_domains->main->custom_not_found_url_help ?></small>
            </div>

            <div class="form-group">
                <label for="is_enabled"><?= language()->admin_domains->main->is_enabled ?></label>
                <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                    <option value="1" <?= $data->domain->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->active ?></option>
                    <option value="0" <?= !$data->domain->is_enabled ? 'selected="selected"' : null ?>><?= language()->global->disabled ?></option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary"><?= language()->global->update ?></button>
            </div>
        </form>

    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/domains/domain_delete_modal.php'), 'modals'); ?>
