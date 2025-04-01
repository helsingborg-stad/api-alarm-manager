<?php

/**
 * Plugin Name:       Api Alarm Manager
 * Plugin URI:        https://github.com/helsingborg-stad/api-alarm-manager
 * Description:       Creates WordPress Rest API endpoint for contal alarms
 * Version: 2.3.3
 * Author:            Kristoffer Svanmark
 * Author URI:        https://github.com/helsingborg-stad
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       api-alarm-manager
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('APIALARMMANAGER_PATH', plugin_dir_path(__FILE__));
define('APIALARMMANAGER_URL', plugins_url('', __FILE__));
define('APIALARMMANAGER_TEMPLATE_PATH', APIALARMMANAGER_PATH . 'templates/');

load_plugin_textdomain('api-alarm-manager', false, plugin_basename(dirname(__FILE__)) . '/languages');

// Autoload from plugin
if (file_exists(APIALARMMANAGER_PATH . 'vendor/autoload.php')) {
    require_once APIALARMMANAGER_PATH . 'vendor/autoload.php';
}

// Autoload from ABSPATH
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

require_once APIALARMMANAGER_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once APIALARMMANAGER_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new \ApiAlarmManager\Vendor\Psr4ClassLoader();
$loader->addPrefix('ApiAlarmManager', APIALARMMANAGER_PATH);
$loader->addPrefix('ApiAlarmManager', APIALARMMANAGER_PATH . 'source/php/');
$loader->register();

// Acf auto import and export
$acfExportManager = new \AcfExportManager\AcfExportManager();
$acfExportManager->setTextdomain('event-manager');
$acfExportManager->setExportFolder(APIALARMMANAGER_PATH . 'source/php/AcfFields/');
$acfExportManager->autoExport(array(
    'station'                => 'group_58ca3def60074',
    'alarm'                  => 'group_58ca423c4016f',
    'options-ftp'            => 'group_58ca5ce582e3b',
    'options-filters'        => 'group_58ca5f387fb86',
    'disturbance-alarm'      => 'group_58cf8618bda6a',
    'options-google-geocode' => 'group_58da566ac8a91',
    'options-rss'            => 'group_591aafe3cd78b',
    'options-templates'      => 'group_591ab89a6e9f5'
));
$acfExportManager->import();

// Start application
new \ApiAlarmManager\App();
