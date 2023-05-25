<?php

namespace ApiAlarmManager\Admin;

class FireDangerLevels
{
    private string $acfFieldId = 'field_646dfb45bc655';
    public static string $dateTimeChangedOptionName = 'fire_danger_levels_date_time_changed';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('acf/save_post', [$this, 'updateDateTimeChanged'], 5);
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

    public function updateDateTimeChanged($post_id)
    {
        $values = $_POST['acf'] ?? [];
        if (!isset($values[$this->acfFieldId])) {
            return;
        }

        update_option(self::$dateTimeChangedOptionName, time());
    }
}
