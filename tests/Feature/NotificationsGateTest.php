<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks every layout / partial that includes the notifications dropdown
 * behind `config('tablar.enable_notifications')`. A fresh install must
 * not render the bell + sample data unless the user opts in.
 */
class NotificationsGateTest extends TestCase
{
    private const VIEWS_DIR = __DIR__.'/../../resources/views';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function gatedPartialProvider(): array
    {
        return [
            'sidebar'                  => [self::VIEWS_DIR.'/partials/navbar/sidebar.blade.php'],
            'overlap-topbar'           => [self::VIEWS_DIR.'/partials/navbar/overlap-topbar.blade.php'],
            'container-xl'             => [self::VIEWS_DIR.'/partials/common/container-xl.blade.php'],
            'condensed-container-xl'   => [self::VIEWS_DIR.'/partials/common/condensed-container-xl.blade.php'],
            'sidebar-top'              => [self::VIEWS_DIR.'/partials/header/sidebar-top.blade.php'],
        ];
    }

    #[DataProvider('gatedPartialProvider')]
    public function test_notifications_include_is_gated_by_config(string $path): void
    {
        $source = file_get_contents($path);

        $this->assertMatchesRegularExpression(
            '/@if\s*\(\s*config\(\s*[\'"]tablar\.enable_notifications[\'"]\s*\)\s*\)\s*@include\([\'"]tablar::partials\.header\.notifications[\'"]\)\s*@endif/s',
            $source,
            "Notifications include in {$path} must be wrapped in @if(config('tablar.enable_notifications')) @endif."
        );
    }

    public function test_no_unguarded_notifications_includes_in_repo(): void
    {
        $views = self::VIEWS_DIR;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($views, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $source = file_get_contents($file->getPathname());

            if (! str_contains($source, 'partials.header.notifications')) {
                continue;
            }

            // Skip the notifications partial itself.
            if (str_ends_with($file->getPathname(), 'header/notifications.blade.php')) {
                continue;
            }

            $this->assertMatchesRegularExpression(
                '/@if\s*\(\s*config\(\s*[\'"]tablar\.enable_notifications[\'"]\s*\)\s*\)\s*@include\([\'"]tablar::partials\.header\.notifications[\'"]\)\s*@endif/s',
                $source,
                "Found unguarded @include('tablar::partials.header.notifications') in {$file->getPathname()}."
            );
        }
    }
}
