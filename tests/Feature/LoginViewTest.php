<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TakiElias\Tablar\TablarServiceProvider;

class LoginViewTest extends TestCase
{
    private const PUBLISHED_VIEW = __DIR__.'/../../resources/views/auth/login.blade.php';

    private const STUB = __DIR__.'/../../src/stubs/resources/views/auth/login.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function loginViewProvider(): array
    {
        return [
            'published view' => [self::PUBLISHED_VIEW],
            'install stub' => [self::STUB],
        ];
    }

    #[DataProvider('loginViewProvider')]
    public function test_no_github_social_login_button(string $path): void
    {
        $source = file_get_contents($path);

        $this->assertStringNotContainsString('Login with Github', $source);
        $this->assertStringNotContainsString('brand-github', $source);
        $this->assertStringNotContainsString('text-github', $source);
    }

    #[DataProvider('loginViewProvider')]
    public function test_no_twitter_social_login_button(string $path): void
    {
        $source = file_get_contents($path);

        $this->assertStringNotContainsString('Login with Twitter', $source);
        $this->assertStringNotContainsString('brand-twitter', $source);
        $this->assertStringNotContainsString('text-twitter', $source);
    }

    public function test_email_password_form_present_in_published_view(): void
    {
        // The published view holds the actual form markup. The install
        // stub is just a thin @extends('tablar::auth.login') wrapper.
        $source = file_get_contents(self::PUBLISHED_VIEW);

        $this->assertMatchesRegularExpression('/name="email"/', $source);
        $this->assertMatchesRegularExpression('/name="password"/', $source);
        $this->assertStringContainsString("route('login')", $source);
        $this->assertStringContainsString('@csrf', $source);
        $this->assertStringContainsString('Sign in', $source);
    }

    public function test_install_stub_extends_published_view(): void
    {
        $stub = file_get_contents(self::STUB);

        $this->assertMatchesRegularExpression(
            "/@extends\(\s*['\"]tablar::auth\.login['\"]\s*\)/",
            $stub,
            'Install stub should extend the package login layout, not duplicate the form markup.'
        );
    }

    public function test_password_toggle_button_wired(): void
    {
        $source = file_get_contents(self::PUBLISHED_VIEW);

        $this->assertStringContainsString('data-password-toggle', $source, 'Eye anchor must carry data-password-toggle hook for the JS handler.');
        $this->assertStringContainsString('data-icon-show', $source, 'Eye-open SVG must be marked data-icon-show.');
        $this->assertStringContainsString('data-icon-hide', $source, 'Eye-closed SVG must be marked data-icon-hide.');
        $this->assertStringContainsString('aria-label="Show password"', $source, 'Toggle anchor must have an accessible aria-label.');
    }

    public function test_auth_layout_handles_password_toggle(): void
    {
        $layout = file_get_contents(__DIR__.'/../../resources/views/auth/layout.blade.php');

        $this->assertStringContainsString('data-password-toggle', $layout, 'Auth layout must wire a click handler for [data-password-toggle].');
        $this->assertMatchesRegularExpression(
            "/input\.type\s*=\s*showing\s*\?\s*['\"]password['\"]\s*:\s*['\"]text['\"]/",
            $layout,
            'Handler must flip input.type between password and text.'
        );
        $this->assertStringContainsString('addEventListener', $layout);
        $this->assertStringContainsString('event.target.closest', $layout, 'Use event delegation rather than per-element listeners.');
    }
}
