<?php

namespace ApiAlarmManager\Api;

use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetTerm;
use WpService\Contracts\RegisterRestRoute;
use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;

class Disturbances
{
    public function __construct(
        private GetOption&AddAction&RegisterRestRoute&GetTerm $wpService,
        private GetField&UpdateField $acfService
    ) {
        $this->wpService->addAction('rest_api_init', array($this, 'registerEndpoint'));
        $this->wpService->addAction('rest_api_init', array($this, 'setCacheHeader'), 15);
    }

    public function registerEndpoint()
    {
        register_rest_route('wp/v2', '/disturbances', array(
        'methods'  => 'GET',
        'callback' => array($this, 'getDisturbances'),
        ));
    }

    public function setCacheHeader()
    {
        remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
        add_filter('rest_pre_serve_request', function ($value) {
            header('Cache-Control: max-age=300');

            return $value;
        });
    }

    public function getDisturbances(\WP_REST_Request $request)
    {
        $args  = array(
            'post_status'    => 'publish',
            'posts_per_page' => -1
        );
        $place = $request->get_param('place');

        if (isset($place) && !empty($place)) {
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'place',
                    'terms'    => explode(',', $place),
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
            'big'   => $big->posts,
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
        $alarmIds = $this->acfService->getField('alarm_connection', $disturbance->ID);
        $alarms   = array();

        if (!is_array($alarmIds)) {
            return $alarms;
        }
        foreach ($alarmIds as $alarmId) {
            $alarms[$alarmId] = array(
                'title' => get_the_title($alarmId),
                'href'  => rest_url('/wp/v2/alarm/' . $alarmId)
            );
        }

        return $alarms;
    }
}
