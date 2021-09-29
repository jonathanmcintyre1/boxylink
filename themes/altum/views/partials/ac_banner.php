<?php
/* For demo purposes only */
defined('ALTUMCODE') || die();
?>

<?php if(isset($data->demo_url)): ?>
<script> if(window.location !== window.parent.location){ window.top.location.href = <?= json_encode($data->demo_url) ?>; } </script>
<?php endif ?>

<style>
    .ac-wrapper {
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        min-height: 4rem;
        background: #161A38;
        padding: .5rem 1rem;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 992px) {
        .ac-wrapper {
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
            min-height: 3rem;
            padding: .5rem 2rem;
        }
    }
    .ac-altumcode-link {
        color: white;
        display: flex;
        align-items: center;
        font-size: .85rem;
        margin-bottom: .5rem;
    }
    @media (min-width: 992px) {
        .ac-altumcode-link {
            margin-bottom: 0;
        }
    }
    .ac-altumcode-link:hover {
        text-decoration: none;
        color: white;
    }
    .ac-altumcode-image {
        width: 1rem;
        height:auto;
        margin-right: 1rem;
    }
    .ac-primary {
        padding: .25rem 1.25rem;
        background: #3f88fd;
        color: white;
        border-radius: .25rem;
        font-size: .95rem;
        transition: all .3s linear !important;
        white-space: nowrap;
    }
    .ac-primary:hover {
        text-decoration: none;
        background: #3370d2;
        color: white;
    }

    .ac-secondary {
        padding: .25rem 0;
        color: hsl(255, 85%, 90%);
        border-radius: .25rem;
        font-size: .95rem;
        transition: all .3s linear !important;
        white-space: nowrap;
        margin-right: 1.25rem;
    }
    .ac-secondary:hover {
        text-decoration: none;
        color: hsl(255, 85%, 70%);
    }
</style>
<div class="ac-wrapper">
    <a href="https://altumcode.com/" target="_blank" class="ac-altumcode-link">
        <img src="https://altumcode.com/themes/altum/assets/images/altumcode.svg" alt="AltumCode logo" class="ac-altumcode-image" />
        <span><?= $data->title_text ?></span>
    </a>

    <div>
        <a href="https://altumcode.com/contact" target="_blank" class="ac-secondary">Any questions? ✉️</a>
        <a href="<?= $data->product_url ?>" class="ac-primary"><?= $data->buy_text ?></a>
    </div>
</div>
