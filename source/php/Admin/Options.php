<?php

namespace ApiAlarmManager\Admin;

class Options
{
    public function __construct()
    {
        if (function_exists('acf_add_options_sub_page')) {
            acf_add_options_sub_page(array(
                'page_title'    => _x('Alarm manager options', 'ACF', 'event-manager'),
                'menu_title'    => _x('Options', 'Alarm manager options', 'event-manager'),
                'menu_slug'     => 'alarm-manager-options',
                'parent_slug'   => 'edit.php?post_type=alarm',
                'capability'    => 'edit_users'
            ));
        }

        add_action('admin_init', function () {
            \ApiAlarmManager\Admin\Options::getFilters();
        });
    }

    /**
     * Get alarm filter keywords
     * @return array
     */
    public static function getFilters()
    {
        $filters = get_field('alarm_filters', 'option');

        if(is_array($filters) && !empty($filters)) {
            foreach ($filters as &$filter) {
                $filter = $filter['keyword'];
            }
        }
        
        return $filters;
    }
}
