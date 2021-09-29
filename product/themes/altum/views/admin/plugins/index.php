<?php defined('ALTUMCODE') || die() ?>

<?php if(count(\Altum\Plugin::$plugins)): ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3"><i class="fa fa-fw fa-xs fa-puzzle-piece text-primary-900 mr-2"></i> <?= language()->admin_plugins->header ?></h1>
    </div>

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th style="width: 50%"><?= language()->admin_plugins->table->plugin ?></th>
                <th style="width: 20%"><?= language()->admin_plugins->table->author ?></th>
                <th style="width: 20%"><?= language()->admin_plugins->table->status ?></th>
                <th style="width: 10%"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach(\Altum\Plugin::$plugins as $plugin): ?>

                <tr>
                    <td>
                        <div class="d-flex">
                            <div class="user-avatar rounded-circle mr-3 d-flex justify-content-center align-items-center" style="<?= $plugin->avatar_style ?? null ?>">
                                <?= $plugin->icon ?? null ?>
                            </div>

                            <div class="d-flex flex-column">
                                <div>
                                    <span><?= $plugin->name ?></span> <span class="text-muted"><?= '(v' . $plugin->version . ')' ?></span>
                                    <a href="<?= $plugin->url ?>" target="_blank" rel="nofollow noreferrer"><i class="fa fa-fw fa-xs fa-external-link-alt ml-1"></i></a>
                                </div>

                                <small class="text-muted"><?= $plugin->description ?></small>
                            </div>
                        </div>
                    </td>

                    <td>
                        <a href="<?= $plugin->author_url ?>" target="_blank" rel="nofollow noreferrer"><?= $plugin->author ?></a>
                    </td>

                    <td>
                        <?php if($plugin->status === -2): ?>
                            <a href="<?= $plugin->url ?>" target="_blank" rel="nofollow noreferrer" class="btn btn-sm btn-success"><?= language()->admin_plugins->status_inexistent ?></a>
                        <?php elseif($plugin->status === -1): ?>
                            <span class="badge badge-light"><?= language()->admin_plugins->status_uninstalled ?></span>
                        <?php elseif($plugin->status === 0): ?>
                            <span class="badge badge-secondary"><?= language()->admin_plugins->status_disabled ?></span>
                        <?php elseif($plugin->status === 1): ?>
                            <span class="badge badge-success"><?= language()->admin_plugins->status_active ?></span>
                        <?php endif ?>
                    </td>

                    <td>
                        <?php if($plugin->actions && $plugin->status !== -2): ?>
                            <?= include_view(THEME_PATH . 'views/admin/plugins/admin_plugin_dropdown_button.php', ['id' => $plugin->plugin_id, 'status' => $plugin->status]) ?>
                        <?php endif ?>
                    </td>
                </tr>

            <?php endforeach ?>
            </tbody>
        </table>
    </div>

<?php else: ?>

    <div class="d-flex flex-column flex-md-row align-items-md-center">
        <div class="mb-3 mb-md-0 mr-md-5">
            <i class="fa fa-fw fa-7x fa-puzzle-piece text-primary-200"></i>
        </div>

        <div class="d-flex flex-column">
            <h1 class="h3"><?= language()->admin_plugins->header_no_data ?></h1>
            <p class="text-muted"><?= language()->admin_plugins->subheader_no_data ?></p>

        </div>
    </div>

<?php endif ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/plugins/plugin_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/plugins/plugin_uninstall_modal.php'), 'modals'); ?>
