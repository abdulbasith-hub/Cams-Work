@once
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/download-button/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/download-button/buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/download-button/buttons.html5.min.js') }}"></script>
    <style>
        {!! file_get_contents(resource_path('css/helpdesk-v2/helpdesk-v2.css')) !!}
    </style>
    <script>
        {!! file_get_contents(resource_path('js/helpdesk-v2/helpdesk-v2.js')) !!}
    </script>
@endonce
