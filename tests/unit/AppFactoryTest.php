<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\App\AppFactory;
use PHPUnit\Framework\TestCase;
use yii\console\Application;

class AppFactoryTest extends TestCase
{
    public function testCreateConsoleApp(): void
    {
        $basePath = __DIR__ . '/../_data';
        $vendorPath = $basePath . '/vendor';

        if (!is_dir($vendorPath)) {
            mkdir($vendorPath, 0777, true);
        }

        $envDefined = defined('YII_ENV');
        $debugDefined = defined('YII_DEBUG');
        $envValue = $envDefined ? YII_ENV : null;
        $debugValue = $debugDefined ? YII_DEBUG : null;

        $factory = new AppFactory();
        $app = $factory->createConsoleApp([
            'id' => 'test-app',
            'basePath' => $basePath,
            'vendorPath' => $vendorPath,
        ]);

        $this->assertInstanceOf(Application::class, $app);
        $this->assertSame($app, \Yii::$app);

        $this->assertTrue(defined('YII_ENV'));
        $this->assertTrue(defined('YII_DEBUG'));

        if ($envDefined) {
            $this->assertSame($envValue, YII_ENV);
        } else {
            $this->assertSame('test', YII_ENV);
        }

        if ($debugDefined) {
            $this->assertSame($debugValue, YII_DEBUG);
        } else {
            $this->assertTrue(YII_DEBUG);
        }

        \Yii::$app = null;

        if (class_exists(\yii\di\Container::class)) {
            \Yii::$container = new \yii\di\Container();
        }
    }
}
