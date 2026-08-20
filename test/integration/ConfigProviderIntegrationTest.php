<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webware\Core\ConfigProvider;

#[CoversClass(ConfigProvider::class)]
final class ConfigProviderIntegrationTest extends TestCase
{
    #[Test]
    public function configProviderReturnsDependencies(): void
    {
        $config = (new ConfigProvider())();

        self::assertArrayHasKey('dependencies', $config);
    }
}
