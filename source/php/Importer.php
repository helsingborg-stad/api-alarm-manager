<?php

namespace ApiAlarmManager;

use WpService\WpService;

class Importer
{
    public $importStarted = null;
    public $remoteNewestFile;

    public function __construct(
        private RemoteFileHandlerInterface $remoteFileHandler,
        private string $folder,
        private string $archiveFolder,
        private WpService $wpService
    ) {
    }

    public function addHooks()
    {
        $this->wpService->addAction('cron_import_alarms', array($this, 'import'));
        $this->wpService->addFilter('cron_schedules', array($this, 'cronSchedules'));

        if (isset($_GET['alarmimport'])) {
            $this->wpService->addAction('init', array($this, 'import'), 100);
        }

        $this->wpService->addAction('admin_init', array($this, 'checkCron'));
        $this->wpService->addAction('acf/save_post', array($this, 'scheduleImportCron'), 20);
        $this->wpService->addAction('wp_ajax_import_alarms', array($this, 'ajaxSingleImport'));
    }

    public function ajaxSingleImport()
    {
        $this->import();
        echo 'true';
        $this->wpService->wpDie();
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
        $intervalKey     = $intervalMinutes . 'min';

        if (!isset($schedules[$intervalKey])) {
            $schedules[$intervalKey] = array(
                'interval' => $intervalMinutes * 60,
                'display'  => __('Once every ' . $intervalMinutes . ' minutes')
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
        if ($postId !== 'options' || $this->wpService->getCurrentScreen()->id !== 'alarm_page_alarm-manager-options') {
            return;
        }

        $this->wpService->wpClearScheduledHook('cron_import_alarms');

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
        if (!$this->wpService->wpDoingAjax() && get_field('ftp_auto_import', 'option')) {
            $this->registerCron();
        }
    }

    /**
     * Register import alarm cron
     */
    public function registerCron()
    {
        if (!$this->wpService->wpNextScheduled('cron_import_alarms')) {
            $intervalMinutes = get_field('ftp_import_interval', 'option');
            $this->wpService->wpScheduleEvent(time(), $intervalMinutes . 'min', 'cron_import_alarms');
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

        $destination = $this->maybeCreateFolder($this->wpService->wpUploadDir()['basedir'] . '/alarms');

        if (!$destination) {
            throw new \Error('Destination folder missing');
        }

        /**
         * Remove content from all alarms previous to this version
         * @since 0.2.11
         */
        if (!get_option('api-alarm-manager-updated-content', false)) {
            $alarms = $this->wpService->getPosts(array(
                'post_type'      => 'alarm',
                'posts_per_page' => -1
            ));

            foreach ($alarms as $alarm) {
                $this->wpService->wpUpdatePost(array(
                    'ID'           => $alarm->ID,
                    'post_content' => null
                ));
            }

            $this->wpService->addOption('api-alarm-manager-updated-content', true);
        }

        $this->downloadFromRemote($destination);
        $this->importFromXml($destination, true);

        //Update last import var
        $this->wpService->updateOption('api-alarm-manager-last-import', $this->remoteNewestFile);

        $this->wpService->wpSendJson('true');
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
            $remoteFile = $this->wpService->trailingslashit($folder) . basename(ltrim($file, '/'));
            $localFile  = $this->wpService->trailingslashit($destination) . basename(ltrim($file, '/'));
            $copied     = $this->remoteFileHandler->copy($remoteFile, $localFile);

            if ($this->shouldArchiveRemoteFiles() && $copied === true) {
                $remoteArchiveDir = rtrim($this->getArchiveFolder(), '/');

                if ($this->remoteFileHandler->fileExists($remoteArchiveDir) === false) {
                    $this->remoteFileHandler->mkdir($remoteArchiveDir);
                }

                $this->remoteFileHandler->moveFile($remoteFile, $this->wpService->trailingslashit($remoteArchiveDir) . basename($file));
            }
        }
    }

    private function shouldArchiveRemoteFiles(): bool
    {
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
        $defaultArchiveFolder = $this->wpService->trailingslashit($this->folder) . '../archive/';
        $archiveFolder        = $this->wpService->sanitizeTextField($this->archiveFolder);
        $yearMonthFolder      = $this->wpService->trailingslashit(date('Y-m'));

        if (empty($archiveFolder)) {
            return $defaultArchiveFolder . $yearMonthFolder;
        }

        return $this->wpService->trailingslashit($archiveFolder) . $yearMonthFolder;
    }

    /**
     * Alerts the site admin that alarms could not be retrived
     * @return bool
     */

    public function alertSiteAdmin($type = "import")
    {
        $alerted = $this->wpService->getTransient('api-alarm-manager-alerted-' . $type);

        if ($alerted != true) {
            $this->wpService->wpMail(
                $this->wpService->bloginfo('admin_email'),
                __("Alarm manager: Could not ", 'api-alarm-manager') . $type,
                __(
                    "Alarm manager could not execute the last action with success. Action may be required to resolve this issue.",
                    'api-alarm-manager'
                )
            );
            $this->wpService->setTransient('api-alarm-manager-alerted-' . $type, true, 12 * HOUR_IN_SECONDS);
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
                if (is_numeric($xmlErrors = $this->wpService->getOption('api-event-manager-xml-error'))) {
                    $this->wpService->updateOption('api-event-manager-xml-error', ($xmlErrors + 1));
                } else {
                    $this->wpService->updateOption('api-event-manager-xml-error', 1);
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

            $station->post_title         = (string) $data->Place . ' ' . (string)$data->Station;
            $station->_alarm_manager_uid = (string) $data->Station;
            $station->station_id         = (string) $data->Station;
            $station->city               = (string) $data->Place;

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

        $alarm                     = new \ApiAlarmManager\Alarm();
        $alarm->post_title         = (string)$data->HtText;
        $alarm->post_date          = (string)$xml->SendTime;
        $alarm->_alarm_manager_uid = (string)$data->IDNumber;
        $alarm->alarm_id           = (string)$data->CaseID;
        $alarm->case_id            = (string)$data->CaseID;
        $alarm->type               = (string)$data->PresGrp;
        $alarm->extend             = (string)$data->Extend;
        $alarm->station            = is_a($station, '\ApiAlarmManager\Station') ? $station->ID : $station;
        $alarm->address            = $this->formatAddress((string)$data->Address);
        $alarm->city               = (string)$data->Place;
        $alarm->coordinate_x       = $coordinates[0];
        $alarm->coordinate_y       = $coordinates[1];
        $alarm->zone               = (string)$data->Zone;
        $alarm->to_zone            = (string)$data->ToZone;
        $alarm->save();

        if (is_string(@(string)$data->Place)) {
            $this->wpService->wpSetObjectTerms($alarm->ID, (string)$data->Place, 'place', false);
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
        $data    = $xml->Alarm;
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
            return $this->wpService->trailingslashit($path);
        }

        if (!mkdir($path, 0777)) {
            throw new \Exception('Could not create folder at path: ' . $path);
        }

        return $this->wpService->tralingslashit($path);
    }
}
