<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_socials" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_socials_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_socials" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="socials" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="socials_color"><i class="fa fa-fw fa-paint-brush fa-sm mr-1"></i> <?= language()->create_biolink_socials_modal->color ?></label>
                        <input type="color" id="socials_color" name="color" class="form-control" value="" required="required" />
                    </div>

                    <?php $biolink_socials = require APP_PATH . 'includes/biolink_socials.php'; ?>
                    <?php foreach($biolink_socials as $key => $value): ?>
                        <?php if($value['input_group']): ?>
                            <div class="form-group">
                                <label for="<?= 'socials_' . $key ?>"><i class="<?= language()->create_biolink_socials_modal->socials->{$key}->icon ?> fa-fw fa-sm mr-1"></i> <?= language()->create_biolink_socials_modal->socials->{$key}->name ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?= str_replace('%s', '', $value['format']) ?></span>
                                    </div>
                                    <input id="<?= 'socials_' . $key ?>" type="text" class="form-control" name="socials[<?= $key ?>]" placeholder="<?= language()->create_biolink_socials_modal->socials->{$key}->placeholder ?>" value="" />
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label for="<?= 'socials_' . $key ?>"><i class="<?= language()->create_biolink_socials_modal->socials->{$key}->icon ?> fa-fw fa-sm mr-1"></i> <?= language()->create_biolink_socials_modal->socials->{$key}->name ?></label>
                                <input id="<?= 'socials_' . $key ?>" type="text" class="form-control" name="socials[<?= $key ?>]" placeholder="<?= language()->create_biolink_socials_modal->socials->{$key}->placeholder ?>" value="" />
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
