<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class DisplayAlertConfigTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_alerts_are_visible_without_publishing_the_config(): void
    {
        $this->assertTrue(config('tablar.display_alert'));
    }

    public function test_shipped_config_file_defaults_to_true(): void
    {
        $config = require __DIR__.'/../../config/tablar.php';

        $this->assertTrue($config['display_alert']);
    }
}
