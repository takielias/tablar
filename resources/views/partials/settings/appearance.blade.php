{{-- Appearance toggle: Light / Dark / System.
     Persists to localStorage['tablar.theme'] and dispatches the
     `tablar:theme-change` event for master.blade.php to re-apply. --}}
<div class="btn-group w-100" role="group" aria-label="{{ __('Theme') }}">
    <input type="radio" class="btn-check" name="theme" id="theme-light"
           value="light" data-bs-theme-value="light" autocomplete="off">
    <label class="btn btn-outline-secondary" for="theme-light">
        <i class="ti ti-sun me-1"></i>{{ __('Light') }}
    </label>

    <input type="radio" class="btn-check" name="theme" id="theme-dark"
           value="dark" data-bs-theme-value="dark" autocomplete="off">
    <label class="btn btn-outline-secondary" for="theme-dark">
        <i class="ti ti-moon me-1"></i>{{ __('Dark') }}
    </label>

    <input type="radio" class="btn-check" name="theme" id="theme-auto"
           value="auto" data-bs-theme-value="auto" autocomplete="off">
    <label class="btn btn-outline-secondary" for="theme-auto">
        <i class="ti ti-device-desktop me-1"></i>{{ __('System') }}
    </label>
</div>
<script>
    (function () {
        var saved = localStorage.getItem('tablar.theme') || 'auto';
        var current = document.querySelector('[data-bs-theme-value="' + saved + '"]');
        if (current) {
            current.checked = true;
        }

        document.querySelectorAll('[data-bs-theme-value]').forEach(function (el) {
            el.addEventListener('change', function () {
                var value = el.getAttribute('data-bs-theme-value');
                localStorage.setItem('tablar.theme', value);
                window.dispatchEvent(new CustomEvent('tablar:theme-change', { detail: { value: value } }));
            });
        });
    })();
</script>
