<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex mb-5 mb-lg-0">
    <img src="<?= get_gravatar($this->user->email) ?>" class="d-none d-md-block mr-3 user-avatar" />

    <div class="d-flex flex-column">
        <span class="h2"><?= $this->user->name ?></span>

        <div>
            <a href="<?= url('account-plan') ?>" class="badge badge-success"><?= sprintf($this->language->account->plan->header, $this->user->plan->name) ?></a>

            <?php if($this->user->plan_id != 'free' && (new \DateTime($this->user->plan_expiration_date)) < (new \DateTime())->modify('+5 years')): ?>
                <small><?= sprintf($this->language->account->plan->subheader, '<strong>' . \Altum\Date::get($this->user->plan_expiration_date, 2) . '</strong>') ?></small>
            <?php endif ?>
        </div>
    </div>
</div>

<ul class="mt-5 nav nav-custom flex-column flex-lg-row">
    <li class="nav-item my-1 my-lg-0 mr-lg-2">
        <a href="<?= url('account') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-wrench mr-1"></i> <?= $this->language->account->menu ?>
        </a>
    </li>

    <li class="nav-item my-1 my-lg-0 mr-lg-2">
        <a href="<?= url('account-plan') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-plan' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-box-open mr-1"></i> <?= $this->language->account_plan->menu ?>
        </a>
    </li>

    <?php if($this->settings->payment->is_enabled): ?>
    <li class="nav-item my-1 my-lg-0 mr-lg-2">
        <a href="<?= url('account-payments') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-payments' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= $this->language->account_payments->menu ?>
        </a>
    </li>
    <?php endif ?>

    <li class="nav-item my-1 my-lg-0 mr-lg-2">
        <a href="<?= url('account-logs') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-logs' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-scroll mr-1"></i> <?= $this->language->account_logs->menu ?>
        </a>
    </li>

    <li class="nav-item my-1 my-lg-0 mr-lg-2">
        <a href="<?= url('account-delete') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-delete' ? 'active' : null ?>">
            <i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= $this->language->account_delete->menu ?>
        </a>
    </li>
</ul>
