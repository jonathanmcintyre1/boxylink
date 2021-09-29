<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="settings_send_test_email_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="<?= url('admin/settings/send_test_email') ?>" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="modal-title">
                            <i class="fa fa-fw fa-sm fa-paper-plane text-primary-900 mr-2"></i>
                            <?= language()->admin_settings_send_test_email_modal->header ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="form-group">
                        <label for="email"><?= language()->admin_settings_send_test_email_modal->email ?></label>
                        <input id="email" type="email" name="email" class="form-control form-control-lg" />
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    /* On modal show load new data */
    $('#settings_send_test_email_modal').on('show.bs.modal', event => {
        let user_id = $(event.relatedTarget).data('user-id');

        $(event.currentTarget).find('#settings_send_test_email_modal_url').attr('href', `${url}admin/users/login/${user_id}&global_token=${global_token}`);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
