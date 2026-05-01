<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class ReadmeImageLinksTest extends TestCase
{
    private const README = __DIR__.'/../../README.md';

    private const SHOTS_DIR = __DIR__.'/../../screenshots';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_screenshots_directory_exists(): void
    {
        $this->assertDirectoryExists(self::SHOTS_DIR, 'screenshots/ directory must exist for local image refs.');
    }

    public function test_screenshots_directory_has_capture_doc(): void
    {
        $this->assertFileExists(self::SHOTS_DIR.'/CAPTURE.md', 'screenshots/CAPTURE.md must document the capture process.');
    }

    public function test_local_image_refs_in_readme_resolve(): void
    {
        $readme = file_get_contents(self::README);

        // Match markdown image refs:  ![alt](path)
        preg_match_all('/!\[[^\]]*\]\(([^)]+)\)/', $readme, $matches);

        $offenders = [];
        foreach ($matches[1] as $ref) {
            // Skip remote URLs and shields.io badges; only validate local relative paths.
            if (preg_match('#^(https?://|//)#', $ref)) {
                continue;
            }

            $local = realpath(__DIR__.'/../../'.ltrim($ref, '/'));
            if ($local === false || ! file_exists($local)) {
                $offenders[] = $ref;
            }
        }

        $this->assertEmpty(
            $offenders,
            'README references missing local images: '.implode(', ', $offenders)
        );
    }

    public function test_readme_drops_legacy_external_screenshot_host(): void
    {
        $readme = file_get_contents(self::README);

        // user-attachment URLs from old issues rot — references should migrate to screenshots/.
        $this->assertStringNotContainsString(
            'github.com/takielias/tablar/assets/',
            $readme,
            'Migrate old github user-attachment screenshots into local screenshots/ dir.'
        );
    }

    public function test_readme_carries_actions_status_badges(): void
    {
        $readme = file_get_contents(self::README);

        $this->assertStringContainsString(
            'actions/workflows/tests.yml/badge.svg',
            $readme,
            'README must show the GitHub Actions tests badge so reviewers can see CI status.'
        );
        $this->assertStringContainsString(
            'actions/workflows/lint.yml/badge.svg',
            $readme,
            'README must show the GitHub Actions lint badge.'
        );
    }

    public function test_readme_advertises_supported_runtimes(): void
    {
        $readme = file_get_contents(self::README);

        $this->assertMatchesRegularExpression(
            '/packagist\/dependency-v\/takielias\/tablar\/php/',
            $readme,
            'README must show the dynamic packagist PHP dependency badge.'
        );
        $this->assertMatchesRegularExpression(
            '/Laravel-11.+12.+13/',
            $readme,
            'README must advertise Laravel 11/12/13 support.'
        );
        $this->assertMatchesRegularExpression(
            '/packagist\/l\/takielias\/tablar/',
            $readme,
            'README must show the license badge.'
        );
    }
}
