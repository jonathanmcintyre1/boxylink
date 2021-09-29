<?php defined('ALTUMCODE') || die() ?>

<body class="<?= language()->direction == 'rtl' ? 'rtl' : null ?> link-body <?= $data->link->design->background_class ?>" style="<?= $data->link->design->background_style ?>">
<div class="container animate__animated animate__fadeIn">
    <div class="row d-flex justify-content-center text-center">
        <div class="col-md-8 link-content <?= isset($_GET['preview']) ? 'container-disabled-simple' : null ?>">

            <?php require THEME_PATH . 'views/partials/ads_header_biolink.php' ?>

            <main id="links" class="my-3">
                <div class="row">
                    <div id="link-verified-wrapper-top" class="col-12 my-2 text-center" style="<?= $data->user->plan_settings->verified && $data->link->settings->display_verified && $data->link->settings->verified_location == 'top' ? null : 'display: none;' ?>">
                        <div>
                            <small class="link-verified"><i class="fa fa-fw fa-check-circle fa-1x"></i> <?= language()->link->biolink->verified ?></small>
                        </div>
                    </div>

                    <?php if($data->biolink_blocks): ?>
                        <?php foreach($data->biolink_blocks as $row): ?>

                            <?php

                            /* Check if its a scheduled link and we should show it or not */
                            if(
                                !empty($row->start_date) &&
                                !empty($row->end_date) &&
                                (
                                    \Altum\Date::get('', null) < \Altum\Date::get($row->start_date, null, \Altum\Date::$default_timezone) ||
                                    \Altum\Date::get('', null) > \Altum\Date::get($row->end_date, null, \Altum\Date::$default_timezone)
                                )
                            ) {
                                continue;
                            }

                            /* Check if the user has permissions to use the link */
                            if(!$data->user->plan_settings->enabled_biolink_blocks->{$row->type}) {
                                continue;
                            }

                            $row->utm = $data->link->settings->utm;

                            ?>

                            <?= \Altum\Link::get_biolink_link($row, $data->user) ?? null ?>

                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </main>

            <?php require THEME_PATH . 'views/partials/ads_footer_biolink.php' ?>

            <footer class="link-footer">
                <div id="link-verified-wrapper-bottom" class="my-3" style="<?= $data->user->plan_settings->verified && $data->link->settings->display_verified && $data->link->settings->verified_location == 'bottom' ? null : 'display: none;' ?>">
                    <small class="link-verified"><i class="fa fa-fw fa-check-circle fa-1x"></i> <?= language()->link->biolink->verified ?></small>
                </div>

                <?php if($data->link->settings->display_branding): ?>
                    <?php if(isset($data->link->settings->branding, $data->link->settings->branding->name, $data->link->settings->branding->url) && !empty($data->link->settings->branding->name)): ?>
                        <a id="branding" class="link-footer-branding" href="<?= !empty($data->link->settings->branding->url) ? $data->link->settings->branding->url : '#' ?>" style="<?= $data->link->design->text_style ?>"><?= $data->link->settings->branding->name ?></a>
                    <?php else: ?>
                        <a id="branding" class="link-footer-branding" href="<?= url() ?>" style="<?= $data->link->design->text_style ?>"><?= settings()->links->branding ?></a>
                    <?php endif ?>
                <?php endif ?>
            </footer>

        </div>
    </div>
</div>

<?= \Altum\Event::get_content('modals') ?>
</body>

<?php ob_start() ?>
<script>
    let base_url = <?= json_encode(SITE_URL) ?>;

    /* Internal tracking for biolink links */
    $('a[data-biolink-block-id]').on('click', event => {
        let biolink_block_id = $(event.currentTarget).data('biolink-block-id');

        $.ajax(`${base_url}l/link?biolink_block_id=${biolink_block_id}&no_redirect`);
    });
</script>

<?= $this->views['pixels'] ?? null ?>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

