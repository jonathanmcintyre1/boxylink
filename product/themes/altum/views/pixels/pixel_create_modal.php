<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_pixel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->pixel_create_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_pixel" method="post" role="form">
                    <div class="notification-container"></div>

                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />

                    <div class="form-group">
                        <label for="create_name"><i class="fa fa-fw fa-signature fa-sm mr-1"></i> <?= language()->pixels->input->name ?></label>
                        <input type="text" id="create_name" class="form-control" name="name" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="create_type"><i class="fa fa-fw fa-adjust fa-sm mr-1"></i> <?= language()->pixels->input->type ?></label>
                        <select id="create_type" name="type" class="form-control">
                            <?php foreach(require APP_PATH . 'includes/pixels.php' as $pixel): ?>
                            <option value="<?= $pixel ?>"><?= language()->pixels->pixels->{$pixel} ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="create_pixel"><i class="fa fa-fw fa-code fa-sm mr-1"></i> <?= language()->pixels->input->pixel ?></label>
                        <input type="text" id="create_pixel" name="pixel" class="form-control" value="" required="required" />
                        <small class="text-muted form-text"><?= language()->pixels->input->pixel_help ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= language()->global->create ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $('form[name="create_pixel"]').on('submit', event => {

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
                    $('#create_pixel').modal('hide');

                    /* Clear input values */
                    $('form[name="create_pixel"] input').val('');

                    redirect(`pixels`);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
