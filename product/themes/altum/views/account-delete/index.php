<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?= \Altum\Alerts::output_alerts() ?>

    <h2 class="h4"><?= language()->account_delete->header ?></h2>
    <p class="text-muted"><?= language()->account_delete->subheader ?></p>

    <form action="" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

        <div class="form-group">
            <label for="current_password"><?= language()->account_delete->current_password ?></label>
            <input type="password" id="current_password" name="current_password" class="form-control" />
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-secondary"><?= language()->global->delete ?></button>
    </form>

</section>
