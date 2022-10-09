@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')
@if ($navbarItemHelper->isSubmenu($item))
    @each('tablar::partials.navbar.multilevel', $item['submenu'], 'item')
@elseif ($navbarItemHelper->isLink($item))
    @include('tablar::partials.navbar.single-item')
@endif
