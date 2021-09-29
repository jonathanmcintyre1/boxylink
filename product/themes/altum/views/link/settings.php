<?php defined('ALTUMCODE') || die() ?>

<?php

/* Get some variables */
$biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';

/* Get the proper settings depending on the type of link */
$settings = require THEME_PATH . 'views/link/settings/' . mb_strtolower($data->link->type) . '.php';

?>

<?= $settings->html ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>

<script>
    moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

    let update_main_url = (new_url) => {
        $('#link_url').text(new_url);

        let new_full_url = null;
        if($('select[name="domain_id"]').length) {
            let link_base = $('select[name="domain_id"]').find(':selected').text();
            new_full_url = `${link_base}${new_url}`;
        } else {
            new_full_url = `${$('input[name="link_base"]').val()}${new_url}`;
        }

        $('#link_full_url').text(new_full_url).attr('href', new_full_url);
        $('#link_full_url_copy').attr('data-clipboard-text', new_full_url);

        /* Refresh iframe */
        if($('#biolink_preview_iframe').length) {
            $('#biolink_preview_iframe').attr('src', $('#biolink_preview_iframe').attr('src'));
        }
    };
</script>

<?= $settings->javascript ?>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
