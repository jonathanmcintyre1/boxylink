<?php defined('ALTUMCODE') || die() ?>

<header class="mb-6">
    <div class="container">

        <nav aria-label="breadcrumb">
            <small>
                <ol class="custom-breadcrumbs">
                    <li><a href="<?= url('dashboard') ?>"><?= language()->dashboard->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li><a href="<?= url('links') ?>"><?= language()->links->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li><a href="<?= url('link/' . $data->biolink_block->link_id) ?>"><?= language()->link->breadcrumb_biolink ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li class="active" aria-current="page">
                        <?= language()->link->breadcrumb_biolink_block . ' ' . language()->link->statistics->breadcrumb ?>
                    </li>
                </ol>
            </small>
        </nav>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['method'] ?>

</section>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

