<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class DarkModeInitTest extends TestCase
{
    private const MASTER_PATH = __DIR__.'/../../resources/views/master.blade.php';

    private const TOGGLE_PATH = __DIR__.'/../../resources/views/partials/header/theme-mode.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function master(): string
    {
        return file_get_contents(self::MASTER_PATH);
    }

    private function toggle(): string
    {
        return file_get_contents(self::TOGGLE_PATH);
    }

    public function test_master_layout_has_inline_theme_init_in_head(): void
    {
        $master = $this->master();

        // Init script must come BEFORE @vite so it runs pre-paint (FOUC guard).
        $this->assertMatchesRegularExpression(
            '/data-bs-theme/',
            $master,
            'Master layout must set data-bs-theme attribute.'
        );

        $this->assertStringContainsString("localStorage.getItem('tablar.theme')", $master);
        $this->assertStringContainsString('prefers-color-scheme: dark', $master);
        $this->assertStringContainsString("document.documentElement.setAttribute('data-bs-theme'", $master);
    }

    public function test_init_script_runs_before_vite_to_prevent_fouc(): void
    {
        $master = $this->master();

        $initPos = strpos($master, "document.documentElement.setAttribute('data-bs-theme'");
        $vitePos = strpos($master, '@vite');

        $this->assertNotFalse($initPos, 'Theme init script missing.');
        $this->assertNotFalse($vitePos, '@vite directive missing.');
        $this->assertLessThan($vitePos, $initPos, 'Theme init must run before Vite asset load to avoid FOUC.');
    }

    public function test_toggle_partial_uses_javascript_not_query_string(): void
    {
        $toggle = $this->toggle();

        $this->assertStringNotContainsString('?theme=dark', $toggle, 'Drop legacy ?theme= query-string toggle.');
        $this->assertStringNotContainsString('?theme=light', $toggle, 'Drop legacy ?theme= query-string toggle.');

        $this->assertMatchesRegularExpression(
            '/data-bs-theme-value=["\'](dark|light)["\']/',
            $toggle,
            'Toggle buttons must declare data-bs-theme-value for the JS handler.'
        );
    }

    public function test_toggle_handler_persists_to_localstorage(): void
    {
        $master = $this->master();

        $this->assertStringContainsString("localStorage.setItem('tablar.theme'", $master, 'Toggle handler must persist preference to localStorage.');
        $this->assertStringContainsString('data-bs-theme-value', $master, 'Master must wire click handlers for the toggle buttons.');
    }
}
