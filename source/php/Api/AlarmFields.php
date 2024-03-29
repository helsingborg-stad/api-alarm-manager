<?php

namespace ApiAlarmManager\Api;

class AlarmFields extends \ApiAlarmManager\Entity\ApiFields
{
    public $postType = 'alarm';

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

        // Station
        register_rest_field(
            $this->postType,
            'station',
            array(
                'get_callback' => array($this, 'getStation'),
                'update_callback' => array($this, 'stringUpdateCallBack'),
                'schema' => array(
                    'description' => 'Field containing a station object.',
                    'type' => 'object',
                    'context' => array('view', 'edit')
                )
            )
        );

        // Meta
        register_rest_field(
            $this->postType,
            'type',
            array(
                'get_callback' => array($this, 'stringGetCallBack'),
                'update_callback' => array($this, 'stringUpdateCallBack'),
                'schema' => array(
                    'description' => 'Field containing the alarm type.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );

        register_rest_field(
            $this->postType,
            'extend',
            array(
                'get_callback' => array($this, 'stringGetCallBack'),
                'update_callback' => array($this, 'stringUpdateCallBack'),
                'schema' => array(
                    'description' => 'Field containing the alarm extend level.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );

        register_rest_field(
            $this->postType,
            'address',
            array(
                'get_callback' => array($this, 'stringGetCallBack'),
                'update_callback' => array($this, 'stringUpdateCallBack'),
                'schema' => array(
                    'description' => 'Field containing the alarm address.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );

        register_rest_field(
            $this->postType,
            'coordinate_x',
            array(
                'get_callback' => array($this, 'stringGetCallBack'),
                'update_callback' => array($this, 'stringUpdateCallBack'),
                'schema' => array(
                    'description' => 'Field containing the x-coordinate for this alarm.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );

        register_rest_field(
            $this->postType,
            'coordinate_y',
            array(
                'get_callback' => array($this, 'stringGetCallBack'),
                'update_callback' => array($this, 'stringUpdateCallBack'),
                'schema' => array(
                    'description' => 'Field containing the y-coordinate for this alarm.',
                    'type' => 'string',
                    'context' => array('view', 'edit')
                )
            )
        );

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
