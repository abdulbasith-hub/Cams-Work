<style>
    .helpdesk-app-theme {
        --primary-color: #5d87ff;
        --secondary-color: #7c3aed;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
        --dark-color: #1f2937;
        --light-color: #f3f4f6;
        color: var(--dark-color);
        /* font-family: 'Nunito', sans-serif; */
    }

    .helpdesk-app-theme .helpdesk-main-content {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: var(--dark-color);
    }

    .helpdesk-app-theme .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .card-header {
        background: var(--primary-color);
        color: #fff;
        border-radius: 15px 15px 0 0 !important;
        padding: 15px 20px;
    }

    .helpdesk-app-theme .btn-primary {
        /* background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); */
         background: var(--primary-color);
        border: none;
        border-radius: 10px;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }

    .helpdesk-app-theme .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
    }

    .helpdesk-app-theme .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .helpdesk-app-theme .table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #d9e6f2;
    }

    .helpdesk-app-theme .table thead {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
    }

    .helpdesk-app-theme .table thead th {
        border-bottom: 0;
        color: #fff;
    }

    .helpdesk-app-theme .table tbody tr {
        transition: background 0.3s ease;
    }

    .helpdesk-app-theme .table tbody td {
        color: #0b0c0c;
        border-color: #e6eef7;
    }

    .helpdesk-app-theme .table tbody tr:nth-child(even) {
        background: #f5f3ff;
    }

    .helpdesk-app-theme .table tbody tr:hover {
        background: #ede9fe;
    }

    .helpdesk-app-theme .form-control,
    .helpdesk-app-theme .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .helpdesk-app-theme .form-control:focus,
    .helpdesk-app-theme .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .helpdesk-app-theme .alert {
        border-radius: 10px;
        border: none;
    }

    .helpdesk-app-theme .attachment-list {
        list-style: none;
        margin: 12px 0 0;
        padding: 0;
    }

    .helpdesk-app-theme .attachment-list li + li {
        margin-top: 10px;
    }

    .helpdesk-app-theme .attachment-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #dbe7ff;
        border-radius: 12px;
        background: #f8fbff;
        color: #0f172a;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .helpdesk-app-theme .attachment-link:hover {
        background: #eef4ff;
        border-color: #b8cdfa;
        color: #0f172a;
    }

    .helpdesk-app-theme .attachment-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .helpdesk-app-theme .attachment-name {
        font-weight: 600;
        word-break: break-word;
    }

    .helpdesk-app-theme .attachment-icon {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        /* background: var(--primary-color); */
        color: #fff;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_length,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_filter,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_info,
    .helpdesk-app-theme .dataTables_wrapper .dataTables_paginate {
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_length select {
        min-width: 80px;
        padding: 0.25rem 2rem 0.25rem 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .helpdesk-app-theme .dataTables_wrapper .dataTables_paginate {
        display: block !important;
    }

    .helpdesk-app-theme .dataTables_wrapper .pagination {
        gap: 6px;
        margin: 0;
    }

    .helpdesk-app-theme .dataTables_wrapper .page-item .page-link {
        min-width: 36px;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #0d6efd;
        background: #0d6efd;
        color: #fff;
        text-align: center;
        box-shadow: none;
    }

    .helpdesk-app-theme .dataTables_wrapper .page-item.disabled .page-link {
        opacity: 0.65;
    }


</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
