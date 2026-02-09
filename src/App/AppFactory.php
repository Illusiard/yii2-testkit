<?php

namespace Illusiard\Yii2Testkit\App;

class AppFactory
{
    public function createConsoleApp(array $config): \yii\console\Application
    {
        $this->ensureEnv();

        return new \yii\console\Application($config);
    }

    public function createWebApp(array $config): \yii\web\Application
    {
        $this->ensureEnv();

        return new \yii\web\Application($config);
    }

    private function ensureEnv(): void
    {
        if (!defined('YII_ENV')) {
            define('YII_ENV', 'test');
        }

        if (!defined('YII_DEBUG')) {
            define('YII_DEBUG', true);
        }
    }
}
