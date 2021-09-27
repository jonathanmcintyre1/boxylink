<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= sprintf(language()->admin_plan_update->header, $data->plan->name) ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => $data->plan->plan_id]) ?>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
            <input type="hidden" name="type" value="update" />

            <div class="form-group">
                <label for="name"><?= language()->admin_plans->main->name ?></label>
                <input type="text" id="name" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->plan->name ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="description"><?= language()->admin_plans->main->description ?></label>
                <input type="text" id="description" name="description" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('description') ? 'is-invalid' : null ?>" value="<?= $data->plan->description ?>" />
                <?= \Altum\Alerts::output_field_error('description') ?>
            </div>

            <?php if(in_array($data->plan_id, ['free', 'custom'])): ?>
                <div class="form-group">
                    <label for="price"><?= language()->admin_plans->main->price ?></label>
                    <input type="text" id="price" name="price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('price') ? 'is-invalid' : null ?>" value="<?= $data->plan->price ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('price') ?>
                </div>
            <?php endif ?>

            <?php if($data->plan_id == 'custom'): ?>
                <div class="form-group">
                    <label for="custom_button_url"><?= language()->admin_plans->main->custom_button_url ?></label>
                    <input type="text" id="custom_button_url" name="custom_button_url" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('custom_button_url') ? 'is-invalid' : null ?>" value="<?= $data->plan->custom_button_url ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('custom_button_url') ?>
                </div>
            <?php endif ?>

            <?php if(is_numeric($data->plan_id)): ?>
                <div class="form-group">
                    <label for="order"><?= language()->admin_plans->main->order ?></label>
                    <input id="order" type="number" min="0"  name="order" class="form-control form-control-lg" value="<?= $data->plan->order ?>" />
                </div>

                <div class="form-group">
                    <label for="trial_days"><?= language()->admin_plans->main->trial_days ?></label>
                    <input id="trial_days" type="number" min="0" name="trial_days" class="form-control form-control-lg" value="<?= $data->plan->trial_days ?>" />
                    <div><small class="form-text text-muted"><?= language()->admin_plans->main->trial_days_help ?></small></div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-xl-4">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="monthly_price"><?= language()->admin_plans->main->monthly_price ?> <small class="form-text text-muted"><?= settings()->payment->currency ?></small></label>
                                <input type="text" id="monthly_price" name="monthly_price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('monthly_price') ? 'is-invalid' : null ?>" value="<?= $data->plan->monthly_price ?>" required="required" />
                                <?= \Altum\Alerts::output_field_error('monthly_price') ?>
                                <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->price_help, language()->admin_plans->main->monthly_price) ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-xl-4">
                        <div class="form-group">
                            <label for="annual_price"><?= language()->admin_plans->main->annual_price ?> <small class="form-text text-muted"><?= settings()->payment->currency ?></small></label>
                            <input type="text" id="annual_price" name="annual_price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('annual_price') ? 'is-invalid' : null ?>" value="<?= $data->plan->annual_price ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('annual_price') ?>
                            <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->price_help, language()->admin_plans->main->annual_price) ?></small>
                        </div>
                    </div>

                    <div class="col-sm-12 col-xl-4">
                        <div class="form-group">
                            <label for="lifetime_price"><?= language()->admin_plans->main->lifetime_price ?> <small class="form-text text-muted"><?= settings()->payment->currency ?></small></label>
                            <input type="text" id="lifetime_price" name="lifetime_price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('lifetime_price') ? 'is-invalid' : null ?>" value="<?= $data->plan->lifetime_price ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('lifetime_price') ?>
                            <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->price_help, language()->admin_plans->main->lifetime_price) ?></small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <span><?= language()->admin_plans->main->taxes_ids ?></span>
                    <div><small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->taxes_ids_help, '<a href="' . url('admin/taxes') .'">', '</a>') ?></small></div>
                </div>

                <?php if($data->taxes): ?>
                    <div class="row">
                        <?php foreach($data->taxes as $row): ?>
                            <div class="col-12 col-xl-6">
                                <div class="custom-control custom-switch my-3">
                                    <input id="<?= 'tax_id_' . $row->tax_id ?>" name="taxes_ids[<?= $row->tax_id ?>]" type="checkbox" class="custom-control-input" <?= in_array($row->tax_id, $data->plan->taxes_ids) ? 'checked="checked"' : null ?>>
                                    <label class="custom-control-label" for="<?= 'tax_id_' . $row->tax_id ?>"><?= $row->internal_name ?></label>
                                    <div><span><small><?= $row->name ?></small> - <small class="text-muted"><?= $row->description ?></small></span></div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

            <?php endif ?>

            <div class="form-group">
                <label for="color"><?= language()->admin_plans->main->color ?></label>
                <input type="text" id="color" name="color" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('color') ? 'is-invalid' : null ?>" value="<?= $data->plan->color ?>" />
                <?= \Altum\Alerts::output_field_error('color') ?>
                <small class="form-text text-muted"><?= language()->admin_plans->main->color_help ?></small>
            </div>

            <div class="form-group">
                <label for="status"><?= language()->admin_plans->main->status ?></label>
                <select id="status" name="status" class="form-control form-control-lg">
                    <option value="1" <?= $data->plan->status == 1 ? 'selected="selected"' : null ?>><?= language()->global->active ?></option>
                    <option value="0" <?= $data->plan->status == 0 ? 'selected="selected"' : null ?> <?= $data->plan->plan_id == 'custom' ? 'disabled="disabled"' : null ?>><?= language()->global->disabled ?></option>
                    <option value="2" <?= $data->plan->status == 2 ? 'selected="selected"' : null ?>><?= language()->global->hidden ?></option>
                </select>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= language()->admin_plans->plan->header ?></h2>

            <div>
                <div class="form-group">
                    <label for="projects_limit"><?= language()->admin_plans->plan->projects_limit ?></label>
                    <input type="number" id="projects_limit" name="projects_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->projects_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->projects_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="pixels_limit"><?= language()->admin_plans->plan->pixels_limit ?></label>
                    <input type="number" id="pixels_limit" name="pixels_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->pixels_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->pixels_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="biolinks_limit"><?= language()->admin_plans->plan->biolinks_limit ?></label>
                    <input type="number" id="biolinks_limit" name="biolinks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->biolinks_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->biolinks_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="biolink_blocks_limit"><?= language()->admin_plans->plan->biolink_blocks_limit ?></label>
                    <input type="number" id="biolink_blocks_limit" name="biolink_blocks_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->biolink_blocks_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->biolink_blocks_limit_help ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->shortener_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="links_limit"><?= language()->admin_plans->plan->links_limit ?></label>
                    <input type="number" id="links_limit" name="links_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->links_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->links_limit_help ?></small>
                </div>

                <div class="form-group" <?= !settings()->links->domains_is_enabled ? 'style="display: none"' : null ?>>
                    <label for="domains_limit"><?= language()->admin_plans->plan->domains_limit ?></label>
                    <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->domains_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->domains_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="track_links_retention"><?= language()->admin_plans->plan->track_links_retention ?></label>
                    <input type="number" id="track_links_retention" name="track_links_retention" min="-1" class="form-control form-control-lg" value="<?= $data->plan->settings->track_links_retention ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->track_links_retention_help ?></small>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="additional_global_domains" name="additional_global_domains" type="checkbox" class="custom-control-input" <?= $data->plan->settings->additional_global_domains ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="additional_global_domains"><?= language()->admin_plans->plan->additional_global_domains ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->additional_global_domains_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_url" name="custom_url" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_url ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_url"><?= language()->admin_plans->plan->custom_url ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_url_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="deep_links" name="deep_links" type="checkbox" class="custom-control-input" <?= $data->plan->settings->deep_links ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="deep_links"><?= language()->admin_plans->plan->deep_links ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->deep_links_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="no_ads"><?= language()->admin_plans->plan->no_ads ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->no_ads_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="removable_branding" name="removable_branding" type="checkbox" class="custom-control-input" <?= $data->plan->settings->removable_branding ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="removable_branding"><?= language()->admin_plans->plan->removable_branding ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->removable_branding_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_branding" name="custom_branding" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_branding ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_branding"><?= language()->admin_plans->plan->custom_branding ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_branding_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_colored_links" name="custom_colored_links" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_colored_links ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_colored_links"><?= language()->admin_plans->plan->custom_colored_links ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_colored_links_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="statistics" name="statistics" type="checkbox" class="custom-control-input" <?= $data->plan->settings->statistics ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="statistics"><?= language()->admin_plans->plan->statistics ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->statistics_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="custom_backgrounds" name="custom_backgrounds" type="checkbox" class="custom-control-input" <?= $data->plan->settings->custom_backgrounds ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_backgrounds"><?= language()->admin_plans->plan->custom_backgrounds ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->custom_backgrounds_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="verified" name="verified" type="checkbox" class="custom-control-input" <?= $data->plan->settings->verified ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="verified"><?= language()->admin_plans->plan->verified ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->verified_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="temporary_url_is_enabled" name="temporary_url_is_enabled" type="checkbox" class="custom-control-input" <?= $data->plan->settings->temporary_url_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="temporary_url_is_enabled"><?= language()->admin_plans->plan->temporary_url_is_enabled ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->temporary_url_is_enabled_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="seo" name="seo" type="checkbox" class="custom-control-input" <?= $data->plan->settings->seo ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="seo"><?= language()->admin_plans->plan->seo ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->seo_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="utm" name="utm" type="checkbox" class="custom-control-input" <?= $data->plan->settings->utm ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="utm"><?= language()->admin_plans->plan->utm ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->utm_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="socials" name="socials" type="checkbox" class="custom-control-input" <?= $data->plan->settings->socials ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="socials"><?= language()->admin_plans->plan->socials ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->socials_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="fonts" name="fonts" type="checkbox" class="custom-control-input" <?= $data->plan->settings->fonts ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="fonts"><?= language()->admin_plans->plan->fonts ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->fonts_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="password" name="password" type="checkbox" class="custom-control-input" <?= $data->plan->settings->password ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="password"><?= language()->admin_plans->plan->password ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->password_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="sensitive_content" name="sensitive_content" type="checkbox" class="custom-control-input" <?= $data->plan->settings->sensitive_content ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="sensitive_content"><?= language()->admin_plans->plan->sensitive_content ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->sensitive_content_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="leap_link" name="leap_link" type="checkbox" class="custom-control-input" <?= $data->plan->settings->leap_link ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="leap_link"><?= language()->admin_plans->plan->leap_link ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->leap_link_help ?></small></div>
                </div>

                <div class="custom-control custom-switch mb-3">
                    <input id="api_is_enabled" name="api_is_enabled" type="checkbox" class="custom-control-input" <?= $data->plan->settings->api_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="api_is_enabled"><?= language()->admin_plans->plan->api_is_enabled ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->api_is_enabled_help ?></small></div>
                </div>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="custom-control custom-switch my-3">
                        <input id="affiliate_is_enabled" name="affiliate_is_enabled" type="checkbox" class="custom-control-input" <?= $data->plan->settings->affiliate_is_enabled ? 'checked="checked"' : null ?>>
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
                                <input id="enabled_biolink_blocks_<?= $key ?>" name="enabled_biolink_blocks[]" value="<?= $key ?>" type="checkbox" class="custom-control-input" <?= $data->plan->settings->enabled_biolink_blocks->{$key} ? 'checked="checked"' : null ?>>
                                <label class="custom-control-label" for="enabled_biolink_blocks_<?= $key ?>"><?= language()->link->biolink->blocks->{mb_strtolower($key)} ?></label>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <?php if($data->plan_id != 'custom'): ?>
                <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
                <button type="submit" name="submit_update_users_plan_settings" class="btn btn-lg btn-block btn-outline-primary mt-2"><?= language()->admin_plan_update->update_users_plan_settings->button ?></button>
            <?php else: ?>
                <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
            <?php endif ?>
        </form>

    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/plans/plan_delete_modal.php'), 'modals'); ?>
