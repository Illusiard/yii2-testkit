<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\TestStand\TestServices;
use PHPUnit\Framework\TestCase;

class TestServicesTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertSame('mysql', TestServices::MYSQL);
        $this->assertSame('postgresql', TestServices::POSTGRESQL);
        $this->assertSame('redis', TestServices::REDIS);
        $this->assertSame('rabbitmq', TestServices::RABBITMQ);
    }
}
