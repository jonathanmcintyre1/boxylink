<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

function settings() {
    return \Altum\Settings::$settings;
}

function db() {
    return \Altum\Database\Database::$db;
}

function database() {
    return \Altum\Database\Database::$database;
}

function language($language = null) {
    return \Altum\Language::get($language);
}
