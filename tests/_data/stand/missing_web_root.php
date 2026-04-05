<?php

return [
    'services' => [],
    'envFiles' => [
        __DIR__ . '/env/test.env',
    ],
    'appConfig' => [
        'projectRoot' => __DIR__ . '/project',
        'webRoot' => __DIR__ . '/project/web-missing',
        'entryScript' => 'index-test.php',
    ],
];
