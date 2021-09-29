<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<?php foreach($data->total as $key => $value): ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="<?= $data->biolink_blocks[$key]['icon'] ?> fa-xs text-muted"></i> <?= language()->link->biolink->blocks->{$key} ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total[$key]) ?></span> <?= language()->link->biolink->blocks->{$key} ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="<?= $key ?>"></canvas>
        </div>
    </div>
</div>
<?php endforeach ?>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    'use strict';

    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    <?php foreach($data->total as $key => $value): ?>
    let <?= $key ?>_chart = document.getElementById('<?= $key ?>').getContext('2d');

    color_gradient = <?= $key ?>_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    new Chart(<?= $key ?>_chart, {
        type: 'line',
        data: {
            labels: <?= $data->biolinks_blocks_chart[$key]['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(language()->link->biolink->blocks->{$key}) ?>,
                    data: <?= $data->biolinks_blocks_chart[$key]['total'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
    <?php endforeach ?>
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
