<?php

namespace ApiAlarmManager\Api;

class Places
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'addPlacesEndpoint'));
    }

    public function addPlacesEndpoint()
    {
        register_rest_route('wp/v2', 'places', array(
            'methods' => 'GET',
            'callback' => array($this, 'getAllPlaces')
        ));
    }

    public function getAllPlaces()
    {
        global $wpdb;
        $dbPlaces = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'place' GROUP BY meta_value ORDER BY meta_value ASC");

        $places = array();
        foreach ($dbPlaces as $place) {
            $places[] = $place->meta_value;
        }

        return $places;
    }
}
