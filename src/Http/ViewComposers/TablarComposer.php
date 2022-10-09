<?php

namespace TakiElias\Tablar\Http\ViewComposers;

use Illuminate\View\View;
use TakiElias\Tablar\Tablar;

class TablarComposer
{
    /**
     * @var Tablar
     */
    private $tablar;

    public function __construct(Tablar $tablar)
    {
        $this->tablar = $tablar;
    }

    public function compose(View $view)
    {
        $view->with('tablar', $this->tablar);
    }
}
