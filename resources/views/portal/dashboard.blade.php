@extends('index2')

@section('title', 'DGA Portal Dashboard')

@section('content')
    @php
        $effectiveScope = $departmentScope ?? $approverDepartmentScope ?? null;
        $portalTitle = $effectiveScope ? strtoupper($effectiveScope) . ' Department Portal' : 'DGA Content Portal';
        $identityName = $identity['name'] ?? 'Portal user';
        $initials = collect(explode(' ', trim($identityName)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => substr($part, 0, 1))
            ->implode('');
        $initials = $initials ? strtoupper($initials) : 'U';
        $myTotal = max((int) ($dashboardMeta['my_total'] ?? 0), 1);
        $approvedPercent = min(100, round(((int) ($counts['approved'] ?? 0) / $myTotal) * 100));
        $sectionIcons = [
            'updates' => 'bi-megaphone',
            'gallery' => 'bi-images',
            'downloads' => 'bi-download',
            'services' => 'bi-grid-3x3-gap',
            'contact' => 'bi-person-lines-fill',
            'establishment' => 'bi-building',
            'acts' => 'bi-journal-bookmark',
            'functions' => 'bi-list-check',
            'achievements' => 'bi-star',
            'institutions' => 'bi-bank',
            'regions' => 'bi-geo-alt',
            'structure_map' => 'bi-diagram-3',
            'nav_menu' => 'bi-menu-button-wide',
            'custom_block' => 'bi-file-earmark-plus',
        ];
        $sectionTones = ['blue', 'teal', 'amber', 'green', 'rose', 'indigo', 'cyan'];
    @endphp

    <style>
        .dga-portal {
            --portal-ink: #172334;
            --portal-muted: #64748b;
            --portal-line: #d8e5ef;
            --portal-soft: #f5f9fc;
            --portal-blue: #2563eb;
            --portal-teal: #0f9f8f;
            --portal-amber: #d99016;
            --portal-green: #22965a;
            --portal-rose: #c33f63;
            --portal-indigo: #5755c9;
            --portal-cyan: #1685a7;
            color: var(--portal-ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        .portal-top-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(300px, .95fr);
            gap: 18px;
            margin-bottom: 18px;
        }

        .portal-hero,
        .portal-identity,
        .portal-panel {
            border: 1px solid var(--portal-line);
            border-radius: 8px;
            box-shadow: 0 14px 32px rgba(17, 35, 52, .08);
        }

        .portal-hero {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 18px;
            min-height: 205px;
            padding: 28px;
            color: #fff;
            background:
                linear-gradient(115deg, rgba(12, 43, 74, .96), rgba(23, 104, 133, .93)),
                repeating-linear-gradient(135deg, rgba(255,255,255,.09) 0 1px, transparent 1px 18px);
            overflow: hidden;
        }

        .portal-hero-kicker,
        .portal-section-kicker {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: #24a78f;
            letter-spacing: 0;
        }

        .portal-hero-kicker {
            color: #9ed7e9;
        }

        .portal-hero h1 {
            margin: 8px 0 10px;
            color: #fff;
            font-size: 31px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: 0;
        }

        .portal-hero p {
            margin: 0;
            max-width: 680px;
            color: rgba(255,255,255,.86);
            font-size: 15px;
        }

        .portal-hero-actions {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            min-width: 205px;
        }

        .portal-updated {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            color: rgba(255,255,255,.9);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .portal-hero-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .portal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 38px;
            padding: 8px 15px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 13px;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .portal-btn-light {
            background: #fff;
            color: #14324a;
        }

        .portal-btn-outline {
            background: rgba(255,255,255,.1);
            color: #fff;
            border-color: rgba(255,255,255,.45);
        }

        .portal-btn-primary {
            background: var(--portal-blue);
            color: #fff;
        }

        .portal-btn:disabled {
            opacity: .75;
            cursor: wait;
        }

        .portal-identity {
            padding: 22px;
            background: #fff;
        }

        .portal-user-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .portal-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            color: #fff;
            background: linear-gradient(145deg, #2563eb, #0f9f8f);
            font-weight: 800;
            font-size: 20px;
            flex: 0 0 auto;
        }

        .portal-user-head h2 {
            margin: 0;
            font-size: 20px;
            line-height: 1.15;
            font-weight: 800;
            color: var(--portal-ink);
        }

        .portal-user-head p {
            margin: 4px 0 0;
            color: var(--portal-muted);
            font-size: 13px;
            word-break: break-word;
        }

        .portal-identity-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .portal-identity-item {
            min-height: 64px;
            border-radius: 8px;
            background: var(--portal-soft);
            border: 1px solid #e3edf4;
            padding: 10px 12px;
        }

        .portal-identity-item span {
            display: block;
            color: var(--portal-muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .portal-identity-item strong {
            display: block;
            margin-top: 5px;
            color: var(--portal-ink);
            font-size: 13px;
            line-height: 1.25;
            word-break: break-word;
        }

        .portal-panel {
            background: #fff;
            padding: 22px;
            margin-bottom: 18px;
        }

        .portal-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .portal-panel h3 {
            margin: 5px 0 0;
            font-size: 23px;
            line-height: 1.2;
            color: var(--portal-ink);
            font-weight: 800;
            letter-spacing: 0;
        }

        .portal-panel-sub {
            color: var(--portal-muted);
            font-size: 13px;
            margin: 7px 0 0;
        }

        .portal-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .portal-stat-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 102px;
            padding: 17px;
            border-radius: 8px;
            border: 1px solid var(--portal-line);
            background: #fff;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .portal-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(17, 35, 52, .1);
            color: inherit;
        }

        .portal-stat-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: var(--accent);
        }

        .portal-stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            color: var(--accent);
            background: var(--accent-soft);
            font-size: 22px;
            flex: 0 0 auto;
        }

        .portal-stat-card h4 {
            margin: 0;
            color: var(--portal-ink);
            font-size: 29px;
            line-height: 1;
            font-weight: 800;
        }

        .portal-stat-card p {
            margin: 6px 0 0;
            color: var(--portal-muted);
            font-size: 13px;
            line-height: 1.25;
        }

        .tone-slate { --accent: #52677a; --accent-soft: #edf3f7; }
        .tone-amber { --accent: var(--portal-amber); --accent-soft: #fff6df; }
        .tone-green { --accent: var(--portal-green); --accent-soft: #e9f8ef; }
        .tone-rose { --accent: var(--portal-rose); --accent-soft: #ffedf2; }
        .tone-blue { --accent: var(--portal-blue); --accent-soft: #edf4ff; }
        .tone-teal { --accent: var(--portal-teal); --accent-soft: #e9fbf7; }
        .tone-indigo { --accent: var(--portal-indigo); --accent-soft: #f0f0ff; }
        .tone-cyan { --accent: var(--portal-cyan); --accent-soft: #e9f8fc; }

        .portal-progress {
            display: grid;
            grid-template-columns: minmax(130px, 160px) minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            padding: 12px 14px;
            border-radius: 8px;
            background: var(--portal-soft);
            border: 1px solid #e3edf4;
            min-width: 330px;
        }

        .portal-progress strong {
            font-size: 13px;
            color: var(--portal-ink);
        }

        .portal-progress-track {
            height: 10px;
            border-radius: 999px;
            background: #dce8f0;
            overflow: hidden;
        }

        .portal-progress-fill {
            height: 100%;
            width: var(--progress-width);
            border-radius: inherit;
            background: linear-gradient(90deg, var(--portal-green), var(--portal-teal));
            transition: width .25s ease;
        }

        .portal-scope-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 34px;
            padding: 7px 13px;
            border-radius: 999px;
            color: #145b7b;
            background: #eef9ff;
            border: 1px solid #cae9f6;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .portal-dept-block {
            margin-top: 18px;
        }

        .portal-dept-block:first-of-type {
            margin-top: 0;
        }

        .portal-dept-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            color: #245f95;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 800;
        }

        .portal-action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(198px, 1fr));
            gap: 13px;
        }

        .portal-action-card {
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            min-height: 94px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--portal-line);
            background: linear-gradient(180deg, #fff, #fbfdff);
            color: inherit;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .portal-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(17, 35, 52, .1);
            border-color: var(--accent);
            color: inherit;
        }

        .portal-action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 8px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 21px;
        }

        .portal-action-card strong {
            display: block;
            font-size: 14px;
            line-height: 1.25;
            color: var(--portal-ink);
        }

        .portal-action-card small {
            display: block;
            margin-top: 5px;
            color: var(--portal-muted);
            font-size: 12px;
        }

        @media (max-width: 1199.98px) {
            .portal-top-grid,
            .portal-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .portal-top-grid,
            .portal-stat-grid,
            .portal-identity-grid {
                grid-template-columns: 1fr;
            }

            .portal-hero,
            .portal-panel-head {
                flex-direction: column;
            }

            .portal-hero-actions {
                align-items: flex-start;
                min-width: 0;
            }

            .portal-hero-buttons {
                justify-content: flex-start;
            }

            .portal-progress {
                grid-template-columns: 1fr;
                min-width: 0;
            }
        }
    </style>

    <div class="dga-portal">
        <div class="portal-top-grid">
            <section class="portal-hero">
                <div>
                    <div class="portal-hero-kicker">Office of the Director General of Audit</div>
                    <h1>{{ $portalTitle }}</h1>
                    <p>Content changes move through draft, review, approval, and publish status for the public DGA website.</p>
                </div>

                <div class="portal-hero-actions">
                    <div class="portal-updated">
                        <i class="bi bi-clock-history"></i>
                        <span>Updated <span data-meta-key="updated_at">{{ $dashboardMeta['updated_at'] ?? now()->format('d-m-Y h:i A') }}</span></span>
                    </div>
                    <div class="portal-hero-buttons">
                        <button type="button" class="portal-btn portal-btn-outline" id="portalDashboardRefresh">
                            <i class="bi bi-arrow-clockwise"></i>
                            Refresh
                        </button>
                        <a href="{{ route('portal.content.index') }}" class="portal-btn portal-btn-light">
                            <i class="bi bi-list-ul"></i>
                            Content list
                        </a>
                        @if ($isApprover)
                            <a href="{{ route('portal.users.index') }}" class="portal-btn portal-btn-outline">
                                <i class="bi bi-people"></i>
                                Portal users
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <aside class="portal-identity">
                <div class="portal-user-head">
                    <div class="portal-avatar">{{ $initials }}</div>
                    <div>
                        <h2 data-identity-key="name">{{ $identity['name'] ?? 'Portal user' }}</h2>
                        <p data-identity-key="email">{{ $identity['email'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="portal-identity-grid">
                    <div class="portal-identity-item">
                        <span>User ID</span>
                        <strong data-identity-key="user_id">{{ $identity['user_id'] ?? 'N/A' }}</strong>
                    </div>
                    <div class="portal-identity-item">
                        <span>Role</span>
                        <strong data-identity-key="roles">{{ $identity['roles'] ?? 'N/A' }}</strong>
                    </div>
                    <div class="portal-identity-item">
                        <span>Department</span>
                        <strong data-identity-key="department">{{ $identity['department'] ?? 'N/A' }}</strong>
                    </div>
                    <div class="portal-identity-item">
                        <span>Designation</span>
                        <strong data-identity-key="designation">{{ $identity['designation'] ?? 'N/A' }}</strong>
                    </div>
                    <div class="portal-identity-item">
                        <span>Initiator Scope</span>
                        <strong data-identity-key="initiator_scope">{{ $identity['initiator_scope'] ?? 'N/A' }}</strong>
                    </div>
                    <div class="portal-identity-item">
                        <span>Last Login</span>
                        <strong data-identity-key="last_login">{{ $identity['last_login'] ?? 'N/A' }}</strong>
                    </div>
                </div>
            </aside>
        </div>

        @if ($isInitiator)
            <section class="portal-panel">
                <div class="portal-panel-head">
                    <div>
                        <div class="portal-section-kicker">Overview</div>
                        <h3>My content</h3>
                        <p class="portal-panel-sub"><span data-meta-key="my_total">{{ $dashboardMeta['my_total'] ?? 0 }}</span> total items in your workspace</p>
                    </div>
                    <div class="portal-progress">
                        <strong>Approved share</strong>
                        <div class="portal-progress-track">
                            <div class="portal-progress-fill" data-progress-key="approved" style="--progress-width: {{ $approvedPercent }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="portal-stat-grid">
                    <a href="{{ route('portal.content.index') }}?status=draft" class="portal-stat-card tone-slate">
                        <span class="portal-stat-icon"><i class="bi bi-pencil-square"></i></span>
                        <div><h4 data-count-key="draft">{{ $counts['draft'] ?? 0 }}</h4><p>Draft</p></div>
                    </a>
                    <a href="{{ route('portal.content.index') }}?status=pending" class="portal-stat-card tone-amber">
                        <span class="portal-stat-icon"><i class="bi bi-hourglass-split"></i></span>
                        <div><h4 data-count-key="pending">{{ $counts['pending'] ?? 0 }}</h4><p>Pending approval</p></div>
                    </a>
                    <a href="{{ route('portal.content.index') }}?status=approved" class="portal-stat-card tone-green">
                        <span class="portal-stat-icon"><i class="bi bi-check2-circle"></i></span>
                        <div><h4 data-count-key="approved">{{ $counts['approved'] ?? 0 }}</h4><p>Approved</p></div>
                    </a>
                    <a href="{{ route('portal.content.index') }}?status=rejected" class="portal-stat-card tone-rose">
                        <span class="portal-stat-icon"><i class="bi bi-x-circle"></i></span>
                        <div><h4 data-count-key="rejected">{{ $counts['rejected'] ?? 0 }}</h4><p>Rejected</p></div>
                    </a>
                </div>
            </section>

            <section class="portal-panel">
                <div class="portal-panel-head">
                    <div>
                        <div class="portal-section-kicker">Publish</div>
                        <h3>Add new content</h3>
                    </div>
                </div>

                @if ($departmentScope)
                    <div class="portal-scope-pill"><i class="bi bi-shield-lock"></i> Scoped to {{ strtoupper($departmentScope) }} only</div>
                @endif

                @if (!$departmentScope)
                    <div class="portal-dept-block">
                        <div class="portal-dept-title"><i class="bi bi-house-door"></i> Home page</div>
                        <div class="portal-action-grid">
                            @foreach ($sectionCatalog['home'] ?? [] as $sectionKey => $section)
                                @php($tone = $sectionTones[$loop->index % count($sectionTones)])
                                <a class="portal-action-card tone-{{ $tone }}" href="{{ route(!empty($section['singleton']) ? 'portal.content.create' : 'portal.content.manage', ['page_key' => 'home', 'section_key' => $sectionKey]) }}">
                                    <span class="portal-action-icon"><i class="bi {{ $sectionIcons[$sectionKey] ?? 'bi-file-earmark' }}"></i></span>
                                    <span>
                                        <strong>{{ $section['label'] }}</strong>
                                        <small>{{ !empty($section['singleton']) ? 'Single record' : 'List' }}</small>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @foreach ($departmentSlugs as $slug)
                    @continue($departmentScope && $slug !== $departmentScope)
                    <div class="portal-dept-block">
                        <div class="portal-dept-title"><i class="bi bi-diagram-3"></i> {{ strtoupper($slug) }} department</div>
                        <div class="portal-action-grid">
                            @foreach ($sectionCatalog['department'] ?? [] as $sectionKey => $section)
                                @php($tone = $sectionTones[$loop->index % count($sectionTones)])
                                <a class="portal-action-card tone-{{ $tone }}" href="{{ route(!empty($section['singleton']) ? 'portal.content.create' : 'portal.content.manage', ['page_key' => 'department', 'page_scope' => $slug, 'section_key' => $sectionKey]) }}">
                                    <span class="portal-action-icon"><i class="bi {{ $sectionIcons[$sectionKey] ?? 'bi-file-earmark' }}"></i></span>
                                    <span>
                                        <strong>{{ $section['label'] }}</strong>
                                        <small>{{ !empty($section['singleton']) ? 'Single record' : 'List' }}</small>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        @if ($isApprover)
            <section class="portal-panel">
                <div class="portal-panel-head">
                    <div>
                        <div class="portal-section-kicker">Review</div>
                        <h3>Approval queue</h3>
                        <p class="portal-panel-sub"><span data-meta-key="review_total">{{ $dashboardMeta['review_total'] ?? 0 }}</span> items visible to your reviewer scope</p>
                    </div>
                    <a href="{{ route('portal.content.index') }}?status=pending" class="portal-btn portal-btn-primary">
                        <i class="bi bi-check2-square"></i>
                        Review pending
                    </a>
                </div>

                @if ($approverDepartmentScope)
                    <div class="portal-scope-pill"><i class="bi bi-shield-lock"></i> You review {{ strtoupper($approverDepartmentScope) }} submissions</div>
                @endif

                <div class="portal-stat-grid">
                    <a href="{{ route('portal.content.index') }}?status=pending" class="portal-stat-card tone-amber">
                        <span class="portal-stat-icon"><i class="bi bi-hourglass-split"></i></span>
                        <div><h4 data-count-key="queue_pending">{{ $counts['queue_pending'] ?? 0 }}</h4><p>Awaiting review</p></div>
                    </a>
                    <a href="{{ route('portal.content.index') }}?status=approved" class="portal-stat-card tone-green">
                        <span class="portal-stat-icon"><i class="bi bi-globe2"></i></span>
                        <div><h4 data-count-key="queue_approved">{{ $counts['queue_approved'] ?? 0 }}</h4><p>Live on site</p></div>
                    </a>
                </div>
            </section>
        @endif
    </div>

    <script>
        (function ($) {
            if (!$) {
                return;
            }

            var inFlight = false;
            var lastFetchAt = 0;
            var minimumGap = 60000;
            var endpoint = "{{ route('portal.dashboard.data') }}";

            function setMappedText(selector, data) {
                $(selector).each(function () {
                    var key = $(this).data(selector.indexOf('identity') > -1 ? 'identity-key' : 'meta-key');
                    if (data && Object.prototype.hasOwnProperty.call(data, key)) {
                        $(this).text(data[key] === null || data[key] === '' ? 'N/A' : data[key]);
                    }
                });
            }

            function updateProgress(counts, meta) {
                var total = Math.max(parseInt(meta.my_total || 0, 10), 1);
                var approved = parseInt(counts.approved || 0, 10);
                var percent = Math.min(100, Math.round((approved / total) * 100));
                $('[data-progress-key="approved"]').css('--progress-width', percent + '%');
            }

            function applyDashboardData(payload) {
                var counts = payload.counts || {};
                var meta = payload.meta || {};

                $('[data-count-key]').each(function () {
                    var key = $(this).data('count-key');
                    $(this).text(counts[key] || 0);
                });

                setMappedText('[data-identity-key]', payload.identity || {});
                setMappedText('[data-meta-key]', meta);
                updateProgress(counts, meta);
            }

            function refreshDashboard(force) {
                var now = Date.now();
                if (inFlight || (!force && now - lastFetchAt < minimumGap) || (!force && document.hidden)) {
                    return;
                }

                inFlight = true;
                $('#portalDashboardRefresh').prop('disabled', true);

                $.ajax({
                    url: endpoint,
                    method: 'GET',
                    dataType: 'json',
                    cache: false
                }).done(function (response) {
                    if (response && response.success) {
                        applyDashboardData(response);
                        lastFetchAt = Date.now();
                    }
                }).always(function () {
                    inFlight = false;
                    $('#portalDashboardRefresh').prop('disabled', false);
                });
            }

            $(function () {
                $('#portalDashboardRefresh').on('click', function () {
                    refreshDashboard(true);
                });

                window.setInterval(function () {
                    refreshDashboard(false);
                }, minimumGap);

                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden) {
                        refreshDashboard(false);
                    }
                });
            });
        })(window.jQuery);
    </script>
@endsection
