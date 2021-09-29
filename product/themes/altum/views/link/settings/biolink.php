<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="row">
    <div class="col-12 col-lg-6">

        <div class="d-flex justify-content-between">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= !isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'settings') ? 'active' : null ?>" id="settings-tab" data-toggle="pill" href="#settings" role="tab" aria-controls="settings" aria-selected="true"><?= language()->link->header->settings_tab ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isset($_GET['tab']) && $_GET['tab'] == 'links'? 'active' : null ?>" id="links-tab" data-toggle="pill" href="#biolink_blocks" role="tab" aria-controls="links" aria-selected="false"><?= language()->link->header->links_tab ?></a>
                </li>
            </ul>

            <div>
                <button type="button" data-toggle="modal" data-target="#biolink_link_create_modal" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->links->create_biolink_block ?></button>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade <?= !isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == 'settings') ? 'show active' : null ?>" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <div class="card">
                    <div class="card-body">

                        <form name="update_biolink" action="" method="post" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
                            <input type="hidden" name="request_type" value="update" />
                            <input type="hidden" name="type" value="biolink" />
                            <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />

                            <div class="notification-container"></div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= language()->link->settings->url ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <?php if(count($data->domains)): ?>
                                            <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                                <?php if(settings()->links->main_domain_is_enabled || \Altum\Middlewares\Authentication::is_admin()): ?>
                                                    <option value="" <?= $data->link->domain ? 'selected="selected"' : null ?>><?= SITE_URL ?></option>
                                                <?php endif ?>

                                                <?php foreach($data->domains as $row): ?>
                                                    <option value="<?= $row->domain_id ?>" <?= $data->link->domain && $row->domain_id == $data->link->domain->domain_id ? 'selected="selected"' : null ?>><?= $row->url ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        <?php else: ?>
                                            <span class="input-group-text"><?= SITE_URL ?></span>
                                        <?php endif ?>
                                    </div>
                                    <input
                                            type="text"
                                            class="form-control"
                                            name="url"
                                            placeholder="<?= language()->link->settings->url_placeholder ?>"
                                            value="<?= $data->link->url ?>"
                                        <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                        <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>
                                    />
                                </div>
                                <small class="form-text text-muted"><?= language()->link->settings->url_help ?></small>
                            </div>

                            <div class="form-group">
                                <label for="settings_project_id">
                                    <i class="fa fa-fw fa-project-diagram fa-sm mr-1"></i> <?= language()->link->settings->project_id ?>
                                    <a href="<?= url('projects') ?>" target="_blank" class="ml-3 small"><?= language()->projects->create ?></a>
                                </label>
                                <select id="settings_project_id" name="project_id" class="form-control">
                                    <option value=""><?= language()->link->settings->project_id_null ?></option>
                                    <?php foreach($data->projects as $row): ?>
                                        <option value="<?= $row->project_id ?>" <?= $data->link->project_id == $row->project_id ? 'selected="selected"' : null?>><?= $row->name ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="settings_background_type"><i class="fa fa-fw fa-fill fa-sm mr-1"></i> <?= language()->link->settings->background_type ?></label>
                                <select id="settings_background_type" name="background_type" class="form-control">
                                    <?php foreach($biolink_backgrounds as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $data->link->settings->background_type == $key ? 'selected="selected"' : null?>><?= language()->link->settings->{'background_type_' . $key} ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div id="background_type_preset" class="row">
                                <?php foreach($biolink_backgrounds['preset'] as $key): ?>
                                    <label for="settings_background_type_preset_<?= $key ?>" class="m-0 col-3 mb-3">
                                        <input type="radio" name="background" value="<?= $key ?>" id="settings_background_type_preset_<?= $key ?>" class="d-none" <?= $data->link->settings->background == $key ? 'checked="checked"' : null ?>/>

                                        <div class="link-background-type-preset link-body-background-<?= $key ?>"></div>
                                    </label>
                                <?php endforeach ?>
                            </div>

                            <div <?= $this->user->plan_settings->custom_backgrounds ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                <div class="<?= $this->user->plan_settings->custom_backgrounds ? null : 'container-disabled' ?>">
                                    <div id="background_type_gradient">
                                        <div class="form-group">
                                            <label for="settings_background_type_gradient_color_one"><?= language()->link->settings->background_type_gradient_color_one ?></label>
                                            <input type="hidden" id="settings_background_type_gradient_color_one" name="background[]" class="form-control" value="<?= $data->link->settings->background->color_one ?? '#000' ?>" />
                                            <div id="settings_background_type_gradient_color_one_pickr"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="settings_background_type_gradient_color_two"><?= language()->link->settings->background_type_gradient_color_two ?></label>
                                            <input type="hidden" id="settings_background_type_gradient_color_two" name="background[]" class="form-control" value="<?= $data->link->settings->background->color_two ?? '#000' ?>" />
                                            <div id="settings_background_type_gradient_color_two_pickr"></div>
                                        </div>
                                    </div>

                                    <div id="background_type_color">
                                        <div class="form-group">
                                            <label for="settings_background_type_color"><?= language()->link->settings->background_type_color ?></label>
                                            <input type="hidden" id="settings_background_type_color" name="background" class="form-control" value="<?= is_string($data->link->settings->background) ? $data->link->settings->background : '#000' ?>" />
                                            <div id="settings_background_type_color_pickr"></div>
                                        </div>
                                    </div>

                                    <div id="background_type_image">
                                        <div class="form-group">
                                            <?php if(is_string($data->link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $data->link->settings->background)): ?>
                                                <img id="background_type_image_preview" src="<?= UPLOADS_FULL_URL . 'backgrounds/' . $data->link->settings->background ?>" data-default-src="<?= UPLOADS_FULL_URL . 'backgrounds/' . $data->link->settings->background ?>" class="link-background-type-image img-fluid" />
                                            <?php endif ?>
                                            <input id="background_type_image_input" type="file" name="background" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= language()->link->settings->favicon ?></label>
                                <div data-favicon-container class="<?= !empty($data->link->settings->favicon) ? null : 'd-none' ?>">
                                    <div class="row">
                                        <div class="m-1 col-6 col-xl-3">
                                            <img src="<?= $data->link->settings->favicon ? UPLOADS_FULL_URL . 'favicons/' . $data->link->settings->favicon : null ?>" class="img-fluid rounded <?= !empty($data->link->settings->favicon) ? null : 'd-none' ?>" loading="lazy" />
                                        </div>
                                    </div>
                                    <div class="custom-control custom-checkbox my-2">
                                        <input id="favicon_remove" name="favicon_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#favicon').classList.add('d-none') : document.querySelector('#favicon').classList.remove('d-none')">
                                        <label class="custom-control-label" for="favicon_remove">
                                            <span class="text-muted"><?= language()->global->delete_file ?></span>
                                        </label>
                                    </div>
                                </div>
                                <input id="favicon" type="file" name="favicon" accept=".gif, .png, .ico" class="form-control-file" />
                                <small class="form-text text-muted"><?= language()->link->settings->favicon_help ?></small>
                            </div>

                            <div <?= $this->user->plan_settings->leap_link ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                <div class="<?= $this->user->plan_settings->leap_link ? null : 'container-disabled' ?>">
                                    <div class="form-group">
                                        <label for="leap_link"><i class="fa fa-fw fa-forward fa-sm mr-1"></i> <?= language()->link->settings->leap_link ?></label>
                                        <input id="leap_link" type="url" class="form-control" name="leap_link" value="<?= $data->link->settings->leap_link ?>" <?= !$this->user->plan_settings->leap_link ? 'disabled="disabled"': null ?> autocomplete="off" />
                                        <small class="form-text text-muted"><?= language()->link->settings->leap_link_help ?></small>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#verified_container" aria-expanded="false" aria-controls="verified_container">
                                <?= language()->link->settings->verified_header ?>
                            </button>

                            <div class="collapse" id="verified_container">
                                <div <?= $this->user->plan_settings->verified ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->verified ? null : 'container-disabled' ?>">
                                        <div class="custom-control custom-switch mr-3 mb-3">
                                            <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="display_verified"
                                                    name="display_verified"
                                                <?= !$this->user->plan_settings->verified ? 'disabled="disabled"': null ?>
                                                <?= $data->link->settings->display_verified ? 'checked="checked"' : null ?>
                                            >
                                            <label class="custom-control-label clickable" for="display_verified"><?= language()->link->settings->display_verified ?></label>
                                        </div>

                                        <div class="form-group">
                                            <label for="verified_location"><?= language()->link->settings->verified_location ?></label>
                                            <select id="verified_location" name="verified_location" class="form-control">
                                                <option value="top" <?= $data->link->settings->verified_location == 'top' ? 'selected="selected"' : null?>><?= language()->link->settings->verified_location_top ?></option>
                                                <option value="bottom" <?= $data->link->settings->verified_location == 'bottom' ? 'selected="selected"' : null?>><?= language()->link->settings->verified_location_bottom ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#branding_container" aria-expanded="false" aria-controls="branding_container">
                                <?= language()->link->settings->branding_header ?>
                            </button>

                            <div class="collapse" id="branding_container">
                                <div <?= $this->user->plan_settings->removable_branding ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->removable_branding ? null : 'container-disabled' ?>">
                                        <div class="custom-control custom-switch mr-3 mb-3">
                                            <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="display_branding"
                                                    name="display_branding"
                                                <?= !$this->user->plan_settings->removable_branding ? 'disabled="disabled"': null ?>
                                                <?= $data->link->settings->display_branding ? 'checked="checked"' : null ?>
                                            >
                                            <label class="custom-control-label clickable" for="display_branding"><?= language()->link->settings->display_branding ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div <?= $this->user->plan_settings->custom_branding ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->custom_branding ? null : 'container-disabled' ?>">
                                        <div class="form-group">
                                            <label><i class="fa fa-fw fa-random fa-sm mr-1"></i> <?= language()->link->settings->branding->name ?></label>
                                            <input id="branding_name" type="text" class="form-control" name="branding_name" value="<?= $data->link->settings->branding->name ?? '' ?>" />
                                            <small class="form-text text-muted"><?= language()->link->settings->branding->name_help ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= language()->link->settings->branding->url ?></label>
                                            <input id="branding_url" type="text" class="form-control" name="branding_url" value="<?= $data->link->settings->branding->url ?? '' ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="settings_text_color"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= language()->link->settings->text_color ?></label>
                                    <input type="hidden" id="settings_text_color" name="text_color" class="form-control" value="<?= $data->link->settings->text_color ?>" required="required" />
                                    <div id="settings_text_color_pickr"></div>
                                </div>
                            </div>

                            <?php if(count($data->pixels)): ?>
                                <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#pixels_container" aria-expanded="false" aria-controls="pixels_container">
                                    <?= language()->link->settings->pixels_header ?>
                                </button>

                                <div class="collapse" id="pixels_container">
                                    <div class="mb-3">
                                        <div><i class="fa fa-fw fa-sm fa-adjust text-muted mr-1"></i><?= language()->link->settings->pixels_ids ?> <a href="<?= url('pixels') ?>" target="_blank" class="ml-3 small"><?= language()->pixels->create ?></a></div>

                                        <div class="row">
                                            <?php foreach($data->pixels as $pixel): ?>
                                                <div class="col-12 col-lg-6">
                                                    <div class="custom-control custom-checkbox my-2">
                                                        <input id="pixel_id_<?= $pixel->pixel_id ?>" name="pixels_ids[]" value="<?= $pixel->pixel_id ?>" type="checkbox" class="custom-control-input" <?= in_array($pixel->pixel_id, $data->link->pixels_ids) ? 'checked="checked"' : null ?>>
                                                        <label class="custom-control-label d-flex align-items-center" for="pixel_id_<?= $pixel->pixel_id ?>">
                                                            <span class="mr-1"><?= $pixel->name ?></span>
                                                            <small class="badge badge-light badge-pill"><?= language()->pixels->pixels->{$pixel->type} ?></small>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#seo_container" aria-expanded="false" aria-controls="seo_container">
                                <?= language()->link->settings->seo_header ?>
                            </button>

                            <div class="collapse" id="seo_container">
                                <div <?= $this->user->plan_settings->seo ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->seo ? null : 'container-disabled' ?>">
                                        <div class="custom-control custom-switch mb-3">
                                            <input id="seo_block" name="seo_block" type="checkbox" class="custom-control-input" <?= $data->link->settings->seo->block ? 'checked="checked"' : null ?>>
                                            <label class="custom-control-label" for="seo_block"><?= language()->link->settings->seo_block ?></label>
                                            <small class="form-text text-muted"><?= language()->link->settings->seo_block_help ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fa fa-fw fa-heading fa-sm mr-1"></i> <?= language()->link->settings->seo_title ?></label>
                                            <input id="seo_title" type="text" class="form-control" name="seo_title" value="<?= $data->link->settings->seo->title ?? '' ?>" />
                                            <small class="form-text text-muted"><?= language()->link->settings->seo_title_help ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->link->settings->seo_meta_description ?></label>
                                            <input id="seo_meta_description" type="text" class="form-control" name="seo_meta_description" value="<?= $data->link->settings->seo->meta_description ?? '' ?>" />
                                            <small class="form-text text-muted"><?= language()->link->settings->seo_meta_description_help ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= language()->link->settings->seo_image ?></label>
                                            <div data-seo-image-container class="<?= !empty($data->link->settings->seo->image) ? null : 'd-none' ?>">
                                                <div class="row">
                                                    <div class="m-1 col-6 col-xl-3">
                                                        <img src="<?= $data->link->settings->seo->image ? UPLOADS_FULL_URL . 'block_images/' . $data->link->settings->seo->image : null ?>" class="img-fluid rounded <?= !empty($data->link->settings->seo->image) ? null : 'd-none' ?>" loading="lazy" />
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-checkbox my-2">
                                                    <input id="seo_image_remove" name="seo_image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#seo_image').classList.add('d-none') : document.querySelector('#seo_image').classList.remove('d-none')">
                                                    <label class="custom-control-label" for="seo_image_remove">
                                                        <span class="text-muted"><?= language()->global->delete_file ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <input id="seo_image" type="file" name="seo_image" accept=".gif, .png, .jpg, .jpeg" class="form-control-file" />
                                            <small class="form-text text-muted"><?= language()->link->settings->seo_image_help ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#utm_container" aria-expanded="false" aria-controls="utm_container">
                                <?= language()->link->settings->utm_header ?>
                            </button>

                            <div class="collapse" id="utm_container">
                                <div <?= $this->user->plan_settings->utm ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->utm ? null : 'container-disabled' ?>">
                                        <small class="form-text text-muted"><?= language()->link->settings->utm_campaign ?></small>

                                        <div class="form-group">
                                            <label><?= language()->link->settings->utm_medium ?></label>
                                            <input id="utm_medium" type="text" class="form-control" name="utm_medium" value="<?= $data->link->settings->utm->medium ?? '' ?>" />
                                        </div>

                                        <div class="form-group">
                                            <label><?= language()->link->settings->utm_source ?></label>
                                            <input id="utm_source" type="text" class="form-control" name="utm_source" value="<?= $data->link->settings->utm->source ?? '' ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#fonts_container" aria-expanded="false" aria-controls="fonts_container">
                                <?= language()->link->settings->fonts_header ?>
                            </button>

                            <div class="collapse" id="fonts_container">
                                <div <?= $this->user->plan_settings->fonts ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->fonts ? null : 'container-disabled' ?>">
                                        <?php $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php'; ?>

                                        <div class="form-group">
                                            <label for="settings_font"><i class="fa fa-fw fa-pen-nib fa-sm mr-1"></i> <?= language()->link->settings->font ?></label>
                                            <select id="settings_font" name="font" class="form-control">
                                                <?php foreach($biolink_fonts as $key => $value): ?>
                                                    <option value="<?= $key ?>" <?= $data->link->settings->font == $key ? 'selected="selected"' : null?>><?= $value['name'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#protection_container" aria-expanded="false" aria-controls="protection_container">
                                <?= language()->link->settings->protection_header ?>
                            </button>

                            <div class="collapse" id="protection_container">

                                <div <?= $this->user->plan_settings->password ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->password ? null : 'container-disabled' ?>">
                                        <div class="form-group">
                                            <label for="qweasdzxc"><i class="fa fa-fw fa-key fa-sm mr-1"></i> <?= language()->link->settings->password ?></label>
                                            <input id="qweasdzxc" type="password" class="form-control" name="qweasdzxc" value="<?= $data->link->settings->password ?>" autocomplete="new-password" <?= !$this->user->plan_settings->password ? 'disabled="disabled"': null ?> />
                                            <small class="form-text text-muted"><?= language()->link->settings->password_help ?></small>
                                        </div>
                                    </div>
                                </div>

                                <div <?= $this->user->plan_settings->sensitive_content ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                                    <div class="<?= $this->user->plan_settings->sensitive_content ? null : 'container-disabled' ?>">
                                        <div class="custom-control custom-switch mr-3 mb-3">
                                            <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="sensitive_content"
                                                    name="sensitive_content"
                                                <?= !$this->user->plan_settings->sensitive_content ? 'disabled="disabled"': null ?>
                                                <?= $data->link->settings->sensitive_content ? 'checked="checked"' : null ?>
                                            >
                                            <label class="custom-control-label clickable" for="sensitive_content"><?= language()->link->settings->sensitive_content ?></label>
                                            <small class="form-text text-muted"><?= language()->link->settings->sensitive_content_help ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade <?= isset($_GET['tab']) && $_GET['tab'] == 'links'? 'show active' : null ?>" id="biolink_blocks" role="tabpanel" aria-labelledby="links-tab">

                <?php if($data->link_links_result->num_rows): ?>
                    <?php while($row = $data->link_links_result->fetch_object()): ?>

                        <?php $row->settings = json_decode($row->settings) ?>

                        <div class="biolink_block card <?= $row->is_enabled ? null : 'custom-row-inactive' ?> my-4" data-biolink-block-id="<?= $row->biolink_block_id ?>">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="custom-row-side-controller">
                                        <span data-toggle="tooltip" title="<?= language()->link->biolink_blocks->link_sort ?>">
                                            <i class="fa fa-fw fa-bars text-muted custom-row-side-controller-grab drag"></i>
                                        </span>
                                    </div>

                                    <div class="col-1 mr-2 p-0 d-none d-lg-block">
                                        <span class="fa-stack fa-1x" data-toggle="tooltip" title="<?= language()->link->biolink->blocks->{$row->type} ?>">
                                            <i class="fa fa-circle fa-stack-2x" style="color: <?= $data->biolink_blocks[$row->type]['color'] ?>"></i>
                                            <i class="fas <?= $data->biolink_blocks[$row->type]['icon'] ?> fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </div>

                                    <div class="col-6 col-md-6">
                                        <div class="d-flex flex-column">
                                            <a href="#"
                                               data-toggle="collapse"
                                               data-target="#biolink_block_expanded_content<?= $row->biolink_block_id ?>"
                                               aria-expanded="false"
                                               aria-controls="biolink_block_expanded_content<?= $row->biolink_block_id ?>"
                                            >
                                                <strong><?= $data->biolink_blocks[$row->type]['display_dynamic_name'] ? $row->settings->{$data->biolink_blocks[$row->type]['display_dynamic_name']} : language()->link->biolink->blocks->{$row->type} ?></strong>
                                            </a>

                                            <span class="d-flex align-items-center">
                                            <?php if(!empty($row->location_url)): ?>
                                                <?php if($parsed_host = parse_url($row->location_url, PHP_URL_HOST)): ?>
                                                    <img src="https://external-content.duckduckgo.com/ip3/<?= $parsed_host ?>.ico" class="img-fluid icon-favicon mr-1" />
                                                <?php endif ?>

                                                <span class="d-inline-block text-truncate">
                                                    <a href="<?= $row->location_url ?>" class="text-muted" title="<?= $row->location_url ?>" target="_blank" rel="noreferrer"><?= $row->location_url ?></a>
                                                </span>
                                            <?php elseif(!empty($row->url)): ?>
                                                <img src="https://external-content.duckduckgo.com/ip3/<?= parse_url(url($row->url))['host'] ?>.ico" class="img-fluid icon-favicon mr-1" />

                                                <span class="d-inline-block text-truncate">
                                                    <a href="<?= url($row->url) ?>" class="text-muted" title="<?= url($row->url) ?>" target="_blank" rel="noreferrer"><?= url($row->url) ?></a>
                                                </span>
                                            <?php endif ?>
                                            </span>

                                        </div>
                                    </div>

                                    <div class="d-none d-md-flex col-md-2">
                                        <?php if($data->biolink_blocks[$row->type]['has_statistics']): ?>
                                            <a href="<?= url('biolink-block/' . $row->biolink_block_id . '/statistics') ?>">
                                                <span data-toggle="tooltip" title="<?= language()->links->clicks ?>" class="badge badge-light"><i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= nr($row->clicks) ?></span>
                                            </a>
                                        <?php endif ?>
                                    </div>

                                    <div class="col-2 col-md-1">
                                        <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= language()->link->biolink_blocks->is_enabled_tooltip ?>">
                                            <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="biolink_block_is_enabled_<?= $row->biolink_block_id ?>"
                                                    data-row-id="<?= $row->biolink_block_id ?>"
                                                <?= $row->is_enabled ? 'checked="checked"' : null ?>
                                            >
                                            <label class="custom-control-label clickable" for="biolink_block_is_enabled_<?= $row->biolink_block_id ?>"></label>
                                        </div>
                                    </div>

                                    <div class="col-3 col-md-2 d-flex justify-content-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown">
                                                <i class="fa fa-fw fa-ellipsis-v"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#"
                                                   class="dropdown-item"
                                                   data-toggle="collapse"
                                                   data-target="#biolink_block_expanded_content<?= $row->biolink_block_id ?>"
                                                   aria-expanded="false"
                                                   aria-controls="biolink_block_expanded_content<?= $row->biolink_block_id ?>"
                                                >
                                                    <i class="fa fa-fw fa-pencil-alt"></i> <?= language()->global->edit ?>
                                                </a>

                                                <?php if(!in_array($row->type, ['mail', 'text', 'youtube', 'vimeo', 'tiktok', 'twitch', 'spotify', 'soundcloud', 'applemusic', 'tidal', 'anchor', 'twitter_tweet', 'instagram_media', 'rss_feed', 'custom_html', 'vcard', 'divider'])): ?>
                                                    <a href="<?= url('link/' . $row->biolink_block_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-chart-bar"></i> <?= language()->link->statistics->link ?></a>
                                                <?php endif ?>

                                                <?php if($row->type == 'link'): ?>
                                                    <a href="#" class="dropdown-item" data-duplicate="true" data-row-id="<?= $row->biolink_block_id ?>"><i class="fa fa-fw fa-copy"></i> <?= language()->link->biolink_blocks->duplicate ?></a>
                                                <?php endif ?>

                                                <a href="#" data-toggle="modal" data-target="#biolink_block_delete" class="dropdown-item" data-biolink-block-id="<?= $row->biolink_block_id ?>"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="collapse mt-3" id="biolink_block_expanded_content<?= $row->biolink_block_id ?>" data-link-type="<?= $row->type ?>">
                                    <?php require THEME_PATH . 'views/link/settings/biolink_blocks/' . $row->type . '/' . $row->type . '_update_form.php' ?>
                                </div>
                            </div>
                        </div>

                    <?php endwhile ?>
                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center justify-content-center mt-5">
                                <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-8 col-lg-6 mb-4" alt="<?= language()->link->biolink_blocks->no_data ?>" />
                                <h2 class="h4 text-muted"><?= language()->link->biolink_blocks->no_data ?></h2>
                            </div>
                        </div>
                    </div>

                <?php endif ?>

            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 mt-5 mt-lg-0 d-flex justify-content-center">
        <div class="biolink-preview-container">
            <div class="biolink-preview">
                <div class="biolink-preview-iframe-container">
                    <iframe id="biolink_preview_iframe" class="biolink-preview-iframe" src="<?= url('l/link?link_id=' . $data->link->link_id . '&preview=' . md5($data->link->user_id)) ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="template_faq_item">
    <div class="mb-4">
        <div class="form-group">
            <label for=""><?= language()->create_biolink_faq_modal->title ?></label>
            <input id="" type="text" name="item_title[]" class="form-control" value="" required="required" />
        </div>

        <div class="form-group">
            <label for=""><?= language()->create_biolink_faq_modal->content ?></label>
            <textarea id="" name="item_content[]" class="form-control" required="required"></textarea>
        </div>

        <button type="button" data-remove="item" class="btn btn-block btn-outline-danger"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></button>
    </div>
</template>
<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/pickr.min.js' ?>"></script>
<script>
    /* Settings Tab */
    /* Initiate the color picker */
    let pickr_options = {
        comparison: false,

        components: {
            preview: true,
            opacity: true,
            hue: true,
            comparison: false,
            interaction: {
                hex: true,
                rgba: false,
                hsla: false,
                hsva: false,
                cmyk: false,
                input: true,
                clear: false,
                save: true
            }
        }
    };

    /* Display verified */
    let display_verified = () => {
        let display_verified = document.querySelector('#display_verified').checked;
        let verified_location = document.querySelector('#verified_location').value;
        let biolink_preview_iframe = $('#biolink_preview_iframe');

        if(display_verified) {
            switch(verified_location) {
                case 'top':
                    biolink_preview_iframe.contents().find(`#link-verified-wrapper-top`).show();
                    biolink_preview_iframe.contents().find(`#link-verified-wrapper-bottom`).hide();
                    break;

                case 'bottom':
                    biolink_preview_iframe.contents().find(`#link-verified-wrapper-top`).hide();
                    biolink_preview_iframe.contents().find(`#link-verified-wrapper-bottom`).show();
                    break;
            }
        } else {
            biolink_preview_iframe.contents().find(`#link-verified-wrapper-top`).hide();
            biolink_preview_iframe.contents().find(`#link-verified-wrapper-bottom`).hide();
        }
    }

    document.querySelector('#display_verified').addEventListener('change', display_verified);
    document.querySelector('#verified_location').addEventListener('change', display_verified);

    /* Text Color Handler */
    let settings_text_color_pickr = Pickr.create({
        el: '#settings_text_color_pickr',
        default: $('#settings_text_color').val(),
        ...{
            comparison: false,

            components: {
                preview: true,
                opacity: false,
                hue: true,
                comparison: false,
                interaction: {
                    hex: true,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: false,
                    save: true
                }
            }
        }
    });

    settings_text_color_pickr.on('change', hsva => {
        $('#settings_text_color').val(hsva.toHEXA().toString());
        $('#biolink_preview_iframe').contents().find('#branding').css('color', hsva.toHEXA().toString());
    });

    /* Background Type Handler */
    let background_type_handler = () => {
        let type = $('#settings_background_type').find(':selected').val();

        /* Show only the active background type */
        $(`div[id="background_type_${type}"]`).show();
        $(`div[id="background_type_${type}"]`).find('[name^="background"]').removeAttr('disabled');

        /* Disable the other possible types so they dont get submitted */
        let background_type_containers = $(`div[id^="background_type_"]:not(div[id$="_${type}"])`);

        background_type_containers.hide();
        background_type_containers.find('[name^="background"]').attr('disabled', 'disabled');
    };

    background_type_handler();

    $('#settings_background_type').on('change', background_type_handler);

    /* Preset background preview */
    $('#background_type_preset input[name="background"]').on('change', event => {
        let value = $(event.currentTarget).val();

        $('#biolink_preview_iframe').contents().find('body').attr('class', `link-body link-body-background-${value}`).attr('style', '');
    });

    /* Gradient Background */
    let settings_background_type_gradient_color_one_pickr = Pickr.create({
        el: '#settings_background_type_gradient_color_one_pickr',
        default: $('#settings_background_type_gradient_color_one').val(),
        ...pickr_options
    });

    settings_background_type_gradient_color_one_pickr.on('change', hsva => {
        $('#settings_background_type_gradient_color_one').val(hsva.toHEXA().toString());

        let color_one = $('#settings_background_type_gradient_color_one').val();
        let color_two = $('#settings_background_type_gradient_color_two').val();

        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background-image: linear-gradient(135deg, ${color_one} 10%, ${color_two} 100%);`);
    });

    let settings_background_type_gradient_color_two_pickr = Pickr.create({
        el: '#settings_background_type_gradient_color_two_pickr',
        default: $('#settings_background_type_gradient_color_two').val(),
        ...pickr_options
    });

    settings_background_type_gradient_color_two_pickr.on('change', hsva => {
        $('#settings_background_type_gradient_color_two').val(hsva.toHEXA().toString());

        let color_one = $('#settings_background_type_gradient_color_one').val();
        let color_two = $('#settings_background_type_gradient_color_two').val();

        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background-image: linear-gradient(135deg, ${color_one} 10%, ${color_two} 100%);`);
    });

    /* Color Background */
    let settings_background_type_color_pickr = Pickr.create({
        el: '#settings_background_type_color_pickr',
        default: $('#settings_background_type_color').val(),
        ...pickr_options
    });

    settings_background_type_color_pickr.on('change', hsva => {
        $('#settings_background_type_color').val(hsva.toHEXA().toString());

        $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background: ${hsva.toHEXA().toString()};`);
    });

    /* Image Background */
    function generate_background_preview(input) {
        if(input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = event => {
                $('#background_type_image_preview').attr('src', event.target.result);
                $('#biolink_preview_iframe').contents().find('body').attr('class', 'link-body').attr('style', `background: url(${event.target.result});`);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#background_type_image_input').on('change', event => {
        generate_background_preview(event.currentTarget);
    });

    /* Display branding switcher */
    $('#display_branding').on('change', event => {
        if($(event.currentTarget).is(':checked')) {
            $('#biolink_preview_iframe').contents().find('#branding').show();
        } else {
            $('#biolink_preview_iframe').contents().find('#branding').hide();
        }
    });

    /* Branding change */
    $('#branding_name').on('change paste keyup', event => {
        $('#biolink_preview_iframe').contents().find('#branding').text($(event.currentTarget).val());
    });

    $('#branding_url').on('change paste keyup', event => {
        $('#biolink_preview_iframe').contents().find('#branding').attr('src', ($(event.currentTarget).val()));
    });

    /* Form handling update */
    $('form[name="update_biolink"],form[name="update_biolink_"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);
        let notification_container = $(event.currentTarget).find('.notification-container');

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            url: event.currentTarget.getAttribute('name') == 'update_biolink_' ? 'biolink-block-ajax' : 'link-ajax',
            data: data,
            success: (data) => {
                display_notifications(data.message, data.status, notification_container);

                /* Auto scroll to notification */
                notification_container[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

                /* Update image previews for some link types */
                if(event.currentTarget.getAttribute('name') == 'update_biolink_') {
                    if(data.details?.image_prop) {
                        if(data.details.image_url) {
                            event.currentTarget.querySelector('img').setAttribute('src', data.details.image_url);
                            event.currentTarget.querySelector('img').classList.remove('d-none');
                            event.currentTarget.querySelector('[data-image-container]').classList.remove('d-none');
                        } else {
                            event.currentTarget.querySelector('img').setAttribute('src', '');
                            event.currentTarget.querySelector('img').classList.add('d-none');
                            event.currentTarget.querySelector('[data-image-container]').classList.add('d-none');
                        }

                        if(event.currentTarget.querySelector('[name="image_remove"]') && event.currentTarget.querySelector('[name="image_remove"]').checked) {
                            event.currentTarget.querySelector('[name="image_remove"]').click();
                        }

                        event.currentTarget.querySelector('input[type="file"]').value = '';
                    }
                }

                /* Update biolink opengraph image */
                if(event.currentTarget.getAttribute('name') == 'update_biolink') {
                    if(data.status == 'success') {
                        update_main_url(data.details.url);
                    }

                    if(data.details?.image_prop) {
                        if(data.details.seo_image_url) {
                            event.currentTarget.querySelector('[data-seo-image-container] img').setAttribute('src', data.details.seo_image_url);
                            event.currentTarget.querySelector('[data-seo-image-container] img').classList.remove('d-none');
                            event.currentTarget.querySelector('[data-seo-image-container]').classList.remove('d-none');
                        } else {
                            event.currentTarget.querySelector('[data-seo-image-container] img').setAttribute('src', '');
                            event.currentTarget.querySelector('[data-seo-image-container] img').classList.add('d-none');
                            event.currentTarget.querySelector('[data-seo-image-container]').classList.add('d-none');
                        }

                        if(event.currentTarget.querySelector('[name="seo_image_remove"]') && event.currentTarget.querySelector('[name="seo_image_remove"]').checked) {
                            event.currentTarget.querySelector('[name="seo_image_remove"]').click();
                        }

                        event.currentTarget.querySelector('#seo_image').value = '';

                        if(data.details.favicon_url) {
                            event.currentTarget.querySelector('[data-favicon-container] img').setAttribute('src', data.details.favicon_url);
                            event.currentTarget.querySelector('[data-favicon-container] img').classList.remove('d-none');
                            event.currentTarget.querySelector('[data-favicon-container]').classList.remove('d-none');
                        } else {
                            event.currentTarget.querySelector('[data-favicon-container] img').setAttribute('src', '');
                            event.currentTarget.querySelector('[data-favicon-container] img').classList.add('d-none');
                            event.currentTarget.querySelector('[data-favicon-container]').classList.add('d-none');
                        }

                        if(event.currentTarget.querySelector('[name="favicon_remove"]') && event.currentTarget.querySelector('[name="favicon_remove"]').checked) {
                            event.currentTarget.querySelector('[name="favicon_remove"]').click();
                        }

                        event.currentTarget.querySelector('#favicon').value = '';
                    }
                }

                /* Refresh iframe */
                let biolink_preview_iframe = document.querySelector('#biolink_preview_iframe');
                biolink_preview_iframe.setAttribute('src', biolink_preview_iframe.getAttribute('src'));
                document.querySelector('#biolink_preview_iframe').dispatchEvent(new Event('refreshed'));

            },
            dataType: 'json'
        });

        event.preventDefault();
    })

    /* Form handling create */
    $('form[name^="create_biolink_"]').on('submit', event => {
        let form = $(event.currentTarget)[0];
        let data = new FormData(form);

        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            url: 'biolink-block-ajax',
            data: data,
            success: (data) => {
                if(data.status == 'error') {

                    let notification_container = $(event.currentTarget).find('.notification-container');

                    notification_container.html('');

                    display_notifications(data.message, 'error', notification_container);

                }

                else if(data.status == 'success') {

                    /* Fade out refresh */
                    fade_out_redirect({ url: data.details.url, full: true });

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })

    /* Daterangepicker */
    let locale = <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>;
    $('[data-daterangepicker]').daterangepicker({
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: {...locale, format: 'YYYY-MM-DD HH:mm:ss'},
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
    }, (start, end, label) => {});
</script>

<script src="<?= ASSETS_FULL_URL . 'js/libraries/sortable.js' ?>"></script>
<script>
    /* Links tab sortable */
    let sortable = Sortable.create(document.getElementById('biolink_blocks'), {
        animation: 150,
        handle: '.drag',
        onUpdate: (event) => {

            let biolink_blocks = [];
            $('#biolink_blocks > .biolink_block').each((i, elm) => {
                biolink_blocks.push({
                    biolink_block_id: $(elm).data('biolink-block-id'),
                    order: i
                });
            });

            $.ajax({
                type: 'POST',
                url: 'biolink-block-ajax',
                data: {
                    request_type: 'order',
                    biolink_blocks,
                    global_token
                },
                dataType: 'json'
            });

            /* Refresh iframe */
            let biolink_preview_iframe = document.querySelector('#biolink_preview_iframe');
            biolink_preview_iframe.setAttribute('src', biolink_preview_iframe.getAttribute('src'));
            document.querySelector('#biolink_preview_iframe').dispatchEvent(new Event('refreshed'));
        }
    });

    /* Status change handler for the links */
    $('[id^="biolink_block_is_enabled_"]').on('change', event => {
        ajax_call_helper(event, 'biolink-block-ajax', 'is_enabled_toggle', () => {

            $(event.currentTarget).closest('.biolink_block').toggleClass('custom-row-inactive');

            /* Refresh iframe */
            let biolink_preview_iframe = document.querySelector('#biolink_preview_iframe');
            biolink_preview_iframe.setAttribute('src', biolink_preview_iframe.getAttribute('src'));
            document.querySelector('#biolink_preview_iframe').dispatchEvent(new Event('refreshed'));
        });
    });

    /* Duplicate link handler for the links */
    $('[data-duplicate="true"]').on('click', event => {
        ajax_call_helper(event, 'biolink-block-ajax', 'duplicate', (event, data) => {

            fade_out_redirect({ url: data.details.url, full: true });

        });
    });

    /* When an expanding happens for a link settings */
    $('[id^="biolink_block_expanded_content"]').on('show.bs.collapse', event => {
        let update_form_content = event.currentTarget;
        let link_type = $(update_form_content).data('link-type');
        let biolink_block_id = $(update_form_content.querySelector('input[name="biolink_block_id"]')).val();
        let biolink_link = $('#biolink_preview_iframe').contents().find(`div[data-biolink-block-id="${biolink_block_id}"]`);

        $('#biolink_preview_iframe').off().on('refreshed', event => {
            setTimeout(() => {
                biolink_link = $('#biolink_preview_iframe').contents().find(`div[data-biolink-block-id="${biolink_block_id}"]`);
                block_expanded_content_init();
            }, 900)
        })

        let block_expanded_content_init = () => {
            switch (link_type) {

                case 'heading':
                    let heading_text_color_pickr_element = update_form_content.querySelector('.text_color_pickr');

                    if(heading_text_color_pickr_element) {
                        let color_input = update_form_content.querySelector('input[name="text_color"]');

                        /* Color Handler */
                        let color_pickr = Pickr.create({
                            el: heading_text_color_pickr_element,
                            default: $(color_input).val(),
                            ...pickr_options
                        });

                        color_pickr.off().on('change', hsva => {
                            $(color_input).val(hsva.toHEXA().toString());
                            biolink_link.children().css('color', hsva.toHEXA().toString());
                        });
                    }

                    $(update_form_content).find('input[name="text"]').off().on('change paste keyup', event => {
                        biolink_link.children().text($(event.currentTarget).val());
                    });

                    break;

                case 'paragraph':
                    let paragraph_text_color_pickr_element = update_form_content.querySelector('.text_color_pickr');

                    if(paragraph_text_color_pickr_element) {
                        let color_input = update_form_content.querySelector('input[name="text_color"]');

                        /* Color Handler */
                        let color_pickr = Pickr.create({
                            el: paragraph_text_color_pickr_element,
                            default: $(color_input).val(),
                            ...pickr_options
                        });

                        color_pickr.off().on('change', hsva => {
                            $(color_input).val(hsva.toHEXA().toString());
                            biolink_link.find('p').css('color', hsva.toHEXA().toString());
                        });
                    }

                    $(update_form_content).find('textarea[name="text"]').off().on('change paste keyup', event => {
                        biolink_link.find('p').text($(event.currentTarget).val());
                    });

                    break;

                default:

                    biolink_link = biolink_link.find('a');

                    let text_color_pickr_element = update_form_content.querySelector('.text_color_pickr');
                    let background_color_pickr_element = update_form_content.querySelector('.background_color_pickr');
                    let border_color_pickr_element = update_form_content.querySelector('.border_color_pickr');

                    /* Schedule Handler */
                    let schedule_handler = () => {
                        if($(update_form_content.querySelector('input[name="schedule"]')).is(':checked')) {
                            $(update_form_content.querySelector('.schedule_container')).show();
                        } else {
                            $(update_form_content.querySelector('.schedule_container')).hide();
                        }
                    };

                    $(update_form_content.querySelector('input[name="schedule"]')).off().on('change', schedule_handler);

                    schedule_handler();

                    /* Name, icon and image thumbnail */
                    $(update_form_content.querySelector('input[name="name"]')).off().on('change paste keyup', event => {
                        let name = $(event.currentTarget).val();

                        /* Set the name in the preview */
                        biolink_link.text(name);
                        $(update_form_content.querySelector('input[name="icon"]')).trigger('change');

                        /* Set the name in the form title */
                        $(`[data-target="#biolink_block_expanded_content${biolink_block_id}"] > strong`).text(name);

                    });

                    $(update_form_content.querySelector('input[name="icon"]')).off().on('change paste keyup', event => {
                        let icon = $(event.currentTarget).val();

                        if(!icon) {
                            biolink_link.find('svg').remove();
                        } else {

                            biolink_link.find('svg,i').remove();
                            biolink_link.prepend(`<i class="${icon} mr-1"></i>`);

                        }

                    });

                    if(text_color_pickr_element) {
                        let color_input = update_form_content.querySelector('input[name="text_color"]');

                        /* text color handler */
                        let color_pickr = Pickr.create({
                            el: text_color_pickr_element,
                            default: $(color_input).val(),
                            ...pickr_options
                        });

                        color_pickr.off().on('change', hsva => {
                            $(color_input).val(hsva.toHEXA().toString());

                            biolink_link.css('color', hsva.toHEXA().toString());
                        });
                    }

                    if(background_color_pickr_element) {
                        let color_input = update_form_content.querySelector('input[name="background_color"]');

                        /* background color handler */
                        let color_pickr = Pickr.create({
                            el: background_color_pickr_element,
                            default: $(color_input).val(),
                            ...pickr_options
                        });

                        color_pickr.off().on('change', hsva => {
                            $(color_input).val(hsva.toHEXA().toString());
                            biolink_link.css('background-color', hsva.toHEXA().toString());
                        });
                    }

                    $(update_form_content.querySelector('input[name="border_width"]')).off().on('change paste keyup', event => {
                        let border_width = $(event.currentTarget).val();
                        biolink_link.css('border-width', border_width+'px');
                    });

                    if(border_color_pickr_element) {
                        let color_input = update_form_content.querySelector('input[name="border_color"]');

                        /* text color handler */
                        let color_pickr = Pickr.create({
                            el: border_color_pickr_element,
                            default: $(color_input).val(),
                            ...pickr_options
                        });

                        color_pickr.off().on('change', hsva => {
                            $(color_input).val(hsva.toHEXA().toString());
                            biolink_link.css('border-color', hsva.toHEXA().toString());
                        });
                    }

                    $(update_form_content.querySelector('select[name="border_radius"]')).off().on('change', event => {
                        let border_radius = $(event.currentTarget).find(':selected').val();

                        switch(border_radius) {
                            case 'straight':
                                biolink_link.removeClass('link-btn-round link-btn-rounded');
                                break;

                            case 'round':
                                biolink_link.removeClass('link-btn-rounded').addClass('link-btn-round');
                                break;

                            case 'rounded':
                                biolink_link.removeClass('link-btn-round').addClass('link-btn-rounded');
                                break;
                        }
                    });

                    $(update_form_content.querySelector('select[name="border_style"]')).off().on('change', event => {
                        let border_style = $(event.currentTarget).find(':selected').val();
                        biolink_link.css('border-style', border_style);
                    });

                    let current_animation = $(update_form_content.querySelector('select[name="animation"]')).val();

                    $(update_form_content.querySelector('select[name="animation"]')).off().on('change', event => {

                        let animation = $(event.currentTarget).find(':selected').val();

                        switch(animation) {
                            case 'false':
                                biolink_link.removeClass(`animated ${current_animation}`);
                                current_animation = false;
                                break;

                            default:
                                biolink_link.removeClass(`animated ${current_animation}`).addClass(`animated ${animation}`);
                                current_animation = animation;
                                break;
                        }

                    });

                    if(link_type == 'cta') {
                        let cta_update_modal_initiate = () => {
                            let cta_type = update_form_content.querySelector('[name="type"]').value;

                            update_form_content.querySelectorAll('[data-cta-type]').forEach(element => {
                                if(element.getAttribute('data-cta-type') == cta_type) {
                                    element.classList.remove('d-none');
                                } else {
                                    if(!element.classList.contains('d-none')) {
                                        element.classList.add('d-none');
                                    }
                                }
                            });
                        }

                        update_form_content.querySelector('[name="type"]').removeEventListener('change', cta_update_modal_initiate);
                        update_form_content.querySelector('[name="type"]').addEventListener('change', cta_update_modal_initiate);

                        cta_update_modal_initiate();
                    }

                    if(link_type == 'external_item') {
                        let name_text_color_pickr_element = update_form_content.querySelector('.name_text_color_pickr');
                        let description_text_color_pickr_element = update_form_content.querySelector('.description_text_color_pickr');
                        let price_text_color_pickr_element = update_form_content.querySelector('.price_text_color_pickr');

                        if(name_text_color_pickr_element) {
                            let color_input = update_form_content.querySelector('input[name="name_text_color"]');

                            /* text color handler */
                            let color_pickr = Pickr.create({
                                el: name_text_color_pickr_element,
                                default: $(color_input).val(),
                                ...pickr_options
                            });

                            color_pickr.off().on('change', hsva => {
                                $(color_input).val(hsva.toHEXA().toString());
                                biolink_link.find('[data-name]').css('color', hsva.toHEXA().toString());
                            });
                        }

                        if(description_text_color_pickr_element) {
                            let color_input = update_form_content.querySelector('input[name="description_text_color"]');

                            /* text color handler */
                            let color_pickr = Pickr.create({
                                el: description_text_color_pickr_element,
                                default: $(color_input).val(),
                                ...pickr_options
                            });

                            color_pickr.off().on('change', hsva => {
                                $(color_input).val(hsva.toHEXA().toString());
                                biolink_link.find('[data-description]').css('color', hsva.toHEXA().toString());
                            });
                        }

                        if(price_text_color_pickr_element) {
                            let color_input = update_form_content.querySelector('input[name="price_text_color"]');

                            /* text color handler */
                            let color_pickr = Pickr.create({
                                el: price_text_color_pickr_element,
                                default: $(color_input).val(),
                                ...pickr_options
                            });

                            color_pickr.off().on('change', hsva => {
                                $(color_input).val(hsva.toHEXA().toString());
                                biolink_link.find('[data-price]').css('color', hsva.toHEXA().toString());
                            });
                        }
                    }

                    break;
            }
        }

        block_expanded_content_init();
    })

</script>

<script>
    /* FAQ Script */
    'use strict';

    /* add new */
    let faq_item_add = event => {
        let biolink_block_id = event.currentTarget.getAttribute('data-biolink-block-id');
        let clone = document.querySelector(`#template_faq_item`).content.cloneNode(true);
        let count = document.querySelectorAll(`[id="faq_items_${biolink_block_id}"] .mb-4`).length;

        if(count >= 100) return;

        clone.querySelector(`input[name="item_title[]"`).setAttribute('name', `item_title[${count}]`);
        clone.querySelector(`textarea[name="item_content[]"`).setAttribute('name', `item_content[${count}]`);

        document.querySelector(`[id="faq_items_${biolink_block_id}"]`).appendChild(clone);

        faq_item_remove_initiator();
    };

    document.querySelectorAll('[data-add="faq_item"]').forEach(element => {
        element.addEventListener('click', faq_item_add);
    })

    /* remove */
    let faq_item_remove = event => {
        event.currentTarget.closest('.mb-4').remove();
    };

    let faq_item_remove_initiator = () => {
        document.querySelectorAll('[id^="faq_items_"] [data-remove]').forEach(element => {
            element.removeEventListener('click', faq_item_remove);
            element.addEventListener('click', faq_item_remove)
        })
    };

    faq_item_remove_initiator();
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
