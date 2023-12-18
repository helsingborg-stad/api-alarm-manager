<?php

namespace ApiAlarmManager;

class App
{
    public function __construct()
    {
        // Redirects
        add_filter('login_redirect', array($this, 'loginRedirect'), 10, 3);
        add_action('admin_init', array($this, 'dashboardRedirect'));

        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        // Post types
        new PostTypes\Alarms();
        new PostTypes\Stations();
        new PostTypes\BigDisturbance();
        new PostTypes\SmallDisturbance();

        // Taxonomies
        new Taxonomies\Place();

        // Api
        new Api\Filter();
        new Api\PostTypes();
        new Api\Taxonomies();
        new Api\Linking();
        new Api\Disturbances();
        new Api\FireDangerLevels();

        new Api\AlarmFields();
        new Api\AlarmFilters();
        new Api\StationFields();
        new Api\SmallDisturbanceFields();
        new Api\BigDisturbanceFields();

        // Misc
        if (is_admin()) {
            new Admin\General();
            new Admin\Options();
            new Admin\Templates();
            new Admin\FireDangerLevels();
        }

        // Importer

        $remoteFileHandler = null;
        $settings = array(
            'enabled' => get_field('ftp_enabled', 'option'),
            'server' => get_field('ftp_server', 'option'),
            'username' => get_field('ftp_username', 'option'),
            'password' => get_field('ftp_password', 'option'),
            'mode' => get_field('ftp_mode', 'option'),
            'folder' => get_field('ftp_folder', 'option'),
            'ftp_archive_folder' => get_field('ftp_archive_folder', 'option')
        );

        if( $settings['enabled'] && $settings['server'] && $settings['username'] && $settings['password'] && $settings['folder'] && $settings['ftp_archive_folder'] ) {

            if ($settings['mode'] === 'sftp') {
                $remoteFileHandler = new SftpFileHandler(
                    $settings['server'],
                    $settings['username'],
                    $settings['password']
                );
            } else {
                $remoteFileHandler = new FtpFileHandler(
                    $settings['server'],
                    $settings['username'],
                    $settings['password'],
                    $settings['mode']
                );
            }
        }

        if( $remoteFileHandler && $settings['folder'] && $settings['ftp_archive_folder'] ) {
            $importer = new Importer($remoteFileHandler, $settings['folder'], $settings['ftp_archive_folder']);
            $importer->addHooks();
        }
        
    }

    public function enqueueScripts()
    {
        wp_enqueue_script('api-alarm-manager', APIALARMMANAGER_URL . '/assets/js/api-alarm-manager.js', array('jquery'), '1.0.0', true);
        wp_localize_script('api-alarm-manager', 'apiAlarmManagerLang', array(
            'importing' => __('Importing alarms', 'api-alarm-manager')
        ));
    }

    /**
     * Redirect user after successful login.
     *
     * @param string $redirect_to URL to redirect to.
     * @param string $request URL the user is coming from.
     * @param object $user Logged user's data.
     * @return string
     */
    public function loginRedirect($redirect_to, $request, $user)
    {
        if (!is_wp_error($user)) {
            // Redirect to alarm list
        }

        return $redirect_to;
    }

    /**
     * Redirect user when entering dashboard.
     * @return void
     */
    public function dashboardRedirect()
    {
        global $pagenow;
        if ($pagenow !== 'index.php') {
            return;
        }

        if (isset($_GET['page']) && in_array($_GET['page'], array('acf-upgrade'))) {
            return;
        }

        // Redirect to alarm list (remove the return here)
        return;
        wp_redirect(admin_url('edit.php?post_type=event'), 301);
        exit;
    }
}
