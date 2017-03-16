<?php

namespace ApiAlarmManager;

class Importer
{
    public function __construct()
    {
        if (isset($_GET['alarmimport'])) {
            add_action('init', array($this, 'import'));
        }
    }

    /**
     * Starts the import process
     * @return void
     */
    public function import()
    {
        $destination = $this->maybeCreateFolder(wp_upload_dir()['basedir'] . '/alarms');

        if (!$destination) {
            return false;
        }

        //$this->downloadFromFtp($destination);
        $this->importFromXml($destination);

        \ApiAlarmManager\Api\Filter::redirectToApi();
    }

    /**
     * Downloads files from ftp to destinatin folder
     * @param  string $destination Path to destination folder
     * @return bool
     */
    public function downloadFromFtp(string $destination) : bool
    {
        $ftp = ftp_connect($this->getFtpDetails('server'));

        // Try to login
        if (!ftp_login($ftp, $this->getFtpDetails('username'), $this->getFtpDetails('password'))) {
            throw new \Exception('Could not connect to alarm ftp.');
        }

        // Set passive mode (?)
        if ($this->getFtpDetails('mode') === 'passive') {
            ftp_pasv($ftp, true);
        }

        $files = ftp_nlist($ftp, $this->getFtpDetails('folder'));
        if (!is_array($files)) {
            throw new \Exception('Could not list alarms from ftp.');
        }

        foreach ($files as $file) {
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
     * Read xml-files from disc and save them as posts
     * @param  string $fromFolder
     * @return void
     */
    public function importFromXml(string $fromFolder, $removeFile = false)
    {
        foreach (glob($fromFolder . '*.{xml,XML}', GLOB_BRACE) as $file) {
            $xml = @simplexml_load_file($file);
            if (!$xml) {
                error_log('Could not read xml-file: ' . $file);
                continue;
            }

            $data = $xml->Alarm;

            // Create/update station
            $station = new \ApiAlarmManager\Station();
            $station->post_title = (string)$data->Place . ' ' . (string)$data->Station;
            $station->_alarm_manager_uid = (string)$data->Station;
            $station->station_id = (string)$data->Station;
            $station->city = (string)$data->Place;
            $station->save();

            // Create/update alarm
            $alarm = new \ApiAlarmManager\Alarm();
            $alarm->post_title = (string)$data->HtText;
            $alarm->post_content = (string)$data->Comment;
            $alarm->post_date = (string)$xml->SendTime;
            $alarm->_alarm_manager_uid = (string)$data->IDNumber;
            $alarm->type = (string)$data->PresGrp;
            $alarm->extend = (string)$data->Extend;
            $alarm->station = $station->ID;
            $alarm->address = (string)$data->Address;
            $alarm->place = (string)$data->Place;
            $alarm->address_description = (string)$data->AddressDescription;
            $alarm->coordinate_x = (string)$data->{"RT90-X"};
            $alarm->coordinate_y = (string)$data->{"RT90-Y"};
            $alarm->zone = (string)$data->Zone;
            $alarm->to_zone = (string)$data->ToZone;
            $alarm->save();

            // Remove xml-file when done
            if ($removeFile) {
                unlink($file);
            }
        }
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
