<?php

use Illusiard\Yii2Testkit\TestStand\TestServices;

return [
    'services' => [
        TestServices::MYSQL,
    ],
    'envFiles' => [
        dirname(__DIR__) . '/_env/test.env',
    ],
    'appConfig' => [
        'projectRoot' => dirname(__DIR__, 2),
        'webRoot' => dirname(__DIR__) . '/_data/stand/project/web',
        'entryScript' => 'index-test.php',
    ],
];
