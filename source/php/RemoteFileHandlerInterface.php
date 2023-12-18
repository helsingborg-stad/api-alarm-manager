<?php

namespace ApiAlarmManager;

interface RemoteFileHandlerInterface
{
    /**
     * Connect to the remote server.
     * 
     * @return bool
     */
    public function connect(): bool;

    /**
     * Copy a remote file to a local file.
     * 
     * @return bool
     */
    public function copy(string $remoteFile, string $localFile): bool;

    /**
     * List remote files in a directory on remote.
     * 
     * @return string[]|false
     */
    public function list(string $path);

    /**
     * Check if a file exists on remote.
     * 
     * @return bool
     */
    public function fileExists(string $path): bool;

    /**
     * Create a directory on remote.
     * 
     * @return bool
     */
    public function mkdir(string $path): bool;

    /**
     * Move a file on remote.
     * 
     * @return bool
     */
    public function moveFile(string $source, string $destination): bool;
}
