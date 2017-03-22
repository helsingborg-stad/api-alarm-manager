<?php

namespace ApiAlarmManager\Api;

class Taxonomies
{
    public function __construct()
    {
        add_action('init', array($this, 'enableTaxonomyRestApi'), 50);
    }

    public function enableTaxonomyRestApi()
    {
        global $wp_taxonomies;

        $taxonomies=  array('place');

        foreach ($taxonomies as $taxonomy) {
            if (!isset($wp_taxonomies[$taxonomy]) || !is_object($wp_taxonomies[$taxonomy])) {
                continue;
            }

            $wp_taxonomies[ $taxonomy ]->show_in_rest = true;
            $wp_taxonomies[ $taxonomy ]->rest_base = $taxonomy;
            $wp_taxonomies[ $taxonomy ]->rest_controller_class = 'WP_REST_Terms_Controller';
        }
    }
}
