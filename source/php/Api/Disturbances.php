<?php

namespace ApiAlarmManager\Api;

class Disturbances
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoint'));
    }

    public function registerEndpoint()
    {
        register_rest_route('wp/v2', '/disturbances', array(
            'methods' => 'GET',
            'callback' => array($this, 'getDisturbances'),
        ));
    }

    public function getDisturbances($data)
    {
        $big = new \WP_Query(array(
            'post_type' => 'big-disturbance',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ));

        $small = new \WP_Query(array(
            'post_type' => 'small-disturbance',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ));

        foreach (array_merge($big->posts, $small->posts) as &$item) {
            $item->alarm = $this->getAlarmConnection($item);
            $item->place = wp_get_post_terms($item->ID, 'place');
        }

        return array(
            'big' => $big->posts,
            'small' => $small->posts
        );
    }

    /**
     * Get alarm connection for disturbance
     * @param  WP_Post $disturbance
     * @return array
     */
    public function getAlarmConnection($disturbance)
    {
        $alarmIds = get_field('alarm_connection', $disturbance->ID);
        $alarms = array();

        if (!is_array($alarmIds)) {
            return $alarms;
        }

        foreach ($alarmIds as $alarmId) {
            $alarms[$alarmId] = array(
                'title' => get_the_title($alarmId),
                'href' => rest_url('/wp/v2/alarm/' . $alarmId)
            );
        }

        return $alarms;
    }
}
