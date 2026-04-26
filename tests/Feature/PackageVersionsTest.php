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
}
