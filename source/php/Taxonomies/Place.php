<?php

namespace ApiAlarmManager\Taxonomies;

class Place
{
    public function __construct()
    {
        add_action('init', array($this, 'registerTaxonomy'));
    }

    public function registerTaxonomy()
    {
        $labels = array(
            'name'                  => _x('Places', 'Taxonomy plural name', 'api-alarm-manager'),
            'singular_name'         => _x('Place', 'Taxonomy singular name', 'api-alarm-manager'),
            'search_items'          => __('Search places', 'api-alarm-manager'),
            'popular_items'         => __('Popular places', 'api-alarm-manager'),
            'all_items'             => __('All places', 'api-alarm-manager'),
            'parent_item'           => __('Parent place', 'api-alarm-manager'),
            'parent_item_colon'     => __('Parent place', 'api-alarm-manager'),
            'edit_item'             => __('Edit place', 'api-alarm-manager'),
            'update_item'           => __('Update place', 'api-alarm-manager'),
            'add_new_item'          => __('Add new place', 'api-alarm-manager'),
            'new_item_name'         => __('New place', 'api-alarm-manager'),
            'add_or_remove_items'   => __('Add or remove places', 'api-alarm-manager'),
            'choose_from_most_used' => __('Choose from most used places', 'api-alarm-manager'),
            'menu_name'             => __('Places', 'api-alarm-manager'),
        );

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'show_in_nav_menus'     => true,
            'show_admin_column'     => true,
            'hierarchical'          => true,
            'show_tagcloud'         => true,
            'show_ui'               => true,
            'query_var'             => true,
            'rewrite'               => true
        );

        register_taxonomy('place', array('alarm', 'station', 'big-disturbance', 'small-disturbance'), $args);
    }
}
