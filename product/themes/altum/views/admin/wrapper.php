<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html class="admin" lang="<?= $this->language->language_code ?>">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta http-equiv="content-language" content="<?= $this->language->language_code ?>" />

        <?php if(!empty($this->settings->favicon)): ?>
            <link href="<?= SITE_URL . UPLOADS_URL_PATH . 'favicon/' . $this->settings->favicon ?>" rel="shortcut icon" />
        <?php endif ?>

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700&display=swap" rel="stylesheet">

        <?php foreach(['admin-' . \Altum\ThemeStyle::get_file(), 'admin-custom.css'] as $file): ?>
            <link href="<?= SITE_URL . ASSETS_URL_PATH ?>css/<?= $file ?>?v=<?= PRODUCT_CODE ?>" rel="stylesheet" media="screen">
        <?php endforeach ?>

        <?= \Altum\Event::get_content('head') ?>
    </head>

    <body class="admin" data-theme-style="<?= \Altum\ThemeStyle::get() ?>">

    <div class="admin-container">

        <?= $this->views['admin_sidebar'] ?>

        <section class="admin-content altum-animate altum-animate-fill-none altum-animate-fade-in">
            <div id="admin_overlay" class="admin-overlay" style="display: none"></div>

            <?= $this->views['admin_menu'] ?>

            <div class="p-3 p-lg-5">
                <?= $this->views['content'] ?>

                <?= $this->views['footer'] ?>
            </div>
        </section>
    </div>

    <?= \Altum\Event::get_content('modals') ?>

    <?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

    <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js'] as $file): ?>
        <script src="<?= SITE_URL . ASSETS_URL_PATH ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <?= \Altum\Event::get_content('javascript') ?>

    <script>
        let toggle_admin_sidebar = () => {
            /* Open sidebar menu */
            let body = document.querySelector('body');
            body.classList.toggle('admin-sidebar-opened');

            /* Toggle overlay */
            let admin_overlay = document.querySelector('#admin_overlay');
            admin_overlay.style.display == 'none' ? admin_overlay.style.display = 'block' : admin_overlay.style.display = 'none';

            /* Change toggle button content */
            let button = document.querySelector('#admin_menu_toggler');

            if(body.classList.contains('admin-sidebar-opened')) {
                button.innerHTML = `<i class="fa fa-fw fa-times"></i>`;
            } else {
                button.innerHTML = `<i class="fa fa-fw fa-bars"></i>`;
            }
        };

        /* Toggler for the sidebar */
        document.querySelector('#admin_menu_toggler').addEventListener('click', event => {
            event.preventDefault();

            toggle_admin_sidebar();

            let admin_sidebar_is_opened = document.querySelector('body').classList.contains('admin-sidebar-opened');

            if(admin_sidebar_is_opened) {
                document.querySelector('#admin_overlay').removeEventListener('click', toggle_admin_sidebar);
                document.querySelector('#admin_overlay').addEventListener('click', toggle_admin_sidebar);
            } else {
                document.querySelector('#admin_overlay').removeEventListener('click', toggle_admin_sidebar);
            }
        });
    </script>
    </body>
</html>
