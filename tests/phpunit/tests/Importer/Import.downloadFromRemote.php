<?php

namespace ApiAlarmManager\Tests\Importer;

use ApiAlarmManager\Importer;
use ApiAlarmManager\RemoteFileHandlerInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use tad\FunctionMocker\FunctionMocker;
use WP_Mock;
use WP_Mock\Tools\TestCase;

class ImporterDownloadFromRemoteTests extends TestCase {

    public function setUp(): void {
        parent::setUp();
        WP_Mock::userFunction('trailingslashit', ['returnUsing' => fn($in) => $in]);
        WP_Mock::userFunction('sanitize_text_field', ['returnUsing' => fn($in) => $in]);
    }
    
    public function testFilesAreNotArchivedOnRemoteIfProductionFlagIsNotSet() {
        // Given
        FunctionMocker::replace('defined', true);
        FunctionMocker::replace('constant', 'test');
        $remoteFileHandler = $this->getRemoteFileHandlerMock();
        $remoteFileHandler->shouldReceive('moveFile')->times(0);
        $importer = new Importer($remoteFileHandler, "", "");
        // When
        $importer->downloadFromRemote("");
        // Then
        $this->assertConditionsMet();
    }

    public function testFilesAreArchivedOnRemoteIfProductionFlagIsSet() {
        // Given
        define('API_ALARM_MANAGER_ARCHIVE_ALARMS_ON_REMOTE', true);
        $remoteFileHandler = $this->getRemoteFileHandlerMock();
        $remoteFileHandler->shouldReceive('moveFile')->times(1);
        $importer = new Importer($remoteFileHandler, "", "");
        
        // When
        $importer->downloadFromRemote("");
        
        // Then
        $this->assertConditionsMet();
    }

    private function getRemoteFileHandlerMock():LegacyMockInterface|RemoteFileHandlerInterface {
        $mock = Mockery::mock(RemoteFileHandlerInterface::class);
        $mock->shouldReceive('connect');
        $mock->shouldReceive('list')->andReturn(['file1']);
        $mock->shouldReceive('copy')->andReturn(true);
        $mock->shouldReceive('fileExists')->andReturn(true);
        $mock->shouldReceive('mkdir')->times(0);

        return $mock;
    }
}