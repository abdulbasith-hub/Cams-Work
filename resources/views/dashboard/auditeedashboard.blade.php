@section('content')
    @extends('index2')
    @include('common.alert')
@section('title', $usertypecode == 'H' ? 'HOD Dashboard' : 'Auditee Dashboard')


<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>


<script src="../common/ajaxfn.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/js/chart.umd.js"></script>
{{-- <script src="../assets/js/ckeditor.js"></script> --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>

@php
    $sessionchargedel = session('charge');
    // Initialize with default values to prevent errors
    $session_deptcode = $sessionchargedel->deptcode ?? null;
    $session_auditeedeptcode = $sessionchargedel->auditeedeptcode ?? null;
    $sessionroletypecode = $sessionchargedel->roletypecode ?? null;
    $sessionroleactioncode = $sessionchargedel->roleactioncode ?? null;

    $subcatcode = $sessionchargedel->auditeeins_subcategoryid ?? '{"1": ["A"]}';
    $catcode = $sessionchargedel->catcode ?? '{"1": ["A"]}';

    $subcatArray = json_decode($subcatcode, true) ?? [];
    $catArray = json_decode($catcode, true) ?? [];

    $showAllCategories = false;
    $showAllSubcategories = false;

    $allowedCategoryIds = [];
    $allowedSubcategoryIds = [];

    // Check if 'A' is present for categories
    if (isset($catArray['1']) && is_array($catArray['1'])) {
        if (in_array('A', $catArray['1'])) {
            $showAllCategories = true;
        } else {
            $allowedCategoryIds = $catArray['1'];
        }
    }

    // Check if 'A' is present for subcategories
    if (isset($subcatArray['1']) && is_array($subcatArray['1'])) {
        if (in_array('A', $subcatArray['1'])) {
            $showAllSubcategories = true;
        } else {
            $allowedSubcategoryIds = $subcatArray['1'];
        }
    }

    $categoryIdsString = implode(',', $allowedCategoryIds);
    $subcategoryIdsString = implode(',', $allowedSubcategoryIds);

    $sessionCatData = [
        'showAll' => $showAllCategories,
        'allowedIds' => $allowedCategoryIds,
        'allowedIdsString' => $categoryIdsString,
        // 'firstId' => $showAllCategories ? '' : $allowedCategoryIds[0] ?? '',
    ];

    $sessionSubcatData = [
        'showAll' => $showAllSubcategories,
        'allowedIds' => $allowedSubcategoryIds,
        'allowedIdsString' => $subcategoryIdsString,
        // 'firstId' => $showAllSubcategories ? '' : $allowedSubcategoryIds[0] ?? '',
    ];
    $make_dept_disable = $session_deptcode ? 'disabled' : '';
    $showSection = $sessionroletypecode == view()->shared('Dist_roletypecode');

    $AD_allowroletype = [
        view()->shared('Dist_roletypecode'),
        view()->shared('Re_roletypecode'),
        view()->shared('Ho_roletypecode'),
    ];

    $showAD_District_tab = 'N';
    if (
        in_array($sessionroletypecode, $AD_allowroletype) &&
        in_array($sessionroleactioncode, ['02', '07', '11', '10'])
    ) {
        $showAD_District_tab = 'Y';
    }

    $adminallow = [
        view()->shared('Ho_roletypecode'),
        view()->shared('DGA_roletypecode'),
        view()->shared('Admin_roletypecode'),
        view()->shared('NIC_roletypecode'),
    ];

    $showSectionTab = in_array($sessionroletypecode, $adminallow) ? 'Y' : 'N';

    $dept = collect($departments)->firstWhere('deptcode', $session_deptcode);
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
        transition: 0.3s ease;
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
        transition: 0.3s ease;
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
        transition: 0.3s ease;
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
        transition: 0.3s ease;
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
        transition: 0.3s ease;
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
        box-shadow: var(--card-shadow);
        padding: 20px;
        overflow-x: auto;
    }

    .chart-container {
        background: var(--white);
        border-radius: 10px;
        box-shadow: var(--card-shadow);
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
        text-align: left;
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
        transition: 0.3s ease;
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
        transition: 0.3s ease;
    }

    .refresh-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .refresh-btn .fa-spinner {
        margin-right: 8px;
    }

    /* Make  table headers bold */
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

    /* Total row styling for  tables */
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
        transition: 0.2s ease;
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

    /* Ensure consistent styling across  tables */
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
        padding: 10px;
        font-weight: 600;
        text-align: right !important;
    }

    /* Make sure the highlight is visible in  view modes */
    .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
        /* padding: 10px; */
        font-weight: 600;
    }

    /* Make sure the highlight is visible in all view modes */
    .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
    }

    #institutionTable .team-info {
        white-space: normal !important;
    }

    .table-responsive .dataTables_wrapper .dataTables_length select {
        padding-right: 1.725rem;

    }

    div#institutionTable_length {
        margin-top: 10px;
    }

    div#institutionTable_length {
        margin-top: 10px;
    }

    .file-attachment {
        display: flex;
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
  #detail-table th:nth-child(n+3),
    #detail-table td:nth-child(n+3) {
        text-align: right;
    }

    #districtTable th:nth-child(n+3),
    #districtTable td:nth-child(n+3) {
        text-align: right;
    }

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


</style>

<div class="loading-overlay">
    <div class="cube"></div>
</div>

<div class="filter-section">
    <div class="filter-group">
        <span class="filter-label">Select Program:</span>
        <select id="audit-program-filter" class="filter-select">
            <option value="plantabform">Audit Plan</option>
            <option value="sliptabform">Audit Slip Details</option>
            <option value="auditreport">Audit Report</option>
            {{-- <option value="inspectionaudit">Inspection Audit</option> --}}
            <option value="templateaudit">Template Audit</option>
            {{-- <option value="parareport">APMS Status</option> --}}
        </select>
    </div>

    @if ($usertypecode == 'H')
        <div class="filter-group">
            <span class="filter-label">Category:</span>
            <select id="category-filter" class="filter-select">
            </select>
        </div>

        <?php if (!in_array($session_deptcode, ['01', '05'])): ?>
        <div class="filter-group">
            <span class="filter-label">Sub Category:</span>
            <select id="subcategory-filter" class="filter-select"></select>
        </div>
        <?php endif; ?>


        @php
            $dept = collect($departments)->firstWhere('deptcode', $session_deptcode);
        @endphp

        <input type="hidden" id="department-filter" value="{{ $session_deptcode }}"
            data-full-name="{{ $dept->deptelname ?? '' }}" data-short-name="{{ $dept->deptesname ?? '' }}">
    @else
        <div class="filter-group">
            <span class="filter-label">Department:</span>
            <select id="department-filter" class="filter-select" disabled>
                @if ($session_deptcode)
                    @php
                        $dept = collect($departments)->firstWhere('deptcode', $session_deptcode);
                    @endphp
                    @if ($dept)
                        <option value="{{ $dept->deptcode }}" data-short-name="{{ $dept->deptesname }}"
                            data-full-name="{{ $dept->deptelname }}" selected>
                            {{ $dept->deptelname }}
                        </option>
                    @endif
                @endif
            </select>
        </div>
    @endif

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

    {{-- <button id="refresh-btn" class="refresh-btn">
            <span style="font-size:1.2em"><i class="fas fa-sync-alt"></i></span> Refresh
        </button> --}}
</div>

<div class="card dashboard-container">
    <div class="summary">
        <div class="card-title">
            <h2>Summary Overview</h2>
        </div>
        <div class="stats-grid" id="summary-cards-container">
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
                <div class="table-container">
                    <div class="table-responsive">
                        <table id="detail-table" class="table table-striped table-bordered" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="districtTableContainer" class="card" style="display: none;">
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

    <div id="institutionTableContainer" class="card" style="display: none;">
        <div class="card-header text-white">
            <h3></h3>
            <button class="close-btn" onclick="hideTable('institutionTableContainer')">×</button>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="institutionTable" class="table table-striped table-bordered table-hover"
                    style="width:100%">
                </table>
            </div>
        </div>
    </div>

    <div id="auditReportTableContainer" class="card" style="display: none;">
        <div class="card-header text-white">
            <h3></h3>
            <button class="close-btn" onclick="hideTable('auditReportTableContainer')">×</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="auditReportTable" class="table table-striped table-bordered table-hover"
                    style="width:100%">
                </table>
            </div>
        </div>
    </div>


    <div class="card hide_this" id="slipview_Details">
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
                                    <th>S. No</th>
                                    <th>Slip No</th>
                                    <th>Objection</th>
                                    <th>Team Head</th>
                                    <th>Auditor Name</th>
                                    <th>Slip Created On</th>
                                    <!--<th>Rejoinder</th>-->
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id='no_data_details' class='hide_this'>
                <center>No Data Available</center>
            </div>
        </div>
        <br>
    </div>

    <div class="modal fade" id="HistoryModel" tabindex="-1" aria-labelledby="HistoryModel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#ffffff !important;">
                    <h4 style="text-align:center !important;">Flow of Slip No <b id="slipnodyn"></b></h4>

                    <button type="button" class="btn-close" onclick="RemoveTempFile()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div id="pdf-preview" style="width: 100%;">
                        <div class="datatables">
                            <div class="table-responsive" id="tableshow">
                                <table id="slipHistoryTable"
                                    class="table w-100 table-striped table-bordered display nowrap datatables-basic">
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
                    <div class="text-center mt-3" style="margin-t">
                        <button id="downloadBtn" class="btn btn-info" style="display: none;">
                            <i class="fas fa-download"></i>&nbsp;&nbsp; Download Report
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

                                                            <label class="form-label lang"
                                                                for="validationDefaultUsername"
                                                                key="observation">Auditor Remarks</label>
                                                            <textarea id="viewslip_auditorremarkscccz" class="form-control" placeholder="Enter remarks"
                                                                name="viewslip_auditorremarks"></textarea>

                                                        </div>
                                                        <!--<div class="col-md-4">

                                                                        <label class="form-label required"
                                                                            for="validationDefaultUsername">Auditor
                                                                            Attachment</label>
                                                                        <div class="container my-1"
                                                                            id="viewslip_auditorcontainer"></div>

                                                                    </div>-->
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

                                                            <textarea id="viewslip_auditeeremarks" class="form-control" placeholder="Enter remarks"
                                                                name="viewslip_auditeeremarks"></textarea>

                                                        </div>
                                                        <!--<div class="col-md-4">
                                                            <label class="form-label required"
                                                                for="validationDefaultUsername">Auditee
                                                                Attachment</label>


                                                            <div class="container my-1"
                                                                id="viewslip_auditeecontainer"></div>


                                                        </div>-->
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
                    <!-- Button container with flexbox for centering -->
                    <div class="text-center mt-3" style="margin-t">
                        <button id="downloadBtn" class="btn btn-info" style="display: none;">
                            <i class="fas fa-download"></i>&nbsp;&nbsp; Download Report
                        </button>
                    </div>

                </div>

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
            //showLoadingFilter();
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
    // --- Variable Declarations ---
    let currentViewType = '';
    let currentDepartment = '';
    let selectedStatCard = null;
    let selectedDepartmentCard = null;
    let chart = null;
    let dashboardData = {};
    let filterOptions = {
        years: [],
        departments: [],
        regions: []
    };
    let visibleTables = {
        district: false,
        institution: false,
        audit: false,
        slipviewDetails: false
    };

    let catcode = @json($sessionCatData['firstId'] ?? '');
    let subcatcode = @json($sessionSubcatData['firstId'] ?? '');

    let currentDepartmentId = null;
    let currentViewData = null;
    let isRefreshing = false;
    let isInitialLoad = true;
    let initialFilters = {};

    function initializeCategoryValues() {
        return new Promise((resolve) => {
            const deptCode = getDepartmentCode();
            const sessionCatData = window.sessionCategoryData || {};
            const sessionSubcatData = window.sessionSubcategoryData || {};

            const hasValidSessionCat = sessionCatData.firstId && sessionCatData.firstId !== '';
            const hasValidSessionSubcat = sessionSubcatData.firstId && sessionSubcatData.firstId !== '';

            if (!deptCode) {
                if (hasValidSessionCat) {
                    catcode = sessionCatData.firstId;
                } else {
                    catcode = '';
                }
                subcatcode = '';

                reloadCategoriesBasedOnDepartment('').then(() => {
                    const categoryFilter = $('#category-filter');
                    const catOptions = categoryFilter.find('option:not([value=""])');

                    const allSessionOption = catOptions.filter('[value="ALL_SESSION"]');
                    const allOption = catOptions.filter('[value="ALL"]');

                    if (allSessionOption.length > 0) {
                        categoryFilter.val('ALL_SESSION');
                        catcode = 'ALL_SESSION';
                    } else if (allOption.length > 0) {
                        categoryFilter.val('ALL');
                        catcode = 'ALL';
                    } else if (hasValidSessionCat) {
                        categoryFilter.val(sessionCatData.firstId);
                        catcode = sessionCatData.firstId;
                    } else if (catOptions.length > 0) {
                        const firstValidOption = catOptions.filter(function() {
                            const text = $(this).text().toLowerCase();
                            return !text.includes('select') && !text.includes('all');
                        }).first();

                        if (firstValidOption.length > 0) {
                            categoryFilter.val(firstValidOption.val());
                            catcode = firstValidOption.val();
                        }
                    }

                    return reloadSubcategoriesBasedOnCategory(catcode).then(() => {
                        const subcategoryFilter = $('#subcategory-filter');
                        const subOptions = subcategoryFilter.find('option:not([value=""])');

                        if (catcode === 'ALL') {
                            subcategoryFilter.val('ALL');
                            subcatcode = 'ALL';
                        } else {
                            const subAllSessionOption = subOptions.filter(
                                '[value="ALL_SESSION"]');
                            const subAllOption = subOptions.filter('[value="ALL"]');

                            if (subAllSessionOption.length > 0) {
                                subcategoryFilter.val('ALL_SESSION');
                                subcatcode = 'ALL_SESSION';
                            } else if (subAllOption.length > 0) {
                                subcategoryFilter.val('ALL');
                                subcatcode = 'ALL';
                            } else if (hasValidSessionSubcat) {
                                subcategoryFilter.val(sessionSubcatData.firstId);
                                subcatcode = sessionSubcatData.firstId;
                            } else if (subOptions.length > 0) {
                                const firstValidSubOption = subOptions.filter(function() {
                                    const text = $(this).text().toLowerCase();
                                    return !text.includes('select') && !text.includes(
                                        'all');
                                }).first();

                                if (firstValidSubOption.length > 0) {
                                    subcategoryFilter.val(firstValidSubOption.val());
                                    subcatcode = firstValidSubOption.val();
                                }
                            }
                        }

                        initializeFilters();
                        resolve();
                    });
                });
                return;
            }

            reloadCategoriesBasedOnDepartment(deptCode).then(() => {
                const categoryFilter = $('#category-filter');
                const options = categoryFilter.find('option:not([value=""])');

                const allSessionOption = options.filter('[value="ALL_SESSION"]');
                const allOption = options.filter('[value="ALL"]');

                if (allSessionOption.length > 0) {
                    categoryFilter.val('ALL_SESSION');
                    catcode = 'ALL_SESSION';
                } else if (allOption.length > 0) {
                    categoryFilter.val('ALL');
                    catcode = 'ALL';
                } else if (hasValidSessionCat && options.filter(`[value="${sessionCatData.firstId}"]`)
                    .length > 0) {
                    categoryFilter.val(sessionCatData.firstId);
                    catcode = sessionCatData.firstId;
                } else if (options.length > 0) {
                    const firstValidOption = options.filter(function() {
                        const text = $(this).text().toLowerCase();
                        return !text.includes('select') && !text.includes('all');
                    }).first();

                    if (firstValidOption.length > 0) {
                        categoryFilter.val(firstValidOption.val());
                        catcode = firstValidOption.val();
                    }
                }

                return reloadSubcategoriesBasedOnCategory(catcode).then(() => {
                    const subcategoryFilter = $('#subcategory-filter');
                    const subOptions = subcategoryFilter.find('option:not([value=""])');

                    // If category is "ALL", ensure subcategory is also "ALL"
                    if (catcode === 'ALL') {
                        subcategoryFilter.val('ALL');
                        subcatcode = 'ALL';
                    } else {
                        const subAllSessionOption = subOptions.filter('[value="ALL_SESSION"]');
                        const subAllOption = subOptions.filter('[value="ALL"]');

                        if (subAllSessionOption.length > 0) {
                            subcategoryFilter.val('ALL_SESSION');
                            subcatcode = 'ALL_SESSION';
                        } else if (subAllOption.length > 0) {
                            subcategoryFilter.val('ALL');
                            subcatcode = 'ALL';
                        } else if (hasValidSessionSubcat && subOptions.filter(
                                `[value="${sessionSubcatData.firstId}"]`).length > 0) {
                            subcategoryFilter.val(sessionSubcatData.firstId);
                            subcatcode = sessionSubcatData.firstId;
                        } else if (subOptions.length > 0) {
                            const firstValidSubOption = subOptions.filter(function() {
                                const text = $(this).text().toLowerCase();
                                return !text.includes('select') && !text.includes(
                                    'all');
                            }).first();

                            if (firstValidSubOption.length > 0) {
                                subcategoryFilter.val(firstValidSubOption.val());
                                subcatcode = firstValidSubOption.val();
                            }
                        }
                    }

                    initializeFilters();
                    resolve();
                });
            }).catch(error => {
                console.error('Error initializing categories:', error);
                // Fallback: set to ALL if available, otherwise empty
                catcode = '';
                subcatcode = '';
                initializeFilters();
                resolve();
            });
        });
    }

    function initializeFilters() {
        initialFilters = {
            program: 'plantabform',
            year: document.getElementById('year-filter').value,
            quarter: document.getElementById('quarter-filter').value,
            department: document.getElementById('department-filter').value,
            catcode: catcode,
            subcatcode: subcatcode
        };

    }

    let whichslipValue = 'nosliptab';
    let isDirectRegionTableClick = false;
    const auditeeDeptCode = @json($session_auditeedeptcode);


    // --- Utility Functions ---
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

    function isParaReport() {
        return getProgramType() === 'parareport';
    }
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
        }
    }

    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    function showLoadingChart() {
        $('.skeleton-loader').show();
        $('.content-wrapper').hide();
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoadingChart() {
        $('.skeleton-loader').hide();
        $('.content-wrapper').show();
        $('.loading-overlay').hide();
    }


    document.addEventListener('DOMContentLoaded', function() {
        const departmentFilter = document.getElementById('department-filter');

        const sessionDeptCode = @json($session_deptcode);
        if (sessionDeptCode) {
            const sessionDeptOption = departmentFilter.querySelector(`option[value="${sessionDeptCode}"]`);

            if (sessionDeptOption) {
                departmentFilter.value = sessionDeptCode;
                departmentFilter.disabled = true;

            }
        }

    });


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
        return {
            extend: 'excelHtml5',
            text: `<i class="fas fa-download"></i> <span class="download-text"> Download</span>`,
            className: 'btn btn-primary',
            filename: titleexcel,
            title: null,
            exportOptions: {
                columns: ':not(.noExport)'
            },
            customize: function(xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                var titleRow = `
                                                    <row r="1">
                                                        <c t="inlineStr" r="A1">
                                                            <is><t>${titleexcel}</t></is>
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

    // --- Filter and Reset Functions ---
    function getCurrentFilters() {

        return {
            program: document.getElementById('audit-program-filter').value,
            year: document.getElementById('year-filter').value,
            quarter: document.getElementById('quarter-filter').value,
            department: document.getElementById('department-filter').value,
            catcode: catcode,
            subcatcode: subcatcode
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

            // Reset view and reload data
            currentDepartment = '';
            currentViewType = '';
            document.getElementById('auditReportTableContainer').style.display = 'none';
            document.getElementById('institutionTableContainer').style.display = 'none';
            document.getElementById('districtTableContainer').style.display = 'none';
            document.getElementById('slipview_Details').style.display = 'none';

            resetView();

            loadDashboardData(true).finally(() => {
                applyBtn.disabled = false;
                applyBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filters';
                applyBtn.classList.remove('loading');
                isRefreshing = false;

                // Disable apply button after applying since we're now in sync
                applyBtn.disabled = true;
                // document.getElementById('refresh-btn').disabled = true;
            });

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
        // const refreshBtn = document.getElementById('refresh-btn'); // This will be null

        const hasChanged = Object.keys(initialFilters).some(
            key => currentFilters[key] !== initialFilters[key]
        );

        // Only set disabled property if the button exists
        if (applyBtn) {
            applyBtn.disabled = !hasChanged;
        }

    }

    function resetFilters() {
        if (isRefreshing) return;
        const refreshBtn = document.getElementById('refresh-btn');
        const applyBtn = document.getElementById('apply-filters-btn');
        isRefreshing = true;
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';

        try {
            document.getElementById('audit-program-filter').value = initialFilters.program;
            document.getElementById('year-filter').value = initialFilters.year;
            document.getElementById('quarter-filter').value = initialFilters.quarter;

            const departmentFilter = document.getElementById('department-filter');
            if (departmentFilter) {
                if (departmentFilter.tagName === 'SELECT' || departmentFilter.tagName === 'INPUT') {
                    departmentFilter.value = initialFilters.department;
                }
            }

            $('#category-filter').val(initialFilters.catcode);
            catcode = initialFilters.catcode;

            document.getElementById('apply-filters-btn').disabled = true;

            if (initialFilters.catcode === 'ALL') {
                reloadSubcategoriesBasedOnCategory('ALL').then(() => {
                    $('#subcategory-filter').val('ALL');
                    subcatcode = 'ALL';
                    continueReset();
                });
            } else {
                $('#subcategory-filter').empty().append('<option value="">Loading...</option>');
                reloadSubcategoriesBasedOnCategory(initialFilters.catcode).then(() => {
                    $('#subcategory-filter').val(initialFilters.subcatcode);
                    subcatcode = initialFilters.subcatcode;
                    continueReset();
                });
            }

            function continueReset() {
                currentDepartment = '';
                currentViewType = '';
                document.getElementById('auditReportTableContainer').style.display = 'none';
                document.getElementById('institutionTableContainer').style.display = 'none';
                document.getElementById('districtTableContainer').style.display = 'none';
                document.getElementById('slipview_Details').style.display = 'none';

                resetView();

                loadDashboardData(true).finally(() => {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    isRefreshing = false;

                    applyBtn.disabled = true;
                    refreshBtn.disabled = true;
                });
            }
        } catch (error) {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            isRefreshing = false;
            console.error("Refresh failed:", error);
        }
    }


    function reloadCategoriesBasedOnDepartment(deptCode) {
        return new Promise((resolve, reject) => {
            const catSelect = $('#category-filter');
            const subSelect = $('#subcategory-filter');

            subSelect.empty();
            subcatcode = '';

            if (!deptCode) {
                catSelect.empty();
                catSelect.val('');
                resolve();
                return;
            }

            $.ajax({
                url: '/getaccountcategoriesbasednndept',
                method: 'POST',
                data: {
                    deptcode: deptCode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    catSelect.empty();

                    // Get session category data
                    const sessionCatData = window.sessionCategoryData || {
                        showAll: false,
                        allowedIds: [],
                        firstId: ''
                    };

                    // If showAll is true, add "All" option FIRST
                    if (sessionCatData.showAll) {
                        catSelect.append('<option value="ALL">All</option>');
                    }

                    // If we have specific allowed IDs and not showing all, add "All Session" option FIRST
                    if (!sessionCatData.showAll && allAllowedCategoryIds.length > 1) {
                        catSelect.append('<option value="ALL_SESSION">All</option>');
                    }

                    if (response && response.length > 0) {
                        const filteredCategories = filterCategoriesBySession(response);

                        if (filteredCategories.length > 0) {
                            filteredCategories.forEach(cat => {
                                catSelect.append(
                                    `<option value="${cat.catcode}">${cat.catename}</option>`
                                );
                            });

                            resolve();
                        } else {
                            catSelect.append('<option value="">No accessible categories</option>');
                            catSelect.val('');
                            resolve();
                        }
                    } else {
                        catSelect.append('<option value="">No categories found</option>');
                        catSelect.val('');
                        resolve();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load categories:', error);
                    catSelect.empty().append('<option value="">Error loading categories</option>');
                    catSelect.val('');
                    resolve();
                }
            });
        });
    }

    function reloadSubcategoriesBasedOnCategory(catCode) {
        return new Promise((resolve, reject) => {
            const subSelect = $('#subcategory-filter');

            subSelect.empty();
            subcatcode = '';

            const sessionSubcatData = window.sessionSubcategoryData || {
                showAll: false,
                allowedIds: [],
                firstId: ''
            };

            if (catCode === 'ALL') {
                subSelect.empty();
                subSelect.append('<option value="ALL">All</option>');
                subSelect.val('ALL');
                subcatcode = 'ALL';
                resolve();
                return;
            }

            if (!catCode || catCode === '' || catCode === 'ALL_SESSION') {
                subSelect.empty();

                if (sessionSubcatData.showAll) {
                    subSelect.append('<option value="ALL">All</option>');
                    subSelect.val('ALL');
                    subcatcode = 'ALL';
                } else if (!sessionSubcatData.showAll && allAllowedSubcategoryIds.length > 1) {
                    subSelect.append('<option value="ALL_SESSION">All</option>');
                    subSelect.val('ALL_SESSION');
                    subcatcode = 'ALL_SESSION';
                } else {
                    subSelect.val('');
                }

                resolve();
                return;
            }

            // For specific category ID, load subcategories and select first one
            $.ajax({
                url: '/getsubCategoriesBasedOncategory',
                method: 'POST',
                data: {
                    category: catCode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    subSelect.empty();


                    if (response.success && response.data && response.data.length > 0) {
                        const filteredSubcategories = filterSubcategoriesBySession(response.data);

                        if (filteredSubcategories.length > 0) {
                            filteredSubcategories.forEach(sub => {
                                const displayName = sub.subcatename || sub.subcatname || sub
                                    .subcatelname || 'Unknown';
                                const subcategoryId = sub.auditeeins_subcategoryid || sub
                                    .id || sub.subcatcode;

                                subSelect.append(
                                    `<option value="${subcategoryId}">${displayName}</option>`
                                );
                            });

                            const firstSubcategory = filteredSubcategories[0];
                            const firstSubcategoryId = firstSubcategory.auditeeins_subcategoryid ||
                                firstSubcategory.id || firstSubcategory.subcatcode;

                            subSelect.val(firstSubcategoryId);
                            subcatcode = firstSubcategoryId;
                        } else {
                            subSelect.append(
                                '<option value="">No accessible subcategories</option>');
                            subSelect.val('');
                        }
                    } else {
                        subSelect.append('<option value="">No subcategories found</option>');
                        subSelect.val('');
                    }

                    resolve();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load subcategories:', error);
                    subSelect.empty();
                    subSelect.append('<option value="">Error loading subcategories</option>');
                    subSelect.val('');
                    resolve();
                }
            });
        });
    }

    // DOM Ready - Set up ONLY button click handlers
    document.addEventListener('DOMContentLoaded', function() {
        initializeSessionData();

        initializeCategoryValues().then(() => {
            initialFilters = getCurrentFilters();
            loadDashboardData(true); // Load initial data
        });

        // ONLY set up button click handlers
        const applyBtn = document.getElementById('apply-filters-btn');
        const refreshBtn = document.getElementById('refresh-btn');

        if (applyBtn) {
            applyBtn.addEventListener('click', applyFilters);
        }

        // if (refreshBtn) {
        //     refreshBtn.addEventListener('click', resetFilters);
        // }

        // Set up filter change listeners ONLY for button state
        const filterControls = [
            'audit-program-filter', 'year-filter', 'quarter-filter',
            'department-filter', 'category-filter', 'subcategory-filter'
        ];

        filterControls.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', function() {
                    checkFiltersChanged(); // Just enable/disable buttons
                });
            }
        });

        // Disable buttons initially since filters match initial state
        if (applyBtn) applyBtn.disabled = true;
        // if (refreshBtn) refreshBtn.disabled = true;
    });

    function filterCategoriesBySession(categories) {
        const sessionCatData = window.sessionCategoryData || {
            showAll: false,
            allowedIds: [],
            firstId: ''
        };

        if (sessionCatData.showAll) {
            return categories;
        } else {
            return categories.filter(cat =>
                sessionCatData.allowedIds.includes(cat.catcode.toString())
            );
        }
    }

    function filterSubcategoriesBySession(subcategories) {
        const sessionSubcatData = window.sessionSubcategoryData || {
            showAll: false,
            allowedIds: [],
            firstId: ''
        };

        if (sessionSubcatData.showAll) {
            return subcategories;
        } else {
            const filtered = subcategories.filter(sub => {
                const subId = (sub.auditeeins_subcategoryid || sub.id || sub.subcatcode).toString();
                const isAllowed = sessionSubcatData.allowedIds.includes(subId);
                return isAllowed;
            });
            return filtered;
        }

    }


    let allAllowedCategoryIds = [];
    let allAllowedSubcategoryIds = [];

    function initializeSessionData() {
        window.sessionCategoryData = @json($sessionCatData);
        window.sessionSubcategoryData = @json($sessionSubcatData);

        // Store all allowed IDs from session
        allAllowedCategoryIds = window.sessionCategoryData.allowedIds || [];
        allAllowedSubcategoryIds = window.sessionSubcategoryData.allowedIds || [];

        window.initialFilters = window.initialFilters || {};
    }

    function getDepartmentCode() {
        const departmentFilter = document.getElementById('department-filter');
        if (!departmentFilter) return '';

        if (departmentFilter.tagName === 'SELECT') {
            return departmentFilter.value;
        } else if (departmentFilter.tagName === 'INPUT') {
            return departmentFilter.value;
        }

        return '';
    }


    function resetView() {
        document.getElementById('department-card').style.display = 'none';
        document.getElementById('detail-card').style.display = 'none';
        document.getElementById('slipview_Details').style.display = 'none';
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
    }

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
            const deptOptions = deptFilter.querySelectorAll('option:not([value=""])');
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
    async function getDashboardData(auditProgram, year, quarter, department, region, auditeeDeptCode, deptCode, catcode,
        subcatcode) {
        try {
            let finalCatcode = '';
            let finalSubcatcode = '';

            if (catcode === 'ALL') {
                finalCatcode = '';
            } else if (catcode === 'ALL_SESSION') {
                finalCatcode = allAllowedCategoryIds.join(',');
            } else {
                finalCatcode = catcode;
            }

            if (subcatcode === 'ALL') {
                finalSubcatcode = '';
            } else if (subcatcode === 'ALL_SESSION') {
                finalSubcatcode = allAllowedSubcategoryIds.join(',');
            } else {
                finalSubcatcode = subcatcode;
            }

            const params = {
                source_form: auditProgram,
                year: year,
                department: department,
                region: region,
                auditeeDeptCode: auditeeDeptCode,
                deptcode: deptCode,
                catcode: finalCatcode,
                subcatcode: finalSubcatcode,
                financialyear: document.getElementById('year-filter').value,
                _token: '{{ csrf_token() }}'
            };

            if (auditProgram === 'plantabform' || auditProgram === 'auditreport' || auditProgram ===
                'templateaudit' || auditProgram ==='inspectionaudit' || auditProgram === 'parareport') {
                params.quarter = quarter;
            } else if (auditProgram === 'sliptabform') {
                params.quarterslip = quarter;
            }

            const queryString = new URLSearchParams(params).toString();

            const response = await fetch(`/ajax-auditeewise-data?${queryString}`, {
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
        const isInspectionAudit = sourceForm === 'inspectionaudit';
        const isParaReport = sourceForm === 'parareport';

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
                total: createDataStructure("Total Inspections"),
                pending: createDataStructure("Inspection Pending"),
                ongoing: createDataStructure("Inspection Ongoing"),
                completed: createDataStructure("Inspection Completed"),
                summary: {
                    total: 0,
                    pending: 0,
                    ongoing: 0,
                    completed: 0
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
                title: `${label} Wise Distribution`,
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
        }  else if (isParaReport) {
            processParaReportData(apiData);
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
                    shortName: item.deptsname || numericDeptCode,
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
                    shortName: item.deptsname || numericDeptCode,
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
                    shortName: item.deptsname || numericDeptCode,
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
                    if (counts[metric] > 0) {
                        addDepartmentData(
                            metric,
                            deptInfo,
                            counts[metric],
                            numericDeptCode,
                            item.deptname,
                            index
                        );
                    }
                });
            });
        }

        function processInspectionAuditData(items) {
            items.forEach((item, index) => {
                const numericDeptCode = item.deptCode;
                const deptInfo = deptCodeMapping[numericDeptCode] || {
                    shortName: item.deptsname || numericDeptCode,
                    fullName: item.deptname || numericDeptCode
                };

                if (shouldSkipDepartment(numericDeptCode, deptInfo.shortName)) return;

                const counts = {
                    total: parseInt(item.total_inspection_count) || 0,
                    pending: parseInt(item.pending_inspection_count) || 0,
                    ongoing: parseInt(item.ongoing_inspection_count) || 0,
                    completed: parseInt(item.completed_inspection_count) || 0
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
                    shortName: item.deptsname || numericDeptCode,
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
        const isParaReport = auditProgram === 'parareport';

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
            await new Promise(resolve => setTimeout(resolve, 100));

            const auditProgram = document.getElementById('audit-program-filter').value;
            const year = document.getElementById('year-filter').value;
            const quarter = document.getElementById('quarter-filter').value;
            const department = currentDepartment || document.getElementById('department-filter').value ||
                sessionDeptCode;
            const deptCode = currentDepartment || document.getElementById('department-filter').value ||
                sessionDeptCode;

            const currentCatcode = catcode;
            const currentSubcatcode = subcatcode;

            const region = '';

            if (forceRefresh || isInitialLoad || !dashboardData.summary) {
                dashboardData = await getDashboardData(auditProgram, year, quarter, department, region,
                    auditeeDeptCode, deptCode, currentCatcode, currentSubcatcode);
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

        const container = document.getElementById('summary-cards-container');
        container.innerHTML = '';

        const auditProgram = getProgramType();
        const isSlipDetails = isSlipTabForm();
        const isAuditReportValue = isAuditReport();
        const isTemplateAuditValue = isTemplateAudit();
        const isInspectionAuditValue = isInspectionAudit();
        const isParaReportValue = isParaReport();
        const selectedDepartment = document.getElementById('department-filter').value;

        let deptName = '';
        const deptFilter = document.getElementById('department-filter');

        if (deptFilter) {
            if (deptFilter.tagName === 'SELECT') {
                const selectedOption = deptFilter.options[deptFilter.selectedIndex];
                if (selectedOption) {
                    deptName = selectedOption.dataset.shortName || '';
                }
            } else if (deptFilter.tagName === 'INPUT' && deptFilter.type === 'hidden') {
                deptName = deptFilter.dataset.shortName || '';
            }
        }


        const summaryTitle = document.querySelector('.summary .card-title h2');
        if (deptName) {
            if (isAuditReportValue) {
                summaryTitle.textContent = `${deptName} - Audit Report Summary`;
            } else if (isTemplateAuditValue) {
                summaryTitle.textContent = `${deptName} - Template Audit Summary`;
            } else if (isInspectionAuditValue) {
                summaryTitle.textContent = `${deptName} - Inspection Audit Summary`;
            }  else if (isParaReportValue) {
                    summaryTitle.textContent = `${deptName} - Audit Para Details Summary`;
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
            } else if (isInspectionAuditValue) {
                summaryTitle.textContent = 'All Departments - Inspection Audit Overview';
            } else if (isParaReportValue) {
                    summaryTitle.textContent = 'All Departments - Audit Para Details Overview';
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
                    value: dashboardData.summary.audit_completed.toLocaleString('en-IN'),
                    label: 'Audit Completed',
                    color: 'success',
                    disabled: dashboardData.summary.audit_completed === 0
                },
                {
                    id: 'report_finalized-card',
                    icon: '📄',
                    value: dashboardData.summary.report_finalized.toLocaleString('en-IN'),
                    label: 'Report Finalized',
                    color: 'info',
                    disabled: dashboardData.summary.report_finalized === 0
                },
                {
                    id: 'report_issued-card',
                    icon: '📤',
                    value: dashboardData.summary.report_issued.toLocaleString('en-IN'),
                    label: 'Report Issued',
                    color: 'primary',
                    disabled: dashboardData.summary.report_issued === 0
                },
                {
                    id: 'pending_finalize-card',
                    icon: '⏳',
                    value: dashboardData.summary.pending_finalize.toLocaleString('en-IN'),
                    label: 'Pending to Finalize',
                    color: 'warning',
                    disabled: dashboardData.summary.pending_finalize === 0
                },
                {
                    id: 'pending_issue-card',
                    icon: '📬',
                    value: dashboardData.summary.pending_issue.toLocaleString('en-IN'),
                    label: 'Pending to Issue',
                    color: 'danger',
                    disabled: dashboardData.summary.pending_issue === 0
                }
            ];
        } else if (isSlipDetails) {
            summaryData = [{
                    id: 'total-card',
                    icon: '📋',
                    value: dashboardData.summary.total.toLocaleString('en-IN'),
                    label: 'Total Slips',
                    color: 'primary',
                    disabled: dashboardData.summary.total === 0
                },
                {
                    id: 'pending-card',
                    icon: '⏳',
                    value: dashboardData.summary.pending.toLocaleString('en-IN'),
                    label: 'Pending Slips',
                    color: 'warning',
                    disabled: dashboardData.summary.pending === 0
                },
                {
                    id: 'converted-card',
                    icon: '🔄',
                    value: dashboardData.summary.converted.toLocaleString('en-IN'),
                    label: 'Converted to Paras',
                    color: 'success',
                    disabled: dashboardData.summary.converted === 0
                },
                {
                    id: 'dropped-card',
                    icon: '🗑️',
                    value: dashboardData.summary.dropped.toLocaleString('en-IN'),
                    label: 'Dropped Slips',
                    color: 'danger',
                    disabled: dashboardData.summary.dropped === 0
                }
            ];
        } else if (isTemplateAuditValue) {
            summaryData = [{
                    id: 'total-card',
                    icon: '📋',
                    value: dashboardData.summary.total.toLocaleString('en-IN'),
                    label: 'Total no. of Template Audits',
                    color: 'primary',
                    disabled: dashboardData.summary.total === 0
                },
                {
                    id: 'completed-card',
                    icon: '✅',
                    value: dashboardData.summary.completed.toLocaleString('en-IN'),
                    label: 'Total no. of Audits Completed',
                    color: 'success',
                    disabled: dashboardData.summary.completed === 0
                },
                {
                    id: 'ongoing-card',
                    icon: '🕓',
                    value: dashboardData.summary.ongoing.toLocaleString('en-IN'),
                    label: 'Total no. of Audits Ongoing',
                    color: 'warning',
                    disabled: dashboardData.summary.ongoing === 0
                },
                {
                    id: 'pending-card',
                    icon: '⏳',
                    value: dashboardData.summary.pending.toLocaleString('en-IN'),
                    label: 'Total no. of Audits Pending',
                    color: 'danger',
                    disabled: dashboardData.summary.pending === 0
                },
            ];
        } else if (isInspectionAuditValue) {
            summaryData = [{
                    id: 'total-card',
                    icon: '📋',
                    value: dashboardData.summary.total.toLocaleString('en-IN'),
                    label: 'Total no. of Scheduled Inspections',
                    color: 'primary',
                    disabled: dashboardData.summary.total === 0
                },
                {
                    id: 'completed-card',
                    icon: '✅',
                    value: dashboardData.summary.completed.toLocaleString('en-IN'),
                    label: 'Inspections Completed',
                    color: 'success',
                    disabled: dashboardData.summary.completed === 0
                },
                {
                    id: 'ongoing-card',
                    icon: '🕓',
                    value: dashboardData.summary.ongoing.toLocaleString('en-IN'),
                    label: 'Inspections Ongoing',
                    color: 'warning',
                    disabled: dashboardData.summary.ongoing === 0
                },
                {
                    id: 'pending-card',
                    icon: '⏳',
                    value: dashboardData.summary.pending.toLocaleString('en-IN'),
                    label: 'Inspections Pending',
                    color: 'danger',
                    disabled: dashboardData.summary.pending === 0
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
            } else {
            summaryData = [{
                    id: 'regions-card',
                    icon: '🕍',
                    value: dashboardData.summary.regions.toLocaleString('en-IN'),
                    label: 'Total Auditable Regions',
                    color: 'primary',
                    disabled: dashboardData.summary.regions === 0
                },
                {
                    id: 'districts-card',
                    icon: '🗺️',
                    value: dashboardData.summary.districts.toLocaleString('en-IN'),
                    label: 'Total Auditable Districts',
                    color: 'warning',
                    disabled: dashboardData.summary.districts === 0
                },
                {
                    id: 'institutions-card',
                    icon: '🏫',
                    value: dashboardData.summary.institutions.toLocaleString('en-IN'),
                    label: 'Total Auditable Institutions',
                    color: 'success',
                    disabled: dashboardData.summary.institutions === 0
                }
            ];
        }

        const allDisabled = summaryData.every(item => item.disabled);
        if (allDisabled) {
            container.innerHTML =
                `
                    <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No data available for the selected filters
                    </div>
                    </div>`;
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
                    currentDepartment = currentDept;
                    showDepartmentView(currentViewType);
                };
            }

            card.innerHTML = `
                                    <div class="stat-icon ${item.color}">${item.icon}</div>
                                    <div class="stat-content">
                                        <h3>${item.value}</h3>
                                        <p>${item.label}</p>
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
        const isAuditReportValue = isAuditReport();
        const isTemplateAuditValue = 'templateaudit' === auditProgram;
        const isInspectionAuditValue = isInspectionAudit();
        const isParaReportValue = 'parareport' === auditProgram;

        if (!dashboardData || !dashboardData[viewType]) {
            console.error(`Data not found for viewType: ${viewType}`);
            const container = document.getElementById('department-cards-container');
            container.innerHTML = '<div class="no-data">Data not available for this view</div>';
            document.getElementById('detail-card').style.display = 'none';
            return;
        }

        const data = dashboardData[viewType];

        const departmentFilter = document.getElementById('department-filter');
        let selectedDepartment = '';
        if (departmentFilter) {
            if (departmentFilter.tagName === 'SELECT') {
                selectedDepartment = departmentFilter.value;
            } else if (departmentFilter.tagName === 'INPUT') {
                selectedDepartment = departmentFilter.value;
            }
        }

        if (selectedDepartment) {
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
                'audit_completed': 'All Departments Region-wise Distribution of Completed Audits',
                'report_finalized': 'All Departments Region-wise Distribution of Finalized Reports',
                'report_issued': 'All Departments Region-wise Distribution of Issued Reports',
                'pending_finalize': 'All Departments Region-wise Distribution of Pending Finalizations',
                'pending_issue': 'All Departments Region-wise Distribution of Pending Issues'
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
        } else if (isInspectionAuditValue) {
            const inspectionTitles = {
                'total': 'All Departments Region-wise Distribution of Total Inspections',
                'pending': 'All Departments Region-wise Distribution of Pending Inspections',
                'ongoing': 'All Departments Region-wise Distribution of Ongoing Inspections',
                'completed': 'All Departments Region-wise Distribution of Completed Inspections'
            };
            title = inspectionTitles[viewType] || title;
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
            !dept.isTotal
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
            card.dataset.distcode = dept.distCode ?? '';
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
        let selectedOption = deptFilterEl.tagName === 'SELECT' ?
            deptFilterEl.options[deptFilterEl.selectedIndex] :
            deptFilterEl;

        let selectedDept = {
            numericDeptCode: deptFilterEl.value,
            name: selectedOption.dataset.fullName || '',
            label: selectedOption.dataset.shortName || ''
        };

        showLoadingChart();
        const quarter = document.getElementById('quarter-filter').value;
        const year = document.getElementById('year-filter').value;
        const sourceForm = getProgramType();
        const url = isAuditReport() ? "load_auditee_auditreport_regionwise" : "load_auditeeregiondata";

        let viewTypeLabel = '';
        const rawTitle = document.getElementById('detail-view-title').textContent || '';

        if (isSlipTabForm()) {
            const slipTitles = {
                'total': 'Region Wise Total Audit Slip(s) Details',
                'pending': 'Region Wise Pending Audit Slip(s) Details',
                'converted': 'Region Wise Converted to Para(s) Audit Slip(s) Details',
                'dropped': 'Region Wise Dropped Audit Slip(s) Details'
            };
            viewTypeLabel = slipTitles[viewType] || rawTitle;
        } else if (isAuditReport()) {
            const auditTitles = {
                'audit_completed': 'Region Wise Audit Completed Institution Details',
                'report_finalized': 'Region Wise Audit Report Finalized Details',
                'report_issued': 'Region Wise Audit Report Issued Details',
                'pending_finalize': 'Region Wise Audit Pending to Finalize Details',
                'pending_issue': 'Region Wise Audit Pending to Issue Details'
            };
            viewTypeLabel = auditTitles[viewType] || rawTitle;
        } else if (isPlantabForm()) {
            const planTitles = {
                'regions': 'Region Wise Audit Region Details',
                'districts': 'Region Wise Audit District Details',
                'institutions': 'Region Wise Audit Institution Details',
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
                'total': 'Region-wise Total Scheduled Institutions for Inspections',
                'pending': 'Region-wise Pending Inspections Details',
                'ongoing': 'Region-wise Ongoing Inspections Details',
                'completed': 'Region-wise Completed Inspections Details'
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
                year: document.getElementById('year-filter').value,
                quarter: quarter,
                regionCode: selectedDept.regionCode ?? '',
                distCode: selectedDept.distCode ?? '',
                auditeeDeptCode: auditeeDeptCode,
                catcode: catcode,
                subcatcode: subcatcode,
                financialyear: document.getElementById('year-filter').value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (!response || !response.data) {
                    console.error('Invalid response format:', response);
                    document.getElementById('detail-card').innerHTML = `
                                <div class="alert alert-danger text-center">
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
                        } else {
                        return parseInt(item.distcount || 0) > 0 || parseInt(item.alloc_inscount ||
                            0) > 0;
                    }
                });


                if (filteredData.length === 0) {
                    document.getElementById('detail-card').innerHTML = `
                                    <div class="alert alert-info text-center">
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
                                    <div class="alert alert-danger text-center">
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

        if (departmentCard) departmentCard.style.display = 'none';
        if (detailCard) detailCard.style.display = 'block';
        if (slipviewDetails) slipviewDetails.style.display = 'none';

        const auditReportTable = $('#auditReportTableContainer');
        if (auditReportTable.length) auditReportTable.hide();

        hideAllDrillDownTables();
    }

    function hideAllDrillDownTables() {
        $('#districtTableContainer').hide();
        $('#institutionTableContainer').hide();
        $('#auditReportTableContainer').hide();
        document.getElementById('slipview_Details').style.display = 'none';
        visibleTables.district = false;
        visibleTables.institution = false;
        visibleTables.audit = false;
        visibleTables.slipviewDetails = false;
    }

    function handleDepartmentCardClick(cardElement) {
        showLoadingChart();
        const deptCode = cardElement.dataset.deptcode;
        const regionCode = cardElement.dataset.regioncode || '';
        const distCode = cardElement.dataset.distcode || '';
        const deptName = cardElement.dataset.deptname;
        const viewType = cardElement.dataset.viewtype || 'total';
        const quarter = document.getElementById('quarter-filter').value;
        const financialYear = document.getElementById('year-filter').value;

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
                'pending_finalize': 'Region-wise Distribution of Audits Pending Finalization',
                'pending_issue': 'Region-wise Distribution of Reports Pending Issue'
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
                'total': 'Region-wise Distribution of Total Inspections',
                'pending': 'Region-wise Distribution of Pending Inspections',
                'ongoing': 'Region-wise Distribution of Ongoing Inspections',
                'completed': 'Region-wise Distribution of Completed Inspections'
            };
            viewTypeLabel = inspectionTitles[viewType] || rawTitle;
        }else if (isParaReport()) {
                const paraTitles = {
                    totalparas: 'Region-wise Audit Para Details for Total Para(s)',
                    processedparas: 'Region-wise Audit Para Details for Under Processing Para(s)',
                    pendingparas: 'Region-wise Audit Para Details for Pending Para(s)',
                    // droppedparas: 'Region-wise Audit Para Details for Dropped Para(s)',
                    // rejectedparas: 'Region-wise Audit Para Details for Rejected Para(s)',
                    // clarifyparas: 'Region-wise Audit Para Details for Clarification Needed Para(s)',
                };
                viewTypeLabel = paraTitles[viewType] || rawTitle;
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

        document.querySelector('.chart-container').style.display = 'block';
        document.querySelector('.table-container').style.display = 'none';
        document.getElementById('view-toggle').textContent = 'View as Table';

        const sourceForm = document.getElementById('audit-program-filter').value;
        const isAuditReportValue = isAuditReport();
        const isSlipTabFormValue = isSlipTabForm();
        const isInspectionAuditValue = 'inspectionaudit' === sourceForm;
        const isTemplateAuditValue = 'templateaudit' === sourceForm;
        const isParaReportValue = 'parareport' === sourceForm;
        const url = isAuditReportValue ? "load_auditee_auditreport_regionwise" : "load_auditeeregiondata";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                deptCode: deptCode,
                sourceform: sourceForm,
                year: document.getElementById('year-filter').value,
                quarter: quarter,
                regionCode: regionCode,
                distCode: distCode,
                auditeeDeptCode: auditeeDeptCode,
                catcode: catcode,
                subcatcode: subcatcode,
                financialyear: document.getElementById('year-filter').value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (!response || !response.data) {
                    console.error('Invalid response format:', response);
                    document.getElementById('detail-card').innerHTML = `
                                <div class="alert alert-danger text-center">
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
                    }   else if (isParaReportValue) {
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
                    } else {
                    return parseInt(item.distcount || 0) > 0 || parseInt(item.alloc_inscount ||
                            0) > 0;
                    }
                });

                if (filteredData.length === 0) {
                    document.getElementById('detail-card').innerHTML = `
                            <div class="alert alert-info text-center">
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
                            <div class="alert alert-danger text-center">
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

        if (isParaReport()) {
            return transformParaReportData(apiData, deptCode, deptName, sourceForm, viewType);
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
            // Show only institutions dataset
            datasets = [{
                label: 'Institutions',
                data: [],
                backgroundColor: institutionColors,
                borderColor: '#fff',
                borderWidth: 1
            }];
        } else if (viewType === 'districts') {
            // Show only districts dataset
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
                headers: isSlipTabForm() ? ["S.No", "Region", "Total No. of Audit Districts",
                    "Total No.of Institutions Audit Commenced", slipColumnTitle
                ] : ["S.No", "Region", "Total No. of Audit Districts", "Total No.of Auditable Institutions"],
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
        let metricColumnTitle = "";
        let districtColumnTitle = "Auditable Districts";


        switch (viewType) {
            case 'audit_completed':
                metricColumnTitle = "Audit Completed Institutions";
                districtColumnTitle = "Districts with Completed Audits";
                break;
            case 'report_finalized':
                metricColumnTitle = "Report Finalized Institutions";
                districtColumnTitle = "Districts with Finalized Reports";
                break;
            case 'report_issued':
                metricColumnTitle = "Report Issued Institutions";
                districtColumnTitle = "Districts with Issued Reports";
                break;
            case 'pending_finalize':
                metricColumnTitle = "Pending to Finalize Institutions";
                districtColumnTitle = "Districts with Pending Finalization";
                break;
            case 'pending_issue':
                metricColumnTitle = "Pending to Issue Institutions";
                districtColumnTitle = "Districts with Pending Issue";
                break;
        }

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
        let metricColumnTitle = "";
        let districtColumnTitle = "Auditable Districts";

        switch (viewType) {
            case 'total':
                metricColumnTitle = "Total Template Audit Institutions";
                districtColumnTitle = "Districts with Template Audits";
                break;
            case 'pending':
                metricColumnTitle = "Template Audit Pending Institutions";
                districtColumnTitle = "Districts with Pending Template Audits";
                break;
            case 'ongoing':
                metricColumnTitle = "Template Audit Ongoing Institutions";
                districtColumnTitle = "Districts with Ongoing Template Audits";
                break;
            case 'completed':
                metricColumnTitle = "Template Audit Completed Institutions";
                districtColumnTitle = "Districts with Completed Template Audits";
                break;

        }

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
        let metricColumnTitle = "";
        let districtColumnTitle = "Auditable Districts";

        switch (viewType) {
            case 'total':
                metricColumnTitle = "Total Inspections";
                districtColumnTitle = "Districts with Total Inspections";
                break;
            case 'pending':
                metricColumnTitle = "Pending Inspections";
                districtColumnTitle = "Districts with Pending Inspections";
                break;
            case 'ongoing':
                metricColumnTitle = "Ongoing Inspections";
                districtColumnTitle = "Districts with Ongoing Inspections";
                break;
            case 'completed':
                metricColumnTitle = "Completed Inspections";
                districtColumnTitle = "Districts with Completed Inspections";
                break;

        }

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

    function updateRegionChart(data, currentDepartment, sourceForm, response, viewType) {
        const ctx = document.getElementById('detail-chart').getContext('2d');

        const isAuditReport = sourceForm === 'auditreport';
        const isParaReport = sourceForm === 'parareport';

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
                updateRegionChart(data, currentDepartment, sourceForm, response, viewType, chartType);
            });
        });
    }

    function updateRegionTable(data, currentDepartment, sourceForm, response, viewType) {
        const table = document.getElementById('detail-table');
        const isAuditReport = sourceForm === 'auditreport';
        const isTemplateAudit = sourceForm === 'templateaudit';
        const isParaReportValue = sourceForm === 'parareport';


        if (!table) {
            console.error('Table element not found');
            return;
        }

        if (data.table.rows.length === 0) {
            table.innerHTML = `
                <div class="alert alert-info text-center">
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

        if (isAuditReport) {
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

        const columnCount = isAuditReport ? 4 : (sourceForm === 'sliptabform' ? 5 : 4);

        const totalDistricts = data.table.rows.reduce((sum, row) => {
            if (row.length < 3) return sum;
            const num = typeof row[2] === 'string' ? parseInt(row[2].replace(/,/g, '')) || 0 : parseInt(row[
                2]) || 0;
            return sum + num;
        }, 0);

        const totalInstitutions = data.table.rows.reduce((sum, row) => {
            if (row.length < (isAuditReport ? 4 : 4)) return sum;
            const colIndex = isAuditReport ? 3 : 3;
            const num = typeof row[colIndex] === 'string' ? parseInt(row[colIndex].replace(/,/g, '')) || 0 :
                parseInt(row[colIndex]) || 0;
            return sum + num;
        }, 0);

        let totalSlips = 0;
        if (sourceForm === 'sliptabform') {
            totalSlips = data.table.rows.reduce((sum, row) => {
                if (row.length < 5) return sum;
                const num = typeof row[4] === 'string' ? parseInt(row[4].replace(/,/g, '')) || 0 : parseInt(row[
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
        data.table.rows.forEach((rowData, rowIndex) => {
            if (!Array.isArray(rowData)) return;

            const tr = document.createElement('tr');

            for (let cellIndex = 0; cellIndex < columnCount; cellIndex++) {
                const cellData = rowData[cellIndex] || '';
                const td = document.createElement('td');

                // For audit report: S.no (0), Region (1), Auditable Districts (2), Institutions (3)
                const numericColumns = isAuditReport ? [2, 3] : (sourceForm === 'sliptabform' ? [2, 3, 4] : [2,
                    3
                ]);

                if (numericColumns.includes(cellIndex)) {
                    const count = typeof cellData === 'object' ? cellData.value : parseInt(cellData) || 0;
                    const isClickable = count > 0;
                    const colors = {
                        2: isAuditReport ? '#28a745' : '#28a745', // Districts - green for both
                        3: isAuditReport ? '#007bff' : '#007bff', // Institutions - blue for both
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
                            } else if (isParaReportValue) {
                                    loadFunction = loadParaInstituteDetails;
                            } else {
                                loadFunction = loadAuditDetails;
                            }

                            const loadDistFunction = isAuditReport ? loadDistrictReportDetails :
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


        if (isPlantabForm()) {
            loadFunction = loadAllocatedInstituteDetails;
        } else if (isSlipTabForm()) {
            loadFunction = loadCommencedInstituteDetails;
        } else if (isTemplateAudit) {
            loadFunction = loadTemplateauditInstituteDetails;
        } else if (isInspectionAudit()) {
            loadFunction = loadInspectionInstituteDetails;
        } else if (isParaReportValue) {
                loadFunction = loadParaInstituteDetails;
        } else {
        loadFunction = loadAuditDetails;
        }


        const loadDistFunction = isAuditReport ? loadDistrictReportDetails : loadDistrictDetails;


        const tfoot = document.createElement('tfoot');
        const footerRow = document.createElement('tr');

        const totalLabelCell = document.createElement('th');
        totalLabelCell.colSpan = isAuditReport ? 2 : 2;
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
                    title: titleexcel,
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
                    const isTotalRow = data.some(cell =>
                        typeof cell === 'string' && cell.toUpperCase().includes('TOTAL')
                    );

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
                    loadFunction(currentDepartment, '', '', '', sourceForm, deptName, viewType, whichslipValue);
                });
                institutionTotalCell.innerHTML = '';
                institutionTotalCell.appendChild(institutionBadge);

                if (sourceForm === 'sliptabform') {
                    const slipBadge = createClickableTotal(totalSlips, '#7000cf', () => {
                        loadFunction(currentDepartment, '', '', '', sourceForm, deptName, viewType);
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
                return "pendingslipscount";
            case 'converted':
                return "convertedslipcount";
            case 'dropped':
                return "droppedslipcount";
            default:
                return "nosliptab";
        }
    }

    function loadDistrictDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
        const isInspectionAudit = sourceForm === 'inspectionaudit';
        const isParaReportValue = sourceForm === 'parareport';

        const quarter = document.getElementById('quarter-filter').value;
        const financialYear = document.getElementById('year-filter').value;
        showLoading();

        $('#loader').show();

        if (visibleTables.institution && $('#institutionTableContainer').data('current-region') !== regionName) {
            $('#institutionTableContainer').hide();
            visibleTables.institution = false;
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
        const titleElement = $('#districtTableContainer .card-header h3');

        if (isPlantabForm()) {
            titleElement.text(`${deptName}: District-wise Audit Plans for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Audit Plans for ${regionName} Region`;
        } else if (isTemplateAudit()) {
            titleElement.text(`${deptName}: District-wise Template Audits for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Template Audits for ${regionName} Region`;
        } else if (isSlipTabForm()) {
            titleElement.text(`${deptName}: District-wise Audit slip Details for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Audit slip Details for ${regionName} Region`;
        } else if (isInspectionAudit) {
            titleElement.text(`${deptName}: District-wise Inspection Audits for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Inspection Audits for ${regionName} Region`;
        } else if (isAuditReport()) {
            titleElement.text(`${deptName}: District-wise Audit Reports for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Audit Reports for ${regionName} Region`;
        } else if (isParaReportValue) {
            titleElement.text(`${deptName}: District-wise Audit Para Details for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Audit Para Details for ${regionName} Region`;
        } else {
            titleElement.text(`${deptName}: District-wise Audit Plans for ${regionName} Region`);
            titleexcel = `${deptName}: District-wise Audit Plans for ${regionName} Region`;
        }

        // Set whichslipValue based on form type
        const whichslipValue = "nosliptab";

        // Define load function based on form type
        const loadFunction = getLoadFunction(sourceForm);


        // Build table structure

        tableElement.append(`
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Name of the District</th>
                        <th>Total No. of Institutions</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right; font-weight:bold;">Total:</th>
                        <th id="totalInstitutionsCell" style="font-weight:bold;"></th>
                    </tr>
                </tfoot>
            `);

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
                url: 'load_auditeedistrictdata',
                type: 'POST',
                data: function(d) {
                    return {
                        deptCode: deptCode,
                        regionCode: regionCode,
                        distCode: distCode,
                        sourceform: sourceForm,
                        quarter: quarter,
                        auditeeDeptCode: auditeeDeptCode,
                        catcode: catcode,
                        subcatcode: subcatcode,
                        financialyear: document.getElementById('year-filter').value,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    };
                },
                dataSrc: function(json) {
                    $('#loader').hide();
                    hideLoading();

                    let totalInstitutions = 0;

                    // Calculate total based on view type
                    if (isTemplateAudit()) {
                        totalInstitutions = json.data.reduce((sum, row) => {
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
                                    count = (parseInt(row.pending_count) || 0) +
                                        (parseInt(row.ongoing_count) || 0) +
                                        (parseInt(row.completed_count) || 0);
                            }
                            return sum + count;
                        }, 0);
                    } else if (isInspectionAudit) {
                        totalInstitutions = json.data.reduce((sum, row) => {
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
                    } else if (isParaReportValue) {
                        totalInstitutions = json.data.reduce((sum, row) => {
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
                    } else {
                        totalInstitutions = json.data.reduce((sum, row) => sum + (parseInt(row
                            .alloc_inscount) || 0), 0);
                    }

                    // Update total badge
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
                    if (isTemplateAudit()) {
                        const filteredData = json.data.map(row => {
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
                            // Return modified row with the correct count
                            return {
                                ...row,
                                alloc_inscount: institutionCount, // This will be used for display
                                original_alloc_inscount: row.alloc_inscount // Keep original
                            };
                        }).filter(row => parseInt(row.alloc_inscount) > 0);

                        return filteredData;

                    } else if (isInspectionAudit) {
                        const filteredData = json.data.map(row => {
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
                                    institutionCount = parseInt(row.completed_inspection_count) ||
                                        0;
                                    break;
                                default:
                                    institutionCount = (parseInt(row
                                            .not_scheduled_inspection_count) || 0) +
                                        (parseInt(row.pending_inspection_count) || 0) +
                                        (parseInt(row.ongoing_inspection_count) || 0) +
                                        (parseInt(row.completed_inspection_count) || 0);
                            }
                            // Return modified row with the correct count
                            return {
                                ...row,
                                alloc_inscount: institutionCount, // This will be used for display
                            };
                        }).filter(row => parseInt(row.alloc_inscount) > 0);

                        return filteredData;
                    } else if (isParaReportValue) {
                            const filteredData = json.data.map(row => {
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
                            // Return modified row with the correct count
                            return {
                                ...row,
                                alloc_inscount: institutionCount, // This will be used for display
                            };
                        }).filter(row => parseInt(row.alloc_inscount) > 0);

                        return filteredData;

                    } else {
                        const filteredData = json.data.filter(row => parseInt(row.alloc_inscount) > 0);
                        return filteredData;
                    }
                },
                error: function(xhr, error, thrown) {
                    $('#loader').hide();
                    hideLoading();
                    console.error("Error loading district details:", error, xhr.responseText);
                    alert('Failed to load district details. Please try again.');
                },
                complete: function() {
                    hideLoading();
                    $('#loader').hide();
                }
            },
            dom: '<"top"Bfrtip<"clear">>',
            buttons: [{
                extend: 'excel',
                text: '<i class="fas fa-download"></i> Download',
                title: titleexcel,
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            }],
            responsive: true,
            searching: true,
            ordering: true,
            info: true,
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
                    className: 'text-center',
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
                                'data-region': row.regioncode,
                                'data-dist': row.distcode,
                                'data-distname': row.distename,
                                'data-sourceform': sourceForm,
                                'data-deptname': deptName,
                                'data-viewtype': viewType,
                                'data-whichtab': whichslipValue
                            });

                            return badge.outerHTML;
                        }
                        return data;
                    }

                }
            ],
            initComplete: function() {
                $('.clickable-badge').hover(
                    function() {
                        $(this).css('opacity', '0.8');
                    },
                    function() {
                        $(this).css('opacity', '1');
                    }
                );

                $(document).on('click', '.clickable-badge', function() {
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

                $("#districtTable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>"
                );
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
                $('.dataTables_paginate').toggle(api.data().length > api.page.len());

            }
        });

        $('#districtTableContainer').data('current-region', regionName);
        $('#districtTableContainer').show();
        visibleTables.district = true;

        $('html, body').animate({
            scrollTop: $('#districtTableContainer').offset().top
        }, 500);
    }

    function getLoadFunction(sourceForm) {
        const loadFunctions = {
            plantabform: loadAllocatedInstituteDetails,
            templateaudit: loadTemplateauditInstituteDetails,
            sliptabform: loadCommencedInstituteDetails,
            inspectionaudit: loadInspectionInstituteDetails,
            auditreport: loadAuditDetails,
            // legacyreport: loadlegacyInstituteDetails,
            parareport: loadParaInstituteDetails,
            // paracount: loadParaCountInstituteDetails,
            // retirementpara: loadRetirementParaDetails

        };

        return loadFunctions[sourceForm] || null;
        }

    function loadDistrictReportDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
        const quarter = document.getElementById('quarter-filter').value;
        const financialYear = document.getElementById('year-filter').value;
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
                className: 'text-center',
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
                viewTypeTitle = 'Audit Completed Institutions';
                break;
            case 'report_finalized':
                viewTypeColumn = 'report_finalized';
                viewTypeTitle = 'Report Finalized';
                break;
            case 'report_issued':
                viewTypeColumn = 'report_issued';
                viewTypeTitle = 'Report Issued';
                break;
            case 'pending_finalize':
                viewTypeColumn = 'pending_finalize';
                viewTypeTitle = 'Pending to Finalize';
                break;
            case 'pending_issue':
                viewTypeColumn = 'pending_issue';
                viewTypeTitle = 'Pending to Issue';
                break;
        }

        columns.push({
            data: viewTypeColumn,
            className: 'text-left',

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
                        <th id="totalInstitutionsCell" style="text-align:center; font-weight:bold;"></th>
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
                url: 'load_auditee_report_districtwise',
                type: 'POST',
                data: function(d) {
                    return {
                        deptCode: deptCode,
                        regionCode: regionCode,
                        distCode: distCode,
                        sourceform: sourceForm,
                        quarter: quarter,
                        viewType: viewType,
                        auditeeDeptCode: auditeeDeptCode,
                        catcode: catcode,
                        subcatcode: subcatcode,
                        financialyear: document.getElementById('year-filter').value,
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
                title: titleexcel,
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

                $(document).off('click', '.clickable-badge').on('click', '.clickable-badge', function() {
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
        const isTemplateAuditValue = isTemplateAudit();

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

        const baseColumns = [{
                data: null,
                title: 'S.No',
                className: 'text-left',
                render: function(data, type, row, meta) {
                    return `<span>${meta.row + 1}</span>`;
                }
            },
            {
                data: 'instename',
                title: 'Institution Name',
                className: 'text-left',
                width: '200px',
                render: function(data, type, row) {
                    return `<span>${data}</span>`;
                }
            },
            {
                data: 'catename',
                title: 'Category',
                className: 'text-left',
                render: function(data, type, row) {
                    return `<span>${data}</span>`;
                }
            },
            {
                data: 'subcatename',
                title: 'Sub Category',
                className: 'text-left',
                render: function(data) {
                    if (data) {
                        return `<span>${data}</span>`;
                    } else {
                        return '-';
                    }
                }
            },
            {
                data: null,
                title: 'Team Members',
                className: 'text-left',
                width: '200px',
                render: function(data) {
                    const teamHead = data.team_head_en ?
                        `<div class="mb-1"><strong class="small">Head:</strong> ${data.team_head_en.replace(' - ', '<span class="text-muted"> - </span>')}</div>` :
                        '';

                    const members = data.team_members_en ?
                        `<div class="mb-1"><strong class="small">Members:</strong> ${data.team_members_en.replace(/ - /g, '<span class="text-muted"> - </span>')}</div>` :
                        '';

                    return `
                <div class="team-info small">
                    ${teamHead}
                    ${members}
                    <div><strong class="small">Team size:</strong> <span>${data.total_team_count || 0}</span></div>
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
                data: 'distename',
                title: 'District',
                className: 'text-left',
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
                data: 'schedule_status',
                title: 'Schedule Status',
                className: 'text-left',
                render: function(data) {
                    if (data === 'Scheduled') {
                        return '<span class="badge bg-success" style="font-size:11px;">Scheduled</span>';
                    } else {
                        return '<span class="badge bg-danger" style="font-size:11px;">Not Scheduled</span>';
                    }
                }
            },
            {
                data: 'response_status',
                title: 'Intimation Status',
                className: 'text-left',
                render: function(data) {
                    if (data === 'Replied') {
                        return '<span class="badge bg-success" style="font-size:11px;">Accepted</span>';
                    } else {
                        return '<span class="badge bg-warning" style="font-size:11px;">Pending</span>';
                    }
                }
            },
            {
                data: 'workallocation_status',
                title: 'Work Allocation',
                className: 'text-left',
                render: function(data) {
                    if (data === 'Work Allocated') {
                        return '<span class="badge bg-success" style="font-size:11px;">Work Allocated</span>';
                    } else {
                        return '<span class="badge bg-danger" style="font-size:11px;">Not Allocated</span>';
                    }
                }
            },
            {
                data: 'entrymeet_status',
                title: 'Entry Meeting',
                className: 'text-left',
                render: function(data) {
                    if (!data || data === 'No') {
                        return '<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>';
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
                        return '<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>';
                    }
                    return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                }
            }
        ];

        // Status column for template audit
        const statusColumn = {
            data: null,
            title: 'Status',
            className: 'text-left',
            render: function(data, type, row) {
                // Determine overall status based on various status fields
                let status = 'Not Started';
                let badgeClass = 'bg-secondary';

                if (row.workallocation_status === 'Work Allocated') {
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

        // Filter columns based on sourceForm
        let finalColumns;
        let columnDefs;

        if (isTemplateAuditValue) {
            finalColumns = [
                baseColumns[0], // S.No
                baseColumns[1], // Institution Name
                baseColumns[5], // Mandays
                baseColumns[6], // District
                baseColumns[7], // From Date
                baseColumns[8], // To Date
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
                    targets: [4, 5, 6, 7, 8, 9, 10, 11, 12,
                        13
                    ],
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
                    title: viewTitle,
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
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
                    url: 'load_auditee_institute_details',
                    type: 'POST',
                    data: {
                        deptCode: deptCode,
                        regionCode: regionCode,
                        distCode: distCode,
                        sourceform: sourceForm,
                        quarter: quarter,
                        auditeeDeptCode: auditeeDeptCode,
                        catcode: catcode,
                        subcatcode: subcatcode,
                        financialyear: document.getElementById('year-filter').value,
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

                    tableElement.DataTable().columns.adjust().draw();
                    hideLoading();

                    // Initialize mobile toggle AFTER table is fully loaded
                    setTimeout(() => {
                        const mobileColumns = isTemplateAudit ? ["mandays", "distename",
                            "fromdate",
                            "todate", "status"
                        ] : ["teammembers", "mandays", "regionename", "distename",
                            "schedule_status",
                            "response_status", "workallocation_status", "entrymeet_status",
                            "exitmeet_status", "viewfieldaudit"
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
        // if (viewType && viewType !== 'all') {
        //     viewTitle += ` (${viewType.charAt(0).toUpperCase() + viewType.slice(1)})`;
        // }

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
                title: 'Institution Name',
                className: 'text-left',
                width: '200px'
            },
            {
                data: 'distename',
                title: 'District',
                className: 'text-left',
            },
            {
                data: 'mandays',
                title: 'Mandays',
                className: 'text-center',
                render: function(data) {
                    return `<span>${data || '0'}</span>`;
                }
            },
            {
                data: 'fromdate',
                title: 'From Date',
                className: 'text-left',
                render: function(data) {
                    return data && data !== '-' ? `<span class="date-badge">${data}</span>` :
                        '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'todate',
                title: 'To Date',
                className: 'text-left',
                render: function(data) {
                    return data && data !== '-' ? `<span class="date-badge">${data}</span>` :
                        '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'startdate',
                title: 'Start Date',
                className: 'text-left',
                render: function(data) {
                    return data && data !== '-' ? `<span class="date-badge">${data}</span>` :
                        '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'enddate',
                title: 'End Date',
                className: 'text-left',
                render: function(data) {
                    return data && data !== '-' ? `<span class="date-badge">${data}</span>` :
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
                    title: viewTitle,
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
                    url: 'load_auditee_institute_details',
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
                            auditeeDeptCode: auditeeDeptCode,
                            catcode: catcode,
                            subcatcode: subcatcode,
                            financialyear: document.getElementById('year-filter').value,
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
            viewTitle = ` ${deptName}: Institution-wise Inspection Audit Details for ${regionName} District`;
        } else if (isRegionView) {
            viewTitle = ` ${deptName}: Institution-wise Inspection Audit Details for ${regionName} Region`;
        } else {
            viewTitle = ` ${deptName}: Institution-wise Inspection Audit Details`;
        }

        // if (viewType && viewType !== 'all') {
        //     viewTitle += ` (${viewType.charAt(0).toUpperCase() + viewType.slice(1)})`;
        // }

        $('#institutionTableContainer .card-header h3').text(viewTitle);

        const columns = [{
                data: null,
                title: 'S.No',
                className: 'align-middle text-center',
                width: '3%',
                render: function(data, type, row, meta) {
                    return `<span>${meta.row + 1}</span>`;
                }
            },
            {
                data: null,
                title: 'Department Details',
                className: 'text-wrap text-start',
                width: '15%',
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
                title: 'Institution Name',
                className: 'd-none d-md-table-cell lang extra-column text-wrap',
                width: '25%',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            {
                data: 'audit_quarter',
                title: 'Audit Quarter',
                className: 'd-none d-md-table-cell lang extra-column text-center',
                width: '10%',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            {
                data: 'audit_period',
                title: 'Audit Period',
                className: 'd-none d-md-table-cell lang extra-column text-center',
                width: '10%',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            {
                data: 'inspected_by',
                title: 'Inspected By',
                className: 'align-middle text-center',
                width: '12%',
                render: function(data, type, row) {
                    if (row.inspected_by) {
                        return `${data}<br>`;
                    }
                    return data || 'Not Assigned';
                }
            },
            {
                data: null,
                title: 'Inspection Date',
                className: 'd-none d-md-table-cell lang extra-column text-center',
                width: '12%',
                render: function(data, type, row) {
                    function formatDate(dateString) {
                        if (!dateString || dateString === '-') return 'NA';
                        // If date is already in DD/MM/YYYY format, return as is
                        if (typeof dateString === 'string' && dateString.includes('/')) {
                            return dateString;
                        }
                        const date = new Date(dateString);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    }

                    const start = formatDate(row.fromdate);
                    const end = formatDate(row.todate);

                    return `
                <div><strong>Start:</strong> <span class="date-badge">${start}</span></div>
                <div><strong>End:</strong> <span class="date-badge">${end}</span></div>
            `;
                }
            },
            {
                data: null,
                title: 'Entry & Exit Meeting Dates',
                className: 'd-none d-md-table-cell lang extra-column text-center',
                width: '12%',
                render: function(data, type, row) {
                    function formatDate(dateString) {
                        if (!dateString || dateString === '-') return 'NA';
                        // If date is already in DD/MM/YYYY format, return as is
                        if (typeof dateString === 'string' && dateString.includes('/')) {
                            return dateString;
                        }
                        const date = new Date(dateString);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}-${month}-${year}`;
                    }

                    const entry = formatDate(row.entrymeetdate);
                    const exit = formatDate(row.exitmeetdate);

                    return `
                <div><strong>Entry:</strong> <span class="date-badge">${entry}</span></div>
                <div><strong>Exit:</strong> <span class="date-badge">${exit}</span></div>
            `;
                }
            },
            {
                data: 'overall_status',
                title: 'Status',
                className: 'align-middle text-center',
                width: '10%',
                render: function(data) {
                    let badgeClass = 'bg-secondary';
                    let displayText = data || 'Not Scheduled';

                    if (data === 'Completed') badgeClass = 'bg-success';
                    else if (data === 'Ongoing') badgeClass = 'bg-info';
                    else if (data === 'Pending') badgeClass = 'bg-warning';
                    else if (data === 'Not Scheduled') badgeClass = 'bg-secondary';

                    return `<span class="badge ${badgeClass}">${displayText}</span>`;
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
                    title: viewTitle,
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
                scrollX: true,
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
                    url: 'load_auditee_institute_details',
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
                            auditeeDeptCode: auditeeDeptCode,
                            catcode: catcode,
                            subcatcode: subcatcode,
                            financialyear: document.getElementById('year-filter').value,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                    },
                    dataSrc: function(json) {
                        if (!json || !json.data) {
                            hideLoading();
                            return [];
                        }

                        // Filter data based on viewType
                        if (viewType && viewType !== 'all') {
                            return json.data.filter(function(item) {
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

                        return json.data;
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

                    // Safe column adjustment
                    const dataTable = tableElement.DataTable();
                    if (dataTable && typeof dataTable.columns === 'function') {
                        dataTable.columns.adjust();

                        // Only call responsive.recalc() if responsive plugin is properly loaded
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
                    // Safe column adjustment on draw
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

    function loadParaInstituteDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
        showLoading();
        const quarterElem = document.getElementById('quarter-filter');
        const quarter = (quarterElem && quarterElem.value) ? quarterElem.value : '';

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

        let slipTypeTitle;
        switch (viewType) {
            case 'total':
                slipTypeTitle = 'Total Slips';
                break;
            case 'pending':
                slipTypeTitle = 'Pending Slips';
                break;
            case 'converted':
                slipTypeTitle = 'Converted Slips';
                break;
            case 'dropped':
                slipTypeTitle = 'Dropped Slips';
                break;
            default:
                slipTypeTitle = 'Slip Details';
        }

        let viewTitle;
        if (isDistrictView) {
            viewTitle = `${deptName}: Institution-wise Audit Slip Details for ${regionName} District`;
        } else if (isRegionView) {
            viewTitle = `${deptName}: Institution-wise Audit Slip Details for ${regionName} region`;
        } else {
            viewTitle = `${deptName}: Institution-wise Audit Slip Details`;
        }

        $('#institutionTableContainer .card-header h3').text(viewTitle);

        const allColumns = [{
                data: null,
                title: 'S.No',
                className: 'text-center',
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
                data: 'auditeedeptename',
                title: 'Auditee Department Name',
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
                    return `<span">${data}</span>`;
                }
            },
            {
                data: 'regionename',
                title: 'Region',
                className: 'text-center',
                render: function(data) {
                    return `<span class="region-badge">${data}</span>`;
                }
            },
            {
                data: 'distename',
                title: 'District',
                className: 'text-center',
                render: function(data) {
                    return `<span class="district-badge">${data}</span>`;
                }
            },
            {
                data: 'fromdate',
                title: 'From Date',
                className: 'text-center',
                render: function(data) {
                    return `<span class="date-badge">${data}</span>`;
                }
            },
            {
                data: 'todate',
                title: 'To Date',
                className: 'text-center',
                render: function(data) {
                    return `<span class="date-badge">${data}</span>`;
                }
            },
            {
                data: 'entrymeet_status',
                title: 'Entry Meeting',
                className: 'text-center',
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
                className: 'text-center',
                render: function(data) {
                    if (!data || data === 'No') {
                        return `<span class="badge bg-danger" style="font-size:11px;">Not Commenced</span>`;
                    }
                    return `<span class="badge bg-success" style="font-size:11px;">${data}</span>`;
                }
            },

            {
                data: 'totalslips',
                title: 'Total Slips',
                className: 'text-center',
                visible: viewType === 'total',
                render: function(data, type, row) {
                    if (type === 'display') {
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? 'badge bg-info sliponchange' : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;' :
                            'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                        const slipText = row.totalslips > 1 ? 'Slips' : 'Slip';

                        return `<span class="${badgeClass}" style="${badgeStyle}"
                            ${isPositive ? `data-instname='${row.instename}' data-slipstatus='all' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                            ${row.totalslips}
                        </span>`;
                    }
                    return data;
                }
            },
            {
                data: 'pendingslips',
                title: 'Pending Slips',
                className: 'text-center',
                visible: viewType === 'pending',
                render: function(data, type, row) {
                    if (type === 'display') {
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? 'badge bg-warning sliponchange' : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;' :
                            'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                        const slipText = row.pendingslips > 1 ? 'Slips' : 'Slip';

                        return `<span class="${badgeClass}" style="${badgeStyle}"
                                ${isPositive ? `data-instname='${row.instename}' data-slipstatus='P' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                                ${row.pendingslips}
                            </span>`;
                    }
                    return data;
                }
            },
            {
                data: 'convertedslips',
                title: 'Converted Slips',
                className: 'text-center',
                visible: viewType === 'converted',
                render: function(data, type, row) {
                    if (type === 'display') {
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? 'badge bg-danger sliponchange' : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;' :
                            'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                        const slipText = row.convertedslips > 1 ? 'Slips' : 'Slip';

                        return `<span class="${badgeClass}" style="${badgeStyle}"
                                ${isPositive ? `data-instname='${row.instename}' data-slipstatus='X' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                                ${row.convertedslips}
                            </span>`;
                    }
                    return data;
                }
            },
            {
                data: 'droppedslips',
                title: 'Dropped Slips',
                className: 'text-center',
                visible: viewType === 'dropped',
                render: function(data, type, row) {
                    if (type === 'display') {
                        const isPositive = parseInt(data) > 0;
                        const badgeClass = isPositive ? 'badge bg-info sliponchange' : 'badge';
                        const badgeStyle = isPositive ?
                            'font-size:13px; cursor:pointer;background-color:#ff4047 !important;' :
                            'font-size:13px; background-color: #ebebeb !important; color: #131313 !important; cursor:text;';
                        const slipText = row.droppedslips > 1 ? 'Slips' : 'Slip';

                        return `<span class="${badgeClass}" style="${badgeStyle}"
                            ${isPositive ? `data-instname='${row.instename}'  data-slipstatus='A' data-auditscheduleid='${row.encrypted_auditscheduleid}'` : ''}>
                            ${row.droppedslips}
                        </span>`;
                    }
                    return data;
                }
            },
            {
                data: 'exitmeet_status',
                title: 'Status',
                className: "text-center",
                render: function(data, type, row) {
                    if (data === 'No') {
                        return '<span class="badge bg-warning text-dark">In Progress</span>';
                    } else {
                        return '<span class="badge bg-success">Completed</span>';
                    }
                }
            }
        ];

        try {
            const footerRow = $('<tr>');
            allColumns.forEach(col => {
                footerRow.append(`<th class="${col.className || ''}"></th>`);
            });
            tableElement.append($('<tfoot>').append(footerRow));

            const dataTable = tableElement.DataTable({
                processing: true,
                serverSide: false,
                dom: '<"top"Bfrtip<"clear">>',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-download"></i> Download',
                    title: viewTitle,
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
                    url: 'load_auditee_commenced_institute_details',
                    type: 'POST',
                    data: function(d) {
                        return {
                            deptCode: deptCode,
                            regionCode: regionCode,
                            distCode: distCode,
                            sourceform: sourceForm,
                            quarter: quarter,
                            whichslip: whichslip,
                            auditeeDeptCode: auditeeDeptCode,
                            catcode: catcode,
                            subcatcode: subcatcode,
                            financialyear: document.getElementById('year-filter').value,
                            draw: d.draw,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                    },
                    dataSrc: function(response) {
                        if (!response.data) return [];

                        const filteredData = response.data.filter(item => {
                            if (isAuditReport()) {
                                switch (viewType) {
                                    case 'audit_completed':
                                        return Number(item.audit_completed) > 0;
                                    case 'report_finalized':
                                        return Number(item.report_finalized) > 0;
                                    case 'report_issued':
                                        return Number(item.report_issued) > 0;
                                    case 'pending_finalize':
                                        return Number(item.pending_finalize) > 0;
                                    case 'pending_issue':
                                        return Number(item.pending_issue) > 0;
                                    default:
                                        return true;
                                }
                            } else if (isSlipTabForm()) {
                                switch (whichslip) {
                                    case 'totalslips':
                                        return Number(item.totalslips) > 0;
                                    case 'pendingslipscount':
                                        return Number(item.pendingslips) > 0;
                                    case 'convertedslipcount':
                                        return Number(item.convertedslips) > 0;
                                    case 'droppedslipcount':
                                        return Number(item.droppedslips) > 0;
                                    case 'nosliptab':
                                        return true;
                                }
                            } else {
                                return Number(item.distcount) > 0 || Number(item.alloc_inscount) >
                                    0;
                            }
                        });

                        if (filteredData.length === 0) {
                            $('#institutionTableContainer').html(`
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No data available for ${deptName} in ${viewType}
                </div>
            `);
                            hideLoading();
                        }

                        return filteredData;
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

                        $(api.table().footer()).find('th').html('').show().attr('colspan', 1);

                        const labelCell = $(api.column(0).footer());
                        labelCell.attr('colspan', 11)
                            .html(`<strong>Total no. of Slips </strong>`)
                            .addClass('dt-footer-label');

                        for (let i = 1; i < columnIndex; i++) {
                            $(api.column(i).footer()).hide();
                        }

                        const countCell = $(api.column(columnIndex).footer());
                        countCell.html(`
                                <span class="badge bg-primary" style="font-size:13px; padding:6px 12px;">
                                    ${total.toLocaleString('en-IN')}
                                </span>
                                `).css('text-align',
                            'left');

                    }
                },

                initComplete: function() {
                    $("#institutionTable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");

                    tableElement.addClass('table-hover table-striped align-middle');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                    $('.dataTables_filter input').addClass('form-control form-control-sm');

                    const columnIndex = getColumnIndex(viewType);
                    if (columnIndex !== -1) {
                        $(`#institutionTable thead th:nth-child(${columnIndex + 1})`).addClass(
                            'bg-primary text-white');
                        $(`#institutionTable tbody td:nth-child(${columnIndex + 1})`).addClass('bg-light');
                    }
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
                        '<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                    hideLoading();
                }
            });

            dataTable.on('draw', function() {
                const columnIndex = getColumnIndex(viewType);
                if (columnIndex !== -1) {
                    $('#institutionTable thead th').removeClass('bg-primary text-white');
                    $('#institutionTable tbody td').removeClass('bg-light');

                    $(`#institutionTable thead th:nth-child(${columnIndex + 1})`).addClass(
                        'bg-primary text-white');
                    $(`#institutionTable tbody td:nth-child(${columnIndex + 1})`).addClass('bg-light');
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

    function getColumnIndex(viewType) {
        const headers = {
            'total': 'Total Slips',
            'pending': 'Pending Slips',
            'converted': 'Converted Slips',
            'dropped': 'Dropped Slips'
        };

        const headerText = headers[viewType];
        if (!headerText) return -1;

        let index = -1;
        $('#institutionTable thead th').each(function(i) {
            if ($(this).text().trim() === headerText) {
                index = i;
                return false;
            }
        });
        return index;
    }

    function loadAuditDetails(deptCode, regionCode, distCode, regionName, sourceForm, deptName, viewType) {
        showLoading();
        const quarter = document.getElementById('quarter-filter').value;

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

        const viewTypeLabel = auditTitles[viewType] || 'Audit Report';

        const headerTitle = `${viewTypeLabel} Details for ${regionName || 'All Regions'} (${deptName})`;
        $('#auditReportTableContainer .card-header h3').text(headerTitle);

        const titleexcel = headerTitle;

        $.ajax({
            url: 'load_auditee_auditreport_institutionwise',
            type: 'POST',
            data: {
                deptCode: deptCode,
                regionCode: regionCode,
                distCode: distCode,
                sourceform: sourceForm,
                quarter: quarter,
                viewType: viewType,
                auditeeDeptCode: auditeeDeptCode,
                catcode: catcode,
                subcatcode: subcatcode,
                financialyear: document.getElementById('year-filter').value,
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

        const viewTypeColumnMap = {
            'audit_completed': 'audit_completed',
            'report_finalized': 'report_finalized',
            'report_issued': 'report_issued',
            'pending_finalize': 'pending_finalize',
            'pending_issue': 'pending_issue'
        };

        const allColumns = [{
                data: 'sno',
                title: 'S.No',
                className: 'text-center',
                orderable: false,
                width: '5%'
            },
            {
                data: 'instename',
                title: 'Institution Name',
                className: 'text-left',
                width: '25%'
            },
            {
                data: 'regionname',
                title: 'Region',
                className: 'text-center',
                defaultContent: '',
                width: '10%'
            },
            {
                data: 'distename',
                title: 'District',
                className: 'text-center',
                defaultContent: '',
                width: '10%'
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
                data: null,
                title: 'Status',
                className: 'text-left',
                width: '12%',
                render: function(data, type, row) {
                    // Show status based on the selected view type
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
                    title: titleexcel,
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: ':visible'
                    }
                }, ],
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

                    const columnName = viewTypeColumnMap[viewType];
                    if (columnName) {
                        const columnIndex = allColumns.findIndex(col => col.data === columnName);
                        if (columnIndex !== -1) {
                            const column = api.column(columnIndex);

                        }
                    }
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
    $(document).on('click', '.sliponchange', function() {
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

        if (sourceform === 'sliptabform') {
            defaultQuarter = plantab;
        } else {
            defaultQuarter = sliptab;
        }

        showLoading();

        $('#slipdetails_Table thead').show();
        $('#Sliptableshow').removeClass('hide_this').show();
        $('#no_data_details').addClass('hide_this');
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
                quarter: quarter
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#loader').show();
            },
            success: function(data, textStatus, jqXHR) {
                if (jqXHR.status === 200 && data.success) {

                    if (data.message === "No auditslips found") {
                        $('#Sliptableshow').addClass('hide_this');
                        $('.cardforslips').hide();
                        $('#no_data_details').removeClass('hide_this');
                        return;
                    }

                    let tableData = [];
                    if (data.data && data.data.data && Array.isArray(data.data.data) && data.data
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
                        $('#no_data_details').addClass('hide_this');
                        $('.cardforslips').show();
                    } else {
                        $('#Sliptableshow').addClass('hide_this');
                        $('.cardforslips').hide();
                        $('#no_data_details').removeClass('hide_this');
                    }

                } else {
                    $('#Sliptableshow').addClass('hide_this');
                    $('#no_data_details').removeClass('hide_this');
                }
            },
            error: function(error) {
                console.error("Error fetching data:", error);
                $('#loader').hide();
                hideLoading();
                $('#Sliptableshow').addClass('hide_this');
                $('#no_data_details').removeClass('hide_this');

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
            scrollX: true,
            autoWidth: false,
            responsive: true,
            data: rows,
            dom: '<"top"Bfrtip<"clear">>',
            buttons: [{
                extend: 'excel',
                text: '<i class="fas fa-download"></i> Download',
                title: titleexcel,
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':visible'
                }
            }],
            columns: [{
                    data: null,
                    className: "text-center",
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
                    defaultContent: "N/A"
                },
                {
                    data: null,
                    render: function(data, type, row) {
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

    function getFormattedDate() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        return `${day}-${month}-${year}`;
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

            if (/Region Wise/i.test(rawTitle)) {
                viewTypeLabel = 'Region Wise Audit Details';
            } else if (/District Wise/i.test(rawTitle)) {
                viewTypeLabel = 'District Wise Audit Details';
            } else if (/Institution Wise/i.test(rawTitle)) {
                viewTypeLabel = 'Institution Wise Audit Details';
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
                        title: titleexcel,
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
                    text: 'Region wise Audit Plan Details',
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
                }
            }
        };

        if (chartType === 'bar' || chartType === 'line') {
            baseOptions.scales = {
                y: {
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

        // Safely initialize refresh button
        const applyBtn = document.getElementById('apply-filters-btn');
        // const refreshBtn = document.getElementById('refresh-btn');

        if (applyBtn) {
            applyBtn.addEventListener('click', applyFilters);
        }

        // if (refreshBtn) {
        //     refreshBtn.addEventListener('click', resetFilters);
        // }

        // Safely initialize filter event listeners
        const filters = [
            'audit-program-filter',
            'year-filter',
            'quarter-filter',
            'department-filter',
            'category-filter',
            'subcategory-filter'
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


    const filterControls = [
        'audit-program-filter',
        'year-filter',
        'quarter-filter',
        'department-filter',
        'category-filter',
        'subcategory-filter'
    ];
    // Initialize filter change listeners safely
    // REPLACE the existing change listeners with this:
    filterControls.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', function() {
                checkFiltersChanged(); // Only enable/disable buttons, don't load data
            });
        }
    });

    // For category/subcategory, only update the button state:
    $('#category-filter').on('change', function() {
        const catCode = $(this).val();
        catcode = catCode === '' ? '' : catCode;

        $('#subcategory-filter').empty().append('<option value="">Loading...</option>');
        subcatcode = '';

        reloadSubcategoriesBasedOnCategory(catCode).then(() => {
            if (catCode === 'ALL') {
                $('#subcategory-filter').val('ALL');
                subcatcode = 'ALL';
            } else if (catCode === 'ALL_SESSION') {
                $('#subcategory-filter').val('ALL_SESSION');
                subcatcode = 'ALL_SESSION';
            } else {
                const firstOption = $('#subcategory-filter option:not([value=""])').first();
                if (firstOption.length > 0) {
                    $('#subcategory-filter').val(firstOption.val());
                    subcatcode = firstOption.val();
                }
            }

            checkFiltersChanged(); // ONLY update button state
        });
    });

    $('#subcategory-filter').on('change', function() {
        const selectedValue = $(this).val();
        subcatcode = selectedValue === '' ? '' : selectedValue;
        checkFiltersChanged(); // ONLY update button state
    });

    $('#department-filter').on('change', function() {
        if (!$(this).prop('disabled')) {
            const deptCode = $(this).val();
            initializeCategoryValues().then(() => {
                checkFiltersChanged(); // ONLY update button state
            });
        }
    });
</script>

@endsection
