<?php

namespace Illusiard\Yii2Testkit\Config;

use InvalidArgumentException;

class ConfigResolver
{
    public function loadPhp(string $path): array
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException('Config file not found: ' . $path);
        }

        $config = require $path;

        if (!is_array($config)) {
            throw new InvalidArgumentException('Config file must return array: ' . $path);
        }

        return $config;
    }
}
