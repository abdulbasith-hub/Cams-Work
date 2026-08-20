@extends('index2')

@section('title', 'Helpdesk Dashboard')

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

    @php use Illuminate\Support\Str; @endphp
    <div class="helpdesk-app-theme">
        <div class="helpdesk-main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title mb-1">
                        <i class="bi bi-speedometer2 text-primary"></i> Helpdesk Dashboard
                    </h1>
                    {{-- <p class="text-muted mb-0">{{ $helpdeskRoleLabel }} view for the current CAMS session user.</p> --}}
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-primary">View Tickets</a>
                    {{-- <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-primary text-light">Create Ticket</a> --}}
                </div>
            </div>

            <div class="row mb-4">
                @foreach ($stats as $label => $value)
                    <div class="col-md-3 mb-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h3>{{ $value }}</h3>
                            <p>{{ Str::headline($label) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-light">Recent Tickets</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Ticket</th>
                                            <th>Subject</th>
                                            <th>Department</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentTickets as $ticket)
                                            <tr>
                                                <td><a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-decoration-none fw-bold text-dark">{{ $ticket->ticket_number }}</a></td>
                                                <td>{{ $ticket->subject }}</td>
                                                <td>{{ $ticket->department_name ?: '-' }}</td>
                                                <td><span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></td>
                                                <td><span class="badge badge-status-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                                                <td>{{ $ticket->created_at->format('D, d/m/Y h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No tickets available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 text-light">Priority Split</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($ticketsByPriority as $item)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <span class="fw-semibold">{{ ucfirst($item->priority) }}</span>
                                    <span class="badge badge-priority-{{ $item->priority }}">{{ $item->count }}</span>
                                </div>
                            @empty
                                <div class="text-muted">No data available.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
