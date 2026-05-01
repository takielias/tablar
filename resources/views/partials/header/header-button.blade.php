@foreach (config('tablar.header_buttons', []) as $btn)
    <a href="{{ $btn['url'] ?? '#' }}" class="btn" target="_blank" rel="noreferrer">
        @if (! empty($btn['icon']))
            <i class="{{ $btn['icon'] }} icon me-1"></i>
        @endif
        {{ $btn['name'] ?? '' }}
    </a>
@endforeach
