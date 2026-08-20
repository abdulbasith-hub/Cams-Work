@extends('index2')
@section('title', 'Dashboard')
@section('content')
@include('common.alert')


<!-- CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- JS -->
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('assets/js/download-button/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/download-button/buttons.print.min.js') }}"></script>

<script src="{{ asset('common/ajaxfn.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/chart.umd.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>



<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">


<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script src="../assets/js/download-button/custom.xl.min.js"></script>


@php
    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode ?? null;
    $distcode = $sessionchargedel->distcode ?? null;
    $hasMultiple = count($departments) > 1;

    $sessionroletypecode = $sessionchargedel->roletypecode ?? null;
    $sessionroleactioncode = $sessionchargedel->roleactioncode ?? null;

    // $showSection = $sessionroletypecode == view()->shared('Dist_roletypecode');
    $showAuditorSection = $sessionroleactioncode == view()->shared('AuditorRoleactioncode');

    $showparacount =
    $sessionroleactioncode === view()->shared('Para_AD_roleactioncode')
    || $sessionroleactioncode === view()->shared('Para_roleactioncode');


    $AD_allowroletype = [
    view()->shared('Dist_roletypecode'),
    view()->shared('Re_roletypecode'),
    view()->shared('Ho_roletypecode'),
    ];

    if (
    in_array($sessionroletypecode, $AD_allowroletype) &&
    ($sessionroleactioncode == '02' ||
    $sessionroleactioncode == '07' ||
    $sessionroleactioncode == '11' ||
    $sessionroleactioncode == '10')
    ) {
    $showAD_District_tab = 'Y';
    } else {
    $showAD_District_tab = 'N';
    }

    // $adminallow =['03','04','05','07'];
    $adminallow = [
    view()->shared('Ho_roletypecode'),
    view()->shared('DGA_roletypecode'),
    view()->shared('Admin_roletypecode'),
    view()->shared('NIC_roletypecode'),
    ];

    if (in_array($sessionroletypecode, $adminallow)) {
    $showSectionTab = 'Y';
    } else {
    $showSectionTab = 'N';
    }

    // User type detection
    $isSectionUser = $showAuditorSection;
    $isADDistrictUser = $showAD_District_tab === 'Y';
    $isAdminUser = $showSectionTab === 'Y';

    // Tab visibility flags
    $showTransactions = true;
    $showSlipDetails = $isSectionUser;
    $showAllSlipDetails =
    $isSectionUser &&
    $institutionDetails->contains(function ($item) {
    return $item->auditteamhead === 'Y';
    });
    $showAuditPlans = $isADDistrictUser || $isAdminUser;
    $showAuditSlips = $isADDistrictUser || $isAdminUser;

    $showAnyTabs =
    $showTransactions || $showSlipDetails || $showAllSlipDetails || $showAuditPlans || $showAuditSlips;

    $showGlobalDashboard = !$showAnyTabs;
@endphp

<style>
    :root {
        --primary: #004aad;
        --primary-light: #336ed4;
        --primary-dark: #00307a;
        --secondary: #007e33;
        --secondary-light: #00b44b;
        --info: #0088cc;
        --warning: #ffa500;
        --danger: #d62828;
        --light: #f0f2f5;
        --dark: #1c1c1c;
        --gray: #6e6e6e;
        --white: #ffffff;
        --card-bg: #ffffff;
        --card-shadow: 0 3px 14px rgba(0, 0, 0, 0.08);
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-title {
        flex: 1;
    }

    .header-title h1 {
        color: var(--primary-dark);
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .header-title p {
        color: var(--gray);
        margin: 5px 0 0;
        font-size: 14px;
    }

    .filter-section {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: flex-end;
        padding: 20px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .filter-label {
        font-weight: 500;
        color: var(--dark);
        font-size: 14px;
        white-space: nowrap;
    }

    .filter-select {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        background-color: var(--white);
        font-size: 14px;
        flex: 1;
        min-width: 120px;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(92, 62, 200, 0.2);
        outline: none;
    }

    .refresh-btn {
        padding: 10px 20px;
        border-radius: 8px;
        background-color: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .refresh-btn:hover {
        background-color: var(--primary-light);
        transform: translateY(-1px);
    }

    .refresh-btn:active {
        transform: translateY(0);
    }

    .card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        padding: 20px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
        border: none;
    }

    .card-title {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        color: var(--primary-dark);
        justify-content: space-between;
    }

    .card-title h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: var(--white);
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 5px solid var(--primary-dark);
    }

    .stat-card.primary {
        background: linear-gradient(135deg, #5D9CEC, #2A77D1);
        border-left-color: #1A4E91;
    }

    .stat-card.warning {
        background: linear-gradient(135deg, #FFB84C, #FF8C42);
        border-left-color: #C8641E;
        color: #fff;
    }

    .stat-card.success {
        background: linear-gradient(135deg, #28D79F, #14A76C);
        border-left-color: #0C6647;
    }

    .stat-card.danger {
        background: linear-gradient(135deg, #FF6B6B, #D64545);
        border-left-color: #A42C2C;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card.selected {
        transform: scale(1.05);
        box-shadow: 0 0 16px 5px rgba(66, 165, 245, 0.6);
    }

    .stat-card.warning.selected {
        box-shadow: 0 0 16px 5px rgba(255, 193, 7, 0.6);
    }

    .stat-card.success.selected {
        box-shadow: 0 0 16px 5px rgba(76, 175, 80, 0.6);
    }

    .stat-card.danger.selected {
        box-shadow: 0 0 16px 5px rgba(244, 67, 54, 0.6);
    }

    .stat-icon {
        padding: 14px;
        border-radius: 12px;
        margin-right: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .stat-icon.primary {
        background-color: rgba(93, 156, 236, 0.15);
        color: #2A77D1;
    }

    .stat-icon.warning {
        background-color: rgba(255, 184, 76, 0.15);
        color: #FF8C42;
    }

    .stat-icon.success {
        background-color: rgba(40, 215, 159, 0.15);
        color: #14A76C;
    }

    .stat-icon.danger {
        background-color: rgba(255, 107, 107, 0.15);
        color: #D64545;
    }

    .stat-content h3 {
        font-size: 26px;
        margin-bottom: 4px;
        font-weight: 700;
        color: inherit;
    }

    .stat-content p {
        font-size: 14px;
        opacity: 0.85;
        font-weight: 500;
        margin: 0;
    }


    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        white-space: normal !important;
    }

    .badge.primary {
        background-color: rgba(111, 66, 193, 0.1);
        color: var(--primary);
    }

    .badge.warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: var(--warning);
    }

    .badge.success {
        background-color: rgba(56, 176, 0, 0.1);
        color: var(--secondary);
    }

    .departments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .department-card {
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .department-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }



    .department-card i {
        font-size: 28px;
        margin-bottom: 15px;
        display: block;
        font-style: normal;
    }

    .department-card h5 {
        margin: 0 0 5px;
        font-size: 16px;
        color: white;
    }

    .department-card small {
        font-size: 12px;
        opacity: 0.9;
        display: block;
        margin-bottom: 10px;
    }

    .department-card h4 {
        margin: 0;
        font-size: 22px;
        color: white;
    }

    .department-card.primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
    }

    .department-card.success {
        background: linear-gradient(135deg, var(--secondary), var(--secondary-light));
    }

    .department-card.info {
        background: linear-gradient(135deg, var(--info), #00d4ff);
    }

    .department-card.warning {
        background: linear-gradient(135deg, var(--warning), #ffcb29);
    }

    .department-card.danger {
        background: linear-gradient(135deg, var(--danger), #ff4d6d);
    }

    .department-card.primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--white);
    }

    .department-card.success {
        background: linear-gradient(135deg, var(--secondary), var(--secondary-light));
        color: var(--white);
    }

    .department-card.info {
        background: linear-gradient(135deg, var(--info), #00d4ff);
        color: var(--white);
    }

    .department-card.warning {
        background: linear-gradient(135deg, var(--warning), #ffcb29);
        color: var(--dark);
    }

    .department-card.danger {
        background: linear-gradient(135deg, var(--danger), #ff4d6d);
        color: var(--white);
    }

    /* Selected States */
    .department-card.primary.selected {
        box-shadow: 0 0 10px 4px rgba(0, 74, 173, 0.6), 0 8px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px) scale(1.03);
    }

    .department-card.success.selected {
        box-shadow: 0 0 10px 4px rgba(0, 126, 51, 0.6), 0 8px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px) scale(1.03);
    }

    .department-card.info.selected {
        box-shadow: 0 0 10px 4px rgba(0, 136, 204, 0.6), 0 8px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px) scale(1.03);
    }

    .department-card.warning.selected {
        box-shadow: 0 0 10px 4px rgba(255, 165, 0, 0.6), 0 8px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px) scale(1.03);
    }

    .department-card.danger.selected {
        box-shadow: 0 0 10px 4px rgba(214, 40, 40, 0.6), 0 8px 25px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px) scale(1.03);
    }


    /* Data Visualization Styles */
    .data-container {
        display: block;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .data-container {
            grid-template-columns: 1fr;
        }
    }

    .table-container {
        background: var(--white);
        border-radius: 10px;
        /* box-shadow: var(--card-shadow); */
        padding: 20px;
        overflow-x: auto;
    }

    .chart-container {
        background: var(--white);
        border-radius: 10px;
        /* box-shadow: var(--card-shadow); */
        padding: 20px;
        position: relative;
        height: 100%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    th,
    td {
        padding: 5px 20px !important;
        border-bottom: 1px solid #e9ecef;
    }

    th {
        background-color: var(--primary);
        color: white;
        font-weight: 500;
        position: sticky;
        top: 0;
    }

    tr:hover td {
        background-color: rgba(111, 66, 193, 0.05);
    }

    .highlight-row {
        background-color: rgba(111, 66, 193, 0.1) !important;
        font-weight: bold;
    }

    .highlight-row td {
        border-left: 3px solid var(--primary);
    }

    .no-data {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray);
        font-style: italic;
    }

    canvas {
        width: 100% !important;
        height: 350px !important;
    }

    .detail,
    .department,
    .summary {
        margin-bottom: 25px;
    }

    .chart-toggle-container {
        margin-bottom: 15px;
        text-align: end;
    }

    .chart-toggle-buttons {
        display: inline-flex;
    }

    .btn-group .btn {
        padding: 5px 15px;
        font-size: 14px;
        border: 1px solid #ddd;
        background-color: #f8f9fa;
        color: #495057;
    }

    .btn-group .btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .table-heading {
        margin-block: 20px;
    }

    .view-toggle-btn {
        background-color: #4a6baf;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-inline: 15px;
    }

    .view-toggle-btn:hover {
        background-color: #3a5a9f;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .view-toggle-btn:active {
        background-color: #2a4a8f;
        transform: translateY(0);
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
    }

    .view-toggle-btn::after {
        content: "⇅";
        margin-left: 6px;
        font-size: 12px;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.312);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }
    .loading-filter-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.312);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        display: none;
    }

    .cube {
        width: 40px;
        height: 40px;
        background: var(--primary);
        animation: cubeFlip 1.2s infinite ease-in-out;
        transform-style: preserve-3d;
    }

    @keyframes cubeFlip {
        0% {
            transform: rotateX(0) rotateY(0);
        }

        25% {
            transform: rotateX(180deg) rotateY(0);
        }

        50% {
            transform: rotateX(180deg) rotateY(180deg);
        }

        75% {
            transform: rotateX(0) rotateY(180deg);
        }

        100% {
            transform: rotateX(0) rotateY(0);
        }
    }

    .dt-buttons .btn {
        background-color: #4e73df;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        margin-right: 10px;
    }

    .dt-buttons .btn:hover {
        background-color: #2e59d9;
    }

    .table-responsive {
        overflow: unset;
    }

    .highlight-row {
        background-color: rgba(0, 123, 255, 0.1) !important;
        font-weight: bold;
    }


    .skeleton {
        background: linear-gradient(90deg, #e5e5e5 25%, #f2f2f2 50%, #e5e5e5 75%);
        background-size: 400% 100%;
        animation: shimmer 1.2s ease-in-out infinite;
        border-radius: 8px;
    }

    @keyframes shimmer {
        0% {
            background-position: -400px 0;
        }

        100% {
            background-position: 400px 0;
        }
    }

    .skeleton-card {
        height: 90px;
        border-radius: 10px;
    }

    .skeleton-title {
        width: 200px;
        height: 24px;
        margin-bottom: 15px;
    }

    .skeleton-filter {
        width: 150px;
        height: 35px;
        border-radius: 6px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .clickable-number {
        color: #007bff;
        cursor: pointer;
        text-decoration: underline;
    }

    .clickable-number:hover {
        color: #0056b3;
        text-decoration: underline;
    }


    .skeleton-loader {
        animation: pulse 1.5s infinite ease-in-out;
    }

    .btn-skeleton,
    .chart-placeholder,
    .skeleton-table td {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 4px;
        color: transparent;
    }

    .skeleton-table td {
        height: 20px;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 0.8;
        }

        50% {
            opacity: 0.5;
        }
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Initial state */
    .content-wrapper {
        display: none;
    }

    .skeleton-loader {
        display: block;
    }

    .chart-toggle-skeleton {
        margin-bottom: 15px;
        text-align: end;
    }

    .refresh-btn {
        transition: all 0.3s ease;
    }

    .refresh-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .refresh-btn .fa-spinner {
        margin-right: 8px;
    }

    /* Make all table headers bold */
    table.dataTable thead th {
        font-weight: bold !important;
    }

    /* Style for totals row */
    .table-totals-row {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    /* Style for footer totals */
    tfoot th {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    .dt-footer-label {
        text-align: right !important;
        font-weight: bold;
    }

    .dataTables_wrapper .dataTables_scroll {
        overflow: auto;
    }

    .stat-card.disabled,
    .department-card.disabled {
        opacity: 0.6;
        cursor: not-allowed !important;
        position: relative;
    }


    .disabled-overlay i {
        font-size: 24px;
        color: #6c757d;
    }

    .badge.bg-secondary {
        cursor: not-allowed !important;
        opacity: 0.7;
    }

    .close-btn {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffffff;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .close-btn:hover {
        color: #fefefe;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #3782ce;
    }

    .card-header h3 {
        color: white;
        font-size: 1.25rem;
    }

    /* Total row styling for all tables */
    .table-total-row {
        background-color: #f8f9fa !important;
        font-weight: 600;
        border-top: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
    }

    .table-total-row td {
        background-color: #f0f7ff !important;
        color: #0d6efd;
        position: relative;
        padding: 12px 8px;
    }

    .table-total-row:hover td {
        background-color: #e6f2ff !important;
    }

    /* Special styling for the total badge */
    .total-badge {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .total-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
    }

    /* Add a subtle indicator to total cells */
    .table-total-row td:not(:first-child)::before {
        content: "➤";
        position: absolute;
        left: 2px;
        color: #0d6efd;
        opacity: 0.6;
        font-size: 12px;
    }

    /* Animation for when total rows are added */
    @keyframes highlightTotal {
        0% {
            background-color: #e6f7ff;
        }

        100% {
            background-color: #f0f7ff;
        }
    }

    .table-total-row {
        animation: highlightTotal 0.8s ease;
    }

    /* Ensure consistent styling across all tables */
    #detail-card,
    #districtTable,
    #institutionTable,
    #auditReportTable,
    #slipdetails_Table {
        border-collapse: separate;
        border-spacing: 0;
    }

    #detail-card tfoot tr,
    #districtTable tfoot tr,
    #institutionTable tfoot tr,
    #auditReportTable tfoot tr {
        background-color: #f8f9fa;
    }

    #detail-card tfoot th,
    #districtTable tfoot th,
    #institutionTable tfoot th,
    #auditReportTable tfoot th {
        background-color: #b8e1f070;
        font-weight: 600;
        text-align: right;
    }

    /* Make sure the highlight is visible in all view modes */
    .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-total-row td {
            padding: 10px 6px;
            font-size: 14px;
        }

        .table-total-row td:not(:first-child)::before {
            display: none;
        }
    }

    #institutionTable {
        table-layout: auto;
        width: 100% !important;
    }

    #institutionTable th,
    #institutionTable td {
        overflow: hidden;
        white-space: normal !important;
        align-content: center;
    }

    .table-responsive .dataTables_wrapper .dataTables_length select {
        padding-right: 1.725rem;

    }

    div#institutionTable_length {
        margin-top: 10px;
    }

    #InstitutionParaTableContainer {
        margin-bottom: 25px;
    }

    .file-attachment {
        display: block;
        align-items: center;
        gap: 8px;
        padding: 8px;
        background-color: #f8f9fa;
        border-radius: 4px;
        border: 1px solid #e9ecef;
    }

    .file-link {
        color: #007bff;
        text-decoration: none;
    }

    .file-link:hover {
        text-decoration: underline;
    }

    .file-list {
        margin-top: 15px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .file-item {
        display: flex;
        align-items: center;
        padding: 5px 0;
    }


    .apply-btn {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .apply-btn:hover {
        background-color: #218838;
    }

    .apply-btn:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }

    .apply-btn.loading {
        background-color: #17a2b8;
    }

    .filter-section {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: end;
    }

    table#dataTable {
        white-space: unset !important;
    }


    .accordion-button:not(.collapsed) {
        background-color: #e9ecef;
        color: #495057;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0, 0, 0, .125);
    }

    .accordion-body {
        padding: 1.5rem;
        background-color: #fff;
    }

    .hide_this {
        display: none;
    }

    .badge {
        font-size: 0.75em;
    }

    .accordion-item {
        border: 1px solid rgba(0, 0, 0, .125);
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
    }

    .accordion-item:first-of-type {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }

    .accordion-item:last-of-type {
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }

    .heading-group {
        border-left: 4px solid #007bff;
        padding-left: 15px;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }


    .slip-remarks-editor,
    .remarks-editor {
        min-height: 120px;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
    }

    .badge {
        font-size: 0.75em;
    }


    .stat-card {
        display: flex;
        align-items: center;
        border-radius: 12px;
        padding: 14px 18px;
        color: #fff;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-icon {
        font-size: 2rem;
        margin-right: 12px;
        opacity: 0.9;
    }

    .stat-content h3 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .stat-content p {
        margin: 2px 0 6px;
        font-size: 0.95rem;
        font-weight: 500;
        opacity: 0.9;
    }

    .sub-stats {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .badge {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border-radius: 6px;
        padding: 5px 8px;
        font-size: 0.8rem;
    }

    .badge.single {
        background: linear-gradient(135deg, var(--secondary-light), var(--secondary));
        color: var(--white);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .badge.multi {
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: var(--white);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
    }
</style>

<style>
    .accordion-button {
        font-weight: 600;
        background-color: #f8f9fa;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e9ecef;
        color: #495057;
    }

    .accordion-body {
        padding: 1.5rem;
    }


    .badge {
        font-size: 0.75em;
    }

    .nav-tabs .nav-link {
        border: 2px solid #5a6174;
        color: #5a6174;
        font-weight: bold;
        transition: all 0.3s ease;
        margin: 0 5px;
    }

    .nav-tabs .nav-link.active {
        border: 2px solid #4973e8;
        color: rgb(245, 245, 245);
        background-color: #4973e8;
    }

    .nav-tabs .nav-link:hover {
        border: 2px solid #4973e8;
    }

    .list-group-item:nth-child(odd) {
        background-color: white;
    }

    .list-group-item:nth-child(even) {
        background-color: #ebf3fe;
    }

    .list-group-item:hover {
        background-color: #809fff;
        cursor: pointer;
    }

    .card-body {
        padding: 12px 10px;
    }

    .card {
        margin-bottom: 2px;
    }

    .card-slip {
        border: 1px solid #ebf1f6 !important;
        border-radius: 12px !important;
        box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1) !important;
        padding: 10px !important;
    }

    .card-slip:nth-child(1) {
        background-color: rgb(41, 86, 153);
        /* Total Slips */
    }

    .card-slip:nth-child(2) {
        background-color: lightgreen;
        /* Dropped Slips */
    }

    .card-slip:nth-child(3) {
        background-color: rgb(225, 138, 6);
        /* Converted to Paras */
    }

    .card-slip:nth-child(4) {
        background-color: lightgoldenrodyellow;
        /* Pending Slips */
    }

    .round-40 {
        width: 50px !important;
        height: 50px !important;
        background: transparent;
    }

    .card-title {
        font-size: 22px;
        font-weight: bold;
    }

    #allocatedplans_tab-pane .card-body {
        padding: 20px !important;
    }

    .schedulingtable th,
    .schedulingtable td {
        text-align: center;

    }

    .schedulingtable thead {
        display: table-header-group !important;
        visibility: visible !important;
    }

    .schedulingtable tfoot th {
        background-color: #e9e9e9 !important;
        color: black !important;
        text-align: right !important;

    }

    #slipHistoryTable th {
        background-color: #6b6b6c;
        color: white !important;


    }



    #commencedinstitutewisetable .dt-buttons,
    #institutewisetable .dt-buttons {
        margin-bottom: 20px !important;
    }

    /* .text-left {
                text-align: left !important;
            }

            .text-right {
                text-align: right !important;

            } */

    .table_slip td {
        width: 60%;
    }

    .table_slip th {
        width: 40%;
    }

    .auditor-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
        border-radius: 8px;
        background-color: #ffffff;
        border: 1px solid #e1e1e1;
        font-size: 14px;
    }

    .auditor-table th {
        text-align: left;
        background-color: #f2f2f2;
        color: #333;
        padding: 12px 15px;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
    }

    .auditor-table td {
        padding: 12px 15px;
        color: #555;
        border-bottom: 1px solid #ddd;

    }

    .auditor-table tr:hover {
        background-color: #fafafa;
    }
</style>

<style>
    .modal-header {
        background-color: #3866ac;
        color: #fff;
        display: flex;
        justify-content: center;
        /* Centers content horizontally */
        align-items: center;
        /* border-radius: 20px; */

    }

    .modal-title {
        font-weight: bold;
        margin: 0 auto;
        color: white;
        padding: 10px;

    }

    .form-control:focus {
        border-color: none;
        */
        /* box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); */
    }

    .btn-primary {
        /* color: rgb(92, 57, 133); */
        background-color: #0d6efd;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .content {


        border-radius: 20px;
        overflow: hidden;
    }

    .form-label {
        color: #42669c;
    }

    .btn-close {
        border: none;
        opacity: 1;
    }

    .btn-close:hover {
        color: #bea9a9;
    }

    .btn-close:focus {
        box-shadow: none;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 10px;
        cursor: pointer;
        pointer-events: auto;
    }

    .toggle-password i {
        color: #5682b3;
        transition: color 0.3s ease;
    }

    .toggle-password.active i {
        color: #28a745;
    }

    .dt-buttons {
        margin-top: 10px;
    }

    .team-info.small {
        min-width: 300px !important;
    }

    #districtTableContainer {
        margin-bottom: 25px;
    }

    .wrap-50 {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px;
    }

    #slipdetailsTableContainer {
        margin-top: 25px;
    }

    #detail-table th:nth-child(n+3),
    #detail-table td:nth-child(n+3) {
        text-align: right;
    }

    #districtTable th:nth-child(n+3),
    #districtTable td:nth-child(n+3) {
        text-align: right;
    }

    #paraview_details {
        margin-top: 20px;
    }

    #slipdetailsTableContainer .dataTables_paginate {
        margin: 15px 0;
        display: block !important;
    }

    #slipdetailsTableContainer .paginate_button {
        padding: 6px 12px;
        margin: 0 3px;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #007bff;
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
    }

    #slipdetailsTableContainer .paginate_button.current {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
        font-weight: bold;
    }

    #slipdetailsTableContainer .paginate_button:hover:not(.disabled) {
        background-color: #e9ecef;
        color: #0056b3;
        border-color: #dee2e6;
    }

    .paginate_button.disabled {
        color: #6c757d !important;
        cursor: not-allowed !important;
        background-color: #f8f9fa !important;
        opacity: 0.6;
    }

    .table-responsive .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 3px;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #007bff;
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
    }

    .table-responsive .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
        font-weight: bold;
    }
</style>
{{-- @dd($showAD_District_tab , $showSectionTab, $showparacount) --}}
@if ($showAD_District_tab == 'Y' || $showSectionTab == 'Y' || $showparacount)
<div class="loading-overlay">
    <div class="cube"></div>
</div>

<div class="filter-section">
    <div class="filter-group">
        <span class="filter-label">Select Program:</span>
        <select id="audit-program-filter" class="filter-select">
             @if ($showAD_District_tab == 'Y' || $showSectionTab == 'Y')
            <option value="plantabform">Audit Plan</option>
            <option value="sliptabform">Audit Slip Details</option>
            <option value="auditreport">Audit Report</option>
            <option value="inspectionaudit">Inspection Report</option>
            <option value="templateaudit">Template Audit</option>
            <option value="legacyreport">Legacy Report</option>
            @endif
            @if ($sessionroleactioncode === view()->shared('Admin_roleactioncode') && $sessionroletypecode ===
            view()->shared('Admin_roletypecode'))
            <option value="parareport">APMS Status</option>
            @endif
            @if (  $showSectionTab == 'Y' )
            <option value="paracount">APMS Pending Details</option>
            {{-- <option value="retirementpara">Retirement Para</option> --}}
            @endif

        </select>
    </div>

    <div class="filter-group">
        <span class="filter-label">Department:</span>
        <select id="department-filter" class="filter-select">
            @if ($hasMultiple || !$deptcode)
            <option value="all" {{ !$deptcode ? 'selected' : '' }}>All Departments</option>
            @endif

            @foreach ($departments as $dept)
            <option value="{{ $dept->deptcode }}" data-short-name="{{ $dept->deptesname }}"
                data-full-name="{{ $dept->deptelname }}" {{ $deptcode==$dept->deptcode ? 'selected' : '' }}>
                {{ $dept->deptelname }}
            </option>
            @endforeach
        </select>
    </div>


    <div class="filter-group" id="year-filter-group">
        <span class="filter-label">Financial Year:</span>
        <select id="year-filter" class="filter-select">
            @if (!empty($financialyear) && count($financialyear) > 0)
            @foreach ($financialyear as $fy)
            <option value="{{ $fy->financialyearcode }}" data-name-en="{{ $fy->financialyear }}">
                {{ $fy->financialyear }}
            </option>
            @endforeach
            @else
            <option data-name-en="No Financial Year Available">No Financial Year Available</option>
            @endif
        </select>
    </div>


    <div class="filter-group" id="quarter-filter-group">
        <span class="filter-label">Quarter:</span>
        <select id="quarter-filter" class="filter-select" name="quarter"
               >
            @if (!empty($auditQuarters) && count($auditQuarters) > 0)
                @foreach ($auditQuarters as $quarter)
                    <option value="{{ $quarter->group_key }}"
                            data-name-en="{{ $quarter->planname }}">
                        {{ $quarter->planname }}
                    </option>
                @endforeach
            @else
                <option data-name-en="No Quarter Available">
                    No Quarters Available
                </option>
            @endif
        </select>
    </div>

    <button id="apply-filters-btn" class="apply-btn">
        <span style="font-size:1.2em"><i class="fas fa-filter"></i></span> Apply Filters
    </button>
</div>

<div class="card dashboard-container">
<div class="loading-filter-overlay">
    <div class="cube"></div>
</div>
    <div class="summary">
        <div class="card-title">
            <h2>Summary Overview</h2>
        </div>
        <div class="stats-grid" id="summary-cards-container">
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
            <div class="skeleton skeleton-card"></div>
        </div>
    </div>

    <div class="department" id="department-card" style="display: none;">
        <div class="card-title">
            <h2 id="department-view-title"></h2>
        </div>
        <div class="departments-grid" id="department-cards-container">
        </div>
    </div>

    <div class="detail" id="detail-card" style="display: none;">
        <div class="card-title">
            <h2 id="detail-view-title"></h2>
            <button id="view-toggle" class="view-toggle-btn">View as Table</button>
        </div>
        <div class="data-container">
            <div class="skeleton-loader" style="display: none;">
                <div class="chart-skeleton">
                    <div class="chart-toggle-skeleton">
                        <div class="btn-group btn-group-sm mb-3">
                            <button class="btn btn-skeleton" style="width: 60px;"></button>
                            <button class="btn btn-skeleton" style="width: 60px;"></button>
                        </div>
                    </div>
                    <div class="chart-placeholder"
                        style="height: 400px; background-color: #f5f5f5; border-radius: 4px;"></div>
                </div>
                <div class="table-skeleton">
                </div>
            </div>
            <div class="content-wrapper">
                <div class="chart-container">
                    <div class="chart-toggle-container">
                        <div class="chart-toggle-buttons btn-group btn-group-sm mb-3">
                            <button type="button" class="btn btn-primary active" data-chart-type="bar">Bar</button>
                            <button type="button" class="btn btn-primary" data-chart-type="pie">Pie</button>
                        </div>
                    </div>
                    <canvas id="detail-chart"></canvas>
                </div>
                <div class="card table-container">
                    <div class="table-responsive">
                        <table id="detail-table" class="table table-striped table-bordered" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="districtTableContainer" class="" style="display: none;">
        <div class="card-header text-white">
            <h3></h3>
            <button class="close-btn" onclick="hideTable('districtTableContainer')">×</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="districtTable" class="table table-striped table-bordered table-hover" style="width:100%">
                </table>
            </div>
        </div>
    </div>

    <div id="institutionTableContainer" class="" style="display: none;">
        <div class="card-header text-white">
            <h3></h3>
            <button class="close-btn" onclick="hideTable('institutionTableContainer')">×</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="institutionTable" class="table table-striped table-bordered table-hover" style="width:100%">
                </table>
            </div>
        </div>
    </div>


    <div id="auditReportTableContainer" class="" style="display: none;">
        <div class="card-header text-white">
            <h3></h3>
            <button class="close-btn" onclick="hideTable('auditReportTableContainer')">×</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="auditReportTable" class="table table-striped table-bordered table-hover" style="width:100%">
                </table>
            </div>
        </div>
    </div>
    <div id="InstitutionParaTableContainer" class="" style="display:none;">
        <div class="card-header text-white">
            <h3></h3>
            <button class="close-btn" onclick="hideTable('InstitutionParaTableContainer')">×</button>

            <div class="card-body">
                <table id="institutionReportTable" class="table table-striped table-bordered table-hover"
                    style="width:100%">

                </table>
            </div>
        </div>
    </div>


    <div id="slipdetailsTableContainer" class="" style="display:none;">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h3 id="slipdetailsTitle" class="mb-0"></h3>
            <button class=" close-btn" onclick="hideTable('slipdetailsTableContainer')">×</button>
        </div>
        <div class="card-body">
            <table id="slipdetailsReportTable" class="table table-striped table-bordered table-hover"
                style="width:100%">
            </table>
        </div>
    </div>


    <div class="hide_this" id="slipview_Details">
        <div class="card-header text-white">
            <h3>Audit Slip Details of <span class="slipshowinstname"></span></h3>
            <button class="close-btn" onclick="hideTable('slipview_Details')">×</button>
        </div>
        <div class="card-body">
            <br>
            <div class="cardforslips">
                <div class="datatables">
                    <div class="table-responsive hide_this usertable_detail_wrapper" id="Sliptableshow">
                        <table id="slipdetails_Table" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Slip No</th>
                                    <th>Objection</th>
                                    <th>Team Head</th>
                                    <th>Auditor Name</th>
                                    <th>Slip Created On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id='noSlipData' class='hide_this'>
                <center>No Slip Data Available</center>
            </div>
        </div>
        <br>
    </div>

    <div class="hide_this" id="paraview_details">
        <div class="card-header text-white">
            <h3>Para Details of <span class="parashowinstname"></span></h3>
            <button class="close-btn" onclick="hideTable('paraview_details')">×</button>
        </div>
        <div class="card-body">
            <br>
            <div class="cardforparas">
                <div class="datatables">
                    <div class="table-responsive hide_this usertable_detail_wrapper" id="ParaTableShow">
                        <table id="paraDetailsTable" class="table table-striped table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>S. No</th>
                                    <th>Objection Details</th>
                                    <th>Amount</th>
                                    <th>Auditor</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id='noParaData' class='hide_this'>
                <center>No Para Data Available</center>
            </div>
        </div>
        <br>
    </div>

</div>



@endif

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content content">
            <div class="modal-header ">
                <h4 class="modal-title text-center lang" id="changePasswordModalLabel" key="password_head">Change
                    Password</h4>

            </div>
            <div class="modal-body">

                <form id="passwordForm">
                    @csrf

                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error"></div>
                    <div class="mb-2">
                        <label for="oldpassword" class="form-label required lang" key="oldpassword">Old
                            Password</label>

                        <div class="position-relative">

                            <input type="password" class="form-control" name="oldpassword" id="oldpassword"
                                data-placeholder-key="oldpassword">

                            <span class="toggle-password position-absolute" data-target="#oldpassword">
                                <i class="fas fa-eye"></i>
                            </span>

                        </div>


                        <div id="oldpassword-error" class="text-danger form-error" style="display: none;"></div>

                    </div>

                    <div class="mb-2">
                        <label for="newpassword" class="form-label required newpassword_error lang"
                            key="newpassword">New Password</label>

                        <div class="position-relative">
                            <input type="password" class="form-control" name="newpassword" id="newpassword"
                                data-placeholder-key="newpassword">

                            <span class="toggle-password position-absolute" data-target="#newpassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <div id="newpassword-error" class="text-danger form-error" style="display: none;"></div>
                        <div id="newpassword_error" class="text-danger form-error" style="display: none;"></div>

                    </div>
                    <div class="mb-1">
                        <label for="confirmpassword" class="form-label required lang" key="confirmpassword">Confirm
                            New Password</label>
                        <input type="password" class="form-control" name="confirmpassword" id="confirmpassword"
                            data-placeholder-key="confirmpassword">

                        <div id="confirmpassword-error" class="text-danger form-error" style="display: none;">
                        </div>

                    </div>

                    <div class="row ">
                        <div class="modal-footer">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button type="submit" class="btn btn-primary submit lang" key="save_changes" action="insert"
                                id="buttonaction" name="buttonaction">Save Changes</button>


                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@if ($showAuditorSection)
<div class="container mt-4">
    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item" role="presentation" onclick="hideDatatable()">
            <button class="nav-link active" id="scehduled_plans_tab" data-bs-toggle="tab"
                data-bs-target="#scehduled_plans_tab-pane" type="button" role="tab"
                aria-controls="scehduled_plans_tab-pane" aria-selected="true">
                Transactions
            </button>
        </li>
        <li class="nav-item" role="presentation" onclick="hideDatatable()">
            <button class="nav-link" id="slip_details_tab" data-bs-toggle="tab" data-bs-target="#slip_details_tab-pane"
                type="button" role="tab" aria-controls="slip_details_tab-pane" aria-selected="false">
                Slip Details
            </button>
        </li>
        @if (
        $institutionDetails->contains(function ($item) {
        return $item->auditteamhead === 'Y';
        }))
        <li class="nav-item" role="presentation" onclick="hideDatatable()">
            <button class="nav-link" id="all-slip-tab" data-bs-toggle="tab" data-bs-target="#all-slip-tab-pane"
                type="button" role="tab" aria-controls="all-slip-tab-pane" aria-selected="false">
                All Slip Details
            </button>
        </li>
        @endif

    </ul>
    @endif
</div>
<br>

@if ($showAuditorSection)
<div class="card mt-3 mx-auto" id="view_Details" style="width: 96%; border: 1px solid grey;">
    <div class="card-body">
        <div class="tab-content pt-2 container-fluid">
            <!-- First Tab Pane -->
            <div class="tab-pane fade show active" id="scehduled_plans_tab-pane" role="tabpanel"
                aria-labelledby="scehduled_plans_tab">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip" style="background-color:rgb(10, 100, 173)">
                            <div class="card-body "
                                style="cursor: <?php echo $countDetails[0]['inboxcount'] == 0 || empty($countDetails[0]['inboxcount']) ? 'default' : 'pointer'; ?>"
                                <?php echo $countDetails[0]['inboxcount']==0 || empty($countDetails[0]['inboxcount'])
                                ? '' : 'onclick="window.location.href=\' init_fieldaudit\';"'; ?>>
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-inbox fs-7"></i> <!-- Changed to inbox icon -->
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="" class="card-title mb-1 text-white">
                                            {{ $countDetails[0]['inboxcount'] ?? 0 }}</h4>
                                        <p class="card-subtitle text-white">Inbox</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip" style="background-color:rgb(134, 10, 97);">
                            <div class="card-body"
                                style="cursor: <?php echo $countDetails[0]['sentcount'] == 0 || empty($countDetails[0]['sentcount']) ? 'default' : 'pointer'; ?>; >"
                                <?php echo $countDetails[0]['sentcount']==0 || empty($countDetails[0]['sentcount']) ? ''
                                : 'onclick="window.location.href=\' sentdetails\';"'; ?>>
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-send fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="" class="card-title mb-1 text-white">
                                            {{ $countDetails[0]['sentcount'] ?? 0 }}</h4>
                                        <p class="card-subtitle text-white ">Sent</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Tab Pane -->
            <div class="tab-pane fade" id="slip_details_tab-pane" role="tabpanel" aria-labelledby="slip_details_tab">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <label class="form-label required">Institution</label>
                        <select class="form-select" id="auditscheduleid" name="auditscheduleid"
                            style="border:1px solid black; color:black;">
                            <option value="0">All</option>
                            @foreach ($institutionDetails as $institution)
                            <option value="{{ $institution->auditscheduleid }}" {{ old('auditscheduleid',
                                request('auditscheduleid'))==$institution->auditscheduleid ? 'selected' : '' }}>
                                {{ $institution->instename }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <br>
                <!-- Wrap all cards inside a single row -->
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip"
                            style="background-color:rgb(5, 150, 207);
                cursor: <?php echo $countDetails[0]['teamcount'] == 0 || empty($countDetails[0]['teamcount']) ? 'default' : 'pointer'; ?>;">
                            <!-- Redundant block -->
                            <div class="card-body"
                                onclick="populateDataTable('scheduledinst', 'auditscheduleid','cnt_team','rgb(5, 150, 207)','Scheduled Plans')">
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-clipboard-text fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="cnt_team" class="card-title mb-1 text-white">
                                            <?php echo $countDetails[0]['teamcount'] ?? 0; ?>
                                        </h4>
                                        <p class="card-subtitle text-white">Scheduled Plans</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip"
                            style="background-color:rgb(167, 17, 227); cursor: <?php echo $countDetails[0]['totalslipcount'] == 0 || empty($countDetails[0]['totalslipcount']) ? 'default' : 'pointer'; ?>;">
                            <div class="card-body"
                                onclick="populateDataTable('allslip', 'auditscheduleid','cnt_total','rgb(167, 17, 227)','Total Slip Generated')">
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-file-stack fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="cnt_total" class="card-title mb-1 text-white">
                                            <?php echo $countDetails[0]['totalslipcount'] ?? 0; ?>
                                        </h4>
                                        <p class="card-subtitle text-white">Total Slip Generated</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip"
                            style="background-color:rgb(191, 96, 79); cursor: <?php echo $countDetails[0]['processslipcount'] == 0 || empty($countDetails[0]['processslipcount']) ? 'default' : 'pointer'; ?>;">
                            <div class="card-body"
                                onclick="populateDataTable('pendingslip', 'auditscheduleid','cnt_pending','rgb(191, 96, 79)','Pending Slips')">
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-hourglass fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="cnt_pending" class="card-title mb-1 text-white">
                                            <?php echo $countDetails[0]['processslipcount'] ?? 0; ?>
                                        </h4>
                                        <p class="card-subtitle text-white">Pending Slips</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-slip"
                            style="background-color:rgb(6, 211, 98); cursor: <?php echo $countDetails[0]['completedcount'] == 0 || empty($countDetails[0]['completedcount']) ? 'default' : 'pointer'; ?>;">
                            <div class="card-body"
                                onclick="populateDataTable('completedslip', 'auditscheduleid','cnt_completed','rgb(6, 211, 98)','Completed Slips')">
                                <div class="d-flex flex-row gap-6 align-items-center">
                                    <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                        style="background: white !important; color: #007bff !important;">
                                        <i class="ti ti-clipboard-check fs-7"></i>
                                    </div>
                                    <div class="align-self-center">
                                        <h4 id="cnt_completed" class="card-title mb-1 text-white">
                                            <?php echo $countDetails[0]['completedcount']; ?>
                                        </h4>
                                        <p class="card-subtitle text-white">Completed Slip</p>
                                        <i
                                            class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End of row -->
            </div>


            @if (
            $headinstitutionDetails->contains(function ($item) {
            return $item->auditteamhead === 'Y';
            }))
            <div class="tab-pane fade" id="all-slip-tab-pane" role="tabpanel" aria-labelledby="all-slip-tab">

                <div class="row justify-content-center" onchange="toggleCardsVisibility()">
                    <div class="row justify-content-center" style='margin-bottom:2%;'>
                        <!-- Center the content horizontally -->
                        <div class="col-md-4">
                            <!-- Set the width of the dropdown -->
                            <label class="form-label required">Institution</label>
                            <select class="form-select" id="auditscheduleid2" name="auditscheduleid"
                                style='border:1px solid black;color:black'>
                                <option value="0">Select</option>
                                @foreach ($headinstitutionDetails as $inst)
                                <option value="{{ $inst->auditscheduleid }}" {{ old('auditscheduleid',
                                    request('auditscheduleid'))==$inst->auditscheduleid ? 'selected' : '' }}>
                                    {{ $inst->instename }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <!-- Cards Container -->
                    <div id="cards-container" class="row justify-content-center">
                        <!-- Initially hidden -->

                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip"
                                style="background-color:rgb(167, 17, 227); cursor: {{ $countDetails[0]['totalslipcount'] == 0 || empty($countDetails[0]['totalslipcount']) ? 'default' : 'pointer' }};">
                                <div class="card-body"
                                    onclick="populateDataTable('allslip','auditscheduleid2','cnt_total_all','rgb(167, 17, 227)','Total Slip Generated')">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                            style="background: white !important; color: #007bff !important;">
                                            <i class="ti ti-file-stack fs-7"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_total_all" class="card-title mb-1 text-white">
                                                {{ $countDetails[0]['totalslipcount'] ?? 0 }}</h4>
                                            <p class="card-subtitle text-white">Total Slip Generated</p>
                                            <i
                                                class="ti ti-arrow-right fs-8 position-absolute bottom-0 end-0 mb-2 me-2 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip" style="background-color:rgb(6, 140, 75);">
                                <div class="card-body"
                                    onclick="populateDataTable('pendingslip','auditscheduleid2','cnt_pending_all','rgb(6, 140, 75)','Pending Slips')">

                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div class="round-40 rounded-circle d-flex align-items-center justify-content-center"
                                            style="background: white !important; color: #007bff !important;">
                                            <i class="ti ti-transform fs-7"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id="cnt_pending_all" class="card-title mb-1 text-white">
                                                {{ $countDetails[0]['processslipcount'] ?? 0 }}</h4>
                                            <p class="card-subtitle text-white">Pending Slip</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card card-slip" style="background-color:rgb(91, 61, 161);">
                                <div class="card-body"
                                    onclick="populateDataTable('completedslip', 'auditscheduleid2','cnt_completed_all','rgb(91, 61, 161)','Completed Slips')">
                                    <div class="d-flex flex-row gap-6 align-items-center">
                                        <div class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center"
                                            style="background: white !important; color: #007bff !important;">
                                            <i class="ti ti-clipboard-text fs-7"></i>
                                        </div>
                                        <div class="align-self-center">
                                            <h4 id='cnt_completed_all' class="card-title mb-1 text-white">
                                                {{ $countDetails[0]['completedcount'] ?? 0 }}</h4>
                                            <p class="card-subtitle text-white ">Completed Slips</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div id="allocatedplans_tab-pane" role="tabpanel" aria-labelledby="allocatedplans_tab"
                class="tab-pane fade">
                <div class="">
                    <div class="card-header lang" style="background-color:white;">
                        <h4 class="text-center mb-0 fs-7 fw-bolder">Department wise Audit Plan Details</h4>
                        <div class="row">
                            <div class="col-md-1 mb-1 ms-auto" id="quarter" style='margin-right:6%;'>
                                <label class="form-label  lang" key="" for="quarter">Quarter</label>

                                <select class="form-select mr-sm-2 lang-dropdown" id="quarter" name="quarter"
                                    onchange="initDeptwiseDataTable(this.value, 'plantabform', 'deptwisedata_table')">
                                    @if (!empty($auditQuarters) && count($auditQuarters) > 0)
                                    @foreach ($auditQuarters as $quarter)
                    <option value="{{ $quarter->group_key }}"
                            data-name-en="{{ $quarter->planname }}">
                        {{ $quarter->planname }}
                    </option>
                @endforeach
                                    @else
                                    <option disabled data-name-en="No Quarter Available"
                                        data-name-ta="காலாண்டு எதுவும் இல்லை">
                                        No Quarters Available
                                    </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div style="width:85%;margin:0 auto;" class="datatables">
                        <div class="table-responsive" id="tableshow">
                            <table id="deptwisedata_table"
                                class="table w-100  table-bordered display text-nowrap datatables-basic schedulingtable">
                                <thead>
                                    <tr>
                                        <th style="width:5% !important;text-align:center !important;">Sl. No</th>
                                        <th style="width:58% !important;text-align:left !important;">Name of the
                                            Department</th>
                                        <th style="width:12% !important;text-align:center !important;">Total No.of
                                            Audit Region</th>
                                        <th style="width:12% !important;text-align:center !important;">Total No.of
                                            Audit District</th>
                                        <th style="width:12% !important;text-align:center !important;">Total No.of
                                            Auditable Institutions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right">Total</th>
                                        <th id="footer_region"></th>
                                        <th id="footer_district"></th>
                                        <th id="footer_institute"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-4"></div>
                </div>
            </div>

            <div id="allocatedslips_tab-pane" role="tabpanel" aria-labelledby="allocatedslips_tab"
                class="tab-pane fade">
                <div class="">
                    <div class="card-header lang" style="background-color:white;">
                        <h4 class="text-center mb-0 fs-7 fw-bolder">Department wise Audit Slip Details</h4>
                        <div class="row">
                            <div class="col-md-1 mb-1 ms-auto" id="slipquarter" style='margin-right:2%;'>
                                <label class="form-label  lang" key="" for="slipquarter">Quarter</label>

                                <select class="form-select mr-sm-2 lang-dropdown" id="slipquarter" name="slipquarter"
                                    onchange="initDeptwiseSlipDataTable(this.value, 'sliptabform', 'Slip_deptwisedata_table')">
                                    @if (!empty($auditQuarters) && count($auditQuarters) > 0)
                                   @foreach ($auditQuarters as $quarter)
                    <option value="{{ $quarter->group_key }}"
                            data-name-en="{{ $quarter->planname }}">
                        {{ $quarter->planname }}
                    </option>
                @endforeach
                                    @else
                                    <option disabled data-name-en="No Quarter Available"
                                        data-name-ta="காலாண்டு எதுவும் இல்லை">
                                        No Quarters Available
                                    </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div style="width:94%;margin:0 auto;" class="datatables">
                        <div class="table-responsive" id="tableshow">
                            <table id="Slip_deptwisedata_table"
                                class="table w-100  table-bordered display text-nowrap datatables-basic schedulingtable">
                                <thead>
                                    <tr>
                                        <th style="width:5% !important;text-align:center !important;">Sl. No</th>
                                        <th style="width:31% !important;text-align:left !important;">Name of the
                                            Department</th>
                                        <th style="width:8% !important;text-align:center !important;">Total No.of
                                            Audit Region</th>
                                        <th style="width:8% !important;text-align:center !important;">Total No.of
                                            Audit District</th>
                                        <th style="width:8% !important;text-align:center !important;">Total No.of
                                            Institutions <br> Audit Commenced</th>
                                        <th style="width:10% !important;text-align:center !important;">Total
                                            Slip(s)</th>
                                        <th style="width:10% !important;text-align:center !important;">Pending
                                            Slip(s)</th>
                                        <th style="width:10% !important;text-align:center !important;">Converted to
                                            Para(s)</th>
                                        <th style="width:10% !important;text-align:center !important;">Dropped
                                            Slip(s)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right">Total</th>
                                        <th id="footer_region"></th>
                                        <th id="footer_district"></th>
                                        <th id="footer_institute"></th>
                                        <th id="footer_totalslip"></th>
                                        <th id="footer_pendingslip"></th>
                                        <th id="footer_convertedslip"></th>
                                        <th id="footer_droppedslip"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-4"></div>
                </div>

            </div>
        </div>



    </div>
    <div class="d-flex justify-content-center">
        <div class="card" id="dataTableContainer"
            style="display: none; border: 1px solid grey; width: 100%;margin-top:1%;">
            <div class="card-header card_header_color">
                <span class="showinstname"></span>
            </div>
            <br>
            <div class="table-responsive usertable_detail_wrapper container">
                <table id="dataTable"
                    class="table w-80 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr id="tableHeader"></tr> <!-- Empty header row will be populated dynamically -->
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<br>
@endif

<div class="card  card_border hide_this mx-auto regionwisetable" style="width: 95%; border:1px solid grey !important;">
    <br>

    <div class="card-header lang" style="background-color:white;">
        <h4 class="text-center mb-0 fs-7 fw-bolder"><span class="deptname_show_Reg"></span></h4>
        <i style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times regwiseclose_btn"></i>
    </div>
    <div style="width:85%;margin:0 auto;" class="datatables">
        <div class="table-responsive" id="tableshow">

            <table id="RegionTable"
                class="table w-100 table-bordered display text-nowrap datatables-basic schedulingtable">
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
    <br>
</div>


<div id="districtwisetable" class="  card_border hide_this mx-auto districtwisetable"
    style="width: 95%; border:1px solid grey !important;">
    <br>

    <div class="card-header lang" style="background-color:white;">
        <h4 class="text-center mb-0 fs-7 fw-bolder"><span class="deptname_show_Reg"></span></h4>
        <i style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times distwiseclose_btn"></i>
    </div>
    <div style="width:85%;margin:0 auto;" class="datatables">
        <div class="table-responsive" id="tableshow">
            <table id="DistrictTable"
                class="table w-100 table-bordered display text-nowrap datatables-basic schedulingtable">
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
    <br>
</div>
<br>

<div id="commencedinstitutewisetable" class=" hide_this mx-auto institutewisetable"
    style="width: 95%;border-color:grey !important;">


    <div class="card-header lang" style="background-color:white;">
        <h4 class="text-center mb-0 fs-7 fw-bolder"> Institute wise Audit Slip Details for <br><span
                class="deptname_show"></span></h4>
        <i style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times instclose_btn"></i>
    </div>
    <br>
    <div style="width:95%;margin:0 auto;" class="datatables">


        <div class="table-responsive" id="tableshow">
            <table id="CommencedinstituteTable"
                class="table w-100  display  table-bordered text-nowrap datatables-basic schedulingtable">
                <thead>
                    @csrf
                    <tr>
                        <th class="text-wrap">Sl.<br>No</th>
                        <th class="text-wrap">Institution</th>
                        <th class="text-wrap">Team Members</th>
                        <th style="width:5% !important;">Man<br>Days</th>
                        <th class="text-wrap">Region</th>
                        <th class="text-wrap">District</th>
                        <th class="text-wrap">From Date</th>
                        <th class="text-wrap">To Date</th>
                        <th class="text-wrap">Entry Meeting</th>
                        <th class="text-wrap">Exit Meeting</th>
                        <th class="text-wrap">Total Slip(s)</th>
                        <th class="text-wrap">Pending Slip(s)</th>
                        <th class="text-wrap">Converted to Para(s)</th>
                        <th class="text-wrap">Dropped Slip(s)</th>
                        <th class="text-wrap">Status</th>

                    </tr>
                </thead>

            </table>
        </div>
    </div>
    <br>
</div>

<div id="institutewisetable" class=" hide_this mx-auto institutewisetable"
    style="width: 95%;border-color:grey !important;">


    <div class="card-header lang" style="background-color:white;">
        <h4 class="text-center mb-0 fs-7 fw-bolder"> Institute wise Audit Plan Details for <br><span
                class="deptname_show"></span></h4>
        <i style="float:right;font-size:25px;margin-top:-25px;;" class="fas fa-times instclose_btn"></i>
    </div>
    <br>
    <div style="width:95%;margin:0 auto;" class="datatables">


        <div class="table-responsive" id="tableshow">
            <table id="instituteTable"
                class="table w-100  display  table-bordered text-nowrap datatables-basic schedulingtable">
                <thead>
                    @csrf
                    <tr>
                        <th>Sl.<br>No</th>
                        <th>Institution</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Team Members</th>
                        <th style="width:5% !important;">Man<br>Days</th>
                        <th>Region</th>
                        <th>District</th>
                        <th class="text-wrap">From Date</th>
                        <th class="text-wrap">To Date</th>
                        <th>Schedule Status</th>
                        <th>Intimation Status</th>
                        <th>Work Allocation</th>
                        <th>Entry Meeting</th>
                        <th>Exit Meeting</th>
                        <!-- <th >Status of Field Audit</th>-->
                        <!-- <th >Slip Status</th>-->
                    </tr>
                </thead>

            </table>
        </div>
    </div>
    <br>
</div>
<br>


<div class="modal fade" id="HistoryModel" tabindex="-1" aria-labelledby="HistoryModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;">Flow of Slip No <b id="slipnodyn"></b></h4>

                <button type="button" class="btn-close" onclick="RemoveTempFile()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <div class="datatables">
                        <div class="table-responsive" id="tableshow">
                            <table id="slipHistoryTable"
                                class="table w-100 table-striped table-bordered display datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th>Forwarded By</th>
                                        <th>Forwarded To</th>
                                        <th>Slip Status</th>
                                        <th>Forwarded On</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='his_no_data_details' class='hide_this' style="border:1px solid #ddd;padding:10px;">
                        <center>No Data Available</center>
                    </div>
                </div>
                <br><br>
                <input type="text" id="filename" style="display: none;" />
                <!-- Button container with flexbox for centering -->
                <div class="text-center mt-3" style="margin-t">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="ParaHistoryModel" tabindex="-1" aria-labelledby="ParaHistoryModel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;">Para Flow For <b id="paranodyn"></b></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="para-pdf-preview" style="width: 100%;">
                    <div class="datatables">
                        <div class="table-responsive" id="paratableshow">
                            <table id="paraHistoryTable"
                                class="table w-100 table-striped table-bordered display datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang" key="s_no">S.No</th>
                                        <th>Forwarded By</th>
                                        <th>Forwarded To</th>
                                        <th>Para Status</th>
                                        <th>Forwarded On</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='para_no_data_details' class='hide_this' style="border:1px solid #ddd;padding:10px;">
                        <center>No Data Available</center>
                    </div>
                </div>
                <br><br>
                <input type="text" id="parafilename" style="display: none;" />
                <!-- Button container with flexbox for centering -->
                <div class="text-center mt-3" style="margin-t">
                    <button id="paraDownloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewSlipModel" tabindex="-1" aria-labelledby="ViewSlipModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;font-weight:600;">Slip Details of <span
                        class="slipnodyn"></span></h4>

                <button type="button" class="btn-close" onclick="closebtn()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview" style="width: 100%;">
                    <div>

                        <div class="table-container" id="auditsliptable">
                        </div>
                        <div class="liabilitydetails">
                            <h5>
                                <center><b>Liability Details</b></center>
                            </h5>
                            <table id="liabilitiesTable" class="auditor-table">
                                <thead>

                                    <tr>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Designation</th>
                                        <th>Amount Involved</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="auditorremarksdiv" style="display:none;">
                        <br>
                        <div
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                            <h5>
                                <center><b>Auditor Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditor Name</th>
                                            <td class="auditorname"></td>
                                        </tr>


                                    </tbody>
                                </table>
                                <div class="accordion" id="auditor">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="auditor_head">
                                            <button class="accordion-button bg-primary-subtle   collapsed"
                                                style="height:20px" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#auditor_acc" aria-expanded="false"
                                                aria-controls="collapseOne">
                                                <b>Auditor Observation /Remarks</b>
                                            </button>
                                        </h2>
                                        <div id="auditor_acc" class="accordion-collapse collapse"
                                            aria-labelledby="auditor_head" data-bs-parent="#auditor">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">

                                                        <label class="form-label lang" for="validationDefaultUsername"
                                                            key="observation">Auditor Remarks</label>
                                                        <textarea id="viewslip_auditorremarkscccz" class="form-control"
                                                            placeholder="Enter remarks"
                                                            name="viewslip_auditorremarks"></textarea>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auditeeremarksdiv" style="display:none;">
                        <br>
                        <div
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px; ">
                            <h5>
                                <center><b>Auditee Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditee Name</th>
                                            <td class="auditeename"></td>
                                        </tr>

                                    </tbody>
                                </table>

                                <div class="accordion mt-3" id="auditee">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button bg-primary-subtle collapsed"
                                                style="height:20px" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                <b>Auditee Reply</b>
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#auditee">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label lang"
                                                            for="validationDefaultUsername">Auditee
                                                            Reply</label>

                                                        <textarea id="viewslip_auditeeremarks" class="form-control"
                                                            placeholder="Enter remarks"
                                                            name="viewslip_auditeeremarks"></textarea>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="auditorreplydiv" style="display:none;">
                        <br>
                        <div class=""
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                            <h5>
                                <center><b>Auditor Reply</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Auditor Reply</th>
                                            <td class="auditoreply_remarks"></td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="teamheaddiv" style="display:none;">
                        <br>
                        <div class="teamheaddiv"
                            style="border: 1px solid #d3d3d3;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);padding:10px;">
                            <h5>
                                <center><b>Team Head Details</b></center>
                            </h5>
                            <div class="table-container">
                                <table class="auditor-table table_slip">
                                    <tbody>
                                        <tr>
                                            <th>Team Head Name</th>
                                            <td class="teamheadname"></td>
                                        </tr>
                                        <tr>
                                            <th>Team Head Final Remarks</th>
                                            <td class="finalremarks"></td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <input type="text" id="filename" style="display: none;" />
                <div class="text-center mt-3" style="margin-t">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="ViewParaModel" tabindex="-1" aria-labelledby="ViewParaModel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;font-weight:600;">Para Details of <span class="parano"></span>
                </h4>
                <button type="button" class="btn-close" onclick="closebtn()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- The iframe will be inserted dynamically here -->
                <div id="pdf-preview-para" style="width: 100%;">
                    <div>
                        <!-- Replace the old table container with accordion -->
                        <div class="accordion" id="paraHistoriesAccordion">
                            <!-- Accordion items will be dynamically inserted here by JavaScript -->
                        </div>

                        <!-- Keep your existing sections but they'll be handled differently -->
                        <div class="liabilitydetails" style="display:none;">
                            <h5>
                                <center><b>Liability Details</b></center>
                            </h5>
                            <table id="liabilitiesTable-para" class="auditor-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Designation</th>
                                        <th>Amount Involved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Liability data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                <br><br>
                <input type="text" id="filename-para" style="display: none;" />
                <div class="text-center mt-3" style="margin-t">
                    <button id="downloadBtn-para" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="liabilityModal" role="dialog" aria-labelledby="liabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center  justify-content-center">
                <h5 class="modal-title" id="liabilityModalLabel">Liability Details</h5>
                <!-- <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                </button> -->
            </div>
            <div class="modal-body" id="liabilityModalContent"
                style="max-height: 70vh; width: 100%; overflow: hidden; padding: 10px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewInspectionModel" tabindex="-1" aria-labelledby="ViewInspectionModel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#ffffff !important;">
                <h4 style="text-align:center !important;font-weight:600;">Inspection Details of <span
                        class="slipnodyn"></span></h4>
                <button type="button" class="btn-close" onclick="closebtn()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="accordion" id="inspectionHistoriesAccordion">
                </div>

                <div id='his_no_data_details' class='hide_this text-center p-3 border rounded mt-3'>
                    No Inspection History Data Available
                </div>

                <br>
                <div class="text-center mt-3">
                    <button id="downloadBtn" class="btn btn-info" style="display: none;">
                        <i class="fas fa-download"></i>&nbsp;&nbsp;Download Report
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="slipDetailsModal" tabindex="-1" aria-labelledby="slipDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header d-flex justify-content-center position-relative">
                <h5 class="modal-title fw-bold text-center flex-grow-1" id="slipDetailsModalLabel"
                    style="font-size: 1.25rem;">
                    Legacy Para Details
                </h5>

            </div>
            <div class="modal-body">
                <h4 id="modalInstitutionName" class="text-center mb-3 fw-bold"
                    style="font-weight: 300; font-size: 1.5rem; color: #333;">
                </h4>
                <div class="d-flex justify-content-center">
                    <div style="width:80%;">
                        <table class="table table-bordered" id="slipDetailsTable">
                            <tbody>
                            </tbody>
                        </table>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">Remarks</h6>
                            <textarea id="remarksEditor" style="width:100%; height:120px;"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Store the initial financial year
        let currentFinancialYear = $('#year-filter').val();
        let currentDepartment = $('#department-filter').val();
        let isInitialLoad = true;
        let isLoadingQuarters = false;
        let isApplyingFilters = false;

        // Store initial filter values
        let initialFilterValues = {
            auditProgram: $('#audit-program-filter').val(),
            year: $('#year-filter').val(),
            quarter: $('#quarter-filter').val(),
            department: $('#department-filter').val()
        };

        // Function to load quarters based on financial year and department
        function loadQuartersForFinancialYear(financialYearCode, departmentCode, callback) {
            if (isLoadingQuarters) return;

            isLoadingQuarters = true;
            const $quarterFilter = $('#quarter-filter');

            // Show loading state
            // $quarterFilter.html('<option>Loading...</option>');

            $.ajax({
                url: '{{ route("get.quarters.by.financial.year") }}',
                type: 'POST',
                data: {
                    deptcode: departmentCode,
                    financialyearcode: financialYearCode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.quarters && response.quarters.length > 0) {

                        let options = '';

                        $.each(response.quarters, function(index, quarter) {

                            let value = quarter.group_key;   // ✅ ALWAYS use group_key
                            let text = quarter.planname;

                            options += `<option value="${value}">${text}</option>`;
                        });

                        $quarterFilter.html(options);

                        currentFinancialYear = financialYearCode;
                        currentDepartment = departmentCode;

                        if (callback && typeof callback === 'function') {
                            callback(response.quarters);
                        }

                    } else {
                        $quarterFilter.html('<option value="">No quarters available</option>');
                        if (callback && typeof callback === 'function') {
                            callback([]);
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error loading quarters:', xhr);
                    $quarterFilter.html('<option value="">Error loading quarters</option>');
                    if (callback && typeof callback === 'function') {
                        callback([]);
                    }
                },
                complete: function() {
                    isLoadingQuarters = false;
                }
            });
        }

        // Function to refresh quarters based on current selections
        function refreshQuarters() {
            const selectedDepartment = $('#department-filter').val();
            const selectedYear = $('#year-filter').val();
            const currentProgram = $('#audit-program-filter').val();

            const isLegacyOrPara = currentProgram === 'legacyreport' ||
                                currentProgram === 'parareport' ||
                                currentProgram === 'paracount' ||
                                currentProgram === 'retirementpara';

            // Only load quarters if not in legacy/para mode AND a specific department is selected
            if (!isLegacyOrPara && selectedDepartment  && selectedYear) {
                loadQuartersForFinancialYear(selectedYear, selectedDepartment, function(quarters) {
                    if (quarters && quarters.length > 0) {
                        // Optionally set a default quarter
                                            const firstQuarter = quarters[0];

                        let firstValue = firstQuarter.group_key;

                        if (firstValue && $('#quarter-filter').val() !== firstValue) {
                            $('#quarter-filter').val(firstValue);
                        }
                    }
                    checkFiltersChanged();
                });
            } else {
                // Clear and hide quarter filter if no department selected
                $('#quarter-filter').html('<option value="">Select quarter</option>');
                if (!isLegacyOrPara && (!selectedDepartment)) {
                    $('#quarter-filter-group').hide();
                } else {
                    $('#quarter-filter-group').show();
                }
                checkFiltersChanged();
            }
        }

        // Initial load - only if a department is selected
        const initialYear = $('#year-filter').val();
        const initialDept = $('#department-filter').val();

        if (initialYear && initialDept) {
            refreshQuarters();
        }

        // Year filter change handler
        $('#year-filter').on('change', function() {
           // showLoadingFilter();
            refreshQuarters();
        });

        // Quarter filter change handler
        $('#quarter-filter').on('change', function() {
            //showLoadingFilter();
            checkFiltersChanged();
        });

        // Audit program filter change handler
        $('#audit-program-filter').change(function() {
        //showLoadingFilter();

            const selectedProgram = $(this).val();
            const isLegacyOrPara = selectedProgram === 'legacyreport' ||
                                  selectedProgram === 'parareport' ||
                                  selectedProgram === 'paracount' ||
                                  selectedProgram === 'retirementpara';

            if (isLegacyOrPara) {
                $('#quarter-filter-group').hide();
                $('#year-filter-group').hide();
                $('#audit-year-group').show();
                $('#quarter-filter').val('');
            } else {
                $('#quarter-filter-group').show();
                $('#year-filter-group').show();
                $('#audit-year-group').hide();

                refreshQuarters();
            }

            checkFiltersChanged();
        });

        // Department filter change handler
        $('#department-filter').on('change', function() {
            //showLoadingFilter();

            const selectedDepartment = $(this).val();
            const isDepartmentSelected = selectedDepartment && selectedDepartment !== 'all';
            const currentProgram = $('#audit-program-filter').val();

            const isLegacyOrPara = currentProgram === 'legacyreport' ||
                                currentProgram === 'parareport' ||
                                currentProgram === 'paracount' ||
                                currentProgram === 'retirementpara';

            if (!isLegacyOrPara) {
                if (isDepartmentSelected) {
                    $('#quarter-filter-group').show();
                    // Refresh quarters for the selected department
                    refreshQuarters();
                } else {
                   refreshQuarters();
                    // $('#quarter-filter').html('<option value="">Select quarter</option>');
                }
            }

            checkFiltersChanged();
        });

        // Apply filters button logic
        $('#apply-filters-btn').prop('disabled', true);

        $('#apply-filters-btn').on('click', function() {
            initialFilterValues = {
                auditProgram: $('#audit-program-filter').val(),
                year: $('#year-filter').val(),
                quarter: $('#quarter-filter').val(),
                department: $('#department-filter').val()
            };

            $('#apply-filters-btn').prop('disabled', true);

            if (typeof applyFilters === 'function') {
                applyFilters();
            }
        });



        // Initial check to see if quarters need loading
        const checkInitialQuarters = function() {
            const yearOptions = $('#quarter-filter option').length;
            const firstOption = $('#quarter-filter option:first').val();

            if (yearOptions <= 1 || firstOption === '' || firstOption === 'Loading...') {
                refreshQuarters();
            }
        };

        // Delay initial check to ensure DOM is fully loaded
        setTimeout(checkInitialQuarters, 500);
    });
</script>

<script>


        function initializeDashboardVisibility() {
            const showGlobalDashboard = {{ $showGlobalDashboard ? 'true' : 'false' }};
            const showAnyTabs = {{ $showAnyTabs ? 'true' : 'false' }};


            if (showGlobalDashboard) {
                initializeGlobalDashboard();
            }

            if (showAnyTabs) {
                initializeTabBasedDashboard();
            }
        }

        function initializeGlobalDashboard() {
            loadFilterOptions().then(() => {
                loadDashboardData();
            });
        }

        function initializeTabBasedDashboard() {

            const allTabs = document.querySelectorAll('.nav-item visible');
            allTabs.forEach(tab => {
                tab.style.display = 'none';
            });

            const transactionsTab = document.getElementById('scehduled_plans_tab')?.closest('.nav-item visible');
            if (transactionsTab) {
                transactionsTab.style.display = 'block';
            }

            @if ($showSlipDetails)
                const slipDetailsTab = document.getElementById('slip_details_tab')?.closest('.nav-item visible');
                if (slipDetailsTab) slipDetailsTab.style.display = 'block';
            @endif

            @if ($showAllSlipDetails)
                const allSlipTab = document.getElementById('all-slip-tab')?.closest('.nav-item visible');
                if (allSlipTab) allSlipTab.style.display = 'block';
            @endif

            @if ($showAuditPlans)
                const auditPlansTab = document.getElementById('allocatedplans_tab')?.closest('.nav-item visible');
                if (auditPlansTab) auditPlansTab.style.display = 'block';
            @endif

            @if ($showAuditSlips)
                const auditSlipsTab = document.getElementById('allocatedslips_tab')?.closest('.nav-item visible');
                if (auditSlipsTab) auditSlipsTab.style.display = 'block';
            @endif

            setTimeout(() => {
                const activeTab = document.querySelector('.nav-link.active');
                if (!activeTab) {
                    const firstVisibleTab = document.querySelector('.nav-item[style="display: block"] .nav-link');
                    if (firstVisibleTab) {
                        firstVisibleTab.classList.add('active');
                        const targetPane = document.querySelector(firstVisibleTab.getAttribute('data-bs-target'));
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                        }
                    }
                }
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboardVisibility();
        });


        const tabs = document.querySelectorAll(".nav-link");
        const viewDetailsCard = document.getElementById("view_Details");
        const tabPanes = document.querySelectorAll(".tab-pane");
        const cardsContainer = document.getElementById("cards-container");

        viewDetailsCard?.style.setProperty("border", "1px solid black");

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                tabs.forEach(t => t.classList.remove("active"));
                tabPanes.forEach(pane => pane.classList.remove("show", "active"));

                this.classList.add("active");
                document.querySelector(this.getAttribute("data-bs-target"))?.classList.add("show",
                    "active");

                if (this.id === "all-slip-tab") {
                    cardsContainer?.style.setProperty("display", "flex");
                } else {
                    cardsContainer?.style.setProperty("display", "none");
                }
            });
        });

        function resetDropdowns() {
            $('#auditscheduleid1').val(0);
            $('#auditscheduleid2').val(0);
            var selectedValue = document.getElementById('auditscheduleid2').value;
            var cardsContainer = document.getElementById('cards-container');

            if (selectedValue === "0" || selectedValue === "") {
                cardsContainer.style.display = 'none';
            } else {
                cardsContainer.style.display = 'flex';
            }
        }

        function hideDatatable() {
            $('#dataTableContainer').hide();
            resetDropdowns();


        }

        function populateDataTable(description, dropdownId, countid, cardColor, cardname) {
            let auditScheduleId = $('#' + dropdownId).val();
            var institutionName = document.getElementById(dropdownId).options[
                document.getElementById(dropdownId).selectedIndex
            ].text;

            if ($('#scehduled_plans_tab').hasClass('active')) {
                tabValue = 1;
            } else if ($('#slip_details_tab').hasClass('active')) {
                tabValue = 2;
            } else if ($('#all-slip-tab').hasClass('active')) {
                tabValue = 3;
            }

            if (auditScheduleId > 0)
                cardname = institutionName + ' - ' + cardname;

            let countvalue = $('#' + countid).html();

            //alert(countvalue);
            if (countvalue === "0" || countvalue === 0) {
                hideDatatable();
                return;
            }

            $('#dataTableContainer').show();
            $('.card_header_color').css('background-color', cardColor);
            $('.showinstname').text(cardname);

            $.ajax({
                url: "{{ route('descriptionData') }}",
                type: 'GET',
                data: {
                    activeTab: tabValue,
                    auditscheduleid: auditScheduleId,
                    description: description,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    updateTable(response, description);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }


        $(document).ready(function() {
            $('#myTab .nav-link').on('click', function() {
                var activeTab = $(this).attr('id');

                window.activeTab = activeTab;
                $('.nav-link').removeClass('active');
                $(this).addClass('active');

                resetDropdowns(); // Reset dropdowns when switching tabs
                hideDatatable(); // Hide data table

                //setTimeout(fetchDataForActiveTab, 300);
            });

            let activeDropdown = null;



            $('#auditscheduleid, #auditscheduleid2').on('change', function() {

                activeDropdown = $(this).attr('id');
                var selectedValue = $(this).val();
                var tabValue = 0;


                if ($('#scehduled_plans_tab').hasClass('active')) {
                    tabValue = 1;
                } else if ($('#slip_details_tab').hasClass('active')) {
                    tabValue = 2;
                } else if ($('#all-slip-tab').hasClass('active')) {
                    tabValue = 3;
                }

                if (selectedValue) {
                    $.ajax({
                        url: "{{ route('CallingData') }}",
                        type: "POST",
                        data: {
                            activeTab: tabValue,
                            auditscheduleid: selectedValue,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            if (Array.isArray(response) && response.length > 0) {
                                const data = response[0];

                                if (activeDropdown === 'auditscheduleid') {
                                    updateCard('cnt_team', data.teamcount, 'scheduledinst',
                                        'auditscheduleid');
                                    updateCard('cnt_total', data.totalslipcount, 'allslip',
                                        'auditscheduleid');
                                    updateCard('cnt_pending', data.processslipcount,
                                        'pendingslip', 'auditscheduleid');
                                    updateCard('cnt_completed', data.completedcount,
                                        'completedslip', 'auditscheduleid');
                                } else if (activeDropdown === 'auditscheduleid2') {
                                    updateCard('cnt_total_all', data.totalslipcount, 'allslip',
                                        'auditscheduleid2');
                                    updateCard('cnt_pending_all', data.processslipcount,
                                        'pendingslip', 'auditscheduleid2');
                                    updateCard('cnt_completed_all', data.completedcount,
                                        'completedslip', 'auditscheduleid2');
                                }
                            }
                        },
                        error: function(error) {
                            console.error("Error fetching data:", error);
                        }
                    });
                }
            });

            function updateCard(countId, countValue, description, dropdownId) {
                let card = $('#' + countId).closest('.card-slip');

                // Update count text
                $('#' + countId).text(countValue);

                if (countValue === 0 || countValue === "0") {
                    card.css('cursor', 'default'); // Disable pointer cursor
                    card.off('click'); // Remove any click event attached
                } else {
                    card.css('cursor', 'pointer'); // Enable pointer cursor
                    card.off('click').on('click', function() {
                        //  populateDataTable(description, dropdownId);
                    });
                }
            }
        });


        function toggleCardsVisibility() {
            var selectedValue = document.getElementById('auditscheduleid2').value;
            var cardsContainer = document.getElementById('cards-container');

            // Hide the cards if "Select" is chosen
            if (selectedValue === "0" || selectedValue === "") {
                cardsContainer.style.display = 'none';
            } else {
                cardsContainer.style.display = 'flex';
            }
        }
        $(document).ready(function() {
            let dropdown = $('#auditscheduleid');
            let optionsCount = dropdown.children('option').length;


            if (optionsCount == 2) {
                let selectedOption = dropdown.find('option').not('[value="0"]').val();
                dropdown.val(selectedOption);
                dropdown.prop('disabled', true);
            }
        });





        $(document).ready(function() {

            $(document).on('click', '.toggle-password', function() {
                const target = $($(this).data('target'));
                const icon = $(this).find('i');
                if (target.attr('type') === 'password') {
                    target.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    target.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });



            //document.addEventListener("DOMContentLoaded", function() {
            @if ($profileUpdate === 'Y')
                var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                changePasswordModal.show();
            @endif
            //});


            $('#translate').change(function() {
                var lang = getLanguage('Y');
                changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn));
                updateValidationMessages(getLanguage('Y'), 'passwordForm');
            });

            $(document).ready(function() {
                $('#oldpassword, #newpassword, #confirmpassword').on('input', function() {
                    $('#' + this.id + '-error').hide();
                });
            });

        });

        function validatePassword(inputSelector, errorSelector) {
            const password = $(inputSelector).val().trim();
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,20}$/;

            let lang = window.localStorage.getItem('lang') || 'en';
            let errorMessage = {
                en: "Password must be 8-20 characters, include uppercase, lowercase, a number, and a special character.",
                ta: "?????????? 8-20 ??????????? ?????? ????????, ????? ???????, ????? ???????, ??? ???, ??????? ??? ??????? ??????? ????????????? ?????? ????????."
            };

            // Always clear the error first
            $(errorSelector).hide().text("");

            // Check password validity
            if (!passwordRegex.test(password)) {
                $(errorSelector).html(errorMessage[lang]).show(); // Show error message
                return false; // ? Invalid password
            }

            return true; // ? Valid password
        }

        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#passwordForm").validate({

                rules: {
                    oldpassword: {
                        required: true,
                    },
                    newpassword: {
                        required: true
                    },
                    confirmpassword: {
                        required: true
                    },


                },
                messages: errorMessages[language], // Set initial messages

            });


            $("#buttonaction").on("click", function(event) {
                event.preventDefault();


                let isFormValid = $("#passwordForm").valid(); // Step 1: Validate form

                if (!isFormValid) {
                    return false; // Stop submission if form is invalid
                }

                let isPasswordValid = validatePassword("#newpassword",
                    "#newpassword-error"); // Step 2: Validate password

                if (!isPasswordValid) {
                    return false; // Stop submission if password is invalid
                }


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                //alert();


                var formData = $('#passwordForm').serializeArray();



                $.ajax({
                    url: "{{ route('changepassword') }}", // URL where the form data will be posted
                    type: 'POST',
                    data: formData,
                    success: async function(response) {
                        if (response.success) {
                            reset_form(); // Reset the form after successful submission

                            getLabels_jsonlayout([{
                                id: response.message,
                                key: response.message
                            }], 'N').then((text) => {
                                passing_alert_value('Confirmation', Object.values(
                                        text)[0], 'confirmation_alert',
                                    'alert_header', 'alert_body',
                                    'confirmation_alert');
                            });

                            $("#changePasswordModal").modal("hide");





                        } else if (response.message) {

                        }
                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);
                        if (response.error == 401) {
                            handleUnauthorizedError();
                        } else {

                            getLabels_jsonlayout([{
                                    id: response.old,
                                    key: response.old
                                },
                                {
                                    id: response.oldandnew,
                                    key: response.oldandnew
                                },
                                {
                                    id: response.newandconf,
                                    key: response.newandconf
                                }
                            ], 'N').then((text) => {
                                $('#oldpassword-error').hide().text('');
                                $('#newpassword-error').hide().text('');
                                $('#confirmpassword-error').hide().text('');

                                if (response.old) {
                                    $('#oldpassword-error').show().text(text[response
                                        .old] || response.old);
                                }
                                if (response.oldandnew) {
                                    $('#newpassword-error').show().text(text[response
                                        .oldandnew] || response.oldandnew);
                                }
                                if (response.newandconf) {
                                    $('#confirmpassword-error').show().text(text[
                                            response.newandconf] || response
                                        .newandconf);
                                }
                            });


                        }
                    }
                });




            });

        }).catch(error => {
            console.error("Failed to load JSON data:", error);
        });

        function reset_form() {
            $('#passwordForm')[0].reset();

            $("#oldpassword-error").hide();
            $("#newpassword-error").hide();
            $("#confirmpassword-error").hide();



            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }





        // Define globalData outside of functions to make it accessible globally
        let globalData = [];

        function updateTable(data, description) {
            globalData = data;

            // Destroy existing DataTable instance if exists
            if ($.fn.DataTable.isDataTable("#dataTable")) {
                $('#dataTable').DataTable().clear().destroy();
            }

            let tabValue = 0;
            if ($('#scehduled_plans_tab').hasClass('active')) {
                tabValue = 1;
            } else if ($('#slip_details_tab').hasClass('active')) {
                tabValue = 2;
            } else if ($('#all-slip-tab').hasClass('active')) {
                tabValue = 3;
            }

            let headerHTML = ''; // Empty variable to store the dynamic header

            // Check the description and set columns accordingly
            if (description === 'scheduledinst') {
                headerHTML = `
                <th class="text-center" style="width: 3%;">S.No</th>
                <th class="text-center">Institute Name</th>
                <th class="text-center w-20">Act As<br> Team Head / Team Member</th>
                <th class="text-center">From Date</th>
                <th class="text-center">To Date</th>
            `;
            } else {
                headerHTML = `
                <th class="text-center" style="width: 2%;">S.No</th>
                <th class="text-center">Objection Title</th>
                <th class="text-center">Amount Involved</th>
                <th class="text-center">Severity</th>
            `;

                // Only add the "Created By" column if the third tab is active
                if (tabValue === 3) {
                    headerHTML += `<th class="text-center">Created By</th>`;
                }

                headerHTML += `
                <th class="text-center">Liability</th>
                <th class="text-center">Status</th>
                <th class="text-center">Pending At</th>
            `;

                // Add Liability Details column if liability is 'Y'
                headerHTML += `<th class="text-center">Liability Details</th>`;
            }

            // Dynamically update the table header
            $('#tableHeader').html(headerHTML);

            // Populate the table body with data
            $('#dataTable tbody').empty();

            if (Array.isArray(data) && data.length > 0) {
                let serialNumber = 1;

                const formatValue = (value) => (value === null || value === undefined || value === '') ? "-" : value;
                const formatLiability = (liability) => liability === "N" ? "No" : liability === "Y" ? "Yes" : "-";
                const formatProcessName = (processelname) => processelname === "Converted to para" ?
                    '<button class="btn btn-secondary btn-sm rounded-24 w-100">Converted to para</button>' :
                    processelname === "Dropped" ?
                    '<button class="btn btn-success btn-sm rounded-24 w-100">Dropped</button>' :
                    '<button class="btn btn-danger btn-sm rounded-24 w-100">Pending</button>';

                const formatAuditteam = (auditteamhead) => auditteamhead === "Y" ? "Team Head" : "Team Member";
                const formatDate = (dateString) => {
                    if (!dateString) return "-";
                    const date = new Date(dateString);
                    return `${("0" + date.getDate()).slice(-2)}-${("0" + (date.getMonth() + 1)).slice(-2)}-${date.getFullYear()}`;
                };

                data.forEach(row => {
                    let rowData = `<tr>`;
                    if (description === 'scheduledinst') {
                        rowData += `
                        <td class='text-end' style='min-width: 2px;'>${serialNumber++}</td>
                        <td>${formatValue(row.instename)}</td>
                        <td class='text-center'>${formatAuditteam(row.auditteamhead)}</td>
                        <td class='text-center'>${formatDate(row.fromdate)}</td>
                        <td class='text-center'>${formatDate(row.todate)}</td>
                    `;
                    } else {
                        rowData += `
                        <td class="text-end" style="min-width: 2px;">${serialNumber++}</td>
                        <td class='text-break'>${formatValue(row.objectionename)}</td>
                        <td class='text-center'>${formatValue(row.amtinvolved)}</td>
                        <td>${formatValue(row.severityelname)}</td>
                    `;

                        // Only include "Created By" column if the third tab is active
                        if (tabValue === 3) {
                            rowData += `<td>${formatValue(row.createdby)}</td>`;
                        }

                        rowData += `
                        <td class="text-center">${formatLiability(row.liability)}</td>
                        <td>${formatProcessName(row.processelname)}</td>
                    `;

                        rowData += `
                        <td class='text-break'>${formatValue(row.forwardto)}</td>
                    `;

                        // Display liability details if liability is 'Y'
                        if (row.liability === 'Y' && Array.isArray(row.liabilitydetails) && row.liabilitydetails
                            .length > 0) {
                            const buttonHTML =
                                `<button class="btn btn-info btn-sm" onclick="showLiabilityDetails(globalData, ${row.auditslipid})">View Details</button>`;

                            rowData += `<td class="text-center">${buttonHTML}</td>`;
                        } else {
                            rowData += `<td class="text-center">-</td>`;
                        }
                    }
                    rowData += `</tr>`;
                    $('#dataTable tbody').append(rowData);
                    $('#dataTableContainer').show();
                });
            } else {
                $('#dataTable tbody').append('<tr><td colspan="9" align="center">No Data Available</td></tr>');
            }

            $(document).ready(function() {
                var table = $('#dataTable').DataTable({
                    "searching": true,
                    "paging": false,
                    "info": false,
                    "order": [
                        [0, 'asc']
                    ],
                    "columnDefs": [{
                        "targets": '_all',
                        "orderable": true
                    }]
                });
            });
        }

        function showLiabilityDetails(data, auditslipid) {
            // Find the row based on the auditslipid
            const row = data.find(item => item.auditslipid === auditslipid);

            // Check if the row exists
            if (!row) {
                $('#liabilityModalContent').html("<p>No liability details available for this audit slip.</p>");
                $('#liabilityModal').modal('show');
                return;
            }

            let liabilityDetailsHTML = "<table class='table table-bordered'>";

            // Check if the row has liability details
            if (Array.isArray(row.liabilitydetails) && row.liabilitydetails.length > 0) {
                // Adding table header
                liabilityDetailsHTML += `
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Number</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                // Iterate through liability details and display them in table rows
                row.liabilitydetails.forEach(detail => {
                    liabilityDetailsHTML += `
                <tr>
                    <td>${detail.liabilitytype}</td>
                    <td>${detail.liabilityname}</td>
                    <td>${detail.liabilitydesignation}</td>
                    <td>${detail.liabilitynumber}</td>
                    <td>${detail.liabilityamount}</td>
                </tr>
            `;
                });

                liabilityDetailsHTML += `</tbody>`;
            } else {
                liabilityDetailsHTML = "<p>No liability details available for this audit slip.</p>";
            }

            // Update the modal content with the table
            $('#liabilityModalContent').html(liabilityDetailsHTML);
            // Show the modal
            $('#liabilityModal').modal('show');
        }
        /*allocated plans start */
        hideAllTables();

        function hideAllTables() {
            $('.institutewisetable, .districtwisetable, .regionwisetable').hide();
            $('#slipview_Details').addClass('hide_this');
            $('#paraview_details').addClass('hide_this');

        }


        $('.nav-link').on('click', function() {
            hideAllTables();
        });





        $('.regwiseclose_btn').on('click', function() {
            hideAllTables();
        });

        $('.distwiseclose_btn').on('click', function() {
            hideAllTables();
        });

        $('.instclose_btn').on('click', function() {
            $('.institutewisetable').hide();
            $('#slipview_Details').addClass('hide_this');
            $('#paraview_details').addClass('hide_this');

        });

        $('.slipclose_btn').on('click', function() {
            //$('#slipview_Details').hide();
            $('#slipview_Details').addClass('hide_this');
            $('#paraview_details').addClass('hide_this');

        });
</script>

<script>
    // --- Utility Functions ---

        $(document).on('click', '.view-inspection-btn', function() {
            const inspectionId = $(this).data('id');
            const institutionName = $(this).data('name');


            Open_inspectionmodel(inspectionId, institutionName);
        });

        function getProgramType() {
            return document.getElementById('audit-program-filter').value;
        }

        function isPlantabForm() {
            return getProgramType() === 'plantabform';
        }

        function isSlipTabForm() {
            return getProgramType() === 'sliptabform';
        }

        function isAuditReport() {
            return getProgramType() === 'auditreport';
        }

        function isTemplateAudit() {
            return getProgramType() === 'templateaudit';
        }

        function isInspectionAudit() {
            return getProgramType() === 'inspectionaudit';
        }

        function isLegacyReport() {
            return getProgramType() === 'legacyreport';
        }

        function isParaReport() {
            return getProgramType() === 'parareport';
        }

        function isParaCount() {
            return getProgramType() === 'paracount';
        }

        function isRetirementPara() {
            return getProgramType() === 'retirementpara';
        }

        // --- Variable Declarations ---
        let currentViewType = '';
        let currentDepartment = '';
        let selectedStatCard = null;
        let selectedDepartmentCard = null;
        let chart = null;
        let dashboardData = {};
        let filterOptions = {
            departments: [],
            regions: []
        };
        let visibleTables = {
            district: false,
            institution: false,
            audit: false,
            slipviewDetails: false,
            paraview_details: false,
            InstitutionParaTableContainer: false,
            slipdetailsTableContainer: false
        };
        let currentDepartmentId = null;
        let currentViewData = null;
        let isRefreshing = false;
        let isInitialLoad = true;
       let initialFilters = {
            program: 'plantabform',
            year: document.getElementById('year-filter').value,
            quarter: document.getElementById('quarter-filter').value,
            department: document.getElementById('department-filter').value,
        };

        let whichslipValue = 'nosliptab';
        let isDirectRegionTableClick = false;
        let logoutTimer;
        let sessiondeptCode = "{{ $deptcode }}";
        let sessiondistCode = "{{ $distcode }}";

        // function resetLogoutTimer() {
        //     clearTimeout(logoutTimer);
        //     logoutTimer = setTimeout(() => {
        //         fetch("{{ route('logout') }}", {
        //             method: "POST",
        //             headers: {
        //                 "X-CSRF-TOKEN": "{{ csrf_token() }}",
        //                 "Content-Type": "application/json"
        //             },
        //         }).then(() => {
        //             window.location.href = "/login";
        //         });
        //     }, 10 * 60 * 1000);
        // }

        // window.onload = resetLogoutTimer;
        // document.onmousemove = resetLogoutTimer;
        // document.onkeydown = resetLogoutTimer;
        // document.onclick = resetLogoutTimer;
        // document.onscroll = resetLogoutTimer;


        function hideTable(tableId) {
            document.getElementById(tableId).style.display = 'none';

            if (tableId === 'districtTableContainer') {
                visibleTables.district = false;
            } else if (tableId === 'institutionTableContainer') {
                visibleTables.institution = false;
            } else if (tableId === 'auditReportTableContainer') {
                visibleTables.audit = false;
            } else if (tableId === 'slipview_Details') {
                visibleTables.slipviewDetails = false;
            } else if (tableId === 'paraview_details') {
                visibleTables.paraview_details = false;
            } else if (tableId === 'InstitutionParaTableContainer') {
                visibleTables.InstitutionParaTableContainer = false;
            } else if (tableId === 'slipdetailsTableContainer') {
                visibleTables.slipdetailsTableContainer = false;
            }
        }

        function showLoading() {
            document.querySelector('.loading-overlay').style.display = 'flex';
        }

        function hideLoading() {
            document.querySelector('.loading-overlay').style.display = 'none';
        }

       // function showLoadingFilter() {
           // document.querySelector('.loading-filter-overlay').style.display = 'flex';
            // resetView();
        //}

        //function hideLoadingFilter() {
            //document.querySelector('.loading-filter-overlay').style.display = 'none';
       //}

        function showLoadingChart() {
            document.querySelector('.skeleton-loader').style.display = 'block';
            document.querySelector('.content-wrapper').style.display = 'none';
            document.querySelector('.loading-overlay').style.display = 'flex';
        }

        function hideLoadingChart() {
            document.querySelector('.skeleton-loader').style.display = 'none';
            document.querySelector('.content-wrapper').style.display = 'block';
            document.querySelector('.loading-overlay').style.display = 'none';
        }

        function generateDynamicColors(count, opacity = 1, saturation = 80, lightness = 70) {
            const colors = [];
            const goldenRatio = 0.618033988749895;
            let hue = Math.random() * 360;
            for (let i = 0; i < count; i++) {
                hue = (hue + goldenRatio * 360) % 360;
                const currentSaturation = saturation + (Math.random() * 10 - 5);
                const currentLightness = lightness + (Math.random() * 10 - 5);
                colors.push(
                    `hsla(${hue}, ${Math.min(100, currentSaturation)}%, ${Math.min(90, currentLightness)}%, ${opacity})`
                );
            }
            return colors;
        }

        function getRichColorsFromPalette(count) {
            const richPalette = [
                '#3366CC', '#DC3912', '#FF9900', '#109618',
                '#990099', '#3B3EAC', '#0099C6', '#DD4477',
                '#66AA00', '#B82E2E', '#316395', '#994499',
                '#22AA99', '#AAAA11', '#6633CC', '#E67300',
                '#8B0707', '#329262', '#5574A6', '#651067'
            ];
            if (count > richPalette.length) {
                const additionalColors = generateDynamicColors(
                    count - richPalette.length, 1, 85, 65
                );
                return [...richPalette, ...additionalColors].slice(0, count);
            }
            return richPalette.slice(0, count);
        }

        function createExcelExportButton(titleexcel) {
            const currentDate = new Date().toLocaleDateString('en-IN');
            const fileNameWithDate = `${titleexcel} - ${currentDate}`;

            return {
                extend: 'excelHtml5',
                text: `<i class="fas fa-download"></i> <span class="download-text"> Download</span>`,
                className: 'btn btn-primary',
                filename: fileNameWithDate,
                title: fileNameWithDate,
                exportOptions: {
                    columns: ':not(.noExport)'
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var titleRow = `
                            <row r="1">
                                <c t="inlineStr" r="A1">
                                    <is><t>${fileNameWithDate}</t></is>
                                </c>
                            </row>
                        `;
                    var rows = sheet.getElementsByTagName('row');
                    for (var i = 0; i < rows.length; i++) {
                        var rIndex = parseInt(rows[i].getAttribute('r'));
                        rows[i].setAttribute('r', rIndex + 1);
                        var cells = rows[i].getElementsByTagName('c');
                        for (var j = 0; j < cells.length; j++) {
                            var cell = cells[j];
                            var cellRef = cell.getAttribute('r');
                            var col = cellRef.replace(/[0-9]/g, '');
                            var row = parseInt(cellRef.replace(/[A-Z]/g, '')) + 1;
                            cell.setAttribute('r', col + row);
                        }
                    }
                    var sheetData = sheet.getElementsByTagName('sheetData')[0];
                    sheetData.innerHTML = titleRow + sheetData.innerHTML;
                    var cols = sheet.getElementsByTagName('col');
                    for (var k = 0; k < cols.length; k++) {
                        cols[k].setAttribute('width', 30);
                    }
                }
            };
        }

        function getFormattedDate() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            return `${day}-${month}-${year}`;
        }

        // --- Filter and Reset Functions ---
        function getCurrentFilters() {
            return {
                program: document.getElementById('audit-program-filter').value,
                year: document.getElementById('year-filter').value,
                quarter: document.getElementById('quarter-filter').value,
                department: document.getElementById('department-filter').value,

            };
        }

        function applyFilters() {
            if (isRefreshing) return;

            const applyBtn = document.getElementById('apply-filters-btn');
            isRefreshing = true;
            applyBtn.disabled = true;
            applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
            applyBtn.classList.add('loading');

            try {
                // Store current filters for comparison
                const previousFilters = {
                    ...initialFilters
                };
                initialFilters = getCurrentFilters();

                // Clear quarter value if department is 'all' to avoid sending it
               //  if (initialFilters.department === 'all') {
                 //   document.getElementById('quarter-filter').value = '';
                   //  initialFilters.quarter = '';
                // }

                // Reset view and reload data
                currentDepartment = '';
                currentViewType = '';
                document.getElementById('auditReportTableContainer').style.display = 'none';
                document.getElementById('institutionTableContainer').style.display = 'none';
                document.getElementById('districtTableContainer').style.display = 'none';
                document.getElementById('slipview_Details').style.display = 'none';
                document.getElementById('paraview_details').style.display = 'none';
                document.getElementById('slipdetailsTableContainer').style.display = 'none';

                resetView();

                loadDashboardData(true).finally(() => {
                    applyBtn.disabled = false;
                    applyBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filters';
                    applyBtn.classList.remove('loading');
                    isRefreshing = false;

                    // Disable apply button after applying since we're now in sync
                    applyBtn.disabled = true;
                });

                //hideLoadingFilter();
            } catch (error) {
                applyBtn.disabled = false;
                applyBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filters';
                applyBtn.classList.remove('loading');
                isRefreshing = false;
                console.error("Apply filters failed:", error);
            }
        }
        function checkFiltersChanged() {
            const currentFilters = getCurrentFilters();
            const applyBtn = document.getElementById('apply-filters-btn');

            const hasChanged = Object.keys(initialFilters).some(
                key => currentFilters[key] !== initialFilters[key]
            );

            if (applyBtn) {
                applyBtn.disabled = !hasChanged;
            }

            if (!hasChanged) {
                //hideLoadingFilter();
            }
        }

        function resetFilters() {
            if (isRefreshing) return;
            const refreshBtn = document.getElementById('refresh-btn');
            isRefreshing = true;
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';

            try {
                // Reset to initial filter values
                document.getElementById('audit-program-filter').value = initialFilters.program;
                document.getElementById('year-filter').value = initialFilters.year;
                document.getElementById('quarter-filter').value = initialFilters.quarter;
                document.getElementById('department-filter').value = initialFilters.department;

                // Disable apply button since we're back to initial state
                document.getElementById('apply-filters-btn').disabled = true;

                currentDepartment = '';
                currentViewType = '';
                document.getElementById('auditReportTableContainer').style.display = 'none';
                document.getElementById('institutionTableContainer').style.display = 'none';
                document.getElementById('districtTableContainer').style.display = 'none';
                document.getElementById('slipview_Details').style.display = 'none';
                document.getElementById('paraview_details').style.display = 'none';
                document.getElementById('slipdetailsTableContainer').style.display = 'none';

                resetView();
                loadDashboardData(true).finally(() => {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    isRefreshing = false;

                    // Disable apply button after applying since we're now in sync
                    applyBtn.disabled = true;
                    // document.getElementById('refresh-btn').disabled = true;
                });

            } catch (error) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                isRefreshing = false;
                console.error("Refresh failed:", error);
            }
        }

        function resetView() {
            document.getElementById('department-card').style.display = 'none';
            document.getElementById('detail-card').style.display = 'none';
            document.getElementById('slipview_Details').style.display = 'none';
            document.getElementById('paraview_details').style.display = 'none';
            $('#auditReportTableContainer').hide();
            hideAllDrillDownTables();
            if (selectedStatCard) {
                selectedStatCard.classList.remove('selected');
                selectedStatCard = null;
            }
            if (selectedDepartmentCard) {
                selectedDepartmentCard.classList.remove('selected');
                selectedDepartmentCard = null;
            }
            if (chart) {
                chart.destroy();
                chart = null;
            }
            currentViewType = '';
            currentDepartment = '';
        }

        function resetDashboard() {
            document.getElementById('department-card').style.display = 'none';
            document.getElementById('detail-card').style.display = 'none';
            document.getElementById('slipview_Details').style.display = 'none';
            document.getElementById('paraview_details').style.display = 'none';
            if (selectedStatCard) {
                selectedStatCard.classList.remove('selected');
                selectedStatCard = null;
            }
            if (selectedDepartmentCard) {
                selectedDepartmentCard.classList.remove('selected');
                selectedDepartmentCard = null;
            }
            if (chart) {
                chart.destroy();
                chart = null;
            }
            $('#auditReportTableContainer').hide();
            $('#institutionTableContainer').hide();
            $('#districtTableContainer').hide();
            $('#slipdetailsTableContainer').hide();
        }

        // --- Filter Option Loader ---
        async function loadFilterOptions() {
            showLoading();
            try {
                filterOptions = {
                    departments: []
                };
                // const yearFilter = document.getElementById('year-filter');
                // yearFilter.innerHTML = '';
                // filterOptions.years.forEach(year => {
                //     const option = document.createElement('option');
                //     option.value = year;
                //     option.textContent = year;
                //     if (year === '2025-2026') option.selected = true;
                //     yearFilter.appendChild(option);
                // });
                const deptFilter = document.getElementById('department-filter');
                const deptOptions = deptFilter.querySelectorAll('option:not([value="all"])');
                deptOptions.forEach(option => {
                    filterOptions.departments.push({
                        code: option.value,
                        shortName: option.dataset.shortName,
                        fullName: option.dataset.fullName,
                        numericDeptCode: option.value
                    });
                });
            } catch (error) {
                console.error('Error loading filter options:', error);
                alert('Failed to load filter options. Please try again.');
            } finally {
                hideLoading();
            }
        }

        // --- Dashboard Data Functions ---
        async function getDashboardData(auditProgram, year, quarter, department, region) {
            try {
                const params = {
                    source_form: auditProgram,
                    financialyear: year,
                    department: department,
                    region: region
                };

                // Only include quarter if a specific department is selected (not 'all')
                // AND if the audit program type requires quarter
                const shouldIncludeQuarter = (auditProgram === 'plantabform' || auditProgram === 'auditreport' ||
                    auditProgram === 'templateaudit' || auditProgram === 'inspectionaudit' ||
                    auditProgram === 'legacyreport' || auditProgram === 'parareport' ||
                    auditProgram === 'paracount' || auditProgram === 'retirementpara');

                if (shouldIncludeQuarter) {
                    params.quarter = quarter;
                } else if (auditProgram === 'sliptabform') {
                    params.quarterslip = quarter;
                }

                // Remove quarter parameter if department is 'all'
                // if (department === 'all') {
                //     delete params.quarter;
                //     delete params.quarterslip;
                // }

                const queryString = new URLSearchParams(params).toString();
                const response = await fetch(`/ajax/deptwise-data?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                return transformDataForDashboard(result.data, department, auditProgram);
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                return generateMockData(auditProgram, year, quarter, department, region);
            }
        }

        function transformDataForDashboard(apiData, selectedDepartment, sourceForm) {
            const isSlipDetails = sourceForm === 'sliptabform';
            const isAuditReport = sourceForm === 'auditreport';
            const isTemplateAudit = sourceForm === 'templateaudit';
            const isLegacyReportValue = sourceForm === 'legacyreport';
            const isInspectionAudit = sourceForm === 'inspectionaudit';
            const isParaReport = sourceForm === 'parareport';
            const isParaCount = sourceForm === 'paracount';
            const isRetirementPara = sourceForm === 'retirementpara';

            const generateColors = (count) => {
                const colors = [];
                const hueStep = 360 / count;
                for (let i = 0; i < count; i++) {
                    colors.push(`hsl(${Math.floor(i * hueStep)}, 70%, 60%)`);
                }
                return colors;
            };

            const getDepartmentIcon = (deptName) => {
                const iconMap = {
                    'HRIA': '🛕',
                    'Hindu Religious': '🛕',
                    'LFA': '🏦',
                    'Local Fund': '🏦',
                    'SGA': '🏛️',
                    'State Government': '🏛️',
                    'DCA': '👥',
                    'Co-operative': '👥',
                    'Milk': '🥛',
                    'TOTAL': '📊',
                    'default': '🏢'
                };

                if (iconMap[deptName]) return iconMap[deptName];

                const foundKey = Object.keys(iconMap).find(key =>
                    deptName.includes(key) && key !== 'default'
                );

                return foundKey ? iconMap[foundKey] : iconMap.default;
            };

            let data = {};

            if (isAuditReport) {
                data = {
                    audit_completed: createDataStructure("Audit Completed"),
                    report_finalized: createDataStructure("Report Finalized"),
                    report_issued: createDataStructure("Report Issued"),
                    pending_finalize: createDataStructure("Pending to Finalize"),
                    pending_issue: createDataStructure("Pending to Issue"),
                    summary: {
                        audit_completed: 0,
                        report_finalized: 0,
                        report_issued: 0,
                        pending_finalize: 0,
                        pending_issue: 0
                    }
                };
            } else if (isSlipDetails) {
                data = {
                    total: createDataStructure("Total Slips"),
                    pending: createDataStructure("Pending Slips"),
                    converted: createDataStructure("Converted to Paras"),
                    dropped: createDataStructure("Dropped Slips"),
                    summary: {
                        total: 0,
                        pending: 0,
                        converted: 0,
                        dropped: 0
                    }
                };
            } else if (isTemplateAudit) {
                data = {
                    total: createDataStructure("Total Template Audits"),
                    pending: createDataStructure("Template Audit Pending"),
                    ongoing: createDataStructure("Template Audit Ongoing"),
                    completed: createDataStructure("Template Audit Completed"),
                    summary: {
                        total: 0,
                        pending: 0,
                        ongoing: 0,
                        completed: 0
                    }
                };
            } else if (isInspectionAudit) {
                data = {
                    total: createDataStructure("Total Instituions"),
                    pending: createDataStructure("Inspection Pending"),
                    ongoing: createDataStructure("Inspection Ongoing"),
                    completed: createDataStructure("Inspection Completed"),
                    single_completed: createDataStructure("Inspection Completed"),
                    multi_completed: createDataStructure("Inspection Completed"),
                    summary: {
                        total: 0,
                        pending: 0,
                        ongoing: 0,
                        completed: 0,
                        single_completed: 0,
                        multi_completed: 0,
                    }
                };
            } else if (isLegacyReportValue) {
                data = {
                    total: createDataStructure("Total Data Entered"),
                    finalize: createDataStructure("Finalized"),
                    pending: createDataStructure("Pending to Finalize"),
                    summary: {
                        total: 0,
                        finalize: 0,
                        pending: 0,
                    }
                };
            } else if (isParaReport) {
                data = {
                    totalparas: createParaDataStructure("Total Para(s)"),
                    pendingparas: createParaDataStructure("Pending Para(s)"),
                    processedparas: createParaDataStructure("Under Processing Para(s)"),
                    // droppedparas: createDataStructure("Dropped Paras"),
                    // rejectedparas: createDataStructure("Rejected Paras"),
                    // clarifyparas: createDataStructure("Need for Clarification Paras"),
                    summary: {
                        totalparas: 0,
                        pendingparas: 0,
                        processedparas: 0,
                        // rejectedparas: 0,
                        // clarifyparas: 0,
                    }
                };
            } else if (isParaCount) {
                data = {
                    totalparas: createParaCountDataStructure("Total Para(s)"),
                    pendingparas: createParaCountDataStructure("Pending Para(s)"),
                    processedparas: createParaCountDataStructure("Processed Para(s)"),
                    summary: {
                        totalparas: 0,
                        pendingparas: 0,
                        processedparas: 0,
                    }
                };
            } else if (isRetirementPara) {
                data = {
                    total_retirement_paracount: createParaCountDataStructure("Total Retirement Para(s)"),
                    pending_retirement_paracount: createParaCountDataStructure("Pending Retirement Para(s)"),
                    processed_retirement_paracount: createParaCountDataStructure("Processed Retirement Para(s)"),
                    summary: {
                        total_retirement_paracount: 0,
                        pending_retirement_paracount: 0,
                        processed_retirement_paracount: 0,
                    }
                };
            } else {
                data = {
                    regions: createDataStructure("Audit Region"),
                    districts: createDataStructure("Audit District"),
                    institutions: createDataStructure("Audit Institution"),
                    summary: {
                        regions: 0,
                        districts: 0,
                        institutions: 0
                    }
                };
            }

            function createDataStructure(label) {
                return {
                    title: `${label}-wise Distribution`,
                    departments: [],
                    tableHeaders: ["S.No", "Department", label],
                    tableData: [],
                    chart: {
                        labels: [],
                        datasets: [{
                            label: label,
                            data: [],
                            backgroundColor: []
                        }]
                    }
                };
            }

            function createParaDataStructure(label) {
                return {
                    title: `${label} Distribution`,
                    departments: [],
                    tableHeaders: ["S.No", "Department", label],
                    tableData: [],
                    chart: {
                        labels: [],
                        datasets: [{
                            label: label,
                            data: [],
                            backgroundColor: []
                        }]
                    }
                };
            }

            function createParaCountDataStructure(label) {
                return {
                    title: `${label} Distribution`,
                    departments: [],
                    tableHeaders: ["S.No", "Department", label],
                    tableData: [],
                    chart: {
                        labels: [],
                        datasets: [{
                            label: label,
                            data: [],
                            backgroundColor: []
                        }]
                    }
                };
            }

            const deptCodeMapping = {};
            const uniqueDepartments = new Set();

            filterOptions.departments.forEach(dept => {
                deptCodeMapping[dept.code] = {
                    shortName: dept.shortName,
                    fullName: dept.fullName
                };
                uniqueDepartments.add(dept.code);
            });

            const departmentColors = generateColors(uniqueDepartments.size);
            const colorIndexMap = {};
            Array.from(uniqueDepartments).forEach((deptCode, index) => {
                colorIndexMap[deptCode] = index;
            });

            if (isAuditReport) {
                processAuditReportData(apiData);
            } else if (isSlipDetails) {
                processSlipData(apiData);
            } else if (isTemplateAudit) {
                processTemplateAuditData(apiData);
            } else if (isInspectionAudit) {
                processInspectionAuditData(apiData);
            } else if ( isLegacyReportValue) {
                processLegacyReportData(apiData);
            } else if (isParaReport) {
                processParaReportData(apiData);
            } else if (isParaCount) {
                processParaCountData(apiData);
            } else if (isRetirementPara) {
                processRetirementParaData(apiData);
            } else {
                processAuditData(apiData);
            }

            return data;

            function processAuditReportData(items) {
                const departmentItems = items.filter(item => item.deptname !== 'TOTAL');

                departmentItems.forEach(item => {
                    const numericDeptCode = item.deptCode;
                    if (shouldSkipDepartment(numericDeptCode, deptCodeMapping[numericDeptCode]?.shortName)) return;

                    data.summary.audit_completed += parseInt(item.audit_completed) || 0;
                    data.summary.report_finalized += parseInt(item.report_finalized) || 0;
                    data.summary.report_issued += parseInt(item.report_issued) || 0;
                    data.summary.pending_finalize += parseInt(item.pending_finalize) || 0;
                    data.summary.pending_issue += parseInt(item.pending_issue) || 0;
                });

                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        audit_completed: parseInt(item.audit_completed) || 0,
                        report_finalized: parseInt(item.report_finalized) || 0,
                        report_issued: parseInt(item.report_issued) || 0,
                        pending_finalize: parseInt(item.pending_finalize) || 0,
                        pending_issue: parseInt(item.pending_issue) || 0
                    };

                    const isTotalRow = item.deptname === 'TOTAL';

                    ['audit_completed', 'report_finalized', 'report_issued', 'pending_finalize', 'pending_issue']
                    .forEach(metric => {
                        addDepartmentData(
                            metric,
                            deptInfo,
                            counts[metric],
                            numericDeptCode,
                            item.deptname,
                            index,
                            isTotalRow
                        );
                    });
                });
            }

            function processTemplateAuditData(items) {

                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        total: parseInt(item.total_count) || 0,
                        pending: parseInt(item.pending_count) || 0,
                        ongoing: parseInt(item.ongoing_count) || 0,
                        completed: parseInt(item.completed_count) || 0
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    ['total', 'pending', 'ongoing', 'completed'].forEach(metric => {
                        addDepartmentData(
                            metric,
                            deptInfo,
                            counts[metric],
                            numericDeptCode,
                            item.deptname,
                            index
                        );
                    });
                });
            }

            function processSlipData(items) {
                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        total: parseInt(item.totalslips) || 0,
                        pending: parseInt(item.pendingslipcount) || 0,
                        converted: parseInt(item.convertedslipcount) || 0,
                        dropped: parseInt(item.droppedslipcount) || 0
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    ['total', 'pending', 'converted', 'dropped'].forEach(metric => {
                        addDepartmentData(
                            metric,
                            deptInfo,
                            counts[metric],
                            numericDeptCode,
                            item.deptname,
                            index
                        );
                    });
                });
            }

            function processInspectionAuditData(items) {
                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        total: parseInt(item.total_inspection_count) || 0,
                        pending: parseInt(item.pending_inspection_count) || 0,
                        ongoing: parseInt(item.ongoing_inspection_count) || 0,
                        completed: parseInt(item.completed_inspection_count) || 0,
                        single_completed: parseInt(item.single_completed_inspection_count) || 0,
                        multi_completed: parseInt(item.multi_completed_inspection_count) || 0
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    ['total', 'pending', 'ongoing', 'completed', 'single_completed', 'multi_completed'].forEach(
                        metric => {
                            addDepartmentData(
                                metric,
                                deptInfo,
                                counts[metric],
                                numericDeptCode,
                                item.deptname,
                                index
                            );
                        });
                });
            }

            function processLegacyReportData(items) {
                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        total: parseInt(item.total_legacy_count) || 0,
                        finalize: parseInt(item.finalize_legacy_count) || 0,
                        pending: parseInt(item.pending_legacy_count) || 0,
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    ['total', 'finalize', 'pending'].forEach(
                        metric => {
                            addDepartmentData(
                                metric,
                                deptInfo,
                                counts[metric],
                                numericDeptCode,
                                item.deptname,
                                index
                            );
                        });
                });
            }

            function processParaReportData(items) {
                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        totalparas: parseInt(item.totalparacount) || 0,
                        pendingparas: parseInt(item.pendingparacount) || 0,
                        processedparas: parseInt(item.processedparacount) || 0,
                        // droppedparas: parseInt(item.pending_legacy_count) || 0,
                        // rejectedparas: parseInt(item.pending_legacy_count) || 0,
                        // clarifyparas: parseInt(item.pending_legacy_count) || 0,
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    // ['totalparas', 'pendingparas', 'droppedparas', 'rejectedparas', 'clarifyparas'].forEach(
                    ['totalparas', 'processedparas', 'pendingparas'].forEach(
                        metric => {
                            addDepartmentData(
                                metric,
                                deptInfo,
                                counts[metric],
                                numericDeptCode,
                                item.deptname,
                                index
                            );
                        });
                });
            }

            function processParaCountData(items) {
                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        totalparas: parseInt(item.totalparacount) || 0,
                        pendingparas: parseInt(item.pendingparacount) || 0,
                        processedparas: parseInt(item.processedparacount) || 0,
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    ['totalparas', 'processedparas', 'pendingparas'].forEach(
                        metric => {
                            addDepartmentData(
                                metric,
                                deptInfo,
                                counts[metric],
                                numericDeptCode,
                                item.deptname,
                                index
                            );
                        });
                });
            }

            function processRetirementParaData(items) {
                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        total_retirement_paracount: parseInt(item.total_retirement_paracount) || 0,
                        pending_retirement_paracount: parseInt(item.pending_retirement_paracount) || 0,
                        processed_retirement_paracount: parseInt(item.processed_retirement_paracount) || 0,
                    };

                    Object.keys(counts).forEach(key => data.summary[key] += counts[key]);

                    ['total_retirement_paracount', 'pending_retirement_paracount','processed_retirement_paracount'].forEach(
                        metric => {
                            addDepartmentData(
                                metric,
                                deptInfo,
                                counts[metric],
                                numericDeptCode,
                                item.deptname,
                                index
                            );
                        });
                });
            }

            function processAuditData(items) {
                items.forEach(item => {
                    const numericDeptCode = item.deptCode;
                    if (shouldSkipDepartment(numericDeptCode, deptCodeMapping[numericDeptCode]?.shortName)) return;

                    data.summary.regions += parseInt(item.regioncount) || 0;
                    data.summary.districts += parseInt(item.distcount) || 0;
                    data.summary.institutions += parseInt(item.alloc_inscount) || 0;
                });

                items.forEach((item, index) => {
                    const numericDeptCode = item.deptCode;
                    const deptInfo = deptCodeMapping[numericDeptCode] || {
                        shortName: numericDeptCode,
                        fullName: item.deptname || numericDeptCode
                    };

                    if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                    const counts = {
                        regions: parseInt(item.regioncount) || 0,
                        districts: parseInt(item.distcount) || 0,
                        institutions: parseInt(item.alloc_inscount) || 0
                    };

                    ['regions', 'districts', 'institutions'].forEach(metric => {
                        if (counts[metric] > 0) {
                            addDepartmentData(
                                metric,
                                deptInfo,
                                counts[metric],
                                numericDeptCode,
                                item.deptname,
                                index,
                                false
                            );
                        }
                    });
                });
            }

            function shouldSkipDepartment(numericCode, shortName) {
                return selectedDepartment &&
                    selectedDepartment !== 'all' &&
                    selectedDepartment !== numericCode &&
                    selectedDepartment !== shortName;
            }

            function addDepartmentData(metric, deptInfo, value, numericCode, deptName, index, isTotalRow = false) {
                const colorIndex = isTotalRow ? -1 : (colorIndexMap[numericCode] || 0);
                const color = isTotalRow ? '#666666' : departmentColors[colorIndex % departmentColors.length];
                const icon = getDepartmentIcon(deptInfo.fullName || deptName);

                data[metric].departments.push({
                    name: deptInfo.shortName,
                    displayName: isTotalRow ? 'TOTAL' : deptInfo.shortName,
                    fullName: deptInfo.fullName,
                    value: value,
                    icon: icon,
                    color: isTotalRow ? 'secondary' : getColorType(colorIndex),
                    label: deptName,
                    numericDeptCode: numericCode,
                    isTotal: isTotalRow
                });

                data[metric].tableData.push([
                    index + 1,
                    isTotalRow ? 'TOTAL' : deptInfo.shortName,
                    value.toLocaleString('en-IN')
                ]);

                data[metric].chart.labels.push(isTotalRow ? 'TOTAL' : deptInfo.shortName);
                data[metric].chart.datasets[0].data.push(value);
                data[metric].chart.datasets[0].backgroundColor.push(color);
            }

            function getColorType(index) {
                const types = ['primary', 'success', 'info', 'warning', 'danger'];
                return types[index % types.length];
            }
        }

        function generateMockData(auditProgram, year, quarter, department, region) {
            const isSlipDetails = auditProgram === 'sliptabform';
            const isAuditReport = auditProgram === 'auditreport';
            const isTemplateAudit = auditProgram === 'templateaudit';
            const isLegacyReport = auditProgram === 'legacyreport';
            const isInspectionAudit = auditProgram === 'inspectionaudit';
            const isParaReport = auditProgram === 'parareport';
            const isParaCount = auditProgram === 'paracount';
            const isRetirementPara = auditProgram === 'retirementpara';


            if (isAuditReport) {
                return {
                    audit_completed: {
                        title: "Audit Completed by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 123,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 894,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 944,
                                color: "warning"
                            },
                            {
                                name: "Milk",
                                value: 54,
                                color: "danger"
                            },
                            {
                                name: "TOTAL",
                                value: 2030,
                                color: "secondary",
                                isTotal: true
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Audit Completed"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 123],
                            [3, "LFA", 894],
                            [4, "DCA", 944],
                            [5, "Milk", 54],
                            [6, "TOTAL", 2030]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA", "Milk", "TOTAL"],
                            datasets: [{
                                label: 'Audit Completed',
                                data: [15, 123, 894, 944, 54, 2030],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703', '#dc3545', '#666666']
                            }]
                        }
                    },
                    report_finalized: {
                        title: "Report Finalized by Department",
                        departments: [{
                                name: "HRIA",
                                value: 0,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 0,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 2,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 1,
                                color: "warning"
                            },
                            {
                                name: "Milk",
                                value: 0,
                                color: "danger"
                            },
                            {
                                name: "TOTAL",
                                value: 3,
                                color: "secondary",
                                isTotal: true
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Report Finalized"],
                        tableData: [
                            [1, "HRIA", 0],
                            [2, "SGA", 0],
                            [3, "LFA", 2],
                            [4, "DCA", 1],
                            [5, "Milk", 0],
                            [6, "TOTAL", 3]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA", "Milk", "TOTAL"],
                            datasets: [{
                                label: 'Report Finalized',
                                data: [0, 0, 2, 1, 0, 3],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703', '#dc3545', '#666666']
                            }]
                        }
                    },
                    report_issued: {
                        title: "Report Issued by Department",
                        departments: [{
                                name: "HRIA",
                                value: 0,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 0,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 0,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 0,
                                color: "warning"
                            },
                            {
                                name: "Milk",
                                value: 0,
                                color: "danger"
                            },
                            {
                                name: "TOTAL",
                                value: 0,
                                color: "secondary",
                                isTotal: true
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Report Issued"],
                        tableData: [
                            [1, "HRIA", 0],
                            [2, "SGA", 0],
                            [3, "LFA", 0],
                            [4, "DCA", 0],
                            [5, "Milk", 0],
                            [6, "TOTAL", 0]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA", "Milk", "TOTAL"],
                            datasets: [{
                                label: 'Report Issued',
                                data: [0, 0, 0, 0, 0, 0],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703', '#dc3545', '#666666']
                            }]
                        }
                    },
                    pending_finalize: {
                        title: "Pending to Finalize by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 123,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 892,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 943,
                                color: "warning"
                            },
                            {
                                name: "Milk",
                                value: 54,
                                color: "danger"
                            },
                            {
                                name: "TOTAL",
                                value: 2027,
                                color: "secondary",
                                isTotal: true
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Pending to Finalize"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 123],
                            [3, "LFA", 892],
                            [4, "DCA", 943],
                            [5, "Milk", 54],
                            [6, "TOTAL", 2027]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA", "Milk", "TOTAL"],
                            datasets: [{
                                label: 'Pending to Finalize',
                                data: [15, 123, 892, 943, 54, 2027],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703', '#dc3545', '#666666']
                            }]
                        }
                    },
                    pending_issue: {
                        title: "Pending to Issue by Department",
                        departments: [{
                                name: "HRIA",
                                value: 0,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 0,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 2,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 1,
                                color: "warning"
                            },
                            {
                                name: "Milk",
                                value: 0,
                                color: "danger"
                            },
                            {
                                name: "TOTAL",
                                value: 3,
                                color: "secondary",
                                isTotal: true
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Pending to Issue"],
                        tableData: [
                            [1, "HRIA", 0],
                            [2, "SGA", 0],
                            [3, "LFA", 2],
                            [4, "DCA", 1],
                            [5, "Milk", 0],
                            [6, "TOTAL", 3]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA", "Milk", "TOTAL"],
                            datasets: [{
                                label: 'Pending to Issue',
                                data: [0, 0, 2, 1, 0, 3],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703', '#dc3545', '#666666']
                            }]
                        }
                    },
                    summary: {
                        audit_completed: 2030,
                        report_finalized: 3,
                        report_issued: 0,
                        pending_finalize: 2027,
                        pending_issue: 3
                    }
                };
            } else if (isSlipDetails) {
                return {
                    total: {
                        title: "Total Slips by Department",
                        departments: [{
                                name: "HRIA",
                                value: 45,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 32,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 28,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 15,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Total Slips"],
                        tableData: [
                            [1, "HRIA", 45],
                            [2, "SGA", 32],
                            [3, "LFA", 28],
                            [4, "DCA", 15]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Total Slips',
                                data: [45, 32, 28, 15],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    pending: {
                        title: "Pending Slips by Department",
                        departments: [{
                                name: "HRIA",
                                value: 12,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 8,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 5,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 3,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Pending Slips"],
                        tableData: [
                            [1, "HRIA", 12],
                            [2, "SGA", 8],
                            [3, "LFA", 5],
                            [4, "DCA", 3]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Pending Slips',
                                data: [12, 8, 5, 3],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    converted: {
                        title: "Converted to Paras by Department",
                        departments: [{
                                name: "HRIA",
                                value: 25,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 18,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 15,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 8,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Converted to Paras"],
                        tableData: [
                            [1, "HRIA", 25],
                            [2, "SGA", 18],
                            [3, "LFA", 15],
                            [4, "DCA", 8]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Converted to Paras',
                                data: [25, 18, 15, 8],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    dropped: {
                        title: "Dropped Slips by Department",
                        departments: [{
                                name: "HRIA",
                                value: 8,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 6,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 8,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 4,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Dropped Slips"],
                        tableData: [
                            [1, "HRIA", 8],
                            [2, "SGA", 6],
                            [3, "LFA", 8],
                            [4, "DCA", 4]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Dropped Slips',
                                data: [8, 6, 8, 4],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        total: 120,
                        pending: 28,
                        converted: 66,
                        dropped: 26
                    }
                };

            } else if (isTemplateAudit) {
                return {
                    template_audit: {
                        title: "Template Audits by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 22,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 18,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 12,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Template Audits"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 22],
                            [3, "LFA", 18],
                            [4, "DCA", 12]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Template Audits',
                                data: [15, 22, 18, 12],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        template_audit_count: 67
                    }
                };
            } else if (isLegacyReport) {
                return {
                    legacy_report: {
                        title: "Legacy Reports by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 22,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 18,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 12,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Legacy Reports"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 22],
                            [3, "LFA", 18],
                            [4, "DCA", 12]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Legacy Reports',
                                data: [15, 22, 18, 12],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        legacy_report_count: 67
                    }
                };
            } else if (isInspectionAudit) {
                return {
                    inspection_audit: {
                        title: "Inspection Audits by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 22,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 18,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 12,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Inspection Audits"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 22],
                            [3, "LFA", 18],
                            [4, "DCA", 12]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Inspection Audits',
                                data: [15, 22, 18, 12],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        inspection_audit_count: 67
                    }
                };
             } else if (isParaReport) {
                return {
                    para_report: {
                        title: "Audit Para Details by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 22,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 18,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 12,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Audit Para Details"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 22],
                            [3, "LFA", 18],
                            [4, "DCA", 12]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Audit Para Details',
                                data: [15, 22, 18, 12],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        para_report_count: 67
                    }
                };
             } else if (isParaCount) {
                return {
                    para_count: {
                        title: "Audit Para Count by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 22,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 18,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 12,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Audit Para Count"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 22],
                            [3, "LFA", 18],
                            [4, "DCA", 12]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Audit Para Count',
                                data: [15, 22, 18, 12],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        para_count: 67
                    }
                };

                } else if (isRetirementPara) {
                    return {
                        retirement_para: {
                            title: "Retirement Para Details by Department",
                            departments: [{
                                    name: "HRIA",
                                    value: 15,
                                    color: "primary"
                                },
                                {
                                    name: "SGA",
                                    value: 22,
                                    color: "success"
                                },
                                {
                                    name: "LFA",
                                    value: 18,
                                    color: "info"
                                },
                                {
                                    name: "DCA",
                                    value: 12,
                                    color: "warning"
                                }
                            ],
                            tableHeaders: ["S.No", "Department", "Retirement Para Details"],
                            tableData: [
                                [1, "HRIA", 15],
                                [2, "SGA", 22],
                                [3, "LFA", 18],
                                [4, "DCA", 12]
                            ],
                            chart: {
                                labels: ["HRIA", "SGA", "LFA", "DCA"],
                                datasets: [{
                                    label: 'Retirement Para Details',
                                    data: [15, 22, 18, 12],
                                    backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                                }]
                            }
                        },
                        summary: {
                            retirement_para_count: 67
                        }
                    };
                } else {
                    return {
                        regions: {
                        title: "Auditable Regions by Department",
                        departments: [{
                                name: "HRIA",
                                value: 15,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 22,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 18,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 12,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Audit Regions"],
                        tableData: [
                            [1, "HRIA", 15],
                            [2, "SGA", 22],
                            [3, "LFA", 18],
                            [4, "DCA", 12]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Audit Regions',
                                data: [15, 22, 18, 12],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    districts: {
                        title: "Auditable Districts by Department",
                        departments: [{
                                name: "HRIA",
                                value: 45,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 62,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 38,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 22,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Audit Districts"],
                        tableData: [
                            [1, "HRIA", 45],
                            [2, "SGA", 62],
                            [3, "LFA", 38],
                            [4, "DCA", 22]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Audit Districts',
                                data: [45, 62, 38, 22],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    institutions: {
                        title: "Auditable Institutions by Department",
                        departments: [{
                                name: "HRIA",
                                value: 1450,
                                color: "primary"
                            },
                            {
                                name: "SGA",
                                value: 2260,
                                color: "success"
                            },
                            {
                                name: "LFA",
                                value: 1380,
                                color: "info"
                            },
                            {
                                name: "DCA",
                                value: 1220,
                                color: "warning"
                            }
                        ],
                        tableHeaders: ["S.No", "Department", "Institutions"],
                        tableData: [
                            [1, "HRIA", "1,450"],
                            [2, "SGA", "2,260"],
                            [3, "LFA", "1,380"],
                            [4, "DCA", "1,220"]
                        ],
                        chart: {
                            labels: ["HRIA", "SGA", "LFA", "DCA"],
                            datasets: [{
                                label: 'Institutions',
                                data: [1450, 2260, 1380, 1220],
                                backgroundColor: ['#6f42c1', '#38b000', '#00b4d8', '#ffb703']
                            }]
                        }
                    },
                    summary: {
                        regions: 67,
                        districts: 167,
                        institutions: 6310
                    }
                };
            }
        }

        async function loadDashboardData(forceRefresh = false) {
            showLoading();
            try {
                const auditProgram = document.getElementById('audit-program-filter').value;
                const year = document.getElementById('year-filter').value;
                const quarter = document.getElementById('quarter-filter').value;
                const department = currentDepartment || document.getElementById('department-filter').value;
                const region = '';

                // Only use quarter if a specific department is selected
                // const effectiveQuarter = (department !== 'all') ? quarter : '';
                const effectiveQuarter = quarter ;

                if (forceRefresh || isInitialLoad || !dashboardData.summary) {
                    dashboardData = await getDashboardData(auditProgram, year, effectiveQuarter, department, region);
                    isInitialLoad = false;
                }
                updateSummaryCards();
                if (currentDepartment) {
                    updateDetailView();
                } else if (currentViewType) {
                    showDepartmentView(currentViewType);
                } else {
                    updateDashboard();
                }
            } catch (error) {
                console.error('Dashboard load error:', error);
                alert('Failed to load dashboard data. Please try again.');
            } finally {
                hideLoading();
            }
        }

        function updateDashboard() {
            updateSummaryCards();
            document.getElementById('department-card').style.display = 'none';
            document.getElementById('detail-card').style.display = 'none';
            document.getElementById('slipview_Details').style.display = 'none';
            document.getElementById('paraview_details').style.display = 'none';
            $('#auditReportTableContainer').hide();


            if (selectedStatCard) {
                selectedStatCard.classList.remove('selected');
                selectedStatCard = null;
            }
            if (selectedDepartmentCard) {
                selectedDepartmentCard.classList.remove('selected');
                selectedDepartmentCard = null;
            }

            if (chart) {
                chart.destroy();
                chart = null;
            }
        }

        function updateSummaryCards() {
            if (!dashboardData || !dashboardData.summary) {
                console.error('Dashboard data or summary is undefined');
                const container = document.getElementById('summary-cards-container');
                if (container) {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger text-left">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error loading dashboard data. Please refresh the page or try again.
                            </div>
                        </div>
                    `;
                }
                return;
            }

            const container = document.getElementById('summary-cards-container');
            container.innerHTML = '';

            const auditProgram = getProgramType();
            const isSlipDetails = isSlipTabForm();
            const isAuditReportValue = isAuditReport();
            const isTemplateAuditValue = isTemplateAudit();
            const isLegacyReportValue = isLegacyReport();
            const isInspectionAuditValue = isInspectionAudit();
            const isParaReportValue = isParaReport();
            const isParaCountValue = isParaCount();
            const isRetirementParaValue = isRetirementPara();
            const selectedDepartment = document.getElementById('department-filter').value;

            let deptName = '';
             if (selectedDepartment !== 'all') {
                 const selectedOption = document.querySelector(`#department-filter option[value="${selectedDepartment}"]`);
              if (selectedOption) {
                     deptName = selectedOption.dataset.shortName;
                 }
             }

            const summaryTitle = document.querySelector('.summary .card-title h2');
            if (deptName) {
                if (isAuditReportValue) {
                    summaryTitle.textContent = `${deptName} - Audit Report Summary`;
                } else if (isTemplateAuditValue) {
                    summaryTitle.textContent = `${deptName} - Template Audit Summary`;
                } else if (isLegacyReportValue) {
                    summaryTitle.textContent = `${deptName} - Legacy Report Summary`;
                } else if (isInspectionAuditValue) {
                    summaryTitle.textContent = `${deptName} - Inspection Audit Summary`;
                } else if (isParaReportValue) {
                    summaryTitle.textContent = `${deptName} - Audit Para Details Summary`;
                } else if (isParaCountValue) {
                    summaryTitle.textContent = `${deptName} - Audit Para Count Summary`;
                } else if (isRetirementParaValue) {
                    summaryTitle.textContent = `${deptName} - Retirement Para Details Summary`;
                } else {
                    summaryTitle.textContent = isSlipDetails ?
                        `${deptName} - Audit Slip Summary` :
                        `${deptName} - Audit Plan Summary`;
                }
            } else {
                if (isAuditReportValue) {
                    summaryTitle.textContent = 'All Departments - Audit Report Overview';
                } else if (isTemplateAuditValue) {
                    summaryTitle.textContent = 'All Departments - Template Audit Overview';
                } else if (isLegacyReportValue) {
                    summaryTitle.textContent = 'All Departments - Legacy Report Overview';
                } else if (isInspectionAuditValue) {
                    summaryTitle.textContent = 'All Departments - Inspection Audit Overview';
                } else if (isParaReportValue) {
                    summaryTitle.textContent = 'All Departments - Audit Para Details Overview';
                } else if (isParaCountValue) {
                    summaryTitle.textContent = 'All Departments - Audit Para Count Overview';
                } else if (isRetirementParaValue) {
                    summaryTitle.textContent = 'All Departments - Retirement Para Details Overview';
                } else {
                    summaryTitle.textContent = isSlipDetails ?
                        'All Departments - Audit Slips Overview' :
                        'All Departments - Audit Plan Overview';
                }
            }

            let summaryData = [];

            if (isAuditReportValue) {
                summaryData = [{
                        id: 'audit_completed-card',
                        icon: '✅',
                        value: (dashboardData.summary.audit_completed?.toLocaleString('en-IN') ?? '0'),
                        label: 'Audit Completed',
                        color: 'success',
                        disabled: (dashboardData.summary.audit_completed ?? 0) === 0
                    },
                    {
                        id: 'report_finalized-card',
                        icon: '📄',
                        value: (dashboardData.summary.report_finalized?.toLocaleString('en-IN') ?? '0'),
                        label: 'Report Finalized',
                        color: 'info',
                        disabled: (dashboardData.summary.report_finalized ?? 0) === 0
                    },
                    {
                        id: 'report_issued-card',
                        icon: '📤',
                        value: (dashboardData.summary.report_issued?.toLocaleString('en-IN') ?? '0'),
                        label: 'Report Issued',
                        color: 'primary',
                        disabled: (dashboardData.summary.report_issued ?? 0) === 0
                    },
                    {
                        id: 'pending_finalize-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pending_finalize?.toLocaleString('en-IN') ?? '0'),
                        label: 'Pending to Finalize',
                        color: 'warning',
                        disabled: (dashboardData.summary.pending_finalize ?? 0) === 0
                    },
                    {
                        id: 'pending_issue-card',
                        icon: '📬',
                        value: (dashboardData.summary.pending_issue?.toLocaleString('en-IN') ?? '0'),
                        label: 'Pending to Issue',
                        color: 'danger',
                        disabled: (dashboardData.summary.pending_issue ?? 0) === 0
                    }
                ];
            } else if (isSlipDetails) {
                summaryData = [{
                        id: 'total-card',
                        icon: '📋',
                        value: (dashboardData.summary.total?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Slips',
                        color: 'primary',
                        disabled: (dashboardData.summary.total ?? 0) === 0
                    },
                    {
                        id: 'pending-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pending?.toLocaleString('en-IN') ?? '0'),
                        label: 'Pending Slips',
                        color: 'warning',
                        disabled: (dashboardData.summary.pending ?? 0) === 0
                    },
                    {
                        id: 'converted-card',
                        icon: '🔄',
                        value: (dashboardData.summary.converted?.toLocaleString('en-IN') ?? '0'),
                        label: 'Converted to Paras',
                        color: 'success',
                        disabled: (dashboardData.summary.converted ?? 0) === 0
                    },
                    {
                        id: 'dropped-card',
                        icon: '🗑️',
                        value: (dashboardData.summary.dropped?.toLocaleString('en-IN') ?? '0'),
                        label: 'Dropped Slips',
                        color: 'danger',
                        disabled: (dashboardData.summary.dropped ?? 0) === 0
                    }
                ];
            } else if (isTemplateAuditValue) {
                summaryData = [{
                        id: 'total-card',
                        icon: '📋',
                        value: (dashboardData.summary.total?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total no. of Template Audits',
                        color: 'primary',
                        disabled: (dashboardData.summary.total ?? 0) === 0
                    },
                    {
                        id: 'completed-card',
                        icon: '✅',
                        value: (dashboardData.summary.completed?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total no. of Audits Completed',
                        color: 'success',
                        disabled: (dashboardData.summary.completed ?? 0) === 0
                    },
                    {
                        id: 'ongoing-card',
                        icon: '🕓',
                        value: (dashboardData.summary.ongoing?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total no. of Audits Ongoing',
                        color: 'warning',
                        disabled: (dashboardData.summary.ongoing ?? 0) === 0
                    },
                    {
                        id: 'pending-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pending?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total no. of Audits Pending',
                        color: 'danger',
                        disabled: (dashboardData.summary.pending ?? 0) === 0
                    },
                ];
            } else if (isInspectionAuditValue) {
                summaryData = [{
                        id: 'total-card',
                        icon: '📋',
                        value: (dashboardData.summary.total?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Scheduled Institutions',
                        color: 'primary',
                        disabled: (dashboardData.summary.total ?? 0) === 0
                    },
                    {
                        id: 'completed-card',
                        icon: '✅',
                        value: (dashboardData.summary.completed?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Instituions Completed',
                        color: 'success',
                        disabled: (dashboardData.summary.completed ?? 0) === 0,
                    },
                    {
                        id: 'ongoing-card',
                        icon: '🕓',
                        value: (dashboardData.summary.ongoing?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Instituions Ongoing',
                        color: 'warning',
                        disabled: (dashboardData.summary.ongoing ?? 0) === 0
                    },
                    {
                        id: 'pending-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pending?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Instituions Pending',
                        color: 'danger',
                        disabled: (dashboardData.summary.pending ?? 0) === 0
                    }
                ];
            } else if (isLegacyReportValue) {
                summaryData = [{
                        id: 'total-card',
                        icon: '📋',
                        value: (dashboardData.summary.total?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Legacy Entries',
                        color: 'primary',
                        disabled: (dashboardData.summary.total ?? 0) === 0
                    },
                    {
                        id: 'finalize-card',
                        icon: '✅',
                        value: (dashboardData.summary.finalize?.toLocaleString('en-IN') ?? '0'),
                        label: 'Legacy Reports Finalized',
                        color: 'success',
                        disabled: (dashboardData.summary.finalize ?? 0) === 0
                    },
                    {
                        id: 'pending-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pending?.toLocaleString('en-IN') ?? '0'),
                        label: 'Legacy Reports Pending',
                        color: 'warning',
                        disabled: (dashboardData.summary.pending ?? 0) === 0
                    }
                ];
            } else if (isParaReportValue) {
                summaryData = [{
                        id: 'totalparas-card',
                        icon: '📋',
                        value: (dashboardData.summary.totalparas?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Para(s)',
                        color: 'primary',
                        disabled: (dashboardData.summary.totalparas ?? 0) === 0
                    },
                    {
                        id: 'processedparas-card',
                        icon: '✅',
                        value: (dashboardData.summary.processedparas?.toLocaleString('en-IN') ?? '0'),
                        label: 'Under Processing Para(s)',
                        color: 'success',
                        disabled: (dashboardData.summary.processedparas ?? 0) === 0
                    },
                    {
                        id: 'pendingparas-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pendingparas?.toLocaleString('en-IN') ?? '0'),
                        label: 'Pending Para(s)',
                        color: 'warning',
                        disabled: (dashboardData.summary.pendingparas ?? 0) === 0
                    },
                ];
            } else if (isParaCountValue) {
                summaryData = [
                    {
                        id: 'totalparas-card',
                        icon: '📋',
                        value: (dashboardData.summary.totalparas?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Para(s)',
                        color: 'primary',
                        disabled: (dashboardData.summary.totalparas ?? 0) === 0
                    },
                    {
                        id: 'pendingparas-card',
                        icon: '🕓',
                        value: (dashboardData.summary.pendingparas?.toLocaleString('en-IN') ?? '0'),
                        label: 'Pending Para(s)',
                        color: 'warning',
                        disabled: (dashboardData.summary.pendingparas ?? 0) === 0
                    },
                    {
                        id: 'processedparas-card',
                        icon: '✅',
                        value: (dashboardData.summary.processedparas?.toLocaleString('en-IN') ?? '0'),
                        label: 'Processed Para(s)',
                        color: 'success',
                        disabled: (dashboardData.summary.processedparas ?? 0) === 0
                    }
                ];
            } else if (isRetirementParaValue) {
                summaryData = [
                    {
                        id: 'total_retirement_paracount-card',
                        icon: '📋',
                        value: (dashboardData.summary.total_retirement_paracount?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Retirement Paras',
                        color: 'primary',
                        disabled: (dashboardData.summary.total_retirement_paracount ?? 0) === 0
                    },
                    {
                        id: 'pending_retirement_paracount-card',
                        icon: '⏳',
                        value: (dashboardData.summary.pending_retirement_paracount?.toLocaleString('en-IN') ?? '0'),
                        label: 'Pending Retirement Paras',
                        color: 'warning',
                        disabled: (dashboardData.summary.pending_retirement_paracount ?? 0) === 0
                    },
                    {
                        id: 'processed_retirement_paracount-card',
                        icon: '✅',
                        value: (dashboardData.summary.processed_retirement_paracount?.toLocaleString('en-IN') ?? '0'),
                        label: 'Processed Retirement Paras',
                        color: 'success',
                        disabled: (dashboardData.summary.processed_retirement_paracount ?? 0) === 0
                    }
                ];
            } else {
                summaryData = [{
                        id: 'regions-card',
                        icon: '🕍',
                        value: (dashboardData.summary.regions?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Auditable Regions',
                        color: 'primary',
                        disabled: (dashboardData.summary.regions ?? 0) === 0
                    },
                    {
                        id: 'districts-card',
                        icon: '🗺️',
                        value: (dashboardData.summary.districts?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Auditable Districts',
                        color: 'warning',
                        disabled: (dashboardData.summary.districts ?? 0) === 0
                    },
                    {
                        id: 'institutions-card',
                        icon: '🏫',
                        value: (dashboardData.summary.institutions?.toLocaleString('en-IN') ?? '0'),
                        label: 'Total Auditable Institutions',
                        color: 'success',
                        disabled: (dashboardData.summary.institutions ?? 0) === 0
                    }
                ];
            }

            const allDisabled = summaryData.every(item => item.disabled);
            if (allDisabled) {
                container.innerHTML =
                    `
                <div class="col-12">
                    <div class="alert alert-info text-left">
                        <i class="fas fa-info-circle me-2"></i>
                        No data available for the selected filters
                    </div>
                </div>
                `;
                return;
            }

            summaryData.forEach(item => {
                const card = document.createElement('div');
                card.className = `stat-card ${item.color} ${item.disabled ? 'disabled' : ''}`;
                card.id = item.id;

                if (!item.disabled) {
                    card.onclick = () => {
                        currentViewType = item.id.replace('-card', '');
                        const currentDept = document.getElementById('department-filter').value;
                        currentDepartment = currentDept === 'all' ? '' : currentDept;
                        showDepartmentView(currentViewType);
                    };
                }

                card.innerHTML = `
                    <div class="stat-icon ${item.color}">${item.icon}</div>
                    <div class="stat-content">
                        <h3>${item.value}</h3>
                        <p>${item.label}</p>
                        ${item.sub ? `
                                <div class="sub-stats">
                                    <div class="sub-item">
                                        <span class="badge single">Single: ${item.sub.single}</span>
                                    </div>
                                    <div class="sub-item">
                                        <span class="badge multi">Multi: ${item.sub.multi}</span>
                                    </div>
                                </div>
                            ` : ''}
                    </div>
                    ${item.disabled ? '<div class="disabled-overlay"></div>' : ''}
                `;

                container.appendChild(card);
            });
        }

        function showDepartmentView(viewType) {
            currentViewType = viewType;
            const auditProgram = getProgramType();
            const isSlipDetails = isSlipTabForm();
            const isPlanAuditValue = 'plantabform' === auditProgram;
            const isAuditReportValue = isAuditReport();
            const isTemplateAuditValue = 'templateaudit' === auditProgram;
            const isLegacyReportValue = 'legacyreport' === auditProgram;
            const isParaReportValue = 'parareport' === auditProgram;
            const isParaCountValue = 'paracount' === auditProgram;
            const isRetirementParaValue = 'retirementpara' === auditProgram;
            const isInspectionAuditValue = isInspectionAudit();

            if (!dashboardData || !dashboardData[viewType]) {
                console.error(`Data not found for viewType: ${viewType}`);
                const container = document.getElementById('department-cards-container');
                container.innerHTML = '<div class="no-data">Data not available for this view</div>';
                document.getElementById('detail-card').style.display = 'none';
                return;
            }

            const data = dashboardData[viewType];
            const selectedDepartment = document.getElementById('department-filter').value;

            if (selectedDepartment !== 'all') {
                const selectedDept = data.departments.find(dept =>
                    dept.numericDeptCode === selectedDepartment ||
                    dept.name === selectedDepartment
                );

                if (selectedDept) {
                    if (selectedStatCard) {
                        selectedStatCard.classList.remove('selected');
                    }
                    selectedStatCard = document.getElementById(`${viewType}-card`);
                    if (selectedStatCard) {
                        selectedStatCard.classList.add('selected');
                    }

                    const title = data.title || 'Department Details';
                    document.getElementById('detail-view-title').textContent =
                        `${selectedDept.label ?? selectedDept.name} - ${title}`;

                    document.getElementById('department-card').style.display = 'none';
                    document.getElementById('detail-card').style.display = 'block';

                    currentDepartmentId = selectedDept.numericDeptCode || selectedDept.name;
                    loadDepartmentDataDirectly(selectedDept, viewType);
                    return;
                }
            }

            let title = data.title || 'Department Data';
            if (isSlipDetails) {
                const slipTitles = {
                    'total': 'All Departments Region-wise Distribution of Total Audit Slips',
                    'pending': 'All Departments Region-wise Distribution of Pending Audit Slips',
                    'converted': 'All Departments Region-wise Distribution of Converted Audit Slips to Paras',
                    'dropped': 'All Departments Region-wise Distribution of Dropped Audit Slips'
                };
                title = slipTitles[viewType] || title;
            } else if (isPlanAuditValue) {
                const planTitles = {
                    'regions': 'All Departments Region-wise Distribution of Audit Plans',
                    'districts': 'All Departments District-wise Distribution of Audit Plans',
                    'institutions': 'All Departments Institution-wise Distribution of Audit Plans'
                };
                title = planTitles[viewType] || title;
            } else if (isAuditReportValue) {

                const auditTitles = {
                    'audit_completed': 'All Departments Region-wise Distribution of Completed Audit Reports',
                    'report_finalized': 'All Departments Region-wise Distribution of Finalized Reports',
                    'report_issued': 'All Departments Region-wise Distribution of Issued Reports',
                    'pending_finalize': 'All Departments Region-wise Distribution of Pending to Finalize Reports',
                    'pending_issue': 'All Departments Region-wise Distribution of Pending to Issue Reports'
                };
                title = auditTitles[viewType] || title;
            } else if (isTemplateAuditValue) {
                const templateTitles = {
                    'total': 'All Departments Region-wise Distribution of Total Template Audits',
                    'pending': 'All Departments Region-wise Distribution of Pending Template Audits',
                    'ongoing': 'All Departments Region-wise Distribution of Ongoing Template Audits',
                    'completed': 'All Departments Region-wise Distribution of Completed Template Audits'
                };
                title = templateTitles[viewType] || title;
            } else if (isParaReportValue) {
                const templateTitles = {
                    totalparas: 'All Departments Region-wise Audit Para Details for Total Para(s)',
                    processedparas: 'All Departments Region-wise Audit Para Details for Under Processing Para(s)',
                    pendingparas: 'All Departments Region-wise Audit Para Details for Pending Para(s)'
                    // droppedparas: 'All Departments Region-wise Audit Para Details for Dropped Para(s)',
                    // rejectedparas: 'All Departments Region-wise Audit Para Details for Rejected Para(s)',
                    // clarifyparas: 'All Departments Region-wise Audit Para Details for Clarification Needed Para(s)'
                };
                title = templateTitles[viewType] || title;

            } else if (isParaCountValue) {
                const paraCountTitles = {
                    totalparas: 'Department-wise Audit Para Details for Total Para(s)',
                    pendingparas: 'Department-wise Audit Para Details for Pending Para(s)',
                    processedparas: 'Department-wise Audit Para Details for Processed Para(s)'
                };
                title = paraCountTitles[viewType] || title;
            } else if (isInspectionAuditValue) {
                const inspectionTitles = {
                    total: 'All Departments Region-wise Distribution of Total Inspection(s)',
                    pending: 'All Departments Region-wise Distribution of Pending Inspection(s)',
                    ongoing: 'All Departments Region-wise Distribution of Ongoing Inspection(s)',
                    completed: 'All Departments Region-wise Distribution of Completed Inspection(s)'
                };
                title = inspectionTitles[viewType] || title;

            } else if (isLegacyReportValue) {
                const legacyTitles = {
                    total: 'All Departments Region-wise Distribution of Total Legacy Entries',
                    finalize: 'All Departments Region-wise Distribution of Legacy Reports Finalized',
                    pending: 'All Departments Region-wise Distribution of Legacy Reports Pending'
                };
                title = legacyTitles[viewType] || title;
            } else if (isRetirementParaValue) {
                const retirementParaTitles = {
                    total_retirement_paracount: 'All Departments Region-wise Distribution of Total Retirement Paras',
                    pending_retirement_paracount: 'All Departments Region-wise Distribution of Pending Retirement Paras',
                    processed_retirement_paracount: 'All Departments Region-wise Distribution of Processed Retirement Paras'
                };
                title = retirementParaTitles[viewType] || title;
            }

            document.getElementById('department-view-title').textContent = title;

            if (selectedStatCard) {
                selectedStatCard.classList.remove('selected');
            }
            selectedStatCard = document.getElementById(`${viewType}-card`);
            if (selectedStatCard) {
                selectedStatCard.classList.add('selected');
            }

            const container = document.getElementById('department-cards-container');
            container.innerHTML = '';

            const departments = data.departments || [];
            if (departments.length === 0) {
                container.innerHTML = '<div class="no-data">No department data available for the selected filters</div>';
                return;
            }

            const displayDepartments = departments.filter(dept =>
                !dept.isTotal || selectedDepartment !== 'all'
            );

            if (displayDepartments.length === 0) {
                container.innerHTML = '<div class="no-data">No department data available for the selected filters</div>';
                return;
            }

            displayDepartments.forEach(dept => {
                const isDisabled = dept.value === 0;
                const card = document.createElement('div');
                card.className = `department-card ${dept.color || 'primary'} ${isDisabled ? 'disabled' : ''}`;

                if (dept.isTotal) {
                    card.classList.add('total-row');
                    card.style.border = '2px solid #666';
                    card.style.fontWeight = 'bold';
                    card.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)';

                }

                card.id = `dept-${dept.name || 'unknown'}`;
                card.dataset.sourceform = auditProgram;
                card.dataset.deptcode = dept.numericDeptCode ?? '';
                card.dataset.regioncode = dept.regionCode ?? '';
                card.dataset.distcode = dept.distCode || sessiondistCode || '';
                card.dataset.deptname = dept.name ?? dept.displayName ?? 'Unknown';
                card.dataset.viewtype = currentViewType;
                card.dataset.isTotal = dept.isTotal ? 'true' : 'false';

                if (!isDisabled && !dept.isTotal && dept.numericDeptCode) {
                    card.onclick = function() {
                        try {
                            hideAllDrillDownTables();
                            handleDepartmentCardClick(this);
                        } catch (e) {
                            console.error('Click handler error:', e);
                        }
                    };
                }

                const icon = dept.icon || '🏢';
                const displayName = dept.displayName || dept.name || 'Unknown';
                const value = dept.value ? dept.value.toLocaleString('en-IN') : '0';

                let cardContent = '';
                if (dept.isTotal) {
                    cardContent = `
                                                                                                            <i>${icon}</i>
                                                                                                            <h5 style="font-size: 1.1em;">${displayName}</h5>
                                                                                                            <h4 style="font-size: 1.4em;">${value}</h4>
                                                                                                        `;
                } else {
                    cardContent = `
                                                                                                            <i>${icon}</i>
                                                                                                            <h5>${displayName}</h5>
                                                                                                            <h4>${value}</h4>
                                                                                                            ${isDisabled ? '<div class="disabled-overlay"></div>' : ''}
                                                                                                        `;
                }

                card.innerHTML = cardContent;
                container.appendChild(card);
            });

            document.getElementById('department-card').style.display = 'block';
            document.getElementById('detail-card').style.display = 'none';
            hideAllDrillDownTables();
        }

        function loadDepartmentDataDirectly(department, viewType) {
            let deptFilterEl = document.getElementById('department-filter');
            let selectedOption = deptFilterEl.options[deptFilterEl.selectedIndex];

            let selectedDept = {
                numericDeptCode: deptFilterEl.value === 'all' ? 'all' : deptFilterEl.value,
                name: selectedOption.dataset.fullName || 'All Departments',
                label: selectedOption.dataset.shortName || 'All'
            };

            showLoadingChart();
            const quarter = document.getElementById('quarter-filter').value;
            const sourceForm = getProgramType();
            const url = isAuditReport() ? "load_auditreport_regionwise" : "load_regiondata";

            let viewTypeLabel = '';
            const rawTitle = document.getElementById('detail-view-title').textContent || '';

            if (isSlipTabForm()) {
                const slipTitles = {
                    'total': 'Region-wise Total Audit Slip(s) Details',
                    'pending': 'Region-wise Pending Audit Slip(s) Details',
                    'converted': 'Region-wise Converted to Para(s) Audit Slip(s) Details',
                    'dropped': 'Region-wise Dropped Audit Slip(s) Details'
                };
                viewTypeLabel = slipTitles[viewType] || rawTitle;
            } else if (isAuditReport()) {
                const auditTitles = {
                    'audit_completed': 'Region-wise Audit Completed Institution Details',
                    'report_finalized': 'Region-wise Audit Report Finalized Details',
                    'report_issued': 'Region-wise Audit Report Issued Details',
                    'pending_finalize': 'Region-wise Audit Pending to Finalize Details',
                    'pending_issue': 'Region-wise Audit Pending to Issue Details'
                };
                viewTypeLabel = auditTitles[viewType] || rawTitle;
            } else if (isPlantabForm()) {
                const planTitles = {
                    regions: 'Audit Plan Region-wise Details',
                    districts: 'Audit Plan District-wise Details',
                    institutions: 'Audit Plan Institution-wise Details',
                };

                viewTypeLabel = planTitles[viewType] || rawTitle;

            } else if (isTemplateAudit()) {
                const templateTitles = {
                    'total': 'Region-wise Total Template Audit Institution Details',
                    'pending': 'Region-wise Template Audit Pending Details',
                    'ongoing': 'Region-wise Template Audit Ongoing Details',
                    'completed': 'Region-wise Template Audit Completed Details',
                };
                viewTypeLabel = templateTitles[viewType] || rawTitle;
            } else if (isInspectionAudit()) {
                const inspectionTitles = {
                    'total': 'Region-wise Total Scheduled Institutions',
                    'pending': 'Region-wise Pending Instituions Details',
                    'ongoing': 'Region-wise Ongoing Instituions Details',
                    'completed': 'Region-wise Completed Instituions Details'
                };
                viewTypeLabel = inspectionTitles[viewType] || rawTitle;
            } else if (isLegacyReport()) {
                const inspectionTitles = {
                    total: 'Region-wise Total Legacy Entry Details',
                    finalize: 'Region-wise Finalized Legacy Report Details',
                    pending: 'Region-wise Pending Legacy Report Details',
                };
                viewTypeLabel = inspectionTitles[viewType] || rawTitle;

            } else if (isParaReport()) {
                const paraTitles = {
                    totalparas: 'Region-wise Audit Para Details for Total Para(s)',
                    processedparas: 'Region-wise Audit Para Details for Under Processing Para(s)',
                    pendingparas: 'Region-wise Audit Para Details for Pending Para(s)',
                    // droppedparas: 'Region-wise Audit Para Details for Dropped Para(s)',
                    // rejectedparas: 'Region-wise Audit Para Details for Rejected Para(s)',
                    // clarifyparas: 'Region-wise Audit Para Details for Clarification Needed Para(s)',
                };
                viewTypeLabel = paraTitles[viewType] || rawTitle;
            } else if (isParaCount()) {
                const paraCountTitles = {
                    totalparas: 'Region-wise Audit Para Details for Total Para(s)',
                    pendingparas: 'Region-wise Audit Para Details for Pending Para(s)',
                    processedparas: 'Region-wise Audit Para Details for Processed Para(s)'
                };
                viewTypeLabel = paraCountTitles[viewType] || rawTitle;
            } else if (isRetirementPara()) {
                const retirementParaTitles = {
                    total_retirement_paracount: 'Region-wise Retirement Para Details for Total Retirement Paras',
                    pending_retirement_paracount: 'Region-wise Retirement Para Details for Pending Retirement Paras',
                    processed_retirement_paracount: 'Region-wise Retirement Para Details for Processed Retirement Paras'
                };
                viewTypeLabel = retirementParaTitles[viewType] || rawTitle;
            } else{
                 viewTypeLabel = rawTitle;
            }



            const deptName = selectedDept.label || selectedDept.name || 'Department';
            let cleanTitle = rawTitle.replace(deptName, '').trim();
            if (cleanTitle.startsWith('-')) cleanTitle = cleanTitle.substring(1).trim();

            document.getElementById('detail-view-title').textContent = `${deptName} : ${viewTypeLabel || cleanTitle}`;

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    deptCode: selectedDept.numericDeptCode,
                    sourceform: sourceForm,
                    financialyearcode: document.getElementById('year-filter').value,
                    quarter: quarter,
                    regionCode: selectedDept.regionCode ?? '',
                    distCode: selectedDept.distCode || sessiondistCode || '',
                    financialyearcode: document.getElementById('year-filter').value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (!response || !response.data) {
                        console.error('Invalid response format:', response);
                        document.getElementById('detail-card').innerHTML = `
                    <div class="alert alert-danger text-left">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Invalid data received from server
                    </div>
                `;
                        hideLoadingChart();
                        return;
                    }

                    const filteredData = response.data.filter(item => {
                        if (isAuditReport()) {
                            switch (viewType) {
                                case 'audit_completed':
                                    return parseInt(item.audit_completed || 0) > 0;
                                case 'report_finalized':
                                    return parseInt(item.report_finalized || 0) > 0;
                                case 'report_issued':
                                    return parseInt(item.report_issued || 0) > 0;
                                case 'pending_finalize':
                                    return parseInt(item.pending_finalize || 0) > 0;
                                case 'pending_issue':
                                    return parseInt(item.pending_issue || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isSlipTabForm()) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.totalslips || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pendingslipcount || 0) > 0;
                                case 'converted':
                                    return parseInt(item.convertedslipcount || 0) > 0;
                                case 'dropped':
                                    return parseInt(item.droppedslipcount || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isTemplateAudit()) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.total_count || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pending_count || 0) > 0;
                                case 'ongoing':
                                    return parseInt(item.ongoing_count || 0) > 0;
                                case 'completed':
                                    return parseInt(item.completed_count || 0) > 0;
                                default:
                                    return true;
                            }

                        } else if (isInspectionAudit()) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.total_inspection_count || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pending_inspection_count || 0) > 0;
                                case 'ongoing':
                                    return parseInt(item.ongoing_inspection_count || 0) > 0;
                                case 'completed':
                                    return parseInt(item.completed_inspection_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isLegacyReport()) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.total_legacy_count || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pending_legacy_count || 0) > 0;
                                case 'finalize':
                                    return parseInt(item.finalize_legacy_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isParaReport()) {
                            switch (viewType) {
                                case 'totalparas':
                                    return parseInt(item.totalparacount || 0) > 0;
                                case 'processedparas':
                                    return parseInt(item.pendingparacount || 0) > 0;
                                case 'pendingparas':
                                    return parseInt(item.processedparacount || 0) > 0;
                                    // case 'droppedparas':
                                    //     return parseInt(item.finalize_legacy_count || 0) > 0;
                                    // case 'rejectedparas':
                                    //     return parseInt(item.finalize_legacy_count || 0) > 0;
                                    // case 'clarifyparas':
                                    //     return parseInt(item.finalize_legacy_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isParaCount()) {
                            switch (viewType) {
                                case 'totalparas':
                                    return parseInt(item.totalparacount || 0) > 0;
                                case 'pendingparas':
                                    return parseInt(item.pendingparacount || 0) > 0;
                                case 'processedparas':
                                    return parseInt(item.processedparacount || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isRetirementPara()) {
                            switch (viewType) {
                                case 'total_retirement_paracount':
                                    return parseInt(item.total_retirement_paracount || 0) > 0;
                                case 'pending_retirement_paracount':
                                    return parseInt(item.pending_retirement_paracount || 0) > 0;
                                case 'processed_retirement_paracount':
                                    return parseInt(item.processed_retirement_paracount || 0) > 0;
                                default:
                                    return true;
                            }
                        } else {
                            return parseInt(item.distcount || 0) > 0 || parseInt(item.alloc_inscount ||
                                0) > 0;
                        }
                    });

                    if (filteredData.length === 0) {
                        document.getElementById('detail-card').innerHTML = `
                    <div class="alert alert-info text-left">
                        <i class="fas fa-info-circle me-2"></i>
                        No data available for ${deptName} in ${viewTypeLabel || viewType}
                    </div>
                `;

                        $('html, body').animate({
                            scrollTop: $('#detail-card').offset().top - 50
                        }, 500);

                        hideLoadingChart();
                        return;
                    }

                    updateDepartmentView(filteredData, viewType, selectedDept, sourceForm);

                    $('html, body').animate({
                        scrollTop: $('#detail-card').offset().top -
                            50
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    document.getElementById('detail-card').innerHTML = `
                <div class="alert alert-danger text-left">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to fetch data: ${error || 'Unknown error'}
                </div>
            `;

                    $('html, body').animate({
                        scrollTop: $('#detail-card').offset().top - 50
                    }, 500);

                    hideLoadingChart();
                },
                complete: function() {
                    hideLoadingChart();
                }
            });
        }

        function updateDepartmentView(data, viewType, selectedDept, sourceForm) {
            if (!data) {
                console.error('No data provided to updateDepartmentView');
                return;
            }

            const transformedData = transformRegionData(
                data,
                selectedDept.numericDeptCode,
                selectedDept.label ?? selectedDept.name,
                sourceForm,
                viewType
            );

            updateRegionChart(transformedData, selectedDept.numericDeptCode, sourceForm, data, viewType);
            updateRegionTable(transformedData, selectedDept.numericDeptCode, sourceForm, data, viewType);

            const departmentCard = document.getElementById('department-card');
            const detailCard = document.getElementById('detail-card');
            const slipviewDetails = document.getElementById('slipview_Details');
            const paraview_details = document.getElementById('paraview_details');

            if (departmentCard) departmentCard.style.display = 'none';
            if (detailCard) detailCard.style.display = 'block';
            if (slipviewDetails) slipviewDetails.style.display = 'none';
            if (paraview_details) paraview_details.style.display = 'none';

            const auditReportTable = $('#auditReportTableContainer');
            if (auditReportTable.length) auditReportTable.hide();

            hideAllDrillDownTables();
        }

        function hideAllDrillDownTables() {
            $('#districtTableContainer').hide();
            $('#institutionTableContainer').hide();
            $('#slipdetailsTableContainer').hide();
            $('#auditReportTableContainer').hide();
            document.getElementById('slipview_Details').style.display = 'none';
            document.getElementById('paraview_details').style.display = 'none';
            visibleTables.district = false;
            visibleTables.institution = false;
            visibleTables.audit = false;
            visibleTables.slipviewDetails = false;
            visibleTables.paraview_details = false;
            visibleTables.slipdetailsTableContainer = false;
        }

        function handleDepartmentCardClick(cardElement) {
            showLoadingChart();
            const deptCode = cardElement.dataset.deptcode || sessiondeptCode || '';
            const regionCode = cardElement.dataset.regioncode || '';
            const distCode = cardElement.dataset.distcode || sessiondistCode || '';
            const deptName = cardElement.dataset.deptname;
            const viewType = cardElement.dataset.viewtype || 'total';
            const quarter = document.getElementById('quarter-filter').value;

            // const effectiveQuarter = (deptCode !== 'all') ? quarter : '';
            const effectiveQuarter = quarter;

            currentDepartment = deptCode;
            let rawTitle = dashboardData[currentViewType]?.title || 'Audit Details';
            let viewTypeLabel = rawTitle;

            if (isSlipTabForm()) {
                const slipTitles = {
                    'total': 'Region-wise Distribution of Total Audit Slips',
                    'pending': 'Region-wise Distribution of Pending Audit Slips',
                    'converted': 'Region-wise Distribution of Audit Slips Converted to Paras',
                    'dropped': 'Region-wise Distribution of Dropped Audit Slips'
                };
                viewTypeLabel = slipTitles[viewType] || rawTitle;
            } else if (isPlantabForm()) {
                const planTitles = {
                    'regions': 'Region-wise Distribution of Audit Plans',
                    'districts': 'District-wise Distribution of Audit Plans',
                    'institutions': 'Institution-wise Distribution of Audit Plans'
                };
                viewTypeLabel = planTitles[viewType] || rawTitle;
            } else if (isAuditReport()) {
                const auditTitles = {
                    'audit_completed': 'Region-wise Distribution of Completed Audits',
                    'report_finalized': 'Region-wise Distribution of Finalized Audit Reports',
                    'report_issued': 'Region-wise Distribution of Issued Audit Reports',
                    'pending_finalize': 'Region-wise Distribution of Reports Pending to Finalize',
                    'pending_issue': 'Region-wise Distribution of Reports Pending to Issue'
                };
                viewTypeLabel = auditTitles[viewType] || rawTitle;
            } else if (isTemplateAudit()) {
                const templateTitles = {
                    'total': 'Region-wise Distribution of Total Template Audits',
                    'pending': 'Region-wise Distribution of Pending Template Audits',
                    'ongoing': 'Region-wise Distribution of Ongoing Template Audits',
                    'completed': 'Region-wise Distribution of Completed Template Audits'
                };
                viewTypeLabel = templateTitles[viewType] || rawTitle;
            } else if (isInspectionAudit()) {
                const inspectionTitles = {
                    'total': 'Region-wise Distribution of Total Scheduled Institutions',
                    'pending': 'Region-wise Distribution of Total Instituions Pending',
                    'ongoing': 'Region-wise Distribution of Total Instituions Ongoing',
                    'completed': 'Region-wise Distribution of Total Instituions Completed'
                };
                viewTypeLabel = inspectionTitles[viewType] || rawTitle;
            } else if (isLegacyReport()) {
                const legacyreportTitles = {
                    total: 'Region-wise Distribution of Total Legacy Entries',
                    finalize: 'Region-wise Distribution of Legacy Reports Finalized',
                    pending: 'Region-wise Distribution of Legacy Reports Pending',
                };
                viewTypeLabel = legacyreportTitles[viewType] || rawTitle;

            } else if (isParaReport()) {
                const paraTitles = {
                    totalparas: 'Region-wise Audit Para Details for Total Para(s)',
                    processedparas: 'Region-wise Audit Para Details for Under Processing Para(s)',
                    pendingparas: 'Region-wise Audit Para Details for Pending Para(s)',
                    // droppedparas: 'Region-wise Audit Para Details for Dropped Para(s)',
                    // rejectedparas: 'Region-wise Audit Para Details for Rejected Para(s)',
                    // clarifyparas: 'Region-wise Audit Para Details for Clarification Needed Para(s)',
                };
                viewTypeLabel = paraTitles[viewType] || rawTitle;
            } else if (isParaCount()) {
                const paraCountTitles = {
                    totalparas: 'Region-wise Audit Para Details for Total Para(s)',
                    processedparas: 'Region-wise Audit Para Details for Processed Para(s)',
                    pendingparas: 'Region-wise Audit Para Details for Pending Para(s)'
                };
                viewTypeLabel = paraCountTitles[viewType] || rawTitle;
            } else if (isRetirementPara()) {
                const retirementParaTitles = {
                    total_retirement_paracount: 'Region-wise Retirement Para Details for Total Retirement Paras',
                    pending_retirement_paracount: 'Region-wise Retirement Para Details for Pending Retirement Paras',
                    processed_retirement_paracount: 'Region-wise Retirement Para Details for Processed Retirement Paras'
                };
                viewTypeLabel = retirementParaTitles[viewType] || rawTitle;
             }  else {
                const match = rawTitle.match(/^Audit (.+?) Distribution$/i);
                if (match && match[1]) {
                    const part = match[1];
                    const keyword = part.split(' ')[0];
                    viewTypeLabel = `Region-wise Distribution of Audit ${keyword}`;
                }
            }


            document.getElementById('detail-view-title').textContent =
                `${deptName} : ${viewTypeLabel}`;

            if (selectedDepartmentCard) {
                selectedDepartmentCard.classList.remove('selected');
            }
            selectedDepartmentCard = cardElement;
            selectedDepartmentCard.classList.add('selected');

            document.querySelector('.chart-container').style.display = 'none';
            document.querySelector('.table-container').style.display = 'block';
            document.getElementById('view-toggle').textContent = 'View Graph';

            const sourceForm = document.getElementById('audit-program-filter').value;
            const isAuditReportValue = isAuditReport();
            const isSlipTabFormValue = isSlipTabForm();
            const isInspectionAuditValue = 'inspectionaudit' === sourceForm;
            const isTemplateAuditValue = 'templateaudit' === sourceForm;
            const isLegacyReportValue = 'legacyreport' === sourceForm;
            const isParaReportValue = 'parareport' === sourceForm;
            const isParaCountValue = 'paracount' === sourceForm;
            const isRetirementParaValue = 'retirementpara' === sourceForm;
            const url = isAuditReportValue ? "load_auditreport_regionwise" : "load_regiondata";

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    deptCode: deptCode || sessiondeptCode || '',
                    sourceform: sourceForm,
                    financialyearcode: document.getElementById('year-filter').value,
                    quarter: effectiveQuarter,
                    regionCode: regionCode,
                    // distCode: distCode,
                    distCode: distCode || sessiondistCode || '',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (!response || !response.data) {
                        console.error('Invalid response format:', response);
                        document.getElementById('detail-card').innerHTML = `
                            <div class="alert alert-danger text-left">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Invalid data received from server
                            </div>
                        `;
                        hideLoadingChart();
                        return;
                    }

                    const filteredData = response.data.filter(item => {
                        if (isAuditReportValue) {
                            switch (viewType) {
                                case 'audit_completed':
                                    return parseInt(item.audit_completed || 0) > 0;
                                case 'report_finalized':
                                    return parseInt(item.report_finalized || 0) > 0;
                                case 'report_issued':
                                    return parseInt(item.report_issued || 0) > 0;
                                case 'pending_finalize':
                                    return parseInt(item.pending_finalize || 0) > 0;
                                case 'pending_issue':
                                    return parseInt(item.pending_issue || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isSlipTabForm()) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.totalslips || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pendingslipcount || 0) > 0;
                                case 'converted':
                                    return parseInt(item.convertedslipcount || 0) > 0;
                                case 'dropped':
                                    return parseInt(item.droppedslipcount || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isTemplateAuditValue) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.total_count || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pending_count || 0) > 0;
                                case 'ongoing':
                                    return parseInt(item.ongoing_count || 0) > 0;
                                case 'completed':
                                    return parseInt(item.completed_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isParaReportValue) {
                            switch (viewType) {
                                case 'totalparas':
                                    return parseInt(item.totalparacount || 0) > 0;
                                case 'processedparas':
                                    return parseInt(item.processedparacount || 0) > 0;
                                case 'pendingparas':
                                    return parseInt(item.pendingparacount || 0) > 0;
                                    // case 'droppedparas':
                                    //     return parseInt(item.ongoing_count || 0) > 0;
                                    // case 'rejectedparas':
                                    //     return parseInt(item.completed_count || 0) > 0;
                                    // case 'clarifyparas':
                                    //     return parseInt(item.completed_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isParaCountValue) {
                            switch (viewType) {
                                case 'totalparas':
                                    return parseInt(item.totalparacount || 0) > 0;
                                case 'pendingparas':
                                    return parseInt(item.pendingparacount || 0) > 0;
                                case 'processedparas':
                                    return parseInt(item.processedparacount || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isInspectionAuditValue) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.total_inspection_count || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pending_inspection_count || 0) > 0;
                                case 'ongoing':
                                    return parseInt(item.ongoing_inspection_count || 0) > 0;
                                case 'completed':
                                    return parseInt(item.completed_inspection_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isLegacyReportValue) {
                            switch (viewType) {
                                case 'total':
                                    return parseInt(item.total_legacy_count || 0) > 0;
                                case 'pending':
                                    return parseInt(item.pending_legacy_count || 0) > 0;
                                case 'finalize':
                                    return parseInt(item.finalize_legacy_count || 0) > 0;
                                default:
                                    return true;
                            }
                        } else if (isRetirementParaValue) {
                            switch (viewType) {
                                case 'total_retirement_paracount':
                                    return parseInt(item.total_retirement_paracount || 0) > 0;
                                case 'pending_retirement_paracount':
                                    return parseInt(item.pending_retirement_paracount || 0) > 0;
                                case 'processed_retirement_paracount':
                                    return parseInt(item.processed_retirement_paracount || 0) > 0;
                                default:
                                    return true;
                            }
                         } else {
                            return parseInt(item.distcount || 0) > 0 || parseInt(item.alloc_inscount ||
                                0) > 0;
                        }
                    });

                    if (filteredData.length === 0) {
                        document.getElementById('detail-card').innerHTML = `
                                <div class="alert alert-info text-left">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No data available for ${deptName} in ${viewType}
                                </div>
                            `;

                        $('html, body').animate({
                            scrollTop: $('#detail-card').offset().top - 50
                        }, 500);

                        hideLoadingChart();
                        return;
                    }

                    const transformedData = transformRegionData(response.data, deptCode, deptName, sourceForm,
                        viewType);
                    updateRegionChart(transformedData, deptCode, sourceForm, response.data, viewType);
                    updateRegionTable(transformedData, deptCode, sourceForm, response.data, viewType);

                    document.getElementById('department-card').style.display = 'block';
                    document.getElementById('detail-card').style.display = 'block';

                    hideAllDrillDownTables();

                    $('html, body').animate({
                        scrollTop: $('#detail-card').offset().top - 50
                    }, 500);

                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    document.getElementById('detail-card').innerHTML = `
                                                                    <div class="alert alert-danger text-left">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        Failed to fetch department details: ${error || 'Unknown error'}
                                                                    </div>
                                                                `;

                    $('html, body').animate({
                        scrollTop: $('#detail-card').offset().top - 50
                    }, 500);
                },
                complete: function() {
                    hideLoadingChart();
                }
            });
        }

        // --- Region/Chart/Table Functions ---
        function transformRegionData(apiData, deptCode, deptName, sourceForm, viewType) {
            if (isAuditReport()) {
                return transformAuditReportData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            if (isTemplateAudit()) {
                return transformTemplateAuditData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            if (isInspectionAudit()) {
                return transformInspectionAuditData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            if (isLegacyReport()) {
                return transformLegacyReportData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            if (isParaReport()) {
                return transformParaReportData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            if (isParaCount()) {
                return transformParaCountData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            if (isRetirementPara()) {
                return transformRetirementParaData(apiData, deptCode, deptName, sourceForm, viewType);
            }

            const regionCount = apiData.length;
            const districtColors = getRichColorsFromPalette(regionCount);
            const institutionColors = districtColors.map(color =>
                color.replace(')', ', 0.7)').replace('rgb', 'rgba')
            );

            let mainTitle = "Audit Regions by Department";
            let slipColumnTitle = "Total Slip(s)";

            if (isSlipTabForm()) {
                switch (viewType) {
                    case 'total':
                        mainTitle = "Total Slips by Region";
                        slipColumnTitle = "Total Slip(s)";
                        break;
                    case 'pending':
                        mainTitle = "Pending Slips by Region";
                        slipColumnTitle = "Pending Slip(s)";
                        break;
                    case 'converted':
                        mainTitle = "Converted to Paras by Region";
                        slipColumnTitle = "Converted to Para(s)";
                        break;
                    case 'dropped':
                        mainTitle = "Dropped Slips by Region";
                        slipColumnTitle = "Dropped Slip(s)";
                        break;
                }
            }

            // Determine which datasets to show based on viewType
            let datasets = [];

            if (viewType === 'regions') {
                datasets = [{
                    label: 'Institutions',
                    data: [],
                    backgroundColor: institutionColors,
                    borderColor: '#fff',
                    borderWidth: 1
                }];
            } else if (viewType === 'districts') {
                datasets = [{
                    label: 'Districts',
                    data: [],
                    backgroundColor: districtColors,
                    borderColor: '#fff',
                    borderWidth: 1
                }];
            } else {
                datasets = [{
                    label: 'Institutions',
                    data: [],
                    backgroundColor: institutionColors,
                    borderColor: '#fff',
                    borderWidth: 1
                }];
            }

            const transformed = {
                chart: {
                    labels: [],
                    datasets: datasets
                },
                table: {
                    headers: isSlipTabForm() ? ["S.No", "Regions", "Total No. of Districts",
                        "Total No. of Institutions Audit Commenced",
                        slipColumnTitle
                    ] : (isParaCount() || isParaReport() || isLegacyReport()) ? ["S.No", "Regions",
                        "Total No. of Districts",
                        "Total No. of Para(s)"
                    ] : ["S.No", "Regions", "Total No. of Districts", "Total No. of Institutions"],
                    rows: []
                }


            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                const districtsCount = parseInt(item.distcount) || 0;
                const institutionsCount = parseInt(item.alloc_inscount) || 0;

                let slipCount = 0;
                if (isSlipTabForm()) {
                    switch (viewType) {
                        case 'total':
                            slipCount = parseInt(item.totalslips) || 0;
                            break;
                        case 'pending':
                            slipCount = parseInt(item.pendingslipcount) || 0;
                            break;
                        case 'converted':
                            slipCount = parseInt(item.convertedslipcount) || 0;
                            break;
                        case 'dropped':
                            slipCount = parseInt(item.droppedslipcount) || 0;
                            break;
                        default:
                            slipCount = parseInt(item.totalslips) || 0;
                    }
                }

                if (districtsCount > 0 || institutionsCount > 0 || (isSlipTabForm() && slipCount > 0)) {
                    transformed.chart.labels.push(regionName);

                    // Push data to appropriate datasets based on viewType
                    if (viewType === 'regions') {
                        transformed.chart.datasets[0].data.push(institutionsCount);
                    } else if (viewType === 'districts') {
                        transformed.chart.datasets[0].data.push(districtsCount);
                    } else {
                        transformed.chart.datasets[0].data.push(institutionsCount);
                    }

                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtsCount,
                        institutionsCount.toLocaleString('en-IN')
                    ];

                    if (isSlipTabForm()) {
                        rowData.push(slipCount);
                    }

                    transformed.table.rows.push(rowData);
                }
            });

            return transformed;
        }

        function transformAuditReportData(apiData, deptCode, deptName, sourceForm, viewType) {
            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Audit Report Status by Region";
            let metricColumnTitle = "Total No. of Institutions";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionname || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'audit_completed':
                        metricValue = parseInt(item.audit_completed) || 0;
                        districtCount = parseInt(item.audit_completed_distcount) || 0;
                        break;
                    case 'report_finalized':
                        metricValue = parseInt(item.report_finalized) || 0;
                        districtCount = parseInt(item.report_finalized_distcount) || 0;
                        break;
                    case 'report_issued':
                        metricValue = parseInt(item.report_issued) || 0;
                        districtCount = parseInt(item.report_issued_distcount) || 0;
                        break;
                    case 'pending_finalize':
                        metricValue = parseInt(item.pending_finalize) || 0;
                        districtCount = parseInt(item.pending_finalize_distcount) || 0;
                        break;
                    case 'pending_issue':
                        metricValue = parseInt(item.pending_issue) || 0;
                        districtCount = parseInt(item.pending_issue_distcount) || 0;
                        break;
                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);

                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];

                    transformed.table.rows.push(rowData);
                }
            });

            return transformed;
        }

        function transformTemplateAuditData(apiData, deptCode, deptName, sourceForm, viewType) {
            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Template Audit Report Status by Region";
            let metricColumnTitle = "Total No. of Institutions";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'total':
                        metricValue = parseInt(item.total_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'pending':
                        metricValue = parseInt(item.pending_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'ongoing':
                        metricValue = parseInt(item.ongoing_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'completed':
                        metricValue = parseInt(item.completed_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;

                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);

                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];

                    transformed.table.rows.push(rowData);
                }
            });

            return transformed;
        }

        function transformInspectionAuditData(apiData, deptCode, deptName, sourceForm, viewType) {

            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Inspection Status by Region";
            let metricColumnTitle = "Total No. of Institutions";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'total':
                        metricValue = parseInt(item.total_inspection_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'pending':
                        metricValue = parseInt(item.pending_inspection_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'ongoing':
                        metricValue = parseInt(item.ongoing_inspection_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'completed':
                        metricValue = parseInt(item.completed_inspection_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;

                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);
                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];
                    transformed.table.rows.push(rowData);
                }
            });
            return transformed;
        }

        function transformLegacyReportData(apiData, deptCode, deptName, sourceForm, viewType) {

            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Legacy Report by Region";
            let metricColumnTitle = "Total No. of Para(s)";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'total':
                        metricValue = parseInt(item.total_legacy_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'pending':
                        metricValue = parseInt(item.pending_legacy_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'finalize':
                        metricValue = parseInt(item.finalize_legacy_count) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);
                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];
                    transformed.table.rows.push(rowData);
                }
            });
            return transformed;
        }

        function transformParaReportData(apiData, deptCode, deptName, sourceForm, viewType) {

            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Audit Para Details by Region";
            let metricColumnTitle = "Total No. of Para(s)";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'totalparas':
                        metricValue = parseInt(item.totalparacount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'processedparas':
                        metricValue = parseInt(item.processedparacount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'pendingparas':
                        metricValue = parseInt(item.pendingparacount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                        // case 'droppedparas':
                        //     metricValue = parseInt(item.finalize_legacy_count) || 0;
                        //     districtCount = parseInt(item.distcount) || 0;
                        //     break;
                        // case 'rejectedparas':
                        //     metricValue = parseInt(item.finalize_legacy_count) || 0;
                        //     districtCount = parseInt(item.distcount) || 0;
                        //     break;
                        // case 'clarifyparas':
                        //     metricValue = parseInt(item.finalize_legacy_count) || 0;
                        //     districtCount = parseInt(item.distcount) || 0;
                        //     break;
                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);
                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];
                    transformed.table.rows.push(rowData);
                }
            });
            return transformed;
        }

        function transformParaCountData(apiData, deptCode, deptName, sourceForm, viewType) {
            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Audit Para Count by Region";
            let metricColumnTitle = "Total No. of Para(s)";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'totalparas':
                        metricValue = parseInt(item.totalparacount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'processedparas':
                        metricValue = parseInt(item.processedparacount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'pendingparas':
                        metricValue = parseInt(item.pendingparacount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);
                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];
                    transformed.table.rows.push(rowData);
                }
            });
            return transformed;
        }

        function transformRetirementParaData(apiData, deptCode, deptName, sourceForm, viewType) {
            const regionCount = apiData.length;
            const colors = getRichColorsFromPalette(regionCount);

            let mainTitle = "Retirement Para Details by Region";
            let metricColumnTitle = "Total No. of Para(s)";
            let districtColumnTitle = "Total No. of Districts";

            const transformed = {
                chart: {
                    labels: [],
                    datasets: [{
                        label: metricColumnTitle,
                        data: [],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                table: {
                    headers: ["S.No", "Regions", districtColumnTitle, metricColumnTitle],
                    rows: []
                }
            };

            let serialNumber = 1;
            apiData.forEach((item, index) => {
                const regionName = item.regionename || `Region ${index + 1}`;
                let metricValue = 0;
                let districtCount = 0;

                switch (viewType) {
                    case 'total_retirement_paracount':
                        metricValue = parseInt(item.total_retirement_paracount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'pending_retirement_paracount':
                        metricValue = parseInt(item.pending_retirement_paracount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                    case 'processed_retirement_paracount':
                        metricValue = parseInt(item.processed_retirement_paracount) || 0;
                        districtCount = parseInt(item.distcount) || 0;
                        break;
                }

                if (metricValue > 0) {
                    transformed.chart.labels.push(regionName);
                    transformed.chart.datasets[0].data.push(metricValue);
                    const rowData = [
                        serialNumber++,
                        regionName,
                        districtCount.toLocaleString('en-IN'),
                        metricValue.toLocaleString('en-IN')
                    ];
                    transformed.table.rows.push(rowData);
                }
            });
            return transformed;
        }

        function updateRegionChart(data, currentDepartment, sourceForm, response, viewType) {
            const ctx = document.getElementById('detail-chart').getContext('2d');

            const isAuditReport = sourceForm === 'auditreport';
            const isTemplateAudit = sourceForm === 'templateaudit';
            const isLegacyReport = sourceForm === 'legacyreport';
            const isParaReport = sourceForm === 'parareport';
            const isParaCount = viewType === 'paracount';
            const isRetirementPara = sourceForm === 'retirementpara';


            if (chart) {
                chart.destroy();
            }

            const regionCodeMap = {};
            if (Array.isArray(response) && response.length > 0) {
                response.forEach(region => {
                    if (region.regioncode) {
                        if (region.regionname) {
                            regionCodeMap[normalize(region.regionname)] = region.regioncode;
                        }
                        if (region.regionename) {
                            regionCodeMap[normalize(region.regionename)] = region.regioncode;
                        }
                        if (data.chart.labels && Array.isArray(data.chart.labels)) {
                            data.chart.labels.forEach(label => {
                                if (normalize(label) === normalize(region.regionname) ||
                                    normalize(label) === normalize(region.regionename)) {
                                    regionCodeMap[normalize(label)] = region.regioncode;
                                }
                            });
                        }
                    }
                });
            }

            const activeButton = document.querySelector('.chart-toggle-buttons .btn.active');
            const chartType = activeButton ? activeButton.getAttribute('data-chart-type') : 'bar';

            const datasetToShow = isAuditReport ?
                data.chart.datasets[0] :
                (chartType === 'pie' || chartType === 'doughnut' ?
                    data.chart.datasets[0] :
                    data.chart.datasets);
            chart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: data.chart.labels,
                    datasets: Array.isArray(datasetToShow) ? datasetToShow : [datasetToShow]
                },
                options: {
                    ...getMultiChartOptions(chartType, data.title),
                    onClick: (evt, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const regionName = data.chart.labels[index];

                            let regionCode = '';

                            regionCode = regionCodeMap[normalize(regionName)];

                            if (!regionCode) {
                                const matchingRegion = response.find(region =>
                                    normalize(region.regionname) === normalize(regionName) ||
                                    normalize(region.regionename) === normalize(regionName)
                                );
                                if (matchingRegion) {
                                    regionCode = matchingRegion.regioncode;
                                }
                            }

                            if (!regionCode && data.table && data.table.rows) {
                                const rowData = data.table.rows.find(row =>
                                    normalize(row[1]) === normalize(regionName)
                                );
                                if (rowData && rowData.length > 2 && rowData[2]) {
                                    regionCode = rowData[2];
                                }
                            }

                            const rowData = data.table.rows.find(row =>
                                normalize(row[1]) === normalize(regionName)
                            );

                            if (rowData) {
                                const deptCode = currentDepartment;
                                const deptName = document.getElementById('detail-view-title').textContent.split(
                                    ':')[0];
                                const auditProgram = document.getElementById('audit-program-filter').value;

                                if (auditProgram === 'plantabform') {
                                    if (viewType === 'regions') {
                                        loadAllocatedInstituteDetails(
                                            currentDepartment,
                                            regionCode,
                                            '',
                                            regionName,
                                            sourceForm,
                                            deptName,
                                            viewType
                                        );
                                    } else if (viewType === 'districts') {
                                        loadDistrictDetails(
                                            currentDepartment,
                                            regionCode,
                                            '',
                                            regionName,
                                            sourceForm,
                                            deptName,
                                            viewType
                                        );
                                    } else {
                                        loadAllocatedInstituteDetails(
                                            currentDepartment,
                                            regionCode,
                                            '',
                                            regionName,
                                            sourceForm,
                                            deptName,
                                            viewType
                                        );
                                    }
                                } else if (auditProgram === 'sliptabform') {
                                    loadCommencedInstituteDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );
                                } else if (auditProgram === 'auditreport') {
                                    loadAuditDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );
                                } else if (auditProgram === 'templateaudit') {
                                    loadTemplateauditInstituteDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );

                                } else if (auditProgram === 'inspectionaudit') {
                                    loadInspectionInstituteDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );
                                } else if (auditProgram === 'legacyreport') {
                                    loadlegacyInstituteDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );

                                } else if (auditProgram === 'parareport') {
                                    loadParaInstituteDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );
                                } else if (auditProgram === 'paracount') {
                                    loadParaCountInstituteDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );
                                } else if (auditProgram === 'retirementpara') {
                                    loadRetirementParaDetails(
                                        currentDepartment,
                                        regionCode,
                                        '',
                                        regionName,
                                        sourceForm,
                                        deptName,
                                        viewType
                                    );
                                }

                            }
                        }
                    }
                }
            });

            setupChartToggleButtons(data, currentDepartment, sourceForm, response, viewType, chartType);
        }

        function normalize(str) {
            return str?.toString().trim().toLowerCase().replace(/\s+/g, ' ') || '';
        }

        function setupChartToggleButtons(data, currentDepartment, sourceForm, response, viewType, chartType) {
            document.querySelectorAll('.chart-toggle-buttons button').forEach(btn => {
                btn.replaceWith(btn.cloneNode(true));
            });
            document.querySelectorAll('.chart-toggle-buttons button').forEach(button => {
                button.addEventListener('click', function() {
                    const newType = this.getAttribute('data-chart-type');
                    if (this.classList.contains('active')) return;

                    document.querySelectorAll('.chart-toggle-buttons button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    updateRegionChart(data, currentDepartment, sourceForm, response, viewType,
                        chartType);
                });
            });
        }

        function updateRegionTable(data, currentDepartment, sourceForm, response, viewType) {
            const table = document.getElementById('detail-table');
            const isAuditReportValue = sourceForm === 'auditreport';
            const isTemplateAudit = sourceForm === 'templateaudit';
            const isParaReportValue = sourceForm === 'parareport';
            const isLegacyReportValue = sourceForm === 'legacyreport';
            const isParaCountValue = sourceForm === 'paracount';
            const isRetirementParaValue = sourceForm === 'retirementpara';

            let whichslipValue = '';
            let loadFunction;

            if (!table) {
                console.error('Table element not found');
                return;
            }

            if (data.table.rows.length === 0) {
                table.innerHTML = `
            <div class="alert alert-info text-left">
                <i class="fas fa-info-circle me-2"></i>
                No table data available for the selected filters
            </div>
            `;
                return;
            }

            let titleexcel = "";
            const auditProgram = getProgramType();
            const deptName = document.getElementById('detail-view-title')?.textContent.split(':')[0] || '';

            const viewTypeLabels = {
                'audit_completed': 'Audit Completed',
                'report_finalized': 'Report Finalized',
                'report_issued': 'Report Issued',
                'pending_finalize': 'Pending to Finalize',
                'pending_issue': 'Pending to Issue',
                'total': 'Total Slips',
                'pending': 'Pending Slips',
                'converted': 'Converted to Paras',
                'dropped': 'Dropped Slips',
                'regions': 'Audit Regions',
                'districts': 'Audit Districts',
                'institutions': 'Auditable Institutions'
            };

            if (isAuditReportValue) {
                titleexcel = `${viewTypeLabels[viewType] || viewType} by Region (${deptName})`;
            } else if (sourceForm === 'sliptabform') {
                titleexcel = `${viewTypeLabels[viewType] || viewType} by Region (${deptName})`;
            } else {
                titleexcel = `${viewTypeLabels[viewType] || viewType} by Region (${deptName})`;
            }

            if (!data?.table?.rows || !data?.table?.headers) {
                console.error('Invalid data structure', data);
                return;
            }

            const columnCount = isAuditReportValue ? 4 : (sourceForm === 'sliptabform' ? 5 : 4);

            const totalDistricts = data.table.rows.reduce((sum, row) => {
                if (row.length < 3) return sum;
                const num = typeof row[2] === 'string' ? parseInt(row[2].replace(/,/g, '')) || 0 : parseInt(row[
                    2]) || 0;
                return sum + num;
            }, 0);

            const totalInstitutions = data.table.rows.reduce((sum, row) => {
                if (row.length < (isAuditReportValue ? 4 : 4)) return sum;
                const colIndex = isAuditReportValue ? 3 : 3;
                const num = typeof row[colIndex] === 'string' ? parseInt(row[colIndex].replace(/,/g, '')) || 0 :
                    parseInt(row[colIndex]) || 0;
                return sum + num;
            }, 0);

            let totalSlips = 0;
            if (sourceForm === 'sliptabform') {
                totalSlips = data.table.rows.reduce((sum, row) => {
                    if (row.length < 5) return sum;
                    const num = typeof row[4] === 'string' ? parseInt(row[4].replace(/,/g, '')) || 0 : parseInt(
                        row[
                            4]) || 0;
                    return sum + num;
                }, 0);
            }

            if ($.fn.DataTable.isDataTable(table)) {
                $(table).DataTable().destroy();
            }
            table.innerHTML = '';

            const normalize = str => str?.trim().toLowerCase();
            const regionCodeMap = {};
            if (Array.isArray(response) && response.length > 0) {
                response.forEach(region => {
                    const names = [];
                    if (region.regionname) names.push(region.regionname);
                    if (region.regionename) names.push(region.regionename);
                    names.forEach(name => {
                        if (name && region.regioncode) {
                            regionCodeMap[normalize(name)] = region.regioncode;
                        }
                    });
                });
            }

            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');

            for (let i = 0; i < columnCount; i++) {
                const th = document.createElement('th');
                th.textContent = data.table.headers[i] || '';
                headerRow.appendChild(th);
            }

            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');

            // Sort rows numerically (1s, 10s, 100s order)
            data.table.rows.sort((a, b) => {
                const numA = parseInt(a[2]?.toString().replace(/,/g, '')) || 0;
                const numB = parseInt(b[2]?.toString().replace(/,/g, '')) || 0;
                return numA - numB; // ascending order
            });

            data.table.rows.forEach((rowData, rowIndex) => {

                if (!Array.isArray(rowData)) return;

                const tr = document.createElement('tr');

                for (let cellIndex = 0; cellIndex < columnCount; cellIndex++) {
                    const cellData = rowData[cellIndex] || '';
                    const td = document.createElement('td');

                    // For audit report: S.no (0), Region (1), Auditable Districts (2), Institutions (3)
                    const numericColumns = isAuditReportValue  ? [2, 3] : (sourceForm === 'sliptabform' ? [2, 3, 4] : [2,3]);

                    if (numericColumns.includes(cellIndex)) {
                        const count = typeof cellData === 'object' ? cellData.value : parseInt(cellData) || 0;
                        const isClickable = count > 0;
                        const colors = {
                            2: isAuditReportValue  ? '#28a745' : '#28a745', // Districts - green for both
                            3: isAuditReportValue  ? '#007bff' : '#007bff', // Institutions - blue for both
                            4: '#7000cf' // Slips - purple
                        };
                        const color = colors[cellIndex] || '#6c757d';

                        const badge = document.createElement('span');
                        badge.textContent = typeof cellData === 'object' ?
                            cellData.display?.toLocaleString('en-IN') || '0' :
                            (cellData?.toLocaleString('en-IN') || '0');

                        Object.assign(badge.style, {
                            display: 'inline-flex',
                            alignItems: 'center',
                            justifyContent: 'end',
                            minWidth: '45px',
                            height: '26px',
                            padding: '0 15px',
                            borderRadius: '6px',
                            textAlign: 'right',
                            color: 'white',
                            backgroundColor: isClickable ? color : '#6c757d',
                            fontWeight: '600',
                            fontSize: '13px',
                            cursor: isClickable ? 'pointer' : 'default',
                            boxShadow: isClickable ? '0 2px 4px rgba(0,0,0,0.1)' : 'none',
                            transition: 'all 0.2s ease'
                        });


                        if (isClickable && cellIndex >= 1 && rowData[1]) {
                            const regionName = normalize(rowData[1]);
                            const regionCode = regionCodeMap[regionName] || '';

                            badge.addEventListener('mouseenter', () => {
                                badge.style.opacity = '0.9';
                                badge.style.transform = 'translateY(-1px)';
                            });
                            badge.addEventListener('mouseleave', () => {
                                badge.style.opacity = '1';
                                badge.style.transform = 'translateY(0)';
                            });

                            badge.addEventListener('click', (e) => {
                                e.stopPropagation();

                                window.isDirectRegionTableClick = true;

                                if (isPlantabForm()) {
                                    loadFunction = loadAllocatedInstituteDetails;
                                } else if (isSlipTabForm()) {
                                    loadFunction = loadCommencedInstituteDetails;
                                } else if (isTemplateAudit) {
                                    loadFunction = loadTemplateauditInstituteDetails;
                                } else if (isInspectionAudit()) {
                                    loadFunction = loadInspectionInstituteDetails;
                                } else if (isLegacyReportValue) {
                                    loadFunction = loadlegacyInstituteDetails;
                                } else if (isParaReportValue) {
                                    loadFunction = loadParaInstituteDetails;
                                } else if (isParaCountValue) {
                                    loadFunction = loadParaCountInstituteDetails;
                                } else if (isAuditReportValue) {
                                    loadFunction = loadAuditDetails;
                                } else if (isRetirementParaValue) {
                                    loadFunction = loadRetirementParaDetails;
                                }

                                const loadDistFunction = isAuditReportValue ? loadDistrictReportDetails :
                                    loadDistrictDetails;

                                if (cellIndex === 2) {
                                    if (!visibleTables.district || $('#districtTableContainer').data(
                                            'current-region') !== regionName) {
                                        loadDistFunction(currentDepartment, regionCode, '', regionName,
                                            sourceForm, deptName, viewType);
                                    }
                                } else if (cellIndex === 3 || cellIndex === 4) {
                                    if (sourceForm === 'sliptabform' && cellIndex === 3) {
                                        whichslipValue = 'nosliptab';
                                    } else {
                                        whichslipValue = getWhichSlipValue(viewType);
                                    }

                                    if (!visibleTables.institution || $('#institutionTableContainer')
                                        .data(
                                            'current-region') !== regionName) {
                                        loadFunction(currentDepartment, regionCode, '', regionName,
                                            sourceForm, deptName, viewType, whichslipValue);
                                    }
                                }

                                setTimeout(() => {
                                    window.isDirectRegionTableClick = false;
                                }, 100);
                            });
                        }

                        td.appendChild(badge);
                    } else {
                        td.textContent = cellData || '';
                        if (cellIndex === 1) {
                            td.style.fontWeight = '600';
                        }
                    }

                    tr.appendChild(td);
                }

                tbody.appendChild(tr);
            });

            table.appendChild(tbody);

            // Determine load functions
            if (isPlantabForm()) {
                loadFunction = loadAllocatedInstituteDetails;
            } else if (isSlipTabForm()) {
                loadFunction = loadCommencedInstituteDetails;
            } else if (isTemplateAudit) {
                loadFunction = loadTemplateauditInstituteDetails;
            } else if (isInspectionAudit()) {
                loadFunction = loadInspectionInstituteDetails;
            } else if (isLegacyReportValue) {
                loadFunction = loadlegacyInstituteDetails;
            } else if (isParaReportValue) {
                loadFunction = loadParaInstituteDetails;
            } else if (isParaCountValue) {
                loadFunction = loadParaCountInstituteDetails;
            } else if (isAuditReportValue) {
                loadFunction = loadAuditDetails;
            } else if (isRetirementParaValue) {
                loadFunction = loadRetirementParaDetails;
            }

            const loadDistFunction = isAuditReportValue ? loadDistrictReportDetails : loadDistrictDetails;
            const tfoot = document.createElement('tfoot');
            const footerRow = document.createElement('tr');

            const totalLabelCell = document.createElement('th');
            totalLabelCell.colSpan = isAuditReportValue ? 2 : 2;
            totalLabelCell.style.textAlign = 'right';
            totalLabelCell.style.fontWeight = 'bold';
            totalLabelCell.textContent = 'Total:';
            footerRow.appendChild(totalLabelCell);

            const districtTotalCell = document.createElement('th');
            districtTotalCell.style.fontWeight = 'bold';
            footerRow.appendChild(districtTotalCell);

            const institutionTotalCell = document.createElement('th');
            institutionTotalCell.style.fontWeight = 'bold';
            footerRow.appendChild(institutionTotalCell);

            let slipTotalCell;
            if (sourceForm === 'sliptabform') {
                slipTotalCell = document.createElement('th');
                slipTotalCell.style.fontWeight = 'bold';
                footerRow.appendChild(slipTotalCell);
            }

            tfoot.appendChild(footerRow);
            table.appendChild(tfoot);

            try {
                const dataTable = $(table).DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${titleexcel} - ${getFormattedDate()}`,
                        className: 'btn btn-success',
                        customize: function(xlsx) {
                            const sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c[r^="A1"]', sheet).attr('s', '2');
                            $('row c[r]', sheet).each(function() {
                                if ($.isNumeric($(this).text())) {
                                    $(this).attr('s', '52');
                                }
                            });
                        }
                    }],
                    responsive: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    destroy: true,
                    createdRow: function(row, data, dataIndex) {
                        // Check if this is a total row (data is an array of cell values)
                        const isTotalRow = data.some(cell => {
                            const cellText = typeof cell === 'string' ? cell : '';
                            return cellText.toUpperCase().includes('TOTAL');
                        });

                        if (isTotalRow) {
                            $(row).addClass('table-total-row');
                        }
                    },
                });

                function addClickableTotals() {
                    const districtBadge = createClickableTotal(totalDistricts, '#28a745', () => {
                        loadDistFunction(currentDepartment, '', '', '', sourceForm, deptName, viewType);
                    });
                    districtTotalCell.innerHTML = '';
                    districtTotalCell.appendChild(districtBadge);

                    const institutionBadge = createClickableTotal(totalInstitutions, '#007bff', () => {
                        loadFunction(currentDepartment, '', '', '', sourceForm, deptName, viewType,
                            whichslipValue);
                    });
                    institutionTotalCell.innerHTML = '';
                    institutionTotalCell.appendChild(institutionBadge);

                    if (sourceForm === 'sliptabform') {
                        const slipBadge = createClickableTotal(totalSlips, '#7000cf', () => {
                            loadFunction(currentDepartment, '', '', '', sourceForm, deptName, viewType,
                                whichslipValue);
                        });
                        slipTotalCell.innerHTML = '';
                        slipTotalCell.appendChild(slipBadge);
                    }
                }

                function createClickableTotal(count, color, onClickHandler) {

                    const isClickable = count > 0;
                    const badge = document.createElement('span');
                    badge.textContent = count.toLocaleString('en-IN');
                    Object.assign(badge.style, {
                        display: 'inline-flex',
                        alignItems: 'center',
                        justifyContent: 'end',
                        minWidth: '45px',
                        height: '26px',
                        padding: '0 15px',
                        borderRadius: '6px',
                        textAlign: 'right',
                        color: 'white',
                        backgroundColor: isClickable ? color : '#6c757d',
                        fontWeight: '600',
                        fontSize: '13px',
                        cursor: isClickable ? 'pointer' : 'default',
                        boxShadow: isClickable ? '0 2px 4px rgba(0,0,0,0.1)' : 'none',
                        transition: 'all 0.2s ease'
                    });

                    if (isClickable) {
                        badge.addEventListener('mouseenter', () => {
                            badge.style.opacity = '0.9';
                            badge.style.transform = 'translateY(-1px)';
                        });
                        badge.addEventListener('mouseleave', () => {
                            badge.style.opacity = '1';
                            badge.style.transform = 'translateY(0)';
                        });
                        badge.addEventListener('click', (e) => {
                            e.stopPropagation();
                            onClickHandler();
                        });
                    }

                    return badge;
                }

                addClickableTotals();

            } catch (e) {
                console.error('Error initializing DataTable:', e);
            }
        }

        // --- Drilldown Table Functions ---
        function getWhichSlipValue(viewType) {
            switch (viewType) {
                case 'total':
                    return "totalslips";
                case 'pending':
                    return "pendingslipcount";
                case 'converted':
                    return "convertedslipcount";
                case 'dropped':
                    return "droppedslipcount";
                default:
                    return "nosliptab";
            }
        }

        function loadDistrictDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            const isAuditSlip = sourceForm === 'sliptabform';
            const isInspectionAudit = sourceForm === 'inspectionaudit';
            const isLegacyReportValue = sourceForm === 'legacyreport';
            const isParaReport = sourceForm === 'parareport';
            const isParaCountValue = sourceForm === 'paracount';
            const isRetirementParaValue = sourceForm === 'retirementpara';
            const quarter = document.getElementById('quarter-filter')?.value || '';
            const financialyearcode = document.getElementById('year-filter')?.value || '';


            showLoading();
            $('#loader').show();
            if (visibleTables.institution || $('#institutionTableContainer').data('current-region') !== regionName) {
                $('#institutionTableContainer').hide();
                visibleTables.institution = false;
            }
            if (visibleTables.slipdetailsTableContainer) {
                $('#slipdetailsTableContainer').hide();
                visibleTables.slipdetailsTableContainer = false;
            }

            if (visibleTables.slipdetailsTableContainer) {
                $('#institutionTableContainer').hide();
                visibleTables.slipdetailsTableContainer = false;
            }

            if (visibleTables.district && $('#districtTableContainer').data('current-region') === regionName) {
                $('#loader').hide();
                hideLoading();
                return;
            }

            const tableElement = $('#districtTable');

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
            }
            tableElement.empty();

            const titles = {
                plantabform: {
                    title: `${deptName}: District-wise Audit Plans for ${regionName} Region`,
                    excel: `${deptName}: District-wise Audit Plans for ${regionName} Region`
                },
                templateaudit: {
                    title: `${deptName}: District-wise Template Audits for ${regionName} Region`,
                    excel: `${deptName}: District-wise Template Audits for ${regionName} Region`
                },
                sliptabform: {
                    title: `${deptName}: District-wise Audit slip Details for ${regionName} Region`,
                    excel: `${deptName}: District-wise Audit slip Details for ${regionName} Region`
                },
                inspectionaudit: {
                    title: `${deptName}: District-wise Inspection Audits for ${regionName} Region`,
                    excel: `${deptName}: District-wise Inspection Audits for ${regionName} Region`
                },
                auditreport: {
                    title: `${deptName}: District-wise Audit Reports for ${regionName} Region`,
                    excel: `${deptName}: District-wise Audit Reports for ${regionName} Region`
                },
                legacyreport: {
                    title: `${deptName}: District-wise legacy Report for ${regionName} Region`,
                    excel: `${deptName}: District-wise legacy Report for ${regionName} Region`
                },
                parareport: {
                    title: `${deptName}: District-wise Audit Para Details for ${regionName} Region`,
                    excel: `${deptName}: District-wise Audit Para Details for ${regionName} Region`
                },
                paracount: {
                    title: `${deptName}: District-wise Audit Para Details for ${regionName} Region`,
                    excel: `${deptName}: District-wise Audit Para Details for ${regionName} Region`
                },
                retirementpara: {
                    title: `${deptName}: District-wise Retirement Para Details for ${regionName} Region`,
                    excel: `${deptName}: District-wise Retirement Para Details for ${regionName} Region`
                }
            };

            const defaultTitle = {
                title: `${deptName}: District-wise Audit Plans for ${regionName} Region`,
                excel: `${deptName}: District-wise Audit Plans for ${regionName} Region`
            };

            // Use selected title/excel values instead of returning early
            const selectedTitle = titles[sourceForm] || defaultTitle;
            const title = selectedTitle.title;
            const excelTitle = selectedTitle.excel;

            $('#districtTableContainer .card-header h3').text(title);

            const whichslipValue = "nosliptab";

            const loadFunction = getLoadFunction(sourceForm);
            if (!loadFunction) {
                console.error('No load function found for sourceForm:', sourceForm);
                $('#loader').hide();
                hideLoading();
                alert('Invalid form type. Please try again.');
                return;
            }

            const colHeader = isParaReport || isLegacyReportValue || isParaCountValue ?
                '<th>Total No. of Para(s)</th>' :
                '<th>Total No. of Institutions</th>';

            tableElement.append(`
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Name of the District</th>
                        ${colHeader}
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right; font-weight:bold;">Total:</th>
                        <th id="totalInstitutionsCell" style="text-align:right; font-weight:bold;"></th>
                    </tr>
                </tfoot>
            `);


            // Initialize DataTable
            const dataTable = tableElement.DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                lengthMenu: [
                    [10, 30, 50, 100],
                    [10, 30, 50, 100]
                ],
                pageLength: 10,
                searching: true,
                ordering: true,
                ajax: {
                    url: 'load_districtdata',
                    type: 'POST',
                    data: function(d) {
                        return {
                            deptCode: deptCode || (typeof sessiondeptCode !== 'undefined' ? sessiondeptCode :
                                ''),
                            regionCode: regionCode,
                            distCode: distCode || (typeof sessiondistCode !== 'undefined' ? sessiondistCode :
                                ''),
                            sourceform: sourceForm,
                            quarter: quarter,
                            financialyearcode: financialyearcode,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        };
                    },
                    dataSrc: function(json) {
                        $('#loader').hide();
                        hideLoading();

                        if (!json || !json.data) {
                            console.error('Invalid response format:', json);
                            return [];
                        }

                        const totalInstitutions = calculateTotalInstitutions(json.data, sourceForm, viewType);
                        updateTotalBadge(totalInstitutions, deptCode, regionCode, regionName, sourceForm,
                            deptName, viewType, whichslipValue, loadFunction);

                        return filterAndTransformData(json.data, sourceForm, viewType);
                    },
                    error: function(xhr, error, thrown) {
                        $('#loader').hide();
                        hideLoading();
                        console.error("Error loading district details:", error, xhr.responseText);
                        alert('Failed to load district details. Please try again.');
                    }
                },
                dom: '<"top"Bfrtip<"clear">>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-download"></i> Download',
                    title: `${excelTitle} - ${getFormattedDate()}`,
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                }],
                responsive: true,
                autoWidth: false,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        className: 'text-left',
                    },
                    {
                        data: 'distename',
                        className: 'text-left',
                    },
                    {
                        data: 'alloc_inscount',
                        className: 'text-left',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const count = parseInt(data) || 0;
                                return createBadgeHTML(count, deptCode, row.regioncode, row.distcode,
                                    row.distename, sourceForm, deptName, viewType, whichslipValue);
                            }
                            return data;
                        }
                    }
                ],
                initComplete: function() {
                    setupBadgeInteractions(loadFunction);
                    $("#districtTable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                drawCallback: function(settings) {
                    const api = this.api();
                    $('.dataTables_paginate').toggle(api.data().length > api.page.len());
                }
            });

            // Show table container and update state
            $('#districtTableContainer').data('current-region', regionName);
            $('#districtTableContainer').show();
            visibleTables.district = true;

            // Scroll to table
            $('html, body').animate({
                scrollTop: $('#districtTableContainer').offset().top
            }, 500);
        }

        // Helper functions
        function getLoadFunction(sourceForm) {
            const loadFunctions = {
                plantabform: loadAllocatedInstituteDetails,
                templateaudit: loadTemplateauditInstituteDetails,
                sliptabform: loadCommencedInstituteDetails,
                inspectionaudit: loadInspectionInstituteDetails,
                auditreport: loadAuditDetails,
                legacyreport: loadlegacyInstituteDetails,
                parareport: loadParaInstituteDetails,
                paracount: loadParaCountInstituteDetails,
                retirementpara: loadRetirementParaDetails

            };

            return loadFunctions[sourceForm] || null;
        }

        function calculateTotalInstitutions(data, sourceForm, viewType) {
            if (!data || !Array.isArray(data)) return 0;

            switch (sourceForm) {
                case 'templateaudit':
                    return data.reduce((sum, row) => {
                        let count = 0;
                        switch (viewType) {
                            case 'total':
                                count = parseInt(row.total_count) || 0;
                                break;
                            case 'pending':
                                count = parseInt(row.pending_count) || 0;
                                break;
                            case 'ongoing':
                                count = parseInt(row.ongoing_count) || 0;
                                break;
                            case 'completed':
                                count = parseInt(row.completed_count) || 0;
                                break;
                            default:
                                count = (parseInt(row.pending_count) || 0) + (parseInt(row.ongoing_count) || 0) + (
                                    parseInt(row.completed_count) || 0);
                        }
                        return sum + count;
                    }, 0);

                case 'inspectionaudit':
                    return data.reduce((sum, row) => {
                        let count = 0;
                        switch (viewType) {
                            case 'total':
                                count = parseInt(row.total_inspection_count) || 0;
                                break;
                            case 'pending':
                                count = parseInt(row.pending_inspection_count) || 0;
                                break;
                            case 'ongoing':
                                count = parseInt(row.ongoing_inspection_count) || 0;
                                break;
                            case 'completed':
                                count = parseInt(row.completed_inspection_count) || 0;
                                break;
                            default:
                                count = (parseInt(row.not_scheduled_inspection_count) || 0) +
                                    (parseInt(row.pending_inspection_count) || 0) +
                                    (parseInt(row.ongoing_inspection_count) || 0) +
                                    (parseInt(row.completed_inspection_count) || 0);
                        }
                        return sum + count;
                    }, 0);

                case 'legacyreport':
                    return data.reduce((sum, row) => {
                        let count = 0;
                        switch (viewType) {
                            case 'total':
                                count = parseInt(row.total_legacy_count) || 0;
                                break;
                            case 'pending':
                                count = parseInt(row.pending_legacy_count) || 0;
                                break;
                            case 'finalize':
                                count = parseInt(row.finalize_legacy_count) || 0;
                                break;
                            default:
                                count = (parseInt(row.total_legacy_count) || 0) +
                                    (parseInt(row.pending_legacy_count) || 0) +
                                    (parseInt(row.finalize_legacy_count) || 0);
                        }
                        return sum + count;
                    }, 0);

                case 'parareport':
                    return data.reduce((sum, row) => {
                        let count = 0;
                        switch (viewType) {
                            case 'totalparas':
                                count = parseInt(row.totalparacount) || 0;
                                break;
                            case 'processedparas':
                                count = parseInt(row.processedparacount) || 0;
                                break;
                            case 'pendingparas':
                                count = parseInt(row.pendingparacount) || 0;
                                break;
                            default:
                                count = (parseInt(row.totalparacount) || 0) +
                                    (parseInt(row.processedparacount) || 0) +
                                    (parseInt(row.pendingparacount) || 0);
                        }
                        return sum + count;
                    }, 0);

                case 'paracount':
                     return data.reduce((sum, row) => {
                        let count = 0;
                        switch (viewType) {
                            case 'totalparas':
                                count = parseInt(row.totalparacount) || 0;
                                break;
                            case 'processedparas':
                                count = parseInt(row.processedparacount) || 0;
                                break;
                            case 'pendingparas':
                                count = parseInt(row.pendingparacount) || 0;
                                break;

                        }
                        return sum + count;
                    }, 0);

                    case 'retirementpara':
                     return data.reduce((sum, row) => {
                        let count = 0;
                        switch (viewType) {
                            case 'total_retirement_paracount':
                                count = parseInt(row.total_retirement_paracount) || 0;
                                break;
                            case 'pending_retirement_paracount':
                                count = parseInt(row.pending_retirement_paracount) || 0;
                                break;
                            case 'processed_retirement_paracount':
                                count = parseInt(row.processed_retirement_paracount) || 0;
                                break;

                        }
                        return sum + count;
                    }, 0);

                default:
                    return data.reduce((sum, row) => sum + (parseInt(row.alloc_inscount) || 0), 0);
            }
        }

        function updateTotalBadge(totalInstitutions, deptCode, regionCode, regionName, sourceForm, deptName, viewType,
            whichslipValue, loadFunction) {
            const isPositive = totalInstitutions > 0;
            const totalBadge = document.createElement('span');
            totalBadge.className = `badge ${isPositive ? 'clickable-badge' : ''}`;
            totalBadge.textContent = totalInstitutions.toLocaleString('en-IN');

            // Apply consistent style
            Object.assign(totalBadge.style, {
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'end',
                minWidth: '45px',
                height: '26px',
                padding: '0 15px',
                borderRadius: '6px',
                textAlign: 'center',
                color: 'white',
                backgroundColor: '#28a745',
                fontWeight: '600',
                fontSize: '13px',
                cursor: isPositive ? 'pointer' : 'default',
                boxShadow: isPositive ? '0 2px 4px rgba(0,0,0,0.1)' : 'none',
                transition: 'all 0.2s ease'
            });

            // Add data attributes
            $(totalBadge).attr({
                'data-dept': deptCode,
                'data-region': regionCode,
                'data-dist': '',
                'data-distname': `${regionName} (All Districts)`,
                'data-sourceform': sourceForm,
                'data-deptname': deptName,
                'data-viewtype': viewType,
                'data-whichtab': whichslipValue
            });

            // Add click only if count > 0
            if (isPositive) {
                totalBadge.addEventListener('click', function() {
                    loadFunction(deptCode, regionCode, '', `${regionName} (All Districts)`,
                        sourceForm, deptName, viewType, whichslipValue);
                });
            }

            $('#totalInstitutionsCell').empty().append(totalBadge);
        }

        function filterAndTransformData(data, sourceForm, viewType) {
            if (!data || !Array.isArray(data)) return [];

            switch (sourceForm) {
                case 'templateaudit':
                    return data.map(row => {
                        let institutionCount = 0;
                        switch (viewType) {
                            case 'total':
                                institutionCount = parseInt(row.total_count) || 0;
                                break;
                            case 'pending':
                                institutionCount = parseInt(row.pending_count) || 0;
                                break;
                            case 'ongoing':
                                institutionCount = parseInt(row.ongoing_count) || 0;
                                break;
                            case 'completed':
                                institutionCount = parseInt(row.completed_count) || 0;
                                break;
                            default:
                                institutionCount = (parseInt(row.pending_count) || 0) +
                                    (parseInt(row.ongoing_count) || 0) +
                                    (parseInt(row.completed_count) || 0);
                        }
                        return {
                            ...row,
                            alloc_inscount: institutionCount,
                            original_alloc_inscount: row.alloc_inscount
                        };
                    }).filter(row => parseInt(row.alloc_inscount) > 0);

                case 'inspectionaudit':
                    return data.map(row => {
                        let institutionCount = 0;
                        switch (viewType) {
                            case 'total':
                                institutionCount = parseInt(row.total_inspection_count) || 0;
                                break;
                            case 'pending':
                                institutionCount = parseInt(row.pending_inspection_count) || 0;
                                break;
                            case 'ongoing':
                                institutionCount = parseInt(row.ongoing_inspection_count) || 0;
                                break;
                            case 'completed':
                                institutionCount = parseInt(row.completed_inspection_count) || 0;
                                break;
                            default:
                                institutionCount = (parseInt(row.not_scheduled_inspection_count) || 0) +
                                    (parseInt(row.pending_inspection_count) || 0) +
                                    (parseInt(row.ongoing_inspection_count) || 0) +
                                    (parseInt(row.completed_inspection_count) || 0);
                        }
                        return {
                            ...row,
                            alloc_inscount: institutionCount,
                        };
                    }).filter(row => parseInt(row.alloc_inscount) > 0);

                case 'legacyreport':
                    return data.map(row => {
                        let institutionCount = 0;
                        switch (viewType) {
                            case 'total':
                                institutionCount = parseInt(row.total_legacy_count) || 0;
                                break;
                            case 'pending':
                                institutionCount = parseInt(row.pending_legacy_count) || 0;
                                break;
                            case 'finalize':
                                institutionCount = parseInt(row.finalize_legacy_count) || 0;
                                break;
                            default:
                                institutionCount = (parseInt(row.total_legacy_count) || 0) +
                                    (parseInt(row.pending_legacy_count) || 0) +
                                    (parseInt(row.finalize_legacy_count) || 0);
                        }
                        return {
                            ...row,
                            alloc_inscount: institutionCount,
                        };
                    }).filter(row => parseInt(row.alloc_inscount) > 0);

                case 'parareport':
                    return data.map(row => {
                        let institutionCount = 0;
                        switch (viewType) {
                            case 'totalparas':
                                institutionCount = parseInt(row.totalparacount) || 0;
                                break;
                            case 'processedparas':
                                institutionCount = parseInt(row.processedparacount) || 0;
                                break;
                            case 'pendingparas':
                                institutionCount = parseInt(row.pendingparacount) || 0;
                                break;
                            default:
                                institutionCount = (parseInt(row.totalparacount) || 0) +
                                    (parseInt(row.processedparacount) || 0) +
                                    (parseInt(row.pendingparacount) || 0);
                        }
                        return {
                            ...row,
                            alloc_inscount: institutionCount,
                        };
                    }).filter(row => parseInt(row.alloc_inscount) > 0);

                case 'paracount':
                    return data.map(row => {
                        let institutionCount = 0;
                        switch (viewType) {
                            case 'totalcount':
                                institutionCount = parseInt(row.totalcount) || 0;
                                break;
                            case 'processedparas':
                                institutionCount = parseInt(row.processedparacount) || 0;
                                break;
                            case 'pendingparas':
                                institutionCount = parseInt(row.pendingparacount) || 0;
                                break;
                        }
                        return {
                            ...row,
                            alloc_inscount: institutionCount,
                        };
                    }).filter(row => parseInt(row.alloc_inscount) > 0);

                case 'retirementpara':
                    return data.map(row => {
                        let institutionCount = 0;
                        switch (viewType) {
                            case 'total_retirement_paracount':
                                institutionCount = parseInt(row.total_retirement_paracount) || 0;
                                break;
                            case 'pending_retirement_paracount':
                                institutionCount = parseInt(row.pending_retirement_paracount) || 0;
                                break;
                            case 'processed_retirement_paracount':
                                institutionCount = parseInt(row.processed_retirement_paracount) || 0;
                                break;
                        }
                        return {
                            ...row,
                            alloc_inscount: institutionCount,
                        };
                    }).filter(row => parseInt(row.alloc_inscount) > 0);


                default:
                    return data.filter(row => parseInt(row.alloc_inscount) > 0);
            }
        }

        function createBadgeHTML(count, deptCode, regionCode, distCode, distName, sourceForm, deptName, viewType,
            whichslipValue) {
            const isPositive = count > 0;
            const badge = document.createElement('span');
            badge.className = `badge ${isPositive ? 'clickable-badge' : ''}`;
            badge.textContent = count.toLocaleString('en-IN');

            Object.assign(badge.style, {
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'end',
                minWidth: '45px',
                height: '26px',
                padding: '0 15px',
                borderRadius: '6px',
                textAlign: 'center',
                color: 'white',
                backgroundColor: '#28a745',
                fontWeight: '600',
                fontSize: '13px',
                cursor: isPositive ? 'pointer' : 'default',
                boxShadow: isPositive ? '0 2px 4px rgba(0,0,0,0.1)' : 'none',
                transition: 'all 0.2s ease'
            });

            $(badge).attr({
                'data-dept': deptCode,
                'data-region': regionCode,
                'data-dist': distCode,
                'data-distname': distName,
                'data-sourceform': sourceForm,
                'data-deptname': deptName,
                'data-viewtype': viewType,
                'data-whichtab': whichslipValue
            });

            return badge.outerHTML;
        }

        function setupBadgeInteractions(loadFunction) {
            $('.clickable-badge').hover(
                function() {
                    $(this).css('opacity', '0.8');
                },
                function() {
                    $(this).css('opacity', '1');
                }
            );

            $(document).off('click', '.clickable-badge').on('click', '.clickable-badge', function() {
                const $this = $(this);
                loadFunction(
                    $this.data('dept'),
                    $this.data('region'),
                    $this.data('dist'),
                    $this.data('distname'),
                    $this.data('sourceform'),
                    $this.data('deptname'),
                    $this.data('viewtype'),
                    $this.data('whichtab')
                );
            });
        }

        function loadDistrictReportDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            const quarter = document.getElementById('quarter-filter').value;
            const financialyearcode = document.getElementById('year-filter').value;
            showLoading();

            $('#loader').show();

            if (visibleTables.audit && $('#auditReportTableContainer').data('current-region') !== regionName) {
                $('#auditReportTableContainer').hide();
                visibleTables.audit = false;
            }

            if (visibleTables.district && $('#districtTableContainer').data('current-region') === regionName) {
                $('#loader').hide();
                hideLoading();
                return;
            }

            const tableElement = $('#districtTable');

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let titleexcel = '';

            if (isPlantabForm()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Audit Plans for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Audit Plans for ${regionName} Region`;

            } else if (isTemplateAudit()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Template Audits for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Template Audits for ${regionName} Region`;

            } else if (isSlipTabForm()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Audit slip Details for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Audit slip Details for ${regionName} Region`;

            } else if (isInspectionAudit()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Inspection Audits for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Inspection Audits for ${regionName} Region`;

            } else if (isAuditReport()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Audit Reports for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Audit Reports for ${regionName} Region`;

            } else if (isRetirementPara()){
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Retirement Para Details for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Retirement Para Details for ${regionName} Region`;

            } else if (isParaReport()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Audit Para Details for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Audit Para Details for ${regionName} Region`;

             } else if (isParaCount()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Audit Para Count for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Audit Para Count for ${regionName} Region`;

             } else if (isLegacyReport()) {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise legacy Report for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise legacy Report for ${regionName} Region`;

             } else {
                $('#districtTableContainer .card-header h3').text(
                    `${deptName}: District-wise Audit Plans for ${regionName} Region`
                );
                titleexcel = `${deptName}: District-wise Audit Plans for ${regionName} Region`;
            }

            let columns = [];
            let tableHeaders = '';

            columns = [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    className: 'text-left',
                    orderable: false
                },
                {
                    data: 'districtname',
                    className: 'text-left'
                }
            ];

            tableHeaders = `
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Name of the District</th>
                `;

            let viewTypeColumn = '';
            let viewTypeTitle = '';

            switch (viewType) {
                case 'audit_completed':
                    viewTypeColumn = 'audit_completed';
                    viewTypeTitle = 'Total No. of Institutions';
                    break;
                case 'report_finalized':
                    viewTypeColumn = 'report_finalized';
                    viewTypeTitle = 'Total No. of Institutions';
                    break;
                case 'report_issued':
                    viewTypeColumn = 'report_issued';
                    viewTypeTitle = 'Total No. of Institutions';
                    break;
                case 'pending_finalize':
                    viewTypeColumn = 'pending_finalize';
                    viewTypeTitle = 'Total No. of Institutions';
                    break;
                case 'pending_issue':
                    viewTypeColumn = 'pending_issue';
                    viewTypeTitle = 'Total No. of Institutions';
                    break;
            }

            columns.push({
                data: viewTypeColumn,
                className: 'text-right',

                render: function(data, type, row) {
                    if (type === 'display') {
                        const count = parseInt(data) || 0;
                        const isPositive = count > 0;
                        const badge = document.createElement('span');
                        badge.className = `badge ${isPositive ? 'clickable-badge' : ''}`;
                        badge.textContent = count.toLocaleString('en-IN');

                        Object.assign(badge.style, {
                            display: 'inline-flex',
                            alignItems: 'center',
                            justifyContent: 'end',
                            minWidth: '45px',
                            height: '26px',
                            padding: '0 15px',
                            borderRadius: '6px',
                            textAlign: 'center',
                            color: 'white',
                            backgroundColor: '#28a745',
                            fontWeight: '600',
                            fontSize: '13px',
                            cursor: isPositive ? 'pointer' : 'default',
                            boxShadow: isPositive ? '0 2px 4px rgba(0,0,0,0.1)' : 'none',
                            transition: 'all 0.2s ease'
                        });

                        $(badge).attr({
                            'data-dept': deptCode,
                            'data-region': row.regioncode || regionCode,
                            'data-dist': row.distcode || '',
                            'data-distname': row.districtname || '',
                            'data-sourceform': sourceForm,
                            'data-deptname': deptName,
                            'data-viewtype': viewType,
                        });

                        return badge.outerHTML;
                    }
                    return data;
                }

            });

            tableHeaders += `<th>${viewTypeTitle}</th></tr></thead>`;

            tableElement.append(tableHeaders +
                `<tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align:right; font-weight:bold;">Total:</th>
                                    <th id="totalInstitutionsCell" style="text-align:right; font-weight:bold;"></th>
                                </tr>
                            </tfoot>
                        `
            );

            const dataTable = tableElement.DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                lengthMenu: [
                    [10, 30, 50, 100],
                    [10, 30, 50, 100]
                ],
                pageLength: 10,
                searching: true,
                ordering: true,
                ajax: {
                    url: 'load_auditreport_districtwise',
                    type: 'POST',
                    data: function(d) {
                        return {
                            deptCode: deptCode || sessiondeptCode || '',
                            regionCode: regionCode,
                            distCode: distCode || sessiondistCode || '',
                            sourceform: sourceForm,
                            quarter: quarter,
                            viewType: viewType,
                            financialyearcode: financialyearcode,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        };
                    },
                    dataSrc: function(json) {
                        hideLoading();
                        $('#loader').hide();

                        if (!json || !json.data) {
                            console.error('Invalid response format:', json);
                            return [];
                        }

                        const totalCount = json.data.reduce((sum, row) => {
                            return sum + (parseInt(row[viewTypeColumn] || 0) || 0);
                        }, 0);


                        const totalBadge = $(
                            `<span class="badge ${totalCount > 0 ? 'clickable-badge' : ''}"
                                    style="display: inline-flex; align-items: center; justify-content: end; min-width: 45px; height: 26px; padding: 0 15px; border-radius: 6px; text-align: right; color: white; background-color: ${totalCount > 0 ? '#28a745' : '#dc3545'}; font-weight: 600; font-size: 13px; cursor: ${totalCount > 0 ? 'pointer' : 'default'}; box-shadow: ${totalCount > 0 ? '0 2px 4px rgba(0,0,0,0.1)' : 'none'}; transition: all 0.2s ease;"
                                    data-dept="${deptCode}"
                                    data-region="${regionCode}"
                                    data-dist=""
                                    data-distname="${regionName} (All Districts)"
                                    data-sourceform="${sourceForm}"
                                    data-deptname="${deptName}"
                                    data-viewtype="${viewType}">
                                    ${totalCount.toLocaleString('en-IN')}
                                </span>`
                        );

                        $('#totalInstitutionsCell').empty().append(totalBadge);

                        return json.data;
                    },
                    error: function(xhr, error, thrown) {
                        $('#loader').hide();
                        hideLoading();
                        console.error("Error loading district details:", error, xhr.responseText);
                        alert('Failed to load district details. Please try again.');
                    }
                },
                dom: '<"top"Bfrtip<"clear">>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-download"></i> Download',
                    title: `${titleexcel} - ${getFormattedDate()}`,
                    className: 'btn btn-success',
                }],
                responsive: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                columns: columns,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                initComplete: function() {
                    $('.clickable-badge').hover(
                        function() {
                            $(this).css('opacity', '0.8');
                        },
                        function() {
                            $(this).css('opacity', '1');
                        }
                    );

                    $(document).off('click', '.clickable-badge').on('click', '.clickable-badge',
                        function() {
                            const $this = $(this);
                            loadAuditDetails(
                                $this.data('dept'),
                                $this.data('region'),
                                $this.data('dist'),
                                $this.data('distname'),
                                $this.data('sourceform'),
                                $this.data('deptname'),
                                $this.data('viewtype')
                            );
                        });

                    $("#districtTable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>"
                    );
                },
                createdRow: function(row, data, dataIndex) {
                    if (data.districtname &&
                        (data.districtname.toUpperCase().includes('TOTAL') ||
                            data.districtname.toUpperCase().includes('ALL DISTRICTS'))) {
                        $(row).addClass('table-total-row');
                    }
                },
                drawCallback: function(settings) {
                    $('.clickable-badge').hover(
                        function() {
                            $(this).css('opacity', '0.8');
                        },
                        function() {
                            $(this).css('opacity', '1');
                        }
                    );

                    const api = this.api();
                    if (api.data().length <= api.page.len()) {
                        $('.dataTables_paginate').hide();
                    } else {
                        $('.dataTables_paginate').show();
                    }
                }
            });

            $('#districtTableContainer').data('current-region', regionName);
            $('#districtTableContainer').show();
            visibleTables.district = true;

            $('html, body').animate({
                scrollTop: $('#districtTableContainer').offset().top
            }, 500);
        }

        function loadAllocatedInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            showLoading();
            const quarter = document.getElementById('quarter-filter').value;
            const financialyearcode = document.getElementById('year-filter').value;
            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.institution &&
                $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const isSlipDetails = sourceForm === 'sliptabform';
            const isTemplateAudit = sourceForm === 'templateaudit';

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let viewTitle;
            if (isDistrictView) {
                viewTitle = `${deptName}: Institution-wise Audit plan Details for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: Institution-wise Audit plan Details for ${regionName} Region`;
            } else {
                viewTitle = `${deptName}: Institution-wise Audit plan Details`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            // Define columns configuration with proper widths
            const baseColumns = [{
                    data: null,
                    title: 'S.No',
                    className: 'text-center',
                    width: '60px',
                    render: (data, type, row, meta) => `<span>${meta.row + 1}</span>`
                },
                {
                    data: 'instename',
                    title: 'Institution',
                    className: 'text-left inst_name',
                    width: '300px',
                    render: function (data, type, row) {

                        const quarter = document.getElementById('quarter-filter').value;
                        let institutionHtml = `<span>${data || '-'}</span>`;

                        if (quarter === 'Q4' && row.prioritycode) {
                            if (row.prioritycode === '01') {
                                institutionHtml += `<span class="badge bg-danger ms-2" style="font-size:11px;">Q4 P1</span>`;
                            } else if (row.prioritycode === '02') {
                                institutionHtml += `<span class="badge bg-warning ms-2" style="font-size:11px;">Q4 P2</span>`;
                            }
                        }

                        if (row.auditmode === 'P') {
                            institutionHtml += `<span class="badge bg-info ms-2" style="font-size:10px;" title="Performance Audit">Performance Audit</span>`;
                        }

 			if (row.datafromapi === 'Y') {
                            institutionHtml += `<span class="badge bg-primary ms-2" style="font-size:10px;" >ERP</span>`;
                        }

                        return institutionHtml;
                    }
                },
                {
                    data: 'catename',
                    title: 'Category',
                    className: 'text-left',
                    render: data => `<span>${data || '-'}</span>`
                },
                {
                    data: 'subcatename',
                    title: 'Sub Category',
                    className: 'text-left',
                    render: data => data ? `<span>${data}</span>` : '<span class="text-muted">-</span>'
                },
                {
                    data: null,
                    title: 'Team Members',
                    className: 'text-left',
                    minWidth: '300px',
                    render: function(data) {
                        const head = data.team_head_en ?
                            `<div><strong class="small">Head:</strong> ${data.team_head_en.replace(' - ', '<span class="text-muted"> - </span>')}</div>` :
                            '';
                        const members = data.team_members_en ?
                            `<div><strong class="small">Members:</strong> ${data.team_members_en.replace(/ - /g, '<span class="text-muted"> - </span>')}</div>` :
                            '';
                        const size =
                            `<div><strong class="small">Team size:</strong> <span>${data.total_team_count || 0}</span></div>`;
                        return `<div class="team-info small">${head}${members}${size}</div>`;
                    }
                },
                {
                    data: 'mandays',
                    title: 'Mandays',
                    className: 'text-center',
                    width: '30px',
                    render: data => `<span>${data || '-'}</span>`
                },
                {
                    data: 'regionename',
                    title: 'Region',
                    className: 'text-left',
                    width: '120px',
                    render: data => `<span>${data || '-'}</span>`
                },
                {
                    data: 'distename',
                    title: 'District',
                    className: 'text-left',
                    width: '120px',
                    render: data => `<span>${data || '-'}</span>`
                },
                {
                    data: null,
                    title: 'From Date',
                    className: 'text-center',
                    width: '100px',
                    render: (data, type, row) => {
                        return row.fromdate && row.fromdate !== '-' ?
                            `<span class="badge bg-light text-dark border">${row.fromdate}</span>` :
                            '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    title: 'To Date',
                    className: 'text-center',
                    width: '100px',
                    render: (data, type, row) => {
                        return row.todate && row.todate !== '-' ?
                            `<span class="badge bg-light text-dark border">${row.todate}</span>` :
                            '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'schedule_status',
                    title: 'Schedule Status',
                    className: 'text-center',
                    width: '120px',
                    render: data =>
                        data === 'Scheduled' ?
                        '<span class="badge bg-success" style="font-size:11px;">Scheduled</span>' :
                        '<span class="badge bg-danger" style="font-size:11px;">Not Scheduled</span>'
                },
                {
                    data: 'response_status',
                    title: 'Intimation Status',
                    className: 'text-center',
                    width: '120px',
                    render: data =>
                        data === 'Replied' ?
                        '<span class="badge bg-success" style="font-size:11px;">Accepted</span>' :
                        '<span class="badge bg-warning" style="font-size:11px;">Pending</span>'
                },
                {
                    data: 'workallocation_status',
                    title: 'Work Allocation',
                    className: 'text-center',
                    width: '130px',
                    render: data =>
                        data === 'work allocated' ?
                        '<span class="badge bg-success" style="font-size:11px;">Work Allocated</span>' :
                        '<span class="badge bg-danger" style="font-size:11px;">Not Allocated</span>'
                },
                {
                    data: null,
                    title: 'Entry Meeting Date',
                    className: 'text-center',
                    width: '120px',
                    render: (data, type, row) => {
                        return row.entrymeet_status && row.entrymeet_status !== 'No' ?
                            `<span class="badge bg-success" style="font-size:11px;">${row.entrymeet_status}</span>` :
                            `<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Exit Meeting Date / Proposed Exit Meet Date',
                    className: 'text-center',
                    minWidth: '200px',
                    render: (data, type, row) => {

                        if (row.exitmeet_status && row.exitmeet_status !== 'No') {
                            return `<span class="badge bg-success" style="font-size:11px;">
                                        Exit: ${row.exitmeet_status}
                                    </span>`;
                        }

                        if (row.proposedexitmeetdate && row.proposedexitmeetdate !== '-') {
                            return `<span class="badge bg-primary" style="font-size:11px;">
                                        Proposed: ${row.proposedexitmeetdate}
                                    </span>`;
                        }

                        return `<span class="badge bg-danger" style="font-size:11px;">Not Conducted</span>`;
                    }
                }



            ];

            const statusColumn = {
                data: null,
                title: 'Status',
                className: 'text-center',
                width: '120px',
                render: function(data, type, row) {
                    let status = 'Not Started';
                    let badgeClass = 'bg-secondary';

                    if (row.workallocation_status === 'work allocated') {
                        status = 'Work Allocated';
                        badgeClass = 'bg-info';
                    }
                    if (row.entrymeet_status && row.entrymeet_status !== 'No') {
                        status = 'Entry Meeting Done';
                        badgeClass = 'bg-primary';
                    }
                    if (row.exitmeet_status && row.exitmeet_status !== 'No') {
                        status = 'Exit Meeting Done';
                        badgeClass = 'bg-success';
                    }
                    if (row.schedule_status === 'Scheduled') {
                        status = 'Scheduled';
                        badgeClass = 'bg-warning';
                    }

                    return `<span class="badge ${badgeClass}" style="font-size:11px;">${status}</span>`;
                }
            };

            let finalColumns;
            let columnDefs;

            if (isTemplateAudit) {
                // For templateaudit: show only S.No, Institution Name, Mandays, District, From Date, To Date, and Status
                finalColumns = [
                    baseColumns[0], // S.No
                    baseColumns[1], // Institution Name
                    baseColumns[5], // Mandays
                    baseColumns[6], // Region
                    baseColumns[7], // District
                    baseColumns[8], // From Date
                    baseColumns[9], // To Date
                    statusColumn // Status
                ];

                columnDefs = [{
                        targets: [0, 1],
                        className: 'all',
                        responsivePriority: 1
                    },
                    {
                        targets: [2, 3, 4, 5, 6],
                        className: 'd-none d-md-table-cell',
                        responsivePriority: 2
                    },
                    {
                        targets: '_all',
                        width: null
                    }
                ];
            } else {
                finalColumns = baseColumns;
                columnDefs = [{
                        targets: [2, 3],
                        visible: false,
                        searchable: true,
                        exportable: true
                    },
                    {
                        targets: [4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                        className: 'd-none d-md-table-cell extra-column',
                        responsivePriority: 2
                    },
                    {
                        targets: [0, 1],
                        className: 'all',
                        responsivePriority: 1
                    },
                    {
                        targets: '_all',
                        width: null
                    }
                ];
            }

            try {
                tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    columnDefs: columnDefs,
                    paging: true,
                    processing: true,
                    serverSide: false,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                        }
                    }],
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Prev'
                        }
                    },
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: {
                            deptCode: deptCode || sessiondeptCode || '',
                            regionCode: regionCode,
                            distCode: distCode || sessiondistCode || '',
                            sourceform: sourceForm,
                            quarter: quarter,
                            financialyearcode: financialyearcode,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataSrc: function(json) {
                            if (!json || !json.data) return [];
                            return json.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', xhr.responseText);
                            tableElement.html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: finalColumns,
                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_filter label').addClass('form-label');
                        $('.dt-buttons .btn').css('margin-right', '5px');

                        $('#institutionTableContainer').css({
                            'overflow': 'hidden',
                            'position': 'relative'
                        });

                        tableElement.DataTable().columns.adjust().draw();
                        hideLoading();

                        // Initialize mobile toggle AFTER table is fully loaded
                        setTimeout(() => {
                            const mobileColumns = isTemplateAudit ? ["mandays", "distename", "fromdate",
                                "todate", "status"
                            ] : ["teammembers", "mandays", "regionename", "distename",
                                "schedule_status", "response_status", "workallocation_status",
                                "entrymeet_status", "exitmeet_status", "viewfieldaudit"
                            ];
                            setupMobileRowToggle(mobileColumns);
                        }, 500);
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }
                        $(row).addClass('align-middle');
                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    drawCallback: function() {
                        $('.institution-link').on('click', function(e) {
                            e.preventDefault();
                            const insteid = $(this).data('id');
                        });

                        $('.clickable-badge').on('click', function(e) {
                            e.preventDefault();
                            const slipType = $(this).hasClass('bg-primary') ? 'total' :
                                $(this).hasClass('bg-danger') ? 'pending' :
                                $(this).hasClass('bg-success') ? 'converted' : 'dropped';
                        });
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                        );
                        hideLoading();
                    }
                });
            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }

            $('#institutionTableContainer').data('current-region', regionName);
            $('#institutionTableContainer').show();
            visibleTables.institution = true;

            $('html, body').animate({
                scrollTop: $('#institutionTableContainer').offset().top
            }, 500);
        }

        function loadTemplateauditInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName,
            viewType) {
            showLoading();
            const quarter = document.getElementById('quarter-filter').value;
            const financialyearcode = document.getElementById('year-filter').value;

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.institution && $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let viewTitle;
            if (isDistrictView) {
                viewTitle = `${deptName}: Institution-wise Template Audit Details for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: Institution-wise Template Audit Details for ${regionName} Region`;
            } else {
                viewTitle = `${deptName}: Institution-wise Template Audit Details`;
            }

            // Add viewType to title if specified
            if (viewType && viewType !== 'all') {
                viewTitle += ` (${viewType.charAt(0).toUpperCase() + viewType.slice(1)})`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            // Define columns configuration for Template Audit
            const columns = [{
                    data: null,
                    title: 'S.No',
                    className: 'text-left',
                    render: function(data, type, row, meta) {
                        return `<span>${meta.row + 1}</span>`;
                    }
                },
                {
                    data: 'instename',
                    title: 'Institution',
                    className: 'text-left',
                    width: '200px'
                },
                {
                    data: 'regionename',
                    title: 'Region',
                    className: 'text-left',
                },
                {
                    data: 'distename',
                    title: 'District',
                    className: 'text-left',
                },
                {
                    data: null,
                    title: 'Auditor Name',
                    className: 'text-left',
                    render: function(data, type, row) {
                        const auditorName = row.auditorname && row.auditorname !== 'Not Assigned' ? row
                            .auditorname : 'Not Assigned';
                        const auditorDesignation = row.auditordesignation && row.auditordesignation !==
                            'Not Assigned' ? row.auditordesignation : '';

                        if (auditorName === 'Not Assigned') {
                            return '<span class="text-muted">Not Assigned</span>';
                        }

                        if (auditorDesignation) {
                            return `<div>
                                <span class="fw-bold">${auditorName}</span>
                                <small class="fw-bold">(${auditorDesignation})</small>
                            </div>`;
                        } else {
                            return `<span class="fw-bold">${auditorName}</span>`;
                        }
                    }
                },
                {
                    data: null,
                    title: 'From Date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.fromdate && row.fromdate !== '-' ?
                            `<span class="date-badge">${row.fromdate}</span>` :
                            '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    title: 'To Date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.todate && row.todate !== '-' ?
                            `<span class="date-badge">${row.todate}</span>` :
                            '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    title: 'Start Date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.startdate && row.startdate !== '-' ?
                            `<span class="date-badge">${row.startdate}</span>` :
                            '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    title: 'End Date',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return row.enddate && row.enddate !== '-' ?
                            `<span class="date-badge">${row.enddate}</span>` :
                            '<span class="text-muted">-</span>';
                    }
                },

                {
                    data: null,
                    title: 'Audit Status',
                    className: 'text-center',
                    render: function(data, type, row) {
                        let status = 'Not Started';
                        let badgeClass = 'bg-secondary';

                        // Determine status based on template audit fields
                        if (row.startdate && row.startdate !== '-' && (!row.enddate || row.enddate === '-')) {
                            status = 'Ongoing';
                            badgeClass = 'bg-primary';
                        } else if (row.startdate && row.startdate !== '-' && row.enddate && row.enddate !==
                            '-') {
                            status = 'Completed';
                            badgeClass = 'bg-success';
                        } else if ((!row.startdate || row.startdate === '-') && (!row.enddate || row.enddate ===
                                '-')) {
                            status = 'Pending';
                            badgeClass = 'bg-warning';
                        }

                        return `<span class="badge ${badgeClass}" style="font-size:11px;">${status}</span>`;
                    }
                }
            ];

            try {
                tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    processing: true,
                    serverSide: false,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Prev'
                        }
                    },
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: function(d) {
                            return {
                                deptCode: deptCode || (typeof sessiondeptCode !== 'undefined' ?
                                    sessiondeptCode : ''),
                                regionCode: regionCode || '',
                                distCode: distCode || (typeof sessiondistCode !== 'undefined' ?
                                    sessiondistCode : ''),
                                sourceform: sourceForm || '',
                                quarter: quarter || '',
                                viewType: viewType || 'all',
                                financialyearcode: financialyearcode || '',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };
                        },
                        dataSrc: function(json) {
                            if (!json || !json.data) {
                                hideLoading();
                                return [];
                            }

                            // Filter data based on viewType if needed
                            let filteredData = json.data;
                            if (viewType && viewType !== 'all') {
                                filteredData = json.data.filter(row => {
                                    // Use actual date fields for filtering instead of count fields
                                    if (viewType === 'pending') {
                                        return (!row.startdate || row.startdate === '-') && (!row
                                            .enddate || row.enddate === '-');
                                    } else if (viewType === 'ongoing') {
                                        return row.startdate && row.startdate !== '-' && (!row
                                            .enddate || row.enddate === '-');
                                    } else if (viewType === 'completed') {
                                        return row.startdate && row.startdate !== '-' && row
                                            .enddate &&
                                            row.enddate !== '-';
                                    }
                                    return true;
                                });
                            }

                            hideLoading();
                            return filteredData;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', error, thrown);
                            console.error('Response:', xhr.responseText);
                            tableElement.html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: columns,
                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_filter label').addClass('form-label');
                        $('.dt-buttons .btn').css('margin-right', '5px');

                        tableElement.DataTable().columns.adjust().draw();
                        hideLoading();
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }
                        $(row).addClass('align-middle');
                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    drawCallback: function() {
                        // Add any additional draw callbacks if needed
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                        );
                        hideLoading();
                    }
                });

                $('#institutionTableContainer').data('current-region', regionName);
                $('#institutionTableContainer').show();
                visibleTables.institution = true;

                $('html, body').animate({
                    scrollTop: $('#institutionTableContainer').offset().top
                }, 500);

            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }
        }

        function loadInspectionInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName,
            viewType) {
            showLoading();
            const quarterElem = document.getElementById('quarter-filter');
            const quarter = (quarterElem && quarterElem.value) ? quarterElem.value : '';
            const financialyearcode = document.getElementById('year-filter').value || '';


            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.institution && $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let viewTitle;
            if (isDistrictView) {
                viewTitle = ` ${deptName}: Institution-wise Inspection Audit Details for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = ` ${deptName}: Institution-wise Inspection Audit Details for ${regionName} Region`;
            } else {
                viewTitle = ` ${deptName}: Institution-wise Inspection Audit Details`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            const columns = [{
                    data: null,
                    title: 'S.No',
                    className: 'align-middle text-center',
                    render: function(data, type, row, meta) {
                        return `<span>${meta.row + 1}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Department Details',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        let details = [];

                        if (row.department_name) {
                            details.push(`<strong>Department:</strong> ${row.department_name}`);
                        }

                        if (row.regionename) {
                            details.push(`<strong>Region:</strong> ${row.regionename}`);
                        }

                        if (row.distename) {
                            details.push(`<strong>District:</strong> ${row.distename}`);
                        }

                        return details.length > 0 ? details.join('<br>') : '-';
                    }
                },
                {
                    data: 'institution_name',
                    title: 'Institution',
                    className: 'd-none d-md-table-cell lang extra-column text-wrap',
                    render: function(data, type, row) {
                        let displayText = data || '-';
                        if (row._inspectionCount > 1) {
                            displayText +=
                                ` <span class="badge bg-primary">${row._inspectionCount} inspections</span>`;
                        }
                        return displayText;
                    }
                },
                {
                    data: 'audit_period',
                    title: 'Audit Period',
                    className: 'd-none d-md-table-cell lang extra-column text-center',
                    render: function(data, type, row) {
                        if (row._multiplePeriods && row._multiplePeriods.length > 1) {
                            return `<span class="multiple-values" title="${row._multiplePeriods.join(', ')}">Multiple</span>`;
                        }
                        return data || '-';
                    }
                },
                {
                    data: null,
                    title: 'Inspected By',
                    className: 'align-middle text-start',
                    render: function(data, type, row) {
                        if (row._allInspections && row._allInspections.length > 0) {
                            let content = '<div class="inspections-list">';
                            row._allInspections.forEach((inspection, index) => {
                                const inspector = inspection.inspected_by || 'Not Assigned';
                                content += `
                                    <div class="inspection-item ${index > 0 ? 'mt-1 pt-1 border-top' : ''}">
                                        <div class="fw-medium">${inspector}</div>
                                    </div>
                                `;
                            });
                            content += '</div>';
                            return content;
                        } else {
                            const inspector = row.inspected_by || 'Not Assigned';
                            return `<div>${inspector}</div>`;
                        }
                    }
                },
                {
                    data: null,
                    title: 'Start Date',
                    className: 'align-middle text-center',
                    render: function(data, type, row) {
                        if (row._allInspections && row._allInspections.length > 0) {
                            let content = '<div class="dates-list">';
                            row._allInspections.forEach((inspection, index) => {
                                const start = formatDate(inspection.createdon);
                                content += `
                                    <div class="date-item ${index > 0 ? 'mt-1 pt-1 border-top' : ''}">
                                        ${start}
                                    </div>
                                `;
                            });
                            content += '</div>';
                            return content;
                        } else {
                            return `<div>${formatDate(row.createdon)}</div>`;
                        }
                    }
                },
                {
                    data: null,
                    title: 'End Date',
                    className: 'align-middle text-center',
                    render: function(data, type, row) {
                        if (row._allInspections && row._allInspections.length > 0) {
                            let content = '<div class="dates-list">';
                            row._allInspections.forEach((inspection, index) => {
                                const end = formatDate(inspection.updatedon);
                                content += `
                                    <div class="date-item ${index > 0 ? 'mt-1 pt-1 border-top' : ''}">
                                        ${end}
                                    </div>
                                `;
                            });
                            content += '</div>';
                            return content;
                        } else {
                            return `<div>${formatDate(row.updatedon)}</div>`;
                        }
                    }
                },
                {
                    data: null,
                    title: 'Entry Meeting',
                    className: 'align-middle text-center',
                    render: function(data, type, row) {
                        if (row._allInspections && row._allInspections.length > 0) {
                            let content = '<div class="meetings-list">';
                            row._allInspections.forEach((inspection, index) => {
                                const entry = formatDate(inspection.entrymeetdate);
                                content += `
                                    <div class="meeting-item ${index > 0 ? 'mt-1 pt-1 border-top' : ''}">
                                        ${entry}
                                    </div>
                                `;
                            });
                            content += '</div>';
                            return content;
                        } else {
                            return `<div>${formatDate(row.entrymeetdate)}</div>`;
                        }
                    }
                },
                {
                    data: null,
                    title: 'Exit Meeting',
                    className: 'align-middle text-center',
                    render: function(data, type, row) {
                        if (row._allInspections && row._allInspections.length > 0) {
                            let content = '<div class="meetings-list">';
                            row._allInspections.forEach((inspection, index) => {
                                const exit = formatDate(inspection.exitmeetdate);
                                content += `
                                    <div class="meeting-item ${index > 0 ? 'mt-1 pt-1 border-top' : ''}">
                                        ${exit}
                                    </div>
                                `;
                            });
                            content += '</div>';
                            return content;
                        } else {
                            return `<div>${formatDate(row.exitmeetdate)}</div>`;
                        }
                    }
                },
                {
                    data: 'overall_status',
                    title: 'Status',
                    className: 'align-middle text-center',
                    render: function(data, type, row) {
                        if (row._multipleStatuses && row._multipleStatuses.length > 1) {
                            const statusBadges = row._multipleStatuses.map(status => {
                                let badgeClass = 'bg-secondary';
                                if (status === 'Completed') badgeClass = 'bg-success';
                                else if (status === 'Ongoing') badgeClass = 'bg-info';
                                else if (status === 'Pending') badgeClass = 'bg-warning';
                                return `<span class="badge ${badgeClass} me-1">${status}</span>`;
                            }).join('');
                            return `<div class="multiple-statuses">${statusBadges}</div>`;
                        }

                        let badgeClass = 'bg-secondary';
                        let displayText = data || 'Not Scheduled';

                        function isValidDate(dateStr) {
                            if (!dateStr || dateStr === '-' || dateStr === 'NA') return false;
                            const date = new Date(dateStr);
                            return !isNaN(date.getTime());
                        }

                        const hasValidEntryDate = isValidDate(row.entrymeetdate);
                        const hasValidExitDate = isValidDate(row.exitmeetdate);

                        if (data === 'Pending' && hasValidEntryDate && hasValidExitDate) {
                            displayText = 'Not Conducted';
                            badgeClass = 'bg-danger';
                        } else if (data === 'Completed') {
                            badgeClass = 'bg-success';
                        } else if (data === 'Ongoing') {
                            badgeClass = 'bg-info';
                        } else if (data === 'Pending') {
                            badgeClass = 'bg-warning';
                        } else if (data === 'Not Scheduled') {
                            badgeClass = 'bg-secondary';
                        }

                        return `<span class="badge ${badgeClass}">${displayText}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    className: "noExport",
                    render: function(data, type, row) {
                        let buttons = '<div class="action-buttons">';
                        if (row._allInspections && row._allInspections.length > 1) {
                            row._allInspections.forEach((inspection, index) => {
                                const auditinspectionid = inspection.auditinspectionid || inspection.id ||
                                    inspection.inspection_id || inspection.inspectionid || '';
                                const instname = inspection.institution_name || inspection.name ||
                                    inspection.instname || '';

                                if (auditinspectionid) {
                                    buttons += `
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary view-inspection-btn w-100 mb-1"
                                            data-id="${auditinspectionid}"
                                            data-name="${instname.replace(/"/g, '&quot;')}">
                                            <i class="ti ti-eye me-1"></i>View ${index + 1}
                                        </button>
                                    `;
                                }
                            });
                            buttons += '</div>';
                            return buttons;
                        } else {
                            const auditinspectionid = row.auditinspectionid || row.id || row.inspection_id || row
                                .inspectionid || '';
                            const instname = row.institution_name || row.name || row.instname || '';

                            if (auditinspectionid) {
                                return `
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary view-inspection-btn"
                                        data-id="${auditinspectionid}"
                                        data-name="${instname.replace(/"/g, '&quot;')}">
                                        <i class="ti ti-eye me-1"></i>View Details
                                    </button>
                                `;
                            }
                        }

                        return '<button class="btn btn-sm btn-secondary" disabled>N/A</button>';
                    }
                }
            ];

            // Date formatting function
            function formatDate(dateString) {
                if (!dateString || dateString === '-') return '-';
                if (typeof dateString === 'string' && dateString.includes('/')) {
                    return dateString;
                }
                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return '-';
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}/${month}/${year}`;
                } catch (e) {
                    return '-';
                }
            }

            try {
                tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    processing: true,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    fixedColumns: false,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Prev'
                        }
                    },
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: function(d) {
                            return {
                                deptCode: deptCode || (typeof sessiondeptCode !== 'undefined' ?
                                    sessiondeptCode : ''),
                                regionCode: regionCode || '',
                                distCode: distCode || (typeof sessiondistCode !== 'undefined' ?
                                    sessiondistCode : ''),
                                sourceform: sourceForm || '',
                                quarter: quarter || '',
                                viewType: viewType || 'all',
                                financialyearcode: financialyearcode || '',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };
                        },
                        dataSrc: function(json) {
                            if (!json || !json.data) {
                                hideLoading();
                                return [];
                            }

                            let filteredData = json.data;

                            // Filter data based on viewType
                            if (viewType && viewType !== 'all') {
                                filteredData = json.data.filter(function(item) {
                                    const status = item.overall_status || 'Not Scheduled';
                                    switch (viewType) {
                                        case 'completed':
                                            return status === 'Completed';
                                        case 'ongoing':
                                            return status === 'Ongoing';
                                        case 'pending':
                                            return status === 'Pending';
                                        case 'not_scheduled':
                                            return status === 'Not Scheduled';
                                        case 'total':
                                            return true;
                                        default:
                                            return true;
                                    }
                                });
                            }

                            // Group by institution name, region, and district
                            const institutionGroups = new Map();

                            // First pass: group by institution details
                            filteredData.forEach(item => {
                                const key =
                                    `${item.institution_name || ''}-${item.regionename || ''}-${item.distename || ''}`;
                                if (!institutionGroups.has(key)) {
                                    institutionGroups.set(key, []);
                                }
                                institutionGroups.get(key).push(item);
                            });

                            // Second pass: merge inspections for each institution
                            const mergedData = [];

                            institutionGroups.forEach((inspections, key) => {
                                if (inspections.length === 0) return;

                                // Use the first inspection as base
                                const baseInspection = inspections[0];

                                // Add merged metadata
                                baseInspection._inspectionCount = inspections.length;
                                baseInspection._allInspections = inspections;

                                // Collect multiple values if they differ
                                const quarters = [...new Set(inspections.map(i => i.audit_quarter)
                                    .filter(Boolean))];
                                const periods = [...new Set(inspections.map(i => i.audit_period).filter(
                                    Boolean))];
                                const statuses = [...new Set(inspections.map(i => i.overall_status ||
                                    'Not Scheduled'))];

                                if (quarters.length > 1) baseInspection._multipleQuarters = quarters;
                                if (periods.length > 1) baseInspection._multiplePeriods = periods;
                                if (statuses.length > 1) baseInspection._multipleStatuses = statuses;

                                mergedData.push(baseInspection);
                            });

                            // Sort by institution name
                            mergedData.sort((a, b) => (a.institution_name || '').localeCompare(b
                                .institution_name || ''));

                            return mergedData;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', error, thrown);
                            console.error('Response:', xhr.responseText);
                            tableElement.html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: columns,
                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_filter label').addClass('form-label');
                        $('.dt-buttons .btn').css('margin-right', '5px');

                        // Add CSS for styling
                        $('<style>')
                            .text(`
                        .multiple-values {
                            background: #e9ecef;
                            padding: 2px 8px;
                            border-radius: 4px;
                            font-size: 0.875em;
                            cursor: help;
                        }
                        .multiple-statuses .badge {
                            font-size: 0.7em;
                            margin-bottom: 2px;
                        }
                        .inspections-list .inspection-item,
                        .dates-list .date-item,
                        .meetings-list .meeting-item {
                            font-size: 0.875rem;
                        }
                        .border-top {
                            border-color: #dee2e6 !important;
                        }
                        .action-buttons {
                            min-width: 100px;
                        }
                        .view-inspection-btn {
                            font-size: 0.8rem;
                            padding: 0.25rem 0.5rem;
                        }
                    `)
                            .appendTo('head');

                        const dataTable = tableElement.DataTable();
                        if (dataTable && typeof dataTable.columns === 'function') {
                            dataTable.columns.adjust();
                            if (dataTable.responsive && typeof dataTable.responsive.recalc === 'function') {
                                dataTable.responsive.recalc();
                            }
                        }

                        hideLoading();
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.institution_name &&
                            (data.institution_name.toUpperCase().includes('TOTAL') ||
                                data.institution_name.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }

                        $(row).addClass('align-middle');
                        $(row).find('td:last-child').addClass('action-buttons');

                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    drawCallback: function() {
                        const dataTable = tableElement.DataTable();
                        if (dataTable && typeof dataTable.columns === 'function') {
                            dataTable.columns.adjust();
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                        );
                        hideLoading();
                    }
                });

                $('#institutionTableContainer').data('current-region', regionName);
                $('#institutionTableContainer').show();
                visibleTables.institution = true;

                $('html, body').animate({
                    scrollTop: $('#institutionTableContainer').offset().top
                }, 500);

            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }
        }

        function loadlegacyInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            showLoading();
            const quarterElem = document.getElementById('quarter-filter');
            const quarter = quarterElem && quarterElem.value ? quarterElem.value : '';
            const financialyearcode = document.getElementById('year-filter').value || '';

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.slipdetailsTableContainer) {
                $('#slipdetailsTableContainer').hide();
                visibleTables.slipdetailsTableContainer = false;
            }

            if (visibleTables.paraview_details) {
                $('#paraview_details').hide();
                visibleTables.paraview_details = false;
            }


            if (visibleTables.institution && $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let viewTitle;
            if (isDistrictView) {
                viewTitle = `${deptName}: Institution-wise Legacy Report Details for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: Institution-wise Legacy Report Details for ${regionName} Region`;
            } else {
                viewTitle = `${deptName}: Institution-wise Legacy Report Details`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            const columns = [{
                    data: null,
                    title: 'S.No',
                    className: 'text-center align-middle',
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'instename',
                    title: 'Institution',
                    className: 'text-start',
                    render: d => d || '-'
                },
                {
                    data: 'regionname',
                    title: 'Region',
                    className: 'text-start',
                    render: d => d || '-'
                },
                {
                    data: 'districtname',
                    title: 'District',
                    className: 'text-start',
                    render: d => d || '-'
                },
                {
                    data: 'totalcount',
                    title: 'Total Legacy Para(s)',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const count = parseInt(data) || 0;
                        if (count === 0) {
                            return `<span class="count-badge zero">0</span>`;
                        }
                        return `
                            <span class="count-badge total show-slipdetails"
                                data-instid="${row.instid}"
                                data-instename="${row.instename}"
                                data-regionname="${row.regionname}"
                                data-districtname="${row.districtname}"
                                data-count="${count}"
                                data-viewtype="${viewType}"
                                data-flag="ALL">
                                ${count}
                            </span>`;
                    }
                },
                {
                    data: 'finalizecount',
                    title: 'Finalized Legacy Para(s)',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const count = parseInt(data) || 0;
                        if (count === 0) {
                            return `<span class="count-badge zero">0</span>`;
                        }
                        return `
                            <span class="count-badge finalized show-slipdetails"
                                data-instid="${row.instid}"
                                data-instename="${row.instename}"
                                data-regionname="${row.regionname}"
                                data-districtname="${row.districtname}"
                                data-count="${count}"
                                data-viewtype="${viewType}"
                                data-flag="F">
                                ${count}
                            </span>`;
                    }
                },
                {
                    data: 'pendingcount',
                    title: 'Pending Legacy Para(s)',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const count = parseInt(data) || 0;
                        if (count === 0) {
                            return `<span class="count-badge zero">0</span>`;
                        }
                        return `
                            <span class="count-badge pending show-slipdetails"
                                data-instid="${row.instid}"
                                data-instename="${row.instename}"
                                data-regionname="${row.regionname}"
                                data-districtname="${row.districtname}"
                                data-count="${count}"
                                data-viewtype="${viewType}"
                                data-flag="Y">
                                ${count}
                            </span>`;
                    }
                }
            ];

            try {
                tableElement.html(`
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Institution</th>
                            <th>Region</th>
                            <th>District</th>
                            <th>Total Legacy Para(s)</th>
                            <th>Finalized Legacy Para(s)</th>
                            <th>Pending Legacy Para(s)</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right !important;font-weight:bold;background:#b8e1f070;">Total:</th>
                            <th id="totalAuditParaCount" class="text-center fw-bold" style="background:#b8e1f070;">0</th>
                            <th id="finalizeParaCount" class="text-center fw-bold" style="background:#b8e1f070;">0</th>
                            <th id="pendingParaCount" class="text-center fw-bold" style="background:#b8e1f070;">0</th>
                        </tr>
                    </tfoot>
                `);

                // Store filtered data for footer calculations
                let filteredData = [];

                const dt = tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    processing: true,
                    serverSide: false, // Make sure this is false for client-side processing
                    deferRender: true,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: {
                            deptCode: deptCode,
                            regionCode: regionCode,
                            distCode: distCode,
                            sourceform: sourceForm,
                            quarter: quarter,
                            financialyearcode: financialyearcode,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataSrc: function(json) {
                            hideLoading();

                            if (!json || !json.data) {
                                console.warn('No data found in response');
                                filteredData = [];
                                return filteredData;
                            }

                            filteredData = json.data.filter(row => {
                                let shouldShow = false;

                                if (viewType === 'total') {
                                    shouldShow = (parseInt(row.totalcount) || 0) > 0;
                                } else if (viewType === 'pending') {
                                    shouldShow = (parseInt(row.pendingcount) || 0) > 0;
                                } else if (viewType === 'finalize') {
                                    shouldShow = (parseInt(row.finalizecount) || 0) > 0;
                                }

                                return shouldShow;
                            });

                            return filteredData;
                        },
                    },
                    columns: columns,
                    footerCallback: function(row, data, start, end, display) {
                        let total = 0;
                        let finalize = 0;
                        let pending = 0;

                        // Use the filteredData array for calculations
                        filteredData.forEach(r => {
                            total += parseInt(r.totalcount) || 0;
                            finalize += parseInt(r.finalizecount) || 0;
                            pending += parseInt(r.pendingcount) || 0;
                        });

                        $('#totalAuditParaCount').text(total.toLocaleString('en-IN'));
                        $('#finalizeParaCount').text(finalize.toLocaleString('en-IN'));
                        $('#pendingParaCount').text(pending.toLocaleString('en-IN'));
                    },
                    initComplete: function() {
                        $('<style>')
                            .text(`
                                .count-badge{display:inline-flex;align-items:center;justify-content:end;min-width:45px;height:26px;padding:0 15px;border-radius:6px;color:#fff;font-weight:600;font-size:13px;cursor:pointer}
                                .count-badge.total{background:#007bff}
                                .count-badge.pending{background:#dc3545}
                                .count-badge.finalized{background:#28a745}
                                .count-badge.zero{background:#e9ecef;color:#6c757d;cursor:default}
                            `)
                            .appendTo('head');

                        hideLoading();
                    },
                    drawCallback: function(settings) {}
                });

                $('#institutionTableContainer').data('current-region', regionName).show();
                visibleTables.institution = true;

                $('html,body').animate({
                    scrollTop: $('#institutionTableContainer').offset().top
                }, 500);

            } catch (e) {
                console.error(e);
                hideLoading();
            }
        }

        let currentAuditYear = null;

        $(document).on('click', '.show-slipdetails', function() {
            const instid = $(this).data('instid');
            const deptcode = $(this).data('deptcode');
            const instename = $(this).data('instename');
            const regionname = $(this).data('regionname');

            const districtname = $(this).data('districtname');
            const statusflag = $(this).data('flag');

            const audityearcode = currentAuditYear;

            $.ajax({
                url: '/fetch_slipdetails',
                type: 'POST',
                data: {
                    instid,
                    deptcode,
                    audityearcode,
                    statusflag
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        showSlipdetailsTable(instename, regionname, districtname, response.data);
                    } else {
                        passing_alert_value(
                            'Info',
                            'No slip details found',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                    visibleTables.slipdetailsTableContainer = true;

                    $('html, body').animate({
                        scrollTop: $('#slipdetailsTableContainer').offset().top
                    }, 500);
                },
                error: function(xhr) {
                    console.error('Error fetching slipdetails data:', xhr.responseText);
                }
            });
        });

        function showSlipdetailsTable(instename, regionname, districtname, data) {

            $('#slipdetailsTableContainer').show();
            const viewTitle = `Para Details for ${instename}`;
            $('#slipdetailsTitle').text(viewTitle);

            if ($.fn.DataTable.isDataTable('#slipdetailsReportTable')) {
                $('#slipdetailsReportTable').DataTable().clear().destroy();
            }

            if ($('#slipdetailsReportTable').length === 0) {
                console.error('Table element #slipdetailsReportTable not found!');
                return;
            }

            try {

                $('#slipdetailsReportTable').DataTable({
                    data: data,
                    processing: true,
                    serverSide: false,
                    searching: true,
                    info: true,
                    ordering: false,
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    lengthChange: true,
                    pageLength: 10,
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        },
                        filename: function() {
                            return viewTitle.replace(/[^a-zA-Z0-9]/g, '_');
                        }
                    }],
                    initComplete: function() {
                        $("#slipdetailsReportTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>"
                        );
                    },
                    columns: [{
                            data: null,
                            title: 'S. No',
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            className: 'text-center align-middle'
                        },
                        {
                            data: 'slipdetails',
                            title: 'Para Details',
                            render: function(data) {
                                return data ? data : '-';
                            },
                            className: 'text-start wrap-50 align-middle'
                        },
                        {
                            data: 'audit_period',
                            title: 'Audit Period',
                            className: 'text-center align-middle'
                        },
                        {
                            data: null,
                            title: 'Status',
                            render: function(data, type, row) {
                                return `
                                <button type="button"
                                    class="btn btn-sm btn-primary show-allslipdeptails"
                                    data-instid="${row.instid}"
                                    data-followupid="${row.followupid}"
                                    title="View Details">
                                    View Details
                                </button>
                                `;
                            },
                            className: 'text-center align-middle'
                        }
                    ]
                });
            } catch (e) {
                console.error('Error initializing slipdetailsReportTable DataTable', e);
            }
        }

        function hideTable(tableId) {
            $('#' + tableId).hide();
            if (tableId === 'slipdetailsTableContainer') {
                visibleTables.slipdetailsTableContainer = false;
            }
        }

        $(document).on('click', '.show-allslipdeptails', function() {
            const instid = $(this).data('instid');
            const followupid = $(this).data('followupid');

            $.ajax({
                url: '/fetch_allslipdetails',
                type: 'POST',
                data: {
                    instid,
                    followupid
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        const slip = response.data[0];
                        $('#modalInstitutionName').text(slip.instename || '-');

                        const $tbody = $('#slipDetailsTable tbody');
                        $tbody.empty();

                        $tbody.append(`
                            <tr><td style="background-color:#f0f0f0; width:30%;  border:1px solid #999;"><strong >Type of Para</strong></td><td  style="border:1px solid #999;">${slip.typeofparaename || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>State of Para</strong></td><td style="border:1px solid #999;">${slip.stateofparaename || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Para Number</strong></td><td style="border:1px solid #999;">${slip.paranumber || '-'}</td></tr>

                            ${(slip.lastactionyear && slip.lastactionmonth) ? `
                                                                                                <tr>
                                                                                                <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                                                                                    <strong>Last Action Year</strong>
                                                                                                </td>
                                                                                                <td style="border:1px solid #999;">${slip.lastactionyear}</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                                                                                    <strong>Last Action Month</strong>
                                                                                                </td>
                                                                                                <td style="border:1px solid #999;">${slip.lastactionmonth}</td>
                                                                                                </tr>
                                                                                                ` : `
                                                                                                <tr>
                                                                                                <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                                                                                    <strong>Last Action Year</strong>
                                                                                                </td>
                                                                                                <td style="border:1px solid #999;">-</td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                                                                                    <strong>Last Action Month</strong>
                                                                                                </td>
                                                                                                <td style="border:1px solid #999;">-</td>
                                                                                                </tr>
                                                                                                `}


                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Main Objection</strong></td><td style="border:1px solid #999;">${slip.objectionename || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Sub Objection</strong></td><td style="border:1px solid #999;">${slip.subobjectionename || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Amount Involved</strong></td><td style="border:1px solid #999;">${slip.amtinvolved || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Severity</strong></td><td style="border:1px solid #999;"> ${slip.severitycode || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Schema Status</strong></td><td style="border:1px solid #999;">${slip.schemastatus || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Auditee Scheme</strong></td><td style="border:1px solid #999;">${slip.auditeeschemeelname || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities</strong></td><td style="border:1px solid #999;">${slip.irregularitieselname || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities Category</strong></td><td style="border:1px solid #999;">${slip.irregularitiescatelname || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Irregularities Subcategory</strong></td><td style="border:1px solid #999;">${slip.irregularitiessubcatelname || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Para Details</strong></td><td style="border:1px solid #999;">${slip.slipdetails || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Type of Audit</strong></td><td style="border:1px solid #999;">${slip.typeofauditename || '-'}</td></tr>

                        <tr>
                            <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                <strong>Auditor File Upload</strong>
                            </td>
                            <td>
                                ${
                                slip.auditorfileupload
                                    ? slip.auditorfileupload
                                        .split(',')
                                        .map(file => {
                                        const parts = file.split('-');
                                        const fileName = parts[0] || 'Unnamed file';
                                        const filePath = parts[1]
                                            ? `/storage/${parts[1].replace(/\\/g, '/')}` // ✅ fix: proper public path
                                            : '#';
                                        return `<a href="${filePath}" target="_blank" style="text-decoration:none; color:#007bff;">${fileName}</a>`;
                                        })
                                        .join('<br>')
                                    : '-'
                                }
                            </td>
                            </tr>


                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Audit Year</strong></td><td style="border:1px solid #999;">${slip.auditperiod || '-'}</td></tr>
                            <tr><td style="background-color:#f0f0f0; width:30%; border:1px solid #999;"><strong>Created By</strong></td><td style="border:1px solid #999;">${slip.createdbyusername || '-'}</td></tr>
                            <tr>
                            <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                <strong>Updated On</strong>
                            </td>
                            <td style="border:1px solid #999;">
                                ${
                                slip.updatedon
                                    ? (() => {
                                        const d = new Date(slip.updatedon);
                                        const day = String(d.getDate()).padStart(2, '0');
                                        const month = String(d.getMonth() + 1).padStart(2, '0');
                                        const year = d.getFullYear();
                                        return `${day}-${month}-${year}`;
                                    })()
                                    : '-'
                                }
                            </td>
                            </tr>



                                <tr>
                                <td style="background-color:#f0f0f0; width:30%; border:1px solid #999;">
                                    <strong>Liability</strong>
                                </td>
                                <td style="border:1px solid #999;">${slip.liability === 'Y' ? 'Yes' : slip.liability === 'N' ? 'No' : '-'}</td>
                                </tr>


                        ${slip.liability === 'Y' ? `
                                                                                                    <tr>
                                                                                                    <td colspan="2" style="padding:0; border:none;">
                                                                                                        <table class="table table-bordered mt-2 mb-2" style="width:100%; border-collapse:collapse;">
                                                                                                        <thead>
                                                                                                            <tr style="text-align:center;">
                                                                                                            <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Type</th>
                                                                                                            <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Number</th>
                                                                                                            <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Name</th>
                                                                                                            <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Designation</th>
                                                                                                            <th style="background-color:#f0f0f0; border:1px solid #dee2e6;">Amount</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            ${
                                                                                                            slip.liabilitydel
                                                                                                                ? slip.liabilitydel.split(',').map(line => {
                                                                                                                    const parts = line.trim().split('-');
                                                                                                                    return `
                                                <tr>
                                                    <td class="text-center" style="border:1px solid #dee2e6;">
                                                    ${
                                                        parts[0] === '01'
                                                        ? 'EPF No'
                                                        : parts[0] === '02'
                                                        ? 'CPS No'
                                                        : parts[0] === '03'
                                                        ? 'IFHRMS No'
                                                        : (parts[0] || '-')
                                                    }
                                                    </td>
                                                    <td class="text-center" style="border:1px solid #dee2e6;">${parts[1] || '-'}</td>
                                                    <td class="text-center" style="border:1px solid #dee2e6;">${parts[2] || '-'}</td>
                                                    <td class="text-center" style="border:1px solid #dee2e6;">${parts[3] || '-'}</td>
                                                    <td class="text-center" style="border:1px solid #dee2e6; text-align:right;">${parts[4] || '-'}</td>
                                                </tr>`;
                                                                                                                }).join('')
                                                                                                                : `<tr><td colspan="5" style="text-align:center; border:1px solid #dee2e6;">No liability records available</td></tr>`
                                                                                                            }
                                                                                                        </tbody>
                                                                                                        </table>
                                                                                                    </td>
                                                                                                    </tr>
                                                                                                    ` : ''}



                        `);

                        let remarksContent = '';

                        try {
                            if (slip.remarks) {
                                const parsed = JSON.parse(slip.remarks);
                                remarksContent = parsed.content || '';
                            }
                        } catch (e) {
                            // Not JSON, use raw text
                            remarksContent = slip.remarks;
                        }

                        loadckeditorauditor(remarksContent, 'remarksEditor');



                        // Show Modal
                        var modal = new bootstrap.Modal(document.getElementById('slipDetailsModal'));
                        modal.show();
                    } else {
                        alert('No institution data found');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching institution data:', xhr.responseText);
                }
            });
        });

        function loadParaInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            showLoading();
            const quarterElem = document.getElementById('quarter-filter');
            const quarter = (quarterElem && quarterElem.value) ? quarterElem.value : '';
            const financialyearcode = document.getElementById('year-filter').value || '';

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }


            if (visibleTables.paraview_details) {
                $('#paraview_details').hide();
                visibleTables.paraview_details = false;
            }

            if (visibleTables.institution && $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            // Update title based on viewType
            let viewTitle;
            const viewTypeLabels = {
                totalparas: 'Audit Para Details for Total Para(s)',
                processedparas: 'Audit Para Details for Processing Para(s)',
                pendingparas: 'Audit Para Details for Pending Para(s)'
            };

            const viewTypeText = viewTypeLabels[viewType] || 'Audit Para Details';

            if (isDistrictView) {
                viewTitle = `${deptName}: Institution-wise ${viewTypeText} for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: Institution-wise ${viewTypeText} for ${regionName} Region`;
            } else {
                viewTitle = `${deptName}: Institution-wise ${viewTypeText}`;
            }


            $('#institutionTableContainer .card-header h3').text(viewTitle);

            const columns = [{
                    data: null,
                    title: 'S.No',
                    className: 'align-middle text-center',
                    render: function(data, type, row, meta) {
                        return `<span>${meta.row + 1}</span>`;
                    }
                },
                {
                    data: 'instename',
                    title: 'Institution',
                    width: '350px',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    title: 'Region',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        const region = row.regionename || row.regionname || '-';
                        return region;
                    }
                },
                {
                    data: null,
                    title: 'District',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        const district = row.distename || row.districtname || '-';
                        return district;
                    }
                },
                {
                    data: null,
                    title: 'Total Para(s)',
                    className: 'text-wrap text-center',
                    render: function(data, type, row) {
                        let count;
                        if (viewType === 'totalparas') {
                            // Overall total from trans_followup
                            count = parseInt(row.overall_total) || 0;
                        } else if (viewType === 'processedparas') {
                            // Processed total from trans_para
                            count = parseInt(row.processed_total) || 0;
                        } else if (viewType === 'pendingparas') {
                            // Pending = Overall - Processed
                            const overall = parseInt(row.overall_total) || 0;
                            const processed = parseInt(row.processed_total) || 0;
                            count = Math.max(0, overall - processed);
                        }
                        return createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName,
                            viewType, 'total', 'all');
                    }
                },
                {
                    data: null,
                    title: 'Total Pending Para(s)',
                    className: 'text-wrap text-center',
                    render: function(data, type, row) {
                        let count;
                        if (viewType === 'totalparas') {
                            // For overall view: pending = overall_total - processed_total
                            const overall = parseInt(row.overall_total) || 0;
                            const processed = parseInt(row.processed_total) || 0;
                            count = Math.max(0, overall - processed);
                        } else if (viewType === 'processedparas') {
                            // For processed view: use the detailed pending count from trans_para
                            count = parseInt(row.pending) || 0;
                        } else if (viewType === 'pendingparas') {
                            // For pending view: same as total column
                            const overall = parseInt(row.overall_total) || 0;
                            const processed = parseInt(row.processed_total) || 0;
                            count = Math.max(0, overall - processed);
                        }
                        return createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName,
                            viewType, 'pending', 'P');
                    }
                },
                {
                    data: null,
                    title: 'Pending at Auditee',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_auditee) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_auditee', 'U'
                        );
                    }
                },
                {
                    data: null,
                    title: '<abbr title="Para Settlement Auditor">Pending at PSA Auditor</abbr>',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_psa_auditor) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_psa_auditor', 'F'
                        );
                    }
                },
                {
                    data: null,
                    title: '<abbr title="Para Settlement Assistant Director">Pending at PSA AD</abbr>',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_psa_ad) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_psa_ad', 'K'
                        );
                    }
                },

                {
                    data: null,
                    title: 'Dropped Para(s)',
                    className: 'text-wrap text-center',
                    render: function(data, type, row) {
                        let count;
                        if (viewType === 'processedparas') {
                            // Only show dropped count for processed view
                            count = parseInt(row.dropped) || 0;
                        } else {
                            count = 0; // Not applicable for other views
                        }
                        return createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName,
                            viewType, 'dropped', 'A');
                    }
                },
                {
                    data: null,
                    title: 'Rejected Para(s)',
                    className: 'text-wrap text-center',
                    render: function(data, type, row) {
                        let count;
                        if (viewType === 'processedparas') {
                            // Only show rejected count for processed view
                            count = parseInt(row.rejected) || 0;
                        } else {
                            count = 0; // Not applicable for other views
                        }
                        return createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName,
                            viewType, 'rejected', 'I');
                    }
                }
            ];

            try {
                // Create table with proper footer structure
                tableElement.html(`
                    <thead>
                        <tr>
                            <th class="align-middle text-center">S.No</th>
                            <th class="text-wrap text-start">Institution</th>
                            <th class="text-wrap text-start">Region</th>
                            <th class="text-wrap text-start">District</th>
                            <th class="text-wrap text-center">Total Para(s)</th>
                            <th class="text-wrap text-center">Pending Para(s)</th>
                            <th class="text-wrap text-center">Pending at Auditee</th>
                            <th class="text-wrap text-center">Pending at PSA Auditor</th>
                            <th class="text-wrap text-center">Pending at PSA AD</th>
                            <th class="text-wrap text-center">Dropped Para(s)</th>
                            <th class="text-wrap text-center">Rejected Para(s)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right !important; font-weight:bold; background-color: #b8e1f070;">Total:</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="totalParaCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingParaCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingAuditeeCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingPsaAuditorCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingPsaAdCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="droppedParaCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="rejectedParaCount">0</th>
                        </tr>
                    </tfoot>
                `);

                const dataTable = tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    processing: true,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    fixedColumns: false,
                    footerCallback: function(row, data, start, end, display) {
                        if (!data || !Array.isArray(data)) return;

                        let totalSum = 0;
                        let pendingSum = 0;
                        let pendingAuditeeSum = 0;
                        let pendingPsaAuditorSum = 0;
                        let pendingPsaAdSum = 0;
                        let droppedSum = 0;
                        let rejectedSum = 0;

                        data.forEach(row => {
                            if (viewType === 'totalparas') {
                                totalSum += parseInt(row.overall_total) || 0;
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                pendingSum += Math.max(0, overall - processed);
                            } else if (viewType === 'processedparas') {
                                totalSum += parseInt(row.processed_total) || 0;
                                pendingSum += parseInt(row.pending) || 0;
                                pendingAuditeeSum += parseInt(row.pending_auditee) || 0;
                                pendingPsaAuditorSum += parseInt(row.pending_psa_auditor) || 0;
                                pendingPsaAdSum += parseInt(row.pending_psa_ad) || 0;
                                droppedSum += parseInt(row.dropped) || 0;
                                rejectedSum += parseInt(row.rejected) || 0;
                            } else if (viewType === 'pendingparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                totalSum += Math.max(0, overall - processed);
                                pendingSum += Math.max(0, overall - processed);
                            }
                        });

                        $('#totalParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${totalSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingAuditeeCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingAuditeeSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAuditorCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAuditorSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAdCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAdSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#droppedParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${droppedSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#rejectedParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${rejectedSum.toLocaleString('en-IN')}</div>`
                        );
                    },
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Prev'
                        }
                    },
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: function(d) {
                            return {
                                deptCode: deptCode || (typeof sessiondeptCode !== 'undefined' ?
                                    sessiondeptCode : ''),
                                regionCode: regionCode || '',
                                distCode: distCode || (typeof sessiondistCode !== 'undefined' ?
                                    sessiondistCode : ''),
                                sourceform: sourceForm || '',
                                quarter: quarter || '',
                                viewType: viewType,
                                financialyearcode: financialyearcode || '',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };
                        },
                        dataSrc: function(json) {
                            hideLoading();

                            if (!json || !json.data) {
                                console.warn('No data found in response');
                                return [];
                            }

                            let filteredData = json.data.filter(row => {
                                let shouldShow = false;

                                if (viewType === 'totalparas') {
                                    // Show if overall_total > 0
                                    shouldShow = (parseInt(row.overall_total) || 0) > 0;
                                } else if (viewType === 'processedparas') {
                                    // Show if processed_total > 0
                                    shouldShow = (parseInt(row.processed_total) || 0) > 0;

                                } else if (viewType === 'pendingparas') {
                                    // Show if pending count > 0
                                    const overall = parseInt(row.overall_total) || 0;
                                    const processed = parseInt(row.processed_total) || 0;
                                    shouldShow = Math.max(0, overall - processed) > 0;
                                }

                                return shouldShow;
                            });

                            return filteredData;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', error, thrown);
                            console.error('Response:', xhr.responseText);
                            tableElement.html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: columns,
                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_filter label').addClass('form-label');
                        $('.dt-buttons .btn').css('margin-right', '5px');

                        // Add CSS for styling
                        $('<style>')
                            .text(`
                                .multiple-values {
                                    background: #e9ecef;
                                    padding: 2px 8px;
                                    border-radius: 4px;
                                    font-size: 0.875em;
                                    cursor: help;
                                }
                                .multiple-statuses .badge {
                                    font-size: 0.7em;
                                    margin-bottom: 2px;
                                }
                                .inspections-list .inspection-item,
                                .dates-list .date-item,
                                .meetings-list .meeting-item {
                                    font-size: 0.875rem;
                                }
                                .count-badge {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: end;
                                    min-width: 45px;
                                    height: 26px;
                                    padding: 0 15px;
                                    border-radius: 6px;
                                    text-align: center;
                                    color: white;
                                    font-weight: 600;
                                    font-size: 13px;
                                    cursor: pointer;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    transition: all 0.2s ease;
                                }
                                .count-badge:hover {
                                    opacity: 0.8;
                                    transform: translateY(-1px);
                                }
                                .count-badge.total {
                                    background-color: #007bff;
                                }
                                .count-badge.processed {
                                    background-color: #28a745;
                                }
                                .count-badge.pending {
                                    background-color: #dc3545;
                                }
                                .count-badge.dropped {
                                    background-color: #099900;
                                }
                                .count-badge.rejected {
                                    background-color: #ffc107;
                                    color: #212529;
                                }
                                .count-badge.zero {
                                    background-color: #e9ecef;
                                    color: #6c757d;
                                    cursor: default;
                                }
                                .count-badge.zero:hover {
                                    opacity: 1;
                                    transform: none;
                                }
                                tfoot tr {
                                    background-color: #f8f9fa !important;
                                }
                                tfoot th {
                                    padding: 12px 8px;
                                    font-size: 14px;
                                }
                                .table-total-row {
                                    background-color: #e3f2fd !important;
                                    font-weight: bold;
                                }
                                .dataTables_empty {
                                    text-align: center;
                                    padding: 20px;
                                }
                            `)
                            .appendTo('head');

                        const dt = tableElement.DataTable();
                        if (dt && typeof dt.columns === 'function') {
                            dt.columns.adjust();
                            if (dt.responsive && typeof dt.responsive.recalc === 'function') {
                                dt.responsive.recalc();
                            }
                        }

                    $(document)
                        .off('click.institutionBadges')
                        .on('click.institutionBadges', '.count-badge:not(.zero)', function () {

                            if (sourceForm === 'parareport') {

                            const $this = $(this);
                            const instid = $this.data('instid');
                            const count = $this.data('count');
                            const instename = $this.data('instename');
                            const viewtype = $this.data('viewtype');
                            const paraprocesscode = $this.data('paraprocesscode');

                            if (count > 0) {
                                // alert('okok');
                                loadParaDetails(instename, instid, viewtype, paraprocesscode);
                            }

                            }

                        });
                        hideLoading();
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }

                        $(row).addClass('align-middle');
                        $(row).find('td:last-child').addClass('action-buttons');

                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    drawCallback: function() {
                        const dt = tableElement.DataTable();
                        if (dt && typeof dt.columns === 'function') {
                            dt.columns.adjust();
                        }

                        const data = dt.data().toArray();
                        let totalSum = 0;
                        let pendingSum = 0;
                        let pendingAuditeeSum = 0;
                        let pendingPsaAuditorSum = 0;
                        let pendingPsaAdSum = 0;
                        let droppedSum = 0;
                        let rejectedSum = 0;

                        data.forEach(row => {
                            if (viewType === 'totalparas') {
                                // For totalparas view: overall_total and pending = overall - processed
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                totalSum += overall;
                                pendingSum += Math.max(0, overall - processed);
                            } else if (viewType === 'processedparas') {
                                // For processedparas view: processed_total and detailed counts
                                totalSum += parseInt(row.processed_total) || 0;
                                pendingSum += parseInt(row.pending) || 0;
                                pendingAuditeeSum += parseInt(row.pending_auditee) || 0;
                                pendingPsaAuditorSum += parseInt(row.pending_psa_auditor) || 0;
                                pendingPsaAdSum += parseInt(row.pending_psa_ad) || 0;
                                droppedSum += parseInt(row.dropped) || 0;
                                rejectedSum += parseInt(row.rejected) || 0;
                            } else if (viewType === 'pendingparas') {
                                // For pendingparas view: pending = overall - processed
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                const pending = Math.max(0, overall - processed);
                                totalSum += pending;
                                pendingSum += pending;
                            }
                        });

                        $('#totalParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${totalSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingSum.toLocaleString('en-IN')}</div>`

                        );
                        $('#pendingAuditeeCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingAuditeeSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAuditorCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAuditorSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAdCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAdSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#droppedParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${droppedSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#rejectedParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${rejectedSum.toLocaleString('en-IN')}</div>`
                        );

                        // Show message if no data
                        if (data.length === 0) {
                            const message = viewType === 'totalparas' ?
                                'No institutions found with para data.' :
                                `No institutions found with ${viewType} paras.`;
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                        hideLoading();
                    }
                });

                $('#institutionTableContainer').data('current-region', regionName);
                $('#institutionTableContainer').show();
                visibleTables.institution = true;

                $('html, body').animate({
                    scrollTop: $('#institutionTableContainer').offset().top
                }, 500);

            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }
        }

        function loadParaCountInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            showLoading();
            const quarterElem = document.getElementById('quarter-filter');
            const quarter = (quarterElem && quarterElem.value) ? quarterElem.value : '';
            const financialyearcode = document.getElementById('year-filter').value || '';

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.paraview_details) {
                $('#paraview_details').hide();
                visibleTables.paraview_details = false;
            }

            if (visibleTables.institution && $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let viewTitle;
            const viewTypeLabels = {
                totalparas: 'Audit Para Details for Total Para(s)',
                processedparas: 'Audit Para Details for Processing Para(s)',
                pendingparas: 'Audit Para Details for Pending Para(s)'
            };

            const viewTypeText = viewTypeLabels[viewType] || 'Audit Para Details';

            if (isDistrictView) {
                viewTitle = `${deptName}: ${viewTypeText} for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: ${viewTypeText} for ${regionName} Region`;
            } else {
                viewTitle = `${deptName}: ${viewTypeText}`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            const columns = [{
                    data: null,
                    title: 'S.No',
                    className: 'align-middle text-center',
                    render: function(data, type, row, meta) {
                        return `<span>${meta.row + 1}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Region',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        const region = row.regionename || row.regionname || '-';
                        return region;
                    }
                },
                {
                    data: null,
                    title: 'District',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        const region = row.distename || row.districtname || '-';
                        return region;
                    }
                },
                {
                    data: null,
                    title: 'Total Para(s)',
                    className: 'text-wrap text-center',
                    render: function(data, type, row) {
                        let count;
                        if (viewType === 'totalparas') {
                            count = parseInt(row.overall_total) || 0;
                        } else if (viewType === 'processedparas') {
                            count = parseInt(row.processed_total) || 0;
                        } else if (viewType === 'pendingparas') {
                            const overall = parseInt(row.overall_total) || 0;
                            const processed = parseInt(row.processed_total) || 0;
                            count = Math.max(0, overall - processed);
                        }
                        return createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName,
                            viewType, 'total', 'all');
                    }
                },
                {
                    data: null,
                    title: '<abbr title="Para Settlement Auditor">Pending at PSA Auditor</abbr>',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_psa_auditor) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_psa_auditor', 'F'
                        );
                    }
                },
                {
                    data: null,
                    title: '<abbr title="Para Settlement Assistant Director">Pending at PSA AD</abbr>',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_psa_ad) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_psa_ad', 'K'
                        );
                    }
                },

            ];

            try {
                // Create table with proper footer structure
                tableElement.html(`
                    <thead>
                        <tr>
                            <th class="align-middle text-center">S.No</th>
                            <th class="text-wrap text-start">Region</th>
                            <th class="text-wrap text-start">District</th>
                            <th class="text-wrap text-center">Total Para(s)</th>
                            <th class="text-wrap text-center">Pending at PSA Auditor</th>
                            <th class="text-wrap text-center">Pending at PSA AD</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align:right !important; font-weight:bold; background-color: #b8e1f070;">Total:</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="totalParaCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingPsaAuditorCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingPsaAdCount">0</th>
                        </tr>
                    </tfoot>
                `);

                const dataTable = tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    processing: true,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    fixedColumns: false,
                    footerCallback: function(row, data, start, end, display) {
                        if (!data || !Array.isArray(data)) return;

                        let totalSum = 0;
                        let pendingSum = 0;
                        let pendingAuditeeSum = 0;
                        let pendingPsaAuditorSum = 0;
                        let pendingPsaAdSum = 0;
                        let droppedSum = 0;
                        let rejectedSum = 0;

                        data.forEach(row => {
                            if (viewType === 'totalparas') {
                                totalSum += parseInt(row.overall_total) || 0;
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                pendingSum += Math.max(0, overall - processed);
                            } else if (viewType === 'processedparas') {
                                totalSum += parseInt(row.processed_total) || 0;
                                pendingSum += parseInt(row.pending) || 0;
                                pendingAuditeeSum += parseInt(row.pending_auditee) || 0;
                                pendingPsaAuditorSum += parseInt(row.pending_psa_auditor) || 0;
                                pendingPsaAdSum += parseInt(row.pending_psa_ad) || 0;
                                droppedSum += parseInt(row.dropped) || 0;
                                rejectedSum += parseInt(row.rejected) || 0;
                            } else if (viewType === 'pendingparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                totalSum += Math.max(0, overall - processed);
                                pendingSum += Math.max(0, overall - processed);
                            }
                        });

                        $('#totalParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${totalSum.toLocaleString('en-IN')}</div>`
                        );

                        $('#pendingPsaAuditorCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAuditorSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAdCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAdSum.toLocaleString('en-IN')}</div>`
                        );
                    },
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Prev'
                        }
                    },
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: function(d) {
                            return {
                                deptCode: deptCode || (typeof sessiondeptCode !== 'undefined' ? sessiondeptCode : ''),
                                regionCode: regionCode || '',
                                distCode: distCode || (typeof sessiondistCode !== 'undefined' ? sessiondistCode : ''),
                                sourceform: sourceForm || '',
                                quarter: quarter || '',
                                viewType: viewType,
                                financialyearcode: financialyearcode || '',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };
                        },
                        dataSrc: function(json) {
                            hideLoading();

                            if (!json || !json.data) {
                                console.warn('No data found in response');
                                return [];
                            }

                            let filteredData = json.data.filter(row => {
                                let shouldShow = false;

                                if (viewType === 'totalparas') {
                                    // Show if overall_total > 0
                                    shouldShow = (parseInt(row.overall_total) || 0) > 0;
                                } else if (viewType === 'processedparas') {
                                    // Show if processed_total > 0
                                    shouldShow = (parseInt(row.processed_total) || 0) > 0;
                                } else if (viewType === 'pendingparas') {
                                    // Show if pending count > 0
                                    const overall = parseInt(row.overall_total) || 0;
                                    const processed = parseInt(row.processed_total) || 0;
                                    shouldShow = Math.max(0, overall - processed) > 0;
                                }

                                return shouldShow;
                            });

                            return filteredData;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', error, thrown);
                            console.error('Response:', xhr.responseText);
                            tableElement.html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: columns,
                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_filter label').addClass('form-label');
                        $('.dt-buttons .btn').css('margin-right', '5px');

                        // Add CSS for styling
                        $('<style>')
                            .text(`
                                .multiple-values {
                                    background: #e9ecef;
                                    padding: 2px 8px;
                                    border-radius: 4px;
                                    font-size: 0.875em;
                                    cursor: help;
                                }
                                .multiple-statuses .badge {
                                    font-size: 0.7em;
                                    margin-bottom: 2px;
                                }
                                .inspections-list .inspection-item,
                                .dates-list .date-item,
                                .meetings-list .meeting-item {
                                    font-size: 0.875rem;
                                }
                                .count-badge {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: end;
                                    min-width: 45px;
                                    height: 26px;
                                    padding: 0 15px;
                                    border-radius: 6px;
                                    text-align: center;
                                    color: white;
                                    font-weight: 600;
                                    font-size: 13px;
                                    cursor: pointer;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    transition: all 0.2s ease;
                                }
                                .count-badge:hover {
                                    opacity: 0.8;
                                    transform: translateY(-1px);
                                }
                                .count-badge.total {
                                    background-color: #007bff;
                                }
                                .count-badge.processed {
                                    background-color: #28a745;
                                }
                                .count-badge.pending {
                                    background-color: #dc3545;
                                }
                                .count-badge.dropped {
                                    background-color: #099900;
                                }
                                .count-badge.rejected {
                                    background-color: #ffc107;
                                    color: #212529;
                                }
                                .count-badge.zero {
                                    background-color: #e9ecef;
                                    color: #6c757d;
                                    cursor: default;
                                }
                                .count-badge.zero:hover {
                                    opacity: 1;
                                    transform: none;
                                }
                                tfoot tr {
                                    background-color: #f8f9fa !important;
                                }
                                tfoot th {
                                    padding: 12px 8px;
                                    font-size: 14px;
                                }
                                .table-total-row {
                                    background-color: #e3f2fd !important;
                                    font-weight: bold;
                                }
                                .dataTables_empty {
                                    text-align: center;
                                    padding: 20px;
                                }
                            `)
                            .appendTo('head');

                        const dt = tableElement.DataTable();

                          setTimeout(() => {
                        const firstRow = dt.row(0).data();
                        if (firstRow) {
                            $('#institutionTableContainer')
                                .data('current-region', regionName)
                                .data('current-region-code', regionCode)
                                .data('current-dist-code', distCode)
                                .data('current-dist2-code', firstRow.dist2code || '')
                                .data('current-dept-code', deptCode);
                        } else {
                            // If no data, still store the basic filter info
                            $('#institutionTableContainer')
                                .data('current-region', regionName)
                                .data('current-region-code', regionCode)
                                .data('current-dist-code', distCode)
                                .data('current-dist2-code', '')
                                .data('current-dept-code', deptCode);
                        }
                    }, 100);

                        if (dt && typeof dt.columns === 'function') {
                            dt.columns.adjust();
                            if (dt.responsive && typeof dt.responsive.recalc === 'function') {
                                dt.responsive.recalc();
                            }
                        }

                        $(document).off('click.institutionBadges').on('click.institutionBadges',
                            '.count-badge:not(.zero)',
                            function() {
                                const $this = $(this);
                                const count = $this.data('count');
                                const viewtype = $this.data('viewtype');
                                const paraprocesscode = $this.data('paraprocesscode');
                                const regionename = $this.data('regionename');
                                const dist2ename = $this.data('dist2ename');
                                const distcode = $this.data('distcode');
                                const dist2code = $this.data('dist2code');
                                const distename = $this.data('distename');
                                const regiondcode = $this.data('regioncode');
                                const designation = $this.data('designation');

                                const columnIndex = $this.closest('td').index();
                                const columnHeader = $this.closest('table').find('thead th').eq(columnIndex).text().trim();

                                if (count > 0) {
                                    $('#institutionTableContainer')
                                        .data('current-region', regionName)
                                        .data('current-region-code', regiondcode)
                                        .data('current-dist-code', distCode)
                                        .data('current-dist2-code', dist2code)
                                        .data('current-dept-code', deptCode);

                                    loadParaDetailscount(
                                        deptName,
                                        viewtype,
                                        paraprocesscode,
                                        columnHeader,
                                        regionename,
                                        dist2ename,
                                        distename,
                                        designation,
                                        distcode
                                    );
                                }
                            });

                            hideLoading();
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }

                        $(row).addClass('align-middle');
                        $(row).find('td:last-child').addClass('action-buttons');

                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    drawCallback: function() {
                        const dt = tableElement.DataTable();
                        if (dt && typeof dt.columns === 'function') {
                            dt.columns.adjust();
                        }

                        const data = dt.data().toArray();
                        let totalSum = 0;
                        let pendingSum = 0;
                        let pendingAuditeeSum = 0;
                        let pendingPsaAuditorSum = 0;
                        let pendingPsaAdSum = 0;
                        let droppedSum = 0;
                        let rejectedSum = 0;

                        data.forEach(row => {
                            if (viewType === 'totalparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                totalSum += overall;
                                pendingSum += Math.max(0, overall - processed);
                            } else if (viewType === 'processedparas') {
                                totalSum += parseInt(row.processed_total) || 0;
                                pendingSum += parseInt(row.pending) || 0;
                                pendingAuditeeSum += parseInt(row.pending_auditee) || 0;
                                pendingPsaAuditorSum += parseInt(row.pending_psa_auditor) || 0;
                                pendingPsaAdSum += parseInt(row.pending_psa_ad) || 0;
                                droppedSum += parseInt(row.dropped) || 0;
                                rejectedSum += parseInt(row.rejected) || 0;
                            } else if (viewType === 'pendingparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                const pending = Math.max(0, overall - processed);
                                totalSum += pending;
                                pendingSum += pending;
                            }
                        });

                        $('#totalParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${totalSum.toLocaleString('en-IN')}</div>`
                        );

                        $('#pendingPsaAuditorCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAuditorSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAdCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAdSum.toLocaleString('en-IN')}</div>`
                        );

                        // Show message if no data
                        if (data.length === 0) {
                            const message = viewType === 'totalparas' ?
                                'No institutions found with para data.' :
                                `No institutions found with ${viewType} paras.`;
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                        hideLoading();
                    }
                });


                $('#institutionTableContainer').show();
                visibleTables.institution = true;

                $('html, body').animate({
                    scrollTop: $('#institutionTableContainer').offset().top
                }, 500);

            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }
        }

        function loadRetirementParaDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            showLoading();
            const quarterElem = document.getElementById('quarter-filter');
            const quarter = (quarterElem && quarterElem.value) ? quarterElem.value : '';
            const financialyearcode = document.getElementById('year-filter').value || '';

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.paraview_details) {
                $('#paraview_details').hide();
                visibleTables.paraview_details = false;
            }

            if (visibleTables.institution && $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }

            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let viewTitle;
            const viewTypeLabels = {
                total_retirement_paracount: 'Total Retirement Para Details',
                pending_retirement_paracount: 'Pending Retirement Para Details',
                processed_retirement_paracount: 'Processed Retirement Para Details',
            };

            const viewTypeText = viewTypeLabels[viewType] || 'Audit Retirement Para Details';

            if (isDistrictView) {
                viewTitle = `${deptName}: ${viewTypeText} for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: ${viewTypeText} for ${regionName} Region`;
            } else {
                viewTitle = `${deptName}: ${viewTypeText}`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            const columns = [
                {
                    data: null,
                    title: 'S.No',
                    className: 'align-middle text-center',
                    render: function(data, type, row, meta) {
                        return `<span>${meta.row + 1}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Region',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        const region = row.regionename || row.regionname || '-';
                        return region;
                    }
                },
                {
                    data: null,
                    title: 'District',
                    className: 'text-wrap text-start',
                    render: function(data, type, row) {
                        const region = row.distename || row.districtname || '-';
                        return region;
                    }
                },
                {
                    data: null,
                    title: 'Total Retirement Para(s)',
                    className: 'text-wrap text-center',
                    render: function(data, type, row) {
                        let count;
                        if (viewType === 'totalparas') {
                            count = parseInt(row.overall_total) || 0;
                        } else if (viewType === 'processedparas') {
                            count = parseInt(row.processed_total) || 0;
                        } else if (viewType === 'pendingparas') {
                            const overall = parseInt(row.overall_total) || 0;
                            const processed = parseInt(row.processed_total) || 0;
                            count = Math.max(0, overall - processed);
                        }
                        return createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName,
                            viewType, 'total', 'all');
                    }
                },
                {
                    data: null,
                    title: 'Pending Retirement Para(S)',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_psa_auditor) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_psa_auditor', 'F'
                        );
                    }
                },
                {
                    data: null,
                    title: 'Processing Retirement Para(S)',
                    className: 'text-center text-wrap',
                    render: function(data, type, row) {
                        const count = parseInt(row.pending_psa_ad) || 0;
                        return createCountBadge(
                            count,
                            deptCode, regionCode, distCode,
                            row, sourceForm, deptName,
                            viewType, 'pending_psa_ad', 'K'
                        );
                    }
                },

            ];

            try {
                tableElement.html(`
                    <thead>
                        <tr>
                            <th class="align-middle text-center">S.No</th>
                            <th class="text-wrap text-start">Region</th>
                            <th class="text-wrap text-start">District</th>
                            <th class="text-wrap text-center">Total Para(s)</th>
                            <th class="text-wrap text-center">Pending at PSA Auditor</th>
                            <th class="text-wrap text-center">Pending at PSA AD</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align:right !important; font-weight:bold; background-color: #b8e1f070;">Total:</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="totalParaCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingPsaAuditorCount">0</th>
                            <th style="text-align:center; font-weight:bold; background-color: #b8e1f070;" id="pendingPsaAdCount">0</th>
                        </tr>
                    </tfoot>
                `);

                const dataTable = tableElement.DataTable({
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    processing: true,
                    dom: 'Blfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    pagingType: 'full_numbers',
                    fixedColumns: false,
                    footerCallback: function(row, data, start, end, display) {
                        if (!data || !Array.isArray(data)) return;

                        let totalSum = 0;
                        let pendingSum = 0;
                        let pendingAuditeeSum = 0;
                        let pendingPsaAuditorSum = 0;
                        let pendingPsaAdSum = 0;
                        let droppedSum = 0;
                        let rejectedSum = 0;

                        data.forEach(row => {
                            if (viewType === 'totalparas') {
                                totalSum += parseInt(row.overall_total) || 0;
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                pendingSum += Math.max(0, overall - processed);
                            } else if (viewType === 'processedparas') {
                                totalSum += parseInt(row.processed_total) || 0;
                                pendingSum += parseInt(row.pending) || 0;
                                pendingAuditeeSum += parseInt(row.pending_auditee) || 0;
                                pendingPsaAuditorSum += parseInt(row.pending_psa_auditor) || 0;
                                pendingPsaAdSum += parseInt(row.pending_psa_ad) || 0;
                                droppedSum += parseInt(row.dropped) || 0;
                                rejectedSum += parseInt(row.rejected) || 0;
                            } else if (viewType === 'pendingparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                totalSum += Math.max(0, overall - processed);
                                pendingSum += Math.max(0, overall - processed);
                            }
                        });

                        $('#totalParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${totalSum.toLocaleString('en-IN')}</div>`
                        );

                        $('#pendingPsaAuditorCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAuditorSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAdCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAdSum.toLocaleString('en-IN')}</div>`
                        );
                    },
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Prev'
                        }
                    },
                    ajax: {
                        url: 'load_institute_details',
                        type: 'POST',
                        data: function(d) {
                            return {
                                deptCode: deptCode || (typeof sessiondeptCode !== 'undefined' ? sessiondeptCode : ''),
                                regionCode: regionCode || '',
                                distCode: distCode || (typeof sessiondistCode !== 'undefined' ? sessiondistCode : ''),
                                sourceform: sourceForm || '',
                                quarter: quarter || '',
                                viewType: viewType,
                                financialyearcode: financialyearcode || '',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };
                        },
                        dataSrc: function(json) {
                            hideLoading();

                            if (!json || !json.data) {
                                console.warn('No data found in response');
                                return [];
                            }

                            let filteredData = json.data.filter(row => {
                                let shouldShow = false;

                                if (viewType === 'totalparas') {
                                    // Show if overall_total > 0
                                    shouldShow = (parseInt(row.overall_total) || 0) > 0;
                                } else if (viewType === 'processedparas') {
                                    // Show if processed_total > 0
                                    shouldShow = (parseInt(row.processed_total) || 0) > 0;
                                } else if (viewType === 'pendingparas') {
                                    // Show if pending count > 0
                                    const overall = parseInt(row.overall_total) || 0;
                                    const processed = parseInt(row.processed_total) || 0;
                                    shouldShow = Math.max(0, overall - processed) > 0;
                                }

                                return shouldShow;
                            });

                            return filteredData;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', error, thrown);
                            console.error('Response:', xhr.responseText);
                            tableElement.html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: columns,
                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_filter label').addClass('form-label');
                        $('.dt-buttons .btn').css('margin-right', '5px');

                        // Add CSS for styling
                        $('<style>')
                            .text(`
                                .multiple-values {
                                    background: #e9ecef;
                                    padding: 2px 8px;
                                    border-radius: 4px;
                                    font-size: 0.875em;
                                    cursor: help;
                                }
                                .multiple-statuses .badge {
                                    font-size: 0.7em;
                                    margin-bottom: 2px;
                                }
                                .inspections-list .inspection-item,
                                .dates-list .date-item,
                                .meetings-list .meeting-item {
                                    font-size: 0.875rem;
                                }
                                .count-badge {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: end;
                                    min-width: 45px;
                                    height: 26px;
                                    padding: 0 15px;
                                    border-radius: 6px;
                                    text-align: center;
                                    color: white;
                                    font-weight: 600;
                                    font-size: 13px;
                                    cursor: pointer;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    transition: all 0.2s ease;
                                }
                                .count-badge:hover {
                                    opacity: 0.8;
                                    transform: translateY(-1px);
                                }
                                .count-badge.total {
                                    background-color: #007bff;
                                }
                                .count-badge.processed {
                                    background-color: #28a745;
                                }
                                .count-badge.pending {
                                    background-color: #dc3545;
                                }
                                .count-badge.dropped {
                                    background-color: #099900;
                                }
                                .count-badge.rejected {
                                    background-color: #ffc107;
                                    color: #212529;
                                }
                                .count-badge.zero {
                                    background-color: #e9ecef;
                                    color: #6c757d;
                                    cursor: default;
                                }
                                .count-badge.zero:hover {
                                    opacity: 1;
                                    transform: none;
                                }
                                tfoot tr {
                                    background-color: #f8f9fa !important;
                                }
                                tfoot th {
                                    padding: 12px 8px;
                                    font-size: 14px;
                                }
                                .table-total-row {
                                    background-color: #e3f2fd !important;
                                    font-weight: bold;
                                }
                                .dataTables_empty {
                                    text-align: center;
                                    padding: 20px;
                                }
                            `)
                            .appendTo('head');

                        const dt = tableElement.DataTable();

                          setTimeout(() => {
                        const firstRow = dt.row(0).data();
                        if (firstRow) {
                            $('#institutionTableContainer')
                                .data('current-region', regionName)
                                .data('current-region-code', regionCode)
                                .data('current-dist-code', distCode)
                                .data('current-dist2-code', firstRow.dist2code || '')
                                .data('current-dept-code', deptCode);
                        } else {
                            // If no data, still store the basic filter info
                            $('#institutionTableContainer')
                                .data('current-region', regionName)
                                .data('current-region-code', regionCode)
                                .data('current-dist-code', distCode)
                                .data('current-dist2-code', '')
                                .data('current-dept-code', deptCode);
                        }
                    }, 100);

                        if (dt && typeof dt.columns === 'function') {
                            dt.columns.adjust();
                            if (dt.responsive && typeof dt.responsive.recalc === 'function') {
                                dt.responsive.recalc();
                            }
                        }

                        $(document).off('click.institutionBadges').on('click.institutionBadges',
                            '.count-badge:not(.zero)',
                            function() {
                                const $this = $(this);
                                const count = $this.data('count');
                                const viewtype = $this.data('viewtype');
                                const paraprocesscode = $this.data('paraprocesscode');
                                const regionename = $this.data('regionename');
                                const dist2ename = $this.data('dist2ename');
                                const distcode = $this.data('distcode');
                                const dist2code = $this.data('dist2code');
                                const distename = $this.data('distename');
                                const regiondcode = $this.data('regioncode');
                                const designation = $this.data('designation');

                                const columnIndex = $this.closest('td').index();
                                const columnHeader = $this.closest('table').find('thead th').eq(columnIndex).text().trim();

                                if (count > 0) {
                                    $('#institutionTableContainer')
                                        .data('current-region', regionName)
                                        .data('current-region-code', regiondcode)
                                        .data('current-dist-code', distCode)
                                        .data('current-dist2-code', dist2code)
                                        .data('current-dept-code', deptCode);

                                    loadParaDetailscount(
                                        deptName,
                                        viewtype,
                                        paraprocesscode,
                                        columnHeader,
                                        regionename,
                                        dist2ename,
                                        distename,
                                        designation,
                                        distcode
                                    );
                                }
                            });

                            hideLoading();
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }

                        $(row).addClass('align-middle');
                        $(row).find('td:last-child').addClass('action-buttons');

                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    drawCallback: function() {
                        const dt = tableElement.DataTable();
                        if (dt && typeof dt.columns === 'function') {
                            dt.columns.adjust();
                        }

                        const data = dt.data().toArray();
                        let totalSum = 0;
                        let pendingSum = 0;
                        let pendingAuditeeSum = 0;
                        let pendingPsaAuditorSum = 0;
                        let pendingPsaAdSum = 0;
                        let droppedSum = 0;
                        let rejectedSum = 0;

                        data.forEach(row => {
                            if (viewType === 'totalparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                totalSum += overall;
                                pendingSum += Math.max(0, overall - processed);
                            } else if (viewType === 'processedparas') {
                                totalSum += parseInt(row.processed_total) || 0;
                                pendingSum += parseInt(row.pending) || 0;
                                pendingAuditeeSum += parseInt(row.pending_auditee) || 0;
                                pendingPsaAuditorSum += parseInt(row.pending_psa_auditor) || 0;
                                pendingPsaAdSum += parseInt(row.pending_psa_ad) || 0;
                                droppedSum += parseInt(row.dropped) || 0;
                                rejectedSum += parseInt(row.rejected) || 0;
                            } else if (viewType === 'pendingparas') {
                                const overall = parseInt(row.overall_total) || 0;
                                const processed = parseInt(row.processed_total) || 0;
                                const pending = Math.max(0, overall - processed);
                                totalSum += pending;
                                pendingSum += pending;
                            }
                        });

                        $('#totalParaCount').html(
                            `<div class="total" style="margin: 0 auto;">${totalSum.toLocaleString('en-IN')}</div>`
                        );

                        $('#pendingPsaAuditorCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAuditorSum.toLocaleString('en-IN')}</div>`
                        );
                        $('#pendingPsaAdCount').html(
                            `<div class="total" style="margin: 0 auto;">${pendingPsaAdSum.toLocaleString('en-IN')}</div>`
                        );

                        // Show message if no data
                        if (data.length === 0) {
                            const message = viewType === 'totalparas' ?
                                'No institutions found with para data.' :
                                `No institutions found with ${viewType} paras.`;
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                        hideLoading();
                    }
                });


                $('#institutionTableContainer').show();
                visibleTables.institution = true;

                $('html, body').animate({
                    scrollTop: $('#institutionTableContainer').offset().top
                }, 500);

            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }
        }



        function createCountBadge(count, deptCode, regionCode, distCode, row, sourceForm, deptName, viewType, countType, processCode, instid = null, instename = null) {

            const isParaCount = sourceForm === 'paracount';
            const isRetirementParaCount = sourceForm === 'retirementpara';


            if (count <= 0) {
                return `<span class="count-badge zero">${count.toLocaleString('en-IN')}</span>`;
            }

            const badge = document.createElement('span');
            badge.className = `count-badge ${countType}`;
            badge.textContent = count.toLocaleString('en-IN');

            Object.assign(badge.style, {
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'end',
                minWidth: '45px',
                height: '26px',
                padding: '0 15px',
                borderRadius: '6px',
                textAlign: 'center',
                color: 'white',
                backgroundColor: getBadgeColor(countType),
                fontWeight: '600',
                fontSize: '13px',
                cursor: 'pointer',
                boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
                transition: 'all 0.2s ease'
            });

            if (isParaCount) {
                $(badge).attr({
                    'data-count': count,
                    'data-viewtype': viewType,
                    'data-paraprocesscode': processCode,
                    'data-regionename': row.regionename || '',
                    'data-dist2ename': row.dist2ename || '',
                    'data-distename': row.distename || '',
                    'data-dist2code': row.dist2code || '',
                    'data-distcode': row.distcode || '',
                    'data-regioncode': row.regioncode || '',
                    'data-designation': row.designation || ''
                });
            } else {
                // For other tables (with institution data)
                $(badge).attr({
                    'data-instid': instid || row.instid,
                    'data-followupid': row.followupid || '',
                    'data-viewtype': viewType,
                    'data-count': count,
                    'data-instename': instename || row.instename,
                    'data-auditscheduleid': row.auditscheduleid || '',
                    'data-paraprocesscode': processCode || '',
                });
            }

            return badge.outerHTML;
        }

        function getBadgeColor(countType) {
            const colorMap = {
                'total': '#007bff',      // blue
                'pending': '#dc3545',    // red
                'processed': '#28a745',  // green
                'dropped': '#099900',    // dark green
                'rejected': '#ffc107',   // yellow (warning)
                'pending_auditee': '#fd7e14',    // orange
                'pending_psa_auditor': '#6f42c1', // purple
                'pending_psa_ad': '#20c997'      // teal
            };

            return colorMap[countType] || '#007bff'; // default blue
        }

        function getBadgeClass(viewType) {
            const classes = {
                'total': 'total',
                'pending': 'pending',
                'processed': 'processed',
                'dropped': 'dropped',
                'rejected': 'rejected',
                'pending_auditee': 'pending',
                'pending_psa_auditor': 'pending',
                'pending_psa_ad': 'pending'
            };
            return classes[viewType] || 'total';
        }

        function getCountByViewType(row, viewType) {
            switch (viewType) {
                case 'totalparas':
                    return parseInt(row.overall_total) || 0;

                case 'processedparas':
                    return parseInt(row.processed_total) || 0;

                case 'pendingparas':
                    if (viewType === 'processedparas') {
                        return parseInt(row.pending) || 0;
                    } else {
                        const overall = parseInt(row.overall_total) || 0;
                        const processed = parseInt(row.processed_total) || 0;
                        return Math.max(0, overall - processed);
                    }

                default:
                    return parseInt(row.overall_total) || 0;
            }
        }

        function loadParaDetails(instename, instid, viewtype, paraprocesscode) {
            showLoading();

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }


            let instname = instename;

            document.getElementById('paraview_details').style.display = 'block';
            $('#paraview_details').removeClass('hide_this');
            $('.parashowinstname').html(instname);

            showLoading();

            $('#ParaTableShow').removeClass('hide_this').show();
            $('#noParaData').addClass('hide_this');
            $('.cardforparas').show();

            if ($.fn.dataTable.isDataTable('#paraDetailsTable')) {
                $('#paraDetailsTable').DataTable().destroy();
                $('#paraDetailsTable').empty(); // Clear the table content
            }

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: 'paradetails',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    instid: instid,
                    viewtype: viewtype,
                    paraprocesscode: paraprocesscode,
                    _token: csrfToken
                },
                dataType: 'json',
                success: function(response, textStatus, jqXHR) {
                    $('#loader').hide();
                    hideLoading();

                    if (response && response.success) {
                        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                            renderParaDetailsTable(response.data, instname);
                            $('#ParaTableShow').removeClass('hide_this').show();
                            $('#noParaData').addClass('hide_this');
                            $('.cardforparas').show();
                        } else {
                            $('#ParaTableShow').addClass('hide_this');
                            $('.cardforparas').hide();
                            $('#noParaData').removeClass('hide_this');

                            // Clear the table if no data
                            $('#paraDetailsTable').html('');
                        }
                    } else {
                        $('#ParaTableShow').addClass('hide_this');
                        $('#noParaData').removeClass('hide_this');

                        // Clear the table if no success
                        $('#paraDetailsTable').html('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching para data:", error);
                    console.error("Response:", xhr.responseText);

                    $('#loader').hide();
                    hideLoading();
                    $('#ParaTableShow').addClass('hide_this');
                    $('#noParaData').removeClass('hide_this');

                    // PROPERLY DESTROY AND CLEAR ON ERROR
                    if ($.fn.dataTable.isDataTable('#paraDetailsTable')) {
                        $('#paraDetailsTable').DataTable().destroy();
                    }
                    $('#paraDetailsTable').html(
                        '<tr><td colspan="7" align="center">Error loading para data. Please try again.</td></tr>'
                    );

                    if (xhr.status === 419) {
                        alert('Session expired. Please refresh the page and try again.');
                    }
                },
                complete: function() {
                    $('#loader').hide();
                    hideLoading();
                }
            });

            $('html, body').animate({
                scrollTop: $('#paraview_details').offset().top
            }, 500);
        }

        function renderParaDetailsTable(rows, instname) {
            var titleexcel = 'Para Details of ' + instname;

            // Clear and recreate the para table structure
            $('#paraDetailsTable').html(`
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Objection Details</th>
                        <th>Amount Involved</th>
                        <th>Audit Period</th>
                        <th>Severity</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Pending Days</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);

            try {
                // CHECK IF DATATABLE ALREADY EXISTS BEFORE INITIALIZING
                if ($.fn.dataTable.isDataTable('#paraDetailsTable')) {
                    $('#paraDetailsTable').DataTable().destroy();
                    $('#paraDetailsTable').empty();

                    // Recreate the table structure
                    $('#paraDetailsTable').html(`
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Objection Details</th>
                                <th>Amount Involved</th>
                                <th>Audit Period</th>
                                <th>Severity</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Pending Days</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    `);
                }

                var table = $('#paraDetailsTable').DataTable({
                    processing: true,
                    serverSide: false,
                    lengthChange: false,
                    autoWidth: false,
                    responsive: true,
                    data: rows,
                    dom: '<"top"Bfrtip<"clear">>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${titleexcel} - ${getFormattedDate()}`,
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':visible:not(.noExport)'
                        }
                    }],
                    columns: [{
                            data: null,
                            className: "text-left",
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: null,
                            className: "text-wrap text-left",
                            render: function(data, type, row) {
                                return `<div>
                                    <p><b>Main Objection: </b>${row.objectionename || 'N/A'}</p>
                                    <p><b>Sub Objection: </b>${row.subobjectionename || 'N/A'}</p>
                                </div>`;
                            }
                        },
                        {
                            data: "amtinvolved",
                            defaultContent: "0",
                            render: function(data, type, row) {
                                return data ? '₹' + parseFloat(data).toLocaleString('en-IN') : '-';
                            }
                        },
                        {
                            data: "auditperiod",
                            defaultContent: "N/A"
                        },
                        {
                            data: "severityelname",
                            defaultContent: "N/A"
                        },
                        {
                            data: "createddate",
                            defaultContent: "N/A"
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                let badgeClass = 'warning';
                                let statusText = row.processelname || 'Unknown';
                                const code = row.processcode;

                                if (['E', 'U', 'F', 'K'].includes(code)) badgeClass = 'danger';
                                if (code === 'A') badgeClass = 'success';
                                if (code === 'I') badgeClass = 'secondary';

                                return `<span class="mb-1 badge text-bg-${badgeClass}" style="font-size:11px;">
                                    ${statusText}
                                </span>`;
                            }
                        },
                        {
                            data: "pendingdays",
                            defaultContent: "-"
                        },

                        {
                            data: null,
                            className: "noExport",
                            render: function(data, type, row) {
                                const buttons = [];
                                const followupId = row.followupid || '';
                                const isDisabled = row.processcode === 'E' ? 'disabled' : '';

                                // Check Flow button
                                buttons.push(
                                    `<button ${isDisabled} onclick="Open_paracheckflow_model('${followupId}')"
                                        type="button" class="btn-sm btn btn-primary">
                                        <i class="ti ti-history fs-4 me-2"></i> Check Flow
                                    </button><div style="height:5px;"></div>`
                                );

                                // View Details button
                                buttons.push(
                                    `<button ${isDisabled} onclick="open_paraview('${followupId}')"
                                    type="button" class="btn-sm btn btn-secondary">
                                    <i class="ti ti-eye fs-4 me-2"></i> View Details
                                    </button>`
                                );

                                return buttons.join('');
                            }
                        }
                    ],
                    language: {
                        search: "Search:",
                        info: "Showing _START_ to _END_ of _TOTAL_ records",
                        emptyTable: "No para data available"
                    },
                    createdRow: function(row, data, dataIndex) {
                        $(row).addClass('align-middle');
                    },
                    initComplete: function() {
                        $(".dt-button").addClass("btn btn-primary");
                        this.api().columns.adjust();

                        // Force show the table
                        $('#ParaTableShow').show().removeClass('hide_this');
                        $(window).trigger('resize');
                    },
                    // ADD DESTROY CALLBACK TO CLEAN UP
                    destroy: true
                });

            } catch (error) {
                console.error("Error rendering para table:", error);

                // FALLBACK: Display simple table if DataTable fails
                $('#paraDetailsTable').html(`
                    <thead>
                        <tr>
                            <th>S. No</th>
                            <th>Objection Details</th>
                            <th>Amount Involved</th>
                            <th>Auditor</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map((row, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>
                                    <div>
                                        <p><b>Main Objection: </b>${row.objectionename || 'N/A'}</p>
                                        <p><b>Sub Objection: </b>${row.subobjectionename || 'N/A'}</p>
                                    </div>
                                </td>
                                <td>${row.amtinvolved ? '₹' + parseFloat(row.amtinvolved).toLocaleString('en-IN') : '-'}</td>
                                <td>${row.auditorname || 'N/A'}</td>
                                <td>${row.createddate || 'N/A'}</td>
                                <td>
                                    <span class="mb-1 badge text-bg-warning" style="font-size:11px;">
                                        ${row.processelname || 'Unknown'}
                                    </span>
                                </td>
                                <td>
                                    <button onclick="open_paraview('${row.followupid || ''}')"
                                    type="button" class="btn-sm btn btn-secondary">
                                    <i class="ti ti-eye fs-4 me-2"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                `);
            }
            visibleTables.paraview_details = true;

        }

        function loadParaDetailscount(deptName, viewtype, paraprocesscode, columnHeader = '', regionename = '', dist2ename = '', distename = '', designation = '', distcode) {
            showLoading();

            const currentDeptCode = $('#institutionTableContainer').data('current-dept-code') || '';
            const currentRegionCode = $('#institutionTableContainer').data('current-region-code') || '';
            const currentDistCode = $('#institutionTableContainer').data('current-dist-code') || '';
            const currentDist2Code = $('#institutionTableContainer').data('current-dist2-code') || '';

            let parts = [];

            if (deptName) parts.push(`${deptName}:`);

            let location = [];

            if (regionename) location.push(`${regionename} Region`);

            if (dist2ename) {
                location.push(`${dist2ename} District`);
            } else if (distename) {
                location.push(`${distename} District`);
            }

            let instname = '';

            if (parts.length) instname += parts.join(' ') + ' ';

            if (location.length) instname += location.join(', ');

            if (columnHeader) instname += ` – ${columnHeader}`;

            if (!instname) instname = viewtype;

            document.getElementById('paraview_details').style.display = 'block';
            $('#paraview_details').removeClass('hide_this');
            $('.parashowinstname').html(instname);



            $('#ParaTableShow').removeClass('hide_this').show();
            $('#noParaData').addClass('hide_this');
            $('.cardforparas').show();

            if ($.fn.dataTable.isDataTable('#paraDetailsTable')) {
                $('#paraDetailsTable').DataTable().destroy();
                $('#paraDetailsTable').empty();
            }

            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: 'paradetailscount',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    viewtype: viewtype,
                    paraprocesscode: paraprocesscode,
                    deptCode: currentDeptCode,
                    regionCode: currentRegionCode,
                    distCode: distcode,
                    dist2Code: currentDist2Code,
                    _token: csrfToken
                },
                dataType: 'json',
                success: function(response, textStatus, jqXHR) {
                    $('#loader').hide();
                    hideLoading();

                    if (response && response.success) {
                        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                            renderParaDetailscountTable(response.data, instname, columnHeader);
                            $('#ParaTableShow').removeClass('hide_this').show();
                            $('#noParaData').addClass('hide_this');
                            $('.cardforparas').show();
                        } else {
                            $('#ParaTableShow').addClass('hide_this');
                            $('.cardforparas').hide();
                            $('#noParaData').removeClass('hide_this');
                            $('#paraDetailsTable').html('');
                            $('.parashowinstname').html(`${instname} - No data found`);
                        }
                    } else {
                        $('#ParaTableShow').addClass('hide_this');
                        $('#noParaData').removeClass('hide_this');
                        $('#paraDetailsTable').html('');
                        $('.parashowinstname').html(`${instname} - Error loading data`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching para data:", error);
                    console.error("Response:", xhr.responseText);

                    $('#loader').hide();
                    hideLoading();
                    $('#ParaTableShow').addClass('hide_this');
                    $('#noParaData').removeClass('hide_this');
                    $('.parashowinstname').html(`${instname} - Error loading data`);

                    if ($.fn.dataTable.isDataTable('#paraDetailsTable')) {
                        $('#paraDetailsTable').DataTable().destroy();
                    }
                    $('#paraDetailsTable').html(
                        '<tr><td colspan="9" align="center">Error loading para data. Please try again.</td></tr>'
                    );

                    if (xhr.status === 419) {
                        alert('Session expired. Please refresh the page and try again.');
                    }
                },
                complete: function() {
                    $('#loader').hide();
                    hideLoading();
                }
            });

            $('html, body').animate({
                scrollTop: $('#paraview_details').offset().top
            }, 500);
        }

        function renderParaDetailscountTable(data, instname, columnHeader = '') {
            var titleexcel = columnHeader ? ` Audit Para Details  - ${columnHeader}` : ` Audit Para Details  of ${instname}`;

            $('#paraDetailsTable').html(`
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Region</th>
                        <th>District</th>
                        <th>Auditor Name</th>
                        <th>Designation</th>
                        <th>Para Received On</th>
                        <th>Pending Days</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);

            try {
                if ($.fn.dataTable.isDataTable('#paraDetailsTable')) {
                    $('#paraDetailsTable').DataTable().destroy();
                    $('#paraDetailsTable').empty();

                    $('#paraDetailsTable').html(`
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Region</th>
                                <th>District</th>
                                <th>Auditor Name</th>
                                <th>Designation</th>
                                <th>Para Received On</th>
                                <th>Pending Days</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    `);
                }

                var rows = [];

                if (Array.isArray(data)) {
                    data.forEach((auditor, auditorIndex) => {
                        if (auditor.para_details) {
                            let paraDetails = auditor.para_details;
                            if (typeof paraDetails === 'string') {
                                try {
                                    paraDetails = JSON.parse(paraDetails);
                                } catch (e) {
                                    console.error("Error parsing para_details:", e);
                                    paraDetails = [];
                                }
                            }

                            paraDetails.forEach((para, paraIndex) => {
                                rows.push({
                                    sno: rows.length + 1,
                                    auditorRegion: auditor.regionename || "N/A",
                                    auditorDistrict: auditor.dist2ename || auditor.distename || "N/A",
                                    auditorname: auditor.auditorname || "N/A",
                                    designation: auditor.designation || "N/A",
                                    paraReceivedOn: para.received_on || "N/A",
                                    pendingDays: para.pending_days || 0,
                                });
                            });
                        }
                    });
                } else if (data.para_details) {
                    let paraDetails = data.para_details;
                    if (typeof paraDetails === 'string') {
                        try {
                            paraDetails = JSON.parse(paraDetails);
                        } catch (e) {
                            console.error("Error parsing para_details:", e);
                            paraDetails = [];
                        }
                    }

                    paraDetails.forEach((para, index) => {
                        rows.push({
                            sno: index + 1,
                            auditorRegion: auditor.regionename || "N/A",
                            auditorDistrict: data.dist2ename || data.distename || "N/A",
                            auditorname: data.auditorname || "N/A",
                            designation: data.designation || "N/A",
                            paraReceivedOn: para.received_on || "N/A",
                            pendingDays: para.pending_days || 0,
                        });
                    });
                }

                var table = $('#paraDetailsTable').DataTable({
                    processing: true,
                    serverSide: false,
                    lengthChange: true,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    pageLength: 10,
                    autoWidth: false,
                    responsive: true,
                    data: rows,
                    dom: '<"top"Bfrtip<"clear">>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${titleexcel} - ${getFormattedDate()}`,
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    columns: [{
                            data: "sno",
                            className: "text-center align-middle"
                        },
                        {
                            data: "auditorRegion",
                            className: "text-left align-middle",
                            defaultContent: "N/A"
                        },
                        {
                            data: "auditorDistrict",
                            className: "text-left align-middle",
                            defaultContent: "N/A"
                        },
                        {
                            data: "auditorname",
                            className: "text-left align-middle",
                            defaultContent: "N/A"
                        },
                        {
                            data: "designation",
                            className: "text-left align-middle",
                            defaultContent: "N/A"
                        },
                        {
                            data: "paraReceivedOn",
                            className: "text-center align-middle",
                            defaultContent: "N/A",
                            render: function(data) {
                                return data || "N/A";
                            }
                        },
                        {
                            data: "pendingDays",
                            className: "text-center align-middle",
                            defaultContent: "-",
                            render: function(data) {
                                if (!data && data !== 0) return "-";

                                const days = parseInt(data);
                                let colorClass = "";
                                let displayText = days + " day" + (days !== 1 ? "s" : "");

                                if (days >= 30) colorClass = "text-danger fw-bold";
                                else if (days >= 15) colorClass = "fw-bold";
                                else colorClass = "text-success";

                                return `<span class="${colorClass}">${displayText}</span>`;
                            }
                        }
                    ],
                    language: {
                        search: "Search:",
                        info: "Showing _START_ to _END_ of _TOTAL_ records",
                        infoEmpty: "Showing 0 to 0 of 0 records",
                        lengthMenu: "Show _MENU_ entries",
                        emptyTable: "No para data available",
                        zeroRecords: "No matching records found",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    createdRow: function(row, data, dataIndex) {
                        $(row).addClass('align-middle');
                    },
                    initComplete: function() {
                        $(".dt-button").addClass("btn btn-primary");
                        this.api().columns.adjust();

                        // Force show the table
                        $('#ParaTableShow').show().removeClass('hide_this');
                        $(window).trigger('resize');

                        // Update summary
                        const totalRecords = this.api().data().length;
                        $('.parashowinstname').html(`${instname}`);
                    },
                    drawCallback: function(settings) {
                        // Add hover effect
                        $('#paraDetailsTable tbody tr').hover(
                            function() {
                                $(this).css('background-color', '#f8f9fa');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );

                        const api = this.api();
                        const totalRecords = api.data().length;

                        if (totalRecords === 0) {
                            $('#ParaTableShow').addClass('hide_this');
                            $('#noParaData').removeClass('hide_this');
                        }
                    }
                });

            } catch (error) {
                console.error("Error rendering para table:", error);

                // FALLBACK: Display simple table if DataTable fails
                $('#paraDetailsTable').html(`
                    <thead>
                        <tr>
                            <th>S. No</th>
                            <th>District</th>
                            <th>Auditor Name</th>
                            <th>Designation</th>
                            <th>Para Received On</th>
                            <th>Pending Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map((row, index) => `
                        <td>${row.auditorDistrict || 'N/A'}</td>
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row.auditorname || 'N/A'}</td>
                                <td>${row.designation || 'N/A'}</td>
                                <td>${row.paraReceivedOn || 'N/A'}</td>
                                <td>${row.pendingDays || 0} day${row.pendingDays !== 1 ? 's' : ''}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                `);
            }
            visibleTables.paraview_details = true;
        }

        function showSimpleTable(data, instname) {
            let html = `
                <div class="alert alert-info mb-2">
                    <h5>${instname} - Para Details</h5>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S. No</th>
                            <th>District</th>
                            <th>Auditor Name</th>
                            <th>Designation</th>
                            <th>Para Received On</th>
                            <th>Pending Days</th>
                        </tr>
                    </thead>
                    <tbody>`;

            let rowCount = 0;

            if (Array.isArray(data)) {
                data.forEach((auditor) => {
                    if (auditor.para_details) {
                        let paraDetails = auditor.para_details;
                        if (typeof paraDetails === 'string') {
                            try {
                                paraDetails = JSON.parse(paraDetails);
                            } catch (e) {
                                paraDetails = [];
                            }
                        }

                        paraDetails.forEach((para) => {
                            rowCount++;
                            const days = para.pending_days || 0;
                            html += `
                                <tr>
                                    <td>${rowCount}</td>
                                    <td>${auditor.dist2ename || auditor.distename || 'N/A'}</td>
                                    <td>${auditor.auditorname || 'N/A'}</td>
                                    <td>${auditor.designation || 'N/A'}</td>
                                    <td>${para.received_on || 'N/A'}</td>
                                    <td>${days} day${days !== 1 ? 's' : ''}</td>
                                </tr>
                            `;
                        });
                    }
                });
            } else if (data.para_details) {
                // Handle single object case
                let paraDetails = data.para_details;
                if (typeof paraDetails === 'string') {
                    try {
                        paraDetails = JSON.parse(paraDetails);
                    } catch (e) {
                        paraDetails = [];
                    }
                }

                paraDetails.forEach((para, index) => {
                    rowCount++;
                    const days = para.pending_days || 0;
                    html += `
                        <tr>
                            <td>${rowCount}</td>
                            <td>${data.dist2ename || data.distename || 'N/A'}</td>
                            <td>${data.auditorname || 'N/A'}</td>
                            <td>${data.designation || 'N/A'}</td>
                            <td>${para.received_on || 'N/A'}</td>
                            <td>${days} day${days !== 1 ? 's' : ''}</td>
                        </tr>
                    `;
                });
            }

            if (rowCount === 0) {
                html += `
                    <tr>
                        <td colspan="8" class="text-center">No para details found</td>
                    </tr>`;
            }

            html += '</tbody></table>';
            $('#paraDetailsTable').html(html);
        }

        function loadCommencedInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType,
            whichslip) {
            showLoading();
            const quarter = document.getElementById('quarter-filter').value;
            const financialyearcode = document.getElementById('year-filter').value;

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.slipviewDetails) {
                $('#slipview_Details').hide();
                visibleTables.slipviewDetails = false;
            }

            if (visibleTables.institution &&
                $('#institutionTableContainer').data('current-region') === regionName) {
                hideLoading();
                return;
            }
            const isSlipDetails = sourceForm === 'sliptabform';
            const tableElement = $('#institutionTable');
            const isRegionView = regionCode && !distCode;
            const isDistrictView = regionCode && distCode;

            whichslip = whichslip || getWhichSlipValue(viewType);

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            // ===== Title logic =====
            const slipTypeTitleMap = {
                total: 'Total Slips',
                pending: 'Pending Slips',
                converted: 'Converted Slips',
                dropped: 'Dropped Slips'
            };
            const slipTypeTitle = slipTypeTitleMap[viewType] || 'Slip Details';

            let viewTitle;
            if (isDistrictView) {
                viewTitle = `${deptName}: Institution-wise Audit Slip Details for ${regionName} District`;
            } else if (isRegionView) {
                viewTitle = `${deptName}: Institution-wise Audit Slip Details for ${regionName} region`;
            } else {
                viewTitle = `${deptName}: Institution-wise Audit Slip Details`;
            }

            $('#institutionTableContainer .card-header h3').text(viewTitle);

            let columnWidths = [{
                    width: "5%",
                    targets: 0
                },
                {
                    width: "20%",
                    targets: 1
                },
                {
                    width: "30%",
                    targets: 2
                },
                {
                    width: "2%",
                    targets: 3
                },
                {
                    width: "10%",
                    targets: 4
                },
                {
                    width: "6%",
                    targets: 5
                },
                {
                    width: "6%",
                    targets: 6
                },
                {
                    width: "7%",
                    targets: 7
                },
                {
                    width: "7%",
                    targets: 8
                },
                {
                    width: "8%",
                    targets: 9
                },
                {
                    width: "8%",
                    targets: 10
                }, // Total Slips
                {
                    width: "8%",
                    targets: 11
                }, // Pending Slips
                {
                    width: "8%",
                    targets: 12
                }, // Converted Slips
                {
                    width: "8%",
                    targets: 13
                }, // Dropped Slips
                {
                    width: "8%",
                    targets: 14
                } // Status
            ];

            let allColumns = [{
                    data: null,
                    title: 'S.No',
                    className: 'text-left',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'instename',
                    title: 'Institution Name',
                    className: 'text-left',
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: null,
                    title: 'Team Members',
                    className: 'text-left',
                    render: function(data) {
                        const teamHead = data.team_head_en ?
                            `<strong>Team Head:</strong> ${data.team_head_en.replace(' - ', ' <span class="text-muted">-</span> ')}` :
                            'N/A';

                        const members = data.team_members_en ?
                            `<div class="mt-1"><strong>Members:</strong> ${data.team_members_en.replace(/ - /g, ' <span class="text-muted">-</span> ')}</div>` :
                            '';

                        return `
                            <div class="team-info small">
                                ${teamHead}
                                ${members}
                                <div class="mt-1"><strong>Team size:</strong> <span>${data.total_team_count || 0}</span></div>
                            </div>`;
                    }
                },
                {
                    data: 'mandays',
                    title: 'Mandays',
                    className: 'text-center',
                    render: function(data) {
                        return `<span>${data}</span>`;
                    }
                },
                {
                    data: 'regionename',
                    title: 'Region',
                    className: 'text-left',
                    render: function(data) {
                        return `<span class="region-badge">${data}</span>`;
                    }
                },
                {
                    data: 'distename',
                    title: 'District',
                    className: 'text-left',
                    render: function(data) {
                        return `<span class="district-badge">${data}</span>`;
                    }
                },
                {
                    data: 'fromdate',
                    title: 'From Date',
                    className: 'text-left',
                    render: function(data) {
                        return `<span class="date-badge">${data}</span>`;
                    }
                },
                {
                    data: 'todate',
                    title: 'To Date',
                    className: 'text-left',
                    render: function(data) {
                        return `<span class="date-badge">${data}</span>`;
                    }
                },
                {
                    data: 'entrymeet_status',
                    title: 'Entry Meeting',
                    className: 'text-left',
                    render: function(data) {
                        if (!data || data === 'No') {
                            return `<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>`;
                        }
                        return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                    }
                },
                {
                    data: 'exitmeet_status',
                    title: 'Exit Meeting',
                    className: 'text-left',
                    render: function(data) {
                        if (!data || data === 'No') {
                            return `<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>`;
                        }
                        return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                    }
                },
                // ===== ALL SLIP COLUMNS INCLUDED DIRECTLY =====
                {
                    data: 'totalslips',
                    title: 'Total Slips',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type !== 'display') return data;
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? `badge bg-info sliponchange` : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;' :
                            'font-size:13px; background-color:#ebebeb !important; color:#131313 !important; cursor:text;';
                        return `<span class="${badgeClass}" style="${badgeStyle}"
                            ${isPositive ? `data-instname='${row.instename}' data-slipstatus='all' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                            ${data}
                        </span>`;
                    }
                },
                {
                    data: 'pendingslips',
                    title: 'Pending Slips',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type !== 'display') return data;
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? `badge bg-warning sliponchange` : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;' :
                            'font-size:13px; background-color:#ebebeb !important; color:#131313 !important; cursor:text;';
                        return `<span class="${badgeClass}" style="${badgeStyle}"
                            ${isPositive ? `data-instname='${row.instename}' data-slipstatus='P' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                            ${data}
                        </span>`;
                    }
                },
                {
                    data: 'convertedslips',
                    title: 'Converted Slips',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type !== 'display') return data;
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? `badge bg-danger sliponchange` : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;' :
                            'font-size:13px; background-color:#ebebeb !important; color:#131313 !important; cursor:text;';
                        return `<span class="${badgeClass}" style="${badgeStyle}"
                            ${isPositive ? `data-instname='${row.instename}' data-slipstatus='X' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                            ${data}
                        </span>`;
                    }
                },
                {
                    data: 'droppedslips',
                    title: 'Dropped Slips',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type !== 'display') return data;
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? `badge sliponchange` : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer; background-color:#ff4047 !important;' :
                            'font-size:13px; background-color:#ebebeb !important; color:#131313 !important; cursor:text;';
                        return `<span class="${badgeClass}" style="${badgeStyle}"
                            ${isPositive ? `data-instname='${row.instename}' data-slipstatus='A' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                            ${data}
                        </span>`;
                    }
                },
                // ===== Status column always last =====
                {
                    data: 'exitmeet_status',
                    title: 'Status',
                    className: 'text-left',
                    render: d => (d === 'No' ?
                        '<span class="badge bg-warning text-dark">In Progress</span>' :
                        '<span class="badge bg-success">Completed</span>')
                }
            ];

            try {
                const footerRow = $('<tr>');
                allColumns.forEach(col => footerRow.append(`<th class="${col.className || ''}"></th>`));

                tableElement.append($('<tfoot>').append(footerRow));

                const dataTable = tableElement.DataTable({
                    autoWidth: false,
                    columnDefs: columnWidths,
                    processing: true,
                    serverSide: false,
                    dom: '<"top"Bfrtip<"clear">>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${viewTitle} - ${getFormattedDate()}`,
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    responsive: true,
                    paging: true,
                    lengthMenu: [
                        [10, 30, 50, 100],
                        [10, 30, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    ajax: {
                        url: 'load_commenced_institute_details',
                        type: 'POST',
                        data: function(d) {
                            return {
                                deptCode: deptCode || sessiondeptCode || '',
                                regionCode: regionCode,
                                distCode: distCode || sessiondistCode || '',
                                sourceform: sourceForm,
                                quarter: quarter,
                                whichslip: whichslip,
                                draw: d.draw,
                                financialyearcode: financialyearcode,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            };
                        },
                        error: function(xhr, error, thrown) {
                            console.error('AJAX Error:', xhr.responseText);
                            $('#institutionTableContainer').html(
                                '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                            );
                            hideLoading();
                        }
                    },
                    columns: allColumns,
                    footerCallback: function(row, data, start, end, display) {
                        const api = this.api();

                        // Remove existing footer row and rebuild
                        let $tfoot = $(api.table().footer());
                        $tfoot.empty();
                        let $tr = $('<tr>');
                        $tfoot.append($tr);

                        // Find slip column index
                        let columnIndex = -1;
                        api.columns().every(function() {
                            if (this.header().textContent.trim() === slipTypeTitle) {
                                columnIndex = this.index();
                                return false;
                            }
                        });

                        if (columnIndex >= 0) {
                            let total = 0;
                            api.column(columnIndex, {
                                search: 'applied'
                            }).data().each(function(value) {
                                total += parseInt(value) || 0;
                            });

                            // Empty cells before slip column (merged with label)
                            $tr.append(
                                `<th style="text-align: right;" colspan="${columnIndex}">
                                    <strong>Total no. of Slips</strong>
                                </th>`
                            );

                            // Total value under slip column
                            $tr.append(
                                `<th class="text-center">
                                    <span class="badge bg-primary" style="font-size:13px; padding:6px 12px;">
                                        ${total.toLocaleString('en-IN')}
                                    </span>
                                </th>`
                            );

                            // Empty cells after slip column
                            const remainingCols = api.columns().count() - (columnIndex + 1);
                            for (let i = 0; i < remainingCols; i++) {
                                $tr.append('<th></th>');
                            }
                        } else {
                            // If slip column not found, keep aligned empty footer
                            for (let i = 0; i < api.columns().count(); i++) {
                                $tr.append('<th></th>');
                            }
                        }
                    },

                    initComplete: function() {
                        $("#institutionTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");

                        tableElement.addClass('table-hover table-striped align-middle');
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        $('.dataTables_filter input').addClass('form-control form-control-sm');

                        // Highlight the current viewType column
                        highlightViewTypeColumn(viewType);
                        hideLoading();
                    },

                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }
                        $(row).hover(
                            function() {
                                $(this).css('background-color', '#f5f5f5');
                            },
                            function() {
                                $(this).css('background-color', '');
                            }
                        );
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        tableElement.html(
                            '<div class="alert alert-danger">Failed to load data. Please try again.</div>'
                        );
                        hideLoading();
                    }
                });

                dataTable.on('draw', function() {
                    highlightViewTypeColumn(viewType);
                });


            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }

            $('#institutionTableContainer').data('current-region', regionName);
            $('#institutionTableContainer').show();
            visibleTables.institution = true;

            $('html, body').animate({
                scrollTop: $('#institutionTableContainer').offset().top
            }, 500);
        }

        function highlightViewTypeColumn(viewType) {
            const columnTitles = {
                'total': 'Total Slips',
                'pending': 'Pending Slips',
                'converted': 'Converted Slips',
                'dropped': 'Dropped Slips'
            };

            const targetTitle = columnTitles[viewType];
            if (!targetTitle) return;

            // Find the column index by checking the header text
            const headers = $('#institutionTable thead th');
            let columnIndex = -1;

            for (let i = 0; i < headers.length; i++) {
                if ($(headers[i]).text().trim() === targetTitle) {
                    columnIndex = i;
                    break;
                }
            }

            if (columnIndex !== -1) {
                // Remove any existing highlights
                $('#institutionTable thead th').removeClass('bg-primary text-white');
                $('#institutionTable tbody td').removeClass('bg-light');

                // Apply highlight to the specific column
                $(`#institutionTable thead th:nth-child(${columnIndex + 1})`).addClass('bg-primary text-white');
                $(`#institutionTable tbody td:nth-child(${columnIndex + 1})`).addClass('bg-light');
            }
        }


        function loadAuditDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
            showLoading();
            const quarter = document.getElementById('quarter-filter').value;
            const financialyearcode = document.getElementById('year-filter').value || '';

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if (visibleTables.audit &&
                $('#auditReportTableContainer').data('current-region') === regionName &&
                $('#auditReportTableContainer').data('current-viewtype') === viewType) {
                hideLoading();
                return;
            }
            $('#loader').show();

            const tableElement = $('#auditReportTableContainer');

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            const auditTitles = {
                'audit_completed': 'Audit Completed Institution',
                'report_finalized': 'Report Finalized',
                'report_issued': 'Report Issued',
                'pending_finalize': 'Pending to Finalize',
                'pending_issue': 'Pending to Issue'
            };

            // const viewTypeLabel = auditTitles[viewType] || 'Audit Report';


            const headerTitle =
                `${deptName}: Institution-wise Audit Report Details for ${regionName ?  regionName : 'All Regions'}`;
            $('#auditReportTableContainer .card-header h3').text(headerTitle);

            const titleexcel = headerTitle;

            $.ajax({
                url: 'load_auditreport_institutionwise',
                type: 'POST',
                data: {
                    deptCode: deptCode || sessiondeptCode || '',
                    regionCode: regionCode,
                    distCode: distCode || sessiondistCode || '',
                    sourceform: sourceForm,
                    quarter: quarter,
                    viewType: viewType,
                    financialyearcode: financialyearcode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    displayAuditReportData(response.data, titleexcel, deptName, viewType);
                    $('#auditReportTableContainer').data('current-region', regionName);
                    $('#auditReportTableContainer').data('current-viewtype', viewType);
                    hideLoading();
                    $('#loader').hide();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#loader').hide();
                    hideLoading();
                    alert("Failed to fetch audit report details.");
                }
            });
        }

        function displayAuditReportData(data, titleexcel, deptName, viewType) {
            showLoading();
            const tableElement = $('#auditReportTable');

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            if ($.fn.DataTable.isDataTable(tableElement)) {
                tableElement.DataTable().destroy();
                tableElement.empty();
            }

            let filteredData = data;
            if (viewType) {
                filteredData = data.filter(item => {
                    switch (viewType) {
                        case 'audit_completed':
                            return item.audit_completed === 'Completed';
                        case 'report_finalized':
                            return item.report_finalized === 'Finalized';
                        case 'report_issued':
                            return item.report_issued === 'Issued';
                        case 'pending_finalize':
                            return item.pending_finalize === 'Pending Finalization';
                        case 'pending_issue':
                            return item.pending_issue === 'Pending Issue';
                        default:
                            return true;
                    }
                });
            }

            const dataWithSno = filteredData.map((item, index) => ({
                sno: index + 1,
                ...item
            }));

            const allColumns = [{
                    data: 'sno',
                    title: 'S.No',
                    className: 'text-left',
                    orderable: false,
                    width: '5%'
                },
                {
                    data: 'instename',
                    title: 'Institution',
                    className: 'text-left',
                    width: '20%'
                },
                {
                    data: 'regionname',
                    title: 'Region',
                    className: 'text-left',
                    defaultContent: '',
                    width: '8%'
                },
                {
                    data: 'distename',
                    title: 'District',
                    className: 'text-left',
                    defaultContent: '',
                    width: '8%'
                },
                {
                    data: 'issuedon',
                    title: 'Report Issued On',
                    className: 'text-center',
                    visible: viewType === 'report_issued',
                    render: function(data) {
                        if (!data || data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Issued</span>';
                        }
                        return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                    }
                },
                {
                    data: 'entrymeetdate',
                    title: 'Entry Meeting',
                    className: 'text-center',
                    render: function(data) {
                        if (!data || data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>';
                        }
                        return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                    }
                },
                {
                    data: 'exitmeetdate',
                    title: 'Exit Meeting',
                    className: 'text-center',
                    render: function(data) {
                        if (!data || data === 'No') {
                            return '<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>';
                        }
                        return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                    }
                },
                {
                    data: 'audit_completed',
                    title: 'Audit Completed',
                    className: 'text-left',
                    visible: false,
                    render: function(data) {
                        return data || 'Not Completed';
                    }
                },
                {
                    data: 'report_finalized',
                    title: 'Report Finalized',
                    className: 'text-left',
                    visible: false,
                    render: function(data) {
                        return data || 'Not Finalized';
                    }
                },
                {
                    data: 'report_issued',
                    title: 'Report Issued',
                    className: 'text-left',
                    visible: false,
                    render: function(data) {
                        return data || 'Not Issued';
                    }
                },
                {
                    data: 'pending_finalize',
                    title: 'Pending Finalization',
                    className: 'text-left',
                    visible: false,
                    render: function(data) {
                        return data || 'N/A';
                    }
                },
                {
                    data: 'pending_issue',
                    title: 'Pending Issue',
                    className: 'text-left',
                    visible: false,
                    render: function(data) {
                        return data || 'N/A';
                    }
                },
                {
                    data: null,
                    title: 'Status',
                    className: 'text-left',
                    width: '12%',
                    render: function(data, type, row) {
                        switch (viewType) {
                            case 'report_issued':
                                if (row.report_issued === 'Issued' && row.issuedon) {
                                    return '<span class="badge bg-success">Report Issued</span>';
                                } else {
                                    return '<span class="badge bg-warning">Pending Issue</span>';
                                }

                            case 'report_finalized':
                                if (row.report_finalized === 'Finalized') {
                                    return '<span class="badge bg-info">Report Finalized</span>';
                                } else {
                                    return '<span class="badge bg-warning">Not Finalized</span>';
                                }

                            case 'audit_completed':
                                if (row.audit_completed === 'Completed') {
                                    return '<span class="badge bg-primary">Audit Completed</span>';
                                } else {
                                    return '<span class="badge bg-warning">Audit In Progress</span>';
                                }

                            case 'pending_finalize':
                                if (row.pending_finalize === 'Pending Finalization') {
                                    return '<span class="badge bg-warning text-dark">Pending to Finalize</span>';
                                } else {
                                    return '<span class="badge bg-success">Finalized</span>';
                                }

                            case 'pending_issue':
                                if (row.pending_issue === 'Pending Issue') {
                                    return '<span class="badge bg-warning">Pending to Issue</span>';
                                } else {
                                    return '<span class="badge bg-success">Issued</span>';
                                }

                            default:
                                // For default view, show comprehensive status
                                if (row.report_issued === 'Issued' && row.issuedon) {
                                    return '<span class="badge bg-success">Report Issued</span>';
                                } else if (row.report_finalized === 'Finalized') {
                                    return '<span class="badge bg-info">Report Finalized</span>';
                                } else if (row.audit_completed === 'Completed') {
                                    return '<span class="badge bg-primary">Audit Completed</span>';
                                } else if (row.pending_finalize === 'Pending Finalization') {
                                    return '<span class="badge bg-warning text-dark">Pending to Finalize</span>';
                                } else if (row.pending_issue === 'Pending Issue') {
                                    return '<span class="badge bg-warning">Pending to Issue</span>';
                                } else if (row.exitmeetdate) {
                                    return '<span class="badge bg-secondary">Exit Meeting Done</span>';
                                } else if (row.entrymeetdate) {
                                    return '<span class="badge bg-light text-dark">Entry Meeting Done</span>';
                                } else {
                                    return '<span class="badge bg-danger">Not Started</span>';
                                }
                        }
                    },
                    orderable: false
                }
            ];

            try {
                const dataTable = tableElement.DataTable({
                    processing: true,
                    serverSide: false,
                    data: dataWithSno,
                    dom: '<"top"Bfrtip<"clear">>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-download"></i> Download',
                        title: `${titleexcel} - ${getFormattedDate()}`,
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':not(:last-child)' // Export all columns except the main status column
                        }
                    }],
                    columns: allColumns,
                    responsive: true,
                    paging: true,
                    lengthMenu: [
                        [10, 25, 50, 100],
                        [10, 25, 50, 100]
                    ],
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    info: true,
                    initComplete: function(settings, json) {
                        $("#auditReportTable").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");

                        const api = this.api();
                        hideLoading();
                    },
                    createdRow: function(row, data, dataIndex) {
                        if (data.instename &&
                            (data.instename.toUpperCase().includes('TOTAL') ||
                                data.instename.toUpperCase().includes('ALL INSTITUTIONS'))) {
                            $(row).addClass('table-total-row');
                        }
                        $(row).addClass('align-middle');
                    },
                    drawCallback: function(settings) {
                        const api = this.api();
                    }
                });

            } catch (e) {
                console.error('DataTables initialization error:', e);
                tableElement.html(
                    '<div class="alert alert-danger">Failed to initialize table. Please check console for details.</div>'
                );
                hideLoading();
            }

            $('#auditReportTableContainer').show();
            visibleTables.audit = true;

            $('html, body').animate({
                scrollTop: $('#auditReportTableContainer').offset().top
            }, 500);
        }

        // --- Slip Details Table ---
        $(document).on('click', '.sliponchange', function(e) {
            e.preventDefault();
            var auditscheduleid = $(this).data('auditscheduleid');
            var slipsts = $(this).data('slipstatus');
            var instname = $(this).data('instname');

            if (window.isDirectRegionTableClick && visibleTables.district) {
                $('#districtTableContainer').hide();
                visibleTables.district = false;
            }

            document.getElementById('slipview_Details').style.display = 'block';
            $('#slipview_Details').removeClass('hide_this');
            $('.slipshowinstname').html(instname);

            var sourceform = $(this).data('sourceform');
            var plantab = $('select[name="quarter"]').val();
            var sliptab = $('select[name="slipquarter"]').val();
            const quarter = document.getElementById('quarter-filter').value;
            const financialyearcode = document.getElementById('year-filter').value || '';

            if (sourceform === 'sliptabform') {
                defaultQuarter = plantab;
            } else {
                defaultQuarter = sliptab;
            }

            showLoading();

            $('#slipdetails_Table thead').show();
            $('#Sliptableshow').removeClass('hide_this').show();
            $('#noSlipData').addClass('hide_this');
            $('.cardforslips').show();

            if ($.fn.dataTable.isDataTable('#slipdetails_Table')) {
                $('#slipdetails_Table').DataTable().destroy();
                $('#slipdetails_Table').empty();
            }

            $.ajax({
                url: "/getpendingparadetails",
                type: "POST",
                data: {
                    auditscheduleid: auditscheduleid,
                    quartercode: quarter,
                    slipsts: slipsts,
                    filterapply: 'all',
                    quarter: quarter,
                    financialyearcode: financialyearcode || ''
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#loader').show();
                },
                success: function(data, textStatus, jqXHR) {
                    if (jqXHR.status === 200 && data.success) {

                        if (data.message) {
                            $('#Sliptableshow').addClass('hide_this');
                            $('.cardforslips').hide();
                            $('#noSlipData').removeClass('hide_this');
                            return;
                        }

                        let tableData = [];
                        if (data.data && data.data.data && Array.isArray(data.data.data) && data
                            .data
                            .data.length > 0) {
                            tableData = data.data.data;
                        } else if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                            tableData = data.data;
                        } else if (Array.isArray(data) && data.length > 0) {
                            tableData = data;
                        }

                        if (tableData.length > 0) {
                            renderTable(tableData, instname);
                            $('#Sliptableshow').removeClass('hide_this').show();
                            $('#noSlipData').addClass('hide_this');
                            $('.cardforslips').show();
                        } else {
                            $('#Sliptableshow').addClass('hide_this');
                            $('.cardforslips').hide();
                            $('#noSlipData').removeClass('hide_this');
                        }

                    } else {
                        $('#Sliptableshow').addClass('hide_this');
                        $('#noSlipData').removeClass('hide_this');
                    }
                },
                error: function(error) {
                    console.error("Error fetching data:", error);
                    $('#loader').hide();
                    hideLoading();
                    $('#Sliptableshow').addClass('hide_this');
                    $('#noSlipData').removeClass('hide_this');

                    if ($.fn.dataTable.isDataTable('#slipdetails_Table')) {
                        $('#slipdetails_Table').DataTable().destroy();
                    }
                    $('#slipdetails_Table').html(
                        '<tr><td colspan="8" align="center">Error loading data. Please try again.</td></tr>'
                    );
                },
                complete: function() {
                    $('#loader').hide();
                    hideLoading();
                }
            });

            $('html, body').animate({
                scrollTop: $('#slipview_Details').offset().top
            }, 500);
        });

        function renderTable(rows, instname) {
            var titleexcel = 'Audit Slip Details of ' + instname;

            if ($.fn.dataTable.isDataTable('#slipdetails_Table')) {
                $('#slipdetails_Table').DataTable().destroy();
                $('#slipdetails_Table').empty();
            }

            $('#slipdetails_Table').html(`
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Slip No</th>
                        <th>Objection Details</th>
                        <th>Team Head</th>
                        <th>Auditor</th>
                        <th>Created</th>
                        <th>Slip Created Quarter</th>
                        <th>Status</th>
                        <th class="noExport">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `);

            var table = $('#slipdetails_Table').DataTable({
                processing: true,
                serverSide: false,
                lengthChange: false,
                autoWidth: false,
                responsive: true,
                data: rows,
                dom: '<"top"Bfrtip<"clear">>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-download"></i> Download',
                    title: `${titleexcel} - ${getFormattedDate()}`,
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
                        format: {
                            body: function(data, row, column, node) {
                                if (data) {
                                    var div = document.createElement('div');
                                    div.innerHTML = data;
                                    var text = div.textContent || div.innerText || '';

                                    text = text.replace(/\s+/g, ' ').trim();

                                    return text;
                                }
                                return data;
                            }
                        }
                    }
                }],
                columns: [{
                        data: null,
                        className: "text-left",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: "mainslipnumber",
                        defaultContent: "N/A"
                    },
                    {
                        data: null,
                        className: "text-wrap text-left",
                        render: function(data, type, row) {
                            if (type === 'export') {
                                return `Main Objection: ${row.objectionename || 'N/A'}\nSub Objection: ${row.subobjectionename || 'N/A'}`;
                            }
                            return `<p><b>Main Objection: </b>${row.objectionename || 'N/A'}</p>
                                    <p><b>Sub Objection: </b>${row.subobjectionename || 'N/A'}</p>`;
                        }
                    },
                    {
                        data: "teamheadname",
                        defaultContent: "N/A"
                    },
                    {
                        data: "auditorname",
                        defaultContent: "N/A"
                    },
                    {
                        data: "createddate",
                        defaultContent: "N/A",
                        width: "15%",
                        className: "text-center",
                        render: function(data, type, row) {
                            if (data && data !== "N/A") {
                                const dateParts = data.split(' ');
                                const dateOnly = dateParts[0];
                                const dateComponents = dateOnly.split('-');

                                const month = parseInt(dateComponents[1]);

                                let quarter;
                                if ([4, 5, 6].includes(month)) quarter = "Q1";
                                else if ([7, 8, 9].includes(month)) quarter = "Q2";
                                else if ([10, 11, 12].includes(month)) quarter = "Q3";
                                else if ([1, 2, 3].includes(month)) quarter = "Q4";
                                else quarter = "N/A";

                                if (type === 'export') {
                                    return data;
                                }

                                return `<div class="mb-1">${data}</div>
                                        <div><span class="badge bg-primary">Slip Created in Quarter: ${quarter}</span></div>`;
                            }
                            return "N/A";
                        }
                    },
                    {
                        data: "createddate",
                        defaultContent: "N/A",
                        className: "text-center",
                        visible: false,
                        render: function(data, type, row) {
                            if (data && data !== "N/A") {
                                const dateParts = data.split(' ');
                                const dateOnly = dateParts[0];
                                const dateComponents = dateOnly.split('-');

                                const month = parseInt(dateComponents[1]);

                                let quarter;
                                if ([4, 5, 6].includes(month)) quarter = "Q1";
                                else if ([7, 8, 9].includes(month)) quarter = "Q2";
                                else if ([10, 11, 12].includes(month)) quarter = "Q3";
                                else if ([1, 2, 3].includes(month)) quarter = "Q4";
                                else quarter = "N/A";

                                return quarter;
                            }
                            return "N/A";
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (type === 'export') {
                                const status = row.processcode === 'A' ? 'Dropped Slip' : row
                                    .processelname || 'Unknown';
                                const quarter = row.auditquartercode || 'N/A';
                                return `${status} | ${quarter}`;
                            }

                            return `
                                <span class="mb-1 badge text-bg-${row.processcode === 'A' ? 'success' : row.processcode === 'X' ? 'danger' : 'warning'}" style="font-size:11px;">
                                    ${row.processcode === 'A' ? 'Dropped Slip' : row.processelname || 'Unknown'}
                                </span>
                                <span class="mb-1 badge text-bg-${row.auditquartercode === 'Q4' ? 'primary' : row.auditquartercode === 'Q3' ? 'secondary' : row.auditquartercode === 'Q2' ? 'info' : 'secondary'}" style="font-size:11px;">
                                    ${row.auditquartercode || 'N/A'}
                                </span>`;
                        }
                    },
                    {
                        data: null,
                        className: "noExport",
                        render: function(data, type, row) {
                            const buttons = [];
                            const auditSlipId = row.auditslipid || '';
                            const mainSlipNumber = row.mainslipnumber || '';

                            // Check Flow button
                            if (row.processcode !== 'E') {
                                buttons.push(
                                    `<button onclick="Open_checkflow_model('${auditSlipId}','${mainSlipNumber}')"
                                    type="button" class="btn-sm btn btn-primary">
                                    <i class="ti ti-history fs-4 me-2"></i> Check Flow
                                </button><div style="height:5px;"></div>`
                                );
                            }

                            // View Details button
                            const isDisabled = row.processcode === 'E' ? 'disabled' : '';
                            buttons.push(
                                `<button ${isDisabled} onclick="Open_viewmodel('${auditSlipId}','${mainSlipNumber}')"
                                type="button" class="btn-sm btn btn-secondary">
                                <i class="ti ti-eye fs-4 me-2"></i> View Details
                            </button>`
                            );

                            return buttons.join('');
                        }
                    }
                ],
                language: {
                    search: "Search:",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    emptyTable: "No slip data available"
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('align-middle');
                },
                initComplete: function() {
                    $(".dt-button").addClass("btn btn-primary");
                    this.api().columns.adjust();
                }
            });
            visibleTables.slipviewDetails = true;
        }

        // --- Detail View Functions ---
        function showDetailView(department) {
            currentDepartment = department;
            updateDetailView();
        }

        function updateDetailView() {
            try {
                const detailViewTitle = document.getElementById('detail-view-title');
                const table = document.getElementById('detail-table');
                const departmentCard = document.getElementById('department-card');
                const detailCard = document.getElementById('detail-card');

                if (!detailViewTitle || !table || !departmentCard || !detailCard) {
                    throw new Error('Required DOM elements not found');
                }

                if (!dashboardData || typeof dashboardData !== 'object') {
                    throw new Error('Dashboard data not loaded');
                }

                currentViewType = currentViewType || 'regions';

                if (!dashboardData[currentViewType]) {
                    throw new Error(`No data available for view type: ${currentViewType}`);
                }

                const data = dashboardData[currentViewType];
                const department = currentDepartment;

                let departmentName = department;
                try {
                    const deptFilter = document.getElementById('department-filter');
                    if (deptFilter) {
                        const selectedOption = deptFilter.querySelector(`option[value="${department}"]`);
                        if (selectedOption) {
                            const parts = selectedOption.textContent.split(' - ');
                            departmentName = parts[parts.length - 1] || department;
                        }
                    }
                } catch (e) {
                    console.warn('Could not get department name:', e);
                }

                let rawTitle = data.title || 'Audit Details';
                let viewTypeLabel = rawTitle;

                if (/Region-wise/i.test(rawTitle)) {
                    viewTypeLabel = 'Region-wise Audit Details';
                } else if (/District-wise/i.test(rawTitle)) {
                    viewTypeLabel = 'District-wise Audit Details';
                } else if (/Institution-wise/i.test(rawTitle)) {
                    viewTypeLabel = 'Institution-wise Audit Details';
                } else {
                    viewTypeLabel = 'Audit Details';
                }

                detailViewTitle.textContent = `${departmentName} - ${viewTypeLabel}`;
                const titleexcel = `${departmentName} - ${viewTypeLabel}`;

                if ($.fn.DataTable.isDataTable(table)) {
                    try {
                        $(table).DataTable().destroy();
                        table.innerHTML = '';
                    } catch (dtError) {
                        console.warn('Failed to destroy existing DataTable:', dtError);
                    }
                }

                table.innerHTML = '';

                if (!Array.isArray(data.tableHeaders) || !Array.isArray(data.tableData)) {
                    throw new Error('Invalid table data structure');
                }

                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                data.tableHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header || '';
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                data.tableData.forEach((rowData, index) => {
                    const row = document.createElement('tr');

                    if (rowData.some(cell => cell === department || cell === departmentName)) {
                        row.classList.add('highlight-row');
                    }

                    const cells = data.tableHeaders.length === rowData.length ?
                        rowData : [index + 1, ...rowData].slice(0, data.tableHeaders.length);

                    cells.forEach(cellData => {
                        const cell = row.insertCell();
                        cell.textContent = cellData !== undefined ? cellData : '';
                    });

                    tbody.appendChild(row);
                });
                table.appendChild(tbody);

                try {
                    $(table).DataTable({
                        dom: 'Bfrtip',
                        buttons: [{
                            extend: 'excel',
                            text: '<i class="fas fa-download"></i> Download',
                            title: `${titleexcel} - ${getFormattedDate()}`,
                            className: 'btn btn-success'
                        }],
                        responsive: true,
                        paging: true,
                        pageLength: 10,
                        searching: true,
                        ordering: true,
                        info: true,
                        destroy: true
                    });
                } catch (dtError) {
                    console.error('DataTables initialization failed:', dtError);
                    table.classList.add('basic-table');
                }

                if (typeof updateDetailChart === 'function') {
                    try {
                        updateDetailChart();
                    } catch (chartError) {
                        console.error('Failed to update chart:', chartError);
                    }
                }

                departmentCard.style.display = 'none';
                detailCard.style.display = 'block';

            } catch (error) {
                console.error('Error in updateDetailView:', error);

                const errorMessage =
                    `
                        <div class="alert alert-danger">
                            <h4>Error Loading Data</h4>
                            <p>${error.message}</p>
                            <button onclick="location.reload()" class="btn btn-sm btn-secondary">
                                Try Again
                            </button>
                        </div>
                        `;

                const detailCard = document.getElementById('detail-card');
                if (detailCard) {
                    detailCard.innerHTML = errorMessage;
                    detailCard.style.display = 'block';
                }

                const departmentCard = document.getElementById('department-card');
                if (departmentCard) {
                    departmentCard.style.display = 'none';
                }
            }
        }

        function updateDetailChart() {
            const data = dashboardData[currentViewType];
            const selectedDept = currentDepartment;
            const deptIndex = data.departments.findIndex(dept => dept.name === selectedDept);

            if (!chart) {
                createDetailChart(selectedDept);
                return;
            }

            const currentType = chart.config.type;

            const backgroundColors = data.chart.datasets[0].backgroundColor.map((color, index) =>
                index === deptIndex ? color : `${color.replace(')', ', 0.2)').replace('rgb', 'rgba')}`
            );
            const borderColors = data.chart.datasets[0].backgroundColor.map((color, index) =>
                index === deptIndex ? color : `${color.replace(')', ', 0.2)').replace('rgb', 'rgba')}`
            );

            chart.data.labels = data.chart.labels;
            chart.data.datasets[0].data = data.chart.datasets[0].data;
            chart.data.datasets[0].backgroundColor = backgroundColors;
            chart.data.datasets[0].borderColor = borderColors;

            chart.update();
        }

        function createDetailChart(selectedDept) {
            const data = dashboardData[currentViewType];
            const ctx = document.getElementById('detail-chart').getContext('2d');

            if (data.chart.datasets[0].data.length === 0) {
                document.getElementById('detail-chart').parentElement.innerHTML =
                    '<div class="no-data">No chart data available for the selected filters</div>';
                return;
            }

            const deptIndex = data.departments.findIndex(dept => dept.name === selectedDept);
            const backgroundColors = data.chart.datasets[0].backgroundColor.map((color, index) =>
                index === deptIndex ? color : `${color}20`
            );
            const borderColors = data.chart.datasets[0].backgroundColor.map((color, index) =>
                index === deptIndex ? color : '#ddd'
            );

            let chartType = 'bar';
            const activeButton = document.querySelector('.chart-toggle-buttons .btn.active');
            if (activeButton) {
                chartType = activeButton.getAttribute('data-chart-type');
            }

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: data.chart.labels,
                    datasets: [{
                        ...data.chart.datasets[0],
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: getChartOptions(data, deptIndex, chartType)
            });

            document.querySelectorAll('.chart-toggle-buttons button').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.chart-toggle-buttons button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    const newChartType = this.getAttribute('data-chart-type');
                    chart.destroy();
                    chart = new Chart(ctx, {
                        type: newChartType,
                        data: {
                            labels: data.chart.labels,
                            datasets: [{
                                ...data.chart.datasets[0],
                                backgroundColor: backgroundColors,
                                borderColor: borderColors,
                                borderWidth: 1
                            }]
                        },
                        options: getChartOptions(data, deptIndex, newChartType)
                    });
                });
            });
        }

        function getChartOptions(data, deptIndex, chartType) {
            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Region-wise Audit Plan Details',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: chartType === 'pie'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = Math.round((value / total) * 100);
                                return `${context.label}: ${value}`;
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    elements: {
                        bar: {
                            borderWidth: 2,
                            minBarLength: 20 // Minimum pixel length for bars
                        }
                    }
                },
                onClick: (evt, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const deptName = data.departments[index].name;

                        showDetailView(deptName);

                        if (selectedDepartmentCard) {
                            selectedDepartmentCard.classList.remove('selected');
                        }
                        selectedDepartmentCard = document.getElementById(`dept-${deptName}`);
                        selectedDepartmentCard.classList.add('selected');
                    }
                }
            };

            if (chartType === 'bar' || chartType === 'line') {
                baseOptions.scales = {
                    y: {
                        type: 'logarithmic',
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                };
            }

            return baseOptions;
        }

        function getMultiChartOptions(chartType, title) {
            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title,
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: chartType === 'pie' || chartType === 'doughnut',
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.raw;
                                return label;
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    elements: {
                        bar: {
                            borderWidth: 2,
                            minBarLength: 20 // Minimum pixel length for bars
                        }
                    }
                }
            };

            if (chartType === 'bar' || chartType === 'line') {
                baseOptions.scales = {
                    y: {
                        type: 'logarithmic',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Regions'
                        }
                    }
                };
            }

            return baseOptions;
        }

        // --- Event Listeners and DOM Ready ---
        document.addEventListener('DOMContentLoaded', function() {

            fetch('/clear_cache', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOoken': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                // console.log('Dashboard cache cleared on page load');
            }).catch(err => {
                // console.log('Cache clear failed:', err);
            });

            // View toggle functionality
            const toggleButton = document.getElementById('view-toggle');
            const tableContainer = document.querySelector('.table-container');
            const chartContainer = document.querySelector('.chart-container');

            tableContainer.style.display = 'block';
            chartContainer.style.display = 'none';
            toggleButton.textContent = 'View Chart';

            toggleButton.addEventListener('click', function() {
                const isTableVisible = tableContainer.style.display === 'block';

                if (isTableVisible) {
                    tableContainer.style.display = 'none';
                    chartContainer.style.display = 'block';
                    toggleButton.textContent = 'View as Table';
                } else {
                    tableContainer.style.display = 'block';
                    chartContainer.style.display = 'none';
                    toggleButton.textContent = 'View Chart';
                }
            });

            const applyBtn = document.getElementById('apply-filters-btn');
            // const refreshBtn = document.getElementById('refresh-btn');

            if (applyBtn) {
                applyBtn.addEventListener('click', applyFilters);
            }

            // if (refreshBtn) {
            //     refreshBtn.addEventListener('click', resetFilters);
            // }
            const filters = [
                'audit-program-filter',
                'year-filter',
                'quarter-filter',
                'department-filter'
            ];

            filters.forEach(filterId => {
                const filterElement = document.getElementById(filterId);
                if (filterElement) {
                    filterElement.addEventListener('change', function() {
                        checkFiltersChanged();
                    });
                }
            });

            if (applyBtn) applyBtn.disabled = true;
            // if (refreshBtn) refreshBtn.disabled = true;
            loadFilterOptions().then(() => {
                loadDashboardData();
            });
        });

        // document.getElementById('refresh-btn').disabled = true;
        const filterControls = [
            'audit-program-filter',
            'year-filter',
            'quarter-filter',
            'department-filter'
        ];

        filterControls.forEach(id => {
            document.getElementById(id).addEventListener('change', function() {
                checkFiltersChanged();
            });
        });
</script>

@endsection
