<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_video" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_video_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_video" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="video" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="video_file"><i class="fa fa-fw fa-video fa-sm mr-1"></i> <?= language()->create_biolink_video_modal->file ?></label>
                        <input id="video_file" type="file" name="file" accept="<?= implode(', ', array_map(function($value) { return '.' . $value; }, $data->biolink_blocks['video']['whitelisted_file_extensions'])) ?>" class="form-control-file" required="required" />
                        <small class="form-text text-muted"><?= language()->create_biolink_video_modal->file_help ?></small>
                    </div>

                    <div class="form-group">
                        <label for="video_name"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
                        <input id="video_name" type="text" name="name" class="form-control" required="required" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
