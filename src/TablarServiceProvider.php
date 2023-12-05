<?php

namespace TakiElias\Tablar;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use TakiElias\Tablar\Console\TablarExportAllCommand;
use TakiElias\Tablar\Console\TablarExportAssetsCommand;
use TakiElias\Tablar\Console\TablarExportAuthCommand;
use TakiElias\Tablar\Console\TablarExportConfigCommand;
use TakiElias\Tablar\Console\TablarExportJsCommand;
use TakiElias\Tablar\Console\TablarExportViewsCommand;
use TakiElias\Tablar\Console\TablarInstallCommand;
use TakiElias\Tablar\Console\TablarUpdateCommand;
use TakiElias\Tablar\Events\BuildingMenu;
use TakiElias\Tablar\Http\ViewComposers\TablarComposer;

class TablarServiceProvider extends ServiceProvider
{
    /**
     * The prefix to use for register/load the package resources.
     *
     * @var string
     */
    protected string $packagePrefix = 'tablar';

    /**
     * Register the package services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind a singleton instance of the Tablar class into the service
        // container.

        $this->app->singleton(Tablar::class, function (Container $app) {
            return new Tablar(
                $app['config']['tablar.filters'],
                $app['events'],
                $app
            );
        });
    }

    /**
     * Bootstrap the package's services.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function boot(Factory $view, Dispatcher $events, Repository $config): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadConfig();
        $this->registerCommands();
        $this->registerViewComposers($view);
        $this->registerMenu($events, $config);

    }


    /**
     * Load the package views.
     *
     * @return void
     * @throws FileNotFoundException
     */
    private function loadViews(): void
    {
        // Get the views path from the config file
        $tablarViewPath = config('tablar.views_path');

        // Check if a custom path is provided
        if ($tablarViewPath) {
            // Adjust the custom path
            $customViewPath = preg_replace('#^resources[\/\.]#', '', $tablarViewPath);
            $customViewPath = str_replace('.', '/', $customViewPath);
            $fullPath = resource_path($customViewPath);

            // Check if the adjusted path is a valid directory
            if (is_dir($fullPath)) {
                $this->callAfterResolving('view', function ($view) use ($fullPath) {
                    $view->addNamespace($this->packagePrefix, $fullPath);
                });
            } else {
                // Throw a FileNotFoundException if the path is not valid
                throw new FileNotFoundException("Custom view path not found : $fullPath");
            }
        } else {
            // Use the default package views path
            $viewsPath = $this->packagePath('resources/views');
            $this->loadViewsFrom($viewsPath, $this->packagePrefix);
        }
    }


    /**
     * Load the package translations.
     *
     * @return void
     */
    private function loadTranslations(): void
    {
        $translationsPath = $this->packagePath('resources/lang');
        $this->loadTranslationsFrom($translationsPath, $this->packagePrefix);
    }

    /**
     * Load the package config.
     *
     * @return void
     */
    private function loadConfig(): void
    {
        $configPath = $this->packagePath('config/tablar.php');
        $this->mergeConfigFrom($configPath, $this->packagePrefix);
    }

    /**
     * Get the absolute path to some package resource.
     *
     * @param string $path The relative path to the resource
     * @return string
     */
    private function packagePath($path): string
    {
        return __DIR__ . "/../$path";
    }

    /**
     * Register the package's artisan commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        $this->commands([
            TablarInstallCommand::class,
            TablarExportAllCommand::class,
            TablarExportConfigCommand::class,
            TablarExportJsCommand::class,
            TablarExportAuthCommand::class,
            TablarExportViewsCommand::class,
            TablarExportAssetsCommand::class,
            TablarUpdateCommand::class,
        ]);
    }

    /**
     * Register the package's view composers.
     *
     * @return void
     */
    private function registerViewComposers(Factory $view): void
    {
        $view->composer('tablar::page', TablarComposer::class);
    }

    /**
     * Register the menu events handlers.
     *
     * @return void
     */
    private static function registerMenu(Dispatcher $events, Repository $config): void
    {
        // Register a handler for the BuildingMenu event; this handler will add
        // the menu defined on the config file to the menu builder instance.

        $events->listen(
            BuildingMenu::class,
            function (BuildingMenu $event) use ($config) {
                $menu = $config->get('tablar.menu', []);
                $menu = is_array($menu) ? $menu : [];
                $event->menu->add(...$menu);
            }
        );
    }

}
