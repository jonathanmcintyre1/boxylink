<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= \Altum\Language::$language_code ?>" dir="<?= language()->direction ?>">
<head>
    <title><?= \Altum\Title::get() ?></title>
    <base href="<?= SITE_URL; ?>">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="content-language" content="<?= \Altum\Language::$language_code  ?>" />

    <link rel="alternate" href="<?= SITE_URL . \Altum\Routing\Router::$original_request ?>" hreflang="x-default" />
    <?php if(count(\Altum\Language::$languages) > 1): ?>
        <?php foreach(\Altum\Language::$languages as $language_code => $language_name): ?>
            <?php if(settings()->default_language != $language_name): ?>
                <link rel="alternate" href="<?= SITE_URL . $language_code . '/' . \Altum\Routing\Router::$original_request ?>" hreflang="<?= $language_code ?>" />
            <?php endif ?>
        <?php endforeach ?>
    <?php endif ?>

    <?php if(!empty(settings()->favicon)): ?>
        <link href="<?= UPLOADS_FULL_URL . 'favicon/' . settings()->favicon ?>" rel="shortcut icon" />
    <?php endif ?>

    <?php foreach(['admin-' . \Altum\ThemeStyle::get_file(), 'admin-custom.css'] as $file): ?>
        <link href="<?= ASSETS_FULL_URL ?>css/<?= $file ?>?v=<?= PRODUCT_CODE ?>" rel="stylesheet" media="screen">
    <?php endforeach ?>

    <?= \Altum\Event::get_content('head') ?>
</head>

<body class="<?= language()->direction == 'rtl' ? 'rtl' : null ?>" data-theme-style="<?= \Altum\ThemeStyle::get() ?>">
<div id="admin_overlay" class="admin-overlay" style="display: none"></div>

<div class="admin-container">
    <?= $this->views['admin_sidebar'] ?>

    <section class="admin-content altum-animate altum-animate-fill-none altum-animate-fade-in">
        <?= $this->views['admin_menu'] ?>

        <div class="p-3 p-lg-5">
            <?= $this->views['content'] ?>

            <?= $this->views['footer'] ?>
        </div>
    </section>
</div>

<?= \Altum\Event::get_content('modals') ?>

<?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

<?php foreach(['libraries/jquery.slim.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'main.js', 'functions.js', 'libraries/fontawesome.min.js', 'libraries/fontawesome-solid.min.js', 'libraries/fontawesome-brands.modified.js'] as $file): ?>
    <script src="<?= ASSETS_FULL_URL ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
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
