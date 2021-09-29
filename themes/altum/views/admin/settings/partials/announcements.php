<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="content"><?= language()->admin_settings->announcements->content ?></label>
        <textarea id="content" name="content" class="form-control form-control-lg"><?= settings()->announcements->content ?></textarea>
        <small class="form-text text-muted"><?= language()->admin_settings->announcements->content_help ?></small>
    </div>

    <div class="form-group">
        <label for="text_color"><?= language()->admin_settings->announcements->text_color ?></label>
        <input id="text_color" type="color" name="text_color" class="form-control form-control-lg" value="<?= settings()->announcements->text_color ?>" />
    </div>

    <div class="form-group">
        <label for="background_color"><?= language()->admin_settings->announcements->background_color ?></label>
        <input id="background_color" type="color" name="background_color" class="form-control form-control-lg" value="<?= settings()->announcements->background_color ?>" />
    </div>

    <div class="custom-control custom-switch my-3">
        <input id="show_logged_in" name="show_logged_in" type="checkbox" class="custom-control-input" <?= settings()->announcements->show_logged_in ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="show_logged_in"><?= language()->admin_settings->announcements->show_logged_in ?></label>
    </div>

    <div class="custom-control custom-switch my-3">
        <input id="show_logged_out" name="show_logged_out" type="checkbox" class="custom-control-input" <?= settings()->announcements->show_logged_out ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="show_logged_out"><?= language()->admin_settings->announcements->show_logged_out ?></label>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
