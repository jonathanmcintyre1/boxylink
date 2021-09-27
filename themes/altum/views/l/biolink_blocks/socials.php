<?php defined('ALTUMCODE') || die() ?>

<div data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">
    <div class="d-flex flex-wrap justify-content-center">
        <?php $biolink_socials = require APP_PATH . 'includes/biolink_socials.php'; ?>
        <?php foreach($data->link->settings->socials as $key => $value): ?>
            <?php if($value): ?>
                <div class="my-2 mx-3">
                    <span data-toggle="tooltip" title="<?= language()->create_biolink_socials_modal->socials->{$key}->name ?>">
                        <a href="<?= sprintf($biolink_socials[$key]['format'], $value) ?>" target="_blank" class="link-hover-animation">
                            <i class="<?= language()->create_biolink_socials_modal->socials->{$key}->icon ?> fa-fw fa-2x" style="color: <?= $data->link->settings->color ?>"></i>
                        </a>
                    </span>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>

