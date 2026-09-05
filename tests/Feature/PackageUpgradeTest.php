<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarPreset;
use TakiElias\Tablar\TablarServiceProvider;

class PackageUpgradeTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function update(array $existing): array
    {
        $method = new \ReflectionMethod(TablarPreset::class, 'updatePackageArray');
        $method->setAccessible(true);

        return $method->invoke(null, $existing, 'devDependencies');
    }

    public function test_tablar_versions_replace_the_versions_already_in_the_app(): void
    {
        $result = $this->update([
            '@tabler/core' => '1.0.0',
            'bootstrap' => '5.2.0',
            'tom-select' => '^1.0.0',
        ]);

        $this->assertSame('1.4.0', $result['@tabler/core']);
        $this->assertSame('5.3.8', $result['bootstrap']);
        $this->assertSame('^2.4.3', $result['tom-select']);
    }

    public function test_packages_the_app_added_itself_are_kept(): void
    {
        $result = $this->update(['alpinejs' => '^3.0.0']);

        $this->assertSame('^3.0.0', $result['alpinejs']);
    }

    public function test_packages_tablar_drops_are_removed(): void
    {
        $result = $this->update(['select2' => '^4.1.0', 'sass-loader' => '^13.0.0']);

        $this->assertArrayNotHasKey('select2', $result);
        $this->assertArrayNotHasKey('sass-loader', $result);
    }
}
