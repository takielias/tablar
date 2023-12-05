<?php

namespace TakiElias\Tablar;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use TakiElias\Tablar\Events\BuildingMenu;
use TakiElias\Tablar\Helpers\NavbarItemHelper;
use TakiElias\Tablar\Helpers\SidebarItemHelper;
use TakiElias\Tablar\Menu\Builder;

/**
 *
 */
class Tablar
{

    /**
     * The array of menu items.
     *
     * @var array
     */
    protected array $menu;

    /**
     * The event dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * The array of menu filters. These filters will apply on each one of the
     * menu items in order to transform them in some way.
     *
     * @var array
     */
    protected array $filters;

    /**
     * The application service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Map between a valid menu filter token and his respective filter method.
     * These filters are intended to get a specific set of menu items.
     *
     * @var array
     */
    protected array $menuFilterMap;

    /**
     * Constructor.
     *
     * @param array $filters
     * @param Dispatcher $events
     * @param Container $container
     */
    public function __construct(array $filters, Dispatcher $events, Container $container)
    {
        $this->filters = $filters;
        $this->events = $events;
        $this->container = $container;

        // Fill the map with filters methods.

        $this->menuFilterMap = [
            'sidebar' => [$this, 'sidebarFilter'],
            'navbar-left' => [$this, 'navbarLeftFilter'],
            'navbar-right' => [$this, 'navbarRightFilter'],
            'navbar-user' => [$this, 'navbarUserMenuFilter'],
        ];
    }


    /**
     * Get all the menu items, or a specific set of these.
     *
     * @param string|null $filterToken Token representing a subset of the menu items
     * @return array A set of menu items
     */
    public function menu(string $filterToken = null): array
    {
        if (empty($this->menu)) {
            $this->menu = $this->buildMenu();
        }

        // Check for filter token.

        if (isset($this->menuFilterMap[$filterToken])) {
            return array_filter(
                $this->menu,
                $this->menuFilterMap[$filterToken]
            );
        }

        // No filter token provided, return the complete menu.
        return $this->menu;
    }

    /**
     * Build the menu.
     *
     * @return array The set of menu items
     */
    protected function buildMenu(): array
    {
        // Create the menu builder instance.

        $builder = new Builder($this->buildFilters());

        // Dispatch the BuildingMenu event. Listeners of this event will fill
        // the menu.

        $this->events->dispatch(new BuildingMenu($builder));

        // Return the set of menu items.

        return $builder->menu;
    }

    /**
     * Build the menu filters.
     *
     * @return array The set of filters that will apply on each menu item
     */
    protected function buildFilters(): array
    {
        return array_map([$this->container, 'make'], $this->filters);
    }

    /**
     * Filter method used to get the sidebar menu items.
     *
     * @param mixed $item A menu item
     * @return bool
     */
    private function sidebarFilter($item): bool
    {
        return SidebarItemHelper::isValidItem($item);
    }

    /**
     * Filter method used to get the top navbar left menu items.
     *
     * @param mixed $item A menu item
     * @return bool
     */
    private function navbarLeftFilter($item): bool
    {
        if (SidebarItemHelper::isValidItem($item)) {
            return NavbarItemHelper::isAcceptedItem($item);
        }

        return NavbarItemHelper::isValidLeftItem($item);
    }

    /**
     * Filter method used to get the top navbar right menu items.
     *
     * @param mixed $item A menu item
     * @return bool
     */
    private function navbarRightFilter($item): bool
    {
        return NavbarItemHelper::isValidRightItem($item);
    }

    /**
     * Filter method used to get the navbar user menu items.
     *
     * @param mixed $item A menu item
     * @return bool
     */
    private function navbarUserMenuFilter($item): bool
    {
        return NavbarItemHelper::isValidUserMenuItem($item);
    }

}
