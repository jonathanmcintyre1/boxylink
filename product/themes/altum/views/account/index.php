<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?= \Altum\Alerts::output_alerts() ?>

    <form action="" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

        <div class="row mb-5">
            <div class="col-12 col-md-4">
                <h2 class="h4"><?= language()->account->settings->header ?></h2>
                <p class="text-muted"><?= language()->account->settings->subheader ?></p>
            </div>

            <div class="col">
                <div class="form-group">
                    <label for="name"><?= language()->account->settings->name ?></label>
                    <input type="text" id="name" name="name" class="form-control <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $this->user->name ?>" />
                    <?= \Altum\Alerts::output_field_error('name') ?>
                </div>

                <div class="form-group">
                    <label for="email"><?= language()->account->settings->email ?></label>
                    <input type="text" id="email" name="email" class="form-control <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $this->user->email ?>" />
                    <?= \Altum\Alerts::output_field_error('email') ?>
                </div>

                <div class="form-group">
                    <label for="timezone"><?= language()->account->settings->timezone ?></label>
                    <select id="timezone" name="timezone" class="form-control">
                        <?php foreach(DateTimeZone::listIdentifiers() as $timezone) echo '<option value="' . $timezone . '" ' . ($this->user->timezone == $timezone ? 'selected="selected"' : null) . '>' . $timezone . '</option>' ?>
                    </select>
                    <small class="form-text text-muted"><?= language()->account->settings->timezone_help ?></small>
                </div>
            </div>
        </div>

        <div class="row mb-5" id="billing" style="<?= !settings()->payment->is_enabled || !settings()->payment->taxes_and_billing_is_enabled ? 'display: none;' : null ?>">
            <div class="col-12 col-xl-4">
                <h2 class="h4"><?= language()->account->billing->header ?></h2>
                <p class="text-muted"><?= language()->account->billing->subheader ?></p>
            </div>

            <div class="col">
                <?php if(!empty($this->user->payment_subscription_id)): ?>
                    <div class="alert alert-info" role="alert">
                        <?= language()->account->billing->subscription_id_active ?>
                    </div>
                <?php endif ?>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label><?= language()->account->billing->type ?></label>
                            <select name="billing_type" class="form-control">
                                <option value="personal" <?= $this->user->billing->type == 'personal' ? 'selected="selected"' : null ?>><?= language()->account->billing->type_personal ?></option>
                                <option value="business" <?= $this->user->billing->type == 'business' ? 'selected="selected"' : null ?>><?= language()->account->billing->type_business ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label><?= language()->account->billing->name ?></label>
                            <input type="text" name="billing_name" class="form-control" value="<?= $this->user->billing->name ?>" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label><?= language()->account->billing->address ?></label>
                            <input type="text" name="billing_address" class="form-control" value="<?= $this->user->billing->address ?>" />
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label><?= language()->account->billing->city ?></label>
                            <input type="text" name="billing_city" class="form-control" value="<?= $this->user->billing->city ?>" />
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label><?= language()->account->billing->county ?></label>
                            <input type="text" name="billing_county" class="form-control" value="<?= $this->user->billing->county ?>" />
                        </div>
                    </div>

                    <div class="col-12 col-lg-2">
                        <div class="form-group">
                            <label><?= language()->account->billing->zip ?></label>
                            <input type="text" name="billing_zip" class="form-control" value="<?= $this->user->billing->zip ?>" />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label><?= language()->account->billing->country ?></label>
                            <select name="billing_country" class="form-control">
                                <?php foreach(get_countries_array() as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $this->user->billing->country == $key ? 'selected="selected"' : null ?>><?= $value ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label><?= language()->account->billing->phone ?></label>
                            <input type="text" name="billing_phone" class="form-control" value="<?= $this->user->billing->phone ?>" />
                        </div>
                    </div>

                    <div class="col-12" id="billing_tax_id_container">
                        <div class="form-group">
                            <label><?= !empty(settings()->business->tax_type) ? settings()->business->tax_type : language()->account->billing->tax_id ?></label>
                            <input type="text" name="billing_tax_id" class="form-control" value="<?= $this->user->billing->tax_id ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php ob_start() ?>
        <script>
            'use strict';

            /* Billing type handler */
            let billing_type = () => {
                let type = document.querySelector('select[name="billing_type"]').value;

                if(type == 'personal') {
                    document.querySelector('#billing_tax_id_container').style.display = 'none';
                } else {
                    document.querySelector('#billing_tax_id_container').style.display = '';
                }
            };

            billing_type();

            document.querySelector('select[name="billing_type"]').addEventListener('change', billing_type);

            <?php if(!empty($this->user->payment_subscription_id)): ?>
            document.querySelectorAll('[name^="billing_"]').forEach(element => {
                element.setAttribute('disabled', 'disabled');
            });
            <?php endif ?>

        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

        <div class="row">
            <div class="col-12 col-md-4">
                <h2 class="h4"><?= language()->account->twofa->header ?></h2>
                <p class="text-muted"><?= language()->account->twofa->subheader ?></p>
            </div>

            <div class="col">
                <div class="form-group">
                    <label><?= language()->account->twofa->is_enabled ?></label>

                    <select id="twofa_is_enabled" name="twofa_is_enabled" class="form-control <?= \Altum\Alerts::has_field_errors('twofa_token') ? 'is-invalid' : null ?>">
                        <option value="1" <?= $this->user->twofa_secret ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                        <option value="0" <?= !$this->user->twofa_secret ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
                    </select>
                </div>

                <div id="twofa_container">
                    <?php if(!$this->user->twofa_secret): ?>
                    <div class="form-group">
                        <label><?= language()->account->twofa->qr ?></label>
                        <p class="text-muted"><?= language()->account->twofa->qr_help ?></p>

                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <div class="mb-3 mb-md-0 mr-md-5">
                                <img src="<?= $data->twofa_image ?>" alt="<?= language()->account->twofa->qr ?>" />
                            </div>

                            <div>
                                <label><?= language()->account->twofa->secret ?></label>
                                <p class="text-muted"><?= language()->account->twofa->secret_help ?></p>

                                <p class="h5"><?= $data->twofa_secret ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="twofa_token"><?= language()->account->twofa->verify ?></label>
                        <p class="text-muted"><?= language()->account->twofa->verify_help ?></p>
                        <input type="text" id="twofa_token" name="twofa_token" class="form-control <?= \Altum\Alerts::has_field_errors('twofa_token') ? 'is-invalid' : null ?>" value="" autocomplete="off" />
                        <?= \Altum\Alerts::output_field_error('twofa_token') ?>
                    </div>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <div class="mt-5"></div>

        <div class="row">
            <div class="col-12 col-md-4">
                <h2 class="h4"><?= language()->account->change_password->header ?></h2>
                <p class="text-muted"><?= language()->account->change_password->subheader ?></p>
            </div>

            <div class="col">
                <div class="form-group">
                    <label for="old_password"><?= language()->account->change_password->current_password ?></label>
                    <input type="password" id="old_password" name="old_password" class="form-control <?= \Altum\Alerts::has_field_errors('old_password') ? 'is-invalid' : null ?>" />
                    <?= \Altum\Alerts::output_field_error('old_password') ?>
                </div>

                <div class="form-group">
                    <label for="new_password"><?= language()->account->change_password->new_password ?></label>
                    <input type="password" id="new_password" name="new_password" class="form-control <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                    <?= \Altum\Alerts::output_field_error('new_password') ?>
                </div>

                <div class="form-group">
                    <label for="repeat_password"><?= language()->account->change_password->repeat_password ?></label>
                    <input type="password" id="repeat_password" name="repeat_password" class="form-control <?= \Altum\Alerts::has_field_errors('repeat_password') ? 'is-invalid' : null ?>" />
                    <?= \Altum\Alerts::output_field_error('repeat_password') ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-4"></div>

            <div class="col">
                <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
            </div>
        </div>
    </form>

</section>

<?php if(!$this->user->twofa_secret): ?>
    <?php ob_start() ?>
    <script>
        'use strict';

        let twofa = () => {
            let is_enabled = parseInt(document.querySelector('select[name="twofa_is_enabled"]').value);

            if(is_enabled) {
                document.querySelector('#twofa_container').style.display = '';
            } else {
                document.querySelector('#twofa_container').style.display = 'none';
            }
        };

        twofa();

        document.querySelector('select[name="twofa_is_enabled"]').addEventListener('change', twofa);
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>
