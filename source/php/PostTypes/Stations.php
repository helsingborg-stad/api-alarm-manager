<?php

namespace ApiAlarmManager\PostTypes;

class Stations extends \ApiAlarmManager\Entity\CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            __('Stations', 'api-alarm-manager'),
            __('Station', 'api-alarm-manager'),
            'station',
            array(
                'description'          => __('Stations', 'api-alarm-manager'),
                'menu_icon'            => 'dashicons-shield-alt',
                'public'               => true,
                'publicly_queriable'   => true,
                'show_ui'              => true,
                'show_in_nav_menus'    => true,
                'has_archive'          => true,
                'rewrite'              => array(
                    'slug'       => 'stations',
                    'with_front' => false
                ),
                'hierarchical'         => false,
                'exclude_from_search'  => false,
                'supports'             => array('title', 'revisions', 'editor', 'thumbnail'),
            )
        );
    }
}
