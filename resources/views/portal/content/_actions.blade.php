<div class="d-flex gap-1 flex-wrap">
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="portalShowHistory({{ $row->id }})">
        <i class="bi bi-clock-history"></i>
    </button>

    @if ($isInitiator && $isOwner && in_array($row->status, ['draft', 'rejected'], true))
        <a href="{{ route('portal.content.edit', $row->id) }}" class="btn btn-sm btn-primary">Edit</a>
        <button type="button" class="btn btn-sm btn-success" onclick="portalSubmit({{ $row->id }})">Submit</button>
        @if ($row->status === 'draft')
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="portalDestroy({{ $row->id }})">Delete</button>
        @endif
    @endif

    @if ($isInitiator && $row->status === 'approved')
        <a href="{{ route('portal.content.edit', $row->id) }}" class="btn btn-sm btn-primary">Edit</a>
    @endif

    @if ($isApprover && $row->status === 'pending')
        <button type="button" class="btn btn-sm btn-success" onclick="portalApprove({{ $row->id }})">Approve</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="portalReject({{ $row->id }})">Reject</button>
    @endif

    @if ($isApprover && $row->status === 'approved')
        <button type="button" class="btn btn-sm btn-outline-warning" onclick="portalToggleActive({{ $row->id }})">
            {{ $row->statusflag === 'Y' ? 'Deactivate' : 'Activate' }}
        </button>
    @endif
</div>
