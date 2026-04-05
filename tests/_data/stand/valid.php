<?php

use Illusiard\Yii2Testkit\TestStand\TestServices;

return [
    'services' => [
        TestServices::MYSQL,
        TestServices::REDIS,
    ],
    'envFiles' => [
        __DIR__ . '/env/test.env',
    ],
    'appConfig' => [
        'projectRoot' => __DIR__ . '/project',
        'webRoot' => __DIR__ . '/project/web',
        'entryScript' => 'index-test.php',
    ],
];
