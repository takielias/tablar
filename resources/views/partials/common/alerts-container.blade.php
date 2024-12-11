<style>
    #alerts-container .alert {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.25rem;
        animation: slide-in 0.3s ease-out;
    }

    #alerts-container .alert .btn-close {
        margin-left: 10px;
    }

    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

@php
    $layout = config('tablar.layout');
    $defaultPosition = config('tablar.alerts.default_position');
    $layoutPositions = config("tablar.alerts.positions_by_layout.$layout", $defaultPosition);

    $positionTop = $layoutPositions['top'] ?? '10px';
    $positionRight = $layoutPositions['right'] ?? '10px';
    $positionLeft = $layoutPositions['left'] ?? null; // Only for RTL

    $defaultDuration = config('tablar.alerts.default_duration', 5);
    $defaultDismissible = config('tablar.alerts.dismissible', true);
@endphp

<div id="alerts-container" style="
    position: fixed;
    top: {{ $positionTop }};
    @if(isset($positionLeft))
        left: {{ $positionLeft }};
    @else
        right: {{ $positionRight }};
    @endif
    z-index: 1050;
    max-width: 300px;
    display: flex;
    flex-direction: column;
    gap: 10px;">
    @if (Session::has('message'))
        @php
            $messageData = Session::get('message');
            $messageText = is_array($messageData) ? $messageData['message'] : $messageData; // Compatibility with string or array
            $messageDismissible = is_array($messageData) ? ($messageData['dismissible'] ?? true) : true;
        @endphp
        <div class="alert alert-info shadow" role="alert" data-dismissible="{{ $messageDismissible }}">
            <span>{{ $messageText }}</span>
            @if ($messageDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            @endif
        </div>
    @endif

    @if (Session::has('success'))
        @php
            $successData = Session::get('success');
            $successMessage = is_array($successData) ? $successData['message'] : $successData;
            $successDismissible = is_array($successData) ? ($successData['dismissible'] ?? true) : true;
        @endphp
        <div class="alert alert-success shadow" role="alert" data-dismissible="{{ $successDismissible }}">
            <span>{{ $successMessage }}</span>
            @if ($successDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            @endif
        </div>
    @endif

    @if (Session::has('info'))
        @php
            $infoData = Session::get('info');
            $infoMessage = is_array($infoData) ? $infoData['message'] : $infoData;
            $infoDismissible = is_array($infoData) ? ($infoData['dismissible'] ?? true) : true;
        @endphp
        <div class="alert alert-info shadow" role="alert" data-dismissible="{{ $infoDismissible }}">
            <span>{{ $infoMessage }}</span>
            @if ($infoDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            @endif
        </div>
    @endif

    @if (Session::has('error'))
        @php
            $errorData = Session::get('error');
            $errorMessage = is_array($errorData) ? $errorData['message'] : $errorData;
            $errorDismissible = is_array($errorData) ? ($errorData['dismissible'] ?? true) : true;
        @endphp
        <div class="alert alert-danger shadow" role="alert" data-dismissible="{{ $errorDismissible }}">
            <span>{{ $errorMessage }}</span>
            @if ($errorDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            @endif
        </div>
    @endif

    @if (Session::has('warning'))
        @php
            $warningData = Session::get('warning');
            $warningMessage = is_array($warningData) ? $warningData['message'] : $warningData;
            $warningDismissible = is_array($warningData) ? ($warningData['dismissible'] ?? true) : true;
        @endphp
        <div class="alert alert-warning shadow" role="alert" data-dismissible="{{ $warningDismissible }}">
            <span>{{ $warningMessage }}</span>
            @if ($warningDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            @endif
        </div>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger shadow" role="alert" data-dismissible="true">
                <span>{{ $error }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.addEventListener('alert', event => {
            const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
            const type = detail.type || 'info';
            const message = detail.message || '';
            const duration = (detail.duration || 5) * 1000;
            const dismissible = detail.dismissible !== false;

            const alertContainer = document.getElementById('alerts-container');

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} shadow d-flex align-items-center justify-content-between`;
            alert.role = 'alert';
            alert.innerHTML = `<span>${message}</span>`;

            if (dismissible) {
                const closeButton = document.createElement('button');
                closeButton.type = 'button';
                closeButton.className = 'btn-close';
                closeButton.setAttribute('data-bs-dismiss', 'alert');
                closeButton.setAttribute('aria-label', 'Close');
                alert.appendChild(closeButton);
            }

            alertContainer.appendChild(alert);

            if (duration > 0) {
                setTimeout(() => alert.remove(), duration);
            }
        });
        document.querySelectorAll('#alerts-container .alert').forEach(alert => {
            const duration = alert.dataset.duration || 5000; // Duration in milliseconds
            if (duration > 0) {
                setTimeout(() => alert.remove(), duration);
            }
        });
    });
</script>
