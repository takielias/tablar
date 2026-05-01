<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the shipped tablar.php menu shape. The first install should
 * land on a clean sidebar with one Home link — not on three Support
 * stubs that point at routes the fresh app doesn't define.
 */
class DefaultMenuConfigTest extends TestCase
{
    private const CONFIG = __DIR__.'/../../config/tablar.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function config(): array
    {
        return require self::CONFIG;
    }

    public function test_default_menu_only_contains_home_entry(): void
    {
        $menu = $this->config()['menu'] ?? [];

        $this->assertCount(1, $menu, 'Default menu should only ship the Home link.');
        $this->assertSame('Home', $menu[0]['text'] ?? null);
        $this->assertSame('home', $menu[0]['url'] ?? null);
    }

    public function test_demo_support_stubs_removed(): void
    {
        $menu = $this->config()['menu'] ?? [];

        $labels = array_column($menu, 'text');

        foreach (['Support 1', 'Support 2', 'Support 3'] as $stub) {
            $this->assertNotContains(
                $stub,
                $labels,
                "Demo stub '{$stub}' must not ship in the default menu — points at routes the fresh app doesn't define."
            );
        }
    }

    public function test_no_hash_placeholder_urls_in_default_menu(): void
    {
        $menu = $this->config()['menu'] ?? [];

        foreach ($menu as $item) {
            $this->assertNotSame(
                '#',
                $item['url'] ?? null,
                "Top-level menu item '{$item['text']}' uses a '#' placeholder URL."
            );
        }
    }
}
