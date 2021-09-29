<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <?= $this->views['account_header'] ?>
    </div>
</header>

<section class="container pt-5">

    <?= \Altum\Alerts::output_alerts() ?>

    <h1 class="h3"><?= language()->referrals->invite->header ?></h1>
    <p class="text-muted"><?= sprintf(language()->referrals->invite->{'subheader_' . settings()->affiliate->commission_type}, '<strong>' . settings()->title . '</strong>', '<strong>' . settings()->affiliate->commission_percentage . '%</strong>') ?></p>

    <div class="card">
        <div class="card-body">

            <div <?= $this->user->plan_settings->affiliate_is_enabled ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                <div class="form-group <?= $this->user->plan_settings->affiliate_is_enabled ? null : 'container-disabled' ?>">
                    <label for="referral_key"><?= language()->referrals->invite->referral_key ?></label>
                    <input type="text" id="referral_key" name="referral_key" value="<?= SITE_URL . 'refer/' . $this->user->referral_key ?>" class="form-control" readonly="readonly" />
                </div>
            </div>

            <button
                    id="url_copy"
                    type="button"
                    class="btn btn-block btn-outline-secondary"
                    data-toggle="tooltip"
                    title="<?= language()->global->clipboard_copy ?>"
                    aria-label="<?= language()->global->clipboard_copy ?>"
                    data-copy="<?= language()->global->clipboard_copy ?>"
                    data-copied="<?= language()->global->clipboard_copied ?>"
                    data-clipboard-text="<?= SITE_URL . 'refer/' . $this->user->referral_key ?>"
            >
                <i class="fa fa-fw fa-sm fa-copy"></i> <?= language()->referrals->invite->button ?>
            </button>
        </div>
    </div>

    <hr class="border-gray-50 my-4" />

    <h1 class="h4 mb-3"><?= language()->referrals->statistics->header ?></h1>

    <div class="row justify-content-between">
        <div class="col-12 col-xl mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body">

                    <div class="d-flex">
                        <span class="text-muted"><?= language()->referrals->statistics->referrals ?></span>

                        <span class="ml-1" data-toggle="tooltip" title="<?= language()->referrals->statistics->referrals_help ?>">
                            <i class="fa fa-fw fa-sm fa-info-circle text-muted"></i>
                        </span>
                    </div>

                    <div class="mt-3">
                        <span class="h3 m-0 text-blue-500"><?= nr($data->referrals_statistics->referrals) ?></span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-xl mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body">

                    <div class="d-flex">
                        <span class="text-muted"><?= language()->referrals->statistics->converted_referrals ?></span>

                        <span class="ml-1" data-toggle="tooltip" title="<?= language()->referrals->statistics->converted_referrals_help ?>">
                            <i class="fa fa-fw fa-sm fa-info-circle text-muted"></i>
                        </span>
                    </div>

                    <div class="mt-3">
                        <span class="h3 m-0 text-blue-500"><?= nr($data->referrals_statistics->converted_referrals) ?></span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-xl mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body">

                    <div class="d-flex">
                        <span class="text-muted"><?= language()->referrals->statistics->pending_affiliate_commissions ?></span>

                        <span class="ml-1" data-toggle="tooltip" title="<?= language()->referrals->statistics->pending_affiliate_commissions_help ?>">
                            <i class="fa fa-fw fa-sm fa-info-circle text-muted"></i>
                        </span>
                    </div>

                    <div class="mt-3">
                        <span class="h3 m-0 text-blue-500"><?= nr($data->pending_affiliate_commissions, 2) . ' ' . settings()->payment->currency ?></span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-xl mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body">

                    <div class="d-flex">
                        <span class="text-muted"><?= language()->referrals->statistics->approved_affiliate_commissions ?></span>

                        <span class="ml-1" data-toggle="tooltip" title="<?= language()->referrals->statistics->approved_affiliate_commissions_help ?>">
                            <i class="fa fa-fw fa-sm fa-info-circle text-muted"></i>
                        </span>
                    </div>

                    <div class="mt-3">
                        <span class="h3 m-0 text-blue-500"><?= nr($data->approved_affiliate_commissions, 2) . ' ' . settings()->payment->currency ?></span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr class="border-gray-50 my-4" />

    <h1 class="h4"><?= language()->referrals->withdraw->header ?></h1>
    <p class="text-muted"><?= sprintf(language()->referrals->withdraw->subheader, '<strong>' . settings()->affiliate->minimum_withdrawal_amount . ' ' . settings()->payment->currency . '</strong>') ?></p>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                <label for="amount"><?= language()->referrals->withdraw->amount ?></label>
                <div class="input-group mb-3">
                    <input type="number" id="amount" name="amount" value="<?= nr($data->approved_affiliate_commissions, 2) ?>" class="form-control" readonly="readonly" />
                    <div class="input-group-append">
                        <span class="input-group-text"><?= settings()->payment->currency ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="note"><?= settings()->affiliate->withdrawal_notes ?></label>
                    <textarea id="note" name="note" class="form-control"></textarea>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary" <?= $data->affiliate_commission_is_pending || ($data->approved_affiliate_commissions < settings()->affiliate->minimum_withdrawal_amount) ? 'disabled="disabled"' : null ?>><?= language()->global->submit ?></button>
            </form>

        </div>
    </div>

    <?php if(count($data->affiliate_withdrawals)): ?>
    <div class="mt-4">
        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= language()->referrals->withdraw->amount ?></th>
                    <th><?= language()->referrals->withdraw->is_paid ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach($data->affiliate_withdrawals as $row): ?>
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span><?= nr($row->amount, 2) . ' ' . settings()->payment->currency ?></span>
                                <small class="text-muted"><?= $row->note ?></small>
                            </div>
                        </td>
                        <td>
                            <?php if($row->is_paid): ?>
                                <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->referrals->withdraw->is_paid_paid ?></span>
                            <?php else: ?>
                                <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->referrals->withdraw->is_paid_pending ?></span>
                            <?php endif ?>
                        </td>
                        <td class="text-muted">
                            <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime) ?>">
                                <?= \Altum\Date::get($row->datetime, 2) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="mt-3"><?= $data->pagination ?></div>
    </div>
    <?php endif ?>

</section>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
