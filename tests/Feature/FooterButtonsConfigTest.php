<?php

namespace TakiElias\Tablar\Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the footer right-side `<ul>` to render from
 * `config('tablar.footer_buttons')`. Empty config hides the entire
 * `<ul>`. The Laravel version line on the left is unaffected.
 */
class FooterButtonsConfigTest extends TestCase
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

    public function test_partial_iterates_config_array(): void
    {
        $this->assertMatchesRegularExpression(
            '/@foreach\s*\(\s*config\(\s*[\'"]tablar\.footer_buttons[\'"]/',
            $this->source(),
        );
    }

    public function test_partial_guards_block_with_empty_check(): void
    {
        $this->assertMatchesRegularExpression(
            '/@if\s*\(\s*!\s*empty\(\s*config\(\s*[\'"]tablar\.footer_buttons[\'"]/',
            $this->source(),
        );
    }

    public function test_empty_config_hides_right_side_ul(): void
    {
        Config::set('tablar.footer_buttons', []);
        Config::set('tablar.current_version', 'v9-test');

        $rendered = Blade::render($this->source());

        $this->assertStringNotContainsString('list-inline list-inline-dots mb-0"', $rendered, 'Right-side `<ul>` must not render with empty footer_buttons.');
        $this->assertStringContainsString('Laravel v', $rendered, 'Left-side version line must still render.');
        $this->assertStringContainsString('v9-test', $rendered, 'tablar version pill must still render.');
    }

    public function test_populated_config_renders_each_link(): void
    {
        Config::set('tablar.footer_buttons', [
            ['name' => 'Docs',    'url' => 'https://example.test/docs',    'icon' => 'ti ti-book'],
            ['name' => 'Sponsor', 'url' => 'https://example.test/sponsor', 'icon' => 'ti ti-heart'],
        ]);
        Config::set('tablar.current_version', 'v9-test');

        $rendered = Blade::render($this->source());

        $this->assertStringContainsString('href="https://example.test/docs"', $rendered);
        $this->assertStringContainsString('href="https://example.test/sponsor"', $rendered);
        $this->assertStringContainsString('Docs', $rendered);
        $this->assertStringContainsString('Sponsor', $rendered);
        $this->assertStringContainsString('ti ti-heart', $rendered);
        $this->assertStringContainsString('link-secondary', $rendered);
    }
}
