<?php

namespace Illusiard\Yii2Testkit\TestStand\Internal;

use Illusiard\Yii2Testkit\TestStand\StandConfig;
use RuntimeException;

class DockerComposeCommandBuilder
{
    private string $composeFile;

    public function __construct(?string $composeFile = null)
    {
        $this->composeFile = $composeFile ?: dirname(__DIR__, 3) . '/docker/compose.yml';
    }

    public function buildEnvironment(StandConfig $config): array
    {
        return [
            'TESTKIT_ENTRY_SCRIPT' => $config->getEntryScript(),
            'TESTKIT_PROJECT_ROOT' => $config->getProjectRoot(),
            'TESTKIT_WEB_ROOT' => $config->getWebRoot(),
        ];
    }

    public function buildUpCommand(StandConfig $config, bool $build): array
    {
        $command = $this->createBaseCommand($config);
        $command[] = 'up';
        $command[] = '-d';

        if ($build) {
            $command[] = '--build';
        }

        foreach ($this->buildServices($config) as $service) {
            $command[] = $service;
        }

        return $command;
    }

    public function buildDownCommand(StandConfig $config, bool $withVolumes): array
    {
        $command = $this->createBaseCommand($config);
        $command[] = 'down';

        if ($withVolumes) {
            $command[] = '--volumes';
        }

        return $command;
    }

    public function buildComposeLogsCommand(StandConfig $config): array
    {
        $command = $this->createBaseCommand($config);
        $command[] = 'logs';
        $command[] = '--no-color';

        return $command;
    }

    public function buildServicesLogsCommand(StandConfig $config): array
    {
        $command = $this->createBaseCommand($config);
        $command[] = 'logs';
        $command[] = '--no-color';

        foreach ($this->buildServices($config) as $service) {
            $command[] = $service;
        }

        return $command;
    }

    private function createBaseCommand(StandConfig $config): array
    {
        if (!is_file($this->composeFile)) {
            throw new RuntimeException('Built-in docker compose file not found: ' . $this->composeFile);
        }

        return [
            'docker',
            'compose',
            '-p',
            $this->buildProjectName($config),
            '-f',
            $this->composeFile,
        ];
    }

    private function buildProjectName(StandConfig $config): string
    {
        $projectRoot = $config->getProjectRoot();
        $baseName = basename($projectRoot);
        $normalizedBaseName = preg_replace('/[^a-z0-9_-]/', '_', strtolower($baseName));

        if (!is_string($normalizedBaseName) || $normalizedBaseName === '') {
            $normalizedBaseName = 'project';
        }

        return sprintf('testkit_%s_%s', $normalizedBaseName, substr(md5($projectRoot), 0, 8));
    }

    private function buildServices(StandConfig $config): array
    {
        return array_merge(
            [
                'nginx',
                'php-fpm',
            ],
            $config->getServices()
        );
    }

}
