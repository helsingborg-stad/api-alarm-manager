<?php

namespace ApiAlarmManager\Api;

class SmallDisturbanceFields extends \ApiAlarmManager\Entity\ApiFields
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
    }
}
