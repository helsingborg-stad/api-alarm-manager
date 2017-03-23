<?php

namespace ApiAlarmManager\PostTypes;

class Alarms extends \ApiAlarmManager\Entity\CustomPostType
{
    public function __construct()
    {
        add_filter('views_edit-alarm', array($this, 'addImportButtons'));
        add_action('wp_ajax_schedule_import', array($this, 'ajaxScheduleSingleImport'));

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
                echo '<span class="screen-reader-text">' . __('No stations', 'api-alarm-manager') . '</span><span aria-hidden="true">—</span>';
                return;
            }

            echo '<a href="' . get_edit_post_link($station) . '">' . get_the_title($station) . '</a>';
        });

        $this->addTableColumn('date', __('Date'));
    }

    public function ajaxScheduleSingleImport()
    {
        wp_schedule_single_event(time() + 10, 'cron_import_alarms');
        echo 'hej på dig din lille graj';
        wp_die();
    }

    /**
     * Add buttons to start parsing xcap and Cbis
     * @return void
     */
    public function addImportButtons($views)
    {
        if (current_user_can('administrator')) {
            $button  = '<div class="import-buttons actions">';

            if (get_field('ftp_enabled', 'option') === true) {
                $button .= '<div class="button-primary extraspace" data-action="start-alarm-import">' . __('Import alarms', 'api-alarm-manager') . '</div>';
            }

            $button .= '</div>';

            $views['import-buttons'] = $button;
        }

        return $views;
    }
}
