<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class HomeViewTest extends TestCase
{
    private const STUB_PATH = __DIR__.'/../../src/stubs/resources/views/home.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::STUB_PATH);
    }

    public function test_extends_tablar_layout(): void
    {
        $this->assertStringContainsString("@extends('tablar::page')", $this->source());
    }

    public function test_shows_empty_state_card(): void
    {
        $source = $this->source();

        $this->assertStringContainsString("You're all set", $source);
        $this->assertStringContainsString('class="empty"', $source);
        $this->assertStringContainsString('empty-title', $source);
        $this->assertStringContainsString('empty-action', $source);
    }

    public function test_no_demo_widgets(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('apexcharts', $source, 'Drop demo charts');
        $this->assertStringNotContainsString('sparkline', strtolower($source), 'Drop demo sparklines');
        $this->assertStringNotContainsString('jsvectormap', strtolower($source), 'Drop demo maps');
        $this->assertStringNotContainsString('Create new report', $source, 'Drop demo CTA copy');
        $this->assertStringNotContainsString('New view', $source, 'Drop demo navtab labels');
    }

    public function test_line_count_under_one_hundred(): void
    {
        $lines = substr_count($this->source(), "\n");

        $this->assertLessThan(100, $lines, "home.blade.php must be a slim empty-state, not a busy demo. Current: {$lines} lines.");
    }

    public function test_dashboard_header_with_authed_user_name(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('page-header', $source);
        $this->assertStringContainsString('page-title', $source);
        $this->assertMatchesRegularExpression(
            '/auth\(\)\s*->\s*user\(\)\s*->\s*name/',
            $source,
            'Welcome back line should reference the authed user name.'
        );
    }
}
