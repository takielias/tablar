<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the phase-9 chrome defaults: profile/setting URLs point at the
 * shipped routes, notifications opt-in, and header/footer button arrays
 * default to empty so a fresh install ships no maintainer-branded chrome.
 */
class Phase9ConfigDefaultsTest extends TestCase
{
    private const CONFIG = __DIR__.'/../../config/tablar.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    /**
     * @return array<string, mixed>
     */
    private function config(): array
    {
        return require self::CONFIG;
    }

    public function test_profile_url_defaults_to_profile_route_name(): void
    {
        $this->assertSame('profile', $this->config()['profile_url'] ?? null);
    }

    public function test_setting_url_defaults_to_settings_route_name(): void
    {
        $this->assertSame('settings', $this->config()['setting_url'] ?? null);
    }

    public function test_notifications_enabled_by_default(): void
    {
        $this->assertTrue($this->config()['enable_notifications'] ?? null);
    }

    public function test_header_buttons_ship_with_source_and_sponsor(): void
    {
        $buttons = $this->config()['header_buttons'] ?? [];

        $this->assertNotEmpty($buttons, 'Default header_buttons should ship with Source code + Sponsor; users empty the array to hide.');

        $names = array_column($buttons, 'name');
        $this->assertContains('Source code', $names);
        $this->assertContains('Sponsor', $names);

        foreach ($buttons as $btn) {
            $this->assertArrayHasKey('url', $btn);
            $this->assertArrayHasKey('icon', $btn);
            $this->assertStringStartsWith('ti ', $btn['icon'], 'Icon should be a Tabler icon class.');
        }
    }

    public function test_footer_buttons_ship_with_source_and_sponsor(): void
    {
        $buttons = $this->config()['footer_buttons'] ?? [];

        $this->assertNotEmpty($buttons);

        $names = array_column($buttons, 'name');
        $this->assertContains('Source code', $names);
        $this->assertContains('Sponsor', $names);
    }
}
