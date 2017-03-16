<?php

namespace ApiAlarmManager;

class App
{
    public function __construct()
    {
        // Redirects
        add_filter('login_redirect', array($this, 'loginRedirect'), 10, 3);
        add_action('admin_init', array($this, 'dashboardRedirect'));

        // Post types
        new PostTypes\Alarms();
        new PostTypes\Stations();

        // Api
        new Api\Filter();
        new Api\PostTypes();
        new Api\Linking();

        new Api\AlarmFields();
        new Api\StationFields();

        // Misc
        new Admin();
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
