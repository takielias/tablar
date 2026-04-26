<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TakiElias\Tablar\TablarPreset;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Asserts the npm package version constraints emitted by
 * TablarPreset::updatePackageArray() match the locked targets in
 * plan/revamp/current-versions.md.
 *
 * These are contract tests — they fail loudly if a version drifts.
 */
class PackageVersionsTest extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    /**
     * @return array<string, mixed>
     */
    private function packageArray(): array
    {
        $reflection = new \ReflectionClass(TablarPreset::class);
        $method = $reflection->getMethod('updatePackageArray');
        $method->setAccessible(true);

        return $method->invoke(null, []);
    }

    public function test_vite_constraint_targets_v8(): void
    {
        $packages = $this->packageArray();
        $this->assertArrayHasKey('vite', $packages);
        $this->assertSame('^8.0.0', $packages['vite']);
    }

    public function test_no_legacy_vite_majors_listed(): void
    {
        $packages = $this->packageArray();
        $vite = $packages['vite'] ?? '';
        foreach (['^5', '^6', '^7'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $vite, "Vite constraint must not contain {$forbidden}");
        }
    }

    public function test_laravel_vite_plugin_targets_v3(): void
    {
        $packages = $this->packageArray();
        $this->assertSame('^3.0.0', $packages['laravel-vite-plugin'] ?? null);
    }

    public function test_vite_plugin_static_copy_targets_v4(): void
    {
        $packages = $this->packageArray();
        $this->assertSame('^4.0.0', $packages['vite-plugin-static-copy'] ?? null);
    }

    public function test_tabler_icons_at_3_41(): void
    {
        $packages = $this->packageArray();
        $this->assertSame('^3.41.0', $packages['@tabler/icons'] ?? null);
        $this->assertSame('^3.41.0', $packages['@tabler/icons-webfont'] ?? null);
    }

    public function test_sass_embedded_present(): void
    {
        $packages = $this->packageArray();
        $this->assertArrayHasKey('sass-embedded', $packages);
        $this->assertSame('^1.99.0', $packages['sass-embedded']);
    }

    public function test_legacy_sass_keys_absent(): void
    {
        $packages = $this->packageArray();
        $this->assertArrayNotHasKey('sass', $packages, 'Replace `sass` with `sass-embedded` for Vite 8 compatibility.');
        $this->assertArrayNotHasKey('sass-loader', $packages, 'sass-loader is webpack-specific and unused under Vite.');
    }

    public function test_jquery_targets_v4(): void
    {
        $this->assertSame('^4.0.0', $this->packageArray()['jquery'] ?? null);
    }

    public function test_apexcharts_targets_v5(): void
    {
        $this->assertSame('^5.10.0', $this->packageArray()['apexcharts'] ?? null);
    }

    public function test_typed_js_targets_v3(): void
    {
        $this->assertSame('^3.0.0', $this->packageArray()['typed.js'] ?? null);
    }
}
