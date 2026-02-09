<?php

namespace Illusiard\Yii2Testkit\App;

use yii\base\InvalidConfigException;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;

class AppFactory
{
    /**
     * @param array $config
     * @return ConsoleApplication
     * @throws InvalidConfigException
     */
    public function createConsoleApp(array $config): ConsoleApplication
    {
        $this->ensureEnv();

        return new ConsoleApplication($config);
    }

    /**
     * @param array $config
     * @return WebApplication
     * @throws InvalidConfigException
     */
    public function createWebApp(array $config): WebApplication
    {
        $this->ensureEnv();

        return new WebApplication($config);
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
