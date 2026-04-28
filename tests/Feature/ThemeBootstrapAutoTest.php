<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the FOUC-safe theme bootstrap script in master.blade.php so it
 * supports the `auto` value (System) — which must follow
 * `prefers-color-scheme` and live-update on OS change.
 */
class ThemeBootstrapAutoTest extends TestCase
{
    private const MASTER = __DIR__.'/../../resources/views/master.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::MASTER);
    }

    public function test_bootstrap_resolves_auto_via_prefers_color_scheme(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('prefers-color-scheme: dark', $source);
        $this->assertMatchesRegularExpression(
            '/if\s*\(\s*value\s*===\s*[\'"]auto[\'"]/',
            $source,
            'Bootstrap must branch on the `auto` theme value.',
        );
    }

    public function test_bootstrap_listens_for_os_theme_changes(): void
    {
        $source = $this->source();

        $this->assertMatchesRegularExpression(
            '/media\.addEventListener\(\s*[\'"]change[\'"]/',
            $source,
            'Bootstrap must listen for prefers-color-scheme `change` events to live-update on OS toggle.',
        );
    }

    public function test_bootstrap_listens_for_custom_theme_change_event(): void
    {
        $source = $this->source();

        $this->assertStringContainsString("'tablar:theme-change'", $source);
        $this->assertMatchesRegularExpression(
            '/window\.addEventListener\(\s*[\'"]tablar:theme-change[\'"]/',
            $source,
        );
    }

    public function test_bootstrap_default_value_is_auto(): void
    {
        $source = $this->source();

        $this->assertMatchesRegularExpression(
            '/localStorage\.getItem\(\s*[\'"]tablar\.theme[\'"]\s*\)\s*\|\|\s*[\'"]auto[\'"]/',
            $source,
            'Unset preference must default to `auto`, not hardcoded light/dark.',
        );
    }

    public function test_bootstrap_runs_before_vite_directive(): void
    {
        $source = $this->source();

        $bootstrapPos = strpos($source, '(prefers-color-scheme: dark)');
        $vitePos = strpos($source, '@vite');

        $this->assertNotFalse($bootstrapPos);
        $this->assertNotFalse($vitePos);
        $this->assertLessThan($vitePos, $bootstrapPos, 'Bootstrap must run before Vite to avoid FOUC.');
    }
}
