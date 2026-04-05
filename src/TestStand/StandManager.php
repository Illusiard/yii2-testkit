<?php

namespace Illusiard\Yii2Testkit\TestStand;

use Illusiard\Yii2Testkit\TestStand\Internal\DockerComposeCommandBuilder;
use Illusiard\Yii2Testkit\TestStand\Internal\ProcessRunner;
use Illusiard\Yii2Testkit\TestStand\Internal\RuntimeEnvFileBuilder;
use RuntimeException;

class StandManager
{
    private DockerComposeCommandBuilder $commandBuilder;
    private ProcessRunner $processRunner;
    private RuntimeEnvFileBuilder $runtimeEnvFileBuilder;

    public function __construct()
    {
        $this->commandBuilder = new DockerComposeCommandBuilder();
        $this->processRunner = new ProcessRunner();
        $this->runtimeEnvFileBuilder = new RuntimeEnvFileBuilder();
    }

    public function up(StandConfig $config, bool $build = false): void
    {
        $environment = $this->prepareEnvironment($config);

        $this->processRunner->run(
            $this->commandBuilder->buildUpCommand($config, $build),
            $environment
        );
    }

    public function down(StandConfig $config, bool $withVolumes = false): void
    {
        $environment = $this->prepareEnvironment($config);

        $this->processRunner->run(
            $this->commandBuilder->buildDownCommand($config, $withVolumes),
            $environment
        );
    }

    public function logs(StandConfig $config): void
    {
        $environment = $this->prepareEnvironment($config);
        $logDirectory = $config->getProjectRoot() . '/tests/_output/testkit';

        $this->ensureDirectoryExists($logDirectory);

        $composeLogs = $this->processRunner->run(
            $this->commandBuilder->buildComposeLogsCommand($config),
            $environment
        );
        $servicesLogs = $this->processRunner->run(
            $this->commandBuilder->buildServicesLogsCommand($config),
            $environment
        );

        $this->writeLogFile($logDirectory . '/docker-compose.log', $composeLogs);
        $this->writeLogFile($logDirectory . '/services.log', $servicesLogs);
    }

    private function prepareEnvironment(StandConfig $config): array
    {
        $runtimeEnvFile = $this->runtimeEnvFileBuilder->build($config);

        return array_merge(
            $this->commandBuilder->buildEnvironment($config),
            [
                'TESTKIT_RUNTIME_ENV_FILE' => $runtimeEnvFile,
            ]
        );
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new RuntimeException('Unable to create directory: ' . $path);
        }
    }

    private function writeLogFile(string $path, string $contents): void
    {
        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException('Unable to write log file: ' . $path);
        }
    }
}
