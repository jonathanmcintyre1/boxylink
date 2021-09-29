<?php

define('ROOT', realpath(__DIR__ . '/..') . '/');
require_once ROOT . 'vendor/autoload.php';
require_once ROOT . 'app/includes/product.php';
require_once ROOT . 'config.php';
require_once ROOT . 'update/info.php';

$database = new \mysqli(
    DATABASE_SERVER,
    DATABASE_USERNAME,
    DATABASE_PASSWORD,
    DATABASE_NAME
);

if($database->connect_error) {
    die('The database connection has failed!');
}

$product_info = $database->query("SELECT `value` FROM `settings` WHERE `key` = 'product_info'")->fetch_object() ?? null;

if($product_info) {
    $product_info = json_decode($product_info->value);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="./assets/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/favicons/favicon-16x16.png">
    <link rel="manifest" href="./assets/favicons/site.webmanifest">

    <title><?= PRODUCT_NAME ?> Update</title>
</head>
<body>

<header class="header">
    <div class="container">
        <div class="d-flex">
            <div class="mr-3">
                <img src="./assets/images/logo.png" class="img-fluid logo" alt="AltumCode logo" />
            </div>

            <div class="d-flex flex-column justify-content-center">
                <h1>Update</h1>
                <p class="subheader d-flex flex-row">
                    <span class="text-muted">
                        <a href="<?= PRODUCT_URL ?>" target="_blank" class="text-gray-500"><?= PRODUCT_NAME ?></a> by <a href="https://altumco.de/site" target="_blank" class="text-gray-500">AltumCode</a>
                    </span>
                </p>
            </div>
        </div>
    </div>
</header>

<main class="main">
    <div class="container">
        <div class="row">

            <div class="col col-md-3 d-none d-md-block">

                <nav class="nav sidebar-nav">
                    <ul class="sidebar" id="sidebar-ul">
                        <li class="nav-item">
                            <a href="#welcome" class="navigator nav-link">Welcome</a>
                        </li>

                        <li class="nav-item">
                            <a href="#update" class="navigator nav-link" style="display: none">Update</a>
                        </li>

                        <li class="nav-item">
                            <a href="#finish" class="navigator nav-link" style="display: none">Finish</a>
                        </li>
                    </ul>
                </nav>

            </div>

            <div class="col" id="content">

                <section id="welcome" style="display: none">
                    <h2>Welcome</h2>
                    <p>Thank you for choosing the <a href="https://altumco.de/site" target="_blank">AltumCode</a> brand.</p>

                    <p>By continuing with the updating process, you are agreeing to the privacy policy and terms of service of <?= PRODUCT_NAME ?>, which are mentioned in their respective pages on <a href="<?= PRODUCT_URL ?>" target="_blank"><?= PRODUCT_URL ?></a>.</p>

                    <a href="#update" id="welcome_start" class="navigator btn btn-block btn-primary">Next</a>
                </section>

                <section id="update" style="display: none">
                    <h2>Update</h2>

                    <form id="setup_form" method="post" action="" role="form">
                        <div class="form-group">
                            <label for="product_version">Current version</label>
                            <input type="text" class="form-control" id="product_version" name="product_version" value="<?= $product_info ? $product_info->version : (defined('PRODUCT_VERSION') ? PRODUCT_VERSION : '8.0.0') ?>" aria-describedby="license_help" readonly="readonly">
                        </div>


                        <div class="form-group">
                            <label for="new_product_version">Final version</label>
                            <input type="text" class="form-control" id="new_product_version" name="new_product_version" value="<?= NEW_PRODUCT_VERSION ?>" aria-describedby="license_help" readonly="readonly">
                        </div>

                        <?php if(($product_info ? $product_info->version : PRODUCT_VERSION) == NEW_PRODUCT_VERSION): ?>
                            <p class="text-success">Your database is already on the latest version.</p>
                        <?php else: ?>
                            <button type="submit" name="submit" class="btn btn-block btn-primary">Finish update</button>
                        <?php endif ?>
                    </form>
                </section>

                <section id="finish" style="display: none">
                    <h2>Update Completed</h2>
                    <p class="text-success">Congratulations! The database update has been successful!</p>

                    <p>Make sure to delete the /update folder now and continue to follow the steps from the documentation.</p>
                </section>
            </div>

        </div>
    </div>
</main>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
