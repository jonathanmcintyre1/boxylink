<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_external_item" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_external_item_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_external_item" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="external_item" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="external_item_location_url"><i class="fa fa-fw fa-signature fa-sm mr-1"></i> <?= language()->create_biolink_external_item_modal->location_url ?></label>
                        <input id="external_item_location_url" type="text" class="form-control" name="location_url" required="required" placeholder="<?= language()->create_biolink_link_modal->input->location_url_placeholder ?>" />
                    </div>

                    <div class="form-group">
                        <label for="external_item_name"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
                        <input id="external_item_name" type="text" name="name" class="form-control" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="external_item_description"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_external_item_modal->description ?></label>
                        <input id="external_item_description" type="text" name="description" class="form-control" value="" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="external_item_price"><i class="fa fa-fw fa-dollar-sign fa-sm mr-1"></i> <?= language()->create_biolink_external_item_modal->price ?></label>
                        <input id="external_item_price" type="text" name="price" class="form-control" value="" placeholder="<?= language()->create_biolink_external_item_modal->price_placeholder ?>" required="required" />
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
