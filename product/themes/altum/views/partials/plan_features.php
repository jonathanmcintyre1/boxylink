<?php defined('ALTUMCODE') || die() ?>


<ul class="list-style-none m-0">

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->projects_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->projects_limit ? null : 'text-muted' ?>">
            <?php if($data->plan_settings->projects_limit == -1): ?>
                <?= \Altum\Language::get()->global->plan_settings->unlimited_projects_limit ?>
            <?php else: ?>
                <?= sprintf(\Altum\Language::get()->global->plan_settings->projects_limit, '<strong>' . nr($data->plan_settings->projects_limit) . '</strong>') ?>
            <?php endif ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->biolinks_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->biolinks_limit ? null : 'text-muted' ?>">
            <?php if($data->plan_settings->biolinks_limit == -1): ?>
                <?= \Altum\Language::get()->global->plan_settings->unlimited_biolinks_limit ?>
            <?php else: ?>
                <?= sprintf(\Altum\Language::get()->global->plan_settings->biolinks_limit, '<strong>' . nr($data->plan_settings->biolinks_limit) . '</strong>') ?>
            <?php endif ?>
        </div>
    </li>

    <?php if($this->settings->links->shortener_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->links_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->links_limit ? null : 'text-muted' ?>">
                <?php if($data->plan_settings->links_limit == -1): ?>
                    <?= \Altum\Language::get()->global->plan_settings->unlimited_links_limit ?>
                <?php else: ?>
                    <?= sprintf(\Altum\Language::get()->global->plan_settings->links_limit, '<strong>' . nr($data->plan_settings->links_limit) . '</strong>') ?>
                <?php endif ?>
            </div>
        </li>
    <?php endif ?>

    <?php $enabled_biolink_blocks = array_filter((array) $data->plan_settings->enabled_biolink_blocks) ?>
    <?php $enabled_biolink_blocks_count = count($enabled_biolink_blocks) ?>
    <?php
    $enabled_biolink_blocks_string = implode(', ', array_map(function($key) {
        return \Altum\Language::get()->link->biolink->{strtolower($key)}->name;
    }, array_keys($enabled_biolink_blocks)));
    ?>
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $enabled_biolink_blocks_count ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $enabled_biolink_blocks_count ? null : 'text-muted' ?>">
            <span data-toggle="tooltip" title="<?= $enabled_biolink_blocks_string ?>">
            <?php if($enabled_biolink_blocks_count == count(require APP_PATH . 'includes/biolink_blocks.php')): ?>
                <?= \Altum\Language::get()->global->plan_settings->enabled_biolink_blocks_all ?>
            <?php else: ?>
                <?= sprintf(\Altum\Language::get()->global->plan_settings->enabled_biolink_blocks_x, '<strong>' . nr($enabled_biolink_blocks_count) . '</strong>') ?>
            <?php endif ?>
            </span>
        </div>
    </li>

    <?php if($this->settings->links->domains_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->domains_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->domains_limit ? null : 'text-muted' ?>">
                <?php if($data->plan_settings->domains_limit == -1): ?>
                    <?= \Altum\Language::get()->global->plan_settings->unlimited_domains_limit ?>
                <?php else: ?>
                    <?= sprintf(\Altum\Language::get()->global->plan_settings->domains_limit, '<strong>' . nr($data->plan_settings->domains_limit) . '</strong>') ?>
                <?php endif ?>
            </div>
        </li>
    <?php endif ?>

    <?php $simple_user_plan_settings = require APP_PATH . 'includes/simple_user_plan_settings.php' ?>
    <?php foreach($simple_user_plan_settings as $row): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->{$row} ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->{$row} ? null : 'text-muted' ?>">
                <span data-toggle="tooltip" title="<?= \Altum\Language::get()->global->plan_settings->{$row . '_help'} ?>">
                    <?= \Altum\Language::get()->global->plan_settings->{$row} ?>
                </span>
            </div>
        </li>
    <?php endforeach ?>

</ul>
