@extends('index2')

@section('title', 'Finalise ReadyforAudit')

@section('content')
    @include('common.alert')

    @php
        $sessioncharge = session('charge');
        $sessionRegioncode = $sessioncharge->regioncode ?? '';
        $sessionDistcode = $sessioncharge->distcode ?? '';
    @endphp

    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header card_header_color lang" key="">Insitution Taken for <?php echo $planname; ?></div>
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control"
                                value="{{ $dept_det->deptesname ?? $dept_det->deptelname ?? '' }}" readonly disabled>
                            <input type="hidden" id="deptcode" value="{{ $dept_det->deptcode ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Region</label>
                            <select class="form-select" id="regioncode">
                                <option value="">Select Region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->regioncode }}"
                                        @selected($sessionRegioncode == $region->regioncode)>
                                        {{ $region->regionename }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">District</label>
                            <select class="form-select" id="distcode">
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary" id="filterInstitutionBtn">Submit</button>
                        </div>
                    </div>

                    <div id="readyforauditform" class="hide_this">
                        <form id="auditForm">
                            @csrf

                            <div class="datatables">
                                <div class="table-responsive" id="tableshow">
                                    <table id="institutionTable"
                                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                        <thead>
                                            <tr>
                                                <th>Select</th>
                                                <th>Institution</th>
                                                <th>Audit Year</th>
                                                <th>Mandays</th>
                                                <th>Team Size</th>
                                            </tr>
                                        </thead>
                                        <tbody id="instTable"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-primary"
                                    onclick="confirmation_saveData()">Save</button>
                                <button type="button" class="btn btn-danger" onclick="uncheckalldata()">Clear</button>
                            </div>
                        </form>
                    </div>

                    <div id="nodata" class="hide_this">
                        <center>
                            <p id="del"></p>
                        </center>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script>
        let table;
        const sessionRegionCode = @json($sessionRegioncode);
        const sessionDistCode = @json($sessionDistcode);
        let initialDistrictLoad = true;

        $(document).ready(function() {
            $('#regioncode').on('change', function() {
                loadDistricts($(this).val());
            });

            $('#filterInstitutionBtn').on('click', function() {
                loadInstitutions();
            });

            initialiseFilters();
        });

        function initialiseFilters() {
            if (sessionRegionCode) {
                $('#regioncode').val(sessionRegionCode);
                loadDistricts(sessionRegionCode);
                return;
            }

            if (sessionDistCode) {
                showNoData('Select district and click submit to view institutions.');
                return;
            }

            showNoData('Select region and district, then click submit to view institutions.');
        }

        function loadDistricts(regioncode) {
            resetTable();
            $('#distcode').html('<option value="">Select District</option>');

            if (!regioncode) {
                showNoData('Select region and district, then click submit to view institutions.');
                return;
            }

            $.ajax({
                url: '/readyforaudit/districts',
                type: 'GET',
                data: {
                    regioncode: regioncode
                },
                success: function(response) {
                    if (response.status !== true) {
                        showNoData('Unable to load districts.');
                        return;
                    }

                    response.data.forEach(function(district) {
                        $('#distcode').append(
                            `<option value="${district.distcode}">${district.distename}</option>`
                        );
                    });

                    if (initialDistrictLoad && sessionDistCode) {
                        $('#distcode').val(sessionDistCode);
                    }

                    initialDistrictLoad = false;

                    showNoData('Select district and click submit to view institutions.');
                },
                error: function() {
                    showNoData('Unable to load districts.');
                }
            });
        }

        function loadInstitutions() {
            const deptcode = $('#deptcode').val();
            const regioncode = $('#regioncode').val();
            const distcode = $('#distcode').val();

            if (!deptcode || !regioncode || !distcode) {
                showNoData(regioncode ? 'Select district and click submit to view institutions.' :
                    'Select region and district, then click submit to view institutions.');
                return;
            }

            $.ajax({
                url: '/readyforaudit/list',
                type: 'GET',
                data: {
                    deptcode: deptcode,
                    regioncode: regioncode,
                    distcode: distcode
                },
                success: function(response) {
                    if (response.requiresDistrict) {
                        showNoData('Select district to view institutions.');
                        return;
                    }

                    if (response.finalised) {
                        showNoData('Plan Completed');
                        return;
                    }

                    if (!response.data || response.data.length === 0) {
                        showNoData('No institutions found.');
                        return;
                    }

                    $('#nodata').hide();
                    $('#readyforauditform').show();
                    resetTable();

                    table = $('#institutionTable').DataTable({
                        processing: true,
                        serverSide: false,
                        paging: true,
                        searching: true,
                        info: true,
                        pageLength: 10,
                        lengthChange: false,
                        data: response.data,
                        columns: [{
                                data: null,
                                orderable: false,
                                render: function(data, type, row) {
                                    const checked = row.finaliseflag == 'Y' ? 'checked' : '';

                                    return `<input type="checkbox" class="instCheckbox" name="instid[]" value="${row.readyforauditid}" ${checked}>
                                    <input type="hidden" name="allinstid[]" value="${row.readyforauditid}">`;
                                }
                            },
                            {
                                data: 'instename'
                            },
                            {
                                data: 'financialyear'
                            },
                            {
                                data: 'mandays'
                            },
                            {
                                data: 'teamsize'
                            }
                        ]
                    });
                },
                error: function() {
                    showNoData('Error fetching institutions.');
                }
            });
        }

        function resetTable() {
            if ($.fn.DataTable.isDataTable('#institutionTable')) {
                $('#institutionTable').DataTable().clear().destroy();
            }

            table = null;
            $('#instTable').empty();
        }

        function showNoData(message) {
            resetTable();
            $('#readyforauditform').hide();
            $('#nodata').show();
            $('#del').html(message);
        }

        function uncheckalldata() {
            if (!table) {
                return;
            }

            table.$('.instCheckbox').prop('checked', false);
        }

        function confirmation_saveData() {
            if (!table) {
                return false;
            }

            const checkedCount = table.$("input[name='instid[]']:checked").length;

            if (checkedCount === 0) {
                passing_alert_value(
                    'Alert',
                    'Please select at least one institution.',
                    'confirmation_alert',
                    'alert_header',
                    'alert_body',
                    'confirmation_alert'
                );

                return false;
            }

            const alert_content = `Are you sure you want to include this institution in {{ $planname ?? '' }}?`;

            passing_alert_value(
                'Confirmation',
                alert_content,
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'forward_alert'
            );

            $('#confirmation_alert').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#process_button').off('click').on('click', function() {
                saveData();
            });
        }

        function saveData() {
            const selectedIds = [];
            const deptcode = $('#deptcode').val();
            const regioncode = $('#regioncode').val();
            const distcode = $('#distcode').val();

            table.$("input[name='instid[]']:checked").each(function() {
                selectedIds.push($(this).val());
            });

            $.ajax({
                url: '/readyforaudit/save',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    deptcode: deptcode,
                    instid: selectedIds,
                    regioncode: regioncode,
                    distcode: distcode
                },
                success: function(response) {
                    if (response.status === true) {
                        passing_alert_value(
                            'Confirmation',
                            'Please redo the plan. The institution will be included only after redo.',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                        loadInstitutions();
                    }
                },
                error: function(xhr) {
                    passing_alert_value(
                        'Alert',
                        xhr.responseJSON?.message || 'Error while saving.',
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            });
        }
    </script>
@endsection
