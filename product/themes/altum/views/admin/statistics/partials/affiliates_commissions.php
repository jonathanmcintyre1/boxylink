<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-wallet fa-xs text-muted"></i> <?= language()->admin_statistics->affiliates_commissions->header ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['total_affiliates_commissions']) ?></span> <?= language()->admin_statistics->affiliates_commissions->chart_total_affiliates_commissions ?>
            </div>
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['amount'], 2) . ' ' . settings()->payment->currency ?></span> <?= language()->admin_statistics->affiliates_commissions->chart_amount ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="affiliates_commissions"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    'use strict';

    let total_affiliates_commissions_color = css.getPropertyValue('--gray-500');
    let amount_color = css.getPropertyValue('--primary');

    /* Display chart */
    let affiliates_commissions_chart = document.getElementById('affiliates_commissions').getContext('2d');

    let amount_color_gradient = affiliates_commissions_chart.createLinearGradient(0, 0, 0, 250);
    amount_color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    amount_color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)')

    let total_affiliates_commissions_color_gradient = affiliates_commissions_chart.createLinearGradient(0, 0, 0, 250);
    total_affiliates_commissions_color_gradient.addColorStop(0, 'rgba(160, 174, 192, .1)');
    total_affiliates_commissions_color_gradient.addColorStop(1, 'rgba(160, 174, 192, 0.025)')

    new Chart(affiliates_commissions_chart, {
        type: 'line',
        data: {
            labels: <?= $data->affiliates_commissions_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(language()->admin_statistics->affiliates_commissions->chart_total_affiliates_commissions) ?>,
                    data: <?= $data->affiliates_commissions_chart['total_affiliates_commissions'] ?? '[]' ?>,
                    backgroundColor: total_affiliates_commissions_color_gradient,
                    borderColor: total_affiliates_commissions_color,
                    fill: true
                },
                {
                    label: <?= json_encode(language()->admin_statistics->affiliates_commissions->chart_amount) ?>,
                    data: <?= $data->affiliates_commissions_chart['amount'] ?? '[]' ?>,
                    backgroundColor: amount_color_gradient,
                    borderColor: amount_color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
