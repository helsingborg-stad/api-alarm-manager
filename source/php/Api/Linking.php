<?php

namespace ApiAlarmManager\Api;

class Linking
{
    public function __construct()
    {
        add_filter('rest_prepare_alarm', array($this, 'addAlarmStation'), 10, 3);
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
}
