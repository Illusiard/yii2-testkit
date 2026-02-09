<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\Testing\YiiTestCase;
use PHPUnit\Framework\TestCase;

class YiiTestCaseTest extends TestCase
{
    public function testLifecycle(): void
    {
        $case = new YiiTestCaseStub();
        $case->publicSetUp();

        $this->assertNotNull(\Yii::$app);

        $case->publicTearDown();

        $this->assertNull(\Yii::$app);

        if (class_exists(\yii\di\Container::class)) {
            $this->assertInstanceOf(\yii\di\Container::class, \Yii::$container);
        }
    }
}

class YiiTestCaseStub extends YiiTestCase
{
    protected function appConfig(): array
    {
        $basePath = __DIR__ . '/../_data';
        $vendorPath = $basePath . '/vendor';

        if (!is_dir($vendorPath)) {
            mkdir($vendorPath, 0777, true);
        }

        return [
            'id' => 'test-app',
            'basePath' => $basePath,
            'vendorPath' => $vendorPath,
        ];
    }

    public function publicSetUp(): void
    {
        $this->setUp();
    }

    public function publicTearDown(): void
    {
        $this->tearDown();
    }
}
