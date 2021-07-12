<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-3">
    <h2 class="h4 mr-3"><?= $this->language->link->statistics->header ?></h2>

    <div>
        <button
                id="daterangepicker"
                type="button"
                class="btn btn-sm btn-outline-primary"
                data-min-date="<?= \Altum\Date::get($data->link->date, 4) ?>"
                data-max-date="<?= \Altum\Date::get('', 4) ?>"
        >
            <i class="fa fa-fw fa-calendar mr-1"></i>
            <span>
                <?php if($data->date->start_date == $data->date->end_date): ?>
                    <?= \Altum\Date::get($data->date->start_date, 2, \Altum\Date::$default_timezone) ?>
                <?php else: ?>
                    <?= \Altum\Date::get($data->date->start_date, 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->date->end_date, 2, \Altum\Date::$default_timezone) ?>
                <?php endif ?>
            </span>
            <i class="fa fa-fw fa-caret-down ml-1"></i>
        </button>
    </div>
</div>

<?php if(!count($data->pageviews)): ?>


    <div class="d-flex flex-column align-items-center justify-content-center">
        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-5 mb-3" alt="<?= $this->language->link->statistics->no_data ?>" />
        <h2 class="h4 text-muted mt-3"><?= $this->language->link->statistics->no_data ?></h2>
        <p class="text-muted"><?= $this->language->link->statistics->no_data_help ?></p>
    </div>

<?php else: ?>

    <div class="chart-container mb-5">
        <canvas id="pageviews_chart"></canvas>
    </div>

    <ul class="nav nav-pills flex-column flex-lg-row mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'overview' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=overview&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-list mr-1"></i>
                <?= $this->language->link->statistics->overview ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($data->type, ['country', 'city_name']) ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=country&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-globe mr-1"></i>
                <?= $this->language->link->statistics->country ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($data->type, ['referrer_host', 'referrer_path']) ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=referrer_host&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-random mr-1"></i>
                <?= $this->language->link->statistics->referrer_host ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'device' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=device&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-laptop mr-1"></i>
                <?= $this->language->link->statistics->device ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'os' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=os&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-server mr-1"></i>
                <?= $this->language->link->statistics->os ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'browser' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=browser&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-window-restore mr-1"></i>
                <?= $this->language->link->statistics->browser ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $data->type == 'language' ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=language&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-language mr-1"></i>
                <?= $this->language->link->statistics->language ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= in_array($data->type, ['utm_source', 'utm_medium', 'utm_campaign']) ? 'active' : null ?>" href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=utm_source&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>">
                <i class="fa fa-fw fa-sm fa-link mr-1"></i>
                <?= $this->language->link->statistics->utms ?>
            </a>
        </li>
    </ul>

    <?= $this->views['statistics'] ?>

<?php endif ?>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/chartjs_defaults.js' ?>"></script>

<script>
    'use strict';

    moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

    /* Daterangepicker */
    $('#daterangepicker').daterangepicker({
        startDate: <?= json_encode($data->date->start_date) ?>,
        endDate: <?= json_encode($data->date->end_date) ?>,
        minDate: $('#daterangepicker').data('min-date'),
        maxDate: $('#daterangepicker').data('max-date'),
        ranges: {
            <?= json_encode($this->language->global->date->today) ?>: [moment(), moment()],
            <?= json_encode($this->language->global->date->yesterday) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            <?= json_encode($this->language->global->date->last_7_days) ?>: [moment().subtract(6, 'days'), moment()],
            <?= json_encode($this->language->global->date->last_30_days) ?>: [moment().subtract(29, 'days'), moment()],
            <?= json_encode($this->language->global->date->this_month) ?>: [moment().startOf('month'), moment().endOf('month')],
            <?= json_encode($this->language->global->date->last_month) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            <?= json_encode($this->language->global->date->all_time) ?>: [moment($('#daterangepicker').data('min-date')), moment()]
        },
        alwaysShowCalendars: true,
        linkedCalendars: false,
        singleCalendar: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {

        /* Redirect */
        redirect(`<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=' . $data->type) ?>&start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });

    /* Charts */
    <?php if(count($data->pageviews)): ?>
    let pageviews_chart = document.getElementById('pageviews_chart').getContext('2d');

    let gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(56, 178, 172, 0.6)');
    gradient.addColorStop(1, 'rgba(56, 178, 172, 0.05)');

    let gradient_white = pageviews_chart.createLinearGradient(0, 0, 0, 250);
    gradient_white.addColorStop(0, 'rgba(56, 62, 178, 0.6)');
    gradient_white.addColorStop(1, 'rgba(56, 62, 178, 0.05)');

    new Chart(pageviews_chart, {
        type: 'line',
        data: {
            labels: <?= $data->pageviews_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode($this->language->link->statistics->pageviews) ?>,
                    data: <?= $data->pageviews_chart['pageviews'] ?? '[]' ?>,
                    backgroundColor: gradient,
                    borderColor: '#38B2AC',
                    fill: true
                },
                {
                    label: <?= json_encode($this->language->link->statistics->visitors) ?>,
                    data: <?= $data->pageviews_chart['visitors'] ?? '[]' ?>,
                    backgroundColor: gradient_white,
                    borderColor: '#383eb2',
                    fill: true
                }
            ]
        },
        options: chart_options
    });

    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
