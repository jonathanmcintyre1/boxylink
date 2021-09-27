<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-globe fa-xs text-muted"></i> <?= language()->admin_statistics->domains->header ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['domains']) ?></span> <?= language()->admin_statistics->domains->chart ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="domains"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Prepare chart */
    let domains_chart = document.getElementById('domains').getContext('2d');
    color_gradient = domains_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    /* Display chart */
    new Chart(domains_chart, {
        type: 'line',
        data: {
            labels: <?= $data->domains_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode(language()->admin_statistics->domains->chart) ?>,
                data: <?= $data->domains_chart['domains'] ?? '[]' ?>,
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
