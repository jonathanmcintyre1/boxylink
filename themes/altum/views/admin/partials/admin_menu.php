<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-expand-lg navbar-light admin-navbar-top">
    <a class="navbar-brand" href="<?= url() ?>">
        <?php if(settings()->logo != ''): ?>
            <img src="<?= UPLOADS_FULL_URL . 'logo/' . settings()->logo ?>" class="img-fluid admin-navbar-logo-top" alt="<?= language()->global->accessibility->logo_alt ?>" />
        <?php else: ?>
            <?= settings()->title ?>
        <?php endif ?>
    </a>

    <ul class="navbar-nav ml-auto">
        <button class="btn navbar-custom-toggler" type="button" id="admin_menu_toggler" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= language()->global->accessibility->toggle_navigation ?>">
            <i class="fa fa-fw fa-bars"></i>
        </button>
    </ul>
</nav>
