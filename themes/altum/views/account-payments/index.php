<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="d-flex justify-content-between">
        <div>
            <h2 class="h4"><?= language()->account_payments->header ?></h2>
            <p class="text-muted"><?= language()->account_payments->subheader ?></p>
        </div>

        <?php if(count($data->payments) || count($data->filters->get)): ?>
            <div class="col-auto p-0 d-flex">
                <div class="ml-3">
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" title="<?= language()->global->export ?>">
                            <i class="fa fa-fw fa-sm fa-download"></i>
                        </button>

                        <div class="dropdown-menu  dropdown-menu-right">
                            <a href="<?= url('account-payments?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                                <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= language()->global->export_csv ?>
                            </a>
                            <a href="<?= url('account-payments?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                                    <a href="<?= url('account-payments') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
                                <?php endif ?>
                            </div>

                            <div class="dropdown-divider"></div>

                            <form action="" method="get" role="form">
                                <div class="form-group px-4">
                                    <label for="processor" class="small"><?= language()->account_payments->filters->processor ?></label>
                                    <select name="processor" id="processor" class="form-control form-control-sm">
                                        <option value=""><?= language()->global->filters->all ?></option>
                                        <option value="paypal" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'paypal' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->processor_paypal ?></option>
                                        <option value="stripe" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'stripe' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->processor_stripe ?></option>
                                        <option value="offline_payment" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'offline_payment' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->processor_offline_payment ?></option>
                                        <option value="coinbase" <?= isset($data->filters->filters['processor']) && $data->filters->filters['processor'] == 'coinbase' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->processor_coinbase ?></option>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="type" class="small"><?= language()->account_payments->filters->type ?></label>
                                    <select name="type" id="type" class="form-control form-control-sm">
                                        <option value=""><?= language()->global->filters->all ?></option>
                                        <option value="one_time" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'one_time' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->type_one_time ?></option>
                                        <option value="recurring" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'recurring' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->type_recurring ?></option>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="frequency" class="small"><?= language()->account_payments->filters->frequency ?></label>
                                    <select name="frequency" id="frequency" class="form-control form-control-sm">
                                        <option value=""><?= language()->global->filters->all ?></option>
                                        <option value="monthly" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'monthly' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->frequency_monthly ?></option>
                                        <option value="annual" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'annual' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->frequency_annual ?></option>
                                        <option value="lifetime" <?= isset($data->filters->filters['frequency']) && $data->filters->filters['frequency'] == 'lifetime' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->frequency_lifetime ?></option>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="order_by" class="small"><?= language()->global->filters->order_by ?></label>
                                    <select name="order_by" id="order_by" class="form-control form-control-sm">
                                        <option value="date" <?= $data->filters->order_by == 'date' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
                                        <option value="total_amount" <?= $data->filters->order_by == 'total_amount' ? 'selected="selected"' : null ?>><?= language()->account_payments->filters->order_by_total_amount ?></option>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="order_type" class="small"><?= language()->global->filters->order_type ?></label>
                                    <select name="order_type" id="order_type" class="form-control form-control-sm">
                                        <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_type_asc ?></option>
                                        <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_type_desc ?></option>
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
        <?php endif ?>
    </div>

    <?php if(count($data->payments)): ?>
        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= language()->account_payments->payments->customer ?></th>
                    <th><?= language()->account_payments->payments->plan_id ?></th>
                    <th><?= language()->account_payments->payments->type ?></th>
                    <th><?= language()->account_payments->payments->total_amount ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data->payments as $row): ?>

                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span><?= $row->email ?></span>
                                <span class="text-muted"><?= $row->name ?></span>
                            </div>
                        </td>

                        <td><?= $row->plan_name ?></td>

                        <td>
                            <div class="d-flex flex-column">
                                <span><?= language()->pay->custom_plan->{$row->type . '_type'} ?></span>
                                <span class="text-muted"><?= language()->pay->custom_plan->{$row->processor} ?></span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span><span class="text-success"><?= $row->total_amount ?></span> <?= $row->currency ?></span>
                                <span class="text-muted"><span data-toggle="tooltip" title="<?= \Altum\Date::get($row->date, 1) ?>"><?= \Altum\Date::get($row->date, 2) ?></span></span>
                            </div>
                        </td>

                        <?php if($row->status): ?>
                            <?php if(settings()->business->invoice_is_enabled): ?>

                                <td>
                                    <a href="<?= url('invoice/' . $row->id) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-fw fa-sm fa-file-invoice"></i> <?= language()->account_payments->payments->invoice ?>
                                    </a>
                                </td>

                            <?php else: ?>

                                <td>
                                    <span class="badge badge-success"><?= language()->account_payments->payments->status_approved ?></span>
                                </td>

                            <?php endif ?>
                        <?php else: ?>

                            <td>
                                <span class="badge badge-warning"><?= language()->account_payments->payments->status_pending ?></span>
                            </td>

                        <?php endif ?>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>

    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= language()->account_payments->payments->no_data ?>" />
            <h2 class="h4 text-muted"><?= language()->account_payments->payments->no_data ?></h2>
        </div>
    <?php endif ?>
</section>
