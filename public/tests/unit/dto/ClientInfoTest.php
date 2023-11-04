<?php

namespace Unit\dto;

use app\models\dto\ClientInfo;
use Codeception\Test\Unit;
use \UnitTester;
use Yii;

class ClientInfoTest extends Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    public function testIfCanAccessClientInfoAsArray()
    {
        $info = new ClientInfo();
        $info->bitrixClient = 1;
        self::assertNotEmpty($info['bitrixClient']);
    }

    public function testParseBitrixClientIsString()
    {
        $info = new ClientInfo();
        $client = '4213';
        self::assertEquals(4213, $info::parseBitrixClient($client));
    }

    public function testParseBitrixClientIsUrl()
    {
        $info = new ClientInfo();
        $client = 'https://'. Yii::$app->params['bitrix']['domain'] .'/crm/deal/4213';
        self::assertEquals(4213, $info::parseBitrixClient($client));
    }

    public function testParseBitrixClientIsInt()
    {
        $info = new ClientInfo();
        $client = 4213;
        self::assertEquals(4213, $info::parseBitrixClient($client));
    }
}
