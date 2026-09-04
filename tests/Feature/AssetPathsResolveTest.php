<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class AssetPathsResolveTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_every_asset_path_points_at_a_file_the_installer_ships(): void
    {
        $root = dirname(__DIR__, 2);
        $shipped = $root.'/src/stubs/assets';
        $offenders = [];

        foreach ([$root.'/src/stubs/resources/views', $root.'/resources/views'] as $dir) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

            foreach ($files as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                preg_match_all("/'(assets\/[^']+)'/", file_get_contents($file->getPathname()), $matches);

                foreach ($matches[1] as $path) {
                    if (! file_exists($shipped.'/'.substr($path, strlen('assets/')))) {
                        $offenders[] = basename($file->getPathname()).' -> '.$path;
                    }
                }
            }
        }

        $this->assertSame([], $offenders, 'Asset paths must exist under src/stubs/assets: '.implode(', ', $offenders));
    }
}
