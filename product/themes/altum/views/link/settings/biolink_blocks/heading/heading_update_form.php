<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="heading" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'heading_heading_type_' . $row->biolink_block_id ?>"><?= language()->create_biolink_heading_modal->heading_type ?></label>
        <select id="<?= 'heading_heading_type_' . $row->biolink_block_id ?>" name="heading_type" class="form-control">
            <option value="h1" <?= $row->settings->heading_type == 'h1' ? 'selected="selected"' : null ?>>H1</option>
            <option value="h2" <?= $row->settings->heading_type == 'h2' ? 'selected="selected"' : null ?>>H2</option>
            <option value="h3" <?= $row->settings->heading_type == 'h3' ? 'selected="selected"' : null ?>>H3</option>
            <option value="h4" <?= $row->settings->heading_type == 'h4' ? 'selected="selected"' : null ?>>H4</option>
            <option value="h5" <?= $row->settings->heading_type == 'h5' ? 'selected="selected"' : null ?>>H5</option>
            <option value="h6" <?= $row->settings->heading_type == 'h6' ? 'selected="selected"' : null ?>>H6</option>
        </select>
    </div>

    <div class="form-group">
        <label for="<?= 'heading_text_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-heading fa-sm mr-1"></i> <?= language()->create_biolink_heading_modal->text ?></label>
        <input id="<?= 'heading_text_' . $row->biolink_block_id ?>" type="text" class="form-control" name="text" value="<?= $row->settings->text ?>" />
    </div>

    <div <?= $this->user->plan_settings->custom_colored_links ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
        <div class="<?= $this->user->plan_settings->custom_colored_links ? null : 'container-disabled' ?>">
            <div class="form-group">
                <label><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= language()->create_biolink_heading_modal->text_color ?></label>
                <input type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                <div class="text_color_pickr"></div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
