<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="countdown" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'countdown_end_date_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-clock fa-sm mr-1"></i> <?= language()->create_biolink_countdown_modal->end_date ?></label>
        <input
                id="<?= 'countdown_end_date_' . $row->biolink_block_id ?>"
                type="text"
                class="form-control"
                name="end_date"
                value="<?= \Altum\Date::get($row->settings->end_date, 1) ?>"
                autocomplete="off"
                data-daterangepicker
        />
    </div>

    <div class="form-group">
        <label for="<?= 'countdown_theme_' . $row->biolink_block_id ?>"><?= language()->create_biolink_countdown_modal->theme ?></label>
        <select id="<?= 'countdown_theme_' . $row->biolink_block_id ?>" name="theme" class="form-control">
            <option value="light" <?= $row->settings->theme == 'light' ? 'selected="selected"' : null ?>><?= language()->create_biolink_countdown_modal->theme_light ?></option>
            <option value="dark" <?= $row->settings->theme == 'dark' ? 'selected="selected"' : null ?>><?= language()->create_biolink_countdown_modal->theme_dark ?></option>
        </select>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
