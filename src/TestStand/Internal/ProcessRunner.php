<?php

namespace Illusiard\Yii2Testkit\TestStand\Internal;

use RuntimeException;

class ProcessRunner
{
    public function run(array $command, array $env = []): string
    {
        $descriptorSpec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, null, $this->buildEnvironment($env));

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start command: ' . $this->stringifyCommand($command));
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        $output = $this->combineOutput($stdout, $stderr);

        if ($exitCode !== 0) {
            $message = sprintf(
                'Command failed with exit code %d: %s',
                $exitCode,
                $this->stringifyCommand($command)
            );

            if ($output !== '') {
                $message .= PHP_EOL . $output;
            }

            throw new RuntimeException($message);
        }

        return $output;
    }

    private function buildEnvironment(array $env): array
    {
        $currentEnvironment = getenv();

        if (!is_array($currentEnvironment)) {
            $currentEnvironment = [];
        }

        return array_merge($currentEnvironment, $_ENV, $env);
    }

    private function combineOutput(string|false $stdout, string|false $stderr): string
    {
        $parts = [];

        if (is_string($stdout) && $stdout !== '') {
            $parts[] = rtrim($stdout);
        }

        if (is_string($stderr) && $stderr !== '') {
            $parts[] = rtrim($stderr);
        }

        return implode(PHP_EOL, $parts);
    }

    private function stringifyCommand(array $command): string
    {
        $escapedCommand = array_map(
            static fn (string $part): string => escapeshellarg($part),
            $command
        );

        return implode(' ', $escapedCommand);
    }
}
