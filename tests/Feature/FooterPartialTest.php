<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class FooterPartialTest extends TestCase
{
    private const FOOTER = __DIR__.'/../../resources/views/partials/footer/bottom.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::FOOTER);
    }

    public function test_no_copyright_text(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('Copyright', $source, 'Drop the static copyright text — bottom_title config no longer needs to render here.');
        $this->assertStringNotContainsString('All rights reserved', $source);
        $this->assertStringNotContainsString('&copy; 2022', $source, 'Stale year hardcode must be gone.');
    }

    public function test_keeps_source_code_and_sponsor_links(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('Source code', $source);
        $this->assertStringContainsString('Sponsor', $source);
    }

    public function test_keeps_version_link(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('current_version', $source, 'Version pill should still surface so reviewers see the deployed build.');
    }

    public function test_shows_running_laravel_version(): void
    {
        $source = $this->source();

        $this->assertStringContainsString(
            'Illuminate\Foundation\Application::VERSION',
            $source,
            'Footer should print the running Laravel framework version so reviewers can see the host stack at a glance.'
        );
        $this->assertMatchesRegularExpression(
            '/Laravel v\{\{\s*Illuminate\\\\Foundation\\\\Application::VERSION\s*\}\}/',
            $source,
            'Version line should read "Laravel v{{ ... }}".'
        );
    }
}
