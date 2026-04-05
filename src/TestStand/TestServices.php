<?php

namespace Illusiard\Yii2Testkit\TestStand;

final class TestServices
{
    public const MYSQL = 'mysql';
    public const POSTGRESQL = 'postgresql';
    public const REDIS = 'redis';
    public const RABBITMQ = 'rabbitmq';

    private function __construct()
    {
    }
}
