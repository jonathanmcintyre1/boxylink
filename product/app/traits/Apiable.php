<?php

namespace Altum\Traits;

use Altum\Database\Database;
use Altum\Response;

trait Apiable {
    public $api_user = null;

    /* Function to check the request authentication */
    private function verify_request($require_to_be_admin = false) {

        /* Define the return content to be treated as JSON */
        header('Content-Type: application/json');

        /* Make sure to check for the Auth header */
        $api_key = (function() {
            $headers = getallheaders();

            if(!isset($headers['Authorization'])) {
                return null;
            }

            if(!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return null;
            }

            return $matches[1];
        })();

        if(!$api_key) {
            Response::jsonapi_error([[
                'title' => $this->language->api->error_message->no_bearer,
                'status' => '401'
            ]], null, 401);
        }

        /* Get the user data of the API key owner, if any */
        $this->api_user = Database::get('*', 'users', ['api_key' => $api_key, 'active' => 1]);

        if(!$this->api_user) {
            $this->response_error($this->language->api->error_message->no_access, 401);
        }

        if($require_to_be_admin && $this->api_user->type != 1) {
            $this->response_error($this->language->api->error_message->no_access, 401);
        }

        $this->api_user->plan_settings = json_decode($this->api_user->plan_settings);

        /* Rate limiting */
        $rate_limit_limit = 60;
        $rate_limit_per_seconds = 60;

        /* Verify the limitation of the bearer */
        $cache_instance = \Altum\Cache::$adapter->getItem('api-' . $api_key);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Initial save */
            $cache_instance->set($rate_limit_limit)->expiresAfter($rate_limit_per_seconds);

        }

        /* Decrement */
        $cache_instance->decrement();

        /* Get the actual value */
        $rate_limit_remaining = $cache_instance->get();

        /* Get the reset time */
        $rate_limit_reset = $cache_instance->getTtl();

        /* Save it */
        \Altum\Cache::$adapter->save($cache_instance);

        /* Set the rate limit headers */
        header('X-RateLimit-Limit: ' . $rate_limit_limit);

        if($rate_limit_remaining >= 0) {
            header('X-RateLimit-Remaining: ' . $rate_limit_remaining);
        }

        if($rate_limit_remaining < 0) {
            header('X-RateLimit-Reset: ' . $rate_limit_reset);
            $this->response_error($this->language->api->error_message->rate_limit, 429);
        }

    }

    private function return_404() {
        $this->response_error($this->language->api->error_message->not_found, 404);
    }

    private function response_error($title = '', $response_code = 400) {
        Response::jsonapi_error([[
            'title' => $title,
            'status' => $response_code
        ]], null, $response_code);
    }

}
