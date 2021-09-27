<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_biolink_cta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->create_biolink_cta_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink_cta" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="link_id" value="<?= $data->link->link_id ?>" />
                    <input type="hidden" name="block_type" value="cta" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="cta_type"><i class="fa fa-fw fa-comments fa-sm mr-1"></i> <?= language()->create_biolink_cta_modal->type ?></label>
                        <select id="cta_type" name="type" class="form-control">
                            <option value="email"><?= language()->create_biolink_cta_modal->type_email ?></option>
                            <option value="call"><?= language()->create_biolink_cta_modal->type_call ?></option>
                            <option value="sms"><?= language()->create_biolink_cta_modal->type_sms ?></option>
                            <option value="facetime"><?= language()->create_biolink_cta_modal->type_facetime ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cta_value">
                            <span data-cta-type="email" class="d-none"><i class="fa fa-fw fa-envelope fa-sm mr-1"></i> <?= language()->create_biolink_cta_modal->value_email ?></span>
                            <span data-cta-type="call" class="d-none"><i class="fa fa-fw fa-phone-square-alt fa-sm mr-1"></i> <?= language()->create_biolink_cta_modal->value_call ?></span>
                            <span data-cta-type="sms" class="d-none"><i class="fa fa-fw fa-sms fa-sm mr-1"></i> <?= language()->create_biolink_cta_modal->value_sms ?></span>
                            <span data-cta-type="facetime" class="d-none"><i class="fa fa-fw fa-headset fa-sm mr-1"></i> <?= language()->create_biolink_cta_modal->value_facetime ?></span>
                        </label>
                        <input id="cta_value" type="text" class="form-control" name="value" required="required" />
                    </div>

                    <div class="form-group">
                        <label for="cta_name"><i class="fa fa-fw fa-paragraph fa-sm mr-1"></i> <?= language()->create_biolink_link_modal->input->name ?></label>
                        <input id="cta_name" type="text" name="name" class="form-control" required="required" />
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
let cta_create_modal_initiate = () => {
    let cta_type = document.querySelector('#cta_type').value;

    document.querySelectorAll('[data-cta-type]').forEach(element => {
        if(element.getAttribute('data-cta-type') == cta_type) {
            element.classList.remove('d-none');
        } else {
            if(!element.classList.contains('d-none')) {
                element.classList.add('d-none');
            }
        }
    });
}

document.querySelector('#cta_type').addEventListener('change', cta_create_modal_initiate);

cta_create_modal_initiate();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
