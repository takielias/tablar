<?php

namespace TakiElias\Tablar\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the top-bar button partial to render from
 * `config('tablar.header_buttons')`. Default is empty so a fresh
 * install ships zero maintainer-branded buttons.
 */
class HeaderButtonsConfigTest extends TestCase
{
    private const PARTIAL = __DIR__.'/../../resources/views/partials/header/header-button.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::PARTIAL);
    }

    public function test_partial_iterates_config_array(): void
    {
        $this->assertMatchesRegularExpression(
            '/@foreach\s*\(\s*config\(\s*[\'"]tablar\.header_buttons[\'"]/',
            $this->source(),
        );
    }

    public function test_no_hardcoded_maintainer_links(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('github.com/takielias/tablar', $source);
        $this->assertStringNotContainsString('buymeacoffee', $source);
        $this->assertStringNotContainsString('Source code', $source);
        $this->assertStringNotContainsString('Sponsor', $source);
    }

    public function test_empty_config_renders_nothing(): void
    {
        Config::set('tablar.header_buttons', []);

        $rendered = trim(Blade::render($this->source()));

        $this->assertSame('', $rendered, 'Empty header_buttons config must render no markup.');
    }

    public function test_populated_config_renders_each_button(): void
    {
        Config::set('tablar.header_buttons', [
            ['name' => 'Docs',   'url' => 'https://example.test/docs',   'icon' => 'ti ti-book'],
            ['name' => 'GitHub', 'url' => 'https://example.test/github', 'icon' => 'ti ti-brand-github'],
        ]);

        $rendered = Blade::render($this->source());

        $this->assertStringContainsString('href="https://example.test/docs"', $rendered);
        $this->assertStringContainsString('href="https://example.test/github"', $rendered);
        $this->assertStringContainsString('Docs', $rendered);
        $this->assertStringContainsString('GitHub', $rendered);
        $this->assertStringContainsString('ti ti-book', $rendered);
        $this->assertStringContainsString('ti ti-brand-github', $rendered);
    }

    public function test_icon_is_optional(): void
    {
        Config::set('tablar.header_buttons', [
            ['name' => 'Plain', 'url' => 'https://example.test/plain'],
        ]);

        $rendered = Blade::render($this->source());

        $this->assertStringContainsString('Plain', $rendered);
        $this->assertStringNotContainsString('<i class=" icon', $rendered);
    }
}
