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
        add_filter('rest_post_dispatch', array($this, 'removeDuplicateStations'), 5000, 3);
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
    public static function redirectToApi()
    {
        if (php_sapi_name() === 'cli') {
            return;
        }

        if (!is_admin() && strpos(self::currentUrl(), rtrim(rest_url(), "/")) === false && self::currentUrl() == rtrim(home_url(), "/")) {
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

    /**
     * Remove repeated stations from collection responses without changing
     * the imported station posts themselves.
     *
     * @param \WP_REST_Response $response
     * @param \WP_REST_Server $server
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function removeDuplicateStations($response, $server, $request)
    {
        if (
            $request->get_method() !== 'GET' ||
            $request->get_route() !== '/wp/v2/station' ||
            !is_array($response->get_data())
        ) {
            return $response;
        }

        $response->set_data(self::uniqueStations($response->get_data()));

        return $response;
    }

    /**
     * Keep the first response item for each source station ID. The REST
     * controller orders items newest first, so this retains the current copy.
     *
     * A title is used only as a fallback for older station posts without an ID.
     *
     * @param array $stations
     * @return array
     */
    public static function uniqueStations(array $stations): array
    {
        $uniqueStations = array();
        $seen = array();

        foreach ($stations as $station) {
            if (!is_array($station)) {
                $uniqueStations[] = $station;
                continue;
            }

            $identity = self::stationIdentity($station);

            if ($identity === '' || isset($seen[$identity])) {
                if ($identity === '') {
                    $uniqueStations[] = $station;
                }
                continue;
            }

            $seen[$identity] = true;
            $uniqueStations[] = $station;
        }

        return $uniqueStations;
    }

    /**
     * @param array $station
     * @return string
     */
    private static function stationIdentity(array $station): string
    {
        $identity = $station['station_id'] ?? null;

        if ($identity === null || $identity === '') {
            $identity = self::stationTitle($station);
        }

        if (!is_string($identity) && !is_numeric($identity)) {
            return '';
        }

        $identity = trim(preg_replace('/\s+/', ' ', (string) $identity));

        return function_exists('mb_strtolower') ? mb_strtolower($identity) : strtolower($identity);
    }

    /**
     * @param array $station
     * @return string|null
     */
    private static function stationTitle(array $station): ?string
    {
        if (is_string($station['title'] ?? null)) {
            return $station['title'];
        }

        if (is_array($station['title'] ?? null)) {
            return $station['title']['plain_text'] ?? $station['title']['rendered'] ?? null;
        }

        return null;
    }

    public static function currentUrl()
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
