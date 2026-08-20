@extends('index2')
@include('common.alert')

@section('title', 'Approver Consolidation Institution List')
@section('content')
@php
$sessionchargedel = session('charge');

$deptcode = optional($sessionchargedel)->deptcode ?? '';
$region = optional($sessionchargedel)->regioncode ?? '';
$district = optional($sessionchargedel)->distcode ?? '';
$catcode = optional($sessionchargedel)->catcode ?? '';
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

        --primary-gradient: linear-gradient(135deg, #006dad 0%, #336ed4 100%);
        --success-gradient: linear-gradient(135deg, #007e33 0%, #00b44b 100%);
        --warning-gradient: linear-gradient(135deg, #ffa500 0%, #ffcb29 100%);
        --info-gradient: linear-gradient(135deg, #0088cc 0%, #00d4ff 100%);
        --danger-gradient: linear-gradient(135deg, #d62828 0%, #ff4d6d 100%);

        --card-bg: #ffffff;
        --card-shadow: 0 3px 14px rgba(0, 0, 0, 0.08);
    }

    .table-responsive {
        overflow: unset;
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

    .alert-info {
        color: #ff0000;
    }

        #institutionsTable {
        table-layout: auto;
        width: 100% !important;
    }

    #institutionsTable th,
    #institutionsTable td {
        overflow: hidden;
        white-space: normal !important;
        align-content: center;
    }


    #institutionsTable {
        border-collapse: separate;
        border-spacing: 0;
    }



    #institutionsTable tfoot tr {
        background-color: #f8f9fa;
    }

    #institutionsTable tfoot th {
        background-color: #b8e1f070;
        font-weight: 600;
        text-align: right;
    }
</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<div class="loading-overlay">
    <div class="cube"></div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color">
                <div class="card-header card_header_color lang" key="">List of institutions for consolidation of paras
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label lang" key="department">Department</label>
                        @if ($deptcode)
                        <input type="hidden" id="deptcode_hidden" value="{{ $deptcode }}">
                        <select class="form-select select2" id="deptcode" name="deptcode"
                            onchange="getRegionBasedOnDept(this.value,'')" {{ $deptcode ? 'disabled' : '' }}>
                            <option value="{{ $deptcode }}" selected>
                                {{ $departments->where('deptcode', $deptcode)->first()->deptelname ?? 'Department' }}
                            </option>
                        </select>
                        @else
                        <select class="form-select select2" id="deptcode" name="deptcode"
                            onchange="getRegionBasedOnDept(this.value,'')">
                            <option value="">---Select Department---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->deptcode }}">
                                {{ $department->deptelname }}
                            </option>
                            @endforeach
                        </select>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label lang">Category</label>
                        <select class="form-select select2" id="catcode" name="catcode" {{ $catcode ? 'disabled' : ''
                            }}>
                            @if ($catcode)
                            <option value="{{ $catcode }}" selected>
                                {{ $Category->where('catcode', $catcode)->first()->catename ?? 'Category' }}
                            </option>
                            @else
                            <option value="">---Select Category---</option>
                            @foreach ($Category as $category)
                            <option value="{{ $category->catcode }}">
                                {{ $category->catename }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 text-end d-flex align-items-center justify-content-end">
                        <div class="alert-info mt-2 p-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Select "View" to review the forwarded paras for that institution.
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="institutionsTable"
                                class="table table-striped table-bordered w-100 datatables-basic">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">S.No</th>
                                        <th rowspan="2" class="text-center">Region / District</th>
                                        <th rowspan="2" class="text-center">Institution Name</th>
                                        <th rowspan="2" class="text-center">Category / Sub Category</th>
                                        <th rowspan="2" class="text-center">Audit Year</th>
                                        <th rowspan="2" class="text-center">Quarter</th>
                                        <th rowspan="2" class="text-center">Total No. of Forwarded Paras</th>
                                        <th rowspan="2" class="text-center">Serious Paras</th>
                                        <th rowspan="2" class="text-center">Non Serious Paras</th>
                                        <th colspan="2" class="text-center">Para Verified</th>
                                        <th colspan="2" class="text-center">Para Selected</th>
                                        <th rowspan="2" class="text-center">Para Verified Status</th>
                                        <th rowspan="2" class="text-center">Action</th>
                                    </tr>

                                    <tr>
                                        <th class="text-center">Serious</th>
                                        <th class="text-center">Non Serious</th>
                                        <th class="text-center">Serious</th>
                                        <th class="text-center">Non Serious</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="15" class="text-center">
                                            <p class="mt-2">Loading institutions...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let institutionsDataTable = null;
    let allInstitutionsData = {};

    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    $(document).ready(function() {
        loadAllInstitutions();
    });

    function loadAllInstitutions() {
        $('#institutionsTable tbody').html(`
            <tr>
                <td colspan="15" class="text-center">
                    <p class="mt-2">Loading institutions...</p>
                </td>
            </tr>
        `);

        showLoading();
        $.ajax({
            url: '/check-all-selected-institutions-paras',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success && response.data && response.data.institutions) {
                    displayInstitutionsTable(response.data.institutions);
                    window.allInstitutionsData = response.data;
                } else {
                    $('#institutionsTable tbody').html(`
                        <tr>
                            <td colspan="15" class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No institutions found
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error loading institutions:', error);
                $('#institutionsTable tbody').html(`
                    <tr>
                        <td colspan="15" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading institutions. Please refresh the page.
                        </td>
                    </tr>
                `);
            }
        });
    }

    function displayInstitutionsTable(institutions) {
        if (institutionsDataTable && $.fn.DataTable.isDataTable('#institutionsTable')) {
            institutionsDataTable.destroy();
            $('#institutionsTable tbody').empty();
        }

        let tableData = [];

        institutions.forEach((institution, index) => {
            const unverifiedParas = institution.serious_unverified || 0;
            const totalParas = institution.total_paras || 0;
            const seriousParas = institution.serious_total || 0;
            const nonseriousParas = institution.nonserious_total || 0;
            const seriousverified = institution.serious_verified || 0;
            const nonseriousverified = institution.nonserious_verified || 0;
            const selectedserious = institution.selected_serious_paras || 0;
            const selectednonserious = institution.selected_nonserious_paras || 0;
            const moneyvalueparas = institution.money_value_paras || 0;
            const moneyvalueunverifiedparas = institution.money_value_unverified_paras || 0;
            const verifiedParas = seriousverified + nonseriousverified;
            const isFinalized = institution.is_finalized > 0;

            let status = 'Pending';
            let statusClass = 'bg-warning';
            if (unverifiedParas === 0 && moneyvalueunverifiedparas === 0 && totalParas > 0) {
                status = 'Completed';
                statusClass = 'bg-success';
            }

            tableData.push({
                index: index + 1,
                instid: institution.instid,
                instename: institution.instename || 'N/A',
                quarter: institution.quarter || '-',
                audityear: institution.audit_period || '-',
                category: institution.catename || '-',
                subcategory: institution.subcatename || '-',
                region: institution.regionename || '-',
                district: institution.distename || '-',
		auditplanid: institution.auditplanid,
                regionDisplay: `${institution.regionename || '-'} / ${institution.distename || '-'}`,
                catSubcatDisplay: `<strong>Category:</strong> ${institution.catename || '-'}<br><strong>Sub Category:</strong> ${institution.subcatename || '-'}`,
                totalParas: totalParas,
                unverifiedParas: unverifiedParas,
                seriousParas: seriousParas,
                nonseriousParas: nonseriousParas,
                seriousverified: seriousverified,
                nonseriousverified: nonseriousverified,
                selectedserious: selectedserious,
                selectednonserious: selectednonserious,
                verifiedParas: verifiedParas,
                moneyvalueparas: moneyvalueparas,
                moneyvalueunverifiedparas: moneyvalueunverifiedparas,
                status: status,
                statusClass: statusClass,
                deptcode: institution.deptcode || '',
                deptname: institution.deptelname || '',
                catcode: institution.catcode || '',
                catname: institution.catename || '',
                subcatid: institution.auditeeins_subcategoryid || '',
                subcatname: institution.subcatename || '',
                regioncode: institution.regioncode || '',
                regionname: institution.regionename || '',
                distcode: institution.distcode || '',
                distname: institution.distename || '',
                auditperiodid: institution.auditperiodid || '',
                auditperiod: institution.audit_period || '',
                isFinalized: isFinalized
            });
        });

        institutionsDataTable = $('#institutionsTable').DataTable({
            data: tableData,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            // responsive: true,
            destroy: true,
            autoWidth: false,
            // scrollX: false,
            columns: [
                {
                    data: 'index',
                    title: 'S.No',
                    className: 'text-center',
                    width: '3%'
                },
                {
                    data: 'regionDisplay',
                    title: 'Region / District',
                    className: 'text-left',
                    width: '8%',
                    render: function(data) {
                        return data;
                    }
                },
                {
                    data: 'instename',
                    title: 'Institution Name',
                    width: '14%'
                },
                {
                    data: 'catSubcatDisplay',
                    title: 'Category / Sub Category',
                    className: 'text-left',
                    width: '15%',
                    render: function(data) {
                        return data;
                    }
                },
                {
                    data: 'audityear',
                    title: 'Audit Year',
                    className: 'text-center',
                    width: '7%'
                },
                {
                    data: 'quarter',
                    title: 'Quarter',
                    className: 'text-center',
                    width: '5%'
                },
                {
                    data: 'totalParas',
                    title: 'Total No. of <br> Forwarded Paras',
                    className: 'text-center',
                    width: '6%',
                    render: d => `<span class="number-badge bg-primary">${d}</span>`
                },
                {
                    data: 'seriousParas',
                    title: 'Serious <br>Paras',
                    className: 'text-center',
                    width: '4%',
                    render: d => `<span class="number-badge bg-danger">${d}</span>`
                },
                {
                    data: 'nonseriousParas',
                    title: 'Non Serious<br> Paras',
                    className: 'text-center',
                    width: '4%',
                    render: d => `<span class="number-badge bg-warning">${d}</span>`
                },
                {
                    data: 'seriousverified',
                    title: 'Serious',
                    className: 'text-center',
                    width: '4%',
                    render: d => `<span class="number-badge bg-success">${d}</span>`
                },
                {
                    data: 'nonseriousverified',
                    title: 'Non Serious',
                    className: 'text-center',
                    width: '4%',
                    render: d => `<span class="number-badge bg-success">${d}</span>`
                },
                {
                    data: 'selectedserious',
                    title: 'Serious',
                    className: 'text-center',
                    width: '4%',
                    render: d => `<span class="number-badge bg-secondary">${d}</span>`
                },
                {
                    data: 'selectednonserious',
                    title: 'Non Serious',
                    className: 'text-center',
                    width: '4%',
                    render: d => `<span class="number-badge bg-secondary">${d}</span>`
                },
                {
                    data: 'status',
                    title: 'Para verified Status',
                    className: 'text-center',
                    width: '5%',
                    render: function(d, t, r) {
                        return `<span class="badge ${r.statusClass}">${r.status}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Action',
                    className: 'text-center',
                    width: '8%',
                    render: function(data, type, row) {
                        let payload = {
                            instid: row.instid,
                            instename: row.instename,
                            deptcode: row.deptcode || '',
                            deptname: row.deptname || '',
			    auditplanid: row.auditplanid,
                            catcode: row.catcode || '',
                            catname: row.catname || '',
                            subcatid: row.subcatid || '',
                            subcatname: row.subcatname || '',
                            regioncode: row.regioncode || '',
                            regionname: row.regionname || '',
                            distcode: row.distcode || '',
                            distname: row.distname || '',
                            auditperiodid: row.auditperiodid || '',
                            audityear: row.audityear || ''
                        };

                        if (row.isFinalized) {
                            return `<button class="btn btn-sm btn-success" style="padding: 0.4rem 1rem;" disabled>
                                        Finalized
                                    </button>`;
                        }

                        return `<form method="GET" action="{{ route('approver.consolidation.details') }}" style="display:inline;">
                                    <input type="hidden" name="id" value='${btoa(JSON.stringify(payload))}'>
                                    <button type="submit" class="btn btn-sm btn-primary" style="padding: 0.4rem 1rem;">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                </form>`;
                    }
                }
            ],
            order: [[0, 'asc']],
            columnDefs: [
                { targets: '_all', className: 'align-middle' }
            ],
            initComplete: function() {
                // Add custom CSS for number badges and ensure length menu displays properly
                $('<style>')
                    .prop('type', 'text/css')
                    .html(`
                        .number-badge {
                            display: inline-flex !important;
                            align-items: center !important;
                            justify-content: flex-end !important;
                            min-width: 50px !important;
                            height: 26px !important;
                            padding: 0px 15px !important;
                            border-radius: 6px !important;
                            text-align: right !important;
                            color: white !important;
                            font-weight: 600 !important;
                            font-size: 13px !important;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
                            transition: 0.2s !important;
                            opacity: 1 !important;
                            transform: translateY(0px) !important;
                            margin: 0 auto !important;
                        }

                        .number-badge.bg-success {
                            background-color: #28a745 !important;
                        }

                        .number-badge.bg-secondary {
                            background-color: #483d8beb !important;
                        }

                        .dataTables_wrapper .dataTable td {
                            vertical-align: middle !important;
                        }

                        .dataTables_wrapper .dataTable td.text-center {
                            text-align: center !important;
                        }

                        /* Ensure length menu is visible */
                        .dataTables_length {
                            display: block !important;
                            margin-bottom: 10px !important;
                        }

                        .dataTables_length select {
                            display: inline-block !important;
                            width: auto !important;
                            padding: 0.375rem 1.75rem 0.375rem 0.75rem !important;
                            background-position: right 0.375rem center !important;
                        }
                    `)
                    .appendTo('head');
            }
        });
    }
</script>
@endsection
