<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Models\Domain;
use Altum\Title;

class Link extends Controller {
    public $link;

    public function index() {

        Authentication::guard();

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;
        $method = isset($this->params[1]) && in_array($this->params[1], ['settings', 'statistics', 'qr']) ? $this->params[1] : 'settings';

        /* Make sure the link exists and is accessible to the user */
        if(!$this->link = db()->where('link_id', $link_id)->where('user_id', $this->user->user_id)->getOne('links')) {
            redirect('dashboard');
        }

        $biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        $this->link->settings = json_decode($this->link->settings);
        $this->link->pixels_ids = json_decode($this->link->pixels_ids ?? '[]');

        /* Get the current domain if needed */
        $this->link->domain = $this->link->domain_id ? (new Domain())->get_domain($this->link->domain_id) : null;

        /* Determine the actual full url */
        $this->link->full_url = $this->link->domain ? $this->link->domain->url . $this->link->url : SITE_URL . $this->link->url;

        /* Handle code for different parts of the page */
        switch($method) {
            case 'settings':

                if($this->link->type == 'biolink') {
                    /* Get the links available for the biolink */
                    $link_links_result = database()->query("SELECT * FROM `biolinks_blocks` WHERE `link_id` = {$this->link->link_id} ORDER BY `order` ASC");

                    /* Add the modals for creating the links inside the biolink */
                    foreach($biolink_blocks as $key => $value) {
                        $data = [
                            'link' => $this->link,
                            'biolink_blocks' => $biolink_blocks,
                        ];

                        $view = new \Altum\Views\View('link/settings/biolink_blocks/' . $key . '/' . $key . '_create_modal', (array) $this);

                        \Altum\Event::add_content($view->run($data), 'modals');
                    }

                    $view = new \Altum\Views\View('link/settings/biolink_link_create_modal', (array) $this);
                    \Altum\Event::add_content($view->run(['biolink_blocks' => $biolink_blocks,]), 'modals');
                }

                /* Get the available domains to use */
                $domains = (new Domain())->get_domains($this->user);

                /* Existing projects */
                $projects = (new \Altum\Models\Project())->get_projects($this->user->user_id);

                /* Existing pixels */
                $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);

                /* Prepare variables for the view */
                $data = [
                    'link'              => $this->link,
                    'method'            => $method,
                    'link_links_result' => $link_links_result ?? null,
                    'domains'           => $domains,
                    'projects'          => $projects,
                    'pixels'            => $pixels,
                    'biolink_blocks'    => $biolink_blocks,
                ];

                break;


            case 'statistics':

                if(!$this->user->plan_settings->statistics) {
                    $_SESSION['info'][] = language()->global->info_message->plan_feature_no_access;
                    redirect('links');
                }

                $type = isset($_GET['type']) && in_array($_GET['type'], ['overview', 'referrer_host', 'referrer_path', 'country', 'city_name', 'os', 'browser', 'device', 'language', 'utm_source', 'utm_medium', 'utm_campaign']) ? Database::clean_string($_GET['type']) : 'overview';

                $datetime = \Altum\Date::get_start_end_dates_new();

                /* Get the required statistics */
                $pageviews = [];
                $pageviews_chart = [];

                $pageviews_result = database()->query("
                    SELECT
                        COUNT(`id`) AS `pageviews`,
                        SUM(`is_unique`) AS `visitors`,
                        DATE_FORMAT(`datetime`, '{$datetime['query_date_format']}') AS `formatted_date`
                    FROM
                         `track_links`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `formatted_date`
                    ORDER BY
                        `formatted_date`
                ");

                /* Generate the raw chart data and save pageviews for later usage */
                while($row = $pageviews_result->fetch_object()) {
                    $pageviews[] = $row;

                    $row->formatted_date = $datetime['process']($row->formatted_date);

                    $pageviews_chart[$row->formatted_date] = [
                        'pageviews' => $row->pageviews,
                        'visitors' => $row->visitors
                    ];
                }

                $pageviews_chart = get_chart_data($pageviews_chart);

                /* Get data based on what statistics are needed */
                switch($type) {
                    case 'overview':

                        $result = database()->query("
                            SELECT
                                *
                            FROM
                                `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                            ORDER BY
                                `datetime` DESC
                        ");

                        break;

                    case 'referrer_host':
                    case 'country':
                    case 'os':
                    case 'browser':
                    case 'device':
                    case 'language':

                        $columns = [
                            'referrer_host' => 'referrer_host',
                            'referrer_path' => 'referrer_path',
                            'country' => 'country_code',
                            'city_name' => 'city_name',
                            'os' => 'os_name',
                            'browser' => 'browser_name',
                            'device' => 'device_type',
                            'language' => 'browser_language'
                        ];

                        $result = database()->query("
                            SELECT
                                `{$columns[$type]}`,
                                COUNT(*) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                            GROUP BY
                                `{$columns[$type]}`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;

                    case 'referrer_path':

                        $referrer_host = trim(Database::clean_string($_GET['referrer_host']));

                        $result = database()->query("
                            SELECT
                                `referrer_path`,
                                COUNT(*) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND `referrer_host` = '{$referrer_host}'
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                            GROUP BY
                                `referrer_path`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;

                    case 'city_name':

                        $country_code = trim(Database::clean_string($_GET['country_code']));

                        $result = database()->query("
                            SELECT
                                `city_name`,
                                COUNT(*) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND `country_code` = '{$country_code}'
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                            GROUP BY
                                `city_name`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;

                    case 'utm_source':

                        $result = database()->query("
                            SELECT
                                `utm_source`,
                                COUNT(*) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                                AND `utm_source` IS NOT NULL
                            GROUP BY
                                `utm_source`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;

                    case 'utm_medium':

                        $utm_source = trim(Database::clean_string($_GET['utm_source']));

                        $result = database()->query("
                            SELECT
                                `utm_medium`,
                                COUNT(*) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND `utm_source` = '{$utm_source}'
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                            GROUP BY
                                `utm_medium`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;

                    case 'utm_campaign':

                        $utm_source = trim(Database::clean_string($_GET['utm_source']));
                        $utm_medium = trim(Database::clean_string($_GET['utm_medium']));

                        $result = database()->query("
                            SELECT
                                `utm_campaign`,
                                COUNT(*) AS `total`
                            FROM
                                 `track_links`
                            WHERE
                                `link_id` = {$this->link->link_id}
                                AND `utm_source` = '{$utm_source}'
                                AND `utm_medium` = '{$utm_medium}'
                                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                            GROUP BY
                                `utm_campaign`
                            ORDER BY
                                `total` DESC
                            LIMIT 250
                        ");

                        break;
                }

                switch($type) {
                    case 'overview':

                        $statistics_keys = [
                            'country_code',
                            'referrer_host',
                            'device_type',
                            'os_name',
                            'browser_name',
                            'browser_language'
                        ];

                        $statistics = [];
                        foreach($statistics_keys as $key) {
                            $statistics[$key] = [];
                            $statistics[$key . '_total_sum'] = 0;
                        }

                        /* Start processing the rows from the database */
                        while($row = $result->fetch_object()) {
                            foreach($statistics_keys as $key) {

                                $statistics[$key][$row->{$key}] = isset($statistics[$key][$row->{$key}]) ? $statistics[$key][$row->{$key}] + 1 : 1;

                                $statistics[$key . '_total_sum']++;

                            }
                        }

                        foreach($statistics_keys as $key) {
                            arsort($statistics[$key]);
                        }

                        /* Prepare the statistics method View */
                        $data = [
                            'statistics' => $statistics,
                            'link' => $this->link,
                            'method' => $method,
                            'datetime' => $datetime,
                        ];

                        break;

                    case 'referrer_host':
                    case 'country':
                    case 'city_name':
                    case 'os':
                    case 'browser':
                    case 'device':
                    case 'language':
                    case 'referrer_path':
                    case 'utm_source':
                    case 'utm_medium':
                    case 'utm_campaign':

                        /* Store all the results from the database */
                        $statistics = [];
                        $statistics_total_sum = 0;

                        while($row = $result->fetch_object()) {
                            $statistics[] = $row;

                            $statistics_total_sum += $row->total;
                        }

                        /* Prepare the statistics method View */
                        $data = [
                            'rows' => $statistics,
                            'total_sum' => $statistics_total_sum,
                            'link' => $this->link,
                            'method' => $method,
                            'datetime' => $datetime,
                            'type' => $type,
                            'url' => 'link/' . $this->link->link_id,

                            'referrer_host' => $referrer_host ?? null,
                            'country_code' => $country_code ?? null,
                            'utm_source' => $utm_source ?? null,
                            'utm_medium' => $utm_medium ?? null,
                        ];

                        break;
                }

                /* Export handler */
                process_export_csv($statistics, 'basic');
                process_export_json($statistics, 'basic');

                $view = new \Altum\Views\View('link/statistics/statistics_' . $type, (array) $this);
                $this->add_view_content('statistics', $view->run($data));

                /* Prepare variables for the view */
                $data = [
                    'link' => $this->link,
                    'method' => $method,
                    'type' => $type,
                    'datetime' => $datetime,
                    'pageviews' => $pageviews,
                    'pageviews_chart' => $pageviews_chart
                ];

                break;

            case 'qr':

                /* Prepare variables for the view */
                $data = [
                    'link'              => $this->link,
                    'method'            => $method,
                ];

                break;
        }

        /* Delete Modal */
        $view = new \Altum\Views\View('links/link_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('biolink-block/biolink_block_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Prepare the method View */
        $view = new \Altum\Views\View('link/' . $method, (array) $this);
        $this->add_view_content('method', $view->run($data));

        /* Prepare the View */
        $data = [
            'link'      => $this->link,
            'method'    => $method
        ];

        $view = new \Altum\Views\View('link/index', (array) $this);
        $this->add_view_content('content', $view->run($data));

        /* Set a custom title */
        Title::set(sprintf(language()->link->title, $this->link->url));

    }

}
