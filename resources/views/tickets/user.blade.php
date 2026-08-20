@extends('index2')

@section('title', 'My Dashboard')

@section('content')
@include('tickets.partials.app-theme')
<style>
    .helpdesk-app-theme .stat-card {
        color: #fff;
        border: none;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .helpdesk-app-theme .stat-card h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .helpdesk-app-theme .stat-card p {
        margin: 0;
        opacity: 0.92;
    }

    .helpdesk-app-theme .stat-card-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .helpdesk-app-theme .stat-card-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .helpdesk-app-theme .stat-card-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .helpdesk-app-theme .stat-card-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .helpdesk-app-theme .stat-card-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .helpdesk-app-theme .stat-card-dark {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }

    .helpdesk-app-theme .badge-priority-low {
        background: #dbeafe;
        color: #1e40af;
    }

    .helpdesk-app-theme .badge-priority-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .helpdesk-app-theme .badge-priority-high {
        background: #fed7aa;
        color: #9a3412;
    }

    .helpdesk-app-theme .badge-priority-urgent {
        background: #fecaca;
        color: #991b1b;
    }

    .helpdesk-app-theme .badge-status-open {
        background: #dbeafe;
        color: #1e40af;
    }

    .helpdesk-app-theme .badge-status-in_progress {
        background: #fef3c7;
        color: #92400e;
    }

    .helpdesk-app-theme .badge-status-resolved {
        background: #d1fae5;
        color: #065f46;
    }

    .helpdesk-app-theme .badge-status-closed {
        background: #e5e7eb;
        color: #374151;
    }
</style>

@php
    use App\Support\HelpdeskSession;
    use Illuminate\Support\Str;
    $isDeveloper = HelpdeskSession::isDeveloper();
@endphp
<div class="helpdesk-app-theme">
    <div class="helpdesk-main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">
            <i class="bi bi-speedometer2 text-primary"></i> My Dashboard
        </h1>
        <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Ticket
        </a>
    </div>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h3>{{ $stats['total_tickets'] }}</h3>
                <p><i class="bi bi-ticket-perforated"></i> Total Tickets</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>{{ $stats['open_tickets'] }}</h3>
                <p><i class="bi bi-inbox"></i> Open Tickets</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>{{ $stats['in_progress_tickets'] }}</h3>
                <p><i class="bi bi-hourglass-split"></i> In Progress</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3>{{ $stats['resolved_tickets'] }}</h3>
                <p><i class="bi bi-check-circle"></i> Resolved</p>
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="card">
        <div class="card-header  d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> My Recent Tickets</h5>
            <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-sm btn-light">View All</a>
        </div>
        <div class="card-body">
            @if($recentTickets->count() > 0)
            <div class="table-responsive">
                <table class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang align-middle text-center">Ticket #</th>
                            <th class="lang align-middle text-center">Subject</th>
                            <th class="lang align-middle text-center">Department</th>
                            <th class="lang align-middle text-center">Priority</th>
                            <th class="lang align-middle text-center">Status</th>
                            <th class="lang align-middle text-center">Created</th>
                            <th class="lang align-middle text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-decoration-none fw-bold" style="color:black">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td>{{ Str::limit($ticket->subject, 40) }}</td>
                            <td>{{ $ticket->department_name ?: 'N/A' }}</td>
                            <td>
                                <span class="badge badge-priority-{{ $ticket->priority }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-status-{{ $ticket->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d/n/Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="mt-3 text-muted">You haven't created any tickets yet.</p>
                <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create Your First Ticket
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
