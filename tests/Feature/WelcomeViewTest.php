<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class WelcomeViewTest extends TestCase
{
    private const STUB_PATH = __DIR__.'/../../src/stubs/resources/views/welcome.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::STUB_PATH);
    }

    public function test_extends_tablar_page_layout(): void
    {
        $this->assertStringContainsString("@extends('tablar::page')", $this->source());
    }

    public function test_contains_tablar_branded_headline(): void
    {
        $this->assertStringContainsString('Welcome to Tablar', $this->source());
    }

    public function test_no_tailwind_or_nunito_dependency(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('tailwind', strtolower($source), 'No Tailwind references allowed.');
        $this->assertStringNotContainsString('Nunito', $source, 'No Google Nunito font allowed.');
        $this->assertStringNotContainsString('fonts.googleapis.com', $source);
        $this->assertStringNotContainsString('normalize.css', $source);
    }

    public function test_no_hardcoded_laravel_brand_svg(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('#EF3B2D', $source, 'Drop the hardcoded Laravel red SVG mark.');
        $this->assertStringNotContainsString('Laracasts', $source, 'Drop the marketing card grid.');
        $this->assertStringNotContainsString('laravel-news.com', $source);
        $this->assertStringNotContainsString('taylorotwell', $source);
    }

    public function test_login_register_cta_guarded_by_route_has(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('@auth', $source);
        $this->assertStringContainsString("route('login')", $source);
        $this->assertStringContainsString("Route::has('register')", $source);
        $this->assertStringContainsString("route('register')", $source);
    }

    public function test_uses_tablar_card_layout_not_inline_svg(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('page page-center', $source, 'Use Tabler page-center container.');
        $this->assertStringContainsString('card', $source);
        $this->assertLessThan(60, substr_count($source, "\n"), 'Welcome page must be slim — under 60 lines.');
    }

    public function test_no_html_doctype_or_body_tag_when_extending_layout(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('<!DOCTYPE html>', $source, 'Layout extension should not also declare a full HTML document.');
        $this->assertStringNotContainsString('<html ', $source);
        $this->assertStringNotContainsString('<body', $source);
    }
}
