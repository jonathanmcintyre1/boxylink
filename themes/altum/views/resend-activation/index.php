<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">

    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?= \Altum\Alerts::output_alerts() ?>

            <div class="card border-0">
                <div class="card-body p-5">

                    <h1 class="h4 card-title d-flex justify-content-between"><?= language()->resend_activation->header ?></h1>
                    <p class="text-muted"><?= language()->resend_activation->subheader ?></p>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label for="email"><?= language()->resend_activation->email ?></label>
                            <input id="email" type="email" name="email" class="form-control <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" required="required" autofocus="autofocus" />
                            <?= \Altum\Alerts::output_field_error('email') ?>
                        </div>

                        <?php if(settings()->captcha->resend_activation_is_enabled): ?>
                            <div class="form-group">
                                <?php $data->captcha->display() ?>
                            </div>
                        <?php endif ?>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= language()->resend_activation->submit ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <small><a href="login" class="text-muted"><?= language()->resend_activation->return ?></a></small>
        </div>
    </div>
</div>



