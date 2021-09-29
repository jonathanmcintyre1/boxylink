<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="title"><i class="fa fa-fw fa-sm fa-heading text-muted mr-1"></i> <?= language()->admin_settings->main->title ?></label>
        <input id="title" type="text" name="title" class="form-control form-control-lg" value="<?= settings()->title ?>" />
    </div>

    <div class="form-group">
        <label for="default_language"><i class="fa fa-fw fa-sm fa-language text-muted mr-1"></i> <?= language()->admin_settings->main->default_language ?></label>
        <select id="default_language" name="default_language" class="form-control form-control-lg">
            <?php foreach(\Altum\Language::$languages as $value) echo '<option value="' . $value . '" ' . (settings()->default_language == $value ? 'selected="selected"' : null) . '>' . $value . '</option>' ?>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->main->default_language_help ?></small>
    </div>

    <div class="form-group">
        <label for="default_theme_style"><i class="fa fa-fw fa-sm fa-fill-drip text-muted mr-1"></i> <?= language()->admin_settings->main->default_theme_style ?></label>
        <select id="default_theme_style" name="default_theme_style" class="form-control form-control-lg">
            <?php foreach(\Altum\ThemeStyle::$themes as $key => $value) echo '<option value="' . $key . '" ' . (settings()->default_theme_style == $key ? 'selected="selected"' : null) . '>' . $key . '</option>' ?>
        </select>
    </div>

    <div class="form-group">
        <label for="logo"><i class="fa fa-fw fa-sm fa-eye text-muted mr-1"></i> <?= language()->admin_settings->main->logo ?></label>
        <?php if(!empty(settings()->logo)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'logo/' . settings()->logo ?>" class="img-fluid" style="max-height: 2.5rem;height: 2.5rem;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="logo_remove" name="logo_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#logo').classList.add('d-none') : document.querySelector('#logo').classList.remove('d-none')">
                <label class="custom-control-label" for="logo_remove">
                    <span class="text-muted"><?= language()->global->delete_file ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="logo" type="file" name="logo" accept=".gif, .ico, .png, .jpg, .jpeg, .svg" class="form-control-file" />
        <small class="form-text text-muted"><?= language()->admin_settings->main->logo_help ?></small>
    </div>

    <div class="form-group">
        <label for="favicon"><i class="fa fa-fw fa-sm fa-icons text-muted mr-1"></i> <?= language()->admin_settings->main->favicon ?></label>
        <?php if(!empty(settings()->favicon)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'favicon/' . settings()->favicon ?>" class="img-fluid" style="max-height: 32px;height: 32px;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="favicon_remove" name="favicon_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#favicon').classList.add('d-none') : document.querySelector('#favicon').classList.remove('d-none')">
                <label class="custom-control-label" for="favicon_remove">
                    <span class="text-muted"><?= language()->global->delete_file ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="favicon" type="file" name="favicon" accept=".gif, .ico, .png" class="form-control-file" />
        <small class="form-text text-muted"><?= language()->admin_settings->main->favicon_help ?></small>
    </div>

    <div class="form-group">
        <label for="opengraph"><i class="fa fa-fw fa-sm fa-image text-muted mr-1"></i> <?= language()->admin_settings->main->opengraph ?></label>
        <?php if(!empty(settings()->opengraph)): ?>
            <div class="m-1">
                <img src="<?= UPLOADS_FULL_URL . 'opengraph/' . settings()->opengraph ?>" class="img-fluid" style="max-height: 5rem;height: 5rem;" />
            </div>
            <div class="custom-control custom-checkbox my-2">
                <input id="opengraph_remove" name="opengraph_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#opengraph').classList.add('d-none') : document.querySelector('#opengraph').classList.remove('d-none')">
                <label class="custom-control-label" for="opengraph_remove">
                    <span class="text-muted"><?= language()->global->delete_file ?></span>
                </label>
            </div>
        <?php endif ?>
        <input id="opengraph" type="file" name="opengraph" accept=".gif, .png, .jpg, .jpeg" class="form-control-file" />
        <small class="form-text text-muted"><?= language()->admin_settings->main->opengraph_help ?></small>
    </div>

    <div class="form-group">
        <label for="default_timezone"><i class="fa fa-fw fa-sm fa-atlas text-muted mr-1"></i> <?= language()->admin_settings->main->default_timezone ?></label>
        <select id="default_timezone" name="default_timezone" class="form-control form-control-lg">
            <?php foreach(DateTimeZone::listIdentifiers() as $timezone) echo '<option value="' . $timezone . '" ' . (settings()->default_timezone == $timezone ? 'selected="selected"' : null) . '>' . $timezone . '</option>' ?>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->main->default_timezone_help ?></small>
    </div>

    <div class="form-group">
        <label for="email_confirmation"><i class="fa fa-fw fa-sm fa-envelope text-muted mr-1"></i> <?= language()->admin_settings->main->email_confirmation ?></label>
        <select id="email_confirmation" name="email_confirmation" class="form-control form-control-lg">
            <option value="1" <?= settings()->email_confirmation ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->email_confirmation ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->main->email_confirmation_help ?></small>
    </div>

    <div class="form-group">
        <label for="register_is_enabled"><i class="fa fa-fw fa-sm fa-users text-muted mr-1"></i> <?= language()->admin_settings->main->register_is_enabled ?></label>
        <select id="register_is_enabled" name="register_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->register_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->register_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="se_indexing"><i class="fa fa-fw fa-sm fa-search text-muted mr-1"></i> <?= language()->admin_settings->main->se_indexing ?></label>
        <select id="se_indexing" name="se_indexing" class="form-control form-control-lg">
            <option value="1" <?= settings()->main->se_indexing ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->main->se_indexing ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="index_url"><i class="fa fa-fw fa-sm fa-sitemap text-muted mr-1"></i> <?= language()->admin_settings->main->index_url ?></label>
        <input id="index_url" type="text" name="index_url" class="form-control form-control-lg" value="<?= settings()->index_url ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->main->index_url_help ?></small>
    </div>

    <div class="form-group">
        <label for="terms_and_conditions_url"><i class="fa fa-fw fa-sm fa-file-word text-muted mr-1"></i> <?= language()->admin_settings->main->terms_and_conditions_url ?></label>
        <input id="terms_and_conditions_url" type="text" name="terms_and_conditions_url" class="form-control form-control-lg" value="<?= settings()->terms_and_conditions_url ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->main->terms_and_conditions_url_help ?></small>
    </div>

    <div class="form-group">
        <label for="privacy_policy_url"><i class="fa fa-fw fa-sm fa-file-word text-muted mr-1"></i> <?= language()->admin_settings->main->privacy_policy_url ?></label>
        <input id="privacy_policy_url" type="text" name="privacy_policy_url" class="form-control form-control-lg" value="<?= settings()->privacy_policy_url ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->main->privacy_policy_url_help ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
