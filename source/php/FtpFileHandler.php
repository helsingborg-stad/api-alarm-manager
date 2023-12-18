<?php

namespace ApiAlarmManager;

class FtpFileHandler implements RemoteFileHandlerInterface
{
    private string $username;
    private string $password;
    private string $mode;
    private string $server;
    private \FTP\Connection|false $ftp;

    public function __construct(string $server, string $username, string $password, string $mode)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->mode = $mode;
    }

    public function connect(): bool
    {
        $connection = ftp_connect($this->server);

        if( $connection !== false ) {
            $this->ftp = $connection;
        } else {
            trigger_error("Failed to connect to FTP server", E_USER_WARNING);
        }

        if (!ftp_login($this->ftp, $this->username, $this->password)) {
            trigger_error("Login Failed", E_USER_WARNING);

            return false;
        }

        if ($this->mode === 'passive') {
            ftp_pasv($this->ftp, true);
        }

        return true;
    }

    public function copy($remoteFile, $localFile): bool
    {
        return ftp_get($this->ftp, $localFile, $remoteFile, FTP_BINARY);
    }

    public function list(string $path): array
    {
        $fileList = ftp_nlist($this->ftp, '-rt ' . $path);

        if (is_array($fileList)) {
            return $fileList;
        }

        trigger_error("Failed to list files", E_USER_WARNING);
        return [];
    }

    public function fileExists(string $path): bool
    {
        // Could be file of folder

        $fileList = $this->list(dirname($path));

        if (in_array($path, $fileList)) {
            return true;
        }

        return false;
    }

    public function mkdir(string $path): bool
    {
        return ftp_mkdir($this->ftp, $path);
    }

    public function moveFile(string $source, string $destination): bool
    {
        return ftp_rename($this->ftp, $source, $destination);
    }
}
