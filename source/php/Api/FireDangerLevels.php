<?php

namespace ApiAlarmManager\Api;

use ApiAlarmManager\Admin\FireDangerLevels as AdminFireDangerLevels;

class FireDangerLevels
{
    private $namespace = 'wp/v2';
    private $route     = '/fire-danger-levels';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoint'));
    }

    public function registerEndpoint()
    {
        register_rest_route($this->namespace, $this->route, array(
            'methods'  => 'GET',
            'callback' => array($this, 'getFireDangerLevels'),
        ));
    }

    public function getFireDangerLevels(): array
    {
        $data            = get_field('fire_danger_levels', 'option');
        $dateTimeChanged = get_option(AdminFireDangerLevels::$dateTimeChangedOptionName, null);

        $places =  is_array($data) ? array_map([$this, 'convertPlaceIdToName'], $data) : [];

        $sanitize = function ($name) {
            // phpcs:ignore WordPress.Security.NonceVerification
            if (isset($_GET[$name]) && !empty($_GET[$name])) {
                // phpcs:ignore WordPress.Security.NonceVerification
                return sanitize_text_field(wp_unslash($_GET[$name]));
            }
            return '';
        };
        $input    = $sanitize('place');

        if (!empty($input)) {
            $filter = explode(',', $input);

            $places = array_filter($places, function ($item) use ($filter) {
                return in_array($item['place'], $filter);
            });
        }

        if (count($places) === 0) {
            trigger_error('No fire danger levels found', E_USER_WARNING);
            return [];
        }

        return [
            'dateTimeChanged' => $dateTimeChanged,
            'places'          => $places
        ];
    }

    public function convertPlaceIdToName($fireDangerLevel)
    {
        $fireDangerLevel['place'] = get_term($fireDangerLevel['place'])->name;
        return $fireDangerLevel;
    }
}
