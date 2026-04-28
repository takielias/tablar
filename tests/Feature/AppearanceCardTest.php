<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the Appearance card on the published settings view: 3 radio
 * inputs (light/dark/auto) wired via data-bs-theme-value, persisted to
 * localStorage, and dispatching `tablar:theme-change` so master.blade.php
 * picks up the change.
 */
class AppearanceCardTest extends TestCase
{
    private const PARTIAL = __DIR__.'/../../resources/views/partials/settings/appearance.blade.php';

    private const SETTINGS_VIEW = __DIR__.'/../../src/stubs/resources/views/settings/show.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_settings_view_includes_appearance_partial(): void
    {
        $source = file_get_contents(self::SETTINGS_VIEW);

        $this->assertStringContainsString("@include('tablar::partials.settings.appearance')", $source);
    }

    public function test_appearance_partial_has_three_theme_options(): void
    {
        $source = file_get_contents(self::PARTIAL);

        foreach (['light', 'dark', 'auto'] as $value) {
            $this->assertMatchesRegularExpression(
                '/data-bs-theme-value="'.$value.'"/',
                $source,
                "Appearance card must include a `{$value}` radio input.",
            );
        }
    }

    public function test_appearance_partial_persists_to_localstorage_and_emits_event(): void
    {
        $source = file_get_contents(self::PARTIAL);

        $this->assertStringContainsString("localStorage.setItem('tablar.theme'", $source);
        $this->assertStringContainsString("'tablar:theme-change'", $source);
    }

    public function test_appearance_partial_uses_tabler_icons_not_svgs(): void
    {
        $source = file_get_contents(self::PARTIAL);

        $this->assertStringContainsString('ti ti-sun', $source);
        $this->assertStringContainsString('ti ti-moon', $source);
        $this->assertStringContainsString('ti ti-device-desktop', $source);
    }
}
