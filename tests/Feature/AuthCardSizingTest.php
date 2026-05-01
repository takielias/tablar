<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the four auth views to the same Tabler card-md / container-tight
 * shell so login, register, forgot-password, and reset-password all
 * render at identical width. Catches drift back to the legacy
 * page-single / col-login wrapper that used a wider column.
 */
class AuthCardSizingTest extends TestCase
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
            'login' => [self::VIEWS_DIR.'/login.blade.php'],
            'register' => [self::VIEWS_DIR.'/register.blade.php'],
            'forgot' => [self::VIEWS_DIR.'/passwords/email.blade.php'],
            'reset' => [self::VIEWS_DIR.'/passwords/reset.blade.php'],
        ];
    }

    #[DataProvider('authViewProvider')]
    public function test_uses_container_tight_wrapper(string $path): void
    {
        $source = file_get_contents($path);

        $this->assertStringContainsString('container container-tight py-4', $source);
    }

    #[DataProvider('authViewProvider')]
    public function test_uses_card_md(string $path): void
    {
        $source = file_get_contents($path);

        $this->assertMatchesRegularExpression(
            '/class="[^"]*card card-md/',
            $source,
            'Auth view must wrap the form in `card card-md` so all four match login width.'
        );
    }

    #[DataProvider('authViewProvider')]
    public function test_does_not_use_legacy_page_single_layout(string $path): void
    {
        $source = file_get_contents($path);

        $this->assertStringNotContainsString('page-single', $source, 'Drop legacy page-single wrapper.');
        $this->assertStringNotContainsString('col-login', $source, 'Drop legacy col-login wrapper — wider than container-tight.');
    }
}
