<?php

namespace ApiAlarmManager\Api;

/**
 * Filtering WordPress API
 */

class Filter
{
    private $removeFields;

    public function __construct()
    {
        //Actions
        add_action('init', array($this, 'redirectToApi'));

        //Filters
        add_filter('rest_url_prefix', array($this, 'apiBasePrefix'), 5000, 1);
        add_filter('rest_prepare_alarm', array($this, 'removeResponseKeys'), 5000, 3);
        add_filter('rest_prepare_station', array($this, 'removeResponseKeys'), 5000, 3);
    }

    /**
     * Rename /wp-json/ to /json/.
     * @return string Returning "json".
     */
    public function apiBasePrefix($prefix)
    {
        return "json";
    }

    /**
     * Force the usage of wordpress api
     * @return void
     */
    public function redirectToApi()
    {
        if (php_sapi_name() === 'cli') {
            return;
        }

        if (!is_admin() && strpos($this->currentUrl(), rtrim(rest_url(), "/")) === false && $this->currentUrl() == rtrim(home_url(), "/")) {
            wp_redirect(rest_url());
            exit;
        }
    }

    public function removeResponseKeys($response, $post, $request)
    {
        //Common keys
        $keys = array('author', 'acf', 'guid', 'link', 'template', 'meta', 'taxonomy', 'menu_order');

        if ($post->post_type !== 'alarm') {
            $keys[] = 'type';
        }

        //Do filtering
        $response->data = array_filter($response->data, function ($k) use ($keys) {
            return !in_array($k, $keys, true);
        }, ARRAY_FILTER_USE_KEY);

        //Return santizied response
        return $response;
    }

    public function currentUrl()
    {
        $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
        $currentURL .= $_SERVER["SERVER_NAME"];

        if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
            $currentURL .= ":".$_SERVER["SERVER_PORT"];
        }

        $currentURL .= $_SERVER["REQUEST_URI"];

        return rtrim($currentURL, "/");
    }
}
