<?php

namespace tests\unit;

use Illusiard\Yii2Testkit\Config\ConfigResolver;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigResolverTest extends TestCase
{
    public function testLoadPhpReturnsArray(): void
    {
        $resolver = new ConfigResolver();
        $config = $resolver->loadPhp(__DIR__ . '/../_data/config.php');

        $this->assertIsArray($config);
        $this->assertSame('test-app', $config['id']);
    }

    public function testLoadPhpThrowsWhenFileMissing(): void
    {
        $resolver = new ConfigResolver();

        $this->expectException(InvalidArgumentException::class);
        $resolver->loadPhp(__DIR__ . '/../_data/missing.php');
    }

    public function testLoadPhpThrowsWhenNotArray(): void
    {
        $resolver = new ConfigResolver();

        $this->expectException(InvalidArgumentException::class);
        $resolver->loadPhp(__DIR__ . '/../_data/not_array.php');
    }
}
