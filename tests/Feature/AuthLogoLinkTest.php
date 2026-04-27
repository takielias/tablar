<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TakiElias\Tablar\TablarServiceProvider;

class AuthLogoLinkTest extends TestCase
{
    private const VIEWS_DIR = __DIR__.'/../../resources/views/auth';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function authViewProvider(): array
    {
        return [
            'login'    => [self::VIEWS_DIR.'/login.blade.php'],
            'register' => [self::VIEWS_DIR.'/register.blade.php'],
            'forgot'   => [self::VIEWS_DIR.'/passwords/email.blade.php'],
            'reset'    => [self::VIEWS_DIR.'/passwords/reset.blade.php'],
        ];
    }

    #[DataProvider('authViewProvider')]
    public function test_logo_links_to_site_root(string $path): void
    {
        $source = file_get_contents($path);

        // The brand anchor must point at the site root, not stay on the
        // current auth page (`href=""`).
        $this->assertMatchesRegularExpression(
            "/<a\s+href=\"\{\{\s*url\(\s*'\/'\s*\)\s*\}\}\"\s+class=\"navbar-brand/",
            $source,
            'Logo anchor must use href="{{ url(\'/\') }}" so clicking it returns the user to the welcome page.'
        );

        $this->assertDoesNotMatchRegularExpression(
            '/<a\s+href=""\s+class="navbar-brand/',
            $source,
            'Empty href on the logo causes a no-op redirect to the current auth page.'
        );
    }
}
