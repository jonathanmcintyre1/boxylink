<?php if(
    settings()->announcements->content
    && (!isset($_COOKIE['announcements_id']) || (isset($_COOKIE['announcements_id']) && $_COOKIE['announcements_id'] != settings()->announcements->id))
    && ((settings()->announcements->show_logged_in && \Altum\Middlewares\Authentication::check()) || (settings()->announcements->show_logged_out && !\Altum\Middlewares\Authentication::check()))
): ?>
    <div id="announcements" class="w-100 py-3" style="background-color: <?= settings()->announcements->background_color ?>;">
        <div class="container d-flex justify-content-center position-relative">
            <div style="color: <?= settings()->announcements->text_color ?>;"><?= settings()->announcements->content ?></div>

            <div class="position-absolute ml-3" style="right: 0;">
                <button id="announcements_close" type="button" class="close" data-dismiss="alert">
                    <i class="fa fa-fw fa-sm fa-times" style="color: <?= settings()->announcements->text_color ?>; opacity: .5;"></i>
                </button>
            </div>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        document.querySelector('#announcements_close').addEventListener('click', event => {
            document.querySelector('#announcements').style.display = 'none';
            set_cookie('announcements_id', <?= json_encode(settings()->announcements->id) ?>, 15, <?= json_encode(COOKIE_PATH) ?>);
        })
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>
