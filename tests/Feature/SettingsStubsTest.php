<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the Settings stubs (controller, FormRequests, view, migration,
 * routes) shipped by `tablar:install`. Source-only checks; runtime
 * coverage lives in the Playwright e2e suite.
 */
class SettingsStubsTest extends TestCase
{
    private const STUBS = __DIR__.'/../../src/stubs';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_settings_controller_stub_exists(): void
    {
        $this->assertFileExists(self::STUBS.'/controllers/SettingsController.stub');
    }

    public function test_settings_controller_uses_form_requests(): void
    {
        $source = file_get_contents(self::STUBS.'/controllers/SettingsController.stub');

        $this->assertStringContainsString('class SettingsController extends Controller', $source);
        $this->assertStringContainsString('use App\Http\Requests\DeleteAccountRequest;', $source);
        $this->assertStringContainsString('use App\Http\Requests\UpdatePasswordRequest;', $source);
        $this->assertMatchesRegularExpression('/public function show\(\):\s*View/', $source);
        $this->assertMatchesRegularExpression(
            '/public function updatePassword\(\s*UpdatePasswordRequest \$request\s*\):\s*RedirectResponse/',
            $source,
        );
        $this->assertMatchesRegularExpression(
            '/public function destroy\(\s*DeleteAccountRequest \$request\s*\):\s*RedirectResponse/',
            $source,
        );
    }

    public function test_settings_destroy_does_soft_delete_via_user_delete(): void
    {
        $source = file_get_contents(self::STUBS.'/controllers/SettingsController.stub');

        // Soft delete relies on the SoftDeletes trait on App\Models\User —
        // calling $user->delete() will then mark deleted_at instead of
        // hard-removing the row. So the controller should call delete(),
        // not forceDelete().
        $this->assertStringContainsString('$user->delete();', $source);
        $this->assertStringNotContainsString('forceDelete', $source);

        // Standard logout + session invalidation flow.
        $this->assertStringContainsString('Auth::logout();', $source);
        $this->assertStringContainsString('session()->invalidate()', $source);
        $this->assertStringContainsString('session()->regenerateToken()', $source);
    }

    public function test_update_password_request_validates_current_and_confirmed(): void
    {
        $source = file_get_contents(self::STUBS.'/requests/UpdatePasswordRequest.stub');

        $this->assertStringContainsString('class UpdatePasswordRequest extends FormRequest', $source);
        $this->assertStringContainsString("'current_password' => ['required', 'current_password']", $source);
        $this->assertStringContainsString("'password' => ['required', 'confirmed', Password::defaults()]", $source);
    }

    public function test_delete_account_request_requires_current_password(): void
    {
        $source = file_get_contents(self::STUBS.'/requests/DeleteAccountRequest.stub');

        $this->assertStringContainsString('class DeleteAccountRequest extends FormRequest', $source);
        $this->assertStringContainsString("'password' => ['required', 'current_password']", $source);
    }

    public function test_settings_view_stub_renders_three_cards(): void
    {
        $path = self::STUBS.'/resources/views/settings/show.blade.php';

        $this->assertFileExists($path);

        $source = file_get_contents($path);

        $this->assertStringContainsString("@extends('tablar::page')", $source);
        $this->assertStringContainsString("__('Appearance')", $source);
        $this->assertStringContainsString("__('Update password')", $source);
        $this->assertStringContainsString("__('Delete account')", $source);
        $this->assertStringContainsString("@include('tablar::partials.settings.appearance')", $source);
        $this->assertStringContainsString("route('settings.password')", $source);
        $this->assertStringContainsString("route('settings.destroy')", $source);
        $this->assertStringContainsString('border-danger', $source, 'Delete card uses border-danger styling.');
    }

    public function test_soft_deletes_migration_exists(): void
    {
        $path = self::STUBS.'/migrations/2014_10_12_100000_add_soft_deletes_to_users_table.php';

        $this->assertFileExists($path);

        $source = file_get_contents($path);

        $this->assertStringContainsString('Schema::table(\'users\'', $source);
        $this->assertStringContainsString('$table->softDeletes()', $source);
        $this->assertStringContainsString('$table->dropSoftDeletes()', $source);
    }

    public function test_appearance_partial_exists(): void
    {
        $path = __DIR__.'/../../resources/views/partials/settings/appearance.blade.php';

        $this->assertFileExists($path);

        $source = file_get_contents($path);

        foreach (['light', 'dark', 'auto'] as $value) {
            $this->assertStringContainsString('data-bs-theme-value="'.$value.'"', $source);
        }
        $this->assertStringContainsString("localStorage.setItem('tablar.theme'", $source);
        $this->assertStringContainsString("'tablar:theme-change'", $source);
    }

    public function test_auth_routes_register_settings_routes_under_auth_middleware(): void
    {
        $source = file_get_contents(self::STUBS.'/routes/auth.php');

        $this->assertStringContainsString('use App\Http\Controllers\SettingsController;', $source);
        $this->assertStringContainsString(
            "Route::get('settings', [SettingsController::class, 'show'])->name('settings')",
            $source,
        );
        $this->assertStringContainsString(
            "Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password')",
            $source,
        );
        $this->assertStringContainsString(
            "Route::delete('settings', [SettingsController::class, 'destroy'])->name('settings.destroy')",
            $source,
        );

        $authGroupStart = strpos($source, "Route::middleware('auth')->group(function () {");
        $settingsRoutePos = strpos($source, "Route::get('settings'");

        $this->assertNotFalse($authGroupStart);
        $this->assertNotFalse($settingsRoutePos);
        $this->assertGreaterThan($authGroupStart, $settingsRoutePos);
    }

    public function test_preset_publishes_settings_stubs(): void
    {
        $preset = file_get_contents(__DIR__.'/../../src/TablarPreset.php');

        $this->assertStringContainsString("'SettingsController.stub' => app_path('Http/Controllers/SettingsController.php')", $preset);
        $this->assertStringContainsString("'UpdatePasswordRequest.stub' => app_path('Http/Requests/UpdatePasswordRequest.php')", $preset);
        $this->assertStringContainsString("'DeleteAccountRequest.stub' => app_path('Http/Requests/DeleteAccountRequest.php')", $preset);
    }
}
