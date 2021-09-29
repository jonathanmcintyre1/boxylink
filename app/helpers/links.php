<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

function url($append = '') {
    return SITE_URL . (\Altum\Language::$default_language != \Altum\Language::$language ? \Altum\Language::$language_code . '/' : null)  . $append;
}

function redirect($append = '') {
    header('Location: ' . SITE_URL . $append);

    die();
}

function get_slug($string, $delimiter = '-', $lowercase = true) {

    /* Replace all non words characters with the specified $delimiter */
    $string = mb_ereg_replace('/[^a-zA-Z0-9._-]+/', $delimiter, $string);

    /* Check for double $delimiters and remove them so it only will be 1 delimiter */
    $string = mb_ereg_replace('/' . $delimiter . '+/', $delimiter, $string);

    /* Remove the $delimiter character from the start and the end of the string */
    $string = trim($string, $delimiter);

    /* Make sure to lowercase it */
    $string = $lowercase ? mb_strtolower($string) : $string;

    return $string;
}

function google_safe_browsing_check($url, $api_key = '') {
    $api_url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . $api_key;

    $body = Unirest\Request\Body::json([
        'client' => [
            'clientId' => '',
            'clientVersion' => '1.5.2'
        ],
        'threatInfo' => [
            'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING','THREAT_TYPE_UNSPECIFIED'],
            'platformTypes' => ['ANY_PLATFORM'],
            'threatEntryTypes' => ['URL'],
            'threatEntries' => [
                ['url' => $url]
            ]
        ]

    ]);

    $headers = [
        'Content-Type' => 'application/json',
        'Authorization' => 'Token :)'
    ];

    $response = Unirest\Request::post($api_url, $headers, $body);

    if(isset($response->body->matches[0]->threatType) && $response->body->matches[0]->threatType) return true;

    return false;
}
