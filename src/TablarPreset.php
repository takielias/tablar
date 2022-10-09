<?php

namespace TakiElias\Tablar;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravel\Ui\Presets\Preset;
use Illuminate\Support\Arr;
use Illuminate\Container\Container;
use SplFileInfo;

/**
 *
 */
class TablarPreset extends Preset
{
    /**
     * Install the preset.
     */
    public static function install()
    {
        static::updatePackages();
        static::updateAssets();
        static::updateBootstrapping();
        static::updateWelcomePage();
        static::removeNodeModules();
    }

    /**
     * Install the preset and auth views.
     */
    public static function exportAuth()
    {
        static::scaffoldController();
        static::scaffoldAuth();
    }


    /**
     * Install the preset and auth views.
     */
    public static function exportConfig()
    {
        static::scaffoldConfig();
    }

    /**
     * Export the Config file.
     */
    protected static function scaffoldConfig()
    {
        copy(__DIR__ . '../../config/tablar.php', base_path('config/tablar.php'));
    }

    /**
     * Install the preset and auth views.
     */
    public static function exportAllView()
    {
        static::scaffoldAllView();
    }

    /**
     * Export the Config file.
     */
    protected static function scaffoldAllView()
    {
        (new Filesystem())->copyDirectory(__DIR__ . '/../resources/views', static::getResourcePath('views/vendor/tablar'));

    }

    /**
     * Export Tabler assets
     * @return void
     */
    public static function exportAssets()
    {

        tap(new Filesystem, function ($filesystem) {
            collect($filesystem->allFiles(base_path('node_modules/@tabler/icons/iconfont/fonts')))
                ->each(function (SplFileInfo $file) use ($filesystem) {
                    $filesystem->copy(
                        $file->getPathname(),
                        public_path('fonts/' . $file->getFilename())
                    );
                });
        });

    }

    /**
     * Update the given package array.
     *
     * @param array $packages
     *
     * @return array
     */
    protected static function updatePackageArray(array $packages): array
    {
        return array_merge([
            "jquery" => "3.6.*",
            "bootstrap" => "~5.2.1",
            "popper.js" => "^1.16.1",
            '@tabler/core' => '1.0.0-beta12',
            "@popperjs/core" => "^2.11.6",
            "@tabler/icons" => "^1.100.0",
            "laravel-vite-plugin" => "^0.6.0",
            "sass" => "^1.32.11",
            "sass-loader" => "^11.0.1",
            "vite" => "^3.0.0",
        ], Arr::except($packages, [
            'lodash',
            'postcss',
            'jquery',
        ]));
    }

    /**
     * Update the Sass files for the application.
     */
    protected static function updateAssets()
    {
        tap(new Filesystem, function ($filesystem) {

            $filesystem->delete(public_path('js/app.js'));
            $filesystem->delete(public_path('css/app.css'));

            if (!$filesystem->isDirectory($directory = public_path('assets'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            $filesystem->copyDirectory(__DIR__ . '/stubs/assets', public_path('assets'));

            if (!$filesystem->isDirectory($directory = public_path('fonts'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            if (!$filesystem->isDirectory($directory = resource_path('css'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            if (!$filesystem->isDirectory($directory = resource_path('sass'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

            if (!$filesystem->isDirectory($directory = resource_path('views'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }

        });
    }

    /**
     * Update the bootstrapping files.
     */
    protected static function updateBootstrapping()
    {
        copy(__DIR__ . '/stubs/vite.config.js', base_path('vite.config.js'));
        (new Filesystem())->copyDirectory(__DIR__ . '/stubs/resources', static::getResourcePath());
    }

    /**
     * Export the authentication views.
     */
    protected static function scaffoldAuth()
    {
        file_put_contents(app_path('Http/Controllers/HomeController.php'), static::compileControllerStub());

        file_put_contents(
            base_path('routes/web.php'),
            "Auth::routes();\n\nRoute::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');\n\n",
            FILE_APPEND
        );

        tap(new Filesystem, function ($filesystem) {
            $filesystem->copyDirectory(__DIR__ . '/stubs/resources/views', resource_path('views'));

            collect($filesystem->allFiles(base_path('vendor/laravel/ui/stubs/migrations')))
                ->each(function (SplFileInfo $file) use ($filesystem) {
                    $filesystem->copy(
                        $file->getPathname(),
                        database_path('migrations/' . $file->getFilename())
                    );
                });
        });
    }


    /**
     * Export Home & Auth controllers
     * @return void
     */
    protected static function scaffoldController()
    {
        if (!is_dir($directory = app_path('Http/Controllers/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(base_path('vendor/laravel/ui/stubs/Auth')))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/Auth/' . Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });
    }

    /**
     * HomeController stub
     * @return array|bool|string
     */
    protected static function compileControllerStub(): array|bool|string
    {
        return str_replace(
            '{{namespace}}',
            Container::getInstance()->getNamespace(),
            file_get_contents(__DIR__ . '/stubs/controllers/HomeController.stub')
        );
    }

    /**
     * Welcome.blade.php
     * @return void
     */
    protected static function updateWelcomePage()
    {
        (new Filesystem)->delete(resource_path('views/welcome.blade.php'));

        copy(__DIR__ . '/stubs/resources/views/welcome.blade.php', resource_path('views/welcome.blade.php'));
    }

    /**
     * Gets resource path depending on version of Laravel.
     *
     * @param string $path
     *
     * @return string
     */
    protected static function getResourcePath(string $path = ''): string
    {
        if (self::expectsAssetsFolder()) {
            return resource_path('assets/' . $path);
        }

        return resource_path($path);
    }

    /**
     * Should we expect to see an assets folder within this version of Laravel?
     *
     * @return bool
     */
    protected static function expectsAssetsFolder(): bool
    {
        return (int)str_replace('.', '', app()->version()) < 570;
    }
}
