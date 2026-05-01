<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Asserts the README declares the modern stack requirements.
 *
 * Reviewer feedback flagged old-version vibes; an explicit Requirements
 * block is the cheapest way to set expectations up front.
 */
class ReadmeRequirementsTest extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function readme(): string
    {
        return file_get_contents(__DIR__.'/../../README.md');
    }

    public function test_readme_lists_php_minimum(): void
    {
        $this->assertMatchesRegularExpression('/PHP\s*\*\*8\.3/', $this->readme());
    }

    public function test_readme_lists_laravel_range(): void
    {
        $readme = $this->readme();
        foreach (['11.x', '12.x', '13.x'] as $major) {
            $this->assertStringContainsString($major, $readme, "Laravel {$major} should be declared as supported");
        }
    }

    public function test_readme_lists_node_minimum(): void
    {
        $readme = $this->readme();
        $this->assertStringContainsString('20.19', $readme, 'Node 20.19+ is the Vite 8 minimum');
        $this->assertStringContainsString('22', $readme, 'Node 22 LTS is the recommended target');
    }
}
