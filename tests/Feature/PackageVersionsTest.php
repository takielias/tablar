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

    public function test_vite_plugin_static_copy_is_not_installed(): void
    {
        $this->assertArrayNotHasKey(
            'vite-plugin-static-copy',
            $this->packageArray(),
            'Vite emits the tabler icon fonts from the scss, so nothing needs copying.'
        );
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

    public function test_jquery_is_not_installed_by_default(): void
    {
        $this->assertArrayNotHasKey('jquery', $this->packageArray(), 'Nothing in Tablar uses jQuery.');
    }

    public function test_jquery_is_kept_when_the_app_already_has_it(): void
    {
        $reflection = new \ReflectionClass(TablarPreset::class);
        $method = $reflection->getMethod('updatePackageArray');
        $method->setAccessible(true);

        $merged = $method->invoke(null, ['jquery' => '^3.7.0']);

        $this->assertSame('^3.7.0', $merged['jquery'] ?? null, 'Re-installing must not strip an app dependency we no longer ship.');
    }

    public function test_first_install_drops_the_skeleton_tailwind_deps(): void
    {
        $reflection = new \ReflectionClass(TablarPreset::class);
        $first = $reflection->getProperty('firstInstall');
        $first->setAccessible(true);
        $first->setValue(null, true);

        $method = $reflection->getMethod('updatePackageArray');
        $method->setAccessible(true);
        $merged = $method->invoke(null, ['tailwindcss' => '^4.0.0', '@tailwindcss/vite' => '^4.0.0']);

        $first->setValue(null, false);

        $this->assertArrayNotHasKey('tailwindcss', $merged, 'Tablar is bootstrap based; the skeleton tailwind deps are dead weight.');
        $this->assertArrayNotHasKey('@tailwindcss/vite', $merged);
    }

    public function test_reinstall_keeps_tailwind_the_app_added(): void
    {
        $reflection = new \ReflectionClass(TablarPreset::class);
        $method = $reflection->getMethod('updatePackageArray');
        $method->setAccessible(true);

        $merged = $method->invoke(null, ['tailwindcss' => '^4.0.0']);

        $this->assertSame('^4.0.0', $merged['tailwindcss'] ?? null, 'Only a first install may strip it; after that it may be theirs.');
    }

    public function test_apexcharts_targets_v5(): void
    {
        $this->assertSame('^5.10.0', $this->packageArray()['apexcharts'] ?? null);
    }

    public function test_typed_js_targets_v3(): void
    {
        $this->assertSame('^3.0.0', $this->packageArray()['typed.js'] ?? null);
    }

    public function test_duplicate_select_libs_dropped(): void
    {
        $packages = $this->packageArray();

        $this->assertArrayNotHasKey('choices.js', $packages, 'Drop choices.js — tom-select is the canonical select replacement.');
        $this->assertArrayNotHasKey('select2', $packages, 'Drop select2 — jQuery dep, superseded by tom-select.');
        $this->assertArrayHasKey('tom-select', $packages, 'tom-select must remain as the canonical select replacement.');
    }

    public function test_no_choices_references_in_stubs(): void
    {
        $stubsDir = realpath(__DIR__.'/../../src/stubs');

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($stubsDir, \FilesystemIterator::SKIP_DOTS));

        $offenders = [];
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            if (! in_array($file->getExtension(), ['js', 'ts', 'php', 'blade', 'scss', 'css'], true)) {
                continue;
            }
            $contents = @file_get_contents($file->getPathname());
            if ($contents !== false && preg_match('/(choices\.js|select2)/i', $contents)) {
                $offenders[] = $file->getPathname();
            }
        }

        $this->assertEmpty(
            $offenders,
            'Stubs must not reference choices.js or select2: '.implode(', ', $offenders)
        );
    }
}
