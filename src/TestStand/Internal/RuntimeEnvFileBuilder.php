<?php

namespace Illusiard\Yii2Testkit\TestStand\Internal;

use Illusiard\Yii2Testkit\TestStand\StandConfig;
use RuntimeException;

class RuntimeEnvFileBuilder
{
    public function build(StandConfig $config): string
    {
        $targetFile = $this->buildTargetFile($config);
        $contents = [];

        foreach ($config->getEnvFiles() as $envFile) {
            $content = file_get_contents($envFile);

            if ($content === false) {
                throw new RuntimeException('Unable to read env file: ' . $envFile);
            }

            $contents[] = rtrim($content);
        }

        $payload = implode(PHP_EOL, array_filter($contents, static fn (string $content): bool => $content !== ''));

        if ($payload !== '') {
            $payload .= PHP_EOL;
        }

        if (file_put_contents($targetFile, $payload) === false) {
            throw new RuntimeException('Unable to write runtime env file: ' . $targetFile);
        }

        return $targetFile;
    }

    private function buildTargetFile(StandConfig $config): string
    {
        return sys_get_temp_dir() . '/yii2-testkit-' . substr(md5($config->getProjectRoot()), 0, 12) . '.env';
    }
}
