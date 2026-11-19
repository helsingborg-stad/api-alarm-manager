<?php

namespace ApiAlarmManager;

use ApiAlarmManager\RemoteFileHandlerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class ImporterTest extends TestCase
{
    #[TestDox("imports do not archive remote files")]
    public function testImportsDoNotArchiveRemoteFiles()
    {
        $remoteFileHandler = $this->getRemoteFileHandlerMock();
        $remoteFileHandler->expects($this->never())->method('moveFile');

        $importer = new Importer($remoteFileHandler, "", "", $this->getWpService());

        $importer->downloadFromRemote("");
    }

    #[TestDox("daily archive moves remote files by default")]
    public function testDailyArchiveMovesRemoteFilesByDefault()
    {
        $remoteFileHandler = $this->getRemoteFileHandlerMock();
        $remoteFileHandler->expects($this->once())->method('moveFile');

        $importer = new Importer($remoteFileHandler, "", "", $this->getWpService());

        $importer->archiveRemoteFiles();
    }

    private function getWpService(): WpService
    {
        return new FakeWpService([
            'trailingslashit'   => function ($path) {
                return rtrim($path, '/');
            },
            'sanitizeTextField' => function ($text) {
                return $text;
            },
        ]);
    }

    private function getRemoteFileHandlerMock(): MockObject|RemoteFileHandlerInterface
    {
        $mock = $this->createMock(RemoteFileHandlerInterface::class);
        $mock->method('connect')->willReturn(true);
        $mock->method('list')->willReturn(['file1']);
        $mock->method('copy')->willReturn(true);
        $mock->method('fileExists')->willReturn(true);
        $mock->method('mkdir')->willReturn(true);

        return $mock;
    }
}
