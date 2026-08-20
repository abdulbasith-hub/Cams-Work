<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortalModel extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'portal.dga_content';

    protected $fillable = [
        'page_key', 'page_scope', 'section_key', 'title', 'content', 'display_order',
        'status', 'statusflag', 'replaces_id',
        'created_by_userid', 'created_by_name', 'created_by_email',
        'submitted_at', 'approved_by_userid', 'approved_by_name', 'approved_by_email',
        'approved_at', 'remarks',
    ];

    protected $casts = [
        'content' => 'array',
        'display_order' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ---------------------------------------------------------------------
    // Static config — drives the form fields, validation, and page/section
    // combinations allowed. Keep in sync with the migration's CHECK constraints.
    // ---------------------------------------------------------------------

    public static function departmentSlugs(): array
    {
        return ['lfa', 'sgad', 'hria', 'dca', 'milk'];
    }

    // The fixed anchors that always exist on a department page layout,
    // plus whichever custom_block sections have been created for it.
    public static function fixedNavTargets(): array
    {
        return [
            '#structure' => ['label' => 'Structure Map', 'section_key' => 'structure_map', 'singleton' => true],
            '#establishment' => ['label' => 'Establishment', 'section_key' => 'establishment', 'singleton' => false],
            '#acts' => ['label' => 'Acts and Rules', 'section_key' => 'acts', 'singleton' => false],
            '#functions' => ['label' => 'Functions', 'section_key' => 'functions', 'singleton' => false],
            '#regions' => ['label' => 'Regional Map', 'section_key' => 'regions', 'singleton' => false],
            '#institutions' => ['label' => 'Institutions', 'section_key' => 'institutions', 'singleton' => false],
            '#highlights' => ['label' => 'Highlights', 'section_key' => 'achievements', 'singleton' => false],
            '#contact' => ['label' => 'Contact', 'section_key' => 'contact', 'singleton' => true],
        ];
    }

    public static function navTargetOptions(?string $pageScope): array
    {
        $options = collect(self::fixedNavTargets())->map(fn ($t) => $t['label'])->all();

        $blocks = DB::table('portal.dga_content')
            ->where('page_key', 'department')
            ->where('page_scope', $pageScope)
            ->where('section_key', 'custom_block')
            ->where('status', 'approved')
            ->orderBy('id')
            ->get(['id', 'content']);

        foreach ($blocks as $block) {
            $content = json_decode($block->content, true) ?: [];
            $options['#custom-' . $block->id] = ($content['title'] ?? 'Custom block') . ' (custom)';
        }

        return $options;
    }

    // audit.mst_dept deptcode -> DGA homepage department slug
    public static function deptcodeToSlug(): array
    {
        return [
            '01' => 'hria',
            '02' => 'lfa',
            '03' => 'sgad',
            '04' => 'dca',
            '05' => 'milk',
        ];
    }

    public static function slugForDeptcode(?string $deptcode): ?string
    {
        if (!$deptcode) {
            return null;
        }

        return self::deptcodeToSlug()[$deptcode] ?? null;
    }

    // Best-effort lookup of the department slug implied by a login's active
    // charge (audit.userchargedetails/chargedetails), used to suggest a
    // department scope when assigning the initiator role.
    public static function suggestedDepartmentSlug(string $sourceTable, string $loginUserid): ?string
    {
        if ($sourceTable !== 'audit.deptuserdetails') {
            return null;
        }

        $charge = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->where('uc.userid', $loginUserid)
            ->where('uc.statusflag', 'Y')
            ->orderByDesc('uc.chargefrom')
            ->select('c.deptcode')
            ->first();

        return self::slugForDeptcode($charge->deptcode ?? null);
    }

    public static function sectionCatalog(): array
    {
        return [
            'home' => [
                'updates' => [
                    'label' => 'Update / Notice',
                    'singleton' => false,
                    'fields' => [
                        'date' => ['type' => 'date', 'label' => 'Date', 'rules' => 'required|date'],
                        'text' => ['type' => 'text', 'label' => 'Notice text', 'rules' => 'required|string|max:500'],
                    ],
                ],
                'gallery' => [
                    'label' => 'Gallery image',
                    'singleton' => false,
                    'fields' => [
                        'image' => ['type' => 'file', 'label' => 'Image', 'rules' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096'],
                        'alt' => ['type' => 'text', 'label' => 'Alt text', 'rules' => 'required|string|max:200'],
                    ],
                ],
                'downloads' => [
                    'label' => 'Download / file link',
                    'singleton' => false,
                    'fields' => [
                        'title' => ['type' => 'text', 'label' => 'Title', 'rules' => 'required|string|max:250'],
                        'meta' => ['type' => 'text', 'label' => 'Meta (e.g. "PDF Act")', 'rules' => 'nullable|string|max:100'],
                        'source' => ['type' => 'select', 'label' => 'Source', 'options' => ['upload' => 'Uploaded file', 'link' => 'External link'], 'rules' => 'required|in:upload,link'],
                        'file' => ['type' => 'file', 'label' => 'File (if uploaded)', 'rules' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240'],
                        'link' => ['type' => 'text', 'label' => 'URL (if external link)', 'rules' => 'nullable|url|max:500'],
                    ],
                ],
                'services' => [
                    'label' => 'Service card',
                    'singleton' => false,
                    'fields' => [
                        'icon' => ['type' => 'text', 'label' => 'Bootstrap icon class (e.g. bi-shield-check)', 'rules' => 'required|string|max:60'],
                        'title' => ['type' => 'text', 'label' => 'Title', 'rules' => 'required|string|max:150'],
                        'description' => ['type' => 'text', 'label' => 'Description', 'rules' => 'required|string|max:300'],
                        'href' => ['type' => 'text', 'label' => 'Link (anchor or URL)', 'rules' => 'required|string|max:300'],
                    ],
                ],
                'contact' => [
                    'label' => 'Contact information',
                    'singleton' => true,
                    'fields' => [
                        'office' => ['type' => 'text', 'label' => 'Office name', 'rules' => 'required|string|max:250'],
                        'address' => ['type' => 'text', 'label' => 'Address', 'rules' => 'required|string|max:500'],
                        'phone' => ['type' => 'text', 'label' => 'Phone', 'rules' => 'required|string|max:50'],
                        'email' => ['type' => 'text', 'label' => 'Email', 'rules' => 'required|email|max:150'],
                        'map_embed' => ['type' => 'text', 'label' => 'Map embed URL', 'rules' => 'nullable|url|max:1000'],
                    ],
                ],
            ],
            'department' => [
                'contact' => [
                    'label' => 'Department contact',
                    'singleton' => true,
                    'fields' => [
                        'office' => ['type' => 'text', 'label' => 'Office name', 'rules' => 'required|string|max:250'],
                        'address' => ['type' => 'text', 'label' => 'Address', 'rules' => 'required|string|max:500'],
                        'phone' => ['type' => 'text', 'label' => 'Phone', 'rules' => 'required|string|max:50'],
                        'email' => ['type' => 'text', 'label' => 'Email', 'rules' => 'required|email|max:150'],
                    ],
                ],
                'establishment' => [
                    'label' => 'Establishment history entry',
                    'singleton' => false,
                    'fields' => [
                        'text' => ['type' => 'text', 'label' => 'Bullet text (e.g. a G.O. order / milestone)', 'rules' => 'required|string|max:600'],
                    ],
                ],
                'acts' => [
                    'label' => 'Act / Rule',
                    'singleton' => false,
                    'fields' => [
                        'text' => ['type' => 'text', 'label' => 'Act / rule name', 'rules' => 'required|string|max:300'],
                    ],
                ],
                'functions' => [
                    'label' => 'Function',
                    'singleton' => false,
                    'fields' => [
                        'text' => ['type' => 'text', 'label' => 'Function / responsibility', 'rules' => 'required|string|max:600'],
                    ],
                ],
                'achievements' => [
                    'label' => 'Achievement / highlight',
                    'singleton' => false,
                    'fields' => [
                        'text' => ['type' => 'text', 'label' => 'Achievement text', 'rules' => 'required|string|max:600'],
                    ],
                ],
                'institutions' => [
                    'label' => 'Institution stat',
                    'singleton' => false,
                    'fields' => [
                        'label' => ['type' => 'text', 'label' => 'Label (e.g. "Total Auditable Institutions")', 'rules' => 'required|string|max:150'],
                        'value' => ['type' => 'text', 'label' => 'Value (e.g. "4229")', 'rules' => 'required|string|max:50'],
                    ],
                ],
                'regions' => [
                    'label' => 'Region',
                    'singleton' => false,
                    'fields' => [
                        'name' => ['type' => 'text', 'label' => 'Region name', 'rules' => 'required|string|max:150'],
                        'places' => ['type' => 'text', 'label' => 'Places (comma-separated)', 'rules' => 'required|string|max:1000'],
                    ],
                ],
                'structure_map' => [
                    'label' => 'Org chart / structure map',
                    'singleton' => true,
                    'fields' => [
                        'json' => [
                            'type' => 'json',
                            'label' => 'Structure map',
                            'rules' => 'required|json',
                        ],
                    ],
                ],
                'nav_menu' => [
                    'label' => 'Department Navigator menu item',
                    'singleton' => false,
                    'fields' => [
                        'label' => ['type' => 'text', 'label' => 'Menu label', 'rules' => 'required|string|max:100'],
                        'target' => [
                            'type' => 'target_select',
                            'label' => 'Links to',
                            // array form, not a pipe-delimited string: the regex itself
                            // contains "|" characters that Laravel's rule-string parser
                            // would otherwise split on, breaking validation.
                            'rules' => ['required', 'regex:/^#(structure|establishment|acts|functions|regions|institutions|highlights|contact|custom-\d+)$/'],
                        ],
                        'parent_id' => ['type' => 'parent_select', 'label' => 'Parent menu item (leave blank for a top-level item)', 'rules' => 'nullable|integer'],
                    ],
                ],
                'custom_block' => [
                    'label' => 'Custom content block',
                    'singleton' => false,
                    'fields' => [
                        'title' => ['type' => 'text', 'label' => 'Heading', 'rules' => 'required|string|max:150'],
                        'body' => ['type' => 'textarea', 'label' => 'Content (one paragraph per line)', 'rules' => 'required|string|max:5000'],
                    ],
                ],
            ],
        ];
    }

    public static function sectionConfig(string $pageKey, string $sectionKey): ?array
    {
        return self::sectionCatalog()[$pageKey][$sectionKey] ?? null;
    }

    public static function sectionsForPage(string $pageKey): array
    {
        return self::sectionCatalog()[$pageKey] ?? [];
    }

    // ---------------------------------------------------------------------
    // Session / role helpers — this app has no Laravel guard system, roles
    // are resolved from session('user') (populated by the existing LoginController)
    // ---------------------------------------------------------------------

    public static function currentUser(): ?object
    {
        return session('user');
    }

    public static function currentUserEmail(): ?string
    {
        return self::currentUser()->email ?? null;
    }

    public static function currentUserName(): ?string
    {
        $user = self::currentUser();

        return $user->username ?? $user->email ?? null;
    }

    public static function currentUserId(): ?string
    {
        $user = self::currentUser();
        if (!$user) {
            return null;
        }

        foreach (['userid', 'deptuserid', 'devuserid', 'auditeeuserid', 'auditeedeptid'] as $key) {
            if (isset($user->$key)) {
                return (string) $user->$key;
            }
        }

        return null;
    }

    public static function currentRoles(): array
    {
        $email = self::currentUserEmail();
        if (!$email) {
            return [];
        }

        return DB::table('portal.portal_users')
            ->whereRaw('LOWER(login_email) = ?', [strtolower($email)])
            ->where('statusflag', 'Y')
            ->pluck('role')
            ->all();
    }

    public static function isInitiator(): bool
    {
        return in_array('initiator', self::currentRoles(), true);
    }

    public static function isApprover(): bool
    {
        return in_array('approver', self::currentRoles(), true);
    }

    public static function canAccessPortal(): bool
    {
        return self::isInitiator() || self::isApprover();
    }

    // Null = unrestricted (may manage home content and every department).
    // A slug (e.g. 'lfa') = may only manage that department's content.
    // $role selects which role's scope to check ('initiator' for create/edit
    // actions, 'approver' for approve/reject/activate actions) since a person
    // could hold both roles with different scopes.
    public static function currentDepartmentScope(string $role = 'initiator'): ?string
    {
        $email = self::currentUserEmail();
        if (!$email) {
            return null;
        }

        $row = DB::table('portal.portal_users')
            ->whereRaw('LOWER(login_email) = ?', [strtolower($email)])
            ->where('role', $role)
            ->where('statusflag', 'Y')
            ->first();

        return $row->department_scope ?? null;
    }

    public static function canManage(string $pageKey, ?string $pageScope, string $role = 'initiator'): bool
    {
        $scope = self::currentDepartmentScope($role);
        if (!$scope) {
            return true;
        }

        return $pageKey === 'department' && $pageScope === $scope;
    }

    // ---------------------------------------------------------------------
    // Listing (feeds Yajra DataTables)
    // ---------------------------------------------------------------------

    public static function listForInitiator()
    {
        $email = self::currentUserEmail();

        return self::query()
            ->when($email, fn ($q) => $q->whereRaw('LOWER(created_by_email) = ?', [strtolower((string) $email)]))
            ->orderByDesc('updated_at');
    }

    public static function listForApprover(?string $status = null)
    {
        $scope = self::currentDepartmentScope('approver');

        return self::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($scope, fn ($q) => $q->where('page_key', 'department')->where('page_scope', $scope))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('updated_at');
    }

    public static function dashboardCounts(bool $isInitiator, bool $isApprover, ?string $approverScope = null): array
    {
        $counts = [
            'draft' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'queue_pending' => 0,
            'queue_approved' => 0,
        ];

        if ($isInitiator) {
            $email = self::currentUserEmail();
            $initiatorRows = self::query()
                ->when($email, fn ($q) => $q->whereRaw('LOWER(created_by_email) = ?', [strtolower((string) $email)]))
                ->select('status', DB::raw('COUNT(*) as aggregate'))
                ->groupBy('status')
                ->pluck('aggregate', 'status');

            foreach (['draft', 'pending', 'approved', 'rejected'] as $status) {
                $counts[$status] = (int) ($initiatorRows[$status] ?? 0);
            }
        }

        if ($isApprover) {
            $approverRows = self::query()
                ->when($approverScope, fn ($q) => $q->where('page_key', 'department')->where('page_scope', $approverScope))
                ->whereIn('status', ['pending', 'approved'])
                ->select('status', DB::raw('COUNT(*) as aggregate'))
                ->groupBy('status')
                ->pluck('aggregate', 'status');

            $counts['queue_pending'] = (int) ($approverRows['pending'] ?? 0);
            $counts['queue_approved'] = (int) ($approverRows['approved'] ?? 0);
        }

        return $counts;
    }

    // ---------------------------------------------------------------------
    // Workflow
    // ---------------------------------------------------------------------

    public static function createDraft(array $data): self
    {
        $item = new self();
        $item->page_key = $data['page_key'];
        $item->page_scope = $data['page_scope'] ?? null;
        $item->section_key = $data['section_key'];
        $item->title = $data['title'] ?? null;
        $item->content = $data['content'];
        $item->display_order = $data['display_order'] ?? 0;
        $item->status = 'draft';
        $item->statusflag = 'N';
        $item->created_by_userid = self::currentUserId();
        $item->created_by_name = self::currentUserName();
        $item->created_by_email = self::currentUserEmail();
        $item->save();

        self::logHistory($item->id, 'created', null, $item->content);

        return $item;
    }

    public static function updateOrCloneDraft(int $id, array $data): self
    {
        $existing = self::findOrFail($id);

        if (in_array($existing->status, ['draft', 'rejected'], true)) {
            $existing->title = $data['title'] ?? $existing->title;
            $existing->content = $data['content'];
            $existing->display_order = $data['display_order'] ?? $existing->display_order;
            $existing->status = 'draft';
            $existing->remarks = null;
            $existing->save();

            self::logHistory($existing->id, 'updated', null, $existing->content);

            return $existing;
        }

        // Approved (live) content — clone into a new draft rather than mutating
        // the live row in place, so the public site keeps showing the original
        // until the edit is reviewed and approved.
        $clone = new self();
        $clone->page_key = $existing->page_key;
        $clone->page_scope = $existing->page_scope;
        $clone->section_key = $existing->section_key;
        $clone->title = $data['title'] ?? $existing->title;
        $clone->content = $data['content'];
        $clone->display_order = $data['display_order'] ?? $existing->display_order;
        $clone->status = 'draft';
        $clone->statusflag = 'N';
        $clone->replaces_id = $existing->id;
        $clone->created_by_userid = self::currentUserId();
        $clone->created_by_name = self::currentUserName();
        $clone->created_by_email = self::currentUserEmail();
        $clone->save();

        self::logHistory($clone->id, 'created', 'Edit of #' . $existing->id, $clone->content);

        return $clone;
    }

    public static function submitForApproval(int $id): self
    {
        $item = self::findOrFail($id);
        abort_unless(in_array($item->status, ['draft', 'rejected'], true), 422, 'Only draft or rejected items can be submitted.');

        $item->status = 'pending';
        $item->submitted_at = now();
        $item->remarks = null;
        $item->save();

        self::logHistory($item->id, 'submitted');

        return $item;
    }

    public static function approve(int $id, ?string $remarks = null): self
    {
        $item = self::findOrFail($id);
        abort_unless($item->status === 'pending', 422, 'Only pending items can be approved.');

        $item->status = 'approved';
        $item->statusflag = 'Y';
        $item->approved_by_userid = self::currentUserId();
        $item->approved_by_name = self::currentUserName();
        $item->approved_by_email = self::currentUserEmail();
        $item->approved_at = now();
        $item->remarks = $remarks;
        $item->save();

        if ($item->replaces_id) {
            $old = self::find($item->replaces_id);
            if ($old) {
                $old->statusflag = 'N';
                $old->save();
                self::logHistory($old->id, 'deactivated', 'Superseded by #' . $item->id);
            }
        }

        self::logHistory($item->id, 'approved', $remarks);

        return $item;
    }

    public static function reject(int $id, string $remarks): self
    {
        $item = self::findOrFail($id);
        abort_unless($item->status === 'pending', 422, 'Only pending items can be rejected.');

        $item->status = 'rejected';
        $item->remarks = $remarks;
        $item->save();

        self::logHistory($item->id, 'rejected', $remarks);

        return $item;
    }

    public static function toggleActive(int $id): self
    {
        $item = self::findOrFail($id);
        abort_unless($item->status === 'approved', 422, 'Only approved items can be activated or deactivated.');

        $item->statusflag = $item->statusflag === 'Y' ? 'N' : 'Y';
        $item->save();

        self::logHistory($item->id, $item->statusflag === 'Y' ? 'activated' : 'deactivated');

        return $item;
    }

    // ---------------------------------------------------------------------
    // History (auxiliary table, plain query builder)
    // ---------------------------------------------------------------------

    public static function logHistory(int $contentId, string $actionKey, ?string $remarks = null, $snapshot = null): void
    {
        DB::table('portal.dga_content_histories')->insert([
            'content_id' => $contentId,
            'action_key' => $actionKey,
            'performed_by_userid' => self::currentUserId(),
            'performed_by_name' => self::currentUserName(),
            'performed_by_email' => self::currentUserEmail(),
            'performed_by_role' => self::isApprover() ? 'approver' : (self::isInitiator() ? 'initiator' : null),
            'remarks' => $remarks,
            'snapshot' => $snapshot !== null ? json_encode($snapshot) : null,
            'performed_at' => now(),
            'created_at' => now(),
        ]);
    }

    public static function historyFor(int $contentId)
    {
        return DB::table('portal.dga_content_histories')
            ->where('content_id', $contentId)
            ->orderByDesc('performed_at')
            ->get();
    }

    // ---------------------------------------------------------------------
    // Published content — consumed by DgaModel for the public site
    // ---------------------------------------------------------------------

    public static function publishedSection(string $pageKey, ?string $pageScope, string $sectionKey): array
    {
        $rows = self::query()
            ->where('page_key', $pageKey)
            ->where('section_key', $sectionKey)
            ->where('status', 'approved')
            ->where('statusflag', 'Y')
            ->when($pageScope === null, fn ($q) => $q->whereNull('page_scope'), fn ($q) => $q->where('page_scope', $pageScope))
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        return $rows->map(function (self $row) use ($sectionKey) {
            $content = $row->content ?? [];
            $content['id'] = $row->id;

            if ($sectionKey === 'gallery' && !empty($content['src'])) {
                $content['src'] = self::resolveAssetUrl($content['src']);
            }

            if ($sectionKey === 'downloads' && ($content['source'] ?? null) === 'upload' && !empty($content['href'])) {
                $content['href'] = self::resolveAssetUrl($content['href']);
            }

            return $content;
        })->all();
    }

    public static function publishedSingleton(string $pageKey, ?string $pageScope, string $sectionKey): ?array
    {
        $row = self::query()
            ->where('page_key', $pageKey)
            ->where('section_key', $sectionKey)
            ->where('status', 'approved')
            ->where('statusflag', 'Y')
            ->when($pageScope === null, fn ($q) => $q->whereNull('page_scope'), fn ($q) => $q->where('page_scope', $pageScope))
            ->orderByDesc('approved_at')
            ->first();

        if (!$row) {
            return null;
        }

        $content = $row->content ?? [];

        if ($pageKey === 'department' && $sectionKey === 'contact') {
            $lines = [];
            if (!empty($content['office'])) {
                $lines[] = $content['office'];
            }
            if (!empty($content['address'])) {
                $lines[] = 'Address: ' . $content['address'];
            }
            if (!empty($content['phone'])) {
                $lines[] = 'Phone: ' . $content['phone'];
            }
            if (!empty($content['email'])) {
                $lines[] = 'Email: ' . $content['email'];
            }

            return $lines;
        }

        return $content;
    }

    private static function resolveAssetUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    // ---------------------------------------------------------------------
    // Portal user (role) management
    // ---------------------------------------------------------------------

    public static function listPortalUsers()
    {
        return DB::table('portal.portal_users')->orderByDesc('created_at');
    }

    public static function findLoginIdentity(string $email): ?object
    {
        $email = trim($email);
        $tables = [
            'audit.deptuserdetails' => 'deptuserid',
            'audit.dev_userdetails' => 'devuserid',
            'audit.audtieeuserdetails' => 'auditeeuserid',
            'audit.auditee_dept' => 'auditeedeptid',
        ];

        foreach ($tables as $table => $primaryKey) {
            $row = DB::table($table)->where('email', $email)->where('statusflag', 'Y')->first();
            if ($row) {
                return (object) [
                    'source_table' => $table,
                    'login_userid' => (string) ($row->{$primaryKey} ?? ''),
                    'login_email' => $row->email,
                    'display_name' => $row->username ?? $row->email,
                ];
            }
        }

        return null;
    }

    public static function assignRole(array $data): void
    {
        $email = strtolower(trim($data['login_email']));
        $existing = DB::table('portal.portal_users')
            ->whereRaw('LOWER(login_email) = ?', [$email])
            ->where('role', $data['role'])
            ->first();

        $payload = [
            'source_table' => $data['source_table'] ?? null,
            'login_userid' => $data['login_userid'] ?? null,
            'login_email' => $data['login_email'],
            'display_name' => $data['display_name'] ?? null,
            'department_scope' => $data['department_scope'] ?? null,
            'statusflag' => 'Y',
            'assigned_by_userid' => self::currentUserId(),
            'assigned_by_name' => self::currentUserName(),
            'assigned_at' => now(),
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('portal.portal_users')->where('id', $existing->id)->update($payload);

            return;
        }

        $payload['role'] = $data['role'];
        $payload['created_at'] = now();
        DB::table('portal.portal_users')->insert($payload);
    }

    public static function toggleUserActive(int $portalUserId): void
    {
        $row = DB::table('portal.portal_users')->where('id', $portalUserId)->first();
        abort_if(!$row, 404);

        DB::table('portal.portal_users')->where('id', $portalUserId)->update([
            'statusflag' => $row->statusflag === 'Y' ? 'N' : 'Y',
            'updated_at' => now(),
        ]);
    }

    public static function changeUserRole(int $portalUserId, string $newRole): void
    {
        DB::table('portal.portal_users')->where('id', $portalUserId)->update([
            'role' => $newRole,
            'updated_at' => now(),
        ]);
    }
}
