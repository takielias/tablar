<?php

namespace TakiElias\Tablar\Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TakiElias\Tablar\TablarPreset;
use TakiElias\Tablar\TablarServiceProvider;

class InstallOverwritePromptTest extends TestCase
{
    private string $stubPath;

    private string $destPath;

    protected function getPackageProviders($app): array
    {
        return [TablarServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $tmp = sys_get_temp_dir().'/tablar-overwrite-'.bin2hex(random_bytes(4));
        mkdir($tmp, 0o755, true);

        $this->stubPath = $tmp.'/stub.txt';
        $this->destPath = $tmp.'/dest.txt';
        file_put_contents($this->stubPath, "stub content\n");

        TablarPreset::reset();
    }

    protected function tearDown(): void
    {
        @unlink($this->stubPath);
        @unlink($this->destPath);
        @rmdir(dirname($this->stubPath));
        TablarPreset::reset();

        parent::tearDown();
    }

    private function invokeSafeCopy(string $stub, string $dest): bool
    {
        $method = new \ReflectionMethod(TablarPreset::class, 'safeCopy');
        $method->setAccessible(true);

        return (bool) $method->invoke(null, $stub, $dest);
    }

    public function test_writes_when_destination_missing(): void
    {
        $this->assertFileDoesNotExist($this->destPath);

        $written = $this->invokeSafeCopy($this->stubPath, $this->destPath);

        $this->assertTrue($written);
        $this->assertFileExists($this->destPath);
        $this->assertSame("stub content\n", file_get_contents($this->destPath));
    }

    public function test_skips_when_destination_matches_stub(): void
    {
        copy($this->stubPath, $this->destPath);

        $written = $this->invokeSafeCopy($this->stubPath, $this->destPath);

        $this->assertFalse($written, 'Identical file should be skipped silently.');
    }

    private function freshCommand(): Command
    {
        $cmd = new class extends Command
        {
            protected $signature = 'fake:cmd';
        };
        $cmd->setOutput(new OutputStyle(new StringInput(''), new BufferedOutput));

        return $cmd;
    }

    public function test_force_flag_overwrites_user_modified_file(): void
    {
        file_put_contents($this->destPath, "user-modified content\n");

        TablarPreset::useCommand($this->freshCommand(), force: true);

        $written = $this->invokeSafeCopy($this->stubPath, $this->destPath);

        $this->assertTrue($written, '--force should overwrite without prompting.');
        $this->assertSame("stub content\n", file_get_contents($this->destPath));
    }

    public function test_no_force_no_command_keeps_user_changes(): void
    {
        file_put_contents($this->destPath, "user-modified content\n");

        $written = $this->invokeSafeCopy($this->stubPath, $this->destPath);

        $this->assertFalse($written, 'Without command/force, user changes must be preserved.');
        $this->assertSame("user-modified content\n", file_get_contents($this->destPath));
    }

    public function test_install_command_signature_has_force_and_no_credits_flags(): void
    {
        $source = file_get_contents(__DIR__.'/../../src/Console/TablarInstallCommand.php');

        $this->assertStringContainsString('--force', $source);
        $this->assertStringContainsString('--no-credits', $source);
        $this->assertMatchesRegularExpression(
            '/TablarPreset::useCommand\(\s*\$this\s*,\s*force:\s*\(bool\)\s*\$this->option\([\'"]force[\'"]\)\)/',
            $source,
            'handle() must wire the command into the preset with the resolved force option.'
        );
    }
}
