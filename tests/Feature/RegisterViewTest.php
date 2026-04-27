<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\TablarServiceProvider;

class RegisterViewTest extends TestCase
{
    private const PUBLISHED_VIEW = __DIR__.'/../../resources/views/auth/register.blade.php';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function source(): string
    {
        return file_get_contents(self::PUBLISHED_VIEW);
    }

    public function test_form_targets_register_route(): void
    {
        $source = $this->source();

        $this->assertStringContainsString("route('register')", $source);
        $this->assertStringContainsString('@csrf', $source);
        $this->assertStringContainsString('Create new account', $source);
    }

    public function test_password_and_confirmation_fields_present(): void
    {
        $source = $this->source();

        $this->assertStringContainsString('name="password"', $source);
        $this->assertStringContainsString('name="password_confirmation"', $source);
        $this->assertStringContainsString('name="email"', $source);
        $this->assertStringContainsString('name="name"', $source);
    }

    public function test_password_toggle_wired_on_both_password_inputs(): void
    {
        $source = $this->source();

        // The toggle anchor should appear once for password + once for password_confirmation.
        $toggleCount = substr_count($source, 'data-password-toggle');
        $this->assertSame(2, $toggleCount, 'Both password fields must carry a data-password-toggle eye anchor.');

        $this->assertStringContainsString('data-icon-show', $source);
        $this->assertStringContainsString('data-icon-hide', $source);
    }

    public function test_no_social_login_buttons(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('Login with Github', $source);
        $this->assertStringNotContainsString('Login with Twitter', $source);
        $this->assertStringNotContainsString('brand-github', $source);
        $this->assertStringNotContainsString('brand-twitter', $source);
    }

    public function test_no_terms_and_policy_checkbox(): void
    {
        $source = $this->source();

        $this->assertStringNotContainsString('terms and policy', $source, 'Drop the placeholder ToS checkbox — the package does not ship a terms page.');
        $this->assertStringNotContainsString('Agree the', $source);
        $this->assertStringNotContainsString('type="checkbox"', $source, 'Register form should not render any checkboxes by default.');
    }
}
