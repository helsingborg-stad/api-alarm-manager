<?php

namespace ApiAlarmManager;

class SftpFileHandler implements RemoteFileHandler
{
    private string $username;
    private string $password;
    private $sftp;

    public function __construct(string $server, string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->sftp = new \phpseclib3\Net\SFTP($server);
    }

    public function connect(): bool
    {
        if (!$this->sftp->login($this->username, $this->password)) {
            trigger_error("Login Failed", E_USER_WARNING);
            return false;
        }

        return true;
    }

    public function copy($remoteFile, $localFile): bool
    {
        $content = $this->sftp->get($remoteFile);

        if (!is_string($content) || empty($content)) {
            trigger_error("Could not download file: $remoteFile", E_USER_WARNING);
            return false;
        }

        if (file_exists($localFile)) {
            trigger_error("File already exists: $localFile", E_USER_NOTICE);
            return false;
        }

        if (($fileHandle = fopen($localFile, 'w')) === false) {
            trigger_error("Could not create file: $localFile", E_USER_WARNING);
            return false;
        }

        if (fwrite($fileHandle, $content) === false) {
            trigger_error("Could not write file: $localFile", E_USER_WARNING);
            return false;
        }

        fclose($fileHandle);

        return true;
    }


    public function list(string $path)
    {
        $fileList = $this->sftp->nlist($path);

        if ($fileList === false) {
            trigger_error("Failed to list files", E_USER_WARNING);
            return false;
        }

        // Remove '.' and '..' from the list
        return $fileList = array_filter($fileList, fn ($file) => $file !== '.' && $file !== '..');
    }

    public function fileExists(string $path): bool
    {
        return $this->sftp->file_exists($path);
    }

    public function mkdir(string $path): bool
    {
        return $this->sftp->mkdir($path);
    }

    public function moveFile(string $source, string $destination): bool
    {
        return $this->sftp->rename($source, $destination);
    }
}
