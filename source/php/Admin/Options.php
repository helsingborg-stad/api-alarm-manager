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
    }
}
