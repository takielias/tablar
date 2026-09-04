<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Asserts the vite.config.js stub shipped with TablarPreset is shaped for
 * Vite 8 (no legacy options, correct imports, expected static-copy targets).
 */
class ViteConfigStubTest extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function stub(): string
    {
        return file_get_contents(__DIR__.'/../../src/stubs/vite.config.js');
    }

    public function test_stub_imports_required_modules(): void
    {
        $stub = $this->stub();
        $this->assertStringContainsString("import { defineConfig } from 'vite';", $stub);
        $this->assertStringContainsString("import laravel from 'laravel-vite-plugin';", $stub);
    }

    public function test_stub_uses_app_js_as_entry_point(): void
    {
        $this->assertStringContainsString("input: ['resources/js/app.js']", $this->stub());
    }

    public function test_stub_does_not_copy_static_assets(): void
    {
        $this->assertStringNotContainsString('viteStaticCopy', $this->stub(), 'Vite emits the icon fonts from the scss; the copy step was writing files nothing read.');
    }

    public function test_stub_drops_legacy_vite_5_options(): void
    {
        $stub = $this->stub();
        $this->assertStringNotContainsString('commonjsOptions', $stub, 'Vite 8 default already enables transformMixedEsModules; drop the legacy explicit option.');
        $this->assertStringNotContainsString("protocol: 'ws'", $stub, 'Drop the explicit HMR ws block — laravel-vite-plugin v3 handles host/protocol detection.');
    }

    public function test_stub_silences_third_party_sass_deprecations(): void
    {
        $stub = $this->stub();
        $this->assertStringContainsString('quietDeps: true', $stub);
        $this->assertStringContainsString('silenceDeprecations', $stub);
    }
}
