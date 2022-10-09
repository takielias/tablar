@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')

<li class="nav-item dropdown" @isset($item['id']) id="{{ $item['id'] }}" @endisset>
    <a class="nav-link dropdown-toggle {{ $item['class'] }}" href="" data-bs-toggle="dropdown"
       data-bs-auto-close="outside" role="button" aria-expanded="false">
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
              @if(isset($item['icon']))
                            <i class="{{ $item['icon'] ?? '' }} {{
                isset($item['icon_color']) ? 'text-' . $item['icon_color'] : ''
            }}"></i>
                        @else
                            <i class="ti ti-brand-tabler"></i>
                        @endif
                    </span>
        <span class="nav-link-title">
                        {{ $item['text'] }}
                    </span>
    </a>
    <div class="dropdown-menu">
        <div class="dropdown-menu-columns">
            <div class="dropdown-menu-column">
                @if ($navbarItemHelper->isSubmenu($item))
                    @each('tablar::partials.navbar.multilevel', $item['submenu'], 'item')
                @elseif ($navbarItemHelper->isLink($item))
                    @include('tablar::partials.navbar.submenu-dropdown-item')
                @endif
            </div>
        </div>
    </div>
</li>
