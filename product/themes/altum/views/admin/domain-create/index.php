<?php defined('ALTUMCODE') || die() ?>

<div class="mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-globe text-primary-900 mr-2"></i> <?= $this->language->admin_domain_create->header ?></h1>
    <p class="text-muted"><?= $this->language->admin_domain_create->subheader ?></p>
</div>

<?php display_notifications() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <?php $url = parse_url(SITE_URL); $host = $url['host'] . (strlen($url['path']) > 1 ? $url['path'] : null); ?>

            <p class="text-muted"><?= sprintf($this->language->admin_domains->main->helper, '<strong>' . $_SERVER['SERVER_ADDR'] . '</strong>', '<strong>' . $host . '</strong>') ?></p>

            <div class="form-group">
                <label><?= $this->language->admin_domains->main->host ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select name="scheme" class="appearance-none select-custom-altum form-control form-control-lg input-group-text">
                            <option value="https://">https://</option>
                            <option value="http://">http://</option>
                        </select>
                    </div>

                    <input type="text" class="form-control form-control-lg" name="host" placeholder="<?= $this->language->admin_domains->main->host_placeholder ?>" required="required" />
                </div>
                <small class="form-text text-muted"><?= $this->language->admin_domains->main->host_help ?></small>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_domains->main->custom_index_url ?></label>
                <input type="text" class="form-control form-control-lg" name="custom_index_url" placeholder="<?= $this->language->admin_domains->main->custom_index_url_placeholder ?>" />
                <small class="form-text text-muted"><?= $this->language->admin_domains->main->custom_index_url_help ?></small>
            </div>

            <div class="form-group">
                <label><?= $this->language->admin_domains->main->is_enabled ?></label>

                <select name="is_enabled" class="form-control form-control-lg">
                    <option value="1"><?= $this->language->global->active ?></option>
                    <option value="0"><?= $this->language->global->disabled ?></option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary"><?= $this->language->global->create ?></button>
            </div>
        </form>

    </div>
</div>

