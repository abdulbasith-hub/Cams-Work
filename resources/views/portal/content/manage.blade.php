@extends('index2')

@section('title', 'Manage ' . $config['label'] . ' · DGA Portal')

@section('content')
    @php
        $inlineEditable = collect($config['fields'])->every(fn ($field) => $field['type'] !== 'file');
        $pageLabel = $pageKey === 'department' ? strtoupper($pageScope) . ' department' : 'Home page';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title mb-0">Manage — {{ $config['label'] }}</h1>
            <small class="text-muted">{{ $pageLabel }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('portal.content.index') }}" class="btn btn-outline-secondary">Full content list</a>
            <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
        </div>
    </div>

    @if ($sectionKey === 'nav_menu' && $pageKey === 'department')
        <div class="card mb-3">
            <div class="card-header">Each menu item links to one of these — manage its actual content here</div>
            <div class="card-body d-flex flex-wrap gap-2">
                @foreach (\App\Models\PortalModel::fixedNavTargets() as $anchor => $target)
                    <a class="btn btn-sm btn-outline-primary" href="{{ route($target['singleton'] ? 'portal.content.create' : 'portal.content.manage', ['page_key' => 'department', 'page_scope' => $pageScope, 'section_key' => $target['section_key']]) }}">
                        {{ $target['label'] }}
                    </a>
                @endforeach
                <a class="btn btn-sm btn-outline-success" href="{{ route('portal.content.manage', ['page_key' => 'department', 'page_scope' => $pageScope, 'section_key' => 'custom_block']) }}">
                    + Create new custom block
                </a>
            </div>
        </div>
    @endif

    <div id="manageRows">
        @forelse ($items as $item)
            @php
                $isOwner = $currentEmail && strcasecmp((string) $item->created_by_email, (string) $currentEmail) === 0;
                $canEditRow = $inlineEditable && $isOwner && in_array($item->status, ['draft', 'rejected'], true);
                $canEditApproved = $inlineEditable && $isOwner && $item->status === 'approved';
            @endphp
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="d-flex align-items-start gap-2">
                        <div class="flex-grow-1">
                            @if ($inlineEditable)
                                <div class="row g-2">
                                    @foreach ($config['fields'] as $key => $field)
                                        <div class="col-md-{{ 12 / max(count($config['fields']), 1) }}">
                                            @if ($field['type'] === 'select')
                                                <select class="form-select form-select-sm manage-f" data-f="{{ $key }}" data-id="{{ $item->id }}" @disabled(!$canEditRow && !$canEditApproved)>
                                                    @foreach ($field['options'] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" @selected(($item->content[$key] ?? '') === $optionValue)>{{ $optionLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif ($field['type'] === 'parent_select')
                                                <select class="form-select form-select-sm manage-f" data-f="{{ $key }}" data-id="{{ $item->id }}" @disabled(!$canEditRow && !$canEditApproved)>
                                                    <option value="">(top level)</option>
                                                    @foreach ($items as $other)
                                                        @continue($other->id === $item->id)
                                                        <option value="{{ $other->id }}" @selected((int) ($item->content['parent_id'] ?? 0) === $other->id)>{{ $other->content['label'] ?? ('#' . $other->id) }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif ($field['type'] === 'target_select')
                                                <select class="form-select form-select-sm manage-f" data-f="{{ $key }}" data-id="{{ $item->id }}" @disabled(!$canEditRow && !$canEditApproved)>
                                                    @foreach ($navTargetOptions ?? [] as $optionValue => $optionLabel)
                                                        <option value="{{ $optionValue }}" @selected(($item->content[$key] ?? '') === $optionValue)>{{ $optionLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif ($field['type'] === 'textarea')
                                                <textarea class="form-control form-control-sm manage-f" data-f="{{ $key }}" data-id="{{ $item->id }}" placeholder="{{ $field['label'] }}" rows="3" @disabled(!$canEditRow && !$canEditApproved)>{{ $item->content[$key] ?? '' }}</textarea>
                                            @else
                                                <input type="{{ $field['type'] === 'date' ? 'date' : 'text' }}" class="form-control form-control-sm manage-f" data-f="{{ $key }}" data-id="{{ $item->id }}" placeholder="{{ $field['label'] }}" value="{{ $item->content[$key] ?? '' }}" @disabled(!$canEditRow && !$canEditApproved)>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>{{ $item->content['title'] ?? json_encode($item->content) }}</div>
                            @endif
                            @if ($item->status === 'rejected' && $item->remarks)
                                <small class="text-danger d-block mt-1">Rejected: {{ $item->remarks }}</small>
                            @endif
                            @if ($canEditApproved)
                                <small class="text-muted d-block mt-1">Saving here creates a new pending version — the live version stays until it's approved.</small>
                            @endif
                        </div>
                        <div class="text-nowrap">
                            @php
                                $badgeClass = ['draft' => 'secondary', 'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$item->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                            @if ($item->status === 'approved')
                                <span class="badge {{ $item->statusflag === 'Y' ? 'bg-info' : 'bg-light text-dark' }}">{{ $item->statusflag === 'Y' ? 'Active' : 'Inactive' }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-1 mt-2">
                        @if ($canEditRow || $canEditApproved)
                            <button type="button" class="btn btn-sm btn-primary manage-save" data-id="{{ $item->id }}">Save</button>
                        @endif
                        @if ($canEditRow)
                            <button type="button" class="btn btn-sm btn-success manage-submit" data-id="{{ $item->id }}">Submit</button>
                        @endif
                        @if ($isOwner && $item->status === 'draft')
                            <button type="button" class="btn btn-sm btn-outline-danger manage-delete" data-id="{{ $item->id }}">Delete</button>
                        @endif
                        @if ($isApprover && $item->status === 'approved')
                            <button type="button" class="btn btn-sm btn-outline-warning manage-toggle" data-id="{{ $item->id }}">{{ $item->statusflag === 'Y' ? 'Remove (deactivate)' : 'Restore (activate)' }}</button>
                        @endif
                        @if (!$inlineEditable && $isOwner && in_array($item->status, ['draft', 'rejected'], true))
                            <a href="{{ route('portal.content.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Nothing here yet.</p>
        @endforelse
    </div>

    @if ($canAdd)
        <div class="card mt-3">
            <div class="card-header">Add new</div>
            <div class="card-body">
                <div class="row g-2" id="manageAddFields">
                    @foreach ($config['fields'] as $key => $field)
                        <div class="col-md-{{ 12 / max(count($config['fields']), 1) }}">
                            @if ($field['type'] === 'select')
                                <select class="form-select form-select-sm manage-add-f" data-f="{{ $key }}">
                                    @foreach ($field['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif ($field['type'] === 'parent_select')
                                <select class="form-select form-select-sm manage-add-f" data-f="{{ $key }}">
                                    <option value="">(top level)</option>
                                    @foreach ($items as $other)
                                        <option value="{{ $other->id }}">{{ $other->content['label'] ?? ('#' . $other->id) }}</option>
                                    @endforeach
                                </select>
                            @elseif ($field['type'] === 'target_select')
                                <select class="form-select form-select-sm manage-add-f" data-f="{{ $key }}">
                                    @foreach ($navTargetOptions ?? [] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif ($field['type'] === 'textarea')
                                <textarea class="form-control form-control-sm manage-add-f" data-f="{{ $key }}" placeholder="{{ $field['label'] }}" rows="3"></textarea>
                            @else
                                <input type="{{ $field['type'] === 'date' ? 'date' : 'text' }}" class="form-control form-control-sm manage-add-f" data-f="{{ $key }}" placeholder="{{ $field['label'] }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-primary mt-2" id="manageAddBtn">+ Add</button>
            </div>
        </div>
    @elseif ($canManageSection)
        <a href="{{ route('portal.content.create', ['page_key' => $pageKey, 'page_scope' => $pageScope, 'section_key' => $sectionKey]) }}" class="btn btn-primary mt-3">+ Add new (full form)</a>
    @endif

    <script>
        function manageAjax(url, method, data) {
            $.ajax({
                url: url,
                method: method,
                data: Object.assign({ _token: '{{ csrf_token() }}' }, data || {}),
                success: function (res) {
                    if (res.success) {
                        window.location.reload();
                    } else {
                        alert(res.message || 'Something went wrong.');
                    }
                },
                error: function (xhr) {
                    const res = xhr.responseJSON || {};
                    let message = res.message || 'Something went wrong.';
                    if (res.errors) message += '\n' + Object.values(res.errors).flat().join('\n');
                    alert(message);
                }
            });
        }

        function manageCollectRow(id) {
            const data = {};
            $('.manage-f[data-id="' + id + '"]').each(function () {
                data[$(this).data('f')] = $(this).val();
            });
            return data;
        }

        $(document).on('click', '.manage-save', function () {
            const id = $(this).data('id');
            manageAjax("{{ url('portal/content') }}/" + id, 'POST', manageCollectRow(id));
        });

        $(document).on('click', '.manage-submit', function () {
            if (!confirm('Submit this for approval?')) return;
            const id = $(this).data('id');
            manageAjax("{{ url('portal/content') }}/" + id + "/submit", 'POST', {});
        });

        $(document).on('click', '.manage-delete', function () {
            if (!confirm('Delete this draft?')) return;
            manageAjax("{{ url('portal/content') }}/" + $(this).data('id'), 'DELETE', {});
        });

        $(document).on('click', '.manage-toggle', function () {
            manageAjax("{{ url('portal/content') }}/" + $(this).data('id') + '/toggle-active', 'POST', {});
        });

        $('#manageAddBtn').on('click', function () {
            const data = { page_key: '{{ $pageKey }}', page_scope: '{{ $pageScope }}', section_key: '{{ $sectionKey }}' };
            $('.manage-add-f').each(function () {
                data[$(this).data('f')] = $(this).val();
            });
            manageAjax("{{ route('portal.content.store') }}", 'POST', data);
        });
    </script>
@endsection
