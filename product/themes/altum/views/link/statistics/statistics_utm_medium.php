<?php defined('ALTUMCODE') || die() ?>

<div class="card my-3">
    <div class="card-body">
        <h3 class="h5"><?= sprintf($this->language->link->statistics->utm_medium, $data->utm_source) ?></h3>

        <?php foreach($data->rows as $row): ?>
            <?php $percentage = round($row->total / $data->total_sum * 100, 1) ?>

            <div class="mt-4">
                <div class="d-flex justify-content-between mb-1">
                    <div class="text-truncate">
                        <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=utm_campaign&utm_source=' . $data->utm_source . '&utm_medium=' . $row->utm_medium . '&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>"><?= $row->utm_medium ?></a>
                    </div>

                    <div>
                        <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                        <span class="ml-3"><?= nr($row->total) ?></span>
                    </div>
                </div>

                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
