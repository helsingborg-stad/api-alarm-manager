<?php

namespace ApiAlarmManager\PostTypes;

class Alarms extends \ApiAlarmManager\Entity\CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            __('Alarms', 'api-alarm-manager'),
            __('Alarm', 'api-alarm-manager'),
            'alarm',
            array(
                'description'          => __('Alarms', 'api-alarm-manager'),
                'menu_icon'            => 'dashicons-megaphone',
                'public'               => true,
                'publicly_queriable'   => true,
                'show_ui'              => true,
                'show_in_nav_menus'    => true,
                'has_archive'          => true,
                'rewrite'              => array(
                    'slug'       => 'alarms',
                    'with_front' => false
                ),
                'hierarchical'         => false,
                'exclude_from_search'  => false,
                'supports'             => array('title', 'revisions', 'editor', 'thumbnail'),
            )
        );
    }
}
