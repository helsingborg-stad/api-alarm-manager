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

        $this->addTableColumn('cb', '<input type="checkbox">');
        $this->addTableColumn('title', __('Title', 'api-alarm-manager'));

        $this->addTableColumn('station_id', __('Station ID', 'api-alarm-manager'), true, function ($column, $postId) {
            echo get_field('station_id', $postId) ? get_field('station_id', $postId) : '<span class="screen-reader-text">' . __('No stations', 'api-alarm-manager') . '</span><span aria-hidden="true">—</span>';
        });

        $this->addTableColumn('city', __('City', 'api-alarm-manager'), true, function ($column, $postId) {
            echo get_field('city', $postId) ? get_field('city', $postId) : '<span class="screen-reader-text">' . __('No stations', 'api-alarm-manager') . '</span><span aria-hidden="true">—</span>';
        });

        $this->addTableColumn('taxonomy-place', __('Places', 'api-alarm-manager'));

        $this->addTableColumn('date', __('Date'));
    }
}
