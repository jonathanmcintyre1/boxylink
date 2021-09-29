<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="file" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'file_file_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-file fa-sm mr-1"></i> <?= language()->create_biolink_file_modal->file ?></label>
        <input id="<?= 'file_file_' . $row->biolink_block_id ?>" type="file" name="file" accept="<?= implode(', ', array_map(function($value) { return '.' . $value; }, $data->biolink_blocks['file']['whitelisted_file_extensions'])) ?>" class="form-control-file" />
        <small class="form-text text-muted"><?= language()->create_biolink_file_modal->file_help ?></small>
    </div>

    <div class="form-group">
        <label for="<?= 'file_name_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
        <input id="<?= 'file_name_' . $row->biolink_block_id ?>" type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" required="required" />
    </div>

    <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
        <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
            <div class="custom-control custom-switch mb-3">
                <input
                        id="<?= 'file_schedule_' . $row->biolink_block_id ?>"
                        name="schedule" type="checkbox"
                        class="custom-control-input"
                    <?= !empty($row->start_date) && !empty($row->end_date) ? 'checked="checked"' : null ?>
                    <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'disabled="disabled"' ?>
                >
                <label class="custom-control-label" for="<?= 'file_schedule_' . $row->biolink_block_id ?>"><?= language()->link->settings->schedule ?></label>
                <small class="form-text text-muted"><?= language()->link->settings->schedule_help ?></small>
            </div>
        </div>
    </div>

    <div class="mt-3 schedule_container" style="display: none;">
        <div <?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
            <div class="<?= $this->user->plan_settings->temporary_url_is_enabled ? null : 'container-disabled' ?>">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="<?= 'file_start_date_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= language()->link->settings->start_date ?></label>
                            <input
                                    id="<?= 'file_start_date_' . $row->biolink_block_id ?>"
                                    type="text"
                                    class="form-control"
                                    name="start_date"
                                    value="<?= \Altum\Date::get($row->start_date, 1) ?>"
                                    placeholder="<?= language()->link->settings->start_date ?>"
                                    autocomplete="off"
                                    data-daterangepicker
                            >
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="<?= 'file_end_date_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= language()->link->settings->end_date ?></label>
                            <input
                                    id="<?= 'file_end_date_' . $row->biolink_block_id ?>"
                                    type="text"
                                    class="form-control"
                                    name="end_date"
                                    value="<?= \Altum\Date::get($row->end_date, 1) ?>"
                                    placeholder="<?= language()->link->settings->end_date ?>"
                                    autocomplete="off"
                                    data-daterangepicker
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="<?= 'file_image_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->image ?></label>
        <div data-image-container class="<?= !empty($row->settings->image) ? null : 'd-none' ?>">
            <div class="row">
                <div class="m-1 col-6 col-xl-3">
                    <img src="<?= $row->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $row->settings->image : null ?>" class="img-fluid rounded <?= !empty($row->settings->image) ? null : 'd-none' ?>" loading="lazy" />
                </div>
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="<?= $row->biolink_block_id . '_image_remove' ?>" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'file_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'file_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove' ?>">
                    <span class="text-muted"><?= language()->global->delete_file ?></span>
                </label>
            </div>
        </div>
        <input id="<?= 'file_image_' . $row->biolink_block_id ?>" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
    </div>

    <div class="form-group">
        <label for="<?= 'file_icon_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-globe fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->icon ?></label>
        <input id="<?= 'file_icon_' . $row->biolink_block_id ?>" type="text" name="icon" class="form-control" value="<?= $row->settings->icon ?>" placeholder="<?= language()->create_biolink_link_modal->input->icon_placeholder ?>" />
        <small class="form-text text-muted"><?= language()->create_biolink_link_modal->input->icon_help ?></small>
    </div>

    <div <?= $this->user->plan_settings->custom_colored_links ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
        <div class="<?= $this->user->plan_settings->custom_colored_links ? null : 'container-disabled' ?>">
            <div class="form-group">
                <label for="<?= 'file_text_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->text_color ?></label>
                <input id="<?= 'file_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>

            <div class="form-group">
                <label for="<?= 'file_background_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->background_color ?></label>
                <input id="<?= 'file_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                <div class="background_color_pickr"></div>
            </div>

            <div class="form-group">
                <label for="<?= 'file_border_width_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-style fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->border_width ?></label>
                <input id="<?= 'file_border_width_' . $row->biolink_block_id ?>" type="number" min="0" max="5" class="form-control" name="border_width" value="<?= $row->settings->border_width ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="<?= 'file_border_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->border_color ?></label>
                <input id="<?= 'file_border_color_' . $row->biolink_block_id ?>" type="hidden" name="border_color" class="form-control" value="<?= $row->settings->border_color ?>" required="required" />
                <div class="border_color_pickr"></div>
            </div>

            <div class="form-group">
                <label for="<?= 'file_border_radius_' . $row->biolink_block_id ?>"><?= language()->create_biolink_link_modal->input->border_radius ?></label>
                <select id="<?= 'file_border_radius_' . $row->biolink_block_id ?>" name="border_radius" class="form-control">
                    <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_radius_straight ?></option>
                    <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_radius_round ?></option>
                    <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_radius_rounded ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="<?= 'file_border_style_' . $row->biolink_block_id ?>"><?= language()->create_biolink_link_modal->input->border_style ?></label>
                <select id="<?= 'file_border_style_' . $row->biolink_block_id ?>" name="border_style" class="form-control">
                    <option value="solid" <?= $row->settings->border_style == 'solid' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_style_solid ?></option>
                    <option value="dashed" <?= $row->settings->border_style == 'dashed' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_style_dashed ?></option>
                    <option value="double" <?= $row->settings->border_style == 'double' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_style_double ?></option>
                    <option value="outset" <?= $row->settings->border_style == 'outset' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_style_outset ?></option>
                    <option value="inset" <?= $row->settings->border_style == 'inset' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->border_style_inset ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="<?= 'file_animation_' . $row->biolink_block_id ?>"><?= language()->create_biolink_link_modal->input->animation ?></label>
                <select id="<?= 'file_animation_' . $row->biolink_block_id ?>" name="animation" class="form-control">
                    <option value="false" <?= !$row->settings->animation ? 'selected="selected"' : null ?>>-</option>
                    <?php foreach(require APP_PATH . 'includes/biolink_animations.php' as $animation): ?>
                    <option value="<?= $animation ?>" <?= $row->settings->animation == $animation ? 'selected="selected"' : null ?>><?= $animation ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="<?= 'file_animation_runs_' . $row->biolink_block_id ?>"><?= language()->create_biolink_link_modal->input->animation_runs ?></label>
                <select id="<?= 'file_animation_runs_' . $row->biolink_block_id ?>" name="animation_runs" class="form-control">
                    <option value="repeat-1" <?= $row->settings->animation_runs == 'repeat-1' ? 'selected="selected"' : null ?>>1</option>
                    <option value="repeat-2" <?= $row->settings->animation_runs == 'repeat-2' ? 'selected="selected"' : null ?>>2</option>
                    <option value="repeat-3" <?= $row->settings->animation_runs == 'repeat-3' ? 'selected="selected"' : null ?>>3</option>
                    <option value="infinite" <?= $row->settings->animation_runs == 'repeat-infinite' ? 'selected="selected"' : null ?>><?= language()->create_biolink_link_modal->input->animation_runs_infinite ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
