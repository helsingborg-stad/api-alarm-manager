<?php

namespace ApiAlarmManager\Api;

class AlarmFilters
{
    public function __construct()
    {
        //add_filter('rest_alarm_query', array($this, 'place'), 10, 2);
        add_filter('rest_alarm_query', array($this, 'station'), 10, 2);
        add_filter('rest_alarm_query', array($this, 'coordinates'), 10, 2);
        add_filter('rest_alarm_query', array($this, 'dates'), 10, 2);
    }

    /**
     * Filter alarms by date
     * @param  array           $args
     * @param  WP_REST_Request $request
     * @return array
     */
    public function dates($args, $request)
    {
        if ((!isset($request['date_from']) || empty($request['date_from'])) && (!isset($request['date_to']) || empty($request['date_to']))) {
            return $args;
        }

        $dateQuery = array('inclusive' => true);
        if (isset($request['date_from']) && !empty($request['date_from'])) {
            $dateQuery['after'] = $request['date_from'];
        }

        if (isset($request['date_to']) && !empty($request['date_to'])) {
            $dateQuery['before'] = $request['date_to'];
        }

        $args['date_query'] = array(
            $dateQuery
        );

        return $args;
    }

    /**
     * Get alarms by place
     * @param  array           $args
     * @param  WP_REST_Request $request
     * @return array
     */
    public function place($args, $request)
    {
        if (!isset($request['place']) || empty($request['place'])) {
            return $args;
        }

        $field = 'slug';
        if (is_numeric($request['place'])) {
            $field = 'term_id';
        }

        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'place',
                'field' => $field,
                'terms' => $request['place']
            )
        );

        return $args;
    }

    /**
     * Get alarms by station
     * @param  array           $args
     * @param  WP_REST_Request $request
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

    /**
     * Filter by coordinates and distance
     * @param  array           $args    The query arguments.
     * @param  WP_REST_Request $request Full details about the request.
     * @return array $args.
     **/
    public function coordinates($args, $request)
    {
        if (!isset($request['latlng']) || empty($request['latlng'])) {
            return $args;
        }

        $distance = !empty($request['distance']) ? str_replace(',', '.', $request['distance']) : 0;
        $filter = $request['latlng'];
        $latlng = explode(',', preg_replace('/\s+/', '', urldecode($filter)));

        if (count($latlng) != 2) {
            return $args;
        }

        $locations = self::getNearbyLocations($latlng[0], $latlng[1], floatval($distance));
        $idArray = ($locations) ? array_column($locations, 'post_id') : array(0);
        $args['post__in'] = $idArray;

        return $args;
    }

    /**
     * Get nearby locations within given distance
     * @param  string       $lat       latitude
     * @param  string       $lng       longitude
     * @param  int/float    $distance  radius distance in km
     * @return array with locations
     */
    public static function getNearbyLocations($lat, $lng, float $distance = 0)
    {
        global $wpdb;

        // Radius of the earth in kilometers.
        $earthRadius = 6371;

        $sql = $wpdb->prepare(
            "SELECT DISTINCT
                latitude.post_id,
                post.post_title,
                latitude.meta_value as lat,
                longitude.meta_value as lng,
                (%s * ACOS(
                    COS(RADIANS( %s )) * COS(RADIANS(latitude.meta_value)) * COS(
                    RADIANS(longitude.meta_value) - RADIANS( %s )
                    ) + SIN(RADIANS( %s )) * SIN(RADIANS(latitude.meta_value))
                )) AS distance
            FROM $wpdb->posts post
            INNER JOIN $wpdb->postmeta latitude ON post.ID = latitude.post_id
            INNER JOIN $wpdb->postmeta longitude ON post.ID = longitude.post_id
            AND post.post_type   = 'alarm'
            AND post.post_status = 'publish'
            AND latitude.meta_key = 'coordinate_x'
            AND longitude.meta_key = 'coordinate_y'
            HAVING distance <= %s
            ORDER BY distance ASC",
            $earthRadius,
            $lat,
            $lng,
            $lat,
            $distance
        );

        return $wpdb->get_results($sql, ARRAY_A);
    }
}
