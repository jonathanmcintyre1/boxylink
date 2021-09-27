<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= language()->admin_plans->header ?></h1>

    <div class="col-auto p-0">
        <a href="<?= url('admin/plan-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->admin_plans->create ?></a>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th><?= language()->admin_plans->table->name ?></th>
            <th><?= language()->admin_plans->table->price ?></th>
            <th><?= language()->admin_plans->table->order ?></th>
            <th><?= language()->admin_plans->table->users ?></th>
            <th><?= language()->admin_plans->table->status ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <a href="<?= url('admin/plan-update/free') ?>"><?= settings()->plan_free->name ?></a>
                <a href="<?= url('pay/free') ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
            </td>
            <td>-</td>
            <td>-</td>
            <td>
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=free') ?>">
                    <?= nr(database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'free'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td>
                <?php if(settings()->plan_free->status == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->disabled ?></span>
                <?php elseif(settings()->plan_free->status == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->global->active ?></span>
                <?php else: ?>
                    <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->hidden ?></span>
                <?php endif ?>
            </td>
            <td><?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => 'free']) ?></td>
        </tr>

        <tr>
            <td>
                <a href="<?= url('admin/plan-update/custom') ?>"><?= settings()->plan_custom->name ?></a>
                <span data-toggle="tooltip" title="<?= language()->admin_plans->table->custom_help ?>"><i class="fa fa-fw fa-info-circle text-muted"></i></span>
            </td>
            <td>-</td>
            <td>-</td>
            <td>
                <i class="fa fa-fw fa-users text-muted"></i>
                <a href="<?= url('admin/users?plan_id=custom') ?>">
                    <?= nr(database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = 'custom'")->fetch_object()->total ?? 0) ?>
                </a>
            </td>
            <td>
                <?php if(settings()->plan_custom->status == 0): ?>
                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->disabled ?></span>
                <?php elseif(settings()->plan_custom->status == 1): ?>
                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->global->active ?></span>
                <?php else: ?>
                    <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->hidden ?></span>
                <?php endif ?>
            </td>
            <td><?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => 'custom']) ?></td>
        </tr>

        <?php foreach($data->plans as $row): ?>

            <tr data-plan-id="<?= $row->plan_id ?>">
                <td>
                    <a href="<?= url('admin/plan-update/' . $row->plan_id) ?>"><?= $row->name ?></a>
                    <?php if($row->status != 0): ?>
                        <a href="<?= url('pay/' . $row->plan_id) ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
                    <?php endif ?>
                </td>
                <td>
                    <div class="d-flex flex-column text-muted small">
                        <span><?= $row->monthly_price . ' ' . settings()->payment->currency . ' ' . language()->admin_plans->table->monthly ?></span>
                        <span><?= $row->annual_price . ' ' . settings()->payment->currency . ' ' . language()->admin_plans->table->annual ?></span>
                        <span><?= $row->lifetime_price . ' ' . settings()->payment->currency . ' ' . language()->admin_plans->table->lifetime ?></span>
                    </div>
                </td>
                <td class="text-muted"><?= $row->order ?></td>
                <td>
                    <i class="fa fa-fw fa-users text-muted"></i>
                    <a href="<?= url('admin/users?plan_id=' . $row->plan_id) ?>">
                        <?= nr(database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE `plan_id` = '{$row->plan_id}'")->fetch_object()->total ?? 0) ?>
                    </a>
                </td>
                <td>
                    <?php if($row->status == 0): ?>
                        <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->disabled ?></span>
                    <?php elseif($row->status == 1): ?>
                        <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->global->active ?></span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-info"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->hidden ?></span>
                    <?php endif ?>
                </td>
                <td><?= include_view(THEME_PATH . 'views/admin/plans/admin_plan_dropdown_button.php', ['id' => $row->plan_id]) ?></td>
            </tr>

        <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/plans/plan_delete_modal.php'), 'modals'); ?>
