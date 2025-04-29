<?php

namespace ApiAlarmManager\Api;

use AcfService\Implementations\FakeAcfService;
use ApiAlarmManager\Api\FireDangerLevels;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_REST_Request;
use WP_Term;
use WpService\Implementations\FakeWpService;

use function PHPUnit\Framework\assertEquals;

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

    public function testWithSingleFilterValue()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);
        $wpRequest->method('get_param')->willReturn('Klippan');

        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(1, $result['places']);

        $items =  array_slice($result['places'], 0, 1);
        $this->assertEquals('Klippan', $items[0]['place']);
    }
    public function testWithSingleFilterValueCaseInsensitive()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);
        $wpRequest->method('get_param')->willReturn('klippan');

        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(1, $result['places']);

        $items =  array_slice($result['places'], 0, 1);
        $this->assertEquals('Klippan', $items[0]['place']);
    }
    public function testMultipleFilterValues()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);
        $wpRequest->method('get_param')->willReturn('Klippan,Helsingborg');

        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(2, $result['places']);

        $items =  array_slice($result['places'], 0, 2);
        $this->assertEquals('Helsingborg', $items[0]['place']);
        $this->assertEquals('Klippan', $items[1]['place']);
    }
    public function testWithoutFilterValues()
    {
        $wpRequest = $this->createMock(WP_REST_Request::class);

        $result = $this->service->getFireDangerLevels($wpRequest);

        $this->assertCount(3, $result['places']);
    }
}
