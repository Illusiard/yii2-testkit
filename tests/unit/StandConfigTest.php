<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\TestStand\StandConfig;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StandConfigTest extends TestCase
{
    public function testLoadReturnsValidatedConfig(): void
    {
        $config = StandConfig::load(__DIR__ . '/../_config/stand.php');

        $this->assertSame(['mysql'], $config->getServices());
        $this->assertSame([realpath(__DIR__ . '/../_env/test.env')], $config->getEnvFiles());
        $this->assertSame(realpath(dirname(__DIR__, 2)), $config->getProjectRoot());
        $this->assertSame(realpath(__DIR__ . '/../_data/stand/project/web'), $config->getWebRoot());
        $this->assertSame('index-test.php', $config->getEntryScript());
    }

    public function testLoadThrowsWhenFileMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Test stand config file not found');

        StandConfig::load(__DIR__ . '/../_data/stand/missing.php');
    }

    public function testLoadThrowsWhenConfigIsNotArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must return array');

        StandConfig::load(__DIR__ . '/../_data/not_array.php');
    }

    public function testLoadThrowsWhenRequiredKeysAreMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required keys');

        StandConfig::load(__DIR__ . '/../_data/stand/missing_keys.php');
    }

    public function testLoadThrowsWhenEnvFileMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Env file not found');

        StandConfig::load(__DIR__ . '/../_data/stand/missing_env.php');
    }

    public function testLoadThrowsWhenServiceIsUnsupported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported test stand service');

        StandConfig::load(__DIR__ . '/../_data/stand/unsupported_service.php');
    }

    public function testLoadThrowsWhenServicesHasInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('key "services" must be an array');

        StandConfig::load(__DIR__ . '/../_data/stand/invalid_services_type.php');
    }

    public function testLoadThrowsWhenEnvFilesHasInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('key "envFiles" must be an array');

        StandConfig::load(__DIR__ . '/../_data/stand/invalid_env_files_type.php');
    }

    public function testLoadThrowsWhenAppConfigHasInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('key "appConfig" must be an array');

        StandConfig::load(__DIR__ . '/../_data/stand/invalid_app_config_type.php');
    }

    public function testLoadThrowsWhenProjectRootMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('projectRoot');

        StandConfig::load(__DIR__ . '/../_data/stand/missing_project_root.php');
    }

    public function testLoadThrowsWhenWebRootMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('webRoot');

        StandConfig::load(__DIR__ . '/../_data/stand/missing_web_root.php');
    }
}
