<?php

namespace ApiAlarmManager\Api;

class AlarmFilters
{
    public function __construct()
    {
        add_filter('rest_alarm_query', array($this, 'place'), 10, 2);
        add_filter('rest_alarm_query', array($this, 'station'), 10, 2);
    }

    /**
     * Get alarms by place
     * @param  array $args
     * @param  array $request
     * @return array
     */
    public function place($args, $request)
    {
        if (!isset($request['place']) || empty($request['place'])) {
            return $args;
        }

        $args['meta_key'] = 'place';
        $args['meta_value'] = $request['place'];

        return $args;
    }

    /**
     * Get alarms by place
     * @param  array $args
     * @param  array $request
     * @return array
     */
    public function station($args, $request)
    {
        if (!isset($request['station']) || empty($request['station'])) {
            return $args;
        }

        if (!is_numeric($request['station'])) {
            $station = get_posts(array(
                'post_type' => 'station',
                'post_status' => 'publish',
                'meta_key' => 'station_id',
                'meta_value' => $request['station'],
                'posts_per_page' => 1
            ));

            if ($station) {
                $request['station'] = $station[0]->ID;
            }
        }

        $args['meta_key'] = 'station';
        $args['meta_value'] = (int) $request['station'];

        return $args;
    }
}
