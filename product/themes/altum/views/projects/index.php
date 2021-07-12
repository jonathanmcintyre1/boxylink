<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">
    <?php display_notifications() ?>

    <nav aria-label="breadcrumb">
        <small>
            <ol class="custom-breadcrumbs">
                <li><a href="<?= url('dashboard') ?>"><?= $this->language->dashboard->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= $this->language->projects->breadcrumb ?></li>
            </ol>
        </small>
    </nav>

    <div class="d-flex justify-content-between">
        <div>
            <h2 class="h4"><?= $this->language->projects->header ?></h2>
            <p class="text-muted"><?= $this->language->projects->subheader ?></p>
        </div>

        <div class="col-auto p-0 d-flex">
            <div>
            <?php if($this->user->plan_settings->projects_limit != -1 && $data->projects_total >= $this->user->plan_settings->projects_limit): ?>
                <button type="button" data-confirm="<?= $this->language->projects->error_message->projects_limit ?>"  class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->projects->create ?></button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_project" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->projects->create ?></button>
            <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> rounded-pill filters-button dropdown-toggle-simple" data-toggle="dropdown"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= $this->language->global->filters->header ?></span>

                            <?php if(count($data->filters->get)): ?>
                                <a href="<?= url('dashboard') ?>" class="text-muted"><?= $this->language->global->filters->reset ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="search" class="small"><?= $this->language->global->filters->search ?></label>
                                <input type="text" name="search" id="search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="search_by" class="small"><?= $this->language->global->filters->search_by ?></label>
                                <select name="search_by" id="search_by" class="form-control form-control-sm">
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= $this->language->projects->filters->search_by_name ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_by" class="small"><?= $this->language->global->filters->order_by ?></label>
                                <select name="order_by" id="order_by" class="form-control form-control-sm">
                                    <option value="date" <?= $data->filters->order_by == 'date' ? 'selected="selected"' : null ?>><?= $this->language->global->filters->order_by_datetime ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= $this->language->projects->filters->order_by_name ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_type" class="small"><?= $this->language->global->filters->order_type ?></label>
                                <select name="order_type" id="order_type" class="form-control form-control-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= $this->language->global->filters->order_type_asc ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= $this->language->global->filters->order_type_desc ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="results_per_page" class="small"><?= $this->language->global->filters->results_per_page ?></label>
                                <select name="results_per_page" id="results_per_page" class="form-control form-control-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" class="btn btn-sm btn-primary btn-block"><?= $this->language->global->submit ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($data->projects)): ?>

        <?php foreach($data->projects as $row): ?>
            <?php

            /* Get some stats about the project */
            $row->statistics = $this->database->query("SELECT COUNT(*) AS `total`, SUM(`clicks`) AS `clicks` FROM `links` WHERE `project_id` = {$row->project_id}")->fetch_object();

            ?>
            <div class="custom-row my-4" data-project-id="<?= $row->project_id ?>">
                <div class="row">
                    <div class="col-6 col-lg-4 d-flex align-items-center">
                        <div class="font-weight-bold text-truncate">
                            <a href="<?= url('links?project_id=' . $row->project_id) ?>"><?= $row->name ?></a>
                        </div>
                    </div>

                    <div class="col-4 col-lg-3 d-flex flex-column flex-lg-row justify-content-lg-between align-items-center">
                        <div>
                            <span data-toggle="tooltip" title="<?= $this->language->links->total ?>" class="badge badge-info">
                                <i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= nr($row->statistics->total) ?>
                            </span>
                        </div>

                        <div>
                            <span data-toggle="tooltip" title="<?= $this->language->links->clicks ?>" class="badge badge-light">
                                <i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= nr($row->statistics->clicks) ?>
                            </span>
                        </div>
                    </div>

                    <div class="col-2 col-lg-3 d-none d-lg-flex justify-content-center justify-content-lg-end align-items-center">
                        <small class="text-muted" data-toggle="tooltip" title="<?= $this->language->links->date ?>"><i class="fa fa-fw fa-calendar-alt fa-sm mr-1"></i> <span class="align-middle"><?= \Altum\Date::get($row->date, 2) ?></span></small>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                    <div class="dropdown">
                        <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                            <i class="fa fa-ellipsis-v"></i>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="#" data-toggle="modal" data-target="#project_update" data-project-id="<?= $row->project_id ?>" data-name="<?= $row->name ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= $this->language->global->edit ?></a>
                                <a href="#" data-toggle="modal" data-target="#project_delete" data-project-id="<?= $row->project_id ?>" class="dropdown-item"><i class="fa fa-fw fa-times"></i> <?= $this->language->global->delete ?></a>
                            </div>
                        </a>
                    </div>
                </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center mt-5">
            <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-4" alt="<?= $this->language->projects->no_data ?>" />
            <h2 class="h4 mb-5 text-muted"><?= $this->language->projects->no_data ?></h2>
        </div>
    <?php endif ?>

</section>

