<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\Testing\YiiTestCase;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Container;

class YiiTestCaseTest extends TestCase
{
    /**
     * @return void
     * @throws InvalidConfigException
     */
    public function testLifecycle(): void
    {
        $case = new YiiTestCaseStub('testLifecycle');
        $case->publicSetUp();

        $this->assertNotNull(Yii::$app);

        $case->publicTearDown();

        $this->assertNull(Yii::$app);

        if (class_exists(Container::class)) {
            $this->assertInstanceOf(Container::class, Yii::$container);
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

    /**
     * @return void
     * @throws InvalidConfigException
     */
    public function publicSetUp(): void
    {
        $this->setUp();
    }

    public function publicTearDown(): void
    {
        $this->tearDown();
    }
}
