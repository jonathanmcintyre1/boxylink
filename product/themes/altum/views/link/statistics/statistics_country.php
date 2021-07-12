<?php defined('ALTUMCODE') || die() ?>

<div class="card my-3">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->country ?></h3>
        <p class="text-muted mb-3"><?= $this->language->link->statistics->country_help ?></p>

        <?php foreach($data->rows as $row): ?>
            <?php $percentage = round($row->total / $data->total_sum * 100, 1) ?>

            <div class="mt-4">
                <div class="d-flex justify-content-between mb-1">
                    <div class="text-truncate">
                        <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/countries/' . ($row->country_code ? strtolower($row->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                        <?php if($row->country_code): ?>
                            <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=city_name&country_code=' . $row->country_code . '&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" title="<?= $row->country_code ?>" class="align-middle"><?= $row->country_code ? get_country_from_country_code($row->country_code) : $this->language->link->statistics->country_unknown ?></a>
                        <?php else: ?>
                            <span class="align-middle"><?= $row->country_code ? get_country_from_country_code($row->country_code) : $this->language->link->statistics->country_unknown ?></span>
                        <?php endif ?>
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
