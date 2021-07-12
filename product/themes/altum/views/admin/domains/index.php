<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-globe text-primary-900 mr-2"></i> <?= $this->language->admin_domains->header ?></h1>

    <div class="col-auto d-flex">
        <div>
            <a href="<?= url('admin/domain-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_domain_create->menu ?></a>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= $this->language->global->filters->header ?></span>

                        <?php if(count($data->filters->get)): ?>
                            <a href="<?= url('admin/domains') ?>" class="text-muted"><?= $this->language->global->filters->reset ?></a>
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
                                <option value="host" <?= $data->filters->search_by == 'host' ? 'selected="selected"' : null ?>><?= $this->language->admin_domains->filters->search_by_host ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="is_enabled" class="small"><?= $this->language->global->filters->status ?></label>
                            <select name="is_enabled" id="is_enabled" class="form-control form-control-sm">
                                <option value=""><?= $this->language->global->filters->all ?></option>
                                <option value="1" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '1' ? 'selected="selected"' : null ?>><?= $this->language->global->active ?></option>
                                <option value="0" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '0' ? 'selected="selected"' : null ?>><?= $this->language->global->disabled ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="order_by" class="small"><?= $this->language->global->filters->order_by ?></label>
                            <select name="order_by" id="order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= $this->language->global->filters->order_by_datetime ?></option>
                                <option value="host" <?= $data->filters->order_by == 'host' ? 'selected="selected"' : null ?>><?= $this->language->admin_domains->filters->order_by_host ?></option>
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

<?php display_notifications() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_domains->table->user ?></th>
            <th><?= $this->language->admin_domains->table->host ?></th>
            <th><?= $this->language->admin_domains->table->is_enabled ?></th>
            <th><?= $this->language->admin_domains->table->links ?></th>
            <th><?= $this->language->admin_domains->table->datetime ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data->domains as $row): ?>
            <tr>
                <td>
                    <div class="d-flex flex-column">
                        <div>
                            <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                        </div>

                        <span class="text-muted"><?= $row->user_email ?></span>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <div>
                            <img src="https://external-content.duckduckgo.com/ip3/<?= $row->host ?>.ico" class="img-fluid icon-favicon mr-1" />
                            <a href="<?= url('admin/domain-update/' . $row->domain_id) ?>"><?= $row->host ?></a>
                            <a href="<?= $row->scheme . $row->host ?>" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
                        </div>
                        <span class="text-muted">
                        <?php if($row->type == 1): ?>
                            <i class="fa fa-fw fa-sm fa-globe"></i> <?= $this->language->admin_domains->main->type_global ?>
                        <?php elseif($row->type == 0): ?>
                            <i class="fa fa-fw fa-sm fa-user"></i> <?= $this->language->admin_domains->main->type_user ?>
                        <?php endif ?>
                    </span>
                    </div>
                </td>
                <td>
                    <?php if($row->is_enabled == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?>
                    <?php elseif($row->is_enabled == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?>
                    <?php endif ?>
                </td>
                <td>
                    <a href="<?= url('admin/links?domain_id=' . $row->domain_id) ?>"><?= $this->language->global->view ?></a>
                </td>
                <td>
                <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->date) ?>">
                    <?= \Altum\Date::get($row->date, 2) ?>
                </span>
                </td>
                <td>
                    <?= include_view(THEME_PATH . 'views/admin/partials/admin_domain_dropdown_button.php', ['id' => $row->domain_id]) ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="mt-3"><?= $data->pagination ?></div>
