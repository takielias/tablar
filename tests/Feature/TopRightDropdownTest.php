<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the trimmed top-right user dropdown to Profile / Settings /
 * Log Out only. Catches drift back to the legacy 5-item menu where
 * Status and Feedback pointed at dead `#` links.
 */
class TopRightDropdownTest extends TestCase
{
    private const PARTIAL = __DIR__.'/../../resources/views/partials/header/top-right.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::PARTIAL);
    }

    public function test_profile_link_present(): void
    {
        $this->assertStringContainsString("__('tablar::tablar.profile')", $this->source());
    }

    public function test_settings_link_present(): void
    {
        $this->assertStringContainsString("__('tablar::tablar.settings')", $this->source());
    }

    public function test_logout_link_present(): void
    {
        $this->assertStringContainsString("__('tablar::tablar.log_out')", $this->source());
    }

    public function test_status_item_removed(): void
    {
        $this->assertStringNotContainsString(
            '>Status<',
            $this->source(),
            'Legacy Status dead link must be gone from the dropdown.'
        );
    }

    public function test_feedback_item_removed(): void
    {
        $this->assertStringNotContainsString(
            '>Feedback<',
            $this->source(),
            'Legacy Feedback dead link must be gone from the dropdown.'
        );
    }

    public function test_no_dead_hash_dropdown_items(): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/<a\s+href="#"\s+class="dropdown-item"/',
            $this->source(),
            'Dropdown items must point at real URLs, not href="#".'
        );
    }

    public function test_profile_href_uses_config_url(): void
    {
        $this->assertMatchesRegularExpression(
            '/<a\s+href="\{\{\s*\$profile_url\s*\}\}"/',
            $this->source()
        );
    }

    public function test_settings_href_uses_config_url(): void
    {
        $this->assertMatchesRegularExpression(
            '/<a\s+href="\{\{\s*\$setting_url\s*\}\}"/',
            $this->source()
        );
    }
}
