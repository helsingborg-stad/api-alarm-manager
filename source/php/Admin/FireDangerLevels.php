<?php

namespace ApiAlarmManager\Admin;

class FireDangerLevels
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'addMenuPage'));
    }

    public function addMenuPage()
    {
        acf_add_options_page(
            array(
                'page_title' => __('Fire Danger Levels', 'api-alarm-manager'),
                'menu_title' => __('Fire Danger Levels', 'api-alarm-manager'),
                'menu_slug' => 'fire-danger-levels',
                'capability' => 'edit_posts',
                'redirect' => false,
                'position' => 30,
                'icon_url' => 'dashicons-location-alt'
            )
        );
    }
}
