<?php

namespace TakiElias\Tablar\Tests;

use TakiElias\Tablar\Events\BuildingMenu;

class TablarTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Register a listener to 'BuildingMenu' event in order to add items
        // to the menu.

        $this->getDispatcher()->listen(
            BuildingMenu::class,
            [$this, 'addMenuItems']
        );
    }

    public function addMenuItems(BuildingMenu $event)
    {
        // Add (5) items to the sidebar menu.

        $event->menu->add(['text' => 'Home', 'url' => 'url']);
        $event->menu->add(['text' => 'Support', 'url' => 'url']);
        $event->menu->add(['text' => 'Contact', 'url' => 'url']);

        // Add (1) submenu to the sidebar menu.

        $event->menu->add(['text' => 'Submenu', 'submenu' => []]);

        // Add (1) invalid item.

        $event->menu->add(['text' => 'invalid']);

        // Add (1) Setting item.

        $event->menu->add(['text' => 'Settings', 'url' => 'url']);

    }

    public function testMenuWithoutFilters()
    {
        $menu = $this->makeTablar()->menu();

        $this->assertCount(6, $menu);
        $this->extracted($menu);
    }


    public function testMenuSettingsFilter()
    {
        $menu = $this->makeTablar()->menu('Settings');

        $this->assertCount(6, $menu);
        $this->assertArrayNotHasKey(7, $menu);
        $this->assertArrayNotHasKey(8, $menu);
        $this->assertArrayNotHasKey(9, $menu);
        $this->assertArrayNotHasKey(10, $menu);
        $this->extracted($menu);
    }

    /**
     * @param array $menu
     * @return void
     */
    public function extracted(array $menu): void
    {
        $this->assertEquals('Home', $menu[0]['text']);
        $this->assertEquals('Support', $menu[1]['text']);
        $this->assertEquals('Contact', $menu[2]['text']);
        $this->assertEquals('Submenu', $menu[3]['text']);
        $this->assertEquals('invalid', $menu[4]['text']);
        $this->assertEquals('Settings', $menu[5]['text']);
    }

}
