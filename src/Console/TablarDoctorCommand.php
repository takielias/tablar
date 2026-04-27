<?php

namespace TakiElias\Tablar\Console;

use Illuminate\Console\Command;

/**
 * `php artisan tablar:doctor` — one-shot environment snapshot.
 *
 * Prints a colour-coded report of the runtime stack so a reviewer can
 * verify the project is on the modern target (PHP 8.3+, Laravel 11+,
 * Vite 8, Node 22) without grepping through composer.lock or
 * package-lock.json by hand. Exit code:
 *   0 — all required components present and at the supported floor
 *   1 — at least one critical check failed (missing Vite manifest,
 *       PHP < 8.3, Laravel < 11, etc.)
 */
class TablarDoctorCommand extends Command
{
    protected $signature = 'tablar:doctor';

    protected $description = 'Report the Tablar runtime stack and surface stale-toolchain issues.';

    private const REQUIRED_PHP = '8.3.0';

    private const REQUIRED_LARAVEL_MAJOR = 11;

    public function handle(): int
    {
        $rows = [
            $this->checkPhp(),
            $this->checkLaravel(),
            $this->checkTablar(),
            $this->checkNode(),
            $this->checkNpm(),
            $this->checkVite(),
            $this->checkTablerCore(),
            $this->checkDbDriver(),
            $this->checkViteManifest(),
        ];

        $this->line('Tablar Doctor');
        $this->line('─────────────');

        $hasFailure = false;
        foreach ($rows as $row) {
            $this->renderRow($row);
            if ($row['status'] === 'fail') {
                $hasFailure = true;
            }
        }

        return $hasFailure ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkPhp(): array
    {
        $current = PHP_VERSION;
        $ok = version_compare($current, self::REQUIRED_PHP, '>=');

        return [
            'label' => 'PHP',
            'value' => $current,
            'status' => $ok ? 'ok' : 'fail',
        ];
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkLaravel(): array
    {
        $version = app()->version();
        $major = (int) explode('.', $version)[0];
        $ok = $major >= self::REQUIRED_LARAVEL_MAJOR;

        return [
            'label' => 'Laravel',
            'value' => $version,
            'status' => $ok ? 'ok' : 'fail',
        ];
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkTablar(): array
    {
        $version = $this->packageVersion('takielias/tablar') ?? 'unknown';

        return [
            'label' => 'Tablar',
            'value' => $version,
            'status' => $version === 'unknown' ? 'warn' : 'ok',
        ];
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkNode(): array
    {
        return $this->shellOutVersion('Node', 'node --version');
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkNpm(): array
    {
        return $this->shellOutVersion('npm', 'npm --version');
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkVite(): array
    {
        return $this->nodeModuleVersion('Vite', 'vite');
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkTablerCore(): array
    {
        return $this->nodeModuleVersion('@tabler/core', '@tabler/core');
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkDbDriver(): array
    {
        $driver = config('database.default') ?? 'unknown';

        return [
            'label' => 'DB driver',
            'value' => (string) $driver,
            'status' => $driver !== 'unknown' ? 'ok' : 'warn',
        ];
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function checkViteManifest(): array
    {
        $candidates = [
            public_path('build/manifest.json'),
            public_path('build/.vite/manifest.json'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return ['label' => 'Vite manifest', 'value' => 'present', 'status' => 'ok'];
            }
        }

        return [
            'label' => 'Vite manifest',
            'value' => 'missing — run `npm run build`',
            'status' => 'fail',
        ];
    }

    private function packageVersion(string $package): ?string
    {
        $lockPath = base_path('composer.lock');
        if (! file_exists($lockPath)) {
            return null;
        }

        $lock = json_decode((string) file_get_contents($lockPath), true);
        foreach (array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []) as $entry) {
            if (($entry['name'] ?? null) === $package) {
                return (string) ($entry['version'] ?? 'unknown');
            }
        }

        return null;
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function shellOutVersion(string $label, string $command): array
    {
        $output = [];
        $exit = 0;
        @exec($command.' 2>/dev/null', $output, $exit);

        $value = $exit === 0 && isset($output[0]) ? trim($output[0]) : 'not found';

        return [
            'label' => $label,
            'value' => $value,
            'status' => $exit === 0 ? 'ok' : 'warn',
        ];
    }

    /**
     * @return array{label: string, value: string, status: string}
     */
    private function nodeModuleVersion(string $label, string $module): array
    {
        $pkgPath = base_path("node_modules/{$module}/package.json");
        if (! file_exists($pkgPath)) {
            return ['label' => $label, 'value' => 'not installed', 'status' => 'warn'];
        }

        $manifest = json_decode((string) file_get_contents($pkgPath), true);
        $version = $manifest['version'] ?? 'unknown';

        return ['label' => $label, 'value' => (string) $version, 'status' => 'ok'];
    }

    /**
     * @param  array{label: string, value: string, status: string}  $row
     */
    private function renderRow(array $row): void
    {
        $marker = match ($row['status']) {
            'ok' => '<fg=green>✓</>',
            'warn' => '<fg=yellow>⚠</>',
            'fail' => '<fg=red>✗</>',
            default => '?',
        };

        $this->line(sprintf(
            '%-15s %-30s %s',
            $row['label'],
            $row['value'],
            $marker
        ));
    }
}
