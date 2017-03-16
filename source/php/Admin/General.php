<?php

namespace ApiAlarmManager\Admin;

class General
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'removeAdminMenuItems'), 100);
        add_action('wp_before_admin_bar_render', array($this, 'removeAdminBarItems'), 100);
    }

    /**
     * Removes unwanted admin menu items
     * @return void
     */
    public function removeAdminMenuItems()
    {
        remove_menu_page('index.php');                      //Dashboard
        remove_menu_page('edit.php');                       //Posts
        remove_menu_page('edit.php?post_type=page');        //Pages
        remove_menu_page('edit-comments.php');              //Comments
        remove_menu_page('themes.php');                     //Appearance
        remove_menu_page('tools.php');                      //Tools
    }

    /**
     * Removes unwanted admin bar items
     * @return void
     */
    public function removeAdminBarItems()
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('about');                // Remove the about WordPress link
        $wp_admin_bar->remove_menu('wporg');                // Remove the WordPress.org link
        $wp_admin_bar->remove_menu('documentation');        // Remove the WordPress documentation link
        $wp_admin_bar->remove_menu('support-forums');       // Remove the support forums link
        $wp_admin_bar->remove_menu('feedback');             // Remove the feedback link
        $wp_admin_bar->remove_menu('view-site');            // Remove the view site link
        $wp_admin_bar->remove_menu('updates');              // Remove the updates link
        $wp_admin_bar->remove_menu('comments');             // Remove the comments link
        $wp_admin_bar->remove_menu('new-content');          // Remove the content link
    }
}
