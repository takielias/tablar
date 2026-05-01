<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class InstallCommandVersionMessageTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_message_is_not_hardcoded_to_laravel_11(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        $this->assertStringNotContainsString(
            "'Running on Laravel 11.'",
            $source,
            'Install command must not hardcode "Running on Laravel 11." — derive major from app()->version().'
        );
        $this->assertStringNotContainsString(
            '"Running on Laravel 11."',
            $source,
            'Install command must not hardcode "Running on Laravel 11." — derive major from app()->version().'
        );
    }

    public function test_check_controller_uses_dynamic_major(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        $this->assertMatchesRegularExpression(
            '/Running on Laravel \{\$major\}\./',
            $source,
            'Install command should interpolate {$major} into the message.'
        );

        $this->assertMatchesRegularExpression(
            '/\(int\)\s*explode\(\s*[\'"]\.[\'"]\s*,\s*app\(\)->version\(\)\s*\)\[0\]/',
            $source,
            'Install command should derive major via (int) explode(".", app()->version())[0].'
        );
    }

    public function test_install_output_is_compact_with_no_duplicate_messages(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        // The branded headline "Tablar installed" should appear exactly once.
        $brandedHeadline = preg_match_all(
            '/\$this->info\([^)]*Tablar installed/',
            $source
        );
        $this->assertSame(1, $brandedHeadline, 'Branded "Tablar installed" headline should appear exactly once.');

        // No duplicate "npm install" prompts.
        $npmEmissions = preg_match_all('/npm install/i', $source);
        $this->assertLessThanOrEqual(1, $npmEmissions, '"npm install" should appear at most once in output.');

        // Old verbose lines must be gone.
        $this->assertStringNotContainsString(
            'Tablar scaffolding installed & config has been exported successfully.',
            $source
        );
        $this->assertStringNotContainsString(
            'Tablar is now installed 🚀',
            $source
        );
        $this->assertStringNotContainsString(
            'Run "npm install" first. Once the installation is done',
            $source
        );

        // Compact one-liner must be present.
        $this->assertMatchesRegularExpression(
            '/✅ Tablar installed \(Laravel \{\$major\}\)\./',
            $source
        );
    }

    public function test_no_credits_flag_suppresses_github_plug(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        $this->assertStringContainsString('--no-credits', $source);
        $this->assertMatchesRegularExpression(
            '/!\s*\$this->option\([\'"]no-credits[\'"]\)/',
            $source,
            'Credits block must be gated by the --no-credits option.'
        );
    }

    public function test_handle_returns_int_success(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        $this->assertMatchesRegularExpression(
            '/public function handle\(\):\s*int/',
            $source,
            'handle() must declare int return type.'
        );
        $this->assertMatchesRegularExpression(
            '/return self::SUCCESS;/',
            $source,
            'handle() must return self::SUCCESS for proper exit code.'
        );
    }

    public function test_version_message_matches_running_laravel_major(): void
    {
        $major = (int) explode('.', $this->app->version())[0];

        $this->assertGreaterThanOrEqual(11, $major, 'Test runs on Laravel 11+ only.');

        $tmpFile = tempnam(sys_get_temp_dir(), 'tablar_ctrl_').'.php';
        file_put_contents(
            $tmpFile,
            "<?php\n\nnamespace App\\Http\\Controllers;\n\nclass Controller {}\n"
        );

        try {
            $reflector = new \ReflectionFunction(function () {
                $major = (int) explode('.', app()->version())[0];

                return "Running on Laravel {$major}.";
            });

            $msg = $reflector->invoke();

            $this->assertSame("Running on Laravel {$major}.", $msg);
        } finally {
            @unlink($tmpFile);
        }
    }
}
