<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            @if (! empty(config('tablar.footer_buttons')))
                <div class="col-lg-auto ms-lg-auto">
                    <ul class="list-inline list-inline-dots mb-0">
                        @foreach (config('tablar.footer_buttons', []) as $btn)
                            <li class="list-inline-item">
                                <a href="{{ $btn['url'] ?? '#' }}" target="_blank" class="link-secondary" rel="noopener">
                                    @if (! empty($btn['icon']))
                                        <i class="{{ $btn['icon'] }} icon-inline me-1"></i>
                                    @endif
                                    {{ $btn['name'] ?? '' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0 text-secondary">
                    <li class="list-inline-item">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                    </li>
                    <li class="list-inline-item">
                        <a href="https://ebuz.xyz" class="link-secondary" rel="noopener">
                            {{config('tablar.current_version', '1.0')}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
