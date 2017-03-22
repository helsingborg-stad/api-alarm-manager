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

        $this->addTableColumn('cb', '<input type="checkbox">');
        $this->addTableColumn('title', __('Title', 'api-alarm-manager'));

        $this->addTableColumn('taxonomy-place', __('Place', 'api-alarm-manager'));

        $this->addTableColumn('station', __('Station', 'api-alarm-manager'), true, function ($column, $postId) {
            $station = get_field('station', $postId);

            if (!$station) {
                echo '<span class="screen-reader-text">Inga kategorier</span><span aria-hidden="true">—</span>';
                return;
            }

            echo '<a href="' . get_edit_post_link($station) . '">' . get_the_title($station) . '</a>';
        });

        $this->addTableColumn('date', __('Date'));
    }
}
