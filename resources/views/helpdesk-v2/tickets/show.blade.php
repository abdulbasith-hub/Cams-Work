@extends('index2')

@section('title', 'Helpdesk V2 Ticket')

@section('content')
    @include('helpdesk-v2.partials.assets')
    <div class="hdv2 hdv2-detail-page hdv2-task-detail-page hdv2-ticket-task-page">
        @include('helpdesk-v2.partials.flashes')
        @php
            $currentOnMeta = $ticket->currentOnMetaForRole($role);
            $ticketNature = 'New Ticket';

            if ((int) $ticket->reopen_count > 0) {
                $ticketNature = 'Reopened Ticket';
            } elseif (\Illuminate\Support\Str::startsWith((string) $ticket->status, 'returned')) {
                $ticketNature = 'Returned Ticket';
            }

            $showUpdatedOn = $ticket->updated_at
                && (! $ticket->created_at || ! $ticket->updated_at->equalTo($ticket->created_at));
            $ticketLocked = in_array($ticket->status, [
                'resolved',
                \App\Models\HelpdeskV2Ticket::STATUS_CLOSED,
                \App\Models\HelpdeskV2Ticket::STATUS_REJECTED,
                \App\Models\HelpdeskV2Ticket::STATUS_CANCELLED,
            ], true);
        @endphp

        <div class="hdv2-task-head">
            <div>
                <div class="hdv2-eyebrow">Helpdesk V2 Ticket</div>
                <h1>Ticket Details</h1>
            </div>
            <div class="hdv2-actions">
                <a href="{{ route('helpdesk-v2.dashboard', $role) }}" class="btn btn-light">Dashboard</a>
                <a href="{{ route('helpdesk-v2.tickets.index', ['role' => $role]) }}" class="btn btn-light">Tickets</a>
                <a href="{{ route('helpdesk-v2.tickets.create') }}" class="btn btn-primary">New Ticket</a>
            </div>
        </div>

        <div class="hdv2-task-layout">
            <main class="hdv2-task-main">
                <section class="hdv2-task-section hdv2-task-record-section">
                    <div class="hdv2-task-section-head"><h2>Ticket Information</h2></div>
	                    <div class="hdv2-task-sheet hdv2-ticket-sheet hdv2-ticket-overview">
	                        <div class="hdv2-task-brief hdv2-ticket-task-brief">
	                            <div class="hdv2-task-title hdv2-ticket-title-block">
	                                <span>{{ $ticket->ticket_number }}</span>
	                                <h3>{{ $ticket->subject }}</h3>
                                </div>
                                <div class="hdv2-task-state">
                                <div class="hdv2-ticket-chip-row">
                                    <span class="hdv2-badge hdv2-priority-{{ strtolower((string) $ticket->priority) }}">{{ $ticket->priority }}</span>
                                    <span class="hdv2-badge hdv2-status-{{ $ticket->mainStatusKey() }}">{{ $ticket->mainStatusLabel() }}</span>
                                    <span class="hdv2-soft-chip">{{ $ticketNature }}</span>
                                    @if ($ticket->reopen_count > 0)
                                        <span class="hdv2-badge hdv2-reopened">REOPENED {{ $ticket->reopen_count }}</span>
                                    @elseif (in_array($ticket->status, [\App\Models\HelpdeskV2Ticket::STATUS_RETURNED_TO_DEVELOPER, 'returned_to_developer'], true))
                                        <span class="hdv2-badge hdv2-returned">RETURNED TO DEVELOPER</span>
                                    @elseif ($ticket->status === 'returned_to_tester')
                                        <span class="hdv2-badge hdv2-returned">RETURNED TO TESTER</span>
	                                    @elseif ($ticket->status === 'testing_in_progress' && $ticket->comments->filter(fn ($comment) => str_contains(strtoupper((string) $comment->comment), 'RETURN_TO_TESTER'))->isNotEmpty())
	                                        <span class="hdv2-badge hdv2-retesting">RETESTING</span>
	                                    @endif
	                                </div>
	                            </div>
	                        </div>

	                        <div class="hdv2-task-record-grid hdv2-ticket-info-grid">
	                            <div class="hdv2-task-record-item hdv2-ticket-info-card">
	                                <span>Created By</span>
	                                <strong>{{ $ticket->created_by_name ?: '-' }}</strong>
	                                <small>{{ $ticket->created_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->created_at) : '-' }}</small>
	                            </div>
	                            <div class="hdv2-task-record-item hdv2-ticket-info-card">
	                                <span>Request Type</span>
	                                <strong>{{ $ticket->request_type_label }}</strong>
	                            </div>
	                            <div class="hdv2-task-record-item hdv2-ticket-info-card">
	                                <span>Category</span>
	                                <strong>{{ $ticket->category ?: '-' }}</strong>
	                            </div>
	                            <div class="hdv2-task-record-item hdv2-ticket-info-card hdv2-ticket-info-card-wide">
	                                <span>Institution</span>
	                                <strong>{{ $ticket->institution ?: '-' }}</strong>
	                            </div>
	                            <div class="hdv2-task-record-item hdv2-ticket-info-card hdv2-ticket-pending-card">
	                                <span>Pending With</span>
	                                <strong>{{ $ticket->currentOnLabelForRole($role) }}</strong>
	                                @if ($currentOnMeta)
	                                    <small>{{ $currentOnMeta }}</small>
	                                @endif
	                            </div>
	                            <div class="hdv2-task-record-item hdv2-ticket-info-card">
	                                <span>Financial Year</span>
	                                <strong>{{ $ticket->financial_year_label }}</strong>
                            </div>
                            <div class="hdv2-task-record-item hdv2-ticket-info-card">
                                <span>Last Updated</span>
                                <strong>{{ $showUpdatedOn ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->updated_at) : '-' }}</strong>
                                <small>Closed: {{ $ticket->closed_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($ticket->closed_at) : '-' }}</small>
                            </div>
                            @if (in_array($role, ['nic_admin', 'developer', 'layer_lead', 'watchlist'], true))
                                @if ($ticket->hasLayerLeadFlow())
                                    <div class="hdv2-task-record-item hdv2-ticket-info-card">
                                        <span>Senior Developer</span>
                                        <strong>{{ $ticket->layer_lead_name ?: '-' }}</strong>
                                    </div>
                                @endif
                                <div class="hdv2-task-record-item hdv2-ticket-info-card">
                                    <span>Developer</span>
                                    <strong>{{ $ticket->developer_name ?: '-' }}</strong>
                                </div>
                                <div class="hdv2-task-record-item hdv2-ticket-info-card">
                                    <span>Tester</span>
                                    <strong>{{ $ticket->tester_name ?: '-' }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="hdv2-task-detail-lines hdv2-ticket-detail-lines">
                            <section class="hdv2-ticket-description-card">
                                <h4>Description</h4>
                                <p>{{ $ticket->description ?: '-' }}</p>
                            </section>
                            <section class="hdv2-ticket-attachment-card">
                                <h4>Attachments</h4>
                                <div class="hdv2-file-list">
                                    @forelse (($ticket->attachments ?? []) as $index => $attachment)
                                        <a href="{{ route('helpdesk-v2.attachments.download', [$ticket, $index]) }}">
                                            <span>{{ $attachment['original_name'] ?? 'Attachment' }}</span>
                                            <small>{{ number_format(($attachment['size_bytes'] ?? 0) / 1024, 1) }} KB</small>
                                        </a>
                                    @empty
                                        <p class="hdv2-empty">No attachments uploaded.</p>
                                    @endforelse
                                </div>
                            </section>
                        </div>
                    </div>
                </section>

                <section class="hdv2-task-section hdv2-task-flow-section">
                    <div class="hdv2-task-section-head">
                        <h2>Workflow Timeline</h2>
                        <div class="hdv2-task-timeline-actions">
                            <span class="hdv2-flow-current">{{ $ticket->currentOnLabelForRole($role) }}</span>
                            <button type="button"
                                    class="btn btn-primary btn-sm"
                                    data-hdv2-toggle="hdv2TimelineCommentForm"
                                    @disabled($ticketLocked)>Add Comment</button>
                        </div>
                    </div>
                    <div class="hdv2-flow-map hdv2-flow-map-compact">
                        @foreach ($flowSteps as $step)
                            <div class="hdv2-flow-step is-{{ $step->state }}" title="{{ trim($step->label.' - '.$step->caption.(! empty($step->date) ? ' - '.$step->date : '')) }}">
                                <span class="hdv2-flow-dot"></span>
                                <strong>{{ $step->label }}</strong>
                                <small>{{ $step->caption }}</small>
                                @if (! empty($step->date))
                                    <em>{{ $step->date }}</em>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <form method="POST"
                          action="{{ route('helpdesk-v2.tickets.comments.store', $ticket) }}"
                          id="hdv2TimelineCommentForm"
                          class="hdv2-comment-form hdv2-timeline-comment-form"
                          @if (! $errors->has('comment') || $ticketLocked) hidden @endif>
                        @csrf
                        <input type="hidden" name="role" value="{{ $role }}">
                        <textarea name="comment" rows="3" placeholder="Add timeline comment" required @disabled($ticketLocked)></textarea>
                        <div>
                            @if (in_array($role, ['layer_lead', 'developer', 'tester'], true))
                                <input type="hidden" name="visibility" value="developer_to_nic">
                                <span class="hdv2-comment-scope">Visible to NIC Admin</span>
                            @elseif ($role === 'user')
                                <input type="hidden" name="visibility" value="public">
                                <span class="hdv2-comment-scope">Visible to all</span>
                            @else
                                <select name="visibility" @disabled($ticketLocked)>
                                    <option value="public">Public</option>
                                    <option value="internal">Internal</option>
                                </select>
                            @endif
                            <button type="submit" class="btn btn-primary" data-hdv2-submit-once @disabled($ticketLocked)>Add</button>
                        </div>
                    </form>

                    <div class="hdv2-task-timeline">
                        <div class="hdv2-task-timeline-head">
                            <span>Timeline Details</span>
                            <strong>{{ $timeline->count() }}</strong>
                        </div>
	                    <ol class="hdv2-task-timeline-list hdv2-ticket-timeline-list">
	                        @forelse ($timeline as $history)
	                            <li>
                                    <span class="hdv2-task-timeline-number">{{ $loop->iteration }}</span>
	                                <div class="hdv2-task-timeline-content hdv2-timeline-entry is-{{ $history->kind ?? 'normal' }}">
	                                    @php
	                                        $historyRoleLabel = \App\Services\HelpdeskV2Session::roleLabel($history->user_role);
	                                        $historyActorLabel = $history->actor_label ?? (($history->user_name ?: $historyRoleLabel).' ('.$historyRoleLabel.')');
	                                    @endphp
                                        <div>
	                                        <span>{{ $historyActorLabel }}</span>
	                                        <strong>{{ $history->created_at ? \App\Models\HelpdeskV2Ticket::displayDateTime($history->created_at) : '-' }}</strong>
                                        </div>
	                                    <p>{!! $history->comment_html ?? e($history->comment) !!}</p>
                                    @if ($history->source === 'dev' && $history->forwarded)
                                        <small class="hdv2-forwarded">Forwarded to State Admin</small>
                                    @endif
                                    @if ($history->can_forward)
                                        <form method="POST"
                                              action="{{ route('helpdesk-v2.tickets.dev-comments.forward', [$ticket, $history->source_id]) }}"
                                              class="hdv2-timeline-forward-form">
                                            @csrf
                                            <button type="submit"
                                                    class="hdv2-icon-btn"
                                                    title="Forward to State Admin"
                                                    aria-label="Forward to State Admin"
                                                    data-hdv2-confirm="Forward this developer comment to State Admin?">
                                                <i class="ti ti-send"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li><div class="hdv2-task-timeline-content"><p>No timeline comments yet.</p></div></li>
                        @endforelse
                    </ol>
                    </div>
                </section>
            </main>

            <aside class="hdv2-task-action-dock">
                @if ($role === 'nic_admin')
                    @php
                        $nicAssignAction = $actions['assign_developer_from_nic'] ?? null;
                        $nicForwardAction = $actions['forward_to_state'] ?? null;
                        $nicStatusAction = $actions['update_nic_status'] ?? null;
                        $nicForwardNeedsStatusUpdate = ! $nicForwardAction && $nicStatusAction;
                        $assignDevelopers = $layerLeads
                            ->merge($developers)
                            ->unique(fn ($person) => (string) $person->devuserid)
                            ->sortBy('devename')
                            ->values();
                        $currentWatchlist = $ticket->assignments
                            ->where('status', 'watchlist')
                            ->unique(fn ($assignment) => (string) $assignment->developer_userid)
                            ->values();
                        $nicOtherActions = collect($actions)
                            ->except(['assign_developer_from_nic', 'assign_layer_lead', 'forward_to_state', 'update_nic_status'])
                            ->all();
                    @endphp

                    @if ($nicStatusAction)
                        <section class="hdv2-task-action-section hdv2-option-card">
                            <h2>Status</h2>
                            <form method="POST"
                                  action="{{ route('helpdesk-v2.tickets.actions', [$ticket, 'update_nic_status']) }}"
                                  class="hdv2-option-body">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <select name="ticket_status" required>
                                    <option value="">Select status</option>
                                    @foreach (['in_progress', 'resolved', 'need_clarification'] as $statusOption)
                                        <option value="{{ $statusOption }}" @selected($ticket->status === $statusOption)>{{ \App\Models\HelpdeskV2Ticket::labelFor($statusOption) }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="remarks" value="NIC Admin updated the ticket status.">
                                <button type="submit"
                                        class="btn btn-primary"
                                        data-hdv2-confirm="Update ticket status?">Update Status</button>
                                <p>Working-stage resolution will stay separate until StateAdmin confirms the final ticket status.</p>
                            </form>
                        </section>
                    @endif

                    <section class="hdv2-task-action-section hdv2-option-card">
                        <h2>Watchlist</h2>
                        <form method="POST" action="{{ route('helpdesk-v2.tickets.watchlist', $ticket) }}" class="hdv2-option-body">
                            @csrf
                            <input type="hidden" name="role" value="{{ $role }}">
                            <label>Watchlist User
                                <select name="watchlist_userid" required @disabled($ticketLocked || ($watchlistLocked ?? false))>
                                    <option value="">Select User</option>
                                    @foreach ($watchlistDevelopers as $person)
                                        <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <button type="submit"
                                    class="btn hdv2-btn-outline"
                                    data-hdv2-confirm="Add this user to watchlist?"
                                    @disabled($ticketLocked || ($watchlistLocked ?? false))>Add to Watchlist</button>
                            @if ($watchlistLocked ?? false)
                                {{-- <p class="hdv2-inline-warning">Watchlist access is disabled after forwarding to StateAdmin.</p> --}}
                            @endif
                        </form>
                        @if ($currentWatchlist->isNotEmpty())
                            <div class="hdv2-watchlist-current">
                                @foreach ($currentWatchlist as $watchlist)
                                    <form method="POST"
                                          action="{{ route('helpdesk-v2.tickets.watchlist.remove', $ticket) }}"
                                          class="hdv2-watchlist-chip">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $role }}">
                                        <input type="hidden" name="watchlist_userid" value="{{ $watchlist->developer_userid }}">
                                        <span>{{ $watchlist->developer_name ?: 'Watchlist User' }}</span>
                                        <button type="submit"
                                                class="hdv2-icon-btn"
                                                title="Remove from watchlist"
                                                aria-label="Remove from watchlist"
                                                data-hdv2-confirm="Remove this user from watchlist?"
                                                @disabled($ticketLocked || ($watchlistLocked ?? false))>
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @if ($nicAssignAction)
                        <section class="hdv2-task-action-section hdv2-option-card">
                            <h2>Assign to Senior Developer / Developer</h2>
                            <form method="POST"
                                  action="{{ route('helpdesk-v2.tickets.actions', [$ticket, 'assign_developer_from_nic']) }}"
                                  class="hdv2-option-body"
                                  data-hdv2-exclusive-assign>
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <label>Senior Developer (Optional)
                                    <select name="additional_layer_userid" data-hdv2-additional-layer>
                                        <option value="">No senior developer</option>
                                        @foreach ($layerLeads as $person)
                                            <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>Developer
                                    <select name="developer_userid" data-hdv2-developer>
                                        <option value="">Select Developer or Senior Developer</option>
                                        @foreach ($assignDevelopers as $person)
                                            <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <input type="hidden" name="remarks" value="">
                                <button type="submit"
                                        class="btn hdv2-btn-warn"
                                        data-hdv2-confirm="Assign this ticket to selected technical user?">Assign</button>
                            </form>
                        </section>
                    @endif

                    @if ($nicForwardAction || $nicForwardNeedsStatusUpdate)
                        <section class="hdv2-task-action-section hdv2-option-card">
                            <h2>Forward to StateAdmin</h2>
                            <form method="POST"
                                  action="{{ $nicForwardAction ? route('helpdesk-v2.tickets.actions', [$ticket, 'forward_to_state']) : '#' }}"
                                  class="hdv2-option-body">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <select disabled>
                                    <option>StateAdmin</option>
                                </select>
                                <input type="hidden" name="remarks" value="Forwarded to StateAdmin for final confirmation.">
                                <button type="submit"
                                        class="btn btn-primary"
                                        @disabled(! $nicForwardAction)
                                        data-hdv2-confirm="Forward this ticket to StateAdmin?">Forward</button>
                                @if ($nicForwardNeedsStatusUpdate)
                                    <p class="hdv2-inline-warning">Update ticket status before forwarding to StateAdmin.</p>
                                @endif
                            </form>
                        </section>
                    @endif

                    @foreach ($nicOtherActions as $action => $definition)
                        <section class="hdv2-task-action-section hdv2-option-card">
                            <h2>{{ $definition['label'] }}</h2>
                            <form method="POST" action="{{ route('helpdesk-v2.tickets.actions', [$ticket, $action]) }}" class="hdv2-option-body">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                @if (($definition['remarks'] ?? false))
                                    <label>Remarks
                                        <textarea name="remarks" rows="3" required></textarea>
                                    </label>
                                @else
                                    <input type="hidden" name="remarks" value="">
                                @endif
                                <button type="submit" class="btn btn-primary" data-hdv2-confirm="{{ $definition['label'] }}?">{{ $definition['label'] }}</button>
                            </form>
                        </section>
                    @endforeach
                @elseif (! empty($actions))
                    <section class="hdv2-task-action-section hdv2-panel">
                        <div class="hdv2-panel-head"><h2>Valid Actions</h2></div>
		                        @forelse ($actions as $action => $definition)
		                            <form method="POST" action="{{ route('helpdesk-v2.tickets.actions', [$ticket, $action]) }}" class="hdv2-action-form">
		                                @csrf
		                                <input type="hidden" name="role" value="{{ $role }}">
		                                @if (($definition['status_change'] ?? false))
	                                    <label>Status
	                                        <select name="ticket_status" required>
	                                            <option value="">Select status</option>
	                                            @foreach (($definition['allowed_statuses'] ?? []) as $statusOption)
	                                                <option value="{{ $statusOption }}" @selected(old('ticket_status') === $statusOption)>{{ \App\Models\HelpdeskV2Ticket::labelFor($statusOption) }}</option>
	                                            @endforeach
	                                        </select>
	                                        @error('ticket_status')
	                                            <span class="hdv2-field-error">{{ $message }}</span>
	                                        @enderror
	                                    </label>
	                                @endif

	                                @if (($definition['assign'] ?? null) === 'layer_lead')
		                                    <label>Senior Developer
                                        <select name="layer_lead_userid" required>
                                            <option value="">Select</option>
                                            @foreach ($layerLeads as $person)
                                                <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                @endif

                                @if (($definition['assign'] ?? null) === 'developer')
                                    <label>Developer
                                        <select name="developer_userid" required>
                                            <option value="">Select</option>
                                            @foreach ($developers as $person)
                                                <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label>Expected Date
                                        <input type="date" name="expected_completion_date">
                                    </label>
                                @endif

                                @if (($definition['assign'] ?? null) === 'tester')
                                    <label>Tester
                                        <select name="tester_userid" required>
                                            <option value="">Select</option>
                                            @foreach ($testers as $person)
                                                <option value="{{ $person->devuserid }}">{{ $person->devename }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                @endif

                                @if (($definition['resolution'] ?? false))
                                    <label>Resolution
                                        <textarea name="resolution" rows="3" required>{{ old('resolution', $ticket->resolution) }}</textarea>
                                    </label>
                                @endif

                                @if (($definition['remarks'] ?? false))
                                    <label>Remarks
                                        <textarea name="remarks" rows="3" required></textarea>
                                    </label>
                                @else
                                    <input type="hidden" name="remarks" value="">
                                @endif

                                <button type="submit" class="btn btn-primary hdv2-action-btn" data-hdv2-confirm="{{ $definition['label'] }}?">
                                    {{ $definition['label'] }}
                                </button>
                            </form>
	                        @empty
	                            <p class="hdv2-empty">No valid action is available for your current role.</p>
	                        @endforelse
	                    </section>
                @endif
            </aside>
        </div>
    </div>
@endsection
