@php
    use App\Services\HelpdeskV2Session;
    $navRole = $role ?? HelpdeskV2Session::role();
@endphp

<div class="hdv2-topbar">
    <div>
        <div class="hdv2-eyebrow">Helpdesk V2</div>
        <h1>{{ $title ?? 'Ticket Management' }}</h1>
    </div>
    <div class="hdv2-actions">
        @if (($showDashboardSwitch ?? false) && ($canViewTaskDashboard ?? false))
            <div class="hdv2-dashboard-switch" role="tablist" aria-label="Dashboard view">
                <button type="button" class="{{ ($dashboardPane ?? 'tickets') === 'tickets' ? 'is-active' : '' }}" data-hdv2-dashboard-pane-switch="tickets" role="tab" aria-selected="{{ ($dashboardPane ?? 'tickets') === 'tickets' ? 'true' : 'false' }}">
                    <span></span>
                    <i class="ti ti-ticket"></i>
                    Tickets
                </button>
                <button type="button" class="{{ ($dashboardPane ?? 'tickets') === 'tasks' ? 'is-active' : '' }}" data-hdv2-dashboard-pane-switch="tasks" role="tab" aria-selected="{{ ($dashboardPane ?? 'tickets') === 'tasks' ? 'true' : 'false' }}">
                    <span></span>
                    <i class="ti ti-clipboard-list"></i>
                    Tasks
                </button>
            </div>
        @endif
        <a href="{{ route('helpdesk-v2.tickets.create') }}" class="btn btn-primary">New Ticket</a>
    </div>
</div>
