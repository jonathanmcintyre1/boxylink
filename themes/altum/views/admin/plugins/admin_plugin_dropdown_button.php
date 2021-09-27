<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown">
        <i class="fa fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <?php if($data->status === -1): ?>
            <a href="<?= url('admin/plugins/install/' . $data->id . '&global_token=' . \Altum\Middlewares\Csrf::get('global_token')) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-server mr-1"></i> <?= language()->admin_plugins->install ?></a>
            <a href="#" data-toggle="modal" data-target="#plugin_delete_modal" data-plugin-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= language()->global->delete ?></a>
        <?php elseif($data->status === 0): ?>
            <a href="<?= url('admin/plugins/activate/' . $data->id . '&global_token=' . \Altum\Middlewares\Csrf::get('global_token')) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-check mr-1"></i> <?= language()->admin_plugins->activate ?></a>
            <a href="#" data-toggle="modal" data-target="#plugin_uninstall_modal" data-plugin-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= language()->admin_plugins->uninstall ?></a>
        <?php elseif($data->status === 1): ?>
            <a href="<?= url('admin/plugins/disable/' . $data->id . '&global_token=' . \Altum\Middlewares\Csrf::get('global_token')) ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-eye-slash mr-1"></i> <?= language()->admin_plugins->disable ?></a>
        <?php endif ?>
    </div>
</div>
