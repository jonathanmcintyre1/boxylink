<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-dollar-sign text-primary-900 mr-2"></i> <?= $this->language->admin_payments->header ?></h1>

    <div class="col-auto d-flex">
        <div class="">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu  dropdown-menu-right">
                    <a href="<?= url('admin/payments?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= $this->language->global->export_csv ?>
                    </a>
                    <a href="<?= url('admin/payments?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-code mr-1"></i> <?= $this->language->global->export_json ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= $this->language->global->filters->header ?></span>

                        <?php if(count($data->filters->get)): ?>
                            <a href="<?= url('admin/payments') ?>" class="text-muted"><?= $this->language->global->filters->reset ?></a>
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
                                <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->search_by_name ?></option>
                                <option value="email" <?= $data->filters->search_by == 'email' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->search_by_email ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="status" class="small"><?= $this->language->admin_payments->filters->status ?></label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value=""><?= $this->language->global->filters->all ?></option>
                                <option value="1" <?= isset($data->filters->filters['status']) && $data->filters->filters['status'] == '1' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->status_paid ?></option>
                                <option value="0" <?= isset($data->filters->filters['status']) && $data->filters->filters['status'] == '0' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->status_pending ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="plan_id" class="small"><?= $this->language->admin_payments->filters->plan_id ?></label>
                            <select name="plan_id" id="plan_id" class="form-control form-control-sm">
                                <option value=""><?= $this->language->global->filters->all ?></option>
                                <?php foreach($data->plans as $plan): ?>
                                    <option value="<?= $plan->plan_id ?>" <?= isset($data->filters->filters['plan_id']) && $data->filters->filters['plan_id'] == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="type" class="small"><?= $this->language->pay->custom_plan->payment_type ?></label>
                            <select name="type" id="type" class="form-control form-control-sm">
                                <option value=""><?= $this->language->global->filters->all ?></option>
                                <option value="recurring" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'recurring' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->recurring_type ?></option>
                                <option value="one_time" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'one_time' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->one_time_type ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="processor" class="small"><?= $this->language->pay->custom_plan->payment_processor ?></label>
                            <select name="processor" id="processor" class="form-control form-control-sm">
                                <option value=""><?= $this->language->global->filters->all ?></option>
                                <option value="stripe" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'stripe' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->stripe ?></option>
                                <option value="paypal" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'paypal' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->paypal ?></option>
                                <option value="offline_payment" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'offline_payment' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->offline_payment ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="frequency" class="small"><?= $this->language->pay->custom_plan->payment_frequency ?></label>
                            <select name="frequency" id="frequency" class="form-control form-control-sm">
                                <option value=""><?= $this->language->global->filters->all ?></option>
                                <option value="monthly" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'monthly' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->monthly ?></option>
                                <option value="annual" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'annual' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->annual ?></option>
                                <option value="lifetime" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'lifetime' ? 'selected="selected"' : null ?>><?= $this->language->pay->custom_plan->lifetime ?></option>
                            </select>
                        </div>


                        <div class="form-group px-4">
                            <label for="order_by" class="small"><?= $this->language->global->filters->order_by ?></label>
                            <select name="order_by" id="order_by" class="form-control form-control-sm">
                                <option value="date" <?= $data->filters->order_by == 'date' ? 'selected="selected"' : null ?>><?= $this->language->global->filters->order_by_datetime ?></option>
                                <option value="total_amount" <?= $data->filters->order_by == 'total_amount' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->order_by_total_amount ?></option>
                                <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->order_by_name ?></option>
                                <option value="email" <?= $data->filters->order_by == 'email' ? 'selected="selected"' : null ?>><?= $this->language->admin_payments->filters->order_by_email ?></option>
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
            <th><?= $this->language->admin_payments->table->user ?></th>
            <th><?= $this->language->admin_payments->table->payer ?></th>
            <th><?= $this->language->admin_payments->table->type ?></th>
            <th><?= $this->language->admin_payments->table->total_amount ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data->payments as $row): ?>
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
                        <span><?= $row->name ?></span>
                        <span class="text-muted"><?= $row->email ?></span>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span><?= $this->language->pay->custom_plan->{$row->type . '_type'} ?></span>
                        <div>
                            <span class="text-muted"><?= $this->language->pay->custom_plan->{$row->frequency} ?></span> - <span class="text-muted"><?= $this->language->pay->custom_plan->{$row->processor} ?></span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <span class=""><?= nr($row->total_amount) . ' ' . $row->currency ?></span>
                        <div>
                            <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->date) ?>">
                                <?= \Altum\Date::get($row->date, 2) ?>
                            </span>
                        </div>
                    </div>
                </td>
                <td>
                    <?= include_view(THEME_PATH . 'views/admin/partials/admin_payment_dropdown_button.php', [
                        'id' => $row->id,
                        'payment_proof' => $row->payment_proof,
                        'processor' => $row->processor,
                        'status' => $row->status
                    ]) ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="mt-3"><?= $data->pagination ?></div>
