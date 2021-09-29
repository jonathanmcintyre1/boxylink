<?php defined('ALTUMCODE') || die() ?>

<body class="link-body">
    <div class="container animate__animated animate__fadeIn">
        <div class="row justify-content-center mt-5 mt-lg-10">
            <div class="col-md-8">

                <div class="mb-4 d-flex">
                    <div class="text-center">
                        <h1 class="h3 mb-4"><?= language()->link->password->header  ?></h1>
                        <span class="text-muted">
                            <?= language()->link->password->subheader ?>
                        </span>
                    </div>
                </div>

                <?= \Altum\Alerts::output_alerts() ?>

                <form action="" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
                    <input type="hidden" name="type" value="password" />

                    <div class="form-group">
                        <label for="password"><?= language()->link->password->input ?></label>
                        <input type="password" id="password" name="password" value="" class="form-control <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('password') ?>
                    </div>

                    <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= language()->global->submit ?></button>
                </form>

            </div>
        </div>
    </div>
</body>


