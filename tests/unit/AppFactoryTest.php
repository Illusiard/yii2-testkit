<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\App\AppFactory;
use PHPUnit\Framework\TestCase;

class AppFactoryTest extends TestCase
{
    public function testCreateConsoleApp(): void
    {
        $basePath = __DIR__ . '/../_data';
        $vendorPath = $basePath . '/vendor';

        if (!is_dir($vendorPath)) {
            mkdir($vendorPath, 0777, true);
        }

        $factory = new AppFactory();
        $app = $factory->createConsoleApp([
            'id' => 'test-app',
            'basePath' => $basePath,
            'vendorPath' => $vendorPath,
        ]);

        $this->assertInstanceOf(\yii\console\Application::class, $app);
        $this->assertSame($app, \Yii::$app);

        \Yii::$app = null;
    }
}
