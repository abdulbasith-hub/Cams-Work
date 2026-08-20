@extends('index2')

@section('title', 'DGA Portal Content')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="page-title mb-0"><i class="bi bi-layout-text-window-reverse text-primary"></i> DGA Portal Content</h1>
        <div class="d-flex gap-2 align-items-center">
            @if ($isApprover || $isInitiator)
                <select id="statusFilter" class="form-select form-select-sm" style="width:auto">
                    <option value="" @selected(request('status') === null || request('status') === '')>All statuses</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                </select>
            @endif
            @if ($isInitiator)
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">Add new</button>
                    <ul class="dropdown-menu dropdown-menu-end" style="max-height:70vh;overflow:auto;">
                        @if (!$departmentScope)
                            @foreach ($sectionCatalog['home'] ?? [] as $sectionKey => $section)
                                <li><a class="dropdown-item" href="{{ route(!empty($section['singleton']) ? 'portal.content.create' : 'portal.content.manage', ['page_key' => 'home', 'section_key' => $sectionKey]) }}">{{ $section['label'] }} (Home)</a></li>
                            @endforeach
                        @endif
                        @foreach ($departmentSlugs as $slug)
                            @continue($departmentScope && $slug !== $departmentScope)
                            <li><h6 class="dropdown-header">{{ strtoupper($slug) }} department</h6></li>
                            @foreach ($sectionCatalog['department'] ?? [] as $sectionKey => $section)
                                <li><a class="dropdown-item" href="{{ route(!empty($section['singleton']) ? 'portal.content.create' : 'portal.content.manage', ['page_key' => 'department', 'page_scope' => $slug, 'section_key' => $sectionKey]) }}">{{ $section['label'] }}</a></li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            @endif
            <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
        </div>
    </div>

    <div class="table-responsive">
        <table id="portalContentTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Page</th>
                    <th>Section</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Created by</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="portalHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Content history</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="portalHistoryBody">Loading...</div>
            </div>
        </div>
    </div>

    <script>
        let portalTable;

        function portalReload() {
            portalTable.ajax.reload(null, false);
        }

        $(function () {
            portalTable = $('#portalContentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('portal.content.data') }}",
                    data: function (d) {
                        d.status = $('#statusFilter').val() || '';
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'page', name: 'page' },
                    { data: 'section_key', name: 'section_key' },
                    { data: 'title', name: 'title', defaultContent: '-' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'created_by_name', name: 'created_by_name', defaultContent: '-' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#statusFilter').on('change', portalReload);
        });

        function portalShowHistory(id) {
            $('#portalHistoryBody').html('Loading...');
            const modal = new bootstrap.Modal(document.getElementById('portalHistoryModal'));
            modal.show();
            $.get("{{ url('portal/content') }}/" + id + "/history", function (res) {
                if (!res.success || !res.history.length) {
                    $('#portalHistoryBody').html('<p class="text-muted mb-0">No history yet.</p>');
                    return;
                }
                let html = '<ul class="list-group">';
                res.history.forEach(function (h) {
                    html += '<li class="list-group-item">' +
                        '<strong>' + h.action_key + '</strong> by ' + (h.performed_by_name || h.performed_by_email || 'system') +
                        '<br><small class="text-muted">' + h.performed_at + '</small>' +
                        (h.remarks ? '<br>' + h.remarks : '') +
                        '</li>';
                });
                html += '</ul>';
                $('#portalHistoryBody').html(html);
            });
        }

        function portalAjax(url, method, data, successMessage) {
            $.ajax({
                url: url,
                method: method,
                data: Object.assign({ _token: '{{ csrf_token() }}' }, data || {}),
                success: function (res) {
                    if (res.success) {
                        portalReload();
                    } else {
                        alert(res.message || 'Something went wrong.');
                    }
                },
                error: function (xhr) {
                    const res = xhr.responseJSON || {};
                    alert(res.message || 'Something went wrong.');
                }
            });
        }

        function portalSubmit(id) {
            if (!confirm('Submit this item for approval?')) return;
            portalAjax("{{ url('portal/content') }}/" + id + "/submit", 'POST', {});
        }

        function portalApprove(id) {
            if (!confirm('Approve and publish this item?')) return;
            portalAjax("{{ url('portal/content') }}/" + id + "/approve", 'POST', {});
        }

        function portalReject(id) {
            const remarks = prompt('Reason for rejection:');
            if (!remarks) return;
            portalAjax("{{ url('portal/content') }}/" + id + "/reject", 'POST', { remarks: remarks });
        }

        function portalToggleActive(id) {
            portalAjax("{{ url('portal/content') }}/" + id + "/toggle-active", 'POST', {});
        }

        function portalDestroy(id) {
            if (!confirm('Delete this draft? This cannot be undone.')) return;
            portalAjax("{{ url('portal/content') }}/" + id, 'DELETE', {});
        }
    </script>
@endsection
