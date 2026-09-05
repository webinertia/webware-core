<?php

declare(strict_types=1);

namespace WebwareTestIntegration\Core\Extension;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use WebwareTestIntegration\Core\FixtureLoader\FixtureLoaderInterface;
use WebwareTestIntegration\Core\FixtureLoader\MysqlFixtureLoader;
use WebwareTestIntegration\Core\FixtureLoader\PgsqlFixtureLoader;
use WebwareTestIntegration\Core\FixtureLoader\SqliteFixtureLoader;

use function getenv;

final class ListenerExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters,
    ): void {
        $fixtureLoaders = $this->fixtureLoaders();

        $facade->registerSubscribers(
            new IntegrationTestStartedListener($fixtureLoaders),
            new IntegrationTestStoppedListener($fixtureLoaders),
        );
    }

    /**
     * @return FixtureLoaderInterface[]
     */
    private function fixtureLoaders(): array
    {
        $loaders = [];

        if (getenv(name: 'TESTS_PHPDB_ADAPTER_MYSQL')) {
            $loaders[] = new MysqlFixtureLoader();
        }

        if (getenv(name: 'TESTS_PHPDB_ADAPTER_PGSQL')) {
            $loaders[] = new PgsqlFixtureLoader();
        }

        if (getenv(name: 'TESTS_PHPDB_ADAPTER_SQLITE')) {
            $loaders[] = new SqliteFixtureLoader();
        }

        return $loaders;
    }
}
