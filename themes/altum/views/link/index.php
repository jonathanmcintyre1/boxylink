<?php defined('ALTUMCODE') || die() ?>

<input type="hidden" name="link_base" value="<?= $this->link->domain ? $this->link->domain->url : url() ?>" />

<header class="mb-6">
    <div class="container">

        <nav aria-label="breadcrumb">
            <small>
                <ol class="custom-breadcrumbs">
                    <li><a href="<?= url('dashboard') ?>"><?= language()->dashboard->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li><a href="<?= url('links') ?>"><?= language()->links->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                    <li class="active" aria-current="page">
                        <?= language()->link->{'breadcrumb_' . $data->link->type} . ' ' . language()->link->{$data->method}->breadcrumb ?>
                    </li>
                </ol>
            </small>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <div class="d-flex align-items-center">
                <h1 id="link_url" class="h3 mr-3"><?= sprintf(language()->link->header->header, $data->link->url) ?></h1>

                <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= language()->links->is_enabled_tooltip ?>">
                    <input
                            type="checkbox"
                            class="custom-control-input"
                            id="link_is_enabled_<?= $data->link->link_id ?>"
                            data-row-id="<?= $data->link->link_id ?>"
                            onchange="ajax_call_helper(event, 'link-ajax', 'is_enabled_toggle')"
                        <?= $data->link->is_enabled ? 'checked="checked"' : null ?>
                    >
                    <label class="custom-control-label clickable" for="link_is_enabled_<?= $data->link->link_id ?>"></label>
                </div>

                <button
                        type="button"
                        class="btn btn-link text-muted"
                        data-toggle="tooltip"
                        title="<?= language()->global->clipboard_copy ?>"
                        aria-label="<?= language()->global->clipboard_copy ?>"
                        data-copy="<?= language()->global->clipboard_copy ?>"
                        data-copied="<?= language()->global->clipboard_copied ?>"
                        data-clipboard-text="<?= $data->link->full_url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>

                <div class="dropdown">
                    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown">
                        <i class="fa fa-fw fa-ellipsis-v"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="<?= url('link/' . $data->link->link_id) ?>" class="dropdown-item"><i class="fa fa-fw fa-pencil-alt"></i> <?= language()->global->edit ?></a>
                        <a href="<?= url('link/' . $data->link->link_id . '/statistics') ?>" class="dropdown-item"><i class="fa fa-fw fa-chart-bar"></i> <?= language()->link->statistics->link ?></a>
                        <a href="<?= url('link/' . $data->link->link_id . '/qr') ?>" class="dropdown-item" rel="noreferrer"><i class="fa fa-fw fa-qrcode"></i> <?= language()->link->qr->link ?></a>
                        <a href="#" data-toggle="modal" data-target="#link_delete" class="dropdown-item" data-link-id="<?= $data->link->link_id ?>"><i class="fa fa-fw fa-times"></i> <?= language()->global->delete ?></a>
                    </div>
                </div>
            </div>

            <div class="d-none d-md-block">
                <?php if($data->method != 'statistics'): ?>
                <a href="<?= url('link/' . $data->link->link_id . '/statistics') ?>" class="btn btn-light mr-3"><i class="fa fa-fw fa-sm fa-chart-bar"></i> <?= language()->link->statistics->link ?></a>
                <?php endif ?>

                <?php if($data->method != 'settings'): ?>
                <a href="<?= url('link/' . $data->link->link_id . '/settings') ?>" class="btn btn-light mr-3"><i class="fa fa-fw fa-sm fa-cog"></i> <?= language()->link->settings->link ?></a>
                <?php endif ?>
            </div>
        </div>

        <div class="d-flex align-items-baseline">
            <span class="mr-1" data-toggle="tooltip" title="<?= language()->link->{$data->link->type}->name ?>">
                <i class="fa fa-fw fa-circle fa-sm" style="color: <?= language()->link->{$data->link->type}->color ?>"></i>
            </span>

            <div class="col-8 col-md-auto text-muted text-truncate">
                <?= sprintf(language()->link->header->subheader, '<a id="link_full_url" href="' . $data->link->full_url . '" target="_blank" rel="noreferrer">' . $data->link->full_url . '</a>') ?>
            </div>
        </div>
    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['method'] ?>

</section>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/pickr.min.css' ?>" rel="stylesheet" media="screen">
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
