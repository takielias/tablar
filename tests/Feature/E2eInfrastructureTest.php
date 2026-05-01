<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Phase 8 contract — locks the e2e infrastructure shipped inside this
 * package. The actual Playwright run happens against a separate demo
 * project provisioned post-push by tests/e2e/scripts/run-fresh-install.sh.
 *
 * These tests assert the scaffolding files exist and have the expected
 * shape so a contributor can rebuild the demo with one command.
 */
class E2eInfrastructureTest extends TestCase
{
    private const E2E_DIR = __DIR__.'/../e2e';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_playwright_config_exists(): void
    {
        $this->assertFileExists(self::E2E_DIR.'/playwright.config.ts');
    }

    public function test_playwright_package_json_pins_modern_version(): void
    {
        $package = json_decode(file_get_contents(self::E2E_DIR.'/package.json'), true);

        $this->assertSame('tablar-e2e', $package['name'] ?? null);
        $this->assertArrayHasKey('@playwright/test', $package['devDependencies'] ?? []);
        $this->assertMatchesRegularExpression(
            '/\^1\.5/',
            $package['devDependencies']['@playwright/test'] ?? '',
            'Playwright must be on the modern 1.5x line.'
        );
    }

    public function test_required_specs_exist(): void
    {
        foreach (['welcome', 'auth', 'dashboard', 'dark-mode'] as $spec) {
            $this->assertFileExists(
                self::E2E_DIR."/specs/{$spec}.spec.ts",
                "Missing e2e spec: {$spec}.spec.ts"
            );
        }
    }

    public function test_required_scripts_exist_and_are_executable(): void
    {
        foreach (['run-fresh-install', 'teardown', 'rebuild'] as $script) {
            $path = self::E2E_DIR."/scripts/{$script}.sh";
            $this->assertFileExists($path, "Missing script: {$script}.sh");
            $this->assertTrue(is_executable($path), "Script must be executable: {$script}.sh");
        }
    }

    public function test_run_fresh_install_supports_dry_run(): void
    {
        $script = file_get_contents(self::E2E_DIR.'/scripts/run-fresh-install.sh');

        $this->assertStringContainsString('--dry-run', $script);
        $this->assertStringContainsString('composer create-project', $script);
        $this->assertStringContainsString('tablar:install --force', $script);
        $this->assertStringContainsString('npm run build', $script);
        $this->assertStringContainsString('Welcome to Tablar', $script, 'Smoke must assert welcome page renders.');
        $this->assertStringContainsString('tablar:doctor', $script);
    }

    public function test_run_fresh_install_wires_all_four_path_repos(): void
    {
        $script = file_get_contents(self::E2E_DIR.'/scripts/run-fresh-install.sh');

        foreach ([
            'takielias/tablar',
            'takielias/tablar-kit',
            'takielias/tablar-crud-generator',
            'takielias/lab',
        ] as $package) {
            $this->assertStringContainsString(
                $package,
                $script,
                "run-fresh-install.sh must wire {$package} via path repo."
            );
        }
    }

    public function test_rebuild_runs_two_cycles_for_idempotence(): void
    {
        $script = file_get_contents(self::E2E_DIR.'/scripts/rebuild.sh');

        $this->assertStringContainsString('first', $script);
        $this->assertStringContainsString('second', $script);
        $this->assertStringContainsString('teardown.sh', $script);
        $this->assertStringContainsString('run-fresh-install.sh', $script);
    }

    public function test_doctor_snapshot_locks_expected_shape(): void
    {
        $snapshot = file_get_contents(self::E2E_DIR.'/snapshots/doctor-output.txt');

        $this->assertStringContainsString('Tablar Doctor', $snapshot);

        // Each row label that the doctor command emits.
        foreach ([
            'PHP',
            'Laravel',
            'Tablar',
            'Node',
            'npm',
            'Vite',
            '@tabler/core',
            'DB driver',
            'Vite manifest',
        ] as $label) {
            $this->assertStringContainsString(
                $label,
                $snapshot,
                "Doctor snapshot must include row: {$label}"
            );
        }

        // Status column markers expected on a happy run.
        $this->assertStringContainsString('✓', $snapshot, 'Snapshot should show at least one OK marker.');
    }

    public function test_e2e_specs_target_actual_features(): void
    {
        $welcome = file_get_contents(self::E2E_DIR.'/specs/welcome.spec.ts');
        $this->assertStringContainsString('Welcome to Tablar', $welcome);
        $this->assertStringContainsString('Tailwind', $welcome, 'Should assert the legacy Tailwind landing is gone.');

        $auth = file_get_contents(self::E2E_DIR.'/specs/auth.spec.ts');
        $this->assertStringContainsString("'/login'", $auth);
        $this->assertStringContainsString("'/register'", $auth);

        $dashboard = file_get_contents(self::E2E_DIR.'/specs/dashboard.spec.ts');
        $this->assertStringContainsString("You're all set", $dashboard);

        $dark = file_get_contents(self::E2E_DIR.'/specs/dark-mode.spec.ts');
        $this->assertStringContainsString('data-bs-theme', $dark);
        $this->assertStringContainsString('tablar.theme', $dark, 'Should assert localStorage key matches master layout.');
    }
}
