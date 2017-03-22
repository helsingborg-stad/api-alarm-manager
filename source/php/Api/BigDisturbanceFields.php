<?php

namespace ApiAlarmManager\Api;

class BigDisturbanceFields extends \ApiAlarmManager\Entity\ApiFields
{
    public $postType = 'big-disturbance';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRestFields'));
    }

    public function registerRestFields()
    {
        // Title as plain text
        register_rest_field(
            $this->postType,
            'title',
            array(
                'get_callback'    => array($this, 'addPlaintextField'),
                'update_callback' => null,
                'schema'          => null,
            )
        );

        // Content as plain text
        register_rest_field(
            $this->postType,
            'content',
            array(
                'get_callback'    => array($this, 'addPlaintextField'),
                'update_callback' => null,
                'schema'          => null,
            )
        );

        register_rest_field(
            $this->postType,
            'alarms',
            array(
                'get_callback' => function ($object, $field_name, $request, $formatted = true) {
                    $alarmIds = get_field('alarm_connection', $object['id']);
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
                },
                'schema' => array(
                    'description' => 'Field containing alarms connected to the disturbance.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );

        // Place
        register_rest_field(
            $this->postType,
            'place',
            array(
                'get_callback' => function ($object, $field_name, $request, $formatted = true) {
                    return wp_get_post_terms($object['id'], 'place');
                },
                'schema' => array(
                    'description' => 'Field containing alarm place taxonomy terms.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );
    }
}
