<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="avatar" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'avatar_image_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm mr-1"></i> <?= language()->create_biolink_avatar_modal->image ?></label>
        <div data-image-container class="<?= !empty($row->settings->image) ? null : 'd-none' ?>">
            <div class="row">
                <div class="m-1 col-6 col-xl-3">
                    <img src="<?= $row->settings->image ? UPLOADS_FULL_URL . 'avatars/' . $row->settings->image : null ?>" class="img-fluid rounded <?= !empty($row->settings->image) ? null : 'd-none' ?>" loading="lazy" />
                </div>
            </div>
        </div>
        <input id="<?= 'avatar_image_' . $row->biolink_block_id ?>" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
    </div>

    <div class="form-group">
        <label for="<?= 'avatar_size_' . $row->biolink_block_id ?>"><?= language()->create_biolink_avatar_modal->size ?></label>
        <select id="<?= 'avatar_size_' . $row->biolink_block_id ?>" name="size" class="form-control">
            <option value="75" <?= $row->settings->size == '75' ? 'selected="selected"' : null ?>>75x75px</option>
            <option value="100" <?= $row->settings->size == '100' ? 'selected="selected"' : null ?>>100x100px</option>
            <option value="125" <?= $row->settings->size == '125' ? 'selected="selected"' : null ?>>125x125px</option>
            <option value="150" <?= $row->settings->size == '150' ? 'selected="selected"' : null ?>>150x150px</option>
        </select>
    </div>

    <div class="form-group">
        <label for="<?= 'avatar_border_radius_' . $row->biolink_block_id ?>"><?= language()->create_biolink_avatar_modal->border_radius ?></label>
        <select id="<?= 'avatar_border_radius_' . $row->biolink_block_id ?>" name="border_radius" class="form-control">
            <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="selected"' : null ?>><?= language()->create_biolink_avatar_modal->border_radius_straight ?></option>
            <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="selected"' : null ?>><?= language()->create_biolink_avatar_modal->border_radius_round ?></option>
            <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="selected"' : null ?>><?= language()->create_biolink_avatar_modal->border_radius_rounded ?></option>
        </select>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->update ?></button>
    </div>
</form>
