<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Meta;
use Altum\Middlewares\Csrf;
use Altum\Models\User;
use Altum\Title;
use MaxMind\Db\Reader;

class Link extends Controller {
    public $link;
    public $user;
    public $is_qr;

    public function index() {

        $link_url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;
        $this->is_qr = isset($this->params[1]) && $this->params[1] == 'qr' ? Database::clean_string($this->params[1]) : false;
        $link_id = isset($_GET['link_id']) ? (int) $_GET['link_id'] : false;

        /* Check if the current link accessed is actually the original url or not ( multi domain use ) */
        $original_url_host = parse_url(url())['host'];
        $request_url_host = Database::clean_string($_SERVER['HTTP_HOST']);

        if($original_url_host == $request_url_host) {

            /* If we have the link id, get it via the link id */
            /* This is used for the preview iframe */
            if($link_id) {
                $this->link = Database::get('*', 'links', ['link_id' => $link_id]);
            } else {
                $this->link = Database::get('*', 'links', ['url' => $link_url, 'is_enabled' => 1, 'domain_id' => 0]);
            }

        } else {
            $this->link = $this->database->query("
                SELECT `links`.*, `domains`.`host`, `domains`.`scheme`
                FROM `links`
                LEFT JOIN `domains` ON `links`.`domain_id` = `domains`.`domain_id`
                WHERE
                    `links`.`url` = '{$link_url}' AND 
                    `links`.`is_enabled` = 1 AND 
                    `domains`.`host` = '{$request_url_host}' AND 
                    (`links`.`user_id` = `domains`.`user_id` OR `domains`.`type` = 1)
            ")->fetch_object() ?? null;
        }

        if(!$this->link) {
            redirect();
        }

        $this->user = (new User())->get_user_by_user_id($this->link->user_id);

        /* Make sure to check if the user is active */
        if($this->user->active != 1) {
            redirect();
        }

        /* Check if its a scheduled link and we should show it or not */
        if(
            $this->user->plan_settings->scheduling &&

            !empty($this->link->start_date) &&
            !empty($this->link->end_date) &&
            (
                \Altum\Date::get('', null) < \Altum\Date::get($this->link->start_date, null, \Altum\Date::$default_timezone) ||
                \Altum\Date::get('', null) > \Altum\Date::get($this->link->end_date, null, \Altum\Date::$default_timezone)
            )
        ) {
            redirect();
        }

        /* Parse the settings */
        $this->link->settings = json_decode($this->link->settings);

        /* Determine the actual full url */
        $this->link->full_url = $this->link->domain_id && !isset($_GET['link_id']) ? $this->link->scheme . $this->link->host . '/' . $this->link->url : url($this->link->url);

        /* Check for vcard download link */
        if($this->link->subtype == 'vcard') {
            $vcard = new \JeroenDesloovere\VCard\VCard();

            $vcard->addName($this->link->settings->last_name, $this->link->settings->first_name);
            $vcard->addAddress(null, null, $this->link->settings->street, $this->link->settings->city, $this->link->settings->region, $this->link->settings->zip, $this->link->settings->country);
            $vcard->addPhoneNumber($this->link->settings->phone);
            $vcard->addEmail($this->link->settings->email);
            $vcard->addURL($this->link->settings->website);
            $vcard->addNote($this->link->settings->note);

            $vcard->download();

            die();
        }

        /* If is QR code only return a QR code */
        if($this->is_qr) {

            $qr = new \Endroid\QrCode\QrCode($this->link->full_url);

            header('Content-Type: ' . $qr->getContentType());

            echo $qr->writeString();

            die();
        }

        /* Check if the user has access to the link */
        $has_access = !$this->link->settings->password || ($this->link->settings->password && isset($_COOKIE['link_password_' . $this->link->link_id]) && $_COOKIE['link_password_' . $this->link->link_id] == $this->link->settings->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->password) {
            $has_access = true;
        }

        /* Check if the password form is submitted */
        if(!$has_access && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'password') {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(!password_verify($_POST['password'], $this->link->settings->password)) {
                $_SESSION['error'][] = $this->language->link->password->error_message;
            }

            if(empty($_SESSION['error'])) {

                /* Set a cookie */
                setcookie('link_password_' . $this->link->link_id, $this->link->settings->password, time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();

            }

        }

        /* Check if the user has access to the link */
        $can_see_content = !$this->link->settings->sensitive_content || ($this->link->settings->sensitive_content && isset($_COOKIE['link_sensitive_content_' . $this->link->link_id]));

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->sensitive_content) {
            $can_see_content = true;
        }

        /* Check if the password form is submitted */
        if(!$can_see_content && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'sensitive_content') {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {

                /* Set a cookie */
                setcookie('link_sensitive_content_' . $this->link->link_id, 'true', time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();

            }

        }

        /* Display the password form */
        if(!$has_access && !isset($_GET['preview'])) {

            /* Set a custom title */
            Title::set($this->language->link->password->title);

            /* Main View */
            $view = new \Altum\Views\View('link-path/partials/password', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else if(!$can_see_content && !isset($_GET['preview'])) {

            /* Set a custom title */
            Title::set($this->language->link->sensitive_content->title);

            /* Main View */
            $view = new \Altum\Views\View('link-path/partials/sensitive_content', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else {

            $this->create_statistics();

            /* Check what to do next */
            if($this->link->type == 'biolink' && $this->link->subtype == 'base') {

                /* Check for a leap link */
                if($this->link->settings->leap_link && $this->user->plan_settings->leap_link && !isset($_GET['preview'])) {
                    header('Location: ' . $this->link->settings->leap_link, true, 301);
                } else {
                    $this->process_biolink();
                }

            } else {

                $this->process_redirect();

            }

        }

    }

    private function create_statistics() {

        $cookie_name = 's_statistics_' . $this->link->link_id;

        if(isset($_COOKIE[$cookie_name]) && (int) $_COOKIE[$cookie_name] >= 3) {
            return;
        }

        if(isset($_GET['preview'])) {
            return;
        }

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        /* Detect extra details about the user */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
        $is_unique = isset($_COOKIE[$cookie_name]) ? 0 : 1;

        /* Detect the location */
        $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
        $country_code = $maxmind && $maxmind['country']['iso_code'] ? $maxmind['country']['iso_code'] : null;
        $city_name =  $maxmind && $maxmind['city']['names']['en'] ? $maxmind['city']['names']['en'] : null;

        /* Process referrer */
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

        if(!isset($referrer)) {
            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if the referrer comes from the same location */
        if(isset($referrer) && isset($referrer['host']) && $referrer['host'] == parse_url($this->link->full_url)['host']) {
            $is_unique = 0;

            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if referrer actually comes from the QR code */
        if(isset($_GET['referrer']) && $_GET['referrer'] == 'qr') {
            $referrer = [
                'host' => 'qr',
                'path' => null
            ];
        }

        $utm_source = $_GET['utm_source'] ?? null;
        $utm_medium = $_GET['utm_medium'] ?? null;
        $utm_campaign = $_GET['utm_campaign'] ?? null;

        /* Insert the log */
        $stmt = Database::$database->prepare("
            INSERT INTO 
                `track_links` (`user_id`, `project_id`, `link_id`, `country_code`, `city_name`, `os_name`, `browser_name`, `referrer_host`, `referrer_path`, `device_type`, `browser_language`, `utm_source`, `utm_medium`, `utm_campaign`, `is_unique`, `datetime`) 
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'ssssssssssssssss',
            $this->link->user_id,
            $this->link->project_id,
            $this->link->link_id,
            $country_code,
            $city_name,
            $os_name,
            $browser_name,
            $referrer['host'],
            $referrer['path'],
            $device_type,
            $browser_language,
            $utm_source,
            $utm_medium,
            $utm_campaign,
            $is_unique,
            Date::$date
        );
        $stmt->execute();
        $stmt->close();

        /* Add the unique hit to the link table as well */
        Database::$database->query("UPDATE `links` SET `clicks` = `clicks` + 1 WHERE `link_id` = {$this->link->link_id}");

        /* Set cookie to try and avoid multiple entrances */
        $cookie_new_value = isset($_COOKIE[$cookie_name]) ? (int) $_COOKIE[$cookie_name] + 1 : 0;
        setcookie($cookie_name, (int) $cookie_new_value, time()+60*60*24*1);
    }

    public function process_biolink() {

        /* Get all the links inside of the biolink */
        $cache_instance = \Altum\Cache::$adapter->getItem('biolink_links_' . $this->link->link_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            $result = Database::$database->query("SELECT * FROM `links` WHERE `biolink_id` = {$this->link->link_id} AND `type` = 'biolink' AND `subtype` <> 'base' AND `is_enabled` = 1 ORDER BY `order` ASC");
            $links = [];

            while($row = $result->fetch_object()) {
                $links[] = $row;
            }

            \Altum\Cache::$adapter->save($cache_instance->set($links)->expiresAfter(86400)->addTag('biolinks_links_user_' . $this->link->user_id));

        } else {

            /* Get cache */
            $links = $cache_instance->get();

        }

        /* Set the meta tags */
        if($this->user->plan_settings->seo) {
            Meta::set_description(string_truncate($this->link->settings->seo->meta_description, 200));
            Meta::set_social_url($this->link->full_url);
            Meta::set_social_title($this->link->settings->seo->title);
            Meta::set_social_description(string_truncate($this->link->settings->seo->meta_description, 200));
            Meta::set_social_image($this->link->settings->seo->image);
        }

        /* Prepare the View */
        $data = [
            'link' => $this->link,
            'links' => $links
        ];

        $view_content = \Altum\Link::get_biolink($this, $this->link, $this->user, $links);

        $this->add_view_content('content', $view_content);

        /* Set a custom title */
        Title::set($this->link->settings->title, true);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        header('Location: ' . $this->link->location_url, true, 301);

    }
}
