<?php

namespace ApiAlarmManager\Api;

use AcfService\Implementations\FakeAcfService;
use ApiAlarmManager\Api\FireDangerLevels;
use PHPUnit\Framework\TestCase;
use WP_REST_Request;
use WP_Term;
use WpService\Implementations\FakeWpService;

class FireDangerLevelTest extends TestCase
{
    protected $service = null;

    protected function setUp(): void
    {
        $acfService = new FakeAcfService([
            'getField' => [
                [
                    'place' => 1,
                    'level' => '1'],
                [
                    'place' => 2,
                    'level' => '2'],
                [
                    'place' => 3,
                    'level' => '3']
                ]
            ]);

        $wpService = new FakeWpService([
            'getOption' => '1745564776',
            'getTerm'   =>  function ($id) {
                $term = new \WP_Term([]);

                switch ($id) {
                    case 1:
                        $term->name = "Helsingborg";
                        break;
                    case 2:
                        $term->name = "Klippan";
                        break;
                    case 3:
                        $term->name = "Ängelholm";
                        break;
                }
                return $term;
            },
            'addAction' => true
            ]);

            $this->service = new FireDangerLevels($wpService, $acfService);
    }

    public function testWithSinglePlaceFilterValue()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);

        $wpRequest->method('get_param')->willReturnCallback(function ($key) {
            switch ($key) {
                case 'place':
                    return '2';
                case 'level':
                    return '0';
                default:
                    return null;
            }
        });

        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(1, $result['places']);
        $this->assertEquals('Klippan', $result['places'][0]['place']);
    }
    public function testWithMultiplePlaceFilterValues()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);

        $wpRequest->method('get_param')->willReturnCallback(function ($key) {
            switch ($key) {
                case 'place':
                    return '2,1';
                case 'level':
                    return 0;
                default:
                    return null;
            }
        });
        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(2, $result['places']);
        $this->assertEquals('Helsingborg', $result['places'][0]['place']);
        $this->assertEquals('Klippan', $result['places'][1]['place']);
    }
    public function testWithoutFilterValues()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);

        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(3, $result['places']);
    }
    public function testWithSingleLevelFilterValue()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);

        $wpRequest->method('get_param')->willReturnCallback(function ($key) {
            switch ($key) {
                case 'level':
                    return '1';
                default:
                    return null;
            }
        });
        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(1, $result['places']);
    }
    public function testMultipleLevelFilterValues()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);

        $wpRequest->method('get_param')->willReturnCallback(function ($key) {
            switch ($key) {
                case 'level':
                    return '1,2';
                default:
                    return null;
            }
        });
        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(2, $result['places']);
        $this->assertEquals('Helsingborg', $result['places'][0]['place']);
        $this->assertEquals('Klippan', $result['places'][1]['place']);
    }
}
