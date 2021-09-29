<?php defined('ALTUMCODE') || die() ?>

<ul class="list-style-none m-0">
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->projects_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->projects_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->projects_limit, ($data->plan_settings->projects_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->projects_limit))) ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->pixels_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->pixels_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->pixels_limit, ($data->plan_settings->pixels_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->pixels_limit))) ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->biolinks_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->biolinks_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->biolinks_limit, ($data->plan_settings->biolinks_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->biolinks_limit))) ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->biolink_blocks_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->biolink_blocks_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->biolink_blocks_limit, ($data->plan_settings->biolink_blocks_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->biolink_blocks_limit))) ?>
        </div>
    </li>

    <?php if(settings()->links->shortener_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->links_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->links_limit ? null : 'text-muted' ?>">
                <?= sprintf(language()->global->plan_settings->links_limit, ($data->plan_settings->links_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->links_limit))) ?>
            </div>
        </li>
    <?php endif ?>

    <?php $enabled_biolink_blocks = array_filter((array) $data->plan_settings->enabled_biolink_blocks) ?>
    <?php $enabled_biolink_blocks_count = count($enabled_biolink_blocks) ?>
    <?php
    $enabled_biolink_blocks_string = implode(', ', array_map(function($key) {
        return language()->link->biolink->blocks->{mb_strtolower($key)};
    }, array_keys($enabled_biolink_blocks)));
    ?>
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $enabled_biolink_blocks_count ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $enabled_biolink_blocks_count ? null : 'text-muted' ?>">
            <span data-toggle="tooltip" title="<?= $enabled_biolink_blocks_string ?>">
            <?php if($enabled_biolink_blocks_count == count(require APP_PATH . 'includes/biolink_blocks.php')): ?>
                <?= language()->global->plan_settings->enabled_biolink_blocks_all ?>
            <?php else: ?>
                <?= sprintf(language()->global->plan_settings->enabled_biolink_blocks_x, '<strong>' . nr($enabled_biolink_blocks_count) . '</strong>') ?>
            <?php endif ?>
            </span>
        </div>
    </li>

    <?php if(settings()->links->domains_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->domains_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->domains_limit ? null : 'text-muted' ?>">
                <?= sprintf(language()->global->plan_settings->domains_limit, ($data->plan_settings->domains_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->domains_limit))) ?>
            </div>
        </li>
    <?php endif ?>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->track_links_retention ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->track_links_retention ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->track_links_retention, ($data->plan_settings->track_links_retention == -1 ? language()->global->unlimited : nr($data->plan_settings->track_links_retention))) ?>
        </div>
    </li>

    <?php foreach(require APP_PATH . 'includes/simple_user_plan_settings.php' as $row): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->{$row} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->{$row} ? null : 'text-muted' ?>">
                <span data-toggle="tooltip" title="<?= language()->global->plan_settings->{$row . '_help'} ?>">
                    <?= language()->global->plan_settings->{$row} ?>
                </span>
            </div>
        </li>
    <?php endforeach ?>
</ul>
