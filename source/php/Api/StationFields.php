<?php

namespace ApiAlarmManager\Api;

class StationFields extends \ApiAlarmManager\Entity\ApiFields
{
    public $postType = 'station';

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'registerRestFields'));
    }

    public function registerRestFields($args)
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
            'station_id',
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
            'street_address',
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
            'postal_code',
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
            'city',
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
