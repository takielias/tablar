<?php

namespace TakiElias\Tablar;

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
use TakiElias\Tablar\Events\BuildingMenu;
use TakiElias\Tablar\Http\ViewComposers\TablarComposer;

class TablarServiceProvider extends ServiceProvider
{
    /**
     * The prefix to use for register/load the package resources.
     *
     * @var string
     */
    protected $pkgPrefix = 'tablar';

    /**
     * Register the package services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind a singleton instance of the AdminLte class into the service
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
     */
    private function loadViews(): void
    {
        $viewsPath = $this->packagePath('resources/views');
        $this->loadViewsFrom($viewsPath, $this->pkgPrefix);
    }

    /**
     * Load the package translations.
     *
     * @return void
     */
    private function loadTranslations(): void
    {
        $translationsPath = $this->packagePath('resources/lang');
        $this->loadTranslationsFrom($translationsPath, $this->pkgPrefix);
    }

    /**
     * Load the package config.
     *
     * @return void
     */
    private function loadConfig(): void
    {
        $configPath = $this->packagePath('config/tablar.php');
        $this->mergeConfigFrom($configPath, $this->pkgPrefix);
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
    private function registerCommands()
    {
        $this->commands([
            TablarInstallCommand::class,
            TablarExportAllCommand::class,
            TablarExportConfigCommand::class,
            TablarExportJsCommand::class,
            TablarExportAuthCommand::class,
            TablarExportViewsCommand::class,
            TablarExportAssetsCommand::class,
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
