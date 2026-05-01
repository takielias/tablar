<?php

namespace TakiElias\Tablar\Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use TakiElias\Tablar\Tablar;
use TakiElias\Tablar\TablarPreset;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Regression guard for the takielias/tablar package.
 *
 * Locks current state of artisan commands, stubs, views, public API,
 * config, and service-provider publish tags. Any drift fails fast.
 *
 * Re-capture only with UPDATE_SNAPSHOTS=1 and an explicit reason in the
 * PR description.
 */
class SnapshotBaselineTest extends BaseTestCase
{
    private string $baselineDir;

    private string $packageRoot;

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->baselineDir = __DIR__.'/../__snapshots__/baseline';
        $this->packageRoot = realpath(__DIR__.'/../../');
    }

    public function test_artisan_command_signatures_unchanged(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $all = array_keys($kernel->all());
        $tablar = array_values(array_filter($all, fn ($n) => str_starts_with($n, 'tablar:')));
        sort($tablar);
        $this->assertSnapshot('tablar-commands.txt', implode("\n", $tablar)."\n");
    }

    public function test_view_inventory_unchanged(): void
    {
        $files = $this->listFiles('resources/views');
        sort($files);
        $this->assertSnapshot('view-inventory.txt', implode("\n", $files)."\n");
    }

    public function test_view_hashes_unchanged(): void
    {
        $hashes = [];
        foreach ($this->listFiles('resources/views') as $rel) {
            $hashes[$rel] = hash_file('sha256', $this->packageRoot.'/resources/views/'.$rel);
        }
        ksort($hashes);
        $this->assertSnapshot('view-hashes.json', json_encode($hashes, JSON_PRETTY_PRINT)."\n");
    }

    public function test_stub_inventory_unchanged(): void
    {
        $files = $this->listFiles('src/stubs');
        sort($files);
        $this->assertSnapshot('stub-inventory.txt', implode("\n", $files)."\n");
    }

    public function test_stub_hashes_unchanged(): void
    {
        $hashes = [];
        foreach ($this->listFiles('src/stubs') as $rel) {
            $hashes[$rel] = hash_file('sha256', $this->packageRoot.'/src/stubs/'.$rel);
        }
        ksort($hashes);
        $this->assertSnapshot('stub-hashes.json', json_encode($hashes, JSON_PRETTY_PRINT)."\n");
    }

    public function test_config_top_level_keys_unchanged(): void
    {
        $config = require $this->packageRoot.'/config/tablar.php';
        $keys = array_keys($config);
        sort($keys);
        $this->assertSnapshot('config-keys.txt', implode("\n", $keys)."\n");
    }

    public function test_tablar_class_public_api_unchanged(): void
    {
        $reflection = new \ReflectionClass(Tablar::class);
        $methods = array_map(fn ($m) => $m->getName(), $reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
        sort($methods);
        $this->assertSnapshot('tablar-class-methods.txt', implode("\n", $methods)."\n");
    }

    public function test_tablar_preset_static_api_unchanged(): void
    {
        $reflection = new \ReflectionClass(TablarPreset::class);
        $methods = array_map(
            fn ($m) => $m->getName(),
            array_filter(
                $reflection->getMethods(\ReflectionMethod::IS_PUBLIC),
                fn ($m) => $m->isStatic()
            )
        );
        sort($methods);
        $this->assertSnapshot('tablar-preset-static-methods.txt', implode("\n", $methods)."\n");
    }

    public function test_publish_tags_unchanged(): void
    {
        $tags = ServiceProvider::publishableGroups();
        sort($tags);
        $this->assertSnapshot('publish-tags.txt', implode("\n", $tags)."\n");
    }

    private function listFiles(string $relDir): array
    {
        $fs = new Filesystem;
        $abs = $this->packageRoot.'/'.$relDir;
        if (! is_dir($abs)) {
            return [];
        }
        $files = [];
        foreach ($fs->allFiles($abs) as $file) {
            $files[] = ltrim(str_replace($abs, '', $file->getPathname()), '/\\');
        }

        return $files;
    }

    private function assertSnapshot(string $name, string $actual): void
    {
        $path = $this->baselineDir.'/'.$name;
        if (! is_dir($this->baselineDir)) {
            mkdir($this->baselineDir, 0755, true);
        }
        if (getenv('UPDATE_SNAPSHOTS') === '1' || ! file_exists($path)) {
            file_put_contents($path, $actual);
            $this->markTestSkipped("Wrote baseline: {$name}");
        }
        $expected = file_get_contents($path);
        $this->assertSame($expected, $actual, "Snapshot drift in {$name}. If intentional, re-run with UPDATE_SNAPSHOTS=1 and commit changes.");
    }
}
