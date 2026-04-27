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
}
