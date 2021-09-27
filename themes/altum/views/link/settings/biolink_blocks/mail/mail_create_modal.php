<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_mail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_mail_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p class="text-muted modal-subheader"><?= language()->create_biolink_mail_modal->subheader ?></p>

            <div class="modal-body">
                <form name="create_biolink_mail" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="mail" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="mail_name"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
                        <input id="mail_name" type="text" name="name" class="form-control" required="required" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
