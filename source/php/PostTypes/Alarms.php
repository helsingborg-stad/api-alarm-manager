<?php

namespace ApiAlarmManager\PostTypes;

class Alarms extends \ApiAlarmManager\Entity\CustomPostType
{
    public function __construct()
    {
        add_filter('views_edit-alarm', array($this, 'addImportButtons'));
        add_action('wp_ajax_schedule_import', array($this, 'ajaxScheduleSingleImport'));

        add_action('rss_item', array($this, 'rssFields'));
        add_action('rss2_item', array($this, 'rssFields'));
        add_filter('the_title_rss', array($this, 'rssTitle'));

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

    public function rssFields()
    {
        if (get_post_type() !== 'alarm') {
            return;
        }

        $fields = array(
            'alarm_id',
            'case_id',
            'type',
            'extend',
            'station',
            'address',
            'city',
            'address_description',
            'coordinate_x',
            'coordinate_y',
            'zone',
            'to_zone'
        );

        $postId = get_the_ID();

        foreach ($fields as $field) {
            if ($value = get_field($field, $postId)) {
                echo '<' . $field . '>' . $value . '</' . $field . '>' . "\n";
            }
        }
    }

    public function rssTitle($title)
    {
        global $post;

        if (empty(get_field('city', $post->ID))) {
            return $title;
        }

        return $title . ' (' . get_field('city', $post->ID) . ')';
    }

    public function ajaxScheduleSingleImport()
    {
        wp_schedule_single_event(time() + 10, 'cron_import_alarms');
        update_option('api-alarm-manager-importing', true);
        echo 'true';
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
                wp_cache_delete('api-alarm-manager-importing', 'options');

                if (!get_transient('api-alarm-manager-importing')) {
                    $button .= '<button type="button" class="button-primary extraspace" data-action="start-alarm-import">' . __('Start alarm import', 'api-alarm-manager') . '</button>';
                } else {
                    $button .= '<button type="button" class="button-primary extraspace" disabled>' . __('Importing alarms', 'api-alarm-manager') . '…</button>';
                }

            }

            $button .= '</div>';

            $views['import-buttons'] = $button;
        }

        return $views;
    }
}
