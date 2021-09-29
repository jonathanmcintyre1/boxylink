<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-wallet text-primary-900 mr-2"></i> <?= language()->admin_affiliates_withdrawals->header ?></h1>

    <div class="col-auto d-flex">
        <div class="">
            <div class="dropdown">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" title="<?= language()->global->export ?>">
                    <i class="fa fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu  dropdown-menu-right">
                    <a href="<?= url('admin/payments?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item">
                        <i class="fa fa-fw fa-sm fa-file-csv mr-1"></i> <?= language()->global->export_csv ?>
                    </a>
                    <a href="<?= url('admin/payments?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item">
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
                            <a href="<?= url('admin/payments') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
                        <?php endif ?>
                    </div>

                    <div class="dropdown-divider"></div>

                    <form action="" method="get" role="form">
                        <div class="form-group px-4">
                            <label for="is_paid" class="small"><?= language()->admin_affiliates_withdrawals->filters->is_paid ?></label>
                            <select name="is_paid" id="is_paid" class="form-control form-control-sm">
                                <option value=""><?= language()->global->filters->all ?></option>
                                <option value="1" <?= isset($data->filters->filters['is_paid']) && $data->filters->filters['is_paid'] == '1' ? 'selected="selected"' : null ?>><?= language()->admin_affiliates_withdrawals->filters->is_paid_paid ?></option>
                                <option value="0" <?= isset($data->filters->filters['is_paid']) && $data->filters->filters['is_paid'] == '0' ? 'selected="selected"' : null ?>><?= language()->admin_affiliates_withdrawals->filters->is_paid_pending ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="order_by" class="small"><?= language()->global->filters->order_by ?></label>
                            <select name="order_by" id="order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
                                <option value="amount" <?= $data->filters->order_by == 'amount' ? 'selected="selected"' : null ?>><?= language()->admin_affiliates_withdrawals->filters->order_by_amount ?></option>
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

<?= \Altum\Alerts::output_alerts() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= language()->admin_affiliates_withdrawals->table->user ?></th>
            <th><?= language()->admin_affiliates_withdrawals->table->amount ?></th>
            <th><?= language()->admin_affiliates_withdrawals->table->is_paid ?></th>
            <th><?= language()->admin_affiliates_withdrawals->table->datetime ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data->affiliates_withdrawals as $row): ?>
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
                        <span><?= nr($row->amount, 2) . ' ' . settings()->payment->currency ?></span>
                        <small class="text-muted"><?= $row->note ?></small>
                    </div>
                </td>
                <td>
                    <?php if($row->is_paid): ?>
                        <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->admin_affiliates_withdrawals->table->is_paid_paid ?></span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->admin_affiliates_withdrawals->table->is_paid_pending ?></span>
                    <?php endif ?>
                </td>
                <td class="text-muted">
                    <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime) ?>">
                        <?= \Altum\Date::get($row->datetime, 2) ?>
                    </span>
                </td>
                <td>
                    <?= include_view(THEME_PATH . 'views/admin/affiliates-withdrawals/admin_affiliate_withdrawal_dropdown_button.php', [
                        'id' => $row->affiliate_withdrawal_id,
                        'is_paid' => $row->is_paid
                    ]) ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="mt-3"><?= $data->pagination ?></div>


<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/affiliates-withdrawals/affiliate_withdrawal_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/affiliates-withdrawals/affiliate_withdrawal_approve_modal.php'), 'modals'); ?>


