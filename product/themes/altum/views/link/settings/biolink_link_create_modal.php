<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="biolink_link_create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= $this->language->biolink_link_create_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= $this->language->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                <?php foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key): ?>
                    <div class="col-12 col-lg-6">
                        <button
                                type="button"
                                data-dismiss="modal"
                                data-toggle="modal"
                                data-target="#create_biolink_<?= $key ?>"
                                class="btn btn-block mb-3"
                                <?= $this->user->plan_settings->enabled_biolink_blocks->{$key} ? null : 'disabled="disabled"' ?>
                        >
                            <i class="fa fa-fw fa-circle fa-sm mr-1" style="color: <?= $this->language->link->biolink->{$key}->color ?>"></i>

                            <?= $this->language->link->biolink->{$key}->name ?>
                        </button>
                    </div>
                <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>
