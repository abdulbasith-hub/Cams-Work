@php
    $activeDepartmentSlug = $activeDepartmentSlug ?? null;
    $homeHref = $homeHref ?? route('dga.home');
    $navLabel = $navLabel ?? 'DGA page navigation';
    $homeLabel = $homeLabel ?? ($activeDepartmentSlug ? 'DGA' : 'CAMS');
@endphp

<div class="dga-topbar">
    <div class="container py-2 d-flex justify-content-between align-items-center flex-wrap">
        <div class="dga-brand-accessibility" aria-label="Accessibility controls">
            <a class="dga-screen-reader-link" href="{{ url('screenreader') }}">
                <i class="fas fa-volume-up" aria-hidden="true"></i>
                <span>Screen Reader</span>
            </a>
            <div class="dga-font-controls" aria-label="Font size controls">
                <button type="button" data-dga-font="decrease" aria-label="Decrease font size">A-</button>
                <button type="button" data-dga-font="reset" aria-label="Reset font size">A</button>
                <button type="button" data-dga-font="increase" aria-label="Increase font size">A+</button>
            </div>
        </div>
        <div class="dga-topbar-tools" aria-label="Display preferences">
            <div class="dga-language-switch" aria-label="Language selector">
                <button type="button" data-dga-language="en" aria-pressed="true">English</button>
                <button type="button" data-dga-language="ta" aria-pressed="false">தமிழ்</button>
            </div>
            <div class="dga-theme-switch" aria-label="Theme selector">
                <button type="button" data-dga-theme="blue" aria-label="Blue theme" title="Blue theme" aria-pressed="true"></button>
                <button type="button" data-dga-theme="green" aria-label="Green theme" title="Green theme" aria-pressed="false"></button>
                <button type="button" data-dga-theme="gold" aria-label="Gold theme" title="Gold theme" aria-pressed="false"></button>
            </div>
        </div>
    </div>
</div>

<header class="dga-header">
    <div class="container py-3">
        <div class="dga-header-bar">
            <div class="dga-brand">
                <img src="{{ asset('site/image/tn__logo.png') }}" alt="Tamil Nadu State Emblem">
                <div class="dga-brand-text">
                    <h1>Director General of Audit</h1>
                    <span>Government of Tamil Nadu</span>
                </div>
            </div>

            <div class="dga-header-actions">
                <nav class="dga-nav dga-desktop-nav" aria-label="{{ $navLabel }}">
                    <a class="dga-nav-link" href="{{ $homeHref }}">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        <span>{{ $homeLabel }}</span>
                    </a>
                    <a class="dga-nav-link primary" href="{{ url('/login') }}">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                        <span>Login</span>
                    </a>
                </nav>

                <button type="button"
                        class="dga-menu-toggle"
                        data-dga-menu-toggle
                        aria-controls="dgaMobileMenu"
                        aria-expanded="false"
                        aria-label="Open navigation menu">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Backdrop + drawer: static on desktop (strip unchanged); off-canvas below 992px --}}
    <div class="dga-mobile-backdrop" data-dga-menu-close aria-hidden="true"></div>
    <div class="dga-mobile-drawer" id="dgaMobileMenu">
        <div class="dga-mobile-drawer__head">
            <strong>Menu</strong>
            <button type="button"
                    class="dga-menu-close"
                    data-dga-menu-close
                    aria-label="Close navigation menu">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="dga-nav dga-mobile-nav" aria-label="{{ $navLabel }}">
            <a class="dga-nav-link" href="{{ $homeHref }}">
                <i class="fas fa-home" aria-hidden="true"></i>
                <span>{{ $homeLabel }}</span>
            </a>
            <a class="dga-nav-link primary" href="{{ url('/login') }}">
                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                <span>Login</span>
            </a>
        </nav>

        <section class="dga-department-strip" aria-label="DGA department sections">
            <div class="container">
                <div class="dga-department-grid">
                    @foreach ($departments as $departmentItem)
                        @php($isActiveDepartment = $departmentItem['slug'] === $activeDepartmentSlug)
                        <div class="dga-department-item is-dept-{{ $departmentItem['slug'] }}">
                            <a class="dga-department-title {{ $isActiveDepartment ? 'is-active' : '' }}"
                               {{-- href="{{ route('dga.department', $departmentItem['slug']) }}" --}}
                               aria-haspopup="true"
                               aria-expanded="false">
                                <strong>{{ $departmentItem['short_name'] }}</strong>
                                <i class="fas fa-chevron-down dga-department-arrow" aria-hidden="true"></i>
                            </a>
                            <div class="dga-department-menu">
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}">Home</a>
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#structure">Structure Map</a>
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#establishment">Establishment</a>
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#acts">Acts and Rules</a>
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#functions">Functions</a>
                                @if (!empty($departmentItem['regions']))
                                    <a href="{{ route('dga.department', $departmentItem['slug']) }}#regions">Regional Map</a>
                                @endif
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#institutions">Auditable Institutions</a>
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#highlights">Highlights</a>
                                <a href="{{ route('dga.department', $departmentItem['slug']) }}#contact">Contact</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</header>
