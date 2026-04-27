<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\Console\TablarInstallCommand;
use TakiElias\Tablar\TablarServiceProvider;

class ControllerPatchTest extends TestCase
{
    private const FIXTURES = __DIR__.'/../Fixtures/Controllers';

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function command(): TablarInstallCommand
    {
        return new TablarInstallCommand;
    }

    private function fixture(string $name): string
    {
        return file_get_contents(self::FIXTURES."/{$name}.stub.php");
    }

    public function test_patches_l11_default_controller_preserving_abstract(): void
    {
        $patched = $this->command()->patchControllerSource($this->fixture('L11'));

        $this->assertNotNull($patched, 'L11 fixture should be patched');
        $this->assertStringContainsString(
            'abstract class Controller extends \Illuminate\Routing\Controller',
            $patched,
            'abstract keyword must be preserved'
        );
    }

    public function test_patches_l12_default_controller(): void
    {
        $patched = $this->command()->patchControllerSource($this->fixture('L12'));

        $this->assertNotNull($patched);
        $this->assertStringContainsString(
            'abstract class Controller extends \Illuminate\Routing\Controller',
            $patched
        );
    }

    public function test_patches_l13_default_controller(): void
    {
        $patched = $this->command()->patchControllerSource($this->fixture('L13'));

        $this->assertNotNull($patched);
        $this->assertStringContainsString(
            'abstract class Controller extends \Illuminate\Routing\Controller',
            $patched
        );
    }

    public function test_patches_l10_legacy_controller_overwriting_base(): void
    {
        $patched = $this->command()->patchControllerSource($this->fixture('L10-legacy'));

        $this->assertNotNull($patched, 'Legacy L10 controller should be repointed to fully-qualified base');
        $this->assertStringContainsString(
            'class Controller extends \Illuminate\Routing\Controller',
            $patched
        );
        $this->assertStringNotContainsString('extends BaseController', $patched);
    }

    public function test_already_patched_returns_null(): void
    {
        $patched = $this->command()->patchControllerSource($this->fixture('already-patched'));

        $this->assertNull($patched, 'Already-patched controller must short-circuit to null (idempotent).');
    }

    public function test_patch_is_idempotent_across_two_passes(): void
    {
        $cmd = $this->command();
        $first = $cmd->patchControllerSource($this->fixture('L13'));

        $this->assertNotNull($first);

        $second = $cmd->patchControllerSource($first);

        $this->assertNull($second, 'Second pass must be a no-op');
    }

    public function test_unknown_class_shape_returns_null(): void
    {
        $weird = "<?php\n\nnamespace App\\Http\\Controllers;\n\nfinal class SomethingElse {}\n";
        $patched = $this->command()->patchControllerSource($weird);

        $this->assertNull($patched, 'Files without a Controller declaration must not be touched');
    }
}
