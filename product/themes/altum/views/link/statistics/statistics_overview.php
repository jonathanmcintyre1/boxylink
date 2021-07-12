<?php defined('ALTUMCODE') || die() ?>

<div class="row">

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= $this->language->link->statistics->country ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['country_code'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['country_code_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <img src="<?= SITE_URL . ASSETS_URL_PATH . 'images/countries/' . ($key ? strtolower($key) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                                <?php if($key): ?>
                                    <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=city_name&country_code=' . $key . '&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" title="<?= $key ?>" class="align-middle"><?= $key ? get_country_from_country_code($key) : $this->language->link->statistics->country_unknown ?></a>
                                <?php else: ?>
                                    <span class="align-middle"><?= $key ? get_country_from_country_code($key) : $this->language->link->statistics->country_unknown ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="px-3 py-2">
                <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=country&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" class="text-muted"><?= $this->language->link->statistics->view_more ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= $this->language->link->statistics->referrer_host ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['referrer_host'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['referrer_host_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= $this->language->link->statistics->referrer_direct ?></span>
                                <?php elseif($key == 'qr'): ?>
                                    <span><?= $this->language->link->statistics->referrer_qr ?></span>
                                <?php else: ?>
                                    <img src="https://external-content.duckduckgo.com/ip3/<?= $key ?>.ico" class="img-fluid icon-favicon mr-1" />
                                    <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=referrer_path&referrer_host=' . $key . '&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" title="<?= $key ?>" class="align-middle"><?= $key ?></a>
                                    <a href="<?= 'https://' . $key ?>" target="_blank" rel="nofollow noopener" class="text-muted ml-1"><i class="fa fa-fw fa-xs fa-external-link-alt"></i></a>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="px-3 py-2">
                <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=referrer_host&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" class="text-muted"><?= $this->language->link->statistics->view_more ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= $this->language->link->statistics->device ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['device_type'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['device_type_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= $this->language->link->statistics->device_type_unknown ?></span>
                                <?php else: ?>
                                    <span><?= $this->language->link->statistics->{'device_' . $key} ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="px-3 py-2">
                <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=device&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" class="text-muted"><?= $this->language->link->statistics->view_more ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= $this->language->link->statistics->os ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['os_name'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['os_name_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= $this->language->link->statistics->os_unknown ?></span>
                                <?php else: ?>
                                    <span><?= $key ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="px-3 py-2">
                <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=os&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" class="text-muted"><?= $this->language->link->statistics->view_more ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= $this->language->link->statistics->browser ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['browser_name'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['browser_name_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= $this->language->link->statistics->browser_unknown ?></span>
                                <?php else: ?>
                                    <span><?= $key ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="px-3 py-2">
                <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=browser&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" class="text-muted"><?= $this->language->link->statistics->view_more ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= $this->language->link->statistics->language ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['browser_language'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['browser_language_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= $this->language->link->statistics->language_unknown ?></span>
                                <?php else: ?>
                                    <span><?= get_language_from_locale($key) ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="px-3 py-2">
                <a href="<?= url('link/' . $data->link->link_id . '/' . $data->method . '?type=language&start_date=' . $data->date->start_date . '&end_date=' . $data->date->end_date) ?>" class="text-muted"><?= $this->language->link->statistics->view_more ?></a>
            </div>
        </div>
    </div>

</div>

