@php
    $departmentLinks = [
        ['label' => 'LFAD', 'name' => 'Local Fund Audit Department', 'slug' => 'lfa'],
        ['label' => 'SGAD', 'name' => 'State Government Audit Department', 'slug' => 'sgad'],
        ['label' => 'HRIA', 'name' => 'Hindu Religious Institutions Audit', 'slug' => 'hria'],
        ['label' => 'DCA', 'name' => 'Department of Co-operative Audit', 'slug' => 'dca'],
        ['label' => 'DAMC', 'name' => 'Dairy Audit and Milk Co-operatives', 'slug' => 'milk'],
    ];
@endphp

<header class="cams-site-header">
    <div class="cams-topbar">
        <div class="container-fluid cams-container">
            <div class="cams-topbar-left">
                <a href="{{ url('screenreader') }}" class="cams-topbar-link">
                    <i class="fas fa-universal-access" aria-hidden="true"></i>
                    <span class="lang" key="screenreaderheading">Screen Reader</span>
                </a>

                <div class="cams-font-tools" aria-label="Font size controls">
                    <button type="button" class="decrease" aria-label="Decrease font size">A-</button>
                    <button type="button" class="resetMe" aria-label="Reset font size">A</button>
                    <button type="button" class="increase" aria-label="Increase font size">A+</button>
                </div>
            </div>

            <div class="cams-topbar-actions">
                <div class="cams-theme-picker" aria-label="Page theme selection">
                    <span class="cams-theme-label" aria-hidden="true">
                        <i class="fas fa-palette"></i>
                        <span>Theme</span>
                    </span>
                    <button type="button" class="cams-theme-swatch is-active" data-cams-theme="ocean"
                        aria-label="Ocean theme" aria-pressed="true"></button>
                    <button type="button" class="cams-theme-swatch" data-cams-theme="emerald"
                        aria-label="Emerald theme" aria-pressed="false"></button>
                    <button type="button" class="cams-theme-swatch" data-cams-theme="royal"
                        aria-label="Royal theme" aria-pressed="false"></button>
                </div>

                <label for="translate" class="visually-hidden">Language</label>
                <div class="dropdown cams-language-wrap">
                    <select id="translate" class="cams-language-select" aria-label="Choose language" tabindex="-1">
                        <option value="en">English</option>
                        <option value="ta">Tamil</option>
                    </select>
                    <button class="cams-language-toggle" type="button" id="languageDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-language" aria-hidden="true"></i>
                        <span id="camsLanguageLabel">English</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end cams-language-menu" aria-labelledby="languageDropdown">
                        <li>
                            <button type="button" class="dropdown-item is-active" data-lang-value="en"
                                aria-pressed="true">English</button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item" data-lang-value="ta"
                                aria-pressed="false">Tamil</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-xl cams-main-nav" aria-label="CAMS public navigation">
        <div class="container-fluid cams-container">
            <a class="cams-brand" href="{{ url('/') }}">
                <span class="cams-brand-mark">
                    <img src="{{ asset('site/image/tn__logo.png') }}" alt="Government of Tamil Nadu emblem">
                </span>
                <span class="cams-brand-copy">
                    <strong>Comprehensive Audit Management System</strong>
                    <span>Director General of Audit</span>
                    <span>Government of Tamil Nadu</span>
                </span>
            </a>

            <button class="navbar-toggler cams-menu-toggle" type="button" data-bs-toggle="collapse"
                data-bs-target="#camsPublicNav" aria-controls="camsPublicNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="camsPublicNav">
                <ul class="navbar-nav cams-nav-links ms-auto">
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#about') }}">Services</a>
                    </li>
                    <li class="nav-item dropdown cams-department-menu">
                        <a class="nav-link dropdown-toggle" href="{{ route('dga.home') }}" id="departmentDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Departments
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="departmentDropdown">
                            @foreach ($departmentLinks as $department)
                                <li>
                                    <a class="dropdown-item" href="{{ route('dga.department', $department['slug']) }}">
                                        <span>{{ $department['label'] }}</span>
                                        <small>{{ $department['name'] }}</small>
                                    </a>
                                </li>
                            @endforeach
                        </ul> --}}
                    {{-- </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#workflow') }}">Workflow</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#contact') }}">Contact</a>
                    </li> --}}
                </ul>

                <div class="cams-header-actions">
                    <a href="{{ route('dga.home') }}" class="cams-dga-chip">
                        <i class="fas fa-university" aria-hidden="true"></i>
                        <span>DGA</span>
                    </a>

                    @if(request()->is('login'))
                        <a href="{{ url('/') }}" class="cams-login-btn">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Home</span>
                        </a>
                    @else
                        <a href="{{ url('/login') }}" class="cams-login-btn">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                            <span class="lang" key="login">Login</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</header>
