<?php

namespace ApiAlarmManager;

class Importer
{
    public $importStarted = null;
    public $remoteNewestFile;
    private RemoteFileHandlerInterface $remoteFileHandler;
    private string $folder;
    private string $archiveFolder;

    public function __construct(RemoteFileHandlerInterface $remoteFileHandler, string $folder, string $archiveFolder)
    {
        $this->remoteFileHandler = $remoteFileHandler;
        $this->folder = $folder;
        $this->archiveFolder = $archiveFolder;
    }

    public function addHooks() {
        add_action('cron_import_alarms', array($this, 'import'));
        add_filter('cron_schedules', array($this, 'cronSchedules'));

        if (isset($_GET['alarmimport'])) {
            add_action('init', array($this, 'import'), 100);
        }

        add_action('admin_init', array($this, 'checkCron'));
        add_action('acf/save_post', array($this, 'scheduleImportCron'), 20);
        add_action('wp_ajax_import_alarms', array($this, 'ajaxSingleImport'));
    }

    public function ajaxSingleImport()
    {
        $this->import();
        echo 'true';
        wp_die();
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

        $this->registerCron();
    }

    /**
     * Create new cron schedule if missing
     */
    public function checkCron()
    {
        if (!wp_doing_ajax() && get_field('ftp_auto_import', 'option')) {
            $this->registerCron();
        }
    }

    /**
     * Register import alarm cron
     */
    public function registerCron()
    {
        if (!wp_next_scheduled('cron_import_alarms')) {
            $intervalMinutes = get_field('ftp_import_interval', 'option');
            wp_schedule_event(time(), $intervalMinutes . 'min', 'cron_import_alarms');
        }
    }

    /**
     * Starts the import process
     * @return void
     */
    public function import()
    {
        ini_set('max_execution_time', 600);
        $this->importStarted = time();

        $destination = $this->maybeCreateFolder(wp_upload_dir()['basedir'] . '/alarms');

        if (!$destination) {
            throw new \Error('Destination folder missing');
        }

        /**
         * Remove content from all alarms previous to this version
         * @since 0.2.11
         */
        if (!get_option('api-alarm-manager-updated-content', false)) {
            $alarms = get_posts(array(
                'post_type' => 'alarm',
                'posts_per_page' => -1
            ));

            foreach ($alarms as $alarm) {
                wp_update_post(array(
                    'ID' => $alarm->ID,
                    'post_content' => null
                ));
            }

            add_option('api-alarm-manager-updated-content', true);
        }

        $this->downloadFromRemote($destination);
        $this->importFromXml($destination, true);

        //Update last import var
        update_option('api-alarm-manager-last-import', $this->remoteNewestFile);

        wp_send_json('true');
        exit;
    }

    /**
     * Downloads files from sftp to destination folder
     * @param  string $destination Path to destination folder
     * @return void
     */
    public function downloadFromRemote(string $destination): void
    {
        $folder = $this->folder;
        $this->remoteFileHandler->connect();

        $fileList = $this->remoteFileHandler->list($folder);

        foreach ($fileList as $file) {
            $remoteFile = trailingslashit($folder) . basename(ltrim($file, '/'));
            $localFile = trailingslashit($destination) . basename(ltrim($file, '/'));
            $copied = $this->remoteFileHandler->copy($remoteFile, $localFile);

            if ($this->shouldArchiveRemoteFiles() && $copied === true) {
                $remoteArchiveDir = rtrim($this->getArchiveFolder(), '/');

                if ($this->remoteFileHandler->fileExists($remoteArchiveDir) === false) {
                    $this->remoteFileHandler->mkdir($remoteArchiveDir);
                }

                $this->remoteFileHandler->moveFile($remoteFile, trailingslashit($remoteArchiveDir) . basename($file));
            }
        }
    }

    private function shouldArchiveRemoteFiles():bool {
        return 
            defined('API_ALARM_MANAGER_ARCHIVE_ALARMS_ON_REMOTE') &&
            constant('API_ALARM_MANAGER_ARCHIVE_ALARMS_ON_REMOTE') === true;
    }

    /**
     * Get archive folder
     *
     * @return string
     */
    private function getArchiveFolder()
    {
        $defaultArchiveFolder = trailingslashit($this->folder) . '../archive/';
        $archiveFolder = sanitize_text_field($this->archiveFolder);
        $yearMonthFolder = trailingslashit(date('Y-m'));

        if (empty($archiveFolder)) {
            return $defaultArchiveFolder . $yearMonthFolder;
        }

        return trailingslashit($archiveFolder) . $yearMonthFolder;
    }

    /**
     * Alerts the site admin that alarms could not be retrived
     * @return bool
     */

    public function alertSiteAdmin($type = "import")
    {
        $alerted = get_transient('api-alarm-manager-alerted-' . $type);

        if ($alerted != true) {
            wp_mail(bloginfo('admin_email'), __("Alarm manager: Could not ", 'api-alarm-manager') . $type,
                __("Alarm manager could not execute the last action with success. Action may be required to resolve this issue.",
                    'api-alarm-manager'));
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
                    update_option('api-event-manager-xml-error', ($xmlErrors + 1));
                } else {
                    update_option('api-event-manager-xml-error', 1);
                }

                if (WP_DEBUG) {
                    error_log('Could not read xml-file: ' . $file);
                }
                // Remove xml-file
                if ($removeFile) {
                    unlink($file);
                }
            } elseif (!$this->isMatchingKeywordFilter($xml)) {
                $this->createOrUpdate($xml);
            }

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
        $station = null;
        if (strlen((string)$data->Station) > 0) {
            $station = new \ApiAlarmManager\Station();

            $station->post_title            = (string) $data->Place . ' ' . (string)$data->Station;
            $station->_alarm_manager_uid    = (string) $data->Station;
            $station->station_id            = (string) $data->Station;
            $station->city                  = (string) $data->Place;

            $station->save([
                'post_title',
                'post_content'
            ]);

            if (is_string(@(string)$data->Place)) {
                wp_set_object_terms($station->ID, (string)$data->Place, 'place', false);
            }
        }

        // Create/update alarm
        if (class_exists('\\Drola\\CoordinateTransformationLibrary\\Transform')) {
            $coordinates = \Drola\CoordinateTransformationLibrary\Transform::RT90ToWGS84(
                (string)$data->{"RT90-X"},
                (string)$data->{"RT90-Y"}
            );

            //Round coordinates, to mask exact position.
            if (is_array($coordinates) && count($coordinates) == 2) {
                $coordinates = array(
                    round($coordinates[0], 2),
                    round($coordinates[1], 2)
                );
            } else {
                $coordinates = array("", "");
            }
        } else {
            $this->alertSiteAdmin("coordinates");
            $coordinates = array("", "");
        }

        $alarm = new \ApiAlarmManager\Alarm();
        $alarm->post_title = (string)$data->HtText;
        $alarm->post_date = (string)$xml->SendTime;
        $alarm->_alarm_manager_uid = (string)$data->IDNumber;
        $alarm->alarm_id = (string)$data->CaseID;
        $alarm->case_id = (string)$data->CaseID;
        $alarm->type = (string)$data->PresGrp;
        $alarm->extend = (string)$data->Extend;
        $alarm->station = is_a($station, '\ApiAlarmManager\Station') ? $station->ID : $station;
        $alarm->address = $this->formatAddress((string)$data->Address);
        $alarm->city = (string)$data->Place;
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
     * @param  string $address The unformatted address
     * @param  bool|boolean $unpersonalize To unpersonalize or not
     * @return string                      Formatted & unpersonalized address
     */
    public function formatAddress(string $address, bool $unpersonalize = true): string
    {
        $parts = \ApiAlarmManager\Helper\Address::gmapsGetAddressComponents($address);

        //Not autogenerated
        if (isset($parts->formatted_address)) {
            $address = $parts->formatted_address;
        }

        //Sanitize
        $removeSpacesBehind = array(
            "Väg",
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
        );

        //Concat words and numbers
        if (is_array($removeSpacesBehind) && !empty($removeSpacesBehind)) {
            foreach ($removeSpacesBehind as $replace) {
                $address = str_replace(" " . $replace . " ", " " . $replace, $address);
            }
        }

        //Explode string as array, to be parsed
        $address = explode(" ", $address);

        //Parse
        if (is_array($address) && !empty($address)) {
            foreach ($address as $key => $word) {
                if (!empty($word) && is_numeric($word[0])) {
                    unset($address[$key]);
                }
            }

            $address = implode(" ", $address);
        }

        return ucwords(trim($address));
    }

    /**
     * Check if any data matches any keyword filter
     * @param  object $xml
     * @return boolean
     */
    public function isMatchingKeywordFilter($xml)
    {
        $data = $xml->Alarm;
        $filters = \ApiAlarmManager\Admin\Options::getFilters();
        $filters = implode('|', $filters);

        if (empty($filters)) {
            return false;
        }

        foreach ($data as $item) {
            foreach ($item as $field => $value) {
                if (preg_match('/(' . $filters . ')/i', $value)) {
                    return true;
                }
            }
        }

        return false;
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
}
