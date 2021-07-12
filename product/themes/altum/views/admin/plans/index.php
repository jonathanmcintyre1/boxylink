<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= $this->language->admin_plans->header ?></h1>

    <div class="col-auto p-0">
        <a href="<?= url('admin/plan-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= $this->language->admin_plans->create ?></a>
    </div>
</div>

<?php display_notifications() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= $this->language->admin_plans->table->name ?></th>
            <th><?= $this->language->admin_plans->table->monthly_price ?></th>
            <th><?= $this->language->admin_plans->table->annual_price ?></th>
            <th><?= $this->language->admin_plans->table->users ?></th>
            <th><?= $this->language->admin_plans->table->status ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <a href="<?= url('admin/plan-update/free') ?>"><?= $this->settings->plan_free->name ?></a>
                <a href="<?= url('pay/free') ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
            </td>
            <td>-</td>
            <td>-</td>
            <td>
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=free') ?>">
                    <?= nr($this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'free'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td>
                <?php if($this->settings->plan_free->status == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?></span>
                <?php elseif($this->settings->plan_free->status == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?></span>
                <?php else: ?>
                    <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span>
                <?php endif ?>
            </td>
            <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_plan_dropdown_button.php', ['id' => 'free']) ?></td>
        </tr>

        <tr>
            <td>
                <a href="<?= url('admin/plan-update/trial') ?>"><?= $this->settings->plan_trial->name ?></a>
                <a href="<?= url('pay/trial') ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
            </td>
            <td>-</td>
            <td>-</td>
            <td>
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=trial') ?>">
                    <?= nr($this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'trial'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td>
                <?php if($this->settings->plan_trial->status == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?></span>
                <?php elseif($this->settings->plan_trial->status == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?></span>
                <?php else: ?>
                    <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span>
                <?php endif ?>
            </td>
            <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_plan_dropdown_button.php', ['id' => 'trial']) ?></td>
        </tr>

        <tr>
            <td>
                <?= $this->settings->plan_custom->name ?>
                <span data-toggle="tooltip" title="<?= $this->language->admin_plans->table->custom_help ?>"><i class="fa fa-fw fa-info-circle text-muted"></i></span>
            </td>
            <td>-</td>
            <td>-</td>
            <td>
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=custom') ?>">
                    <?= nr($this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'custom'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td><span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span></td>
            <td></td>
        </tr>

        <?php while($row = $data->plans_result->fetch_object()): ?>

            <tr data-plan-id="<?= $row->plan_id ?>">
                <td>
                    <a href="<?= url('admin/plan-update/' . $row->plan_id) ?>"><?= $row->name ?></a>
                    <?php if($row->status != 0): ?>
                        <a href="<?= url('pay/' . $row->plan_id) ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
                    <?php endif ?>
                </td>
                <td><?= $row->monthly_price . ' ' . $this->settings->payment->currency ?></td>
                <td><?= $row->annual_price . ' ' . $this->settings->payment->currency ?></td>
                <td>
                    <i class="fa fa-fw fa-users text-muted"></i>
                    <a href="<?= url('admin/users?plan_id=' . $row->plan_id) ?>">
                        <?= nr($this->database->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = '{$row->plan_id}'")->fetch_object()->total ?? 0) ?>
                    </a>
                </td>
                <td>
                    <?php if($row->status == 0): ?>
                        <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->disabled ?></span>
                    <?php elseif($row->status == 1): ?>
                        <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= $this->language->global->active ?></span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= $this->language->global->hidden ?></span>
                    <?php endif ?>
                </td>
                <td><?= include_view(THEME_PATH . 'views/admin/partials/admin_plan_dropdown_button.php', ['id' => $row->plan_id]) ?></td>
            </tr>

        <?php endwhile ?>
        </tbody>
    </table>
</div>
