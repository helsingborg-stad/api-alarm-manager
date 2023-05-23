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

        add_action('admin_notices', [$this, 'adminNotices']);
    }

    /**
     * Get alarm filter keywords
     * @return array
     */
    public static function getFilters(): array
    {
        $filters = get_field('alarm_filters', 'option');

        if (is_array($filters) && !empty($filters)) {
            foreach ($filters as &$filter) {
                $filter = $filter['keyword'];
            }

            return $filters;
        }

        if (is_string($filters)) {
            return [$filters];
        }

        return [];
    }

    public function adminNotices()
    {
        $currentScreen = get_current_screen();

        if ($currentScreen->base !== 'alarm_page_alarm-manager-options') {
            return;
        }

        if (self::serverSupportsSftp() === false) {
            echo $this->getNoSftpNotice();
        }
    }

    public static function serverSupportsSftp(): bool
    {
        return extension_loaded( 'ssh2' );
    }

    private function getNoSftpNotice(): string
    {
        $message = __('This server does not support SFTP connections. If you intend to utilize SFTP connection, please verify that you support the SSH PHP library', 'event-manager');
        $notice = '<div class="notice notice-warning is-dismissible">';
        $notice .= '<p>' . $message . '</p>';
        $notice .= '</div>';

        return $notice;
    }
}
