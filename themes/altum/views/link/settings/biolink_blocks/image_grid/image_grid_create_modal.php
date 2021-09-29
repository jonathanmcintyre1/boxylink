<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_image_grid" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_image_grid_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_image_grid" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="image_grid" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="image_grid_name"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
                        <input id="image_grid_name" type="text" name="name" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="image_grid_image"><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= language()->create_biolink_image_grid_modal->image ?></label>
                        <input id="image_grid_image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="image_grid_location_url"><i class="fa fa-fw fa-link fa-sm mr-1"></i> <?= language()->create_biolink_image_grid_modal->location_url ?></label>
                        <input id="image_grid_location_url" type="text" class="form-control" name="location_url" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
