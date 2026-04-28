<?php

namespace TakiElias\Tablar;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SplFileInfo;
use TakiElias\Tablar\Support\Preset;

class TablarPreset extends Preset
{
    protected static ?Command $command = null;

    protected static bool $force = false;

    /**
     * Wire the preset to a console command so safeCopy() can prompt the
     * user, and pre-set the force flag for non-interactive overwrites.
     */
    public static function useCommand(Command $command, bool $force = false): void
    {
        static::$command = $command;
        static::$force = $force;
    }

    public static function reset(): void
    {
        static::$command = null;
        static::$force = false;
    }

    /**
     * Copy a stub file to a destination, respecting user changes.
     *
     * Returns true if the file was written, false if it was skipped.
     *
     * - Destination missing → write silently
     * - Destination exists & content matches stub → skip silently (already in sync)
     * - Destination exists & user-modified → if force OR user confirms, overwrite; otherwise skip
     */
    protected static function safeCopy(string $stub, string $destination): bool
    {
        if (! file_exists($destination)) {
            copy($stub, $destination);

            return true;
        }

        if (hash_file('sha256', $stub) === hash_file('sha256', $destination)) {
            return false;
        }

        if (static::$force) {
            copy($stub, $destination);
            static::$command?->info("Overwrote user-modified {$destination}.");

            return true;
        }

        $relative = str_replace(base_path().'/', '', $destination);
        $confirmed = static::$command?->confirm(
            "Overwrite user-modified {$relative}?",
            false
        ) ?? false;

        if ($confirmed) {
            copy($stub, $destination);
            static::$command?->info("Overwrote {$relative}.");

            return true;
        }

        static::$command?->warn("Kept user changes in {$relative}.");

        return false;
    }

    /**
     * Install the preset.
     */
    public static function install(): void
    {
        static::updatePackages();
        static::updateAssets();
        static::updateBootstrapping();
        static::updateWelcomePage();
        static::removeNodeModules();
    }

    /**
     * Update the preset.
     */
    public static function update(): void
    {
        static::updatePackages();
        static::updateAssets();
        static::removeNodeModules();
    }

    /**
     * Install the preset and auth views.
     */
    public static function exportAuth(): void
    {
        static::scaffoldController();
        static::scaffoldProfileAndSettings();
        static::scaffoldAuth();
    }

    /**
     * Publish the Profile + Settings controller, request, and view stubs.
     *
     * Stubs land alongside the user's existing Breeze-style auth controllers
     * so the dropdown links to /profile and /settings resolve immediately
     * after `tablar:install`.
     */
    protected static function scaffoldProfileAndSettings(): void
    {
        $filesystem = new Filesystem;

        $controllerStubs = [
            'ProfileController.stub' => app_path('Http/Controllers/ProfileController.php'),
            'SettingsController.stub' => app_path('Http/Controllers/SettingsController.php'),
        ];

        foreach ($controllerStubs as $stub => $destination) {
            $source = __DIR__.'/stubs/controllers/'.$stub;

            if (! file_exists($source)) {
                continue;
            }

            $filesystem->ensureDirectoryExists(dirname($destination));
            static::safeCopy($source, $destination);
        }

        $requestStubs = [
            'UpdateProfileRequest.stub' => app_path('Http/Requests/UpdateProfileRequest.php'),
            'UpdatePasswordRequest.stub' => app_path('Http/Requests/UpdatePasswordRequest.php'),
            'DeleteAccountRequest.stub' => app_path('Http/Requests/DeleteAccountRequest.php'),
        ];

        foreach ($requestStubs as $stub => $destination) {
            $source = __DIR__.'/stubs/requests/'.$stub;

            if (! file_exists($source)) {
                continue;
            }

            $filesystem->ensureDirectoryExists(dirname($destination));
            static::safeCopy($source, $destination);
        }
    }

    /**
     * Export the Js files.
     */
    public static function exportJs(): void
    {
        /**
         * static::updateAssets();
         */
        (new Filesystem)->copyDirectory(__DIR__.'/stubs/resources/js', static::getResourcePath().'/js');
    }

    /**
     * Install the preset and auth views.
     */
    public static function exportConfig(): void
    {
        static::scaffoldConfig();
    }

    /**
     * Export the Config file.
     */
    protected static function scaffoldConfig(): void
    {
        copy(__DIR__.'../../config/tablar.php', base_path('config/tablar.php'));
    }

    /**
     * Install the preset and auth views.
     */
    public static function exportAllView(): void
    {
        static::scaffoldAllView();
    }

    /**
     * Export the Config file.
     */
    protected static function scaffoldAllView(): void
    {
        (new Filesystem)->copyDirectory(__DIR__.'/../resources/views', static::getResourcePath('views/vendor/tablar'));

    }

    /**
     * Export Tabler assets
     */
    public static function exportAssets(): void
    {

        tap(new Filesystem, function ($filesystem) {
            collect($filesystem->allFiles(base_path('node_modules/@tabler/icons-webfont/dist/fonts')))
                ->each(function (SplFileInfo $file) use ($filesystem) {
                    $filesystem->copy(
                        $file->getPathname(),
                        public_path('fonts/'.$file->getFilename())
                    );
                });
        });

    }

    /**
     * Update the given package array.
     */
    protected static function updatePackageArray(array $packages, string $configurationKey = 'devDependencies'): array
    {
        return array_merge([
            'jquery' => '^4.0.0',
            'bootstrap' => '5.3.8',
            '@tabler/core' => '1.4.0',
            '@popperjs/core' => '^2.11.8',
            '@tabler/icons' => '^3.41.0',
            '@tabler/icons-webfont' => '^3.41.0',
            'apexcharts' => '^5.10.0',
            'countup.js' => '^2.9.0',
            'dropzone' => '^6.0.0-beta.2',
            'autosize' => '^6.0.1',
            'star-rating.js' => '^4.3.1',
            'fslightbox' => '^3.7.4',
            'jsvectormap' => '^1.7.0',
            'fullcalendar' => '^6.1.19',
            'signature_pad' => '^5.1.1',
            'list.js' => '^2.3.1',
            'litepicker' => '^2.0.12',
            'nouislider' => '^15.8.1',
            'plyr' => '^3.8.3',
            'tom-select' => '^2.4.3',
            '@melloware/coloris' => '^0.25.0',
            'typed.js' => '^3.0.0',
            'imask' => '^7.6.1',
            'laravel-vite-plugin' => '^3.0.0',
            'sass-embedded' => '^1.99.0',
            'vite' => '^8.0.0',
            'axios' => '^1.7.4',
            'vite-plugin-static-copy' => '^4.0.0',
        ], Arr::except($packages, [
            'axios',
            'choices.js',
            'laravel-vite-plugin',
            'postcss',
            'sass',
            'sass-loader',
            'select2',
            'vite-plugin-static-copy',
            'vite',
        ]));
    }

    /**
     * Update the Sass files for the application.
     */
    protected static function updateAssets(): void
    {
        tap(new Filesystem, function ($filesystem) {

            $filesystem->delete(public_path('js/app.js'));
            $filesystem->delete(public_path('css/app.css'));

            if (! $filesystem->isDirectory($directory = public_path('assets'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            $filesystem->copyDirectory(__DIR__.'/stubs/assets', public_path('assets'));

            if (! $filesystem->isDirectory($directory = public_path('fonts'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            if (! $filesystem->isDirectory($directory = resource_path('css'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            if (! $filesystem->isDirectory($directory = resource_path('sass'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            if (! $filesystem->isDirectory($directory = resource_path('views'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

        });
    }

    /**
     * Update the bootstrapping files.
     */
    protected static function updateBootstrapping(): void
    {
        static::safeCopy(__DIR__.'/stubs/vite.config.js', base_path('vite.config.js'));
        (new Filesystem)->copyDirectory(__DIR__.'/stubs/resources', static::getResourcePath());
    }

    /**
     * Export the authentication views.
     */
    protected static function scaffoldAuth(): void
    {
        file_put_contents(app_path('Http/Controllers/HomeController.php'), static::compileControllerStub());

        // Copy auth routes file
        copy(__DIR__.'/stubs/routes/auth.php', base_path('routes/auth.php'));

        // Add route includes to web.php
        file_put_contents(
            base_path('routes/web.php'),
            "require __DIR__.'/auth.php';\n\nRoute::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');\n\n",
            FILE_APPEND
        );

        tap(new Filesystem, function ($filesystem) {
            $filesystem->copyDirectory(__DIR__.'/stubs/resources/views', resource_path('views'));

            collect($filesystem->allFiles(__DIR__.'/stubs/migrations'))
                ->each(function (SplFileInfo $file) use ($filesystem) {
                    $filesystem->copy(
                        $file->getPathname(),
                        database_path('migrations/'.$file->getFilename())
                    );
                });
        });
    }

    /**
     * Export Home & Auth controllers
     */
    protected static function scaffoldController(): void
    {
        // Ensure Auth controllers directory exists
        if (! is_dir($directory = app_path('Http/Controllers/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        // Copy auth controllers from our stubs
        collect($filesystem->allFiles(__DIR__.'/stubs/controllers/Auth'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        // Ensure Auth requests directory exists
        if (! is_dir($directory = app_path('Http/Requests/Auth'))) {
            mkdir($directory, 0755, true);
        }

        // Copy request classes from our stubs
        collect($filesystem->allFiles(__DIR__.'/stubs/requests/Auth'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Requests/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });
    }

    /**
     * HomeController stub
     */
    protected static function compileControllerStub(): array|bool|string
    {
        return str_replace(
            '{{namespace}}',
            Container::getInstance()->getNamespace(),
            file_get_contents(__DIR__.'/stubs/controllers/HomeController.stub')
        );
    }

    /**
     * Welcome.blade.php
     */
    protected static function updateWelcomePage(): void
    {
        $dest = resource_path('views/welcome.blade.php');
        $stub = __DIR__.'/stubs/resources/views/welcome.blade.php';

        if (! file_exists($dest)) {
            (new Filesystem)->ensureDirectoryExists(dirname($dest));
            copy($stub, $dest);

            return;
        }

        static::safeCopy($stub, $dest);
    }

    /**
     * Gets a resource path depending on a version of Laravel.
     */
    protected static function getResourcePath(string $path = ''): string
    {
        if (self::expectsAssetsFolder()) {
            return resource_path('assets/'.$path);
        }

        return resource_path($path);
    }

    /**
     * Should we expect to see an assets folder within this version of Laravel?
     */
    protected static function expectsAssetsFolder(): bool
    {
        return (int) str_replace('.', '', app()->version()) < 570;
    }
}
