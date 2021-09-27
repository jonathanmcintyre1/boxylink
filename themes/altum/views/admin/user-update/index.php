<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= language()->admin_user_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $data->user->user_id]) ?>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<?php //ALTUMCODE:DEMO if(DEMO) {$data->user->email = 'hidden@demo.com'; $data->user->name = $data->user->ip = 'hidden on demo';} ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><?= language()->admin_user_update->main->name ?></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->user->name ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="email"><?= language()->admin_user_update->main->email ?></label>
                <input id="email" type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->user->email ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('email') ?>
            </div>

            <div class="form-group">
                <label for="is_enabled"><?= language()->admin_user_update->main->is_enabled ?></label>
                <select id="is_enabled" name="is_enabled" class="form-control form-control-lg">
                    <option value="2" <?= $data->user->active == 2 ? 'selected="selected"' : null ?>><?= language()->admin_user_update->main->is_enabled_disabled ?></option>
                    <option value="1" <?= $data->user->active == 1 ? 'selected="selected"' : null ?>><?= language()->admin_user_update->main->is_enabled_active ?></option>
                    <option value="0" <?= $data->user->active == 0 ? 'selected="selected"' : null ?>><?= language()->admin_user_update->main->is_enabled_unconfirmed ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="type"><?= language()->admin_user_update->main->type ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->type == 1 ? 'selected="selected"' : null ?>><?= language()->admin_user_update->main->type_admin ?></option>
                    <option value="0" <?= $data->user->type == 0 ? 'selected="selected"' : null ?>><?= language()->admin_user_update->main->type_user ?></option>
                </select>
                <small class="form-text text-muted"><?= language()->admin_user_update->main->type_help ?></small>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= language()->admin_user_update->plan->header ?></h2>

            <div class="form-group">
                <label for="plan_id"><?= language()->admin_user_update->plan->plan_id ?></label>
                <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                    <option value="free" <?= $data->user->plan->plan_id == 'free' ? 'selected="selected"' : null ?>><?= settings()->plan_free->name ?></option>
                    <option value="custom" <?= $data->user->plan->plan_id == 'custom' ? 'selected="selected"' : null ?>><?= settings()->plan_custom->name ?></option>

                    <?php foreach($data->plans as $plan): ?>
                        <option value="<?= $plan->plan_id ?>" <?= $data->user->plan->plan_id == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="plan_trial_done"><?= language()->admin_user_update->plan->plan_trial_done ?></label>
                <select id="plan_trial_done" name="plan_trial_done" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                    <option value="0" <?= !$data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
                </select>
            </div>

            <div id="plan_expiration_date_container" class="form-group">
                <label for="plan_expiration_date"><?= language()->admin_user_update->plan->plan_expiration_date ?></label>
                <input id="plan_expiration_date" type="text" name="plan_expiration_date" class="form-control form-control-lg" autocomplete="off" value="<?= $data->user->plan_expiration_date ?>">
                <div class="invalid-feedback">
                    <?= language()->admin_user_update->plan->plan_expiration_date_invalid ?>
                </div>
            </div>

            <div id="plan_settings" style="display: none">
                <div class="form-group">
                    <label for="projects_limit"><?= language()->admin_plans->plan->projects_limit ?></label>
                    <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->projects_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->projects_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="pixels_limit"><?= language()->admin_plans->plan->pixels_limit ?></label>
                    <input type="number" id="pixels_limit" name="pixels_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->pixels_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->pixels_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="biolinks_limit"><?= language()->admin_plans->plan->biolinks_limit ?></label>
                    <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->biolinks_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->biolinks_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="biolink_blocks_limit"><?= language()->admin_plans->plan->biolink_blocks_limit ?></label>
                    <input type="number" id="biolink_blocks_limit" name="biolink_blocks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->biolink_blocks_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->biolink_blocks_limit_help ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="links_limit"><?= language()->admin_plans->plan->links_limit ?></label>
                    <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->links_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->links_limit_help ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="domains_limit"><?= language()->admin_plans->plan->domains_limit ?></label>
                    <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->domains_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->domains_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="track_links_retention"><?= language()->admin_plans->plan->track_links_retention ?></label>
                    <input type="number" id="track_links_retention" name="track_links_retention" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->track_links_retention ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->track_links_retention_help ?></small>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->additional_global_domains ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="additional_global_domains"><?= language()->admin_plans->plan->additional_global_domains ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->additional_global_domains_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_url ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_url"><?= language()->admin_plans->plan->custom_url ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_url_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->deep_links ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="deep_links"><?= language()->admin_plans->plan->deep_links ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->deep_links_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="no_ads"><?= language()->admin_plans->plan->no_ads ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->no_ads_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->removable_branding ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="removable_branding"><?= language()->admin_plans->plan->removable_branding ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->removable_branding_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_branding ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_branding"><?= language()->admin_plans->plan->custom_branding ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_branding_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_colored_links ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_colored_links"><?= language()->admin_plans->plan->custom_colored_links ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_colored_links_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->statistics ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="statistics"><?= language()->admin_plans->plan->statistics ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->statistics_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_backgrounds ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_backgrounds"><?= language()->admin_plans->plan->custom_backgrounds ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_backgrounds_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->verified ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="verified"><?= language()->admin_plans->plan->verified ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->verified_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="temporary_url_is_enabled" name="temporary_url_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->temporary_url_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="temporary_url_is_enabled"><?= language()->admin_plans->plan->temporary_url_is_enabled ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->temporary_url_is_enabled_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->seo ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="seo"><?= language()->admin_plans->plan->seo ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->seo_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->utm ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="utm"><?= language()->admin_plans->plan->utm ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->utm_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="socials" name="socials" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->socials ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="socials"><?= language()->admin_plans->plan->socials ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->socials_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->fonts ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="fonts"><?= language()->admin_plans->plan->fonts ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->fonts_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="password" name="password" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->password ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="password"><?= language()->admin_plans->plan->password ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->password_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="sensitive_content" name="sensitive_content" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->sensitive_content ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="sensitive_content"><?= language()->admin_plans->plan->sensitive_content ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->sensitive_content_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="leap_link" name="leap_link" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->leap_link ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="leap_link"><?= language()->admin_plans->plan->leap_link ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->leap_link_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="api_is_enabled" name="api_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->api_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="api_is_enabled"><?= language()->admin_plans->plan->api_is_enabled ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->api_is_enabled_help ?></small></div>
                </div>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="custom-control custom-switch my-3">
                        <input id="affiliate_is_enabled" name="affiliate_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->affiliate_is_enabled ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="affiliate_is_enabled"><?= language()->admin_plans->plan->affiliate_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->affiliate_is_enabled_help ?></small></div>
                    </div>
                <?php endif ?>

                <h3 class="h5 mt-4"><?= language()->admin_plans->plan->enabled_biolink_blocks ?></h3>
                <p class="text-muted"><?= language()->admin_plans->plan->enabled_biolink_blocks_help ?></p>

                <div class="row">
                    <?php foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key => $value): ?>
                        <div class="col-6 mb-3">
                            <div class="custom-control custom-switch">
                                <input id="enabled_biolink_blocks_<?= $key ?>" name="enabled_biolink_blocks[]" value="<?= $key ?>" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->enabled_biolink_blocks->{$key} ? 'checked="checked"' : null ?>>
                                <label class="custom-control-label" for="enabled_biolink_blocks_<?= $key ?>"><?= language()->link->biolink->blocks->{mb_strtolower($key)} ?></label>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= language()->admin_user_update->change_password->header ?></h2>
            <p class="text-muted"><?= language()->admin_user_update->change_password->subheader ?></p>

            <div class="form-group">
                <label for="new_password"><?= language()->admin_user_update->change_password->new_password ?></label>
                <input id="new_password" type="password" name="new_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <div class="form-group">
                <label for="repeat_password"><?= language()->admin_user_update->change_password->repeat_password ?></label>
                <input id="repeat_password" type="password" name="repeat_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>

<script>
    'use strict';

    moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

    let check_plan_id = () => {
        let selected_plan_id = document.querySelector('[name="plan_id"]').value;

        if(selected_plan_id == 'free') {
            document.querySelector('#plan_expiration_date_container').style.display = 'none';
        } else {
            document.querySelector('#plan_expiration_date_container').style.display = 'block';
        }

        if(selected_plan_id == 'custom') {
            document.querySelector('#plan_settings').style.display = 'block';
        } else {
            document.querySelector('#plan_settings').style.display = 'none';
        }
    };

    check_plan_id();

    /* Dont show expiration date when the chosen plan is the free one */
    document.querySelector('[name="plan_id"]').addEventListener('change', check_plan_id);

    /* Check for expiration date to show a warning if expired */
    let check_plan_expiration_date = () => {
        let plan_expiration_date = document.querySelector('[name="plan_expiration_date"]');

        let plan_expiration_date_object = new Date(plan_expiration_date.value);
        let today_date_object = new Date();

        if(plan_expiration_date_object < today_date_object) {
            plan_expiration_date.classList.add('is-invalid');
        } else {
            plan_expiration_date.classList.remove('is-invalid');
        }
    };

    check_plan_expiration_date();
    document.querySelector('[name="plan_expiration_date"]').addEventListener('change', check_plan_expiration_date);

    /* Daterangepicker */
    $('[name="plan_expiration_date"]').daterangepicker({
        startDate: <?= json_encode($data->user->plan_expiration_date) ?>,
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {
        check_plan_expiration_date()
    });

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
