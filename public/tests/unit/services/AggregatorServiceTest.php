<?php

namespace Unit\services;

use app\services\AggregatorService;
use Codeception\Test\Unit;
use TypeError;
use \UnitTester;
use Yii;

class AggregatorServiceTest extends Unit
{

    protected UnitTester $tester;
    protected AggregatorService $aggregatorService;
    protected static string $fileName = 'aggregator.test.log';

    protected function _before()
    {
        $this->aggregatorService = new AggregatorService(
            Yii::$app->queue,
            Yii::$app->db,
        );
    }

    public function testLogFail()
    {
        $str = '';
        self::assertFalse($this->aggregatorService->log($str, static::$fileName));
    }

    public function testLogSuccess()
    {
        $str = 'some-test-data';
        self::assertNotFalse($this->aggregatorService->log($str, static::$fileName));
    }

    public function testPagesCreateFail()
    {
        $this->tester->expectThrowable(TypeError::class, function () {
            $this->aggregatorService->createPages([
                'status' => '123',
                'total' => 2,
                'page' => 'asdasd',
                'perPage' => 4,
                'lastPage' => 5
            ]);
        });
    }

    public function testPagesCreateSuccess()
    {
        $this->aggregatorService->createPages([
            'status' => 5,
            'total' => 2,
            'page' => 123,
            'perPage' => 4,
            'lastPage' => 5
        ]);
    }

    public function testCreatePageContentSuccess()
    {
        $content = $this->aggregatorService->createPageContent([
            [
                'id' => 123,
                'companyName' => 'test name',
                'subdomain' => 'test domain',
            ]
        ]);

        self::assertNotEmpty($content->data);
    }

    public function testCreatePageContentFail()
    {
        $this->tester->expectThrowable(TypeError::class, function () {
            $this->aggregatorService->createPageContent([
                [
                    'id' => 'test',
                    'companyName' => 123,
                    'subdomain' => 123,
                ]
            ]);
        });
    }
}
