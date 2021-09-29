<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-wrench text-primary-900 mr-2"></i> <?= language()->admin_settings->header ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="row">
    <div class="mb-3 mb-xl-5 mb-xl-0 col-12 col-xl-3">
        <div class="d-xl-none">
            <select name="settings_menu" class="form-control">
                <option value="<?= url('admin/settings/main') ?>" class="nav-link"><?= language()->admin_settings->tab->main ?></option>
                <option value="<?= url('admin/settings/links') ?>" class="nav-link"><?= language()->admin_settings->tab->links ?></option>
                <option value="<?= url('admin/settings/payment') ?>" class="nav-link"><?= language()->admin_settings->tab->payment ?></option>
                <option value="<?= url('admin/settings/paypal') ?>" class="nav-link"><?= language()->admin_settings->tab->paypal ?></option>
                <option value="<?= url('admin/settings/stripe') ?>" class="nav-link"><?= language()->admin_settings->tab->stripe ?></option>
                <option value="<?= url('admin/settings/offline_payment') ?>" class="nav-link"><?= language()->admin_settings->tab->offline_payment ?></option>
                <option value="<?= url('admin/settings/coinbase') ?>" class="nav-link"><?= language()->admin_settings->tab->coinbase ?></option>
                <option value="<?= url('admin/settings/affiliate') ?>" class="nav-link"><?= language()->admin_settings->tab->affiliate ?></option>
                <option value="<?= url('admin/settings/business') ?>" class="nav-link"><?= language()->admin_settings->tab->business ?></option>
                <option value="<?= url('admin/settings/captcha') ?>" class="nav-link"><?= language()->admin_settings->tab->captcha ?></option>
                <option value="<?= url('admin/settings/facebook') ?>" class="nav-link"><?= language()->admin_settings->tab->facebook ?></option>
                <option value="<?= url('admin/settings/google') ?>" class="nav-link"><?= language()->admin_settings->tab->google ?></option>
                <option value="<?= url('admin/settings/twitter') ?>" class="nav-link"><?= language()->admin_settings->tab->twitter ?></option>
                <option value="<?= url('admin/settings/ads') ?>" class="nav-link"><?= language()->admin_settings->tab->ads ?></option>
                <option value="<?= url('admin/settings/socials') ?>" class="nav-link"><?= language()->admin_settings->tab->socials ?></option>
                <option value="<?= url('admin/settings/smtp') ?>" class="nav-link"><?= language()->admin_settings->tab->smtp ?></option>
                <option value="<?= url('admin/settings/custom') ?>" class="nav-link"><?= language()->admin_settings->tab->custom ?></option>
                <option value="<?= url('admin/settings/announcements') ?>" class="nav-link"><?= language()->admin_settings->tab->announcements ?></option>
                <option value="<?= url('admin/settings/email_notifications') ?>" class="nav-link"><?= language()->admin_settings->tab->email_notifications ?></option>
                <option value="<?= url('admin/settings/webhooks') ?>" class="nav-link"><?= language()->admin_settings->tab->webhooks ?></option>
                <option value="<?= url('admin/settings/offload') ?>" class="nav-link"><?= language()->admin_settings->tab->offload ?></option>
                <option value="<?= url('admin/settings/cron') ?>" class="nav-link"><?= language()->admin_settings->tab->cron ?></option>
                <option value="<?= url('admin/settings/license') ?>" class="nav-link"><?= language()->admin_settings->tab->license ?></option>
            </select>
        </div>

        <?php ob_start() ?>
        <script>
            document.querySelector('select[name="settings_menu"]').addEventListener('change', event => {
                document.querySelector(`a[href="${event.currentTarget.value}"]`).click();
            })
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

        <div class="nav flex-column nav-pills d-none d-xl-flex" role="tablist" aria-orientation="vertical">
            <a class="nav-link <?= $data->method == 'main' ? 'active' : null ?>" href="<?= url('admin/settings/main') ?>"><i class="fa fa-fw fa-sm fa-home mr-1"></i> <?= language()->admin_settings->tab->main ?></a>
            <a class="nav-link <?= $data->method == 'links' ? 'active' : null ?>" href="<?= url('admin/settings/links') ?>"><i class="fa fa-fw fa-sm fa-link mr-1"></i> <?= language()->admin_settings->tab->links ?></a>
            <a class="nav-link <?= $data->method == 'payment' ? 'active' : null ?>" href="<?= url('admin/settings/payment') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= language()->admin_settings->tab->payment ?></a>
            <a class="nav-link <?= $data->method == 'paypal' ? 'active' : null ?>" href="<?= url('admin/settings/paypal') ?>"><i class="fab fa-fw fa-sm fa-paypal mr-1"></i> <?= language()->admin_settings->tab->paypal ?></a>
            <a class="nav-link <?= $data->method == 'stripe' ? 'active' : null ?>" href="<?= url('admin/settings/stripe') ?>"><i class="fab fa-fw fa-sm fa-stripe mr-1"></i> <?= language()->admin_settings->tab->stripe ?></a>
            <a class="nav-link <?= $data->method == 'offline_payment' ? 'active' : null ?>" href="<?= url('admin/settings/offline_payment') ?>"><i class="fa fa-fw fa-sm fa-university mr-1"></i> <?= language()->admin_settings->tab->offline_payment ?></a>
            <a class="nav-link <?= $data->method == 'coinbase' ? 'active' : null ?>" href="<?= url('admin/settings/coinbase') ?>"><i class="fab fa-fw fa-sm fa-bitcoin mr-1"></i> <?= language()->admin_settings->tab->coinbase ?></a>
            <a class="nav-link <?= $data->method == 'affiliate' ? 'active' : null ?>" href="<?= url('admin/settings/affiliate') ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= language()->admin_settings->tab->affiliate ?></a>
            <a class="nav-link <?= $data->method == 'business' ? 'active' : null ?>" href="<?= url('admin/settings/business') ?>"><i class="fa fa-fw fa-sm fa-briefcase mr-1"></i> <?= language()->admin_settings->tab->business ?></a>
            <a class="nav-link <?= $data->method == 'captcha' ? 'active' : null ?>" href="<?= url('admin/settings/captcha') ?>"><i class="fa fa-fw fa-sm fa-low-vision mr-1"></i> <?= language()->admin_settings->tab->captcha ?></a>
            <a class="nav-link <?= $data->method == 'facebook' ? 'active' : null ?>" href="<?= url('admin/settings/facebook') ?>"><i class="fab fa-fw fa-sm fa-facebook mr-1"></i> <?= language()->admin_settings->tab->facebook ?></a>
            <a class="nav-link <?= $data->method == 'google' ? 'active' : null ?>" href="<?= url('admin/settings/google') ?>"><i class="fab fa-fw fa-sm fa-google mr-1"></i> <?= language()->admin_settings->tab->google ?></a>
            <a class="nav-link <?= $data->method == 'twitter' ? 'active' : null ?>" href="<?= url('admin/settings/twitter') ?>"><i class="fab fa-fw fa-sm fa-twitter mr-1"></i> <?= language()->admin_settings->tab->twitter ?></a>
            <a class="nav-link <?= $data->method == 'ads' ? 'active' : null ?>" href="<?= url('admin/settings/ads') ?>"><i class="fa fa-fw fa-sm fa-ad mr-1"></i> <?= language()->admin_settings->tab->ads ?></a>
            <a class="nav-link <?= $data->method == 'socials' ? 'active' : null ?>" href="<?= url('admin/settings/socials') ?>"><i class="fab fa-fw fa-sm fa-instagram mr-1"></i> <?= language()->admin_settings->tab->socials ?></a>
            <a class="nav-link <?= $data->method == 'smtp' ? 'active' : null ?>" href="<?= url('admin/settings/smtp') ?>"><i class="fa fa-fw fa-sm fa-mail-bulk mr-1"></i> <?= language()->admin_settings->tab->smtp ?></a>
            <a class="nav-link <?= $data->method == 'custom' ? 'active' : null ?>" href="<?= url('admin/settings/custom') ?>"><i class="fa fa-fw fa-sm fa-paint-brush mr-1"></i> <?= language()->admin_settings->tab->custom ?></a>
            <a class="nav-link <?= $data->method == 'announcements' ? 'active' : null ?>" href="<?= url('admin/settings/announcements') ?>"><i class="fa fa-fw fa-sm fa-bullhorn mr-1"></i> <?= language()->admin_settings->tab->announcements ?></a>
            <a class="nav-link <?= $data->method == 'email_notifications' ? 'active' : null ?>" href="<?= url('admin/settings/email_notifications') ?>"><i class="fa fa-fw fa-sm fa-bell mr-1"></i> <?= language()->admin_settings->tab->email_notifications ?></a>
            <a class="nav-link <?= $data->method == 'webhooks' ? 'active' : null ?>" href="<?= url('admin/settings/webhooks') ?>"><i class="fa fa-fw fa-sm fa-code-branch mr-1"></i> <?= language()->admin_settings->tab->webhooks ?></a>
            <a class="nav-link <?= $data->method == 'offload' ? 'active' : null ?>" href="<?= url('admin/settings/offload') ?>"><i class="fa fa-fw fa-sm fa-copy mr-1"></i> <?= language()->admin_settings->tab->offload ?></a>
            <a class="nav-link <?= $data->method == 'cron' ? 'active' : null ?>" href="<?= url('admin/settings/cron') ?>"><i class="fa fa-fw fa-sm fa-sync mr-1"></i> <?= language()->admin_settings->tab->cron ?></a>
            <a class="nav-link <?= $data->method == 'license' ? 'active' : null ?>" href="<?= url('admin/settings/license') ?>"><i class="fa fa-fw fa-sm fa-key mr-1"></i> <?= language()->admin_settings->tab->license ?></a>
        </div>
    </div>

    <div class="col">
        <div class="card">
            <div class="card-body">

                <form action="<?= url('admin/settings/' . $data->method) ?>" method="post" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                    <?= $this->views['method'] ?>
                </form>

            </div>
        </div>

        <p class="text-muted my-3"><?= language()->admin_settings->documentation ?></p>
    </div>
</div>
