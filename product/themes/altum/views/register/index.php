<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-5">
            <?php display_notifications() ?>

            <div class="card border-0">
                <div class="card-body p-5">
                    <h4 class="card-title"><?= $this->language->register->header ?></h4>

                    <form action="" method="post" class="mt-4" role="form">
                        <div class="form-group">
                            <label for="name"><?= $this->language->register->form->name ?></label>
                            <input id="name" type="text" name="name" class="form-control" value="<?= $data->values['name'] ?>" placeholder="<?= $this->language->register->form->name_placeholder ?>" required="required" autofocus="autofocus" />
                        </div>

                        <div class="form-group">
                            <label for="email"><?= $this->language->register->form->email ?></label>
                            <input id="email" type="text" name="email" class="form-control" value="<?= $data->values['email'] ?>" placeholder="<?= $this->language->register->form->email_placeholder ?>" required="required" />
                        </div>

                        <div class="form-group">
                            <label for="password"><?= $this->language->register->form->password ?></label>
                            <input id="password" type="password" name="password" class="form-control" value="<?= $data->values['password'] ?>" placeholder="<?= $this->language->register->form->password_placeholder ?>" required="required" />
                        </div>

                        <?php if($this->settings->captcha->register_is_enabled): ?>
                        <div class="form-group">
                            <?php $data->captcha->display() ?>
                        </div>
                        <?php endif ?>

                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" name="accept" type="checkbox" required="required">
                                <small class="form-text text-muted">
                                    <?= sprintf(
                                        $this->language->register->form->accept,
                                        '<a href="' . $this->settings->terms_and_conditions_url . '" target="_blank">' . $this->language->global->terms_and_conditions . '</a>',
                                        '<a href="' . $this->settings->privacy_policy_url . '" target="_blank">' . $this->language->global->privacy_policy . '</a>'
                                    ) ?>
                                </small>
                            </label>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" name="submit" class="btn btn-primary btn-block"><?= $this->language->register->form->register ?></button>
                        </div>

                        <div class="row">
                            <?php if($this->settings->facebook->is_enabled): ?>
                                <div class="col-sm mt-1">
                                    <a href="<?= $data->facebook_login_url ?>" class="btn btn-light btn-block"><?= sprintf($this->language->login->display->facebook, "<i class=\"fab fa-fw fa-facebook\"></i>") ?></a>
                                </div>
                            <?php endif ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <small><a href="login" class="text-muted" role="button"><?= $this->language->register->login ?></a></small>
        </div>
    </div>
</div>



