<?php

namespace TakiElias\Tablar\Tests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

use TakiElias\Tablar\Tablar;

class ServiceProviderTest extends TestCase
{
    public function testRegisterSingletonInstance()
    {
        // Check the instance of Tablar resolver.

        $tablar = $this->app->make(Tablar::class);
        $this->assertInstanceOf(Tablar::class, $tablar);

        // Check that a singleton instance is registered.

        $this->assertSame($tablar, $this->app->make(Tablar::class));
    }

    public function testBootLoadViews()
    {
        // Check that the main views are loaded.

        $this->assertTrue(View::exists('tablar::master'));
        $this->assertTrue(View::exists('tablar::page'));
        $this->assertTrue(View::exists('tablar::pagination'));
        $this->assertTrue(View::exists('tablar::auth.login'));
        $this->assertTrue(View::exists('tablar::auth.register'));
        $this->assertTrue(View::exists('tablar::auth.verify'));
        $this->assertTrue(View::exists('tablar::auth.passwords.email'));
        $this->assertTrue(View::exists('tablar::auth.passwords.reset'));

        $this->assertTrue(View::exists('tablar::partials.common.container-xl'));
        $this->assertTrue(View::exists('tablar::partials.common.logo'));
        $this->assertTrue(View::exists('tablar::partials.common.search-form'));

        $this->assertTrue(View::exists('tablar::partials.footer.bottom'));

        $this->assertTrue(View::exists('tablar::partials.header.header-button'));
        $this->assertTrue(View::exists('tablar::partials.header.notifications'));
        $this->assertTrue(View::exists('tablar::partials.header.page-header'));
        $this->assertTrue(View::exists('tablar::partials.header.sidebar-top'));
        $this->assertTrue(View::exists('tablar::partials.header.theme-mode'));
        $this->assertTrue(View::exists('tablar::partials.header.top'));
        $this->assertTrue(View::exists('tablar::partials.header.top-right'));


        $this->assertTrue(View::exists('tablar::partials.navbar.dropdown-item'));
        $this->assertTrue(View::exists('tablar::partials.navbar.dropdown-item-link'));
        $this->assertTrue(View::exists('tablar::partials.navbar.dropend'));
        $this->assertTrue(View::exists('tablar::partials.navbar.multilevel'));
        $this->assertTrue(View::exists('tablar::partials.navbar.overlap-topbar'));
        $this->assertTrue(View::exists('tablar::partials.navbar.search'));
        $this->assertTrue(View::exists('tablar::partials.navbar.sidebar'));
        $this->assertTrue(View::exists('tablar::partials.navbar.single-item'));
        $this->assertTrue(View::exists('tablar::partials.navbar.submenu-dropdown-item'));
        $this->assertTrue(View::exists('tablar::partials.navbar.topbar'));


        $this->assertTrue(View::exists('tablar::layouts.boxed'));
        $this->assertTrue(View::exists('tablar::layouts.combo'));
        $this->assertTrue(View::exists('tablar::layouts.condensed'));
        $this->assertTrue(View::exists('tablar::layouts.fluid'));
        $this->assertTrue(View::exists('tablar::layouts.fluid-vertical'));
        $this->assertTrue(View::exists('tablar::layouts.horizontal'));
        $this->assertTrue(View::exists('tablar::layouts.navbar-overlap'));
        $this->assertTrue(View::exists('tablar::layouts.navbar-sticky'));
        $this->assertTrue(View::exists('tablar::layouts.rtl'));
        $this->assertTrue(View::exists('tablar::layouts.vertical'));
        $this->assertTrue(View::exists('tablar::layouts.vertical-right'));
        $this->assertTrue(View::exists('tablar::layouts.vertical-transparent'));

    }

    public function testBootLoadTranslations()
    {
        // Check that the main translations are loaded.

        $this->assertTrue(Lang::has('tablar::tablar.sign_in'));
        $this->assertTrue(Lang::has('tablar::menu.main_navigation'));
    }

    public function testBootLoadConfig()
    {
        // Check that config values are loaded.

        $this->assertTrue(Config::has('tablar.title'));
        $this->assertEquals('Tablar', Config::get('tablar.title'));

        $this->assertTrue(Config::has('tablar.menu'));
        $this->assertTrue(is_array(Config::get('tablar.menu')));
    }


    public function testLayout()
    {
        $this->assertTrue(Config::has('tablar.layout'));
        $this->assertTrue(in_array(Config::get('tablar.layout'), [
            'boxed',
            'combo',
            'condensed',
            'fluid',
            'fluid-vertical',
            'navbar-overlap',
            'horizontal',
            'navbar-sticky',
            'rtl',
            'vertical',
            'vertical-right',
            'vertical-transparent',
        ]));
    }

    public function testBootRegisterCommands()
    {
        // Check that the artisan commands are registered.

        $commands = Artisan::all();
        $this->assertTrue(Arr::has($commands, 'tablar:install'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-all'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-js'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-config'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-auth'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-views'));
        $this->assertTrue(Arr::has($commands, 'tablar:export-assets'));
    }

    public function testBootRegisterViewComposers()
    {
        // Check that the Tablar instance exists on the page blade.

        $view = View::make('tablar::page');
        View::callComposer($view);
        $viewData = $view->getData();

        $this->assertTrue(Arr::has($viewData, 'tablar'));
    }

    public function testBootRegisterMenu()
    {
        $tablar = $this->app->make(Tablar::class);
        $menu = $tablar->menu();
        $this->assertCount(4, $menu);
        $this->assertEquals('Home', $menu[0]['text']);
    }

}
