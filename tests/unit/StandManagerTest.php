<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\TestStand\Internal\ProcessRunner;
use Illusiard\Yii2Testkit\TestStand\StandConfig;
use Illusiard\Yii2Testkit\TestStand\StandManager;
use Illusiard\Yii2Testkit\TestStand\TestServices;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class StandManagerTest extends TestCase
{
    private string $projectRoot;
    private string $runtimeEnvFile;

    protected function setUp(): void
    {
        $this->projectRoot = (string) realpath(__DIR__ . '/../_data/stand/project');
        $this->runtimeEnvFile = sys_get_temp_dir() . '/yii2-testkit-' . substr(md5($this->projectRoot), 0, 12) . '.env';

        $this->cleanupLogDirectory();

        if (is_file($this->runtimeEnvFile)) {
            unlink($this->runtimeEnvFile);
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupLogDirectory();

        if (is_file($this->runtimeEnvFile)) {
            unlink($this->runtimeEnvFile);
        }
    }

    public function testUpRunsBuiltInAndConfiguredServices(): void
    {
        $config = StandConfig::load(__DIR__ . '/../_data/stand/valid.php');
        $manager = new StandManager();
        $runner = new StandManagerProcessRunnerStub();

        $this->injectProcessRunner($manager, $runner);

        $manager->up($config, true);

        $this->assertCount(1, $runner->calls);
        $command = $runner->calls[0]['command'];
        $environment = $runner->calls[0]['env'];

        $this->assertContains('up', $command);
        $this->assertContains('--build', $command);
        $this->assertSame(
            ['nginx', 'php-fpm', TestServices::MYSQL, TestServices::REDIS],
            array_slice($command, -4)
        );
        $this->assertSame($this->projectRoot, $environment['TESTKIT_PROJECT_ROOT']);
        $this->assertSame($this->projectRoot . '/web', $environment['TESTKIT_WEB_ROOT']);
        $this->assertSame('index-test.php', $environment['TESTKIT_ENTRY_SCRIPT']);
        $this->assertSame($this->runtimeEnvFile, $environment['TESTKIT_RUNTIME_ENV_FILE']);
        $this->assertFileExists($this->runtimeEnvFile);
    }

    public function testDownAddsVolumesFlagWhenRequested(): void
    {
        $config = StandConfig::load(__DIR__ . '/../_data/stand/valid.php');
        $manager = new StandManager();
        $runner = new StandManagerProcessRunnerStub();

        $this->injectProcessRunner($manager, $runner);

        $manager->down($config, true);

        $this->assertCount(1, $runner->calls);
        $this->assertContains('down', $runner->calls[0]['command']);
        $this->assertContains('--volumes', $runner->calls[0]['command']);
    }

    public function testLogsWritesFilesToFixedDirectory(): void
    {
        $config = StandConfig::load(__DIR__ . '/../_data/stand/valid.php');
        $manager = new StandManager();
        $runner = new StandManagerProcessRunnerStub();
        $runner->responses = [
            'compose logs',
            'services logs',
        ];

        $this->injectProcessRunner($manager, $runner);

        $manager->logs($config);

        $logDirectory = $this->projectRoot . '/tests/_output/testkit';

        $this->assertFileExists($logDirectory . '/docker-compose.log');
        $this->assertFileExists($logDirectory . '/services.log');
        $this->assertSame('compose logs', file_get_contents($logDirectory . '/docker-compose.log'));
        $this->assertSame('services logs', file_get_contents($logDirectory . '/services.log'));
        $this->assertCount(2, $runner->calls);
        $this->assertContains('logs', $runner->calls[0]['command']);
        $this->assertContains('logs', $runner->calls[1]['command']);
    }

    private function injectProcessRunner(StandManager $manager, ProcessRunner $runner): void
    {
        $reflection = new ReflectionClass($manager);
        $property = $reflection->getProperty('processRunner');
        $property->setAccessible(true);
        $property->setValue($manager, $runner);
    }

    private function cleanupLogDirectory(): void
    {
        $files = [
            $this->projectRoot . '/tests/_output/testkit/docker-compose.log',
            $this->projectRoot . '/tests/_output/testkit/services.log',
            $this->projectRoot . '/tests/_output/testkit',
            $this->projectRoot . '/tests/_output',
            $this->projectRoot . '/tests',
        ];

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                continue;
            }

            if (is_dir($file)) {
                rmdir($file);
            }
        }
    }
}

class StandManagerProcessRunnerStub extends ProcessRunner
{
    public array $calls = [];
    public array $responses = [];

    public function run(array $command, array $env = []): string
    {
        $this->calls[] = [
            'command' => $command,
            'env' => $env,
        ];

        if ($this->responses === []) {
            return '';
        }

        return array_shift($this->responses);
    }
}
