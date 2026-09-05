<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class MigrateHintOrderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_install_points_at_export_auth_before_migrate(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        preg_match("/else \{(.+?)\}/s", $source, $branch);
        $this->assertNotEmpty($branch, 'Expected an else branch for the plain install.');

        $exportAuth = strpos($branch[1], 'php artisan tablar:export-auth');
        $migrate = strpos($branch[1], 'php artisan migrate');

        $this->assertNotFalse($exportAuth);
        $this->assertNotFalse($migrate);
        $this->assertLessThan($migrate, $exportAuth, 'export-auth publishes the migrations, so it must be suggested first.');
    }

    public function test_export_auth_tells_you_to_migrate(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarExportAuthCommand.php');

        $this->assertStringContainsString('php artisan migrate', $source);
    }

    public function test_migrations_ship_with_the_auth_scaffolding(): void
    {
        $this->assertFileExists(__DIR__.'/../../src/stubs/migrations');
        $this->assertNotEmpty(glob(__DIR__.'/../../src/stubs/migrations/*.php'));
    }
}
