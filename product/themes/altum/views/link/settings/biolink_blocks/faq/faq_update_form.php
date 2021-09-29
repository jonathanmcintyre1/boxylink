<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="faq" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div id="<?= 'faq_items_' . $row->biolink_block_id ?>" data-biolink-block-id="<?= $row->biolink_block_id ?>">
        <?php foreach($row->settings->items as $key => $item): ?>
            <div class="mb-4">
                <div class="form-group">
                    <label for="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>"><?= language()->create_biolink_faq_modal->title ?></label>
                    <input id="<?= 'item_title_' . $key . '_' . $row->biolink_block_id ?>" type="text" name="item_title[<?= $key ?>]" class="form-control" value="<?= $item->title ?>" required="required" />
                </div>

                <div class="form-group">
                    <label for="<?= 'item_content_' . $key . '_' . $row->biolink_block_id ?>"><?= language()->create_biolink_faq_modal->content ?></label>
                    <textarea id="<?= 'item_content_' . $key . '_' . $row->biolink_block_id ?>" name="item_content[<?= $key ?>]" class="form-control" required="required"><?= $item->content ?></textarea>
                </div>

                <button type="button" data-remove="item" class="btn btn-block btn-outline-danger"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></button>
            </div>
        <?php endforeach ?>
    </div>

    <div class="mb-3">
        <button data-add="faq_item" data-biolink-block-id="<?= $row->biolink_block_id ?>" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->global->create ?></button>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
