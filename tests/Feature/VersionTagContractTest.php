<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class VersionTagContractTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_tags_released_after_13_1_0_use_semver(): void
    {
        $root = dirname(__DIR__, 2);

        if (! is_dir($root.'/.git')) {
            $this->markTestSkipped('Not a git checkout.');
        }

        exec('git -C '.escapeshellarg($root).' for-each-ref --sort=creatordate --format="%(refname:short)" refs/tags', $tags);

        $index = array_search('13.1.0', $tags, true);

        if ($index === false) {
            $this->markTestSkipped('Tag 13.1.0 not found.');
        }

        $offenders = array_filter(
            array_slice($tags, $index + 1),
            fn (string $tag): bool => ! preg_match('/^\d+\.\d+\.\d+$/', $tag)
        );

        $this->assertSame([], array_values($offenders), 'Use MAJOR.MINOR.PATCH. Padded tags like 13.02 sort above 13.1.0.');
    }
}
