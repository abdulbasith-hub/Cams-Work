<?php

namespace App\Http\Controllers;

use App\Models\CommonModel;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskTicketAssignment;
use App\Models\HelpdeskTicketComment;
use App\Models\HelpdeskTicketDevComment;
use App\Services\PHPMailerService;
use App\Support\HelpdeskSession;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HelpdeskTicketController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);

        $isDataTableRequest = $request->boolean('datatable');
        $view = $request->string('view', 'all')->toString();
        $query = $this->buildTicketIndexQuery($view);

        if ($isDataTableRequest) {
            $search = trim((string) $request->input('search.value', ''));

            return $this->ticketDataTableResponse($request, $query, $view, $search);
        }

        $tickets = $query
            ->with(['comments', 'assignments', 'devComments'])
            ->orderBy('created_at', 'asc')
            ->orderBy('updated_at', 'asc')
            ->get();

        $tickets->transform(function (HelpdeskTicket $ticket) {
            $ticket->tech_team_status = $this->resolveTechTeamStatus($ticket);

            return $ticket;
        });

        return view('tickets.index', [
            'tickets' => $tickets,
            'view' => $view,
            'forwardedCount' => HelpdeskSession::isSuperAdmin()
                ? $this->stateAdminSentBackCount()
                : 0,
            'helpdeskRole' => HelpdeskSession::role(),
            'isSuperAdmin' => HelpdeskSession::isSuperAdmin(),
            'isNicAdmin' => HelpdeskSession::isNicAdmin(),
            'isDeveloper' => HelpdeskSession::isDeveloper(),
        ]);
    }

    private function buildTicketIndexQuery(string $view)
    {
        $query = HelpdeskTicket::query();

        if (HelpdeskSession::isSuperAdmin()) {
            $currentUserId = HelpdeskSession::userId();

            $query->where(function ($builder) use ($currentUserId) {
                $builder->whereIn('forwarded_to_role', ['superadmin', 'stateadmin'])
                    ->orWhereNull('forwarded_to_role')
                    ->orWhere(function ($historyBuilder) {
                        $historyBuilder->where('forwarded_to_role', 'nicadmin')
                            ->whereExists(function ($commentQuery) {
                                $commentQuery->select(DB::raw(1))
                                    ->from('audit.helpdesk_ticket_comments as htc')
                                    ->whereColumn('htc.ticket_id', 'audit.helpdesk_tickets.id')
                                    ->where(function ($historyQuery) {
                                        $historyQuery->where('htc.comment', 'like', 'Ticket forwarded to NIC Admin.%')
                                            ->orWhere('htc.comment', 'Ticket created and forwarded NIC Admin.');
                                    })
                                    ->whereIn('htc.user_role', ['StateAdmin', 'IT Team']);
                            });
                    });

                if ($currentUserId) {
                    $builder->orWhere('cams_userid', $currentUserId);
                }
            });

            $query->where(function ($builder) {
                $builder->where('forwarded_to_role', '!=', 'nicadmin')
                    ->orWhereNull('forwarded_to_role')
                    ->orWhereNotExists(function ($commentQuery) {
                        $commentQuery->select(DB::raw(1))
                            ->from('audit.helpdesk_ticket_comments as htc')
                            ->whereColumn('htc.ticket_id', 'audit.helpdesk_tickets.id')
                            ->where('htc.comment', 'Ticket created by NIC Admin.');
                    });
            });

            if ($view === 'forwarded') {
                $this->applyStateAdminSentBackFilter($query);
            }
        } elseif (HelpdeskSession::isNicAdmin()) {
            $query->where(function ($q) {
                $q->where('forwarded_to_role', 'nicadmin')
                    ->orWhere('forwarded_to_role', 'developer')
                    ->orWhereExists(function ($commentQuery) {
                        $commentQuery->select(DB::raw(1))
                            ->from('audit.helpdesk_ticket_comments as htc')
                            ->whereColumn('htc.ticket_id', 'audit.helpdesk_tickets.id')
                            ->where(function ($nestedQuery) {
                                $nestedQuery->where('htc.comment', 'like', 'Ticket forwarded to NIC Admin.%')
                                    ->orWhere('htc.comment', 'Ticket created and forwarded NIC Admin.')
                                    ->orWhere('htc.comment', 'like', 'Ticket forwarded to StateAdmin.%');
                            });
                    })
                    ->orWhereExists(function ($assignmentQuery) {
                        $assignmentQuery->select(DB::raw(1))
                            ->from('audit.helpdesk_ticket_assignments as hta')
                            ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id');
                    });
            });
        } elseif (HelpdeskSession::isDeveloper()) {
            $currentUserId = HelpdeskSession::userId();
            $isAdditionalLayerDeveloper = $this->isAdditionalAssignmentLayerUser($currentUserId);
            $query->where(function ($q) use ($currentUserId, $isAdditionalLayerDeveloper) {
                $q->where('assigned_to_userid', $currentUserId)
                    ->orWhere(function ($developerScope) use ($currentUserId) {
                        $developerScope->whereExists(function ($assignmentQuery) use ($currentUserId) {
                            $assignmentQuery->select(DB::raw(1))
	                                ->from('audit.helpdesk_ticket_assignments as hta')
	                                ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
	                                ->where('hta.developer_userid', $currentUserId)
	                                ->where('hta.status', '!=', 'watchlist')
	                                ->whereNotExists(function ($latestAssignmentQuery) {
	                                    $latestAssignmentQuery->select(DB::raw(1))
	                                        ->from('audit.helpdesk_ticket_assignments as hta_latest')
	                                        ->whereColumn('hta_latest.ticket_id', 'hta.ticket_id')
	                                        ->where('hta_latest.status', '!=', 'watchlist')
	                                        ->whereColumn('hta_latest.id', '>', 'hta.id');
	                                });
                        });
                    })
                    ->orWhere('cams_userid', $currentUserId);

	                if ($isAdditionalLayerDeveloper) {
	                    $q->orWhereExists(function ($assignmentQuery) use ($currentUserId) {
	                        $assignmentQuery->select(DB::raw(1))
	                            ->from('audit.helpdesk_ticket_assignments as hta')
	                            ->whereColumn('hta.ticket_id', 'audit.helpdesk_tickets.id')
	                            ->where('hta.assigned_by_userid', (string) $currentUserId)
	                            ->where('hta.status', '!=', 'watchlist')
	                            ->whereNotExists(function ($latestAssignmentQuery) {
	                                $latestAssignmentQuery->select(DB::raw(1))
	                                    ->from('audit.helpdesk_ticket_assignments as hta_latest')
	                                    ->whereColumn('hta_latest.ticket_id', 'hta.ticket_id')
	                                    ->where('hta_latest.status', '!=', 'watchlist')
	                                    ->whereColumn('hta_latest.id', '>', 'hta.id');
	                            });
	                    });
	                }

	                $q->orWhereExists(function ($watchlistQuery) use ($currentUserId) {
	                    $watchlistQuery->select(DB::raw(1))
	                        ->from('audit.helpdesk_ticket_assignments as hta_watch')
	                        ->whereColumn('hta_watch.ticket_id', 'audit.helpdesk_tickets.id')
	                        ->where('hta_watch.developer_userid', (string) $currentUserId)
	                        ->where('hta_watch.status', 'watchlist');
	                });
	            });
        } elseif (HelpdeskSession::isDepartmentAdmin()) {
            $query->where('deptcode', HelpdeskSession::deptCode());
        } else {
            $query->where('cams_userid', HelpdeskSession::userId());
        }

        return $query;
    }

    private function applyTicketSearch($query, string $search)
    {
        if ($search === '') {
            return $query;
        }

        $searchTerm = '%'.$search.'%';

        $query->where(function ($builder) use ($searchTerm) {
            $builder->where('ticket_number', 'like', $searchTerm)
                ->orWhere('subject', 'like', $searchTerm)
                ->orWhere('department_name', 'like', $searchTerm)
                ->orWhere('category', 'like', $searchTerm)
                ->orWhere('request_type', 'like', $searchTerm)
                ->orWhere('user_name', 'like', $searchTerm)
                ->orWhere('assigned_to_name', 'like', $searchTerm)
                ->orWhere('status', 'like', $searchTerm)
                ->orWhere('priority', 'like', $searchTerm);
        });

        return $query;
    }

    private function ticketDataTableResponse(Request $request, $query, string $view, string $fallbackSearch): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = max(1, min((int) $request->input('length', 10), 100));
        $search = trim((string) $request->input('search.value', $fallbackSearch));
        $orderColumnIndex = (int) $request->input('order.0.column', 2);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = (string) $request->input("columns.$orderColumnIndex.name", 'created_at');

        $recordsTotal = (clone $query)->count();

        $filteredQuery = $this->applyTicketSearch(clone $query, $search);
        $recordsFiltered = (clone $filteredQuery)->count();

        $sortedQuery = $filteredQuery->with(['comments', 'assignments', 'devComments']);
        $this->applyTicketTableOrdering($sortedQuery, $orderColumn, $orderDirection);

        $tickets = $sortedQuery
            ->skip($start)
            ->take($length)
            ->get()
            ->values();

        $data = $tickets->map(function (HelpdeskTicket $ticket, int $index) use ($start) {
            $ticket->tech_team_status = $this->resolveTechTeamStatus($ticket);

            return $this->formatTicketTableRow($ticket, $start + $index + 1);
        })->all();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'view' => $view,
        ]);
    }

    private function applyTicketTableOrdering($query, string $orderColumn, string $orderDirection): void
    {
        if ($orderColumn === 'priority') {
            $priorityCase = "CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'high' THEN 2 WHEN priority = 'medium' THEN 3 WHEN priority = 'low' THEN 4 ELSE 5 END";
            $query->orderByRaw($orderDirection === 'asc' ? $priorityCase.' asc' : $priorityCase.' desc');
            $query->orderByDesc('created_at');

            return;
        }

        $sortableColumns = [
            'ticket_number' => 'ticket_number',
            'created_at' => 'created_at',
            'department_name' => 'department_name',
            'category' => 'category',
            'request_type' => 'request_type',
            'subject' => 'subject',
            'user_name' => 'user_name',
            'assigned_to_name' => 'assigned_to_name',
            'status' => 'status',
            'updated_at' => 'updated_at',
        ];

        if (isset($sortableColumns[$orderColumn])) {
            $query->orderBy($sortableColumns[$orderColumn], $orderDirection);

            return;
        }

        $query->orderByRaw('DATE(created_at) DESC')
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'high' THEN 2 WHEN priority = 'medium' THEN 3 WHEN priority = 'low' THEN 4 ELSE 5 END")
            ->orderByDesc('created_at');
    }

	    private function formatTicketTableRow(HelpdeskTicket $ticket, int $serial): array
	    {
	        $currentHolderLabel = $ticket->currentHolderLabel();
	        $assignedDeveloperLabel = $ticket->assignedDeveloperLabel();
	        $currentHolderDisplay = $currentHolderLabel;

	        if ((HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper()) && $assignedDeveloperLabel !== '-') {
	            $currentHolderDisplay = $currentHolderLabel.' ('.$assignedDeveloperLabel.')';
	        }

	        $ticketBadges = [];

	        if ((HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper()) && $ticket->isReturnedDeveloperReassignment()) {
	            $ticketBadges[] = '<span class="badge badge-ticket-return">Return</span>';
	        }

	        $isWatchlistedTicket = $ticket->assignments->contains(function ($assignment) {
	            if (($assignment->status ?? null) !== 'watchlist') {
	                return false;
	            }

	            if (HelpdeskSession::isNicAdmin()) {
	                return true;
	            }

	            return HelpdeskSession::isDeveloper()
	                && (string) $assignment->developer_userid === (string) HelpdeskSession::userId();
	        });

	        if ($isWatchlistedTicket) {
	            $ticketBadges[] = '<span class="badge badge-ticket-watchlist">Watchlist</span>';
	        }

	        $row = [
            'serial' => $serial,
	            'ticket_number' => sprintf(
	                '<a href="%s" class="text-decoration-none fw-bold text-dark">%s</a>%s',
	                route('helpdesk.tickets.show', $ticket),
	                e($ticket->ticket_number),
	                $ticketBadges ? ' '.implode(' ', $ticketBadges) : ''
	            ),
            'created_at' => $ticket->created_at?->format('d/n/Y h:i A') ?? '-',
            'reopened_on' => $ticket->reopenedOn()?->format('d/n/Y h:i A') ?? '-',
            'department_name' => e($ticket->department_name ?: '-'),
            'category' => e($ticket->category ?: '-'),
            'request_type' => e($ticket->requestTypeLabel()),
            'subject' => e($ticket->subject ?: '-'),
            'user_name' => e(HelpdeskSession::normalizeUserName($ticket->user_name) ?: '-'),
	            'current_holder' => e($currentHolderDisplay),
            'priority' => sprintf(
                '<span class="badge badge-priority-%s">%s</span>',
                e($ticket->priority),
                e(ucfirst((string) $ticket->priority))
            ),
        ];

        if (HelpdeskSession::isNicAdmin() || HelpdeskSession::isDeveloper()) {
            $row['tech_team_status'] = $ticket->tech_team_status !== '-'
                ? sprintf(
                    '<span class="badge badge-status-%s">%s</span>',
                    e($ticket->tech_team_status),
                    e(ucfirst(str_replace('_', ' ', $ticket->tech_team_status)))
                )
                : '-';
        }

        $row['status'] = sprintf(
            '<span class="badge badge-status-%s">%s</span>',
            e($ticket->status),
            e(ucfirst(str_replace('_', ' ', (string) $ticket->status)))
        );

        if (HelpdeskSession::isNicAdmin()) {
            $row['assigned_to_name'] = sprintf(
                '<span class="badge badge-assigned-developer">%s</span>',
	                e($assignedDeveloperLabel)
            );
            $row['completed_by'] = e($ticket->completedByLabel());
        }

        $row['updated_at'] = $ticket->updated_at?->format('d/n/Y h:i A') ?? '-';
        $row['action'] = sprintf(
            '<a href="%s" class="btn text-dark" title="View Ticket"><i class="bi bi-eye fs-5"></i></a>',
            route('helpdesk.tickets.show', $ticket)
        );

        return $row;
    }

    public function create(): View
    {
        abort_unless(HelpdeskSession::canAccess(), 403);

        $departments = collect();
        $deptCode = old('deptcode', HelpdeskSession::deptCode());

        if (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin()) {
            $departments = DB::table('audit.mst_dept')
                ->select('deptcode', 'deptelname')
                ->orderBy('deptcode')
                ->get();
        }

        $planDetails = collect();
        if ($deptCode) {
            $planDetails = collect(CommonModel::getplandetailsforreport($deptCode))->values();
        }
        // dd($planDetails);
        [$financialYears, $auditQuarters] = $this->buildPlanOptions($planDetails);

        return view('tickets.create', [
            'categories' => HelpdeskTicket::CATEGORY_OPTIONS,
            'requestTypes' => HelpdeskTicket::REQUEST_TYPE_OPTIONS,
            'departments' => $departments,
            'canSelectDepartment' => HelpdeskSession::isSuperAdmin() || HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin(),
            'selectedDeptCode' => $deptCode,
            'financialYears' => $financialYears,
            'auditQuarters' => $auditQuarters,
            'isStateAdmin' => HelpdeskSession::isSuperAdmin(),
            'isNicAdmin' => HelpdeskSession::isNicAdmin(),
        ]);
    }

    public function planDetails(Request $request): JsonResponse
    {
        abort_unless(HelpdeskSession::canAccess() && (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin()), 403);

        $validated = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        $planDetails = collect(CommonModel::getplandetailsforreport($validated['deptcode']))->values();
        [$financialYears, $auditQuarters] = $this->buildPlanOptions($planDetails);

        return response()->json([
            'financialYears' => $financialYears->values()->all(),
            'auditQuarters' => $auditQuarters->values()->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);

        $textFieldRules = ['required', 'string', function ($attribute, $value, $fail) {
            $this->validateSafeTicketText($attribute, $value, $fail, true);
        }];

        $optionalTextFieldRules = ['nullable', 'string', 'max:200', function ($attribute, $value, $fail) {
            if ($value !== null && $value !== '') {
                $this->validateSafeTicketText($attribute, $value, $fail, false);
            }
        }];

        $validationRules = [
            'subject' => array_merge($textFieldRules, ['max:200']),
            'description' => ['required', 'string', 'max:750', function ($attribute, $value, $fail) {
                $normalizedValue = trim($value);

                if ($this->containsForbiddenCommand($normalizedValue)) {
                    $fail('Description contains blocked script or SQL content.');

                    return;
                }

                if (preg_match('/([A-Za-z0-9])\1{4,}/', $normalizedValue)) {
                    $fail('Description cannot contain the same letter or number repeated 5 times continuously.');
                }
            }],
            'deptcode' => (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin()) ? ['required', 'string', 'regex:/^\d+$/'] : ['nullable'],
            'financial_year' => 'required|string|max:50',
            'audit_quarter' => 'required|integer',
            'institution' => $optionalTextFieldRules,
            'request_type' => 'required|string|in:'.implode(',', array_keys(HelpdeskTicket::REQUEST_TYPE_OPTIONS)),
            'category' => 'required|string|in:'.implode(',', HelpdeskTicket::CATEGORY_OPTIONS),
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments' => 'nullable',
            'attachments.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:500',
        ];

        $validated = $request->validate($validationRules);

        $userId = HelpdeskSession::userId();
        abort_unless($userId, 403);

        $deptCode = (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin())
            ? ($validated['deptcode'] ?? null)
            : HelpdeskSession::deptCode();

        $createdAt = Carbon::now('Asia/Kolkata');

        $validated['cams_userid'] = $userId;
        $validated['user_name'] = HelpdeskSession::userName();
        $validated['user_email'] = HelpdeskSession::email();
        $validated['deptcode'] = $deptCode;
        $validated['department_name'] = $this->departmentName($deptCode);
        $validated['financialyearcode'] = $validated['financial_year'] ?? null;
        $validated['planmappingid'] = $validated['audit_quarter'] ?? null;
        $validated['institution'] = $validated['institution'] ?? null;
        $validated['status'] = 'open';
        $validated['assigned_to_userid'] = null;
        $validated['assigned_to_name'] = null;
        $validated['forwarded_to_chargeid'] = null;
        $forwardTo = 'stateadmin';
        $forwardNotes = 'Forwarded To StateAdmin.';
        $createdComment = 'Ticket created and forwarded StateAdmin.';

        if (HelpdeskSession::isSuperAdmin()) {
            $forwardTo = 'nicadmin';
            $forwardNotes = 'Forwarded To NIC Admin.';
            $createdComment = 'Ticket created and forwarded NIC Admin.';
        } elseif (HelpdeskSession::isNicAdmin()) {
            $forwardTo = 'nicadmin';
            $forwardNotes = 'Ticket created by NIC Admin.';
            $createdComment = 'Ticket created by NIC Admin.';
        }

        $validated['forwarded_to_role'] = $forwardTo;
        $validated['forwarded_at'] = $createdAt;
        $validated['created_at'] = $createdAt;
        $validated['updated_at'] = $createdAt;
        $validated['forward_notes'] = $forwardNotes;

        unset($validated['financial_year'], $validated['audit_quarter']);

        if ($request->hasFile('attachments')) {
            $validated['attachments'] = $this->storeAttachments($request);
        }

        $this->syncHelpdeskTicketPrimaryKeySequence();

        try {
            $ticket = HelpdeskTicket::create($validated);
        } catch (QueryException $exception) {
            if (! $this->isHelpdeskTicketPrimaryKeyViolation($exception)) {
                throw $exception;
            }

            $this->syncHelpdeskTicketPrimaryKeySequence();
            $ticket = HelpdeskTicket::create($validated);
        }

        HelpdeskTicketComment::create([
            'ticket_id' => $ticket->id,
            'cams_userid' => $userId,
            'user_name' => HelpdeskSession::userName(),
            'user_role' => HelpdeskSession::roleLabel(),
            'comment' => $createdComment,
            'is_internal' => false,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $this->notifyHelpdeskTicketAction(
            $ticket,
            'Helpdesk ticket created',
            'New Helpdesk Ticket Created',
            $createdComment,
            $this->helpdeskEmailsForForwardRole($forwardTo, $ticket)
        );

        return redirect()->route('helpdesk.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    private function buildPlanOptions($planDetails): array
    {
        $financialYears = $planDetails
            ->map(function ($detail) {
                return [
                    'value' => $detail->financialyearcode ?? null,
                    'label' => $detail->financialyear ?? null,
                ];
            })
            ->filter(function ($detail) {
                return ! empty($detail['value']) && ! empty($detail['label']);
            })
            ->unique('value')
            ->values();

        $auditQuarters = $planDetails
            ->map(function ($detail) {
                return [
                    'value' => $detail->planmappingid ?? null,
                    'quartercode' => $detail->auditquartercode ?? null,
                    'financialyearcode' => $detail->financialyearcode ?? null,
                    'label' => $detail->planname ?? $detail->auditquarter ?? $detail->auditquartercode ?? null,
                ];
            })
            ->filter(function ($detail) {
                return ! empty($detail['value']) && ! empty($detail['label']);
            })
            ->unique('value')
            ->sortBy(function ($detail) {
                return (int) $detail['value'];
            })
            ->values();

        return [$financialYears, $auditQuarters];
    }

    private function validateSafeTicketText(string $attribute, string $value, callable $fail, bool $blockRepeatedPattern, bool $allowNewLines = false): void
    {
        $normalizedValue = trim($value);

        if ($this->containsForbiddenCommand($normalizedValue)) {
            $fail(ucfirst(str_replace('_', ' ', $attribute)).' contains blocked script or SQL content.');

            return;
        }

        if ($this->containsDisallowedCharacters($normalizedValue, $allowNewLines)) {
            $fail(ucfirst(str_replace('_', ' ', $attribute)).' contains invalid special characters.');

            return;
        }

        if ($attribute === 'institution' && substr_count($normalizedValue, '-') > 1) {
            $fail('Institution can contain only one hyphen.');

            return;
        }

        if ($blockRepeatedPattern && preg_match('/([A-Za-z0-9])\1{4,}/', $normalizedValue)) {
            $fail(ucfirst(str_replace('_', ' ', $attribute)).' cannot contain the same letter or number repeated 5 times continuously.');
        }
    }

    private function containsForbiddenCommand(string $value): bool
    {
        return preg_match('/(<\s*script\b|<\/\s*script\b|\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\btruncate\b|\balter\b|\bunion\b|\bexec\b|\bexecute\b|\bjavascript:|--|;)/i', $value) === 1;
    }

    private function containsDisallowedCharacters(string $value, bool $allowNewLines = false): bool
    {
        $pattern = $allowNewLines
            ? '/^[A-Za-z0-9\s\.\,\:\;\'\"\-\(\)\/\&\n\r]+$/'
            : '/^[A-Za-z0-9\s\.\,\:\;\'\"\-\(\)\/\&]+$/';

        return preg_match($pattern, $value) !== 1;
    }

    public function show(HelpdeskTicket $ticket): View
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        $this->authorizeTicketAccess($ticket);
        $ticket->load(['comments', 'devComments', 'assignments']);

        $financialYearLabel = null;
        if ($ticket->financialyearcode) {
            $financialYearLabel = DB::table('audit.mst_financialyear')
                ->where('financialyearcode', $ticket->financialyearcode)
                ->value('financialyear');
        }

        $planName = null;
        if ($ticket->planmappingid) {
            $planName = DB::table('audit.auditplanmapping')
                ->where('planmappingid', $ticket->planmappingid)
                ->value('planname');
        }

        $isAssignedDeveloper = HelpdeskSession::isDeveloper()
            && (string) $ticket->assigned_to_userid === (string) HelpdeskSession::userId();
        $isConcernedDeveloper = HelpdeskSession::isDeveloper()
            && $this->isCurrentOrLatestAssignedDeveloper($ticket->id, HelpdeskSession::userId());
        $isAdditionalLayerDeveloper = HelpdeskSession::isDeveloper()
            && $this->isAdditionalAssignmentLayerUser(HelpdeskSession::userId());
	        $isAdditionalLayerHandler = $isAdditionalLayerDeveloper
	            && $this->isLatestAssignmentLayerForTicket($ticket->id, HelpdeskSession::userId());
	        $isWatchedDeveloper = HelpdeskSession::isDeveloper()
	            && $this->isWatchlistedDeveloper($ticket->id, HelpdeskSession::userId());
	        $canAssignDeveloper = HelpdeskSession::isNicAdmin()
	            || ($isAdditionalLayerDeveloper && $isAssignedDeveloper && $ticket->isDeveloperStage());
	        $ticketWatchers = $this->ticketWatchlistDevelopers($ticket->id);
	        $watchlistExcludeIds = collect([$ticket->assigned_to_userid])
	            ->merge($ticketWatchers->pluck('developer_userid'))
	            ->filter(fn ($userId) => trim((string) $userId) !== '')
	            ->map(fn ($userId) => (string) $userId)
	            ->unique()
	            ->values()
	            ->all();

	        return view('tickets.show', [
	            'ticket' => $ticket,
	            'canViewInternalNotes' => HelpdeskSession::isNicAdmin() || $isConcernedDeveloper || $isAdditionalLayerHandler || $isWatchedDeveloper,
	            'canViewAdminInternalNotes' => HelpdeskSession::isNicAdmin() || HelpdeskSession::isSuperAdmin(),
            'helpdeskRole' => HelpdeskSession::role(),
            'financialYearLabel' => $financialYearLabel,
            'planName' => $planName,
            'lockedStatusSelection' => $this->lockedStatusSelection($ticket),
            'techTeamStatus' => $this->resolveTechTeamStatus($ticket),
            'developerStatusLocked' => $this->isDeveloperStatusLocked($ticket),
	            'developers' => $canAssignDeveloper
	                ? $this->fetchActiveDevelopers($isAdditionalLayerDeveloper ? HelpdeskSession::userId() : null)
	                : collect(),
	            'watchlistDevelopers' => HelpdeskSession::isNicAdmin()
	                ? $this->fetchActiveDevelopers($watchlistExcludeIds)
	                : collect(),
	            'ticketWatchers' => $ticketWatchers,
	            'additionalLayerDevelopers' => HelpdeskSession::isNicAdmin()
	                ? $this->fetchAdditionalLayerDevelopers()
	                : collect(),
            'isAssignedDeveloper' => $isAssignedDeveloper,
	            'isConcernedDeveloper' => $isConcernedDeveloper,
	            'isWatchedDeveloper' => $isWatchedDeveloper,
	            'isAdditionalLayerDeveloper' => $isAdditionalLayerDeveloper,
            'isAdditionalLayerHandler' => $isAdditionalLayerHandler,
            'canAssignDeveloper' => $canAssignDeveloper,
            'sendBackTargetLabel' => $this->sendBackTargetLabel($ticket),
        ]);
    }

    public function addComment(Request $request, HelpdeskTicket $ticket): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        $this->authorizeTicketAccess($ticket);
        abort_if(in_array($ticket->status, ['resolved', 'closed'], true), 403);
        $currentTimestamp = $this->currentTimestamp();

        $validated = $request->validate([
            'comment' => 'required|string',
            'comment_visibility' => 'nullable|string|in:public,developer_internal,dg_internal',
        ]);

	        $commentVisibility = $validated['comment_visibility'] ?? 'public';
	        $isAssignedDeveloper = HelpdeskSession::isDeveloper()
	            && (string) $ticket->assigned_to_userid === (string) HelpdeskSession::userId();
	        $isWatchedDeveloper = HelpdeskSession::isDeveloper()
	            && $this->isWatchlistedDeveloper($ticket->id, HelpdeskSession::userId());

	        if ($isAssignedDeveloper) {
	            $commentVisibility = 'developer_internal';
	        }

	        if ($isWatchedDeveloper) {
	            $commentVisibility = 'developer_internal';
	        }

        if ($commentVisibility === 'developer_internal') {
	            abort_unless(
	                HelpdeskSession::isNicAdmin()
	                || ($isAssignedDeveloper && ! empty($ticket->assigned_to_userid))
	                || $isWatchedDeveloper,
	                403
	            );

	            $developerComment = $isWatchedDeveloper
	                ? '[Watchlist] '.$validated['comment']
	                : $validated['comment'];

	            HelpdeskTicketDevComment::create([
	                'ticket_id' => $ticket->id,
	                'cams_userid' => HelpdeskSession::userId(),
	                'user_name' => HelpdeskSession::userName(),
	                'user_role' => HelpdeskSession::roleLabel(),
	                'comment' => $developerComment,
	                'created_at' => $currentTimestamp,
	                'updated_at' => $currentTimestamp,
	            ]);
        } elseif ($commentVisibility === 'dg_internal') {
            abort_unless(HelpdeskSession::isNicAdmin() || HelpdeskSession::isSuperAdmin(), 403);

            HelpdeskTicketComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskSession::userId(),
                'user_name' => HelpdeskSession::userName(),
                'user_role' => HelpdeskSession::roleLabel(),
                'comment' => $validated['comment'],
                'is_internal' => true,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        } else {
            HelpdeskTicketComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskSession::userId(),
                'user_name' => HelpdeskSession::userName(),
                'user_role' => HelpdeskSession::roleLabel(),
                'comment' => $validated['comment'],
                'is_internal' => false,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        }

        $mailStatus = $this->notifyHelpdeskTicketAction(
            $ticket,
            'Helpdesk ticket comment added',
            'Helpdesk Ticket Comment Added',
            $validated['comment'],
            $this->helpdeskCommentNotificationEmails($ticket, $commentVisibility),
            [
                // 'Comment Type' => match ($commentVisibility) {
                //     'developer_internal' => 'Developer Internal',
                //     'dg_internal' => 'DG Internal',
                //     default => 'Public',
                // },
                'Comment Added On' => $currentTimestamp->format('d/m/Y h:i A'),
            ],
            $this->helpdeskCommentNotificationCcEmails($commentVisibility)
        );

        $response = back()->with('success', 'Comment added successfully.');

        if (($mailStatus['sent'] ?? 0) === 0) {
            $message = $mailStatus['message'] ?? 'Mail not sent. Please check helpdesk mail log.';
            $response->with('warning', $message);
        }

	        return $response;
	    }

	    public function watchlist(Request $request, HelpdeskTicket $ticket): RedirectResponse
	    {
	        abort_unless(HelpdeskSession::canAccess() && HelpdeskSession::isNicAdmin(), 403);
	        $this->authorizeTicketAccess($ticket);
	        abort_if(in_array($ticket->status, ['resolved', 'closed'], true), 403);

	        $validated = $request->validate([
	            'watchlist_userid' => 'required',
	        ]);

	        $developerUserId = trim((string) $validated['watchlist_userid']);
	        $developer = DB::table('audit.dev_userdetails')
	            ->where('devuserid', $developerUserId)
	            ->where('statusflag', 'Y')
	            ->select('devuserid', 'devename', 'email')
	            ->first();

	        if (! $developer) {
	            return back()->withErrors(['watchlist_userid' => 'Selected developer is invalid.'])->withInput();
	        }

	        if ((string) $ticket->assigned_to_userid === (string) $developer->devuserid) {
	            return back()->withErrors(['watchlist_userid' => 'Assigned developer cannot be added to watchlist.'])->withInput();
	        }

	        $currentTimestamp = $this->currentTimestamp();
	        $alreadyWatching = DB::table('audit.helpdesk_ticket_assignments')
	            ->where('ticket_id', $ticket->id)
	            ->where('developer_userid', (string) $developer->devuserid)
	            ->where('status', 'watchlist')
	            ->exists();

	        if (! $alreadyWatching) {
	            HelpdeskTicketAssignment::create([
	                'ticket_id' => $ticket->id,
	                'assigned_by_userid' => HelpdeskSession::userId(),
	                'assigned_by_name' => HelpdeskSession::userName(),
	                'developer_userid' => (string) $developer->devuserid,
	                'developer_name' => $developer->devename,
	                'notes' => 'Watchlist access',
	                'status' => 'watchlist',
	                'assigned_at' => $currentTimestamp,
	                'created_at' => $currentTimestamp,
	                'updated_at' => $currentTimestamp,
	            ]);

	            HelpdeskTicketDevComment::create([
	                'ticket_id' => $ticket->id,
	                'cams_userid' => HelpdeskSession::userId(),
	                'user_name' => HelpdeskSession::userName(),
	                'user_role' => HelpdeskSession::roleLabel(),
	                'comment' => 'Watchlist added: '.$developer->devename.'.',
	                'created_at' => $currentTimestamp,
	                'updated_at' => $currentTimestamp,
	            ]);
	        }

	        return back()->with(
	            $alreadyWatching ? 'warning' : 'success',
	            $alreadyWatching ? 'Developer is already in the watchlist.' : 'Developer added to watchlist.'
	        );
	    }

	    public function removeWatchlist(Request $request, HelpdeskTicket $ticket): RedirectResponse
	    {
	        abort_unless(HelpdeskSession::canAccess() && HelpdeskSession::isNicAdmin(), 403);
	        $this->authorizeTicketAccess($ticket);
	        abort_if(in_array($ticket->status, ['resolved', 'closed'], true), 403);

	        $validated = $request->validate([
	            'watchlist_userid' => 'required',
	        ]);

	        $developerUserId = trim((string) $validated['watchlist_userid']);
	        $watcherName = DB::table('audit.helpdesk_ticket_assignments')
	            ->where('ticket_id', $ticket->id)
	            ->where('developer_userid', $developerUserId)
	            ->where('status', 'watchlist')
	            ->value('developer_name');

	        if (! $watcherName) {
	            return back()->with('warning', 'Developer is not in the watchlist.');
	        }

	        DB::table('audit.helpdesk_ticket_assignments')
	            ->where('ticket_id', $ticket->id)
	            ->where('developer_userid', $developerUserId)
	            ->where('status', 'watchlist')
	            ->delete();

	        $currentTimestamp = $this->currentTimestamp();
	        HelpdeskTicketDevComment::create([
	            'ticket_id' => $ticket->id,
	            'cams_userid' => HelpdeskSession::userId(),
	            'user_name' => HelpdeskSession::userName(),
	            'user_role' => HelpdeskSession::roleLabel(),
	            'comment' => 'Watchlist removed: '.$watcherName.'.',
	            'created_at' => $currentTimestamp,
	            'updated_at' => $currentTimestamp,
	        ]);

	        return back()->with('success', 'Developer removed from watchlist.');
	    }

	    public function updateStatus(Request $request, HelpdeskTicket $ticket): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        $this->authorizeStaffTicketAccess($ticket);
        abort_unless($this->canCurrentStaffUpdateStatus($ticket), 403);
        $currentTimestamp = $this->currentTimestamp();

        $allowedStatuses = $this->helpdeskAllowedStatusesForCurrentRole();

        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', $allowedStatuses),
        ]);

        $requestedStatus = $validated['status'];
        $this->applyHelpdeskStatusUpdate($ticket, $requestedStatus, $currentTimestamp);

        $successMessage = 'Ticket status updated.';
        if (HelpdeskSession::isDeveloper() && $requestedStatus === 'resolved') {
            $successMessage = 'Resolution noted for NIC Admin. Ticket public status remains In Progress until NIC Admin or StateAdmin finalizes it.';
        } elseif (HelpdeskSession::isNicAdmin() && $requestedStatus === 'resolved') {
            $successMessage = 'Resolution noted for StateAdmin. Ticket public status remains In Progress until StateAdmin finalizes it.';
        }

        return back()->with('success', $successMessage);
    }

    private function helpdeskAllowedStatusesForCurrentRole(): array
    {
        if (HelpdeskSession::isDeveloper()) {
            return ['in_progress', 'resolved'];
        }

        if (HelpdeskSession::isNicAdmin()) {
            return ['open', 'in_progress', 'resolved'];
        }

        return ['open', 'in_progress', 'closed'];
    }

    private function applyHelpdeskStatusUpdate(HelpdeskTicket $ticket, string $requestedStatus, Carbon $currentTimestamp): void
    {
        $statusForTicket = $requestedStatus;

        if ((HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin()) && $requestedStatus === 'resolved') {
            $statusForTicket = 'in_progress';
        }

        $updateData = [
            'status' => $statusForTicket,
            'updated_at' => $currentTimestamp,
        ];

        if (in_array($statusForTicket, ['resolved', 'closed'], true)) {
            $updateData['resolved_at'] = $currentTimestamp;
        }

        if ($statusForTicket === 'open') {
            $updateData['resolved_at'] = null;
        }

        if (HelpdeskSession::isSuperAdmin() && in_array($requestedStatus, ['resolved', 'closed'], true)) {
            $updateData['assigned_to_userid'] = null;
            $updateData['assigned_to_name'] = null;
            $updateData['forwarded_to_chargeid'] = null;
            $updateData['forwarded_to_role'] = null;
        }

        $ticket->update($updateData);

        $comment = 'Status changed to '.Str::headline($requestedStatus).'.';
        if ((HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin()) && $requestedStatus === 'resolved') {
            $comment = HelpdeskSession::userName().' marked the issue as resolved. Ticket remains In Progress until StateAdmin confirms and closes it.';
        } elseif (HelpdeskSession::isDeveloper() || HelpdeskSession::isNicAdmin()) {
            $comment = HelpdeskSession::userName().' updated the status to '.Str::headline($requestedStatus).'.';
        }

        if (HelpdeskSession::isDeveloper()) {
            HelpdeskTicketDevComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskSession::userId(),
                'user_name' => HelpdeskSession::userName(),
                'user_role' => HelpdeskSession::roleLabel(),
                'comment' => $comment,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);

            return;
        }

        HelpdeskTicketComment::create([
            'ticket_id' => $ticket->id,
            'cams_userid' => HelpdeskSession::userId(),
            'user_name' => HelpdeskSession::userName(),
            'user_role' => HelpdeskSession::roleLabel(),
            'comment' => $comment,
            'is_internal' => false,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]);
    }

    public function forward(Request $request, HelpdeskTicket $ticket): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        $this->authorizeStaffTicketAccess($ticket);
        abort_unless($this->canCurrentStaffForwardTicket($ticket), 403);
        $currentTimestamp = $this->currentTimestamp();

        $validated = $request->validate([
            'forward_to' => 'required|in:stateadmin,nicadmin,developer',
            'forward_notes' => 'nullable|string',
            'developer_userid' => 'nullable',
            'addition_layer_userid' => 'nullable',
            'status' => 'nullable|in:'.implode(',', $this->helpdeskAllowedStatusesForCurrentRole()),
        ]);

        if (HelpdeskSession::isDepartmentAdmin() && $validated['forward_to'] !== 'stateadmin') {
            abort(403);
        }

        if (HelpdeskSession::isSuperAdmin() && $validated['forward_to'] !== 'nicadmin') {
            abort(403);
        }

        $canAssignDeveloper = HelpdeskSession::isNicAdmin()
            || (
                HelpdeskSession::isDeveloper()
                && $this->isAdditionalAssignmentLayerUser(HelpdeskSession::userId())
                && $ticket->isDeveloperStage()
                && (string) $ticket->assigned_to_userid === (string) HelpdeskSession::userId()
            );

        if (! $canAssignDeveloper && $validated['forward_to'] === 'developer') {
            abort(403);
        }

        if (! HelpdeskSession::isNicAdmin() && $validated['forward_to'] === 'stateadmin') {
            abort(403);
        }

        if (HelpdeskSession::isNicAdmin() && $validated['forward_to'] === 'stateadmin' && $ticket->normalizedForwardedRole() !== 'nicadmin') {
            return back()->withErrors([
                'forward_to' => 'Ticket can be sent back to StateAdmin only after it returns to NIC Admin.',
            ]);
        }

        if (! $this->hasCurrentRoleUpdatedStatusForCurrentStage($ticket)) {
            $submittedStatus = trim((string) ($validated['status'] ?? ''));

            if ($submittedStatus === 'closed') {
                return back()->withErrors([
                    'forward_to' => 'Closed tickets cannot be forwarded.',
                ])->withInput();
            }

            if ($submittedStatus !== '' && $submittedStatus !== 'open') {
                $this->applyHelpdeskStatusUpdate($ticket, $submittedStatus, $currentTimestamp);
                $ticket->refresh();
            }
        }

        if (! $this->hasCurrentRoleUpdatedStatusForCurrentStage($ticket)) {
            return back()->withErrors([
                'forward_to' => 'Please change the ticket status before forwarding.',
            ])->withInput();
        }

        $forwardNotes = $validated['forward_notes'] ?? null;

        $assignedDeveloper = null;
        if ($validated['forward_to'] === 'developer') {
            $additionLayerUserId = trim((string) ($validated['addition_layer_userid'] ?? ''));
            $selectedDeveloperUserId = $additionLayerUserId !== ''
                ? $additionLayerUserId
                : trim((string) ($validated['developer_userid'] ?? ''));

            if ($selectedDeveloperUserId === '') {
                return back()->withErrors(['developer_userid' => 'Developer is required.'])->withInput();
            }

            $assignedDeveloper = DB::table('audit.dev_userdetails')
                ->where('devuserid', $selectedDeveloperUserId)
                ->where('statusflag', 'Y')
                ->select('devuserid', 'devename', 'email')
                ->first();

            if (! $assignedDeveloper) {
                return back()->withErrors(['developer_userid' => 'Selected developer is invalid.'])->withInput();
            }

            if ($additionLayerUserId !== '' && ! $this->isAdditionalAssignmentLayerUser($additionLayerUserId)) {
                return back()->withErrors(['addition_layer_userid' => 'Selected additional layer is invalid.'])->withInput();
            }

            DB::table('audit.helpdesk_ticket_assignments')
                ->where('ticket_id', $ticket->id)
                ->where('status', 'assigned')
                ->update([
                    'status' => 'reassigned',
                    'released_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);
        }

        $ticket->update([
            'assigned_to_userid' => $assignedDeveloper->devuserid ?? null,
            'assigned_to_name' => $assignedDeveloper->devename ?? null,
            'forwarded_to_chargeid' => null,
            'forwarded_to_role' => $validated['forward_to'],
            'forwarded_at' => $currentTimestamp,
            'forward_notes' => $forwardNotes,
            'status' => 'in_progress',
            'updated_at' => $currentTimestamp,
        ]);

        if ($validated['forward_to'] === 'developer') {
            HelpdeskTicketAssignment::create([
                'ticket_id' => $ticket->id,
                'assigned_by_userid' => HelpdeskSession::userId(),
                'assigned_by_name' => HelpdeskSession::userName(),
                'developer_userid' => (string) $assignedDeveloper->devuserid,
                'developer_name' => $assignedDeveloper->devename,
                'notes' => $forwardNotes,
                'status' => 'assigned',
                'assigned_at' => $currentTimestamp,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        }

        $isAssignedToAdditionalLayer = $validated['forward_to'] === 'developer'
            && ! empty($validated['addition_layer_userid'])
            && (string) ($assignedDeveloper->devuserid ?? '') === (string) $validated['addition_layer_userid'];

        $comment = $validated['forward_to'] === 'developer'
            ? trim('Ticket assigned to '.($isAssignedToAdditionalLayer ? 'Additional Layer' : 'Tech Team').': '.($assignedDeveloper->devename ?? '').'. '.($forwardNotes ?? ''))
            : trim('Ticket forwarded to '.match ($validated['forward_to']) {
                'stateadmin' => 'StateAdmin',
                'nicadmin' => 'NIC Admin',
                default => Str::headline($validated['forward_to']),
            }.'. '.($forwardNotes ?? ''));

        if ($validated['forward_to'] === 'developer') {
            HelpdeskTicketDevComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskSession::userId(),
                'user_name' => HelpdeskSession::userName(),
                'user_role' => HelpdeskSession::roleLabel(),
                'comment' => trim($comment),
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        } else {
            HelpdeskTicketComment::create([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskSession::userId(),
                'user_name' => HelpdeskSession::userName(),
                'user_role' => HelpdeskSession::roleLabel(),
                'comment' => trim($comment),
                'is_internal' => false,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        }

        $forwardRecipients = $validated['forward_to'] === 'developer'
            ? [$assignedDeveloper->email ?? null]
            : $this->helpdeskEmailsForForwardRole($validated['forward_to'], $ticket);

        $forwardLabel = match ($validated['forward_to']) {
            'stateadmin' => 'StateAdmin',
            'nicadmin' => 'NIC Admin',
            'developer' => $isAssignedToAdditionalLayer ? 'Additional Layer' : 'Tech Team',
            default => Str::headline($validated['forward_to']),
        };

        $this->notifyHelpdeskTicketAction(
            $ticket,
            $validated['forward_to'] === 'developer' ? 'Helpdesk ticket assigned' : 'Helpdesk ticket forwarded',
            $validated['forward_to'] === 'developer' ? 'Helpdesk Ticket Assigned' : 'Helpdesk Ticket Forwarded',
            trim($comment),
            array_merge($forwardRecipients, [$this->helpdeskTicketOwnerEmail($ticket)]),
            [
                'Forwarded To' => $forwardLabel,
                'Assigned Developer' => $assignedDeveloper->devename ?? '-',
                'Forward Notes' => $forwardNotes ?: '-',
                'Forwarded On' => $currentTimestamp->format('d/m/Y h:i A'),
            ],
            $this->helpdeskForwardNotificationCcEmails($validated['forward_to'])
        );

        return back()->with('success', $validated['forward_to'] === 'developer'
            ? 'Ticket assigned to developer successfully.'
            : 'Ticket forwarded successfully.');
    }

    public function sendBack(Request $request, HelpdeskTicket $ticket): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::isDeveloper(), 403);
        $this->authorizeTicketAccess($ticket);
        $currentTimestamp = $this->currentTimestamp();

        $validated = $request->validate([
            'send_back_message' => 'nullable|string',
            'status' => 'nullable|in:'.implode(',', $this->helpdeskAllowedStatusesForCurrentRole()),
        ]);

        if (! $this->hasCurrentRoleUpdatedStatusForCurrentStage($ticket)) {
            $submittedStatus = trim((string) ($validated['status'] ?? ''));

            if ($submittedStatus !== '' && $submittedStatus !== 'open') {
                $this->applyHelpdeskStatusUpdate($ticket, $submittedStatus, $currentTimestamp);
                $ticket->refresh();
            }
        }

        if (! $this->hasCurrentRoleUpdatedStatusForCurrentStage($ticket)) {
            return back()->withErrors([
                'send_back_message' => 'Please change the ticket status before sending it back.',
            ])->withInput();
        }

        $notes = trim(($ticket->forward_notes ? $ticket->forward_notes."\n" : '').'Returned by Tech Team. '.($validated['send_back_message'] ?? ''));
        $activeAssignment = DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticket->id)
            ->where('developer_userid', HelpdeskSession::userId())
            ->where('status', 'assigned')
            ->orderByDesc('id')
            ->first();
	        $returnLayerUserId = $activeAssignment?->assigned_by_userid;
	        $returnToAdditionalLayer = $this->isAdditionalAssignmentLayerUser($returnLayerUserId);
	        $returnLayerName = $returnToAdditionalLayer
	            ? ($activeAssignment->assigned_by_name ?: $this->developerNameById($returnLayerUserId))
	            : null;

	        if (! $returnToAdditionalLayer) {
	            $defaultReturnLayer = $this->defaultSendBackLayerDeveloper();

	            if ($defaultReturnLayer && (string) $defaultReturnLayer->devuserid !== (string) HelpdeskSession::userId()) {
	                $returnLayerUserId = $defaultReturnLayer->devuserid;
	                $returnLayerName = $defaultReturnLayer->devename;
	                $returnToAdditionalLayer = true;
	            }
	        }

	        $returnTargetLabel = $returnToAdditionalLayer
	            ? ($returnLayerName ?: 'Additional NIC Admin')
	            : 'NIC Admin';

        $ticket->update([
            'assigned_to_userid' => $returnToAdditionalLayer ? $returnLayerUserId : null,
            'assigned_to_name' => $returnToAdditionalLayer ? $returnLayerName : null,
            'forwarded_to_chargeid' => null,
            'forwarded_to_role' => $returnToAdditionalLayer ? 'developer' : 'nicadmin',
            'forwarded_at' => $currentTimestamp,
            'forward_notes' => $notes,
            'status' => 'in_progress',
            'updated_at' => $currentTimestamp,
        ]);

        DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticket->id)
            ->where('developer_userid', HelpdeskSession::userId())
            ->where('status', 'assigned')
            ->update([
                'status' => 'returned',
                'released_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);

        if ($returnToAdditionalLayer) {
            HelpdeskTicketAssignment::create([
                'ticket_id' => $ticket->id,
                'assigned_by_userid' => HelpdeskSession::userId(),
                'assigned_by_name' => HelpdeskSession::userName(),
                'developer_userid' => (string) $returnLayerUserId,
                'developer_name' => $returnLayerName ?: 'Additional NIC Admin',
                'notes' => $notes,
                'status' => 'assigned',
                'assigned_at' => $currentTimestamp,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);
        }

        HelpdeskTicketDevComment::create([
            'ticket_id' => $ticket->id,
            'cams_userid' => HelpdeskSession::userId(),
            'user_name' => HelpdeskSession::userName(),
            'user_role' => HelpdeskSession::roleLabel(),
            'comment' => 'Ticket sent back to '.$returnTargetLabel.'.',
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]);

        $this->notifyHelpdeskTicketAction(
            $ticket,
            'Helpdesk ticket returned to '.$returnTargetLabel,
            'Helpdesk Ticket Returned to '.$returnTargetLabel,
            trim('Ticket sent back to '.$returnTargetLabel.'. '.($validated['send_back_message'] ?? '')),
            $returnToAdditionalLayer ? [$this->developerEmailById($returnLayerUserId)] : $this->helpdeskNicAdminEmails()
        );

        $redirect = $returnToAdditionalLayer
            ? redirect()->route('helpdesk.tickets.index')
            : redirect()->route('helpdesk.tickets.show', $ticket);

        return $redirect->with('success', 'Ticket sent back to '.$returnTargetLabel.'.');
    }

    public function reopen(HelpdeskTicket $ticket): RedirectResponse
    {
        abort_unless(HelpdeskSession::canAccess(), 403);
        abort_unless(HelpdeskSession::userId() === $ticket->cams_userid, 403);
        $currentTimestamp = $this->currentTimestamp();

        $ticket->update([
            'status' => 'open',
            'resolved_at' => null,
            'assigned_to_userid' => null,
            'assigned_to_name' => null,
            'forwarded_to_chargeid' => null,
            'forwarded_to_role' => 'stateadmin',
            'forwarded_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]);

        DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticket->id)
            ->where('status', 'assigned')
            ->update([
                'status' => 'reopened',
                'released_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]);

        HelpdeskTicketComment::create([
            'ticket_id' => $ticket->id,
            'cams_userid' => HelpdeskSession::userId(),
            'user_name' => HelpdeskSession::userName(),
            'user_role' => HelpdeskSession::roleLabel(),
            'comment' => 'Ticket reopened by user.',
            'is_internal' => false,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ]);

        return back()->with('success', 'Ticket reopened successfully.');
    }

    private function authorizeTicketAccess(HelpdeskTicket $ticket): void
    {
        $forwardedRole = $ticket->normalizedForwardedRole();

        if (HelpdeskSession::isSuperAdmin()) {
            if ((string) $ticket->cams_userid === (string) HelpdeskSession::userId()) {
                return;
            }

            if ($this->isNicAdminCreatedPendingTicket($ticket)) {
                abort(403);
            }

            abort_unless(
                in_array($forwardedRole, ['stateadmin'], true)
                || $forwardedRole === null
                || $this->stateAdminHasForwardHistoryToNicAdmin($ticket->id),
                403
            );

            return;
        }

        if (HelpdeskSession::isNicAdmin()) {
            abort_unless(
                in_array($forwardedRole, ['nicadmin', 'developer'], true)
                || $this->nicAdminHasTicketHistory($ticket->id),
                403
            );

            return;
        }

	        if (HelpdeskSession::isDeveloper()) {
	            $currentUserId = HelpdeskSession::userId();
	            $isOwner = $ticket->cams_userid === $currentUserId;
	            $isConcernedDeveloper = $this->isCurrentOrLatestAssignedDeveloper($ticket->id, $currentUserId);
	            $isWatchedDeveloper = $this->isWatchlistedDeveloper($ticket->id, $currentUserId);
	            $isAdditionalLayerHandler = $this->isAdditionalAssignmentLayerUser($currentUserId)
	                && $this->isLatestAssignmentLayerForTicket($ticket->id, $currentUserId);

	            abort_unless($isOwner || $isConcernedDeveloper || $isAdditionalLayerHandler || $isWatchedDeveloper, 403);

            return;
        }

        if (HelpdeskSession::isDepartmentAdmin()) {
            abort_unless($ticket->deptcode === HelpdeskSession::deptCode(), 403);

            return;
        }

        abort_unless($ticket->cams_userid === HelpdeskSession::userId(), 403);
    }

    private function authorizeStaffTicketAccess(HelpdeskTicket $ticket): void
    {
        abort_unless(HelpdeskSession::isStaff(), 403);
        $this->authorizeTicketAccess($ticket);
    }

    private function canCurrentStaffUpdateStatus(HelpdeskTicket $ticket): bool
    {
        $forwardedRole = $ticket->normalizedForwardedRole();

        if (HelpdeskSession::isSuperAdmin() && $forwardedRole !== 'stateadmin') {
            return false;
        }

        if (HelpdeskSession::isNicAdmin() && $forwardedRole !== 'nicadmin') {
            return false;
        }

        if (HelpdeskSession::isDeveloper()) {
            if (! $ticket->isDeveloperStage()) {
                return false;
            }

            if ($ticket->assigned_to_userid !== HelpdeskSession::userId()) {
                return false;
            }
        }

        if (HelpdeskSession::isDeveloper() && $this->isDeveloperStatusLocked($ticket)) {
            return false;
        }

        return true;
    }

    private function canCurrentStaffForwardTicket(HelpdeskTicket $ticket): bool
    {
        $forwardedRole = $ticket->normalizedForwardedRole();

        if (HelpdeskSession::isSuperAdmin() && $forwardedRole !== 'stateadmin') {
            return false;
        }

        if (HelpdeskSession::isNicAdmin() && $forwardedRole !== 'nicadmin') {
            return false;
        }

        if (HelpdeskSession::isDeveloper()) {
            return $this->isAdditionalAssignmentLayerUser(HelpdeskSession::userId())
                && $ticket->isDeveloperStage()
                && (string) $ticket->assigned_to_userid === (string) HelpdeskSession::userId();
        }

        return true;
    }

    private function isStateAdminWaitingForReturn(HelpdeskTicket $ticket): bool
    {
        $forwardedRole = $ticket->normalizedForwardedRole();

        return ! empty($forwardedRole) && $forwardedRole !== 'stateadmin';
    }

    private function lockedStatusSelection(HelpdeskTicket $ticket): string
    {
        $currentDeveloperId = HelpdeskSession::userId();
        $stageStart = $ticket->forwarded_at ?? $ticket->created_at;

        foreach ($ticket->devComments as $comment) {
            if (($comment->user_role ?? null) !== 'Tech Team') {
                continue;
            }

            if ((string) ($comment->cams_userid ?? '') !== (string) $currentDeveloperId) {
                continue;
            }

            if (($comment->created_at ?? null) && $comment->created_at < $stageStart) {
                continue;
            }

            if (str_contains((string) $comment->comment, 'marked the issue as resolved. Ticket remains In Progress until StateAdmin confirms and closes it.')) {
                return 'resolved';
            }

            if (
                preg_match('/^Status changed to (.+)\.$/', (string) $comment->comment, $matches) === 1
                || preg_match('/^.+ updated the status to (.+)\.$/', (string) $comment->comment, $matches) === 1
            ) {
                return Str::of($matches[1])->lower()->replace(' ', '_')->toString();
            }
        }

        return (string) $ticket->status;
    }

    private function isDeveloperStatusLocked(HelpdeskTicket $ticket): bool
    {
        if (! HelpdeskSession::isDeveloper() || ! $ticket->isDeveloperStage()) {
            return false;
        }

        if ($ticket->assigned_to_userid !== HelpdeskSession::userId()) {
            return false;
        }

        $stageStart = $ticket->forwarded_at ?? $ticket->created_at;

        foreach ($ticket->devComments as $comment) {
            if (($comment->user_role ?? null) !== 'Tech Team') {
                continue;
            }

            if ((string) ($comment->cams_userid ?? '') !== (string) HelpdeskSession::userId()) {
                continue;
            }

            if (($comment->created_at ?? null) && $comment->created_at < $stageStart) {
                continue;
            }

            if (
                str_contains((string) $comment->comment, 'marked the issue as resolved. Ticket remains In Progress until StateAdmin confirms and closes it.')
                || preg_match('/^Status changed to (.+)\.$/', (string) $comment->comment) === 1
                || preg_match('/^.+ updated the status to (.+)\.$/', (string) $comment->comment) === 1
            ) {
                return true;
            }
        }

        return false;
    }

	    private function fetchActiveDevelopers($excludeUserId = null)
	    {
	        $excludeUserIds = collect(is_array($excludeUserId) ? $excludeUserId : [$excludeUserId])
	            ->filter(fn ($userId) => trim((string) $userId) !== '')
	            ->map(fn ($userId) => (string) $userId)
	            ->unique()
	            ->values()
	            ->all();

	        return DB::table('audit.dev_userdetails')
            ->where('statusflag', 'Y')
            ->when($excludeUserIds, function ($query) use ($excludeUserIds) {
                $query->whereNotIn('devuserid', $excludeUserIds);
            })
            ->orderBy('devename')
            ->select('devuserid', 'devename', 'email')
	            ->get();
	    }

	    private function ticketWatchlistDevelopers($ticketId)
	    {
	        return DB::table('audit.helpdesk_ticket_assignments')
	            ->where('ticket_id', $ticketId)
	            ->where('status', 'watchlist')
	            ->orderBy('developer_name')
	            ->select('developer_userid', 'developer_name', 'assigned_at')
	            ->get();
	    }

	    private function isWatchlistedDeveloper($ticketId, $developerUserId): bool
	    {
	        if (! $developerUserId) {
	            return false;
	        }

	        return DB::table('audit.helpdesk_ticket_assignments')
	            ->where('ticket_id', $ticketId)
	            ->where('developer_userid', (string) $developerUserId)
	            ->where('status', 'watchlist')
	            ->exists();
	    }

	    private function fetchAdditionalLayerDevelopers()
	    {
	        return DB::table('audit.dev_userdetails')
	            ->where('statusflag', 'Y')
            ->where('senior_flag', 'Y')
            ->orderBy('devename')
	            ->select('devuserid', 'devename', 'email')
	            ->get();
	    }

	    private function defaultSendBackLayerDeveloper()
	    {
	        return DB::table('audit.dev_userdetails')
	            ->where('statusflag', 'Y')
	            ->where('senior_flag', 'Y')
	            ->where(function ($query) {
	                $query->whereRaw('LOWER(devename) like ?', ['%siva%'])
	                    ->orWhereRaw('LOWER(email) like ?', ['%siva%']);
	            })
	            ->orderBy('devename')
	            ->select('devuserid', 'devename', 'email')
	            ->first();
	    }

	    private function isAdditionalAssignmentLayerUser($developerUserId = null): bool
    {
        $developerUserId = $developerUserId ?: HelpdeskSession::userId();

        if (! $developerUserId) {
            return false;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', (string) $developerUserId)
            ->where('statusflag', 'Y')
            ->where('senior_flag', 'Y')
            ->exists();
    }

    private function isLatestAssignmentLayerForTicket($ticketId, $developerUserId): bool
    {
        if (! $developerUserId) {
            return false;
        }

	        return DB::table('audit.helpdesk_ticket_assignments as hta')
	            ->where('hta.ticket_id', $ticketId)
	            ->where('hta.assigned_by_userid', (string) $developerUserId)
	            ->where('hta.status', '!=', 'watchlist')
	            ->whereNotExists(function ($latestAssignmentQuery) {
	                $latestAssignmentQuery->select(DB::raw(1))
	                    ->from('audit.helpdesk_ticket_assignments as hta_latest')
	                    ->whereColumn('hta_latest.ticket_id', 'hta.ticket_id')
	                    ->where('hta_latest.status', '!=', 'watchlist')
	                    ->whereColumn('hta_latest.id', '>', 'hta.id');
	            })
            ->exists();
    }

    private function sendBackTargetLabel(HelpdeskTicket $ticket): string
    {
        if (! HelpdeskSession::isDeveloper()) {
            return 'NIC Admin';
        }

        $assignment = DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticket->id)
            ->where('developer_userid', HelpdeskSession::userId())
            ->where('status', 'assigned')
            ->orderByDesc('id')
            ->first();

	        if ($assignment && $this->isAdditionalAssignmentLayerUser($assignment->assigned_by_userid ?? null)) {
	            return $assignment->assigned_by_name ?: ($this->developerNameById($assignment->assigned_by_userid) ?: 'Additional NIC Admin');
	        }

	        $defaultReturnLayer = $this->defaultSendBackLayerDeveloper();

	        if ($defaultReturnLayer && (string) $defaultReturnLayer->devuserid !== (string) HelpdeskSession::userId()) {
	            return $defaultReturnLayer->devename ?: 'Additional NIC Admin';
	        }

	        return 'NIC Admin';
	    }

    private function nicAdminHasTicketHistory($ticketId): bool
    {
        return DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $ticketId)
            ->where(function ($query) {
                $query->where('comment', 'like', 'Ticket forwarded to NIC Admin.%')
                    ->orWhere('comment', 'Ticket created and forwarded NIC Admin.')
                    ->orWhere('comment', 'like', 'Ticket forwarded to StateAdmin.%');
            })
            ->exists()
            || DB::table('audit.helpdesk_ticket_assignments')
                ->where('ticket_id', $ticketId)
                ->exists();
    }

    private function isCurrentOrLatestAssignedDeveloper($ticketId, $developerUserId): bool
    {
        if (! $developerUserId) {
            return false;
        }

	        return DB::table('audit.helpdesk_ticket_assignments as hta')
	            ->where('hta.ticket_id', $ticketId)
	            ->where('hta.developer_userid', (string) $developerUserId)
	            ->where('hta.status', '!=', 'watchlist')
	            ->whereNotExists(function ($latestAssignmentQuery) {
	                $latestAssignmentQuery->select(DB::raw(1))
	                    ->from('audit.helpdesk_ticket_assignments as hta_latest')
	                    ->whereColumn('hta_latest.ticket_id', 'hta.ticket_id')
	                    ->where('hta_latest.status', '!=', 'watchlist')
	                    ->whereColumn('hta_latest.id', '>', 'hta.id');
	            })
            ->exists();
    }

    private function applyStateAdminSentBackFilter($query): void
    {
        $query->whereNotIn('status', ['resolved', 'closed']);

        $query->whereExists(function ($commentQuery) {
            $commentQuery->select(DB::raw(1))
                ->from('audit.helpdesk_ticket_comments as htc')
                ->whereColumn('htc.ticket_id', 'audit.helpdesk_tickets.id')
                ->where('htc.comment', 'like', 'Ticket forwarded to StateAdmin.%')
                ->where('htc.user_role', 'NIC Admin');
        });
    }

    private function stateAdminSentBackCount(): int
    {
        $query = HelpdeskTicket::query();
        $currentUserId = HelpdeskSession::userId();

        $query->where(function ($builder) use ($currentUserId) {
            $builder->whereIn('forwarded_to_role', ['superadmin', 'stateadmin'])
                ->orWhereNull('forwarded_to_role')
                ->orWhere(function ($historyBuilder) {
                    $historyBuilder->where('forwarded_to_role', 'nicadmin')
                        ->whereExists(function ($commentQuery) {
                            $commentQuery->select(DB::raw(1))
                                ->from('audit.helpdesk_ticket_comments as htc')
                                ->whereColumn('htc.ticket_id', 'audit.helpdesk_tickets.id')
                                ->where(function ($historyQuery) {
                                    $historyQuery->where('htc.comment', 'like', 'Ticket forwarded to NIC Admin.%')
                                        ->orWhere('htc.comment', 'Ticket created and forwarded NIC Admin.');
                                })
                                ->whereIn('htc.user_role', ['StateAdmin', 'IT Team']);
                        });
                });

            if ($currentUserId) {
                $builder->orWhere('cams_userid', $currentUserId);
            }
        });

        $query->where(function ($builder) {
            $builder->where('forwarded_to_role', '!=', 'nicadmin')
                ->orWhereNull('forwarded_to_role')
                ->orWhereNotExists(function ($commentQuery) {
                    $commentQuery->select(DB::raw(1))
                        ->from('audit.helpdesk_ticket_comments as htc')
                        ->whereColumn('htc.ticket_id', 'audit.helpdesk_tickets.id')
                        ->where('htc.comment', 'Ticket created by NIC Admin.');
                });
        });

        $this->applyStateAdminSentBackFilter($query);

        return $query->count();
    }

    private function isNicAdminCreatedPendingTicket(HelpdeskTicket $ticket): bool
    {
        if ($ticket->normalizedForwardedRole() !== 'nicadmin') {
            return false;
        }

        if ($this->stateAdminHasForwardHistoryToNicAdmin($ticket->id)) {
            return false;
        }

        return DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $ticket->id)
            ->where('comment', 'Ticket created by NIC Admin.')
            ->exists();
    }

    private function stateAdminHasForwardHistoryToNicAdmin($ticketId): bool
    {
        return DB::table('audit.helpdesk_ticket_comments')
            ->where('ticket_id', $ticketId)
            ->where(function ($query) {
                $query->where('comment', 'like', 'Ticket forwarded to NIC Admin.%')
                    ->orWhere('comment', 'Ticket created and forwarded NIC Admin.');
            })
            ->whereIn('user_role', ['StateAdmin', 'IT Team'])
            ->exists();
    }

    private function departmentName(?string $deptCode): ?string
    {
        if (! $deptCode) {
            return null;
        }

        return DB::table('audit.mst_dept')
            ->where('deptcode', $deptCode)
            ->value('deptesname');
    }

    private function currentTimestamp(): Carbon
    {
        return Carbon::now('Asia/Kolkata');
    }

    private function syncHelpdeskTicketPrimaryKeySequence(): void
    {
        $sequenceNameResult = DB::selectOne(
            "select pg_get_serial_sequence(?, 'id') as sequence_name",
            ['audit.helpdesk_tickets']
        );

        $sequenceName = $sequenceNameResult->sequence_name ?? null;

        if (! $sequenceName) {
            return;
        }

        DB::statement(
            'select setval(?, coalesce((select max(id) from audit.helpdesk_tickets), 0) + 1, false)',
            [$sequenceName]
        );
    }

    private function isHelpdeskTicketPrimaryKeyViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $message = (string) $exception->getMessage();

        return $sqlState === '23505' && str_contains($message, 'helpdesk_tickets_pkey');
    }

    private function resolveTechTeamStatus(HelpdeskTicket $ticket): string
    {
        $statusUpdates = collect();

        foreach ($ticket->devComments ?? collect() as $comment) {
            $status = $this->extractStatusFromComment((string) $comment->comment);
            if ($status !== null) {
                $statusUpdates->push([
                    'status' => $status,
                    'created_at' => $comment->created_at,
                ]);
            }
        }

        foreach ($ticket->comments ?? collect() as $comment) {
            if (($comment->user_role ?? null) !== 'NIC Admin') {
                continue;
            }

            $status = $this->extractStatusFromComment((string) $comment->comment);
            if ($status !== null) {
                $statusUpdates->push([
                    'status' => $status,
                    'created_at' => $comment->created_at,
                ]);
            }
        }

        $latestStatus = $statusUpdates
            ->sortByDesc('created_at')
            ->first();

        if ($latestStatus) {
            return (string) $latestStatus['status'];
        }

        if (in_array($ticket->normalizedForwardedRole(), ['nicadmin', 'developer'], true)) {
            return (string) $ticket->status;
        }

        return '-';
    }

    private function extractStatusFromComment(string $comment): ?string
    {
        if (str_contains($comment, 'marked the issue as resolved')) {
            return 'resolved';
        }

        if (
            preg_match('/^Status changed to (.+)\.$/', $comment, $matches) === 1
            || preg_match('/^.+ updated the status to (.+)\.$/', $comment, $matches) === 1
        ) {
            return Str::of($matches[1])->lower()->replace(' ', '_')->toString();
        }

        return null;
    }

    private function notifyHelpdeskTicketAction(HelpdeskTicket $ticket, string $subject, string $headline, string $message, array $emails, array $extraDetails = [], array $ccEmails = []): array
    {
        $recipients = collect($emails)
            ->filter()
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->reject(fn (string $email): bool => strtolower($email) === strtolower((string) HelpdeskSession::email()))
            ->unique(fn (string $email): string => strtolower($email))
            ->values();

        $ccRecipients = collect($ccEmails)
            ->filter()
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->reject(fn (string $email): bool => strtolower($email) === strtolower((string) HelpdeskSession::email()))
            ->unique(fn (string $email): string => strtolower($email))
            ->values();

        if ($recipients->isEmpty()) {
            Log::info('Helpdesk ticket email notification skipped because no valid recipient email was found.', [
                'ticket_id' => $ticket->id,
                'subject' => $subject,
                'raw_emails' => $emails,
            ]);

            return [
                'sent' => 0,
                'failed' => 0,
                'recipients' => [],
                'message' => 'Mail not sent: no valid recipient email found.',
            ];
        }

        $details = [
            'Ticket No' => $ticket->ticket_number ?: '-',
            'Subject' => $ticket->subject ?: '-',
            'Department' => $ticket->department_name ?: '-',
            'Category' => $ticket->category ?: '-',
            'Priority' => Str::headline((string) ($ticket->priority ?: '-')),
            'Status' => Str::headline(str_replace('_', ' ', (string) ($ticket->status ?: '-'))),
            'Action By' => HelpdeskSession::userName().' ('.HelpdeskSession::roleLabel().')',
        ];

        $details = array_merge($details, $extraDetails, [
            'Message' => $message,
            'Check' => 'https://cams.tn.gov.in/',
            // 'Ticket Link' => route('helpdesk.tickets.show', $ticket),
        ]);

        $failedMessages = [];
        $primaryEmail = $recipients->first();
        $mailCc = $ccRecipients
            ->reject(fn (string $ccEmail): bool => strtolower($ccEmail) === strtolower((string) $primaryEmail))
            ->unique(fn (string $email): string => strtolower($email))
            ->values()
            ->all();

        try {
            $result = $this->sendHelpdeskEmailNotification($primaryEmail, $subject, $headline, $details, $mailCc);
        } catch (\Throwable $exception) {
            $result = null;
            $failedMessages[] = $primaryEmail.': '.$exception->getMessage();

            Log::error('Helpdesk ticket email notification error.', [
                'ticket_id' => $ticket->id,
                'recipient' => $primaryEmail,
                'cc' => $mailCc,
                'subject' => $subject,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($result !== null && $result !== 'Message has been sent') {
            $failedMessages[] = $primaryEmail.': '.$result;

            Log::warning('Helpdesk ticket email notification failed.', [
                'ticket_id' => $ticket->id,
                'recipient' => $primaryEmail,
                'cc' => $mailCc,
                'subject' => $subject,
                'result' => $result,
            ]);
        }

        $sentCount = $result === 'Message has been sent' ? 1 : 0;

        return [
            'sent' => $sentCount,
            'failed' => count($failedMessages),
            'recipients' => [$primaryEmail],
            'cc' => $mailCc,
            'message' => $sentCount > 0
                ? 'Mail sent successfully.'
                : 'Mail not sent: '.implode(' | ', $failedMessages),
        ];
    }

    private function sendHelpdeskEmailNotification(?string $to, string $subject, string $headline, array $details = [], $cc = [])
    {
        $to = trim((string) $to);

        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid recipient email.';
        }

        $ccEmails = [];
        foreach ((array) $cc as $ccEmail) {
            $ccEmail = trim((string) $ccEmail);

            if ($ccEmail !== '' && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $ccEmails[] = $ccEmail;
            }
        }
        $ccEmails = array_values(array_unique($ccEmails));

        $detailRows = '';
        foreach ($details as $label => $value) {
            $detailRows .= '<tr>'
                .'<td style="padding:8px 12px;border:1px solid #e5e7eb;font-weight:600;background:#f8fafc;">'.htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8').'</td>'
                .'<td style="padding:8px 12px;border:1px solid #e5e7eb;">'.nl2br(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')).'</td>'
                .'</tr>';
        }

        $htmlContent = '<div style="font-family:Arial,sans-serif;color:#111827;line-height:1.5;">'
            .'<h2 style="margin:0 0 12px;color:#0f2f52;">'.htmlspecialchars($headline, ENT_QUOTES, 'UTF-8').'</h2>'
            .'<table style="border-collapse:collapse;width:100%;max-width:720px;font-size:14px;">'.$detailRows.'</table>'
            .'<p style="margin-top:18px;color:#64748b;font-size:13px;">Please login to CAMS Helpdesk for more details.</p>'
            .'</div>';

        return app(PHPMailerService::class)->sendEmail($to, $subject, $htmlContent, $ccEmails);
        // return $result;
    }

    private function helpdeskCommentNotificationEmails(HelpdeskTicket $ticket, string $commentVisibility): array
    {
        if (HelpdeskSession::isSuperAdmin()) {
            return $this->helpdeskNicAdminEmails();
        }

        if ($commentVisibility === 'developer_internal') {
            return HelpdeskSession::isDeveloper()
                ? $this->helpdeskDeveloperLayerEmails($ticket)
                : [$this->developerEmailById($ticket->assigned_to_userid)];
        }

        if ($commentVisibility === 'dg_internal') {
            return HelpdeskSession::isNicAdmin()
                ? array_merge($this->helpdeskStateAdminEmails(), [$this->developerEmailById($ticket->assigned_to_userid)])
                : $this->helpdeskNicAdminEmails();
        }

        return array_merge($this->helpdeskCurrentStageEmails($ticket), [$this->helpdeskTicketOwnerEmail($ticket)]);
    }

    private function helpdeskCommentNotificationCcEmails(string $commentVisibility): array
    {
        if (
            HelpdeskSession::isSuperAdmin()
            || (HelpdeskSession::isNicAdmin() && in_array($commentVisibility, ['developer_internal', 'dg_internal'], true))
        ) {
            return $this->helpdeskInternalCcEmails();
        }

        return [];
    }

    private function helpdeskForwardNotificationCcEmails(string $forwardTo): array
    {
        if (
            (HelpdeskSession::isSuperAdmin() && $forwardTo === 'nicadmin')
            || (HelpdeskSession::isNicAdmin() && in_array($forwardTo, ['developer', 'stateadmin'], true))
        ) {
            return $this->helpdeskInternalCcEmails();
        }

        return [];
    }

    private function helpdeskInternalCcEmails(): array
    {
        return (array) \Illuminate\Support\Facades\View::shared('internal_cc_emails', []);
    }

    private function helpdeskDeveloperLayerEmails(HelpdeskTicket $ticket): array
    {
        $assignment = DB::table('audit.helpdesk_ticket_assignments')
            ->where('ticket_id', $ticket->id)
            ->where('developer_userid', HelpdeskSession::userId())
            ->where('status', 'assigned')
            ->orderByDesc('id')
            ->first();

        if ($assignment && $this->isAdditionalAssignmentLayerUser($assignment->assigned_by_userid ?? null)) {
            return [$this->developerEmailById($assignment->assigned_by_userid)];
        }

        return $this->helpdeskNicAdminEmails();
    }

    private function helpdeskCurrentStageEmails(HelpdeskTicket $ticket): array
    {
        $forwardedRole = $ticket->normalizedForwardedRole();

        if ($forwardedRole === 'developer' && $ticket->assigned_to_userid) {
            return [$this->developerEmailById($ticket->assigned_to_userid)];
        }

        return $this->helpdeskEmailsForForwardRole((string) $forwardedRole, $ticket);
    }

    private function helpdeskEmailsForForwardRole(string $forwardedRole, HelpdeskTicket $ticket): array
    {
        return match ($forwardedRole) {
            'stateadmin', 'superadmin' => $this->helpdeskStateAdminEmails(),
            'nicadmin', 'developer' => $this->helpdeskNicAdminEmails(),
            default => [$this->helpdeskTicketOwnerEmail($ticket)],
        };
    }

    private function helpdeskStateAdminEmails(): array
    {
        return $this->helpdeskDeptUserEmails(function ($query) {
            $query->where('uc.chargeid', '1');
        });
    }

    private function helpdeskNicAdminEmails(): array
    {
        return $this->helpdeskDeptUserEmails(function ($query) {
            $query->where('uc.chargeid', (string) \Illuminate\Support\Facades\View::shared('NICAdminchargeid', '907'));
        });
    }

    private function helpdeskDeptUserEmails(callable $scope): array
    {
        $query = DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'uc.userid')
            ->leftJoin('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->leftJoin('audit.rolemapping as ro', 'ro.rolemappingid', '=', 'ch.rolemappingid')
            ->where('uc.statusflag', 'Y')
            ->where('du.statusflag', 'Y')
            ->whereNotNull('du.email');

        $scope($query);

        return $query
            ->pluck('du.email')
            ->filter()
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique(fn (string $email): string => strtolower($email))
            ->values()
            ->all();
    }

    private function developerEmailById($developerUserId): ?string
    {
        if (! $developerUserId) {
            return null;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', (string) $developerUserId)
            ->where('statusflag', 'Y')
            ->value('email');
    }

    private function developerNameById($developerUserId): ?string
    {
        if (! $developerUserId) {
            return null;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', (string) $developerUserId)
            ->where('statusflag', 'Y')
            ->value('devename');
    }

    private function helpdeskTicketOwnerEmail(HelpdeskTicket $ticket): ?string
    {
        if ($ticket->user_email) {
            return $ticket->user_email;
        }

        if (! $ticket->cams_userid) {
            return null;
        }

        return DB::table('audit.deptuserdetails')
            ->where('deptuserid', $ticket->cams_userid)
            ->value('email');
    }

    private function hasCurrentRoleUpdatedStatusForCurrentStage(HelpdeskTicket $ticket): bool
    {
        $stageStart = $ticket->forwarded_at ?? $ticket->created_at;

        if (HelpdeskSession::isDeveloper()) {
            return HelpdeskTicketDevComment::query()
                ->where('ticket_id', $ticket->id)
                ->where('cams_userid', HelpdeskSession::userId())
                ->where('created_at', '>=', $stageStart)
                ->where(function ($query) {
                    $query->where('comment', 'like', '%updated the status to %')
                        ->orWhere('comment', 'like', 'Status changed to %')
                        ->orWhere('comment', 'like', '%marked the issue as resolved.%');
                })
                ->exists();
        }

        if (HelpdeskSession::isSuperAdmin() || HelpdeskSession::isNicAdmin()) {
            return DB::table('audit.helpdesk_ticket_comments')
                ->where('ticket_id', $ticket->id)
                ->where('user_role', HelpdeskSession::roleLabel())
                ->where('created_at', '>=', $stageStart)
                ->where(function ($query) {
                    $query->where('comment', 'like', 'Status changed to %')
                        ->orWhere('comment', 'like', '%updated the status to %')
                        ->orWhere('comment', 'like', '%marked the issue as resolved.%');
                })
                ->exists();
        }

        return false;
    }

    private function storeAttachments(Request $request): array
    {
        $uploadPath = public_path('uploads/helpdesk');
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $attachments = [];
        foreach ($request->file('attachments', []) as $file) {
            if (! $file) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $storedName = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($uploadPath, $storedName);

            $attachments[] = [
                'name' => $originalName,
                'path' => 'uploads/helpdesk/'.$storedName,
                'size' => $fileSize,
            ];
        }

        return $attachments;
    }
}
