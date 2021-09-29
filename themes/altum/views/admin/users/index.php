<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-users text-primary-900 mr-2"></i> <?= language()->admin_users->header ?></h1>

    <div class="col-auto d-flex">

        <div class="">
            <a href="<?= url('admin/user-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->admin_user_create->menu ?></a>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" title="<?= language()->global->export ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu  dropdown-menu-right">
                    <a href="<?= url('admin/users?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= language()->global->export_csv ?>
                    </a>
                    <a href="<?= url('admin/users?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= language()->global->export_json ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" title="<?= language()->global->filters->header ?>">
                    <i class="fa fa-fw fa-sm fa-filter"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= language()->global->filters->header ?></span>

                        <?php if(count($data->filters->get)): ?>
                            <a href="<?= url('admin/users') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
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
                                <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->search_by_name ?></option>
                                <option value="email" <?= $data->filters->search_by == 'email' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->search_by_email ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="active" class="small"><?= language()->admin_users->filters->is_enabled ?></label>
                            <select name="active" id="active" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <option value="1" <?= isset($data->filters->filters['active']) && $data->filters->filters['active'] == '1' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->is_enabled_active ?></option>
                                <option value="0" <?= isset($data->filters->filters['active']) && $data->filters->filters['active'] == '0' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->is_enabled_unconfirmed ?></option>
                                <option value="2" <?= isset($data->filters->filters['active']) && $data->filters->filters['active'] == '2' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->is_enabled_disabled ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="plan_id" class="small"><?= language()->admin_users->filters->plan_id ?></label>
                            <select name="plan_id" id="plan_id" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <?php foreach($data->plans as $plan): ?>
                                    <option value="<?= $plan->plan_id ?>" <?= isset($data->filters->filters['plan_id']) && $data->filters->filters['plan_id'] == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="country" class="small"><?= language()->admin_users->filters->country ?></label>
                            <select name="country" id="country" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <?php foreach(get_countries_array() as $country => $country_name): ?>
                                    <option value="<?= $country ?>" <?= isset($data->filters->filters['country']) && $data->filters->filters['country'] == $country ? 'selected="selected"' : null ?>><?= $country_name ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="order_by" class="small"><?= language()->global->filters->order_by ?></label>
                            <select name="order_by" id="order_by" class="form-control form-control-sm">
                                <option value="date" <?= $data->filters->order_by == 'date' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->order_by_date ?></option>
                                <option value="last_activity" <?= $data->filters->order_by == 'last_activity' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->order_by_last_activity ?></option>
                                <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->order_by_name ?></option>
                                <option value="email" <?= $data->filters->order_by == 'email' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->order_by_email ?></option>
                                <option value="total_logins" <?= $data->filters->order_by == 'total_logins' ? 'selected="selected"' : null ?>><?= language()->admin_users->filters->order_by_total_logins ?></option>
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

<form id="table" action="<?= SITE_URL . 'admin/users/bulk' ?>" method="post" role="form">
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
                <th><?= language()->admin_users->table->user ?></th>
                <th><?= language()->admin_users->table->active ?></th>
                <th><?= language()->admin_users->table->plan_id ?></th>
                <th><?= language()->admin_users->table->details ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->users as $row): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->email = 'hidden@demo.com'; $row->name = 'hidden on demo';} ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_user_id_<?= $row->user_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->user_id ?>" />
                            <label class="custom-control-label" for="selected_user_id_<?= $row->user_id ?>"></label>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex">
                            <img src="<?= get_gravatar($row->email) ?>" class="user-avatar rounded-circle mr-3" alt="" />

                            <div class="d-flex flex-column">
                                <div>
                                    <a href="<?= url('admin/user-view/' . $row->user_id) ?>" <?= $row->type == 1 ? 'class="font-weight-bold" data-toggle="tooltip" title="' . language()->admin_users->table->admin . '"' : null ?>><?= $row->name ?></a>
                                </div>

                                <span class="text-muted"><?= $row->email ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($row->active == 0): ?>
                        <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->admin_user_update->main->is_enabled_unconfirmed ?>
                        <?php elseif($row->active == 1): ?>
                        <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->admin_user_update->main->is_enabled_active ?>
                        <?php elseif($row->active == 2): ?>
                        <span class="badge badge-pill badge-light"><i class="fa fa-fw fa-times"></i> <?= language()->admin_user_update->main->is_enabled_disabled ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span><?= $data->plans[$row->plan_id]->name ?></span>

                            <?php if($row->plan_id != 'free'): ?>
                                <div>
                                    <small class="text-muted" data-toggle="tooltip" title="<?= language()->admin_users->table->plan_expiration_date ?>"><?= \Altum\Date::get($row->plan_expiration_date) ?></small>
                                </div>
                            <?php endif ?>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(language()->admin_users->table->date, \Altum\Date::get($row->date)) ?>">
                                <i class="fa fa-fw fa-clock text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(language()->admin_users->table->last_activity, ($row->last_activity ? \Altum\Date::get($row->last_activity) : '-')) ?>">
                                <i class="fa fa-fw fa-history text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(language()->admin_users->table->total_logins, nr($row->total_logins)) ?>">
                                <i class="fa fa-fw fa-user-clock text-muted"></i>
                            </span>

                            <?php if($row->country): ?>
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($row->country) . '.svg' ?>" class="img-fluid icon-favicon mr-2" data-toggle="tooltip" title="<?= get_country_from_country_code($row->country) ?>" />
                            <?php else: ?>
                                <span class="mr-2" data-toggle="tooltip" title="<?= language()->admin_users->table->country_unknown ?>">
                                    <i class="fa fa-fw fa-globe text-muted"></i>
                                </span>
                            <?php endif ?>
                        </div>
                    </td>
                    <td><?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $row->user_id]) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</form>

<div class="mt-3"><?= $data->pagination ?></div>

<?php require THEME_PATH . 'views/admin/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/partials/bulk_delete_modal.php'), 'modals'); ?>
