@inject('navbarItemHelper', 'TakiElias\Tablar\Helpers\NavbarItemHelper')
@if ($navbarItemHelper->isSubmenu($item))
    <li class="nav-item dropdown {{ $item['class'] }}" @isset($item['id']) id="{{ $item['id'] }}" @endisset>
        <a class="nav-link dropdown-toggle {{ $item['class'] }} {{ $item['submenu_class'] }}" href="" data-bs-toggle="dropdown"
                   @if(in_array(config('tablar.layout'), array(
                        'fluid-vertical',
                        'combo',
                        'vertical',
                        'vertical-right',
                        'vertical-transparent'
                    )))     
                    data-bs-auto-close="false"
                    @else
                    data-bs-auto-close="outside"
                    @endif
         role="button" aria-expanded="false">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <!-- Download SVG icon from http://tabler-icons.io/i/package -->
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
        @if(in_array(config('tablar.layout'), array(
            'fluid-vertical',
            'combo',
            'vertical',
            'vertical-right',
            'vertical-transparent'
        ))) 
            <div class="dropdown-menu {{ $item['submenu_class'] }}">
        @else 
            <div class="dropdown-menu">
        @endif
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
@elseif ($navbarItemHelper->isLink($item))
    {{-- Dropdown link --}}
    @include('tablar::partials.navbar.dropdown-item-link')
@endif

