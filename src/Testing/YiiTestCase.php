<?php

namespace Illusiard\Yii2Testkit\Testing;

use Illusiard\Yii2Testkit\App\AppFactory;
use PHPUnit\Framework\TestCase;

class YiiTestCase extends TestCase
{
    protected function appType(): string
    {
        return 'console';
    }

    protected function appConfig(): array
    {
        return [
            'id' => 'yii2-testkit',
            'basePath' => dirname(__DIR__, 2),
            'vendorPath' => dirname(__DIR__, 3) . '/vendor',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $factory = new AppFactory();
        $type = $this->appType();
        $config = $this->appConfig();

        if ($type === 'web') {
            $factory->createWebApp($config);
            return;
        }

        $factory->createConsoleApp($config);
    }

    protected function tearDown(): void
    {
        if (class_exists('Yii', false)) {
            \Yii::$app = null;

            if (class_exists(\yii\di\Container::class)) {
                \Yii::$container = new \yii\di\Container();
            }
        }

        parent::tearDown();
    }
}
