<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <small>
            <ol class="custom-breadcrumbs">
                <li><a href="<?= url('dashboard') ?>"><?= language()->dashboard->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= language()->pixels->breadcrumb ?></li>
            </ol>
        </small>
    </nav>

    <div class="row mb-4">
        <div class="col-12 col-xl mb-3 mb-xl-0">
            <h2 class="h4"><?= language()->pixels->header ?></h2>
            <p class="text-muted"><?= language()->pixels->subheader ?></p>
        </div>

        <div class="col-12 col-xl-auto d-flex">
            <div>
            <?php if($this->user->plan_settings->pixels_limit != -1 && $data->total_pixels >= $this->user->plan_settings->pixels_limit): ?>
                <button type="button" data-toggle="tooltip" title="<?= language()->pixels->error_message->pixels_limit ?>" class="btn btn-primary disabled">
                    <i class="fa fa-fw fa-plus-circle"></i> <?= language()->pixels->create ?>
                </button>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_pixel" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->pixels->create ?></button>
            <?php endif ?>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= language()->global->filters->header ?></span>

                            <?php if(count($data->filters->get)): ?>
                                <a href="<?= url('pixels') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="search" class="small"><?= language()->global->filters->search ?></label>
                                <input type="search" name="search" id="search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="search_by" class="small"><?= language()->global->filters->search_by ?></label>
                                <select name="search_by" id="search_by" class="form-control form-control-sm">
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= language()->pixels->filters->search_by_name ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="type" class="small"><?= language()->pixels->filters->type ?></label>
                                <select name="type" id="type" class="form-control form-control-sm">
                                    <option value=""><?= language()->global->filters->all ?></option>
                                    <?php foreach(require APP_PATH . 'includes/pixels.php' as $pixel): ?>
                                        <option value="<?= $pixel ?>" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == $pixel ? 'selected="selected"' : null ?>><?= language()->pixels->pixels->{$pixel} ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_by" class="small"><?= language()->global->filters->order_by ?></label>
                                <select name="order_by" id="order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= language()->pixels->filters->order_by_name ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="order_type" class="small"><?= language()->global->filters->order_type ?></label>
                                <select name="order_type" id="order_type" class="form-control form-control-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_type_asc ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_type_desc ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="results_per_page" class="small"><?= language()->global->filters->results_per_page ?></label>
                                <select name="results_per_page" id="results_per_page" class="form-control form-control-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= language()->global->submit ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($data->pixels)): ?>

        <?php foreach($data->pixels as $row): ?>
            <div class="custom-row my-4" data-pixel-id="<?= $row->pixel_id ?>">
                <div class="row">
                    <div class="col-4 col-lg-4 d-flex align-items-center">
                        <div class="font-weight-bold text-truncate">
                            <a href="#" data-toggle="modal" data-target="#pixel_update" data-pixel-id="<?= $row->pixel_id ?>" data-name="<?= $row->name ?>" data-type="<?= $row->type ?>" data-pixel="<?= $row->pixel ?>"><?= $row->name ?></a>
                        </div>
                    </div>

                    <div class="col-4 col-lg-4 d-flex align-items-center">
                        <?= language()->pixels->pixels->{$row->type} ?>
                    </div>

                    <div class="col-2 col-lg-2 d-none d-lg-flex justify-content-center justify-content-lg-end align-items-center">
                        <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime) ?>"><i class="fa fa-fw fa-calendar-alt fa-sm mr-1"></i> <span class="align-middle"><?= \Altum\Date::get($row->datetime, 2) ?></span></small>
                    </div>

                    <div class="col-2 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown">
                            <i class="fa fa-fw fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="#" data-toggle="modal" data-target="#pixel_update" data-pixel-id="<?= $row->pixel_id ?>" data-name="<?= $row->name ?>" data-type="<?= $row->type ?>" data-pixel="<?= $row->pixel ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= language()->global->edit ?></a>
                            <a href="#" data-toggle="modal" data-target="#pixel_delete" data-pixel-id="<?= $row->pixel_id ?>" class="dropdown-item"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></a>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        <?php endforeach ?>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center mt-5">
            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-4" alt="<?= language()->pixels->no_data ?>" />
            <h2 class="h4 mb-5 text-muted"><?= language()->pixels->no_data ?></h2>
        </div>
    <?php endif ?>

</section>

