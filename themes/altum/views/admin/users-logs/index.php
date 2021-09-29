<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-scroll text-primary-900 mr-2"></i> <?= language()->admin_users_logs->header ?></h1>

    <div class="col-auto d-flex">
        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" title="<?= language()->global->export ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu  dropdown-menu-right">
                    <a href="<?= url('admin/users-logs?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= language()->global->export_csv ?>
                    </a>
                    <a href="<?= url('admin/users-logs?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                            <a href="<?= url('admin/users-logs') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
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
                                <option value="type" <?= $data->filters->search_by == 'type' ? 'selected="selected"' : null ?>><?= language()->admin_users_logs->filters->search_by_type ?></option>
                                <option value="ip" <?= $data->filters->search_by == 'ip' ? 'selected="selected"' : null ?>><?= language()->admin_users_logs->filters->search_by_ip ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="order_by" class="small"><?= language()->global->filters->order_by ?></label>
                            <select name="order_by" id="order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
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

        <div class="ml-3">
            <button id="bulk_enable" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= language()->global->bulk_actions ?>"><i class="fa fa-fw fa-sm fa-list"></i></button>

            <div id="bulk_group" class="btn-group d-none" role="group">
                <div class="btn-group" role="group">
                    <button id="bulk_actions" type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= language()->global->bulk_actions ?> <span id="bulk_counter" class="d-none"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="bulk_actions">
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><?= language()->global->delete ?></a>
                    </div>
                </div>

                <button id="bulk_disable" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= language()->global->close ?>"><i class="fa fa-fw fa-times"></i></button>
            </div>
        </div>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<form id="table" action="<?= SITE_URL . 'admin/users-logs/bulk' ?>" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
    <input type="hidden" name="type" value="" data-bulk-type />

    <div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th data-bulk-table class="d-none">
                <div class="custom-control custom-checkbox">
                    <input id="bulk_select_all" type="checkbox" class="custom-control-input" />
                    <label class="custom-control-label" for="bulk_select_all"></label>
                </div>
            </th>
            <th><?= language()->admin_users_logs->table->user ?></th>
            <th><?= language()->admin_users_logs->table->type ?></th>
            <th><?= language()->admin_users_logs->table->ip ?></th>
            <th><?= language()->admin_users_logs->table->details ?></th>
            <th><?= language()->admin_users_logs->table->datetime ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data->users_logs as $row): ?>
            <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = $row->ip = 'hidden on demo';} ?>
            <tr>
                <td data-bulk-table class="d-none">
                    <div class="custom-control custom-checkbox">
                        <input id="selected_id_<?= $row->id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->id ?>" />
                        <label class="custom-control-label" for="selected_id_<?= $row->id ?>"></label>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <div>
                            <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                        </div>

                        <span class="text-muted"><?= $row->user_email ?></span>
                    </div>
                </td>
                <td>
                    <span><?= $row->type ?></span>
                </td>
                <td>
                    <span><?= $row->ip ?></span>
                </td>
                <td>
                    <?php if($row->device_type): ?>
                        <span class="mr-2" data-toggle="tooltip" title="<?= $row->device_type ?>">
                            <i class="fa fa-fw fa-<?= $row->device_type ?> text-muted"></i>
                        </span>
                    <?php endif ?>

                    <?php if($row->os_name): ?>
                        <span class="mr-2" data-toggle="tooltip" title="<?= $row->os_name ?>">
                            <i class="fa fa-fw fa-server text-muted"></i>
                        </span>
                    <?php endif ?>

                    <?php if($row->country_code): ?>
                        <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($row->country_code) . '.svg' ?>" class="img-fluid icon-favicon mr-2" data-toggle="tooltip" title="<?= get_country_from_country_code($row->country_code) ?>" />
                    <?php endif ?>
                </td>
                <td>
                    <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime) ?>">
                        <?= \Altum\Date::get($row->datetime, 2) ?>
                    </span>
                </td>
                <td>
                    <?= include_view(THEME_PATH . 'views/admin/users-logs/admin_user_log_dropdown_button.php', ['id' => $row->id]) ?>
                </td>
            </tr>
        <?php endforeach ?>

        </tbody>
    </table>
</div>
</form>

<div class="mt-3"><?= $data->pagination ?></div>

<?php require THEME_PATH . 'views/admin/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/partials/bulk_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users-logs/user_log_delete_modal.php'), 'modals'); ?>
