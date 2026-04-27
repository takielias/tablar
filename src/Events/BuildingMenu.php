<?php

namespace TakiElias\Tablar\Events;

use TakiElias\Tablar\Menu\Builder;

class BuildingMenu
{
    /**
     * The menu builder.
     */
    public Builder $menu;

    /**
     * Create a new event instance.
     */
    public function __construct(Builder $menu)
    {
        $this->menu = $menu;
    }
}
