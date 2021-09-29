<?php
define('ROOT', realpath(__DIR__ . '/..') . '/');
require_once ROOT . 'app/includes/product.php';

if(file_exists(ROOT . 'install/installed')) {
    die();
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
    <link rel="mask-icon" href="./assets/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="./assets/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/assets/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <title><?= PRODUCT_NAME ?> Installation</title>
</head>
<body>

    <header class="header">
        <div class="container">
            <div class="d-flex">
                <div class="mr-3">
                    <img src="./assets/images/logo.png" class="img-fluid logo" alt="AltumCode logo" />
                </div>

                <div class="d-flex flex-column justify-content-center">
                    <h1>Installation</h1>
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
                            <a href="#requirements" class="navigator nav-link" style="display: none">Requirements</a>
                        </li>

                        <li class="nav-item">
                            <a href="#setup" class="navigator nav-link" style="display: none">Setup</a>
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

                    <p>By continuing with the installation process, you are agreeing to the privacy policy and terms of service of <?= PRODUCT_NAME ?>, which are mentioned in their respective pages on <a href="<?= PRODUCT_URL ?>" target="_blank"><?= PRODUCT_URL ?></a>.</p>

                    <a href="#requirements" id="welcome_start" class="navigator btn btn-block btn-primary">Start the installation</a>
                </section>

                <section id="requirements" style="display: none">
                    <?php $requirements = true ?>
                    <h2>Requirements</h2>

                    <table class="table mt-3">
                        <thead>
                        <th class="bg-gray-200"></th>
                        <th class="bg-gray-200">Required</th>
                        <th class="bg-gray-200">Current</th>
                        <th class="bg-gray-200"></th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>PHP Version</td>
                            <td>7.4+</td>
                            <td><?= PHP_VERSION ?></td>
                            <td>
                                <?php if(version_compare(PHP_VERSION, '7.4.0') >= 0): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>cURL</td>
                            <td>Enabled</td>
                            <td><?= function_exists('curl_version') ? 'Enabled' : 'Not Enabled' ?></td>
                            <td>
                                <?php if(function_exists('curl_version')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>OpenSSL</td>
                            <td>Enabled</td>
                            <td><?= extension_loaded('openssl') ? 'Enabled' : 'Not Enabled' ?></td>
                            <td>
                                <?php if(extension_loaded('openssl')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>mbstring</td>
                            <td>Enabled</td>
                            <td><?= extension_loaded('mbstring') && function_exists('mb_get_info') ? 'Enabled' : 'Not Enabled' ?></td>
                            <td>
                                <?php if(extension_loaded('mbstring') && function_exists('mb_get_info')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>MySQLi</td>
                            <td>Enabled</td>
                            <td><?= function_exists('mysqli_connect') ? 'Enabled' : 'Not Enabled' ?></td>
                            <td>
                                <?php if(function_exists('mysqli_connect')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table mt-5">
                        <thead>
                        <th class="bg-gray-200">Path / File</th>
                        <th class="bg-gray-200">Status</th>
                        <th class="bg-gray-200"></th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>/uploads/favicon/</td>
                            <td><?= is_writable(ROOT . 'uploads/favicon/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/favicon/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/logo/</td>
                            <td><?= is_writable(ROOT . 'uploads/logo/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/logo/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/opengraph/</td>
                            <td><?= is_writable(ROOT . 'uploads/opengraph/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/opengraph/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/cache/</td>
                            <td><?= is_writable(ROOT . 'uploads/cache/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/cache/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/avatars/</td>
                            <td><?= is_writable(ROOT . 'uploads/avatars/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/avatars/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/backgrounds/</td>
                            <td><?= is_writable(ROOT . 'uploads/backgrounds/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/backgrounds/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/offline_payment_proofs/</td>
                            <td><?= is_writable(ROOT . 'uploads/offline_payment_proofs/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/offline_payment_proofs/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/block_thumbnail_images/</td>
                            <td><?= is_writable(ROOT . 'uploads/block_thumbnail_images/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/block_thumbnail_images/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/block_images/</td>
                            <td><?= is_writable(ROOT . 'uploads/block_images/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/block_images/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/files/</td>
                            <td><?= is_writable(ROOT . 'uploads/files/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/files/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/uploads/favicons/</td>
                            <td><?= is_writable(ROOT . 'uploads/favicons/') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'uploads/favicons/')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td>/config.php</td>
                            <td><?= is_writable(ROOT . 'config.php') ? 'Writable' : 'Not Writable' ?></td>
                            <td>
                                <?php if(is_writable(ROOT . 'config.php')): ?>
                                    <img src="assets/svg/check-circle-solid.svg" class="img-fluid img-icon text-success" />
                                <?php else: ?>
                                    <img src="assets/svg/times-circle-solid.svg" class="img-fluid img-icon text-danger" />
                                    <?php $requirements = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <?php if($requirements): ?>
                            <a href="#setup" class="navigator btn btn-block btn-primary">Next</a>
                        <?php else: ?>
                            <div class="alert alert-danger" role="alert">
                                Please make sure all the requirements listed on the documentation and on this page are met before continuing!
                            </div>
                            <p class="text-danger"></p>
                        <?php endif ?>
                    </div>
                </section>

                <section id="setup" style="display: none">
                    <?php
                    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $installation_url = preg_replace('/install\/$/', '', $actual_link);
                    ?>
                    <h2>Setup</h2>

                    <form id="setup_form" method="post" action="" role="form">
                        <div class="form-group">
                            <label for="license_key">License key</label>
                            <input type="text" class="form-control" id="license_key" name="license_key" required="required">
                            <small class="form-text text-muted">The unique license key that you got after purchasing.</small>
                        </div>

                        <div class="form-group">
                            <label for="installation_url">Website URL</label>
                            <input type="text" class="form-control" id="installation_url" name="installation_url" value="<?= $installation_url ?>" placeholder="https://example.com/" required="required">
                            <small class="form-text text-muted">Make sure to specify the full url of the installation path of the product.<br /> Subdomain example: <code>https://subdomain.domain.com/</code> <br />Subfolder example: <code>https://domain.com/product/</code></small>
                        </div>

                        <h3 class="mt-5">Database Details</h3>
                        <p>Fill in the database details that you will use for the installation of this product.</p>

                        <div class="form-group">
                            <label for="database_host">Host</label>
                            <input type="text" class="form-control" id="database_host" name="database_host" value="localhost" required="required">
                        </div>

                        <div class="form-group">
                            <label for="database_name">Name</label>
                            <input type="text" class="form-control" id="database_name" name="database_name" required="required">
                        </div>

                        <div class="form-group">
                            <label for="database_username">Username</label>
                            <input type="text" class="form-control" id="database_username" name="database_username" required="required">
                        </div>

                        <div class="form-group">
                            <label for="database_password">Password</label>
                            <input type="password" class="form-control" id="database_password" name="database_password">
                        </div>


                        <h3 class="mt-5">Keep in touch</h3>
                        <p>Subscribe to the newsletter and you will receive email updates of <strong>new products</strong>, <strong>discounts</strong> and <strong>product updates</strong>.</p>

                        <div class="form-group row">
                            <label for="newsletter_email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="newsletter_email" name="newsletter_email" placeholder="Your valid email address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="newsletter_name" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="newsletter_name" name="newsletter_name" placeholder="Your full name">
                            </div>
                        </div>

                        <p class="text-muted"><small>By subscribing, you agree with Revue’s <a target="_blank" href="https://www.getrevue.co/terms">Terms of Service</a> and <a target="_blank" href="https://www.getrevue.co/privacy">Privacy Policy</a>. Leave the fields empty if you do not wish to subscribe to the newsletter.</small></p>

                        <button type="submit" name="submit" class="btn btn-block btn-primary mt-5">Finish installation</button>
                    </form>
                </section>

                <section id="finish" style="display: none">
                    <h2>Installation Completed</h2>
                    <p class="text-success">Congratulations! The installation has been successful!</p>

                    <p>You can now login with the following information:</p>

                    <table class="table">
                        <tbody>
                        <tr>
                            <th>URL</th>
                            <td><a href="" id="final_url"></a></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>admin</td>
                        </tr>
                        <tr>
                            <th>Password</th>
                            <td>admin</td>
                        </tr>
                        </tbody>
                    </table>
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
