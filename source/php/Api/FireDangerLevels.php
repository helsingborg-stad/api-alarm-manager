<?php

namespace ApiAlarmManager\Api;

class FireDangerLevels
{
    private $namespace = 'wp/v2';
    private $route = '/fire-danger-levels';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoint'));
    }

    public function registerEndpoint()
    {
        register_rest_route($this->namespace, $this->route, array(
            'methods' => 'GET',
            'callback' => array($this, 'getFireDangerLevels'),
        ));
    }

    public function getFireDangerLevels(): array
    {
        $data = get_field('fire_danger_levels', 'option');

        if ($data === false) {
            return [];
        }

        $fireDangerLevels = array_map([$this, 'convertPlaceIdToName'], $data);

        return $fireDangerLevels;
    }

    public function convertPlaceIdToName($fireDangerLevel)
    {
        // Get term name from term id
        $fireDangerLevel['place'] = get_term($fireDangerLevel['place'])->name;
        return $fireDangerLevel;
    }
}
