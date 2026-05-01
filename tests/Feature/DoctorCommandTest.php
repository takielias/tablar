<?php

namespace TakiElias\Tablar\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\Console\TablarDoctorCommand;
use TakiElias\Tablar\TablarServiceProvider;

class DoctorCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_command_is_registered(): void
    {
        $this->assertArrayHasKey('tablar:doctor', Artisan::all());
    }

    public function test_reports_running_php_version(): void
    {
        $exit = Artisan::call('tablar:doctor');
        $output = Artisan::output();

        $this->assertStringContainsString('PHP', $output);
        $this->assertStringContainsString(PHP_VERSION, $output);
        // exit code may be FAILURE if Vite manifest missing in testbench, but PHP should be reported either way.
        $this->assertContains($exit, [0, 1]);
    }

    public function test_reports_running_laravel_version(): void
    {
        Artisan::call('tablar:doctor');
        $output = Artisan::output();

        $this->assertStringContainsString('Laravel', $output);
        $this->assertStringContainsString($this->app->version(), $output);
    }

    public function test_reports_db_driver(): void
    {
        Artisan::call('tablar:doctor');
        $output = Artisan::output();

        $this->assertStringContainsString('DB driver', $output);
    }

    public function test_warns_when_vite_manifest_missing(): void
    {
        // Testbench has no public/build/manifest.json by default.
        $exit = Artisan::call('tablar:doctor');
        $output = Artisan::output();

        $this->assertStringContainsString('Vite manifest', $output);
        $this->assertStringContainsString('missing', $output);
        $this->assertSame(1, $exit, 'Missing manifest is a fail-state — exit code must be non-zero.');
    }

    public function test_passes_when_vite_manifest_present(): void
    {
        $manifestPath = public_path('build/manifest.json');
        @mkdir(dirname($manifestPath), 0o755, true);
        file_put_contents($manifestPath, '{}');

        try {
            $exit = Artisan::call('tablar:doctor');
            $output = Artisan::output();

            $this->assertStringContainsString('Vite manifest', $output);
            $this->assertStringContainsString('present', $output);
            $this->assertSame(0, $exit, 'With manifest in place, exit code should be 0.');
        } finally {
            @unlink($manifestPath);
            @rmdir(dirname($manifestPath));
            @rmdir(public_path('build'));
        }
    }

    public function test_renders_branded_header(): void
    {
        Artisan::call('tablar:doctor');
        $output = Artisan::output();

        $this->assertStringContainsString('Tablar Doctor', $output);
        $this->assertMatchesRegularExpression('/─{5,}/u', $output, 'Header divider should be present.');
    }

    public function test_command_handles_missing_node_gracefully(): void
    {
        // Even when node/npm are unreachable, command must not throw.
        Artisan::call('tablar:doctor');
        $output = Artisan::output();

        $this->assertStringContainsString('Node', $output);
        $this->assertStringContainsString('npm', $output);
    }

    public function test_class_exposes_signature_string(): void
    {
        $cmd = new TablarDoctorCommand;
        $reflection = new \ReflectionClass($cmd);
        $signature = $reflection->getProperty('signature');
        $signature->setAccessible(true);

        $this->assertSame('tablar:doctor', $signature->getValue($cmd));
    }
}
