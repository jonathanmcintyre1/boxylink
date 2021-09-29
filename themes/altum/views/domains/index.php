<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <small>
            <ol class="custom-breadcrumbs">
                <li><a href="<?= url('dashboard') ?>"><?= language()->dashboard->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= language()->domains->breadcrumb ?></li>
            </ol>
        </small>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl mb-3 mb-xl-0">
            <h1 class="h4"><?= language()->domains->header ?></h1>
            <p class="text-muted"><?= language()->domains->subheader ?></p>
        </div>

        <div class="col-12 col-xl-auto d-flex">
            <div>
            <?php if($this->user->plan_settings->domains_limit != -1 && $data->total_domains >= $this->user->plan_settings->domains_limit): ?>
                <button type="button" data-toggle="tooltip" title="<?= language()->domains->error_message->domains_limit ?>"  class="btn btn-primary disabled">
                    <i class="fa fa-fw fa-plus-circle"></i> <?= language()->global->create ?>
                </button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#domain_create" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->global->create ?></button>
            <?php endif ?>
            </div>
        </div>
    </div>

    <?php if(count($data->domains)): ?>
        <?php foreach($data->domains as $row): ?>
            <?php

            /* Get some stats about the domain */
            $row->statistics = database()->query("SELECT COUNT(*) AS `total`, SUM(`clicks`) AS `clicks` FROM `links` WHERE `domain_id` = {$row->domain_id}")->fetch_object();

            ?>
            <div class="d-flex custom-row align-items-center my-4" data-domain-id="<?= $row->domain_id ?>">
                <div class="col-5">
                    <div class="font-weight-bold text-truncate h6">
                        <img src="https://external-content.duckduckgo.com/ip3/<?= $row->host ?>.ico" class="img-fluid icon-favicon mr-1" />
                        <span class="align-middle"><?= $row->host ?></span>
                    </div>

                    <div class="text-muted d-flex align-items-center"><i class="fa fa-fw fa-calendar-alt fa-sm mr-1"></i> <?= \Altum\Date::get($row->datetime, 2) ?></div>
                </div>

                <div class="col-4 d-flex flex-column flex-lg-row justify-content-lg-between">
                    <div>
                        <span data-toggle="tooltip" title="<?= language()->domains->domains->total ?>" class="badge badge-info">
                            <i class="fa fa-fw fa-link mr-1"></i> <?= nr($row->statistics->total) ?>
                        </span>
                    </div>

                    <div>
                        <span data-toggle="tooltip" title="<?= language()->domains->domains->clicks ?>"class="badge badge-primary">
                            <i class="fa fa-fw fa-chart-bar mr-1"></i> <?= nr($row->statistics->clicks) ?>
                        </span>
                    </div>
                </div>

                <div class="col-2">
                    <?php if($row->is_enabled): ?>
                        <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-sm fa-check"></i> <?= language()->domains->domains->is_enabled_active ?></span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-sm fa-eye-slash"></i> <?= language()->domains->domains->is_enabled_pending ?></span>
                    <?php endif ?>
                </div>

                <div class="col-1 d-flex justify-content-end">
                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown">
                            <i class="fa fa-fw fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" data-toggle="modal" data-target="#domain_update" data-domain-id="<?= $row->domain_id ?>" data-scheme="<?= $row->scheme ?>" data-host="<?= $row->host ?>" data-custom-index-url="<?= $row->custom_index_url ?>" data-custom-not-found-url="<?= $row->custom_not_found_url ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= language()->global->edit ?></a>
                            <a href="#" data-toggle="modal" data-target="#domain_delete" data-domain-id="<?= $row->domain_id ?>" class="dropdown-item"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= language()->domains->domains->no_data ?>" />
            <h2 class="h4 text-muted"><?= language()->domains->domains->no_data ?></h2>

            <?php if($this->user->plan_settings->domains_limit != -1 && $data->total_domains < $this->user->plan_settings->domains_limit): ?>
            <p><a href="#" data-toggle="modal" data-target="#domain_create"><?= language()->domains->domains->no_data_help ?></a></p>
            <?php endif ?>
        </div>
    <?php endif ?>
</section>



