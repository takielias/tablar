<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class CiWorkflowsContractTest extends TestCase
{
    private const TESTS_WORKFLOW = __DIR__.'/../../.github/workflows/tests.yml';

    private const LINT_WORKFLOW = __DIR__.'/../../.github/workflows/lint.yml';

    private const FRESH_INSTALL_WORKFLOW = __DIR__.'/../../.github/workflows/fresh-install.yml';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function tests(): string
    {
        return file_get_contents(self::TESTS_WORKFLOW);
    }

    public function test_tests_workflow_exists(): void
    {
        $this->assertFileExists(self::TESTS_WORKFLOW);
    }

    public function test_tests_workflow_runs_on_modern_php_majors(): void
    {
        $yaml = $this->tests();

        foreach (["'8.3'", "'8.4'"] as $php) {
            $this->assertStringContainsString($php, $yaml, "tests.yml must include {$php} in the matrix.");
        }

        $this->assertStringNotContainsString("'8.2'", $yaml, 'Drop PHP 8.2 — package floor is 8.3.');
    }

    public function test_tests_workflow_runs_l11_l12_l13_matrix(): void
    {
        $yaml = $this->tests();

        foreach (['11.*', '12.*', '13.*'] as $laravel) {
            $this->assertStringContainsString($laravel, $yaml, "tests.yml must include Laravel {$laravel}.");
        }
    }

    public function test_tests_workflow_displays_deprecations(): void
    {
        $this->assertStringContainsString(
            '--display-deprecations',
            $this->tests(),
            'CI must surface PHPUnit deprecations to fail loud on framework drift.'
        );
    }

    public function test_tests_workflow_uses_setup_php_v2(): void
    {
        $this->assertStringContainsString('shivammathur/setup-php@v2', $this->tests());
    }

    public function test_tests_workflow_uses_checkout_v4(): void
    {
        $this->assertStringContainsString('actions/checkout@v4', $this->tests());
    }

    public function test_tests_workflow_caches_composer(): void
    {
        $this->assertStringContainsString('actions/cache@v4', $this->tests(), 'Composer cache speeds up matrix runs significantly.');
    }

    public function test_lint_workflow_exists(): void
    {
        $this->assertFileExists(self::LINT_WORKFLOW);
    }

    public function test_lint_workflow_runs_pint_in_test_mode(): void
    {
        $lint = file_get_contents(self::LINT_WORKFLOW);

        $this->assertStringContainsString('vendor/bin/pint --test', $lint);
    }

    public function test_lint_workflow_validates_composer_strict(): void
    {
        $lint = file_get_contents(self::LINT_WORKFLOW);

        $this->assertStringContainsString('composer validate --strict', $lint);
    }

    public function test_fresh_install_workflow_exists(): void
    {
        $this->assertFileExists(self::FRESH_INSTALL_WORKFLOW);
    }

    public function test_fresh_install_workflow_provisions_l11_l12_l13(): void
    {
        $yaml = file_get_contents(self::FRESH_INSTALL_WORKFLOW);

        foreach (['11.*', '12.*', '13.*'] as $laravel) {
            $this->assertStringContainsString($laravel, $yaml, "fresh-install must cover Laravel {$laravel}.");
        }
    }

    public function test_fresh_install_workflow_runs_doctor_and_curls_root(): void
    {
        $yaml = file_get_contents(self::FRESH_INSTALL_WORKFLOW);

        $this->assertStringContainsString('php artisan tablar:install --force', $yaml);
        $this->assertStringContainsString('php artisan tablar:doctor', $yaml);
        $this->assertStringContainsString('Welcome to Tablar', $yaml, 'Smoke must assert the welcome page renders.');
        $this->assertStringContainsString('npm run build', $yaml);
    }
}
