<?php

namespace TakiElias\Tablar\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class AuthScaffoldingTest extends TestCase
{
    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
    }

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_auth_controller_stubs_exist(): void
    {
        $stubsPath = __DIR__ . '/../../src/stubs/controllers/Auth';

        $expectedControllers = [
            'AuthenticatedSessionController.stub',
            'RegisteredUserController.stub',
            'PasswordResetLinkController.stub',
            'NewPasswordController.stub',
            'ConfirmablePasswordController.stub',
            'EmailVerificationPromptController.stub',
            'EmailVerificationNotificationController.stub',
            'VerifyEmailController.stub',
            'PasswordController.stub',
        ];

        foreach ($expectedControllers as $controller) {
            $this->assertFileExists(
                $stubsPath . '/' . $controller,
                "Auth controller stub {$controller} should exist"
            );
        }
    }

    public function test_auth_request_stubs_exist(): void
    {
        $stubsPath = __DIR__ . '/../../src/stubs/requests/Auth';

        $expectedRequests = [
            'LoginRequest.stub',
            'ProfileUpdateRequest.stub',
        ];

        foreach ($expectedRequests as $request) {
            $this->assertFileExists(
                $stubsPath . '/' . $request,
                "Auth request stub {$request} should exist"
            );
        }
    }

    public function test_migration_stub_exists(): void
    {
        $migrationPath = __DIR__ . '/../../src/stubs/migrations/2014_10_12_100000_create_password_resets_table.php';

        $this->assertFileExists($migrationPath, 'Password reset migration should exist');
    }

    public function test_controller_stubs_reference_tablar_views(): void
    {
        $stubsPath = __DIR__ . '/../../src/stubs/controllers/Auth';

        $files = $this->files->allFiles($stubsPath);

        foreach ($files as $file) {
            $content = $this->files->get($file->getPathname());

            // Check that files contain view references
            if (str_contains($content, "view('")) {
                // Ensure they use tablar:: prefix
                $this->assertMatchesRegularExpression(
                    "/view\('tablar::/",
                    $content,
                    "Controller {$file->getFilename()} should reference tablar:: views"
                );

                // Ensure they don't reference auth. directly (laravel/ui pattern)
                $this->assertDoesNotMatchRegularExpression(
                    "/view\('auth\./",
                    $content,
                    "Controller {$file->getFilename()} should not reference auth. views directly"
                );
            }
        }
    }

    public function test_no_laravel_ui_references_in_stubs(): void
    {
        $stubsPath = __DIR__ . '/../../src/stubs/controllers/Auth';

        $files = $this->files->allFiles($stubsPath);

        foreach ($files as $file) {
            $content = $this->files->get($file->getPathname());

            $this->assertStringNotContainsString(
                'Laravel\Ui',
                $content,
                "Controller stub {$file->getFilename()} should not reference Laravel\\Ui"
            );
        }
    }

    public function test_preset_base_class_exists(): void
    {
        $presetPath = __DIR__ . '/../../src/Support/Preset.php';

        $this->assertFileExists($presetPath, 'Internal Preset base class should exist');

        $content = $this->files->get($presetPath);

        // Check for essential methods
        $this->assertStringContainsString('updatePackages', $content);
        $this->assertStringContainsString('removeNodeModules', $content);
        $this->assertStringContainsString('abstract', $content);
    }

    public function test_tablar_preset_uses_internal_preset(): void
    {
        $tablarPresetPath = __DIR__ . '/../../src/TablarPreset.php';

        $content = $this->files->get($tablarPresetPath);

        // Should use internal Preset
        $this->assertStringContainsString(
            'use TakiElias\Tablar\Support\Preset;',
            $content,
            'TablarPreset should use internal Preset class'
        );

        // Should NOT use Laravel\Ui\Presets\Preset
        $this->assertStringNotContainsString(
            'Laravel\Ui\Presets\Preset',
            $content,
            'TablarPreset should not reference Laravel\\Ui\\Presets\\Preset'
        );

        // Should reference internal stubs, not vendor/laravel/ui
        $this->assertStringContainsString(
            "__DIR__ . '/stubs/migrations'",
            $content,
            'TablarPreset should reference internal migration stubs'
        );

        $this->assertStringContainsString(
            "__DIR__ . '/stubs/controllers/Auth'",
            $content,
            'TablarPreset should reference internal controller stubs'
        );

        $this->assertStringNotContainsString(
            'vendor/laravel/ui',
            $content,
            'TablarPreset should not reference vendor/laravel/ui'
        );
    }

    public function test_composer_json_does_not_require_laravel_ui(): void
    {
        $composerPath = __DIR__ . '/../../composer.json';

        $content = $this->files->get($composerPath);
        $composer = json_decode($content, true);

        $this->assertArrayNotHasKey(
            'laravel/ui',
            $composer['require'] ?? [],
            'composer.json should not require laravel/ui'
        );
    }

    public function test_composer_json_has_breeze_conflict(): void
    {
        $composerPath = __DIR__ . '/../../composer.json';

        $content = $this->files->get($composerPath);
        $composer = json_decode($content, true);

        $this->assertArrayHasKey(
            'conflict',
            $composer,
            'composer.json should have conflict section'
        );

        $this->assertArrayHasKey(
            'laravel/breeze',
            $composer['conflict'] ?? [],
            'composer.json should conflict with laravel/breeze'
        );
    }

    public function test_auth_routes_stub_exists(): void
    {
        $authRoutesPath = __DIR__ . '/../../src/stubs/routes/auth.php';

        $this->assertFileExists($authRoutesPath, 'Auth routes stub should exist');

        $content = $this->files->get($authRoutesPath);

        // Should not use Auth::routes()
        $this->assertStringNotContainsString(
            'Auth::routes()',
            $content,
            'Auth routes should not use Auth::routes()'
        );

        // Should define explicit routes
        $this->assertStringContainsString('Route::get(\'login\'', $content);
        $this->assertStringContainsString('Route::post(\'login\'', $content);
        $this->assertStringContainsString('Route::get(\'register\'', $content);
        $this->assertStringContainsString('Route::post(\'register\'', $content);
        $this->assertStringContainsString('Route::post(\'logout\'', $content);

        // Should reference new controllers
        $this->assertStringContainsString('AuthenticatedSessionController', $content);
        $this->assertStringContainsString('RegisteredUserController', $content);
        $this->assertStringContainsString('PasswordResetLinkController', $content);
    }

    public function test_scaffold_auth_does_not_use_auth_routes(): void
    {
        $tablarPresetPath = __DIR__ . '/../../src/TablarPreset.php';

        $content = $this->files->get($tablarPresetPath);

        // Should not append Auth::routes() to web.php
        $this->assertStringNotContainsString(
            'Auth::routes()',
            $content,
            'TablarPreset should not use Auth::routes()'
        );

        // Should copy auth routes file
        $this->assertStringContainsString(
            "'/stubs/routes/auth.php'",
            $content,
            'TablarPreset should copy auth routes file'
        );

        // Should require auth.php in web.php
        $this->assertStringContainsString(
            "require __DIR__.'/auth.php'",
            $content,
            'TablarPreset should add require for auth routes'
        );
    }
}
