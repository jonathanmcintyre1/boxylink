<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_avatar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_avatar_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_avatar" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="avatar" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="avatar_image"><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= language()->create_biolink_avatar_modal->image ?></label>
                        <input id="avatar_image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="avatar_size"><?= language()->create_biolink_avatar_modal->size ?></label>
                        <select id="avatar_size" name="size" class="form-control">
                            <option value="75">75x75px</option>
                            <option value="100">100x100px</option>
                            <option value="125">125x125px</option>
                            <option value="150">150x150px</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="avatar_border_radius"><?= language()->create_biolink_avatar_modal->border_radius ?></label>
                        <select id="avatar_border_radius" name="border_radius" class="form-control">
                            <option value="straight"><?= language()->create_biolink_avatar_modal->border_radius_straight ?></option>
                            <option value="round"><?= language()->create_biolink_avatar_modal->border_radius_round ?></option>
                            <option value="rounded"><?= language()->create_biolink_avatar_modal->border_radius_rounded ?></option>
                        </select>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
