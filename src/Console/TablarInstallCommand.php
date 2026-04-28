<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;
use TakiElias\Tablar\TablarPreset;

class TablarInstallCommand extends Command
{
    protected $signature = 'tablar:install
        {--force : Overwrite user-modified files without prompting}
        {--no-credits : Suppress the GitHub-star credits line}';

    protected $description = 'Install Tablar scaffolding and export config';

    public function handle(): int
    {
        TablarPreset::useCommand($this, force: (bool) $this->option('force'));

        TablarPreset::install();
        TablarPreset::exportConfig();

        $this->checkController();
        $this->patchUserModelForSoftDeletes();

        $major = (int) explode('.', app()->version())[0];

        $this->newLine();
        $this->info("✅ Tablar installed (Laravel {$major}).");
        $this->line('Next: npm install && npm run dev');
        $this->line('Then: php artisan tablar:export-auth');

        if (! $this->option('no-credits')) {
            $this->newLine();
            $this->line('⭐️  Star us on GitHub: https://github.com/takielias/tablar');
        }

        return self::SUCCESS;
    }

    protected function patchUserModelForSoftDeletes(): void
    {
        $filePath = app_path('Models/User.php');

        if (! file_exists($filePath)) {
            return;
        }

        $source = file_get_contents($filePath);
        $patched = $this->patchUserModelSource($source);

        if ($patched === null) {
            return;
        }

        file_put_contents($filePath, $patched);
        $this->info('Added SoftDeletes trait to App\\Models\\User.');
    }

    protected function checkController(): void
    {
        $major = (int) explode('.', app()->version())[0];

        if ($major < 11) {
            return;
        }

        $this->info("Running on Laravel {$major}.");

        $filePath = app_path('Http/Controllers/Controller.php');
        $fileContents = file_get_contents($filePath);

        $result = $this->patchControllerSource($fileContents);
        $newBaseClass = self::TARGET_BASE_CONTROLLER;

        if ($result === null) {
            $this->info("No changes made. The Controller class already extends {$newBaseClass}.");

            return;
        }

        file_put_contents($filePath, $result);
        $this->info("The Controller class has been modified to extend {$newBaseClass}.");
    }

    public const TARGET_BASE_CONTROLLER = '\\Illuminate\\Routing\\Controller';

    public const SOFT_DELETES_IMPORT = 'use Illuminate\\Database\\Eloquent\\SoftDeletes;';

    /**
     * Patch an `app/Models/User.php` source so the model uses the
     * SoftDeletes trait. Idempotent: returns null when the import + trait
     * are already present.
     *
     * Handles both single-line and multi-line trait usages, e.g.
     *   `use HasFactory, Notifiable;`
     *   `use HasFactory;\n    use Notifiable;`
     */
    public function patchUserModelSource(string $source): ?string
    {
        $hasImport = str_contains($source, self::SOFT_DELETES_IMPORT);
        $hasTraitUse = (bool) preg_match('/use\s+[^;]*\bSoftDeletes\b[^;]*;/', $this->classBody($source));

        if ($hasImport && $hasTraitUse) {
            return null;
        }

        $patched = $source;

        if (! $hasImport) {
            $patched = preg_replace_callback(
                '/^(use\s+[^;]+;\n)(?!use\s+)/m',
                function (array $m): string {
                    return $m[1].self::SOFT_DELETES_IMPORT."\n";
                },
                $patched,
                1,
                $count
            );

            if ($patched === null || $count === 0) {
                return null;
            }
        }

        if (! $hasTraitUse) {
            $patched = preg_replace_callback(
                '/(class\s+User\s+extends\s+[^\{]+\{)([\s\S]*?)(use\s+[^;]+;)/',
                function (array $m): string {
                    $existing = $m[3];

                    if (preg_match('/use\s+([^;]+);/', $existing, $traitMatch)) {
                        $traits = preg_split('/\s*,\s*/', trim($traitMatch[1]));
                        if (! in_array('SoftDeletes', $traits, true)) {
                            $traits[] = 'SoftDeletes';
                        }
                        $newUse = 'use '.implode(', ', $traits).';';

                        return $m[1].$m[2].$newUse;
                    }

                    return $m[0];
                },
                $patched,
                1,
                $count
            );

            if ($patched === null || $count === 0) {
                return null;
            }
        }

        return $patched;
    }

    private function classBody(string $source): string
    {
        if (preg_match('/class\s+User\s+extends\s+[^\{]+\{([\s\S]*)\}/', $source, $m)) {
            return $m[1];
        }

        return $source;
    }

    /**
     * Patch an `app/Http/Controllers/Controller.php` source so it extends
     * \Illuminate\Routing\Controller. Returns null when no change is needed
     * (already extending that base, or no `class Controller` declaration).
     *
     * Handles all post-L10 streamlined skeletons:
     *   - `class Controller {}`           (legacy)
     *   - `abstract class Controller {}`  (L11/L12/L13 default — `abstract` preserved)
     *   - `class Controller extends X {}` (already-extending other class — overwritten)
     */
    public function patchControllerSource(string $source): ?string
    {
        $target = self::TARGET_BASE_CONTROLLER;

        if (str_contains($source, 'extends '.$target)) {
            return null;
        }

        $patched = preg_replace_callback(
            '/(?P<prefix>(?:abstract\s+)?)class\s+Controller\s*(?:extends\s+\\\\?[a-zA-Z0-9_\\\\]+)?\s*\{/',
            fn (array $m): string => "{$m['prefix']}class Controller extends {$target}\n{",
            $source,
            1,
            $count
        );

        if ($patched === null || $count === 0) {
            return null;
        }

        return $patched;
    }
}
