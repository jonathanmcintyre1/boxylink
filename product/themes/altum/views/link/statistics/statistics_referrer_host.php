<?php defined('ALTUMCODE') || die() ?>

<div class="card my-3">
    <div class="card-body">
        <h3 class="h5"><?= $this->language->link->statistics->referrer_host ?></h3>
        <p class="text-muted mb-3"><?= $this->language->link->statistics->referrer_help ?></p>

        <?php foreach($data->rows as $row): ?>
            <?php $percentage = round($row->total / $data->total_sum * 100, 1) ?>

            <div class="mt-4">
                <div class="d-flex justify-content-between mb-1">
                    <div class="text-truncate">
                        <?php if(!$row->referrer_host): ?>
                            <span><?= $this->language->link->statistics->referrer_direct ?></span>
                        <?php elseif($row->referrer_host == 'qr'): ?>
                            <span><?= $this->language->link->statistics->referrer_qr ?></span>
                        <?php else: ?>
                            <img src="https://external-content.duckduckgo.com/ip3/<?= $row->referrer_host ?>.ico" class="img-fluid icon-favicon mr-1" />
                            <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=referrer_path&referrer_host=' . $row->referrer_host . '&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" title="<?= $row->referrer_host ?>" class="align-middle"><?= $row->referrer_host ?></a>
                            <a href="<?= 'https://' . $row->referrer_host ?>" target="_blank" rel="nofollow noopener" class="text-muted ml-1"><i class="fa fa-fw fa-xs fa-external-link-alt"></i></a>
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
