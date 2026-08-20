@include('common.alert')

@php
    $flashMessages = collect([
        ['key' => 'success', 'title' => 'Success', 'message' => session('success')],
        ['key' => 'info', 'title' => 'Information', 'message' => session('info')],
        ['key' => 'warning', 'title' => 'Alert', 'message' => session('warning')],
        ['key' => 'error', 'title' => 'Alert', 'message' => session('error')],
    ])->filter(fn ($flash) => filled($flash['message']))->values();
@endphp

@if ($flashMessages->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flashMessages = @json($flashMessages);
            const flash = flashMessages[0] || null;

            if (!flash) {
                return;
            }

            const escapeHtml = function (value) {
                const div = document.createElement('div');
                div.textContent = value || '';
                return div.innerHTML;
            };
            const title = escapeHtml(flash.title || 'Alert');
            const message = escapeHtml(flash.message || '');

            if (typeof passing_alert_value === 'function') {
                passing_alert_value(title, message, 'confirmation_alert', 'confirmation_alertmodal', 'alert_body', 'confirmation_alert');
                return;
            }

            const modalElement = document.getElementById('confirmation_alert');
            const titleElement = document.getElementById('confirmation_alertmodal');
            const bodyElement = document.getElementById('alert_body');

            if (!modalElement || !titleElement || !bodyElement || typeof bootstrap === 'undefined') {
                return;
            }

            titleElement.innerHTML = title;
            bodyElement.innerHTML = message;
            document.getElementById('process_button')?.style.setProperty('display', 'none');
            document.getElementById('cancel_button')?.style.setProperty('display', 'none');
            document.getElementById('ok_button')?.style.setProperty('display', 'inline-block');
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        });
    </script>
@endif

@if ($errors->any())
    <div class="alert alert-danger hdv2-alert" role="alert">
        <strong>Please check the form.</strong>
        @unless ($compactErrors ?? false)
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endunless
    </div>
@endif
