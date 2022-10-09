@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')

    @if ($navbarItemHelper->isSubmenu($item))
        {{-- Dropdown submenu --}}
        <a class="nav-link {{ $item['class'] }}" @isset($item['target']) target="{{ $item['target'] }}" @endisset
        {!! $item['data-compiled'] ?? '' !!}
        href="{{ $item['href'] }}">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
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
        @each('tablar::partials.navbar.dropdown-item-submenu', $item['submenu'], 'item')
    @elseif ($navbarItemHelper->isLink($item))
        {{-- Dropdown link --}}
        @include('tablar::partials.navbar.dropdown-item-link')
    @endif

