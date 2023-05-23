<?php

namespace ApiAlarmManager;

interface RemoteFileHandler
{
    /**
     * Connect to the remote server.
     * 
     * @return bool
     */
    public function connect():bool;

    /**
     * Copy a remote file to a local file.
     * 
     * @return bool
     */
    public function copy(string $remoteFile, string $localFile):bool;

    /**
     * List remote files in a directory.
     * 
     * @return string[]|false
     */
    public function list(string $path);
}
