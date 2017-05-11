<?php

namespace ApiAlarmManager;

class Importer
{
    public $importStarted = null;
    public $remoteNewestFile;

    private $excecutionTime = 600; //Execution time in minutes

    public function __construct()
    {
        add_action('cron_import_alarms', array($this, 'import'));
        add_filter('cron_schedules', array($this, 'cronSchedules'));

        if (isset($_GET['alarmimport'])) {
            add_action('init', array($this, 'import'), 100);
        }

        add_action('acf/save_post', array($this, 'scheduleImportCron'), 20);
    }

    /**
     * Add interval to schedules
     * @param  array $schedules
     * @return array
     */
    public function cronSchedules($schedules)
    {
        if (!get_field('ftp_auto_import', 'option')) {
            return $schedules;
        }

        $intervalMinutes = get_field('ftp_import_interval', 'option');
        $intervalKey = $intervalMinutes . 'min';

        if (!isset($schedules[$intervalKey])) {
            $schedules[$intervalKey] = array(
                'interval' => $intervalMinutes * 60,
                'display' => __('Once every ' . $intervalMinutes . ' minutes')
            );
        }

        return $schedules;
    }

    /**
     * Schedule import
     * @param  int $postId
     * @return void
     */
    public function scheduleImportCron($postId)
    {
        if ($postId !== 'options' || get_current_screen()->id !== 'alarm_page_alarm-manager-options') {
            return;
        }

        wp_clear_scheduled_hook('cron_import_alarms');

        if (!get_field('ftp_auto_import', 'option')) {
            return;
        }

        $intervalMinutes = get_field('ftp_import_interval', 'option');

        if (!wp_next_scheduled('cron_import_alarms')) {
            wp_schedule_event(time(), $intervalMinutes . 'min', 'cron_import_alarms');
        }
    }

    /**
     * Starts the import process
     * @return void
     */
    public function import()
    {

        //Check if running (using transient)
        if (get_transient('api-alarm-manager-importing')) {
            wp_send_json('false, already running');
            exit;
        }

        ini_set('max_execution_time', $this->excecutionTime);
        $this->importStarted = time();

        $destination = $this->maybeCreateFolder(wp_upload_dir()['basedir'] . '/alarms');

        if (!$destination) {
            throw new \Error('Destination folder missing');
        }

        $this->downloadFromFtp($destination);
        $this->importFromXml($destination, true);

        //Mark as done
        update_option('api-alarm-manager-last-import', $this->remoteNewestFile);
        delete_transient('api-alarm-manager-importing');

        wp_send_json('true');
        exit;
    }

    /**
     * Downloads files from ftp to destinatin folder
     * @param  string $destination Path to destination folder
     * @return bool
     */
    public function downloadFromFtp(string $destination) : bool
    {
        $ftp = ftp_connect($this->getFtpDetails('server'));

        wp_cache_delete('api-alarm-manager-last-import', 'options');
        $lastImport = get_option('api-alarm-manager-last-import');

        // Try to login
        if (!ftp_login($ftp, $this->getFtpDetails('username'), $this->getFtpDetails('password'))) {
            throw new \Exception('Could not connect to alarm ftp.');
        }

        // Set passive mode (?)
        if ($this->getFtpDetails('mode') === 'passive') {
            ftp_pasv($ftp, true);
        }

        $files = ftp_nlist($ftp, '-rt ' . $this->getFtpDetails('folder'));
        if (!is_array($files)) {
            $this->alertSiteAdmin("import");
            throw new \Exception('Could not list alarms from ftp.');
        }

        $skipped = 0;
        foreach ($files as $file) {
            $modtime = ftp_mdtm($ftp, trailingslashit($this->getFtpDetails('folder')) . $file);

            if ($lastImport && $lastImport > $modtime) {
                $skipped++;

                // Break if skipped more than 5
                if ($skipped > 5) {
                    break;
                }

                continue;
            }

            if (!get_transient('api-alarm-manager-importing')) {
                set_transient('api-alarm-manager-importing', true, $this->excecutionTime);
            }

            if (empty($this->remoteNewestFile) || $modtime > $this->remoteNewestFile) {
                $this->remoteNewestFile = $modtime;
            }

            ftp_get(
                $ftp,
                $destination . $file,
                trailingslashit($this->getFtpDetails('folder')) . $file,
                FTP_ASCII
            );
        }

        ftp_close($ftp);

        return true;
    }

    /**
     * Alerts the site admin that alarms could not be retrived
     * @return bool
     */

    public function alertSiteAdmin($type = "import")
    {
        $alerted = get_transient('api-alarm-manager-alerted-' . $type);

        if ($alerted != true) {
            wp_mail(bloginfo('admin_email'),  __("Alarm manager: Could not ", 'api-alarm-manager') . $type, __("Alarm manager could not execute the last action with success. Action may be required to resolve this issue.", 'api-alarm-manager'));
            set_transient('api-alarm-manager-alerted-' . $type, true, 12 * HOUR_IN_SECONDS);
        }
    }

    /**
     * Read xml-files from disc and save them as posts
     * @param  string $fromFolder
     * @return void
     */
    public function importFromXml(string $fromFolder, $removeFile = false)
    {
        foreach (glob($fromFolder . '*.{xml,XML}', GLOB_BRACE) as $file) {
            $xml = @simplexml_load_file($file);

            if (!$xml) {
                if (is_numeric($xmlErrors = get_option('api-event-manager-xml-error'))) {
                    update_option('api-event-manager-xml-error', ($xmlErrors+1));
                } else {
                    update_option('api-event-manager-xml-error', ($xmlErrors+1));
                }

                if (WP_DEBUG) {
                    error_log('Could not read xml-file: ' . $file);
                }
                continue;
            }

            if ($this->isMatchingKeywordFilter($xml)) {
                continue;
            }

            $this->createOrUpdate($xml);

            // Remove xml-file when done
            if ($removeFile) {
                unlink($file);
            }
        }
    }

    /**
     * Create or update post for alarm
     * @param  object $xml
     * @return bool
     */
    public function createOrUpdate($xml)
    {
        $data = $xml->Alarm;

        // Create/update station
        $station = new \ApiAlarmManager\Station();
        $station->post_title = (string)$data->Place . ' ' . (string)$data->Station;
        $station->_alarm_manager_uid = (string)$data->Station;
        $station->station_id = (string)$data->Station;
        $station->city = (string)$data->Place;
        $station->save();

        if (is_string(@(string)$data->Place)) {
            wp_set_object_terms($station->ID, (string)$data->Place, 'place', false);
        }

        // Create/update alarm
        if (class_exists('\\Drola\\CoordinateTransformationLibrary\\Transform')) {
            $coordinates = \Drola\CoordinateTransformationLibrary\Transform::RT90ToWGS84((string)$data->{"RT90-X"}, (string)$data->{"RT90-Y"});
        } else {
            $this->alertSiteAdmin("coordinates");
            $coordinates = array("","");
        }

        $alarm = new \ApiAlarmManager\Alarm();
        $alarm->post_title = (string)$data->HtText;
        $alarm->post_content = (string)$data->Comment;
        $alarm->post_date = (string)$xml->SendTime;
        $alarm->_alarm_manager_uid = (string)$data->IDNumber;
        $alarm->alarm_id = (string)$data->CaseID;
        $alarm->case_id = (string)$data->CaseID;
        $alarm->type = (string)$data->PresGrp;
        $alarm->extend = (string)$data->Extend;
        $alarm->station = $station->ID;
        $alarm->address = $this->formatAddress((string)$data->Address);
        $alarm->city = (string)$data->Place;
        $alarm->address_description = (string)$data->AddressDescription;
        $alarm->coordinate_x = $coordinates[0];
        $alarm->coordinate_y = $coordinates[1];
        $alarm->zone = (string)$data->Zone;
        $alarm->to_zone = (string)$data->ToZone;
        $alarm->save();

        if (is_string(@(string)$data->Place)) {
            wp_set_object_terms($alarm->ID, (string)$data->Place, 'place', false);
        }

        return true;
    }

    /**
     * Unpersonalizes and formats address
     * @param  string       $address       The unformatted address
     * @param  bool|boolean $unpersonalize To unpersonalize or not
     * @return string                      Formatted & unpersonalized address
     */
    public function formatAddress(string $address, bool $unpersonalize = true) : string
    {
        $parts = \ApiAlarmManager\Helper\Address::gmapsGetAddressComponents($address);

        // Bail if no parts
        if (!isset($parts->formatted_address)) {
            return trim(preg_replace('/(\d+)(\s)?(trappor|tr|[a-z]+)?/i', '', $address));
        }

        $streetBefore = $parts->street;
        $parts->street = trim(preg_replace('/(\d+)(\s)?(trappor|tr|[a-z]+)?/i', '', $parts->street));

        $parts->formatted_address = str_replace($streetBefore, $parts->street, $parts->formatted_address);

        return $parts->formatted_address;
    }

    /**
     * Check if any data matches any keyword filter
     * @param  object  $xml
     * @return boolean
     */
    public function isMatchingKeywordFilter($xml)
    {
        $data = $xml->Alarm;

        $filters = \ApiAlarmManager\Admin\Options::getFilters();
        $filters = implode('|', $filters);

        $continue = false;

        foreach ($data as $item) {
            foreach ($item as $field => $value) {
                if (preg_match('/(' . $filters . ')/i', $value)) {
                    $continue = true;
                }
            }
        }

        return $continue;
    }

    /**
     * Creates folder if needed
     * @param  string $path
     * @return string
     */
    public function maybeCreateFolder(string $path)
    {
        if (file_exists($path)) {
            return trailingslashit($path);
        }

        if (!mkdir($path, 0777)) {
            throw new \Exception('Could not create folder at path: ' . $path);
        }

        return trailingslashit($path);
    }

    /**
     * Gets ftp connection details
     * @return array
     */
    public function getFtpDetails($what = null)
    {
        if (!get_field('ftp_enabled', 'option')) {
            return array();
        }

        if (in_array($what, array('server', 'username', 'password', 'mode', 'folder'))) {
            return get_field('ftp_' . $what, 'option');
        }

        return array(
            'server' => get_field('ftp_server', 'option'),
            'username' => get_field('ftp_username', 'option'),
            'password' => get_field('ftp_password', 'option'),
            'mode' => get_field('ftp_mode', 'option'),
            'folder' => get_field('ftp_folder', 'option')
        );
    }
}
