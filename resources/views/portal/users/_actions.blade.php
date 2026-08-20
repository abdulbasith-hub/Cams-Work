<div class="d-flex gap-1">
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="portalUserChangeRole({{ $row->id }}, '{{ $row->role }}')">
        Switch to {{ $row->role === 'initiator' ? 'approver' : 'initiator' }}
    </button>
    <button type="button" class="btn btn-sm {{ $row->statusflag === 'Y' ? 'btn-outline-danger' : 'btn-outline-success' }}" onclick="portalUserToggle({{ $row->id }})">
        {{ $row->statusflag === 'Y' ? 'Revoke' : 'Restore' }}
    </button>
</div>
