<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="pixel_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->pixel_update_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="pixel_update" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="update" />
                    <input type="hidden" name="pixel_id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="update_name"><i class="fa fa-fw fa-signature fa-sm mr-1"></i> <?= language()->pixels->input->name ?></label>
                        <input type="text" id="update_name" class="form-control" name="name" />
                    </div>

                    <div class="form-group">
                        <label for="update_type"><i class="fa fa-fw fa-adjust fa-sm mr-1"></i> <?= language()->pixels->input->type ?></label>
                        <select id="update_type" name="type" class="form-control">
                            <?php foreach(require APP_PATH . 'includes/pixels.php' as $pixel): ?>
                                <option value="<?= $pixel ?>"><?= language()->pixels->pixels->{$pixel} ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="update_pixel"><i class="fa fa-fw fa-code fa-sm mr-1"></i> <?= language()->pixels->input->pixel ?></label>
                        <input type="text" id="update_pixel" name="pixel" class="form-control" value="" required="required" />
                        <small class="text-muted form-text"><?= language()->pixels->input->pixel_help ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#pixel_update').on('show.bs.modal', event => {
        let pixel_id = $(event.relatedTarget).data('pixel-id');
        let name = $(event.relatedTarget).data('name');
        let type = $(event.relatedTarget).data('type');
        let pixel = $(event.relatedTarget).data('pixel');

        $(event.currentTarget).find('input[name="pixel_id"]').val(pixel_id);
        $(event.currentTarget).find('input[name="name"]').val(name);
        $(event.currentTarget).find(`select[name="type"] option[value="${type}"]`).prop('selected', 'selected');
        $(event.currentTarget).find('input[name="pixel"]').val(pixel);
    });

    $('form[name="pixel_update"]').on('submit', event => {

        $.ajax({
            type: 'POST',
            url: 'pixel-ajax',
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                if (data.status == 'error') {
                    let notification_container = $(event.currentTarget).find('.notification-container');

                    notification_container.html('');

                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Hide modal */
                    $('#pixel_update').modal('hide');

                    /* Clear input values */
                    $('form[name="pixel_update"] input').val('');

                    redirect(`pixels`);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
