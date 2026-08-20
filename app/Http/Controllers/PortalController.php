<?php

namespace App\Http\Controllers;

use App\Models\PortalModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class PortalController extends Controller
{
    public function dashboard()
    {
        $isInitiator = PortalModel::isInitiator();
        $isApprover = PortalModel::isApprover();
        abort_unless($isInitiator || $isApprover, 403);

        $dashboard = $this->dashboardPayload($isInitiator, $isApprover);

        return view('portal.dashboard', [
            'isInitiator' => $isInitiator,
            'isApprover' => $isApprover,
            'counts' => $dashboard['counts'],
            'identity' => $dashboard['identity'],
            'dashboardMeta' => $dashboard['meta'],
            'sectionCatalog' => PortalModel::sectionCatalog(),
            'departmentSlugs' => PortalModel::departmentSlugs(),
            'departmentScope' => $dashboard['department_scope'],
            'approverDepartmentScope' => $dashboard['approver_department_scope'],
        ]);
    }

    public function dashboardData()
    {
        $isInitiator = PortalModel::isInitiator();
        $isApprover = PortalModel::isApprover();
        abort_unless($isInitiator || $isApprover, 403);

        return response()->json([
            'success' => true,
        ] + $this->dashboardPayload($isInitiator, $isApprover));
    }

    public function index()
    {
        abort_unless(PortalModel::canAccessPortal(), 403);

        return view('portal.content.index', [
            'isApprover' => PortalModel::isApprover(),
            'isInitiator' => PortalModel::isInitiator(),
            'sectionCatalog' => PortalModel::sectionCatalog(),
            'departmentSlugs' => PortalModel::departmentSlugs(),
            'departmentScope' => PortalModel::currentDepartmentScope('initiator'),
        ]);
    }

    public function data(Request $request)
    {
        abort_unless(PortalModel::canAccessPortal(), 403);

        $isApprover = PortalModel::isApprover();
        $isInitiator = PortalModel::isInitiator();
        $currentEmail = PortalModel::currentUserEmail();

        $status = $request->query('status');
        $query = $isApprover
            ? PortalModel::listForApprover($status)
            : PortalModel::listForInitiator()->when($status, fn ($q) => $q->where('status', $status));

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('page', function ($row) {
                return $row->page_key === 'department'
                    ? ('Department: ' . strtoupper((string) $row->page_scope))
                    : 'Home';
            })
            ->addColumn('status_badge', function ($row) {
                $map = ['draft' => 'secondary', 'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                $class = $map[$row->status] ?? 'secondary';
                $badge = '<span class="badge bg-' . $class . '">' . ucfirst($row->status) . '</span>';
                if ($row->status === 'approved') {
                    $badge .= $row->statusflag === 'Y'
                        ? ' <span class="badge bg-info">Active</span>'
                        : ' <span class="badge bg-light text-dark">Inactive</span>';
                }

                return $badge;
            })
            ->addColumn('action', function ($row) use ($isApprover, $isInitiator, $currentEmail) {
                return view('portal.content._actions', [
                    'row' => $row,
                    'isApprover' => $isApprover,
                    'isInitiator' => $isInitiator,
                    'isOwner' => strcasecmp((string) $row->created_by_email, (string) $currentEmail) === 0,
                ])->render();
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function create(Request $request)
    {
        abort_unless(PortalModel::isInitiator(), 403);

        $pageKey = $request->query('page_key', 'home');
        $sectionKey = $request->query('section_key', 'updates');
        $pageScope = $pageKey === 'department' ? $request->query('page_scope') : null;

        $config = PortalModel::sectionConfig($pageKey, $sectionKey);
        abort_if(!$config, 404, 'Unknown section.');

        if ($pageKey === 'department') {
            abort_unless(in_array($pageScope, PortalModel::departmentSlugs(), true), 404, 'Unknown department.');
        }

        abort_unless(PortalModel::canManage($pageKey, $pageScope), 403, 'You can only manage content for your assigned department.');

        if ($config['singleton'] ?? false) {
            $existing = PortalModel::query()
                ->where('page_key', $pageKey)
                ->where('section_key', $sectionKey)
                ->when($pageScope, fn ($q) => $q->where('page_scope', $pageScope), fn ($q) => $q->whereNull('page_scope'))
                ->orderByDesc('updated_at')
                ->first();

            if ($existing) {
                return redirect()->route('portal.content.edit', $existing->id);
            }
        }

        return view('portal.content.form', [
            'item' => null,
            'pageKey' => $pageKey,
            'sectionKey' => $sectionKey,
            'pageScope' => $pageScope,
            'config' => $config,
            'departmentSlugs' => PortalModel::departmentSlugs(),
            'sectionCatalog' => PortalModel::sectionCatalog(),
            'referenceContent' => $this->currentSiteContent($pageKey, $pageScope, $sectionKey),
            'siblingItems' => $this->siblingItems($pageKey, $pageScope, $sectionKey, null),
            'navTargetOptions' => $sectionKey === 'nav_menu' ? PortalModel::navTargetOptions($pageScope) : [],
        ]);
    }

    private function siblingItems(string $pageKey, ?string $pageScope, string $sectionKey, ?int $excludeId)
    {
        if ($sectionKey !== 'nav_menu') {
            return collect();
        }

        return PortalModel::query()
            ->where('page_key', $pageKey)
            ->when($pageScope, fn ($q) => $q->where('page_scope', $pageScope), fn ($q) => $q->whereNull('page_scope'))
            ->where('section_key', 'nav_menu')
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get();
    }

    // Combined "manage this section" view for list-type sections: shows every
    // live item plus the current user's own in-progress items together, with
    // inline add/edit/submit/remove — each item still goes through its own
    // draft -> submit -> approve lifecycle via the existing endpoints below,
    // this just avoids navigating to a separate page per bullet.
    public function manage(Request $request)
    {
        abort_unless(PortalModel::canAccessPortal(), 403);

        $pageKey = $request->query('page_key', 'home');
        $sectionKey = $request->query('section_key');
        $pageScope = $pageKey === 'department' ? $request->query('page_scope') : null;

        $config = PortalModel::sectionConfig($pageKey, $sectionKey);
        abort_if(!$config, 404, 'Unknown section.');
        abort_if($config['singleton'] ?? false, 404, 'This section is a single record — use the edit form instead.');

        if ($pageKey === 'department') {
            abort_unless(in_array($pageScope, PortalModel::departmentSlugs(), true), 404, 'Unknown department.');
        }

        $email = PortalModel::currentUserEmail();
        $hasFileField = collect($config['fields'])->contains(fn ($field) => $field['type'] === 'file');

        $items = PortalModel::query()
            ->where('page_key', $pageKey)
            ->where('section_key', $sectionKey)
            ->when($pageScope, fn ($q) => $q->where('page_scope', $pageScope), fn ($q) => $q->whereNull('page_scope'))
            ->where(function ($q) use ($email) {
                $q->where('status', 'approved');
                if ($email) {
                    $q->orWhereRaw('LOWER(created_by_email) = ?', [strtolower($email)]);
                }
            })
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        return view('portal.content.manage', [
            'pageKey' => $pageKey,
            'sectionKey' => $sectionKey,
            'pageScope' => $pageScope,
            'config' => $config,
            'items' => $items,
            'currentEmail' => $email,
            'isApprover' => PortalModel::isApprover(),
            'canManageSection' => PortalModel::isInitiator() && PortalModel::canManage($pageKey, $pageScope),
            'canAdd' => PortalModel::isInitiator() && !$hasFileField && PortalModel::canManage($pageKey, $pageScope),
            'navTargetOptions' => $sectionKey === 'nav_menu' ? PortalModel::navTargetOptions($pageScope) : [],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(PortalModel::isInitiator(), 403);

        $request->validate([
            'page_key' => 'required|in:home,department',
            'page_scope' => 'nullable|string',
            'section_key' => 'required|string',
            'title' => 'nullable|string|max:250',
        ]);

        $pageKey = $request->input('page_key');
        $pageScope = $pageKey === 'department' ? $request->input('page_scope') : null;
        $sectionKey = $request->input('section_key');

        $config = PortalModel::sectionConfig($pageKey, $sectionKey);
        abort_if(!$config, 422, 'Invalid section.');

        if ($pageKey === 'department') {
            abort_unless(in_array($pageScope, PortalModel::departmentSlugs(), true), 422, 'Invalid department.');
        }

        abort_unless(PortalModel::canManage($pageKey, $pageScope), 403, 'You can only manage content for your assigned department.');

        try {
            $this->assertRequiredExtras($request, $sectionKey, true, []);
            $content = $this->validateAndBuildContent($request, $sectionKey, $config['fields'], []);

            $item = PortalModel::createDraft([
                'page_key' => $pageKey,
                'page_scope' => $pageScope,
                'section_key' => $sectionKey,
                'title' => $request->input('title'),
                'content' => $content,
                'display_order' => (int) $request->input('display_order', 0),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Saved as draft.',
                'redirect_url' => route('portal.content.edit', $item->id),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Please fix the errors below.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save content.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        abort_unless(PortalModel::isInitiator(), 403);

        $item = PortalModel::findOrFail($id);
        abort_unless(PortalModel::canManage($item->page_key, $item->page_scope), 403, 'You can only manage content for your assigned department.');

        if ($item->status !== 'approved') {
            abort_unless($this->ownsItem($item), 403, 'Only the creator can edit this draft.');
        }
        abort_if($item->status === 'pending', 403, 'This item is awaiting approval and cannot be edited right now.');

        $config = PortalModel::sectionConfig($item->page_key, $item->section_key);

        return view('portal.content.form', [
            'item' => $item,
            'pageKey' => $item->page_key,
            'sectionKey' => $item->section_key,
            'pageScope' => $item->page_scope,
            'config' => $config,
            'departmentSlugs' => PortalModel::departmentSlugs(),
            'sectionCatalog' => PortalModel::sectionCatalog(),
            'referenceContent' => $this->currentSiteContent($item->page_key, $item->page_scope, $item->section_key),
            'siblingItems' => $this->siblingItems($item->page_key, $item->page_scope, $item->section_key, $item->id),
            'navTargetOptions' => $item->section_key === 'nav_menu' ? PortalModel::navTargetOptions($item->page_scope) : [],
        ]);
    }

    public function update(Request $request, int $id)
    {
        abort_unless(PortalModel::isInitiator(), 403);

        $existingItem = PortalModel::findOrFail($id);
        abort_unless(PortalModel::canManage($existingItem->page_key, $existingItem->page_scope), 403, 'You can only manage content for your assigned department.');
        if ($existingItem->status !== 'approved') {
            abort_unless($this->ownsItem($existingItem), 403, 'Only the creator can edit this draft.');
        }
        abort_if($existingItem->status === 'pending', 422, 'This item is awaiting approval and cannot be edited right now.');

        $config = PortalModel::sectionConfig($existingItem->page_key, $existingItem->section_key);

        try {
            $existingContent = $existingItem->content ?? [];
            $this->assertRequiredExtras($request, $existingItem->section_key, false, $existingContent);
            $content = $this->validateAndBuildContent($request, $existingItem->section_key, $config['fields'], $existingContent);

            $item = PortalModel::updateOrCloneDraft($id, [
                'title' => $request->input('title'),
                'content' => $content,
                'display_order' => (int) $request->input('display_order', $existingItem->display_order),
            ]);

            return response()->json([
                'success' => true,
                'message' => $item->id === $existingItem->id ? 'Updated.' : 'Saved as a new draft pending re-approval.',
                'redirect_url' => route('portal.content.edit', $item->id),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Please fix the errors below.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update content.', 'error' => $e->getMessage()], 500);
        }
    }

    public function submit(int $id)
    {
        abort_unless(PortalModel::isInitiator(), 403);

        $item = PortalModel::findOrFail($id);
        abort_unless($this->ownsItem($item), 403);

        try {
            PortalModel::submitForApproval($id);

            return response()->json(['success' => true, 'message' => 'Submitted for approval.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve(Request $request, int $id)
    {
        abort_unless(PortalModel::isApprover(), 403);
        $item = PortalModel::findOrFail($id);
        abort_unless(PortalModel::canManage($item->page_key, $item->page_scope, 'approver'), 403, 'You can only manage content for your assigned department.');

        try {
            PortalModel::approve($id, $request->input('remarks'));

            return response()->json(['success' => true, 'message' => 'Approved and published.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function reject(Request $request, int $id)
    {
        abort_unless(PortalModel::isApprover(), 403);
        $item = PortalModel::findOrFail($id);
        abort_unless(PortalModel::canManage($item->page_key, $item->page_scope, 'approver'), 403, 'You can only manage content for your assigned department.');
        $request->validate(['remarks' => 'required|string|max:500']);

        try {
            PortalModel::reject($id, $request->input('remarks'));

            return response()->json(['success' => true, 'message' => 'Rejected.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function toggleActive(int $id)
    {
        abort_unless(PortalModel::isApprover(), 403);
        $item = PortalModel::findOrFail($id);
        abort_unless(PortalModel::canManage($item->page_key, $item->page_scope, 'approver'), 403, 'You can only manage content for your assigned department.');

        try {
            $item = PortalModel::toggleActive($id);

            return response()->json(['success' => true, 'message' => $item->statusflag === 'Y' ? 'Activated.' : 'Deactivated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function history(int $id)
    {
        abort_unless(PortalModel::canAccessPortal(), 403);
        PortalModel::findOrFail($id);

        return response()->json(['success' => true, 'history' => PortalModel::historyFor($id)]);
    }

    public function destroy(int $id)
    {
        abort_unless(PortalModel::isInitiator(), 403);

        $item = PortalModel::findOrFail($id);
        abort_unless($this->ownsItem($item), 403);
        abort_unless($item->status === 'draft', 422, 'Only never-submitted drafts can be deleted.');

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Deleted.']);
    }

    public function users()
    {
        abort_unless(PortalModel::isApprover(), 403);

        return view('portal.users.index', [
            'departmentSlugs' => PortalModel::departmentSlugs(),
        ]);
    }

    public function usersData()
    {
        abort_unless(PortalModel::isApprover(), 403);

        $query = PortalModel::listPortalUsers();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status_badge', function ($row) {
                return $row->statusflag === 'Y'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return view('portal.users._actions', ['row' => $row])->render();
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function lookupUser(Request $request)
    {
        abort_unless(PortalModel::isApprover(), 403);
        $request->validate(['email' => 'required|email']);

        $identity = PortalModel::findLoginIdentity($request->input('email'));
        if (!$identity) {
            return response()->json(['success' => false, 'message' => 'No active CAMS login found with that email.']);
        }

        $suggestedDepartment = PortalModel::suggestedDepartmentSlug($identity->source_table, $identity->login_userid);

        return response()->json(['success' => true, 'identity' => $identity, 'suggested_department' => $suggestedDepartment]);
    }

    public function assignRole(Request $request)
    {
        abort_unless(PortalModel::isApprover(), 403);
        $request->validate([
            'login_email' => 'required|email',
            'role' => 'required|in:initiator,approver',
            'department_scope' => 'nullable|in:' . implode(',', PortalModel::departmentSlugs()),
        ]);

        $identity = PortalModel::findLoginIdentity($request->input('login_email'));
        if (!$identity) {
            return response()->json(['success' => false, 'message' => 'No active CAMS login found with that email.'], 422);
        }

        try {
            PortalModel::assignRole([
                'login_email' => $identity->login_email,
                'role' => $request->input('role'),
                'department_scope' => $request->input('department_scope') ?: null,
                'source_table' => $identity->source_table,
                'login_userid' => $identity->login_userid,
                'display_name' => $identity->display_name,
            ]);

            return response()->json(['success' => true, 'message' => 'Role assigned.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to assign role.', 'error' => $e->getMessage()], 500);
        }
    }

    public function toggleUserActive(int $id)
    {
        abort_unless(PortalModel::isApprover(), 403);
        PortalModel::toggleUserActive($id);

        return response()->json(['success' => true, 'message' => 'Updated.']);
    }

    public function changeUserRole(Request $request, int $id)
    {
        abort_unless(PortalModel::isApprover(), 403);
        $request->validate(['role' => 'required|in:initiator,approver']);
        PortalModel::changeUserRole($id, $request->input('role'));

        return response()->json(['success' => true, 'message' => 'Updated.']);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    private function dashboardPayload(bool $isInitiator, bool $isApprover): array
    {
        $departmentScope = PortalModel::currentDepartmentScope('initiator');
        $approverDepartmentScope = PortalModel::currentDepartmentScope('approver');
        $counts = PortalModel::dashboardCounts($isInitiator, $isApprover, $approverDepartmentScope);
        $user = PortalModel::currentUser();
        $charge = session('charge');
        $roles = [];

        if ($isInitiator) {
            $roles[] = 'Initiator';
        }
        if ($isApprover) {
            $roles[] = 'Approver';
        }

        $department = ($charge->deptesname ?? null)
            ?? ($charge->auditeedeptename ?? null)
            ?? ($charge->instename ?? null)
            ?? $departmentScope
            ?? $approverDepartmentScope
            ?? 'All departments';

        $lastLogin = 'N/A';
        if (!empty($user->lastlogin)) {
            try {
                $lastLogin = Carbon::parse($user->lastlogin)->format('d-m-Y h:i A');
            } catch (\Exception $e) {
                $lastLogin = (string) $user->lastlogin;
            }
        }

        return [
            'counts' => $counts,
            'department_scope' => $departmentScope,
            'approver_department_scope' => $approverDepartmentScope,
            'identity' => [
                'name' => PortalModel::currentUserName() ?: 'Portal user',
                'email' => PortalModel::currentUserEmail() ?: 'N/A',
                'user_id' => PortalModel::currentUserId() ?: 'N/A',
                'department' => $department,
                'designation' => ($charge->desigelname ?? null) ?: 'N/A',
                'roles' => implode(' + ', $roles),
                'initiator_scope' => $departmentScope ? strtoupper($departmentScope) : 'All departments',
                'approver_scope' => $approverDepartmentScope ? strtoupper($approverDepartmentScope) : 'All departments',
                'last_login' => $lastLogin,
            ],
            'meta' => [
                'my_total' => (int) ($counts['draft'] + $counts['pending'] + $counts['approved'] + $counts['rejected']),
                'review_total' => (int) ($counts['queue_pending'] + $counts['queue_approved']),
                'updated_at' => now()->format('d-m-Y h:i A'),
            ],
        ];
    }

    private function ownsItem($item): bool
    {
        return strcasecmp((string) $item->created_by_email, (string) PortalModel::currentUserEmail()) === 0;
    }

    // What's currently live for this page/section — used as read-only reference
    // (list sections) or a starting-point prefill (singletons) on the form, so an
    // initiator can see/build on what's already published instead of starting blank.
    private function currentSiteContent(string $pageKey, ?string $pageScope, string $sectionKey)
    {
        if ($pageKey === 'home') {
            return \App\Models\DgaModel::homeContent()[$sectionKey] ?? null;
        }

        $department = $pageScope ? \App\Models\DgaModel::departmentContent($pageScope) : null;

        return $department[$sectionKey] ?? null;
    }

    private function validateAndBuildContent(Request $request, string $sectionKey, array $fields, array $existing): array
    {
        $rules = [];
        foreach ($fields as $key => $field) {
            $rules[$key] = $field['rules'];
        }
        $request->validate($rules);

        return $this->buildContentPayload($request, $sectionKey, $fields, $existing);
    }

    private function assertRequiredExtras(Request $request, string $sectionKey, bool $isCreate, array $existing): void
    {
        if ($sectionKey === 'gallery' && $isCreate && !$request->hasFile('image')) {
            throw ValidationException::withMessages(['image' => 'An image is required.']);
        }

        if ($sectionKey === 'downloads') {
            $source = $request->input('source');
            if ($source === 'upload' && !$request->hasFile('file') && empty($existing['href'] ?? null)) {
                throw ValidationException::withMessages(['file' => 'Please upload a file.']);
            }
            if ($source === 'link' && !$request->filled('link')) {
                throw ValidationException::withMessages(['link' => 'Please provide a URL.']);
            }
        }

        if ($sectionKey === 'structure_map') {
            $this->assertValidStructureMap($request->input('json'));
        }
    }

    // department.blade.php's org-chart renderer has two code paths: a rich one
    // used when "left" is a non-empty array, and a legacy fallback (for when
    // "left" is empty) that expects root/lead to be plain strings and crashes
    // the whole public page if they're arrays instead. To avoid ever hitting
    // that fallback, we only accept the rich shape here.
    private function assertValidStructureMap(?string $json): void
    {
        $decoded = json_decode((string) $json, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages(['json' => 'Structure map must be valid JSON.']);
        }

        $hasNode = fn ($node) => is_array($node) && !empty($node['title']) && !empty($node['detail']);

        if (!$hasNode($decoded['root'] ?? null)) {
            throw ValidationException::withMessages(['json' => '"root" must be an object with "title" and "detail".']);
        }
        if (!$hasNode($decoded['lead'] ?? null)) {
            throw ValidationException::withMessages(['json' => '"lead" must be an object with "title" and "detail".']);
        }
        if (empty($decoded['left']) || !is_array($decoded['left'])) {
            throw ValidationException::withMessages(['json' => '"left" must be a non-empty array of {title, detail} nodes.']);
        }

        foreach (['left', 'right', 'common'] as $branchKey) {
            foreach (($decoded[$branchKey] ?? []) as $node) {
                if (!$hasNode($node)) {
                    throw ValidationException::withMessages(['json' => "Every item in \"{$branchKey}\" needs a \"title\" and \"detail\"."]);
                }
            }
        }
    }

    private function buildContentPayload(Request $request, string $sectionKey, array $fields, array $existing): array
    {
        if ($sectionKey === 'gallery') {
            $src = $existing['src'] ?? null;
            if ($request->hasFile('image')) {
                $src = $request->file('image')->store('portal/gallery', 'public');
            }

            return [
                'src' => $src,
                'alt' => $request->input('alt'),
            ];
        }

        if ($sectionKey === 'downloads') {
            $source = $request->input('source', 'link');
            if ($source === 'upload') {
                $href = $existing['href'] ?? null;
                if ($request->hasFile('file')) {
                    $href = $request->file('file')->store('portal/downloads', 'public');
                }
            } else {
                $href = $request->input('link');
            }

            return [
                'title' => $request->input('title'),
                'meta' => $request->input('meta'),
                'source' => $source,
                'href' => $href,
            ];
        }

        if ($sectionKey === 'regions') {
            $places = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('places')))));

            return [
                'name' => $request->input('name'),
                'places' => $places,
            ];
        }

        if ($sectionKey === 'structure_map') {
            return (array) json_decode((string) $request->input('json'), true);
        }

        if ($sectionKey === 'nav_menu') {
            $parentId = $request->input('parent_id');

            return [
                'label' => $request->input('label'),
                'icon' => 'fas fa-circle',
                'target' => $request->input('target'),
                'parent_id' => ($parentId !== null && $parentId !== '') ? (int) $parentId : null,
            ];
        }

        $content = [];
        foreach ($fields as $key => $field) {
            if ($field['type'] === 'file') {
                continue;
            }
            $content[$key] = $request->input($key);
        }

        return $content;
    }
}
