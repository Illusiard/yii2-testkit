<?php

return [
    'services' => [],
    'envFiles' => [
        __DIR__ . '/env/test.env',
    ],
    'appConfig' => [
        'projectRoot' => __DIR__ . '/project-missing',
        'webRoot' => __DIR__ . '/project/web',
        'entryScript' => 'index-test.php',
    ],
];
