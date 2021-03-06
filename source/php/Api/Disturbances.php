<?php

namespace ApiAlarmManager\Api;

class Disturbances
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerEndpoint'));
        add_action('rest_api_init', array($this, 'setCacheHeader'), 15);
    }

    public function registerEndpoint()
    {
        register_rest_route('wp/v2', '/disturbances', array(
            'methods' => 'GET',
            'callback' => array($this, 'getDisturbances'),
        ));
    }

    public function setCacheHeader() {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        add_filter( 'rest_pre_serve_request', function( $value ) {
            header('Cache-Control: max-age=300');

            return $value;
        });
    }

    public function getDisturbances($data)
    {
        $args = array(
            'post_status' => 'publish',
            'posts_per_page' => -1
        );

        if (isset($_GET['place']) && !empty($_GET['place'])) {
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'place',
                    'terms' => explode(',', $_GET['place']),
                )
            );
        }

        $big = new \WP_Query(array_merge(
            $args,
            array(
                'post_type' => 'big-disturbance',
            )
        ));

        $small = new \WP_Query(array_merge(
            $args,
            array(
                'post_type' => 'small-disturbance',
            )
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
