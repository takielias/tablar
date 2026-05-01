<?php

namespace TakiElias\Tablar\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TakiElias\Tablar\Console\TablarInstallCommand;
use TakiElias\Tablar\TablarServiceProvider;

/**
 * Locks the User-model patcher in `TablarInstallCommand`. The patch
 * adds the `SoftDeletes` trait import and trait usage idempotently —
 * running twice must not produce duplicate imports or trait listings.
 */
class UserSoftDeletePatchTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    private function command(): TablarInstallCommand
    {
        return new TablarInstallCommand;
    }

    private function l11UserModel(): string
    {
        return <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
PHP;
    }

    public function test_adds_softdeletes_import_and_trait_to_l11_user(): void
    {
        $patched = $this->command()->patchUserModelSource($this->l11UserModel());

        $this->assertNotNull($patched);
        $this->assertStringContainsString('use Illuminate\Database\Eloquent\SoftDeletes;', $patched);
        $this->assertStringContainsString('use HasFactory, Notifiable, SoftDeletes;', $patched);
    }

    public function test_patch_is_idempotent(): void
    {
        $first = $this->command()->patchUserModelSource($this->l11UserModel());
        $this->assertNotNull($first);

        $second = $this->command()->patchUserModelSource($first);

        $this->assertNull($second, 'Re-running the patch on already-patched source must return null.');
    }

    public function test_does_not_duplicate_trait_when_only_import_missing(): void
    {
        $source = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
}
PHP;

        $patched = $this->command()->patchUserModelSource($source);

        $this->assertNotNull($patched);
        $this->assertStringContainsString('use Illuminate\Database\Eloquent\SoftDeletes;', $patched);
        // Trait listing should remain unchanged — already had SoftDeletes.
        $this->assertSame(
            1,
            substr_count($patched, 'use HasFactory, Notifiable, SoftDeletes;'),
        );
    }

    public function test_returns_null_when_no_user_class_present(): void
    {
        $patched = $this->command()->patchUserModelSource('<?php // not a model');

        $this->assertNull($patched);
    }
}
