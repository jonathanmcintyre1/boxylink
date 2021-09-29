<?php defined('ALTUMCODE') || die() ?>

<div class="row mb-4">
    <div class="col-12 col-xl mb-3 mb-xl-0">
        <h1 class="h4 m-0"><?= language()->links->header ?></h1>
    </div>

    <div class="col-12 col-xl-auto d-flex">
        <div>
            <?php if(settings()->links->shortener_is_enabled): ?>
                <div class="dropdown">
                    <button type="button" data-toggle="dropdown" class="btn btn-primary dropdown-toggle dropdown-toggle-simple">
                        <i class="fa fa-fw fa-plus-circle"></i> <?= language()->links->create ?>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#create_biolink">
                            <i class="fa fa-fw fa-circle fa-sm mr-1" style="color: <?= language()->link->biolink->color ?>"></i>

                            <?= language()->link->biolink->name ?>
                        </a>

                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#create_link">
                            <i class="fa fa-fw fa-circle fa-sm mr-1" style="color: <?= language()->link->link->color ?>"></i>

                            <?= language()->link->link->name ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <button type="button" data-toggle="modal" data-target="#create_biolink" class="btn btn-primary">
                    <i class="fa fa-fw fa-plus-circle"></i> <?= language()->links->create ?>
                </button>
            <?php endif ?>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" title="<?= language()->global->export ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?= url('links?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= language()->global->export_csv ?>
                    </a>
                    <a href="<?= url('links?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= language()->global->export_json ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= language()->global->filters->header ?></span>

                        <?php if(count($data->filters->get)): ?>
                            <a href="<?= url('links') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
                        <?php endif ?>
                    </div>

                    <div class="dropdown-divider"></div>

                    <form action="<?= url('links') ?>" method="get" role="form">
                        <div class="form-group px-4">
                            <label for="search" class="small"><?= language()->global->filters->search ?></label>
                            <input type="search" name="search" id="search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                        </div>

                        <div class="form-group px-4">
                            <label for="search_by" class="small"><?= language()->global->filters->search_by ?></label>
                            <select name="search_by" id="search_by" class="form-control form-control-sm">
                                <option value="url" <?= $data->filters->search_by == 'url' ? 'selected="selected"' : null ?>><?= language()->links->filters->search_by_url ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="is_enabled" class="small"><?= language()->global->filters->status ?></label>
                            <select name="is_enabled" id="is_enabled" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <option value="1" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '1' ? 'selected="selected"' : null ?>><?= language()->global->active ?></option>
                                <option value="0" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '0' ? 'selected="selected"' : null ?>><?= language()->global->disabled ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="project_id" class="small">
                                <?= language()->links->filters->project_id ?>
                                <a href="<?= url('projects') ?>" target="_blank" class="ml-3 small"><?= language()->projects->create ?></a>
                            </label>
                            <select name="project_id" id="project_id" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <?php foreach($data->projects as $row): ?>
                                    <option value="<?= $row->project_id ?>" <?= isset($data->filters->filters['project_id']) && $data->filters->filters['project_id'] == $row->project_id ? 'selected="selected"' : null ?>><?= $row->name ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="type" class="small"><?= language()->links->filters->type ?></label>
                            <select name="type" id="type" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <option value="biolink" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'biolink' ? 'selected="selected"' : null ?>><?= language()->links->filters->type_biolink ?></option>
                                <option value="link" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'link' ? 'selected="selected"' : null ?>><?= language()->links->filters->type_link ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="order_by" class="small"><?= language()->global->filters->order_by ?></label>
                            <select name="order_by" id="order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
                                <option value="clicks" <?= $data->filters->order_by == 'clicks' ? 'selected="selected"' : null ?>><?= language()->links->filters->order_by_clicks ?></option>
                                <option value="url" <?= $data->filters->order_by == 'url' ? 'selected="selected"' : null ?>><?= language()->links->filters->order_by_url ?></option>
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

<?php if(count($data->links)): ?>

    <?php foreach($data->links as $row): ?>

        <div class="custom-row my-4 <?= $row->is_enabled ? null : 'custom-row-inactive' ?>">
            <div class="row">
                <div class="col-8 col-lg-5">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 d-flex align-items-center">
                            <span class="fa-stack fa-1x" data-toggle="tooltip" title="<?= language()->link->{$row->type}->name ?>">
                                <i class="fa fa-circle fa-stack-2x" style="color: <?= language()->link->{$row->type}->color ?>"></i>
                                <i class="fas <?= language()->link->{$row->type}->icon ?> fa-stack-1x fa-inverse"></i>
                            </span>
                        </div>

                        <div class="d-flex flex-column" style="min-width: 0;">
                            <div class="d-inline-block text-truncate">
                                <a href="<?= url('link/' . $row->link_id) ?>" class="font-weight-bold"><?= $row->url ?></a>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="d-inline-block text-truncate">
                                <?php if(!empty($row->location_url)): ?>
                                    <img src="https://external-content.duckduckgo.com/ip3/<?= parse_url($row->location_url)['host'] ?>.ico" class="img-fluid icon-favicon mr-1" />
                                    <a href="<?= $row->location_url ?>" class="text-muted align-middle" target="_blank" rel="noreferrer"><?= $row->location_url ?></a>
                                <?php else: ?>
                                    <img src="https://external-content.duckduckgo.com/ip3/<?= parse_url($row->full_url)['host'] ?>.ico" class="img-fluid icon-favicon mr-1" />
                                    <a href="<?= $row->full_url ?>" class="text-muted align-middle" target="_blank" rel="noreferrer"><?= $row->full_url ?></a>
                                <?php endif ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col col-lg-3 d-none d-lg-flex flex-lg-row justify-content-lg-between align-items-center">
                    <div>
                        <?php if($row->project_id): ?>
                            <a href="<?= url('links?project_id=' . $row->project_id) ?>" class="text-decoration-none">
                                <span class="py-1 px-2 border rounded text-muted small" style="border-color: <?= $data->projects[$row->project_id]->color ?> !important;">
                                    <?= $data->projects[$row->project_id]->name ?>
                                </span>
                            </a>
                        <?php endif ?>
                    </div>

                    <div>
                        <a href="<?= url('link/' . $row->link_id . '/statistics') ?>">
                            <span data-toggle="tooltip" title="<?= language()->links->clicks ?>"><span class="badge badge-light"><i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= nr($row->clicks) ?></span></span>
                        </a>
                    </div>
                </div>

                <div class="col col-lg-2 d-none d-lg-flex justify-content-lg-end align-items-center">
                    <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime) ?>"><i class="fa fa-fw fa-calendar-alt fa-sm mr-1"></i> <span class="align-middle"><?= \Altum\Date::get($row->datetime, 2) ?></span></small>
                </div>

                <div class="col-4 col-lg-2 d-flex justify-content-center justify-content-lg-end align-items-center">
                    <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= language()->links->is_enabled_tooltip ?>">
                        <input
                                type="checkbox"
                                class="custom-control-input"
                                id="link_is_enabled_<?= $row->link_id ?>"
                                data-row-id="<?= $row->link_id ?>"
                                onchange="ajax_call_helper(event, 'link-ajax', 'is_enabled_toggle')"
                            <?= $row->is_enabled ? 'checked="checked"' : null ?>
                        >
                        <label class="custom-control-label clickable" for="link_is_enabled_<?= $row->link_id ?>"></label>
                    </div>

                    <button
                            id="url_copy"
                            type="button"
                            class="btn btn-link text-muted"
                            data-toggle="tooltip"
                            title="<?= language()->global->clipboard_copy ?>"
                            aria-label="<?= language()->global->clipboard_copy ?>"
                            data-copy="<?= language()->global->clipboard_copy ?>"
                            data-copied="<?= language()->global->clipboard_copied ?>"
                            data-clipboard-text="<?= $row->full_url ?>"
                    >
                        <i class="fa fa-fw fa-sm fa-copy"></i>
                    </button>

                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown">
                            <i class="fa fa-fw fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?= url('link/' . $row->link_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= language()->global->edit ?></a>
                            <a href="<?= url('link/' . $row->link_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-chart-bar"></i> <?= language()->link->statistics->link ?></a>
                            <a href="<?= url('link/' . $row->link_id . '/qr') ?>" class="dropdown-item"><i class="fa fa-fw fa-qrcode"></i> <?= language()->link->qr->link ?></a>
                            <a href="#" data-toggle="modal" data-target="#link_delete" class="dropdown-item" data-link-id="<?= $row->link_id ?>"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>

    <div class="mt-3"><?= $data->pagination ?></div>

<?php else: ?>

    <div class="d-flex flex-column align-items-center justify-content-center mt-5">
        <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-4" alt="<?= language()->links->no_data ?>" />
        <h2 class="h4 text-muted mb-4"><?= language()->links->no_data ?></h2>
    </div>

<?php endif ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
