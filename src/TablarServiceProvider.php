<?php

namespace TakiElias\Tablar;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Laravel\Ui\UiCommand as PresetCommand;
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
    public function register()
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
    public function boot(Factory $view, Dispatcher $events, Repository $config)
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadConfig();
        $this->registerViewComposers($view);
        $this->registerMenu($events, $config);

        PresetCommand::macro('tablar:install', function ($command) {
            TablarPreset::install();
            TablarPreset::exportConfig();
            $command->info('Tablar scaffolding installed & config has been exported successfully.');
            $command->comment('Please run "npm install" first. Once the installation is done, run "php artisan ui tablar:export"');
        });

        PresetCommand::macro('tablar:export-config', function ($command) {
            TablarPreset::exportConfig();
            $command->info('Tablar Config Exported successfully.');
        });

        PresetCommand::macro('tablar:export-auth', function ($command) {
            TablarPreset::exportAuth();
            $command->info('Tablar auth scaffolding installed successfully.');
        });

        PresetCommand::macro('tablar:export-views', function ($command) {
            TablarPreset::exportAllView();
            $command->info('Tablar views scaffolding has been exported successfully.');
        });

        PresetCommand::macro('tablar:export-asset', function ($command) {
            TablarPreset::exportAssets();
            $command->info('Tablar Assets Exported successfully.');
        });

    }

    /**
     * Load the package views.
     *
     * @return void
     */
    private function loadViews()
    {
        $viewsPath = $this->packagePath('resources/views');
        $this->loadViewsFrom($viewsPath, $this->pkgPrefix);
    }

    /**
     * Load the package translations.
     *
     * @return void
     */
    private function loadTranslations()
    {
        $translationsPath = $this->packagePath('resources/lang');
        $this->loadTranslationsFrom($translationsPath, $this->pkgPrefix);
    }

    /**
     * Load the package config.
     *
     * @return void
     */
    private function loadConfig()
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
    private function packagePath($path)
    {
        return __DIR__ . "/../$path";
    }

    /**
     * Register the package's view composers.
     *
     * @return void
     */
    private function registerViewComposers(Factory $view)
    {
        $view->composer('tablar::page', TablarComposer::class);
    }

    /**
     * Register the menu events handlers.
     *
     * @return void
     */
    private static function registerMenu(Dispatcher $events, Repository $config)
    {
        // Register a handler for the BuildingMenu event, this handler will add
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
