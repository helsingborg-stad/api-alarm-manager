<?php

namespace ApiAlarmManager\Api;

use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testStationCollectionExcludesRepeatedStationIds(): void
    {
        $stations = array(
            array('id' => 10, 'station_id' => '264-1600', 'title' => 'Berga deltid 264-1600'),
            array('id' => 9, 'station_id' => '264-1600', 'title' => 'Berga deltid 264-1600'),
            array('id' => 8, 'station_id' => '264-1200', 'title' => 'Helsingborg station City 264-1200'),
            array('id' => 7, 'station_id' => '264-1200', 'title' => 'Helsingborg station City 264-1200'),
            array('id' => 6, 'station_id' => '264-1000', 'title' => 'Helsingborg station Bårslöv 264-1000'),
        );

        $result = Filter::uniqueStations($stations);

        $this->assertSame(array(10, 8, 6), array_column($result, 'id'));
    }

    public function testStationCollectionFallsBackToNormalizedTitle(): void
    {
        $stations = array(
            array('id' => 2, 'title' => array('plain_text' => 'Station  One')),
            array('id' => 1, 'title' => array('plain_text' => ' station one ')),
        );

        $result = Filter::uniqueStations($stations);

        $this->assertSame(array(2), array_column($result, 'id'));
    }
}
