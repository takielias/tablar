<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the Profile stubs (controller, FormRequest, route, view) shipped
 * by `tablar:install`. Stubs land in the user's app, so this test checks
 * the source files in the package's stubs/ tree — runtime behavior is
 * covered by the Playwright e2e suite against the live demo.
 */
class ProfileStubsTest extends TestCase
{
    private const STUBS = __DIR__.'/../../src/stubs';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    public function test_profile_controller_stub_exists(): void
    {
        $this->assertFileExists(self::STUBS.'/controllers/ProfileController.stub');
    }

    public function test_profile_controller_extends_base_and_uses_form_request(): void
    {
        $source = file_get_contents(self::STUBS.'/controllers/ProfileController.stub');

        $this->assertStringContainsString('class ProfileController extends Controller', $source);
        $this->assertStringContainsString('use App\Http\Requests\UpdateProfileRequest;', $source);
        $this->assertMatchesRegularExpression('/public function show\(.*?\):\s*View/', $source);
        $this->assertMatchesRegularExpression(
            '/public function update\(\s*UpdateProfileRequest \$request\s*\):\s*RedirectResponse/',
            $source,
        );
        $this->assertStringContainsString("with('status', 'profile-updated')", $source);
    }

    public function test_update_profile_request_stub_exists(): void
    {
        $this->assertFileExists(self::STUBS.'/requests/UpdateProfileRequest.stub');
    }

    public function test_update_profile_request_validates_name_only(): void
    {
        $source = file_get_contents(self::STUBS.'/requests/UpdateProfileRequest.stub');

        $this->assertStringContainsString('class UpdateProfileRequest extends FormRequest', $source);
        $this->assertStringContainsString("'name' => ['required', 'string', 'max:255']", $source);

        // Email is intentionally NOT in rules — read-only on the profile form.
        $this->assertStringNotContainsString("'email'", $source);
    }

    public function test_profile_view_stub_exists_and_extends_tablar_page(): void
    {
        $path = self::STUBS.'/resources/views/profile/show.blade.php';

        $this->assertFileExists($path);

        $source = file_get_contents($path);

        $this->assertStringContainsString("@extends('tablar::page')", $source);
        $this->assertStringContainsString("route('profile.update')", $source);
        $this->assertStringContainsString("@method('PATCH')", $source);
        $this->assertStringContainsString('name="name"', $source);
        $this->assertStringContainsString('readonly', $source, 'Email field must be read-only.');
        $this->assertStringContainsString("session('status') === 'profile-updated'", $source);
    }

    public function test_auth_routes_register_profile_routes_under_auth_middleware(): void
    {
        $source = file_get_contents(self::STUBS.'/routes/auth.php');

        $this->assertStringContainsString('use App\Http\Controllers\ProfileController;', $source);
        $this->assertStringContainsString(
            "Route::get('profile', [ProfileController::class, 'show'])->name('profile')",
            $source,
        );
        $this->assertStringContainsString(
            "Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update')",
            $source,
        );

        // Confirm routes are inside the auth middleware group.
        $authGroupStart = strpos($source, "Route::middleware('auth')->group(function () {");
        $profileRoutePos = strpos($source, "Route::get('profile'");

        $this->assertNotFalse($authGroupStart);
        $this->assertNotFalse($profileRoutePos);
        $this->assertGreaterThan($authGroupStart, $profileRoutePos, 'Profile routes must be inside the auth middleware group.');
    }

    public function test_preset_publishes_profile_stubs(): void
    {
        $preset = file_get_contents(__DIR__.'/../../src/TablarPreset.php');

        $this->assertStringContainsString('scaffoldProfileAndSettings', $preset);
        $this->assertStringContainsString("'ProfileController.stub' => app_path('Http/Controllers/ProfileController.php')", $preset);
        $this->assertStringContainsString("'UpdateProfileRequest.stub' => app_path('Http/Requests/UpdateProfileRequest.php')", $preset);
    }
}
