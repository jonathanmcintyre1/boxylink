<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="biolink_link_create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->biolink_link_create_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="px-3">
                <form action="" method="get" role="form">
                    <div class="form-group">
                        <input type="search" name="search" class="form-control form-control-lg" value="" placeholder="<?= language()->global->filters->search ?>" aria-label="<?= language()->global->filters->search ?>" />
                    </div>
                </form>
            </div>

            <div class="modal-body">
                <div class="row">
                <?php foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key => $value): ?>
                    <div class="col-12 col-lg-12" data-block-id="<?= $key ?>" data-block-name="<?= language()->link->biolink->blocks->{$key} ?>">
                        <button
                                type="button"
                                data-dismiss="modal"
                                data-toggle="modal"
                                data-target="#create_biolink_<?= $key ?>"
                                class="btn btn-light btn-block btn-lg mb-3"
                                <?= $this->user->plan_settings->enabled_biolink_blocks->{$key} ? null : 'disabled="disabled"' ?>
                        >
                            <i class="<?= $data->biolink_blocks[$key]['icon'] ?> fa-fw fa-sm mr-1" style="color: <?= $data->biolink_blocks[$key]['color'] ?>"></i>

                            <?= language()->link->biolink->blocks->{$key} ?>
                        </button>
                    </div>
                <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    let blocks = [];
    document.querySelectorAll('[data-block-id]').forEach(element => blocks.push({
        id: element.getAttribute('data-block-id'),
        name: element.getAttribute('data-block-name').toLowerCase(),
    }));

    document.querySelector('#biolink_link_create_modal input').addEventListener('keyup', event => {
        let string = event.currentTarget.value.toLowerCase();

        for(let block of blocks) {
            if(block.name.includes(string)) {
                document.querySelector(`[data-block-id="${block.id}"]`).classList.remove('d-none');
            } else {
                document.querySelector(`[data-block-id="${block.id}"]`).classList.add('d-none');
            }
        }
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
