<?php

namespace Illusiard\Yii2Testkit\TestStand;

use InvalidArgumentException;

class StandConfig
{
    private const REQUIRED_KEYS = [
        'services',
        'envFiles',
        'appConfig',
    ];

    private const REQUIRED_APP_CONFIG_KEYS = [
        'projectRoot',
        'webRoot',
        'entryScript',
    ];

    private array $services;
    private array $envFiles;
    private array $appConfig;

    private function __construct(array $services, array $envFiles, array $appConfig)
    {
        $this->services = $services;
        $this->envFiles = $envFiles;
        $this->appConfig = $appConfig;
    }

    public static function load(string $path): self
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException('Test stand config file not found: ' . $path);
        }

        $config = require $path;

        if (!is_array($config)) {
            throw new InvalidArgumentException('Test stand config must return array: ' . $path);
        }

        self::assertRequiredKeys($config, self::REQUIRED_KEYS, 'Test stand config');

        return new self(
            self::validateServices($config['services']),
            self::validateEnvFiles($config['envFiles']),
            self::validateAppConfig($config['appConfig'])
        );
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function getEnvFiles(): array
    {
        return $this->envFiles;
    }

    public function getAppConfig(): array
    {
        return $this->appConfig;
    }

    public function getProjectRoot(): string
    {
        return $this->appConfig['projectRoot'];
    }

    public function getWebRoot(): string
    {
        return $this->appConfig['webRoot'];
    }

    public function getEntryScript(): string
    {
        return $this->appConfig['entryScript'];
    }

    private static function validateServices(mixed $services): array
    {
        if (!is_array($services)) {
            throw new InvalidArgumentException('Test stand config key "services" must be an array.');
        }

        $supportedServices = [
            TestServices::MYSQL,
            TestServices::POSTGRESQL,
            TestServices::REDIS,
            TestServices::RABBITMQ,
        ];

        $validatedServices = [];

        foreach ($services as $service) {
            if (!is_string($service) || $service === '') {
                throw new InvalidArgumentException('Each test stand service must be a non-empty string.');
            }

            if (!in_array($service, $supportedServices, true)) {
                throw new InvalidArgumentException('Unsupported test stand service: ' . $service);
            }

            $validatedServices[] = $service;
        }

        return array_values(array_unique($validatedServices));
    }

    private static function validateEnvFiles(mixed $envFiles): array
    {
        if (!is_array($envFiles)) {
            throw new InvalidArgumentException('Test stand config key "envFiles" must be an array.');
        }

        $validatedEnvFiles = [];

        foreach ($envFiles as $envFile) {
            if (!is_string($envFile) || $envFile === '') {
                throw new InvalidArgumentException('Each env file path must be a non-empty string.');
            }

            if (!is_file($envFile)) {
                throw new InvalidArgumentException('Env file not found: ' . $envFile);
            }

            $validatedEnvFiles[] = self::normalizePath($envFile);
        }

        return $validatedEnvFiles;
    }

    private static function validateAppConfig(mixed $appConfig): array
    {
        if (!is_array($appConfig)) {
            throw new InvalidArgumentException('Test stand config key "appConfig" must be an array.');
        }

        self::assertRequiredKeys($appConfig, self::REQUIRED_APP_CONFIG_KEYS, 'Test stand appConfig');

        $projectRoot = self::validateDirectoryPath($appConfig['projectRoot'], 'projectRoot');
        $webRoot = self::validateDirectoryPath($appConfig['webRoot'], 'webRoot');
        $entryScript = self::validateString($appConfig['entryScript'], 'entryScript');

        return [
            'projectRoot' => $projectRoot,
            'webRoot' => $webRoot,
            'entryScript' => $entryScript,
        ];
    }

    private static function assertRequiredKeys(array $config, array $requiredKeys, string $scope): void
    {
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys !== []) {
            throw new InvalidArgumentException(
                sprintf('%s is missing required keys: %s', $scope, implode(', ', $missingKeys))
            );
        }
    }

    private static function validateDirectoryPath(mixed $value, string $key): string
    {
        $path = self::validateString($value, $key);

        if (!is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Test stand appConfig key "%s" directory not found: %s', $key, $path));
        }

        return self::normalizePath($path);
    }

    private static function validateString(mixed $value, string $key): string
    {
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException(
                sprintf('Test stand appConfig key "%s" must be a non-empty string.', $key)
            );
        }

        return $value;
    }

    private static function normalizePath(string $path): string
    {
        $resolvedPath = realpath($path);

        if ($resolvedPath === false) {
            return $path;
        }

        return $resolvedPath;
    }
}
