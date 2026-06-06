<?php

namespace Illusiard\Yii2Testkit\TestStand;

final class TestServices
{
    public const string MYSQL = 'mysql';
    public const string POSTGRESQL = 'postgresql';
    public const string REDIS = 'redis';
    public const string RABBITMQ = 'rabbitmq';

    private function __construct()
    {
    }
}
