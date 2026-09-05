<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class InstallWithAuthTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function installSource(): string
    {
        return file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');
    }

    public function test_install_accepts_a_with_auth_flag(): void
    {
        $this->assertStringContainsString('--with-auth', $this->installSource());
    }

    public function test_with_auth_runs_the_auth_export(): void
    {
        $source = $this->installSource();

        $this->assertMatchesRegularExpression(
            "/option\('with-auth'\)\s*\)\s*\{\s*TablarPreset::exportAuth\(\);/",
            $source
        );
    }

    public function test_export_auth_is_only_suggested_when_the_flag_is_absent(): void
    {
        $source = $this->installSource();

        $this->assertStringContainsString('Next: php artisan tablar:export-auth', $source);
        $this->assertStringContainsString('Next: php artisan migrate', $source);
    }

    public function test_auth_routes_are_only_appended_once(): void
    {
        $preset = file_get_contents(__DIR__.'/../../src/TablarPreset.php');

        $this->assertStringContainsString('str_contains(file_get_contents($webRoutes)', $preset);
    }
}
