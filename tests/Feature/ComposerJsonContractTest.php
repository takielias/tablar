<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class ComposerJsonContractTest extends TestCase
{
    private const COMPOSER_PATH = __DIR__.'/../../composer.json';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function composer(): array
    {
        return json_decode(file_get_contents(self::COMPOSER_PATH), true, flags: JSON_THROW_ON_ERROR);
    }

    public function test_php_constraint_pins_8_3_minimum(): void
    {
        $php = $this->composer()['require']['php'] ?? null;

        $this->assertNotNull($php, 'require.php constraint must be set.');
        $this->assertStringContainsString('^8.3', $php, 'PHP constraint must include ^8.3.');
    }

    public function test_illuminate_support_supports_l11_l12_l13(): void
    {
        $constraint = $this->composer()['require']['illuminate/support'] ?? '';

        foreach (['^11.0', '^12.0', '^13.0'] as $required) {
            $this->assertStringContainsString(
                $required,
                $constraint,
                "illuminate/support must support {$required}; got '{$constraint}'."
            );
        }
    }

    public function test_no_loose_ge_ten_constraints(): void
    {
        $composer = $this->composer();
        $haystack = json_encode(array_merge($composer['require'] ?? [], $composer['require-dev'] ?? []));

        $this->assertDoesNotMatchRegularExpression(
            '/>=\s*\d/',
            $haystack,
            'Drop loose `>=X` constraints in favour of explicit caret ranges.'
        );
    }

    public function test_phpunit_pinned_to_modern_majors(): void
    {
        $constraint = $this->composer()['require-dev']['phpunit/phpunit'] ?? '';

        $this->assertMatchesRegularExpression(
            '/\^11\.0/',
            $constraint,
            'phpunit constraint must include ^11.0.'
        );
    }

    public function test_testbench_supports_l11_l12_l13(): void
    {
        $constraint = $this->composer()['require-dev']['orchestra/testbench'] ?? '';

        foreach (['^9.0', '^10.0', '^11.0'] as $required) {
            $this->assertStringContainsString(
                $required,
                $constraint,
                "orchestra/testbench must support {$required}; got '{$constraint}'."
            );
        }
    }

    public function test_composer_validate_succeeds(): void
    {
        $output = [];
        $exit = 0;
        exec('cd '.escapeshellarg(realpath(__DIR__.'/../..')).' && composer validate --strict 2>&1', $output, $exit);

        $this->assertSame(
            0,
            $exit,
            "composer validate --strict failed:\n".implode("\n", $output)
        );
    }
}
