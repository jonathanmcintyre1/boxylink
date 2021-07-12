<?php defined('ALTUMCODE') || die() ?>

<header class="mb-6">
    <div class="container">
        <?php display_notifications() ?>

        <div class="row justify-content-between">
            <?php if($this->settings->links->domains_is_enabled): ?>
            <div class="col-12 col-lg mb-3 mb-xl-0">
                <div class="card h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-100 text-primary-600 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-globe fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->domains_total) ?></div>
                            <span class="text-muted"><?= $this->language->dashboard->header->domains ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>

            <div class="col-12 col-lg mb-3 mb-xl-0">
                <div class="card h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-100 text-primary-600 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-project-diagram fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->projects_total) ?></div>
                            <span class="text-muted"><?= $this->language->dashboard->header->projects ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg mb-3 mb-xl-0">
                <div class="card h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-100 text-primary-600 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-link fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->links_total) ?></div>
                            <span class="text-muted"><?= $this->language->dashboard->header->links ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg mb-3 mb-xl-0">
                <div class="card h-100">
                    <div class="card-body d-flex">

                        <div>
                            <div class="card border-0 bg-primary-100 text-primary-600 mr-3">
                                <div class="p-3 d-flex align-items-center justify-content-between">
                                    <i class="fa fa-fw fa-chart-bar fa-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card-title h4 m-0"><?= nr($data->links_clicks_total) ?></div>
                            <span class="text-muted"><?= $this->language->dashboard->header->clicks ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($data->links_chart): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="clicks_chart"></canvas>
                    </div>
                </div>
            </div>
        <?php endif ?>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?php display_notifications() ?>

    <?= $this->views['links_content'] ?>

</section>

<?php ob_start() ?>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= SITE_URL . ASSETS_URL_PATH . 'js/chartjs_defaults.js' ?>"></script>

<script>
    let clicks_chart = document.getElementById('clicks_chart').getContext('2d');

    let gradient = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(56, 178, 172, 0.6)');
    gradient.addColorStop(1, 'rgba(56, 178, 172, 0.05)');

    let gradient_white = clicks_chart.createLinearGradient(0, 0, 0, 250);
    gradient_white.addColorStop(0, 'rgba(56,62,178,0.6)');
    gradient_white.addColorStop(1, 'rgba(56, 62, 178, 0.05)');

    new Chart(clicks_chart, {
        type: 'line',
        data: {
            labels: <?= $data->links_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode($this->language->link->statistics->pageviews) ?>,
                    data: <?= $data->links_chart['pageviews'] ?? '[]' ?>,
                    backgroundColor: gradient,
                    borderColor: '#38B2AC',
                    fill: true
                },
                {
                    label: <?= json_encode($this->language->link->statistics->visitors) ?>,
                    data: <?= $data->links_chart['visitors'] ?? '[]' ?>,
                    backgroundColor: gradient_white,
                    borderColor: '#383eb2',
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

