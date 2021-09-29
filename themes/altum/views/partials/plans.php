<?php defined('ALTUMCODE') || die() ?>

<?php if(settings()->payment->is_enabled): ?>

    <?php
    $plans = [];
    $available_payment_frequencies = [];

    $plans_result = database()->query("SELECT * FROM `plans` WHERE `status` = 1 ORDER BY `order`");

    while($plan = $plans_result->fetch_object()) {
        $plans[] = $plan;

        foreach(['monthly', 'annual', 'lifetime'] as $value) {
            if($plan->{$value . '_price'}) {
                $available_payment_frequencies[$value] = true;
            }
        }
    }

    ?>

    <?php if(count($plans)): ?>
        <div class="mb-5 d-flex justify-content-center">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">

                <?php if(isset($available_payment_frequencies['monthly'])): ?>
                    <label class="btn btn-outline-primary-900 active" data-payment-frequency="monthly">
                        <input type="radio" name="payment_frequency" checked="checked"> <?= language()->plan->custom_plan->monthly ?>
                    </label>
                <?php endif ?>

                <?php if(isset($available_payment_frequencies['annual'])): ?>
                    <label class="btn btn-outline-primary-900 <?= !isset($available_payment_frequencies['monthly']) ? 'active' : null ?>" data-payment-frequency="annual">
                        <input type="radio" name="payment_frequency" <?= !isset($available_payment_frequencies['monthly']) ? 'checked="checked"' : null ?>> <?= language()->plan->custom_plan->annual ?>
                    </label>
                <?php endif ?>

                <?php if(isset($available_payment_frequencies['lifetime'])): ?>
                    <label class="btn btn-outline-primary-900 <?= !isset($available_payment_frequencies['monthly']) && !isset($available_payment_frequencies['annual']) ? 'active' : null ?>" data-payment-frequency="lifetime">
                        <input type="radio" name="payment_frequency" <?= !isset($available_payment_frequencies['monthly']) && !isset($available_payment_frequencies['annual']) ? 'checked="checked"' : null ?>> <?= language()->plan->custom_plan->lifetime ?>
                    </label>
                <?php endif ?>

            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<div>
    <div class="row">

        <?php if(settings()->plan_free->status == 1): ?>

            <div class="col-12 col-md-6 col-lg-4 p-3">
                <div class="pricing-plan rounded" style="<?= settings()->plan_free->color ? 'border-color: ' . settings()->plan_free->color : null ?>">
                    <div class="pricing-header">
                        <span class="pricing-name"><?= settings()->plan_free->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount"><?= settings()->plan_free->price ?></span>
                        </div>

                        <div class="pricing-details"><?= settings()->plan_free->description ?></div>
                    </div>

                    <div class="pricing-body d-flex flex-column justify-content-between">
                        <?= include_view(THEME_PATH . 'views/partials/plans_plan_content.php', ['plan_settings' => settings()->plan_free->settings]) ?>

                        <a href="<?= url('register?redirect=pay/free') ?>" class="btn btn-lg btn-block btn-primary <?= \Altum\Middlewares\Authentication::check() && $this->user->plan_id != 'free' ? 'disabled' : null ?>"><?= language()->plan->button->choose ?></a>
                    </div>
                </div>
            </div>

        <?php endif ?>

        <?php if(settings()->payment->is_enabled): ?>

            <?php foreach($plans as $plan): ?>

            <?php $plan->settings = json_decode($plan->settings) ?>

            <div
                    class="col-12 col-md-6 col-lg-4 p-3"
                    data-plan-monthly="<?= json_encode((bool) $plan->monthly_price) ?>"
                    data-plan-annual="<?= json_encode((bool) $plan->annual_price) ?>"
                    data-plan-lifetime="<?= json_encode((bool) $plan->lifetime_price) ?>"
            >
                <div class="pricing-plan rounded" style="<?= $plan->color ? 'border-color: ' . $plan->color : null ?>">
                    <div class="pricing-header">
                        <span class="pricing-name"><?= $plan->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount d-none" data-plan-payment-frequency="monthly"><?= $plan->monthly_price ?></span>
                            <span class="pricing-price-amount d-none" data-plan-payment-frequency="annual"><?= $plan->annual_price ?></span>
                            <span class="pricing-price-amount d-none" data-plan-payment-frequency="lifetime"><?= $plan->lifetime_price ?></span>
                            <span class="pricing-price-currency"><?= settings()->payment->currency ?></span>
                        </div>

                        <div class="pricing-details"><?= $plan->description ?></div>
                    </div>

                    <div class="pricing-body d-flex flex-column justify-content-between">
                        <?= include_view(THEME_PATH . 'views/partials/plans_plan_content.php', ['plan_settings' => $plan->settings]) ?>

                        <a href="<?= url('register?redirect=pay/' . $plan->plan_id) ?>" class="btn btn-lg btn-block btn-primary">
                            <?php if(\Altum\Middlewares\Authentication::check()): ?>
                                <?php if(!$this->user->plan_trial_done && $plan->trial_days): ?>
                                    <?= sprintf(language()->plan->button->trial, $plan->trial_days) ?>
                                <?php elseif($this->user->plan_id == $plan->plan_id): ?>
                                    <?= language()->plan->button->renew ?>
                                <?php else: ?>
                                    <?= language()->plan->button->choose ?>
                                <?php endif ?>
                            <?php else: ?>
                                <?php if($plan->trial_days): ?>
                                    <?= sprintf(language()->plan->button->trial, $plan->trial_days) ?>
                                <?php else: ?>
                                    <?= language()->plan->button->choose ?>
                                <?php endif ?>
                            <?php endif ?>
                        </a>
                    </div>
                </div>
            </div>

        <?php endforeach ?>

        <?php ob_start() ?>
            <script>
                'use strict';

                let payment_frequency_handler = (event = null) => {

                    let payment_frequency = null;

                    if(event) {
                        payment_frequency = $(event.currentTarget).data('payment-frequency');
                    } else {
                        payment_frequency = $('[name="payment_frequency"]:checked').closest('label').data('payment-frequency');
                    }

                    switch(payment_frequency) {
                        case 'monthly':
                            $(`[data-plan-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break;

                        case 'annual':
                            $(`[data-plan-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="lifetime"]`).removeClass('d-inline-block').addClass('d-none');

                            break

                        case 'lifetime':
                            $(`[data-plan-payment-frequency="monthly"]`).removeClass('d-inline-block').addClass('d-none');
                            $(`[data-plan-payment-frequency="annual"]`).removeClass('d-inline-block').addClass('d-none');

                            break
                    }

                    $(`[data-plan-payment-frequency="${payment_frequency}"]`).addClass('d-inline-block');

                    $(`[data-plan-${payment_frequency}="true"]`).removeClass('d-none').addClass('');
                    $(`[data-plan-${payment_frequency}="false"]`).addClass('d-none').removeClass('');

                };

                $('[data-payment-frequency]').on('click', payment_frequency_handler);

                payment_frequency_handler();
            </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

        <?php if(settings()->plan_custom->status == 1): ?>

            <div class="col-12 col-md-6 col-lg-4 p-3">
                <div class="pricing-plan rounded" style="<?= settings()->plan_custom->color ? 'border-color: ' . settings()->plan_custom->color : null ?>">
                    <div class="pricing-header">
                        <span class="pricing-name"><?= settings()->plan_custom->name ?></span>

                        <div class="pricing-price">
                            <span class="pricing-price-amount"><?= settings()->plan_custom->price ?></span>
                        </div>

                        <div class="pricing-details"><?= settings()->plan_custom->description ?></div>
                    </div>

                    <div class="pricing-body d-flex flex-column justify-content-between">
                        <?= include_view(THEME_PATH . 'views/partials/plans_plan_content.php', ['plan_settings' => settings()->plan_custom->settings]) ?>

                        <a href="<?= settings()->plan_custom->custom_button_url ?>" class="btn btn-lg btn-block btn-primary"><?= language()->plan->button->contact ?></a>
                    </div>
                </div>
            </div>

        <?php endif ?>

        <?php endif ?>

    </div>
</div>











