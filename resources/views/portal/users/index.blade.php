@extends('index2')

@section('title', 'DGA Portal Users')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="page-title mb-0"><i class="bi bi-people text-primary"></i> DGA Portal Users</h1>
        <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">Assign a role</div>
        <div class="card-body">
            <form id="assignRoleForm" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">CAMS login email</label>
                    <input type="email" id="assignEmail" class="form-control" required placeholder="user@example.gov.in">
                    <small id="assignEmailStatus" class="text-muted"></small>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select id="assignRole" class="form-select">
                        <option value="initiator">Initiator</option>
                        <option value="approver">Approver</option>
                    </select>
                </div>
                <div class="col-md-2" id="departmentScopeGroup">
                    <label class="form-label">Department</label>
                    <select id="assignDepartmentScope" class="form-select">
                        <option value="">All (unrestricted)</option>
                        @foreach ($departmentSlugs as $slug)
                            <option value="{{ $slug }}">{{ strtoupper($slug) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Assign role</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table id="portalUsersTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Assigned by</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        let portalUsersTable;

        $(function () {
            portalUsersTable = $('#portalUsersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('portal.users.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'display_name', name: 'display_name', defaultContent: '-' },
                    { data: 'login_email', name: 'login_email' },
                    { data: 'role', name: 'role' },
                    { data: 'department_scope', name: 'department_scope', defaultContent: 'All' },
                    { data: 'status_badge', name: 'statusflag' },
                    { data: 'assigned_by_name', name: 'assigned_by_name', defaultContent: '-' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#assignEmail').on('blur', function () {
                const email = $(this).val();
                if (!email) return;
                $('#assignEmailStatus').text('Checking...');
                $.post("{{ route('portal.users.lookup') }}", { email: email, _token: '{{ csrf_token() }}' }, function (res) {
                    if (res.success) {
                        $('#assignEmailStatus').removeClass('text-danger').addClass('text-success')
                            .text('Found: ' + (res.identity.display_name || res.identity.login_email));
                        if (res.suggested_department) {
                            $('#assignDepartmentScope').val(res.suggested_department);
                        }
                    } else {
                        $('#assignEmailStatus').removeClass('text-success').addClass('text-danger')
                            .text(res.message || 'No matching CAMS login found.');
                    }
                });
            });

            $('#assignRoleForm').on('submit', function (event) {
                event.preventDefault();
                $.ajax({
                    url: "{{ route('portal.users.store') }}",
                    method: 'POST',
                    data: {
                        login_email: $('#assignEmail').val(),
                        role: $('#assignRole').val(),
                        department_scope: $('#assignDepartmentScope').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        alert(res.message);
                        if (res.success) {
                            $('#assignEmail').val('');
                            $('#assignEmailStatus').text('');
                            $('#assignDepartmentScope').val('');
                            portalUsersTable.ajax.reload(null, false);
                        }
                    },
                    error: function (xhr) {
                        const res = xhr.responseJSON || {};
                        alert(res.message || 'Failed to assign role.');
                    }
                });
            });
        });

        function portalUserToggle(id) {
            $.post("{{ url('portal/users') }}/" + id + "/toggle", { _token: '{{ csrf_token() }}' }, function (res) {
                portalUsersTable.ajax.reload(null, false);
            });
        }

        function portalUserChangeRole(id, currentRole) {
            const newRole = currentRole === 'initiator' ? 'approver' : 'initiator';
            if (!confirm('Change role to ' + newRole + '?')) return;
            $.post("{{ url('portal/users') }}/" + id + "/role", { role: newRole, _token: '{{ csrf_token() }}' }, function (res) {
                portalUsersTable.ajax.reload(null, false);
            });
        }
    </script>
@endsection
