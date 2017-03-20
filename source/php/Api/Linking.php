<?php

namespace ApiAlarmManager\Api;

class Linking
{
    public function __construct()
    {
        add_filter('rest_prepare_alarm', array($this, 'addAlarmStation'), 10, 3);

        add_filter('rest_prepare_big-disturbance', array($this, 'addAlarmToDisturbance'), 10, 3);
        add_filter('rest_prepare_small-disturbance', array($this, 'addAlarmToDisturbance'), 10, 3);
    }

    /**
     * Add station link to alarms
     * @param  object   $response
     * @param  \WP_Post $post
     * @param  array    $request
     * @return object
     */
    public function addAlarmStation($response, $post, $request)
    {
        $station = get_post(get_field('station', $post->ID));

        if ($station) {
            $response->add_link(
                'station',
                rest_url('/wp/v2/station/' . $station->ID),
                array('embeddable' => true)
            );
        }

        return $response;
    }

    public function addAlarmToDisturbance($response, $post, $request)
    {
        if (!get_field('alarm_connection', $post->ID)) {
            return $response;
        }

        $alarm = get_post(get_field('alarm_connection', $post->ID));

        if ($alarm) {
            $response->add_link(
                'alarm',
                rest_url('/wp/v2/alarm/' . $alarm->ID),
                array('embeddable' => true)
            );
        }

        return $response;
    }
}
