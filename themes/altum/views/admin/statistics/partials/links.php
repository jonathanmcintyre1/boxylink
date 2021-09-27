<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-link fa-xs text-muted"></i> <?= language()->admin_statistics->links->shortened_links->header ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['shortened_links']) ?></span> <?= language()->admin_statistics->links->shortened_links->chart ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="shortened_links"></canvas>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-hashtag fa-xs text-muted"></i> <?= language()->admin_statistics->links->biolink_links->header ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['biolink_links']) ?></span> <?= language()->admin_statistics->links->biolink_links->chart ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="biolink_links"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Prepare chart */
    let shortened_links_chart = document.getElementById('shortened_links').getContext('2d');
    color_gradient = shortened_links_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    /* Display chart */
    new Chart(shortened_links_chart, {
        type: 'line',
        data: {
            labels: <?= $data->shortened_links_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode(language()->admin_statistics->links->shortened_links->chart) ?>,
                data: <?= $data->shortened_links_chart['shortened_links'] ?? '[]' ?>,
                backgroundColor: color_gradient,
                borderColor: color,
                fill: true
            }]
        },
        options: chart_options
    });

    /* Prepare chart */
    let biolink_links_chart = document.getElementById('biolink_links').getContext('2d');
    color_gradient = biolink_links_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    /* Display chart */
    new Chart(biolink_links_chart, {
        type: 'line',
        data: {
            labels: <?= $data->biolink_links_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode(language()->admin_statistics->links->biolink_links->chart) ?>,
                data: <?= $data->biolink_links_chart['biolink_links'] ?? '[]' ?>,
                backgroundColor: color_gradient,
                borderColor: color,
                fill: true
            }]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
