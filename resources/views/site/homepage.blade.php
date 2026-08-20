<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Home - CAMS</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('site/image/tn__logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/cams-public.css') }}?v=20260727a">
</head>

<body class="cams-public-page">
    @include('layouts.site_header')

    <main class="cams-page-main">
        <section class="cams-hero" id="home">
            <div class="container-fluid cams-container">
                <div class="cams-hero-grid">
                    <div class="cams-hero-copy">
                        <span class="cams-eyebrow">
                            <i class="fas fa-university" aria-hidden="true"></i>
                            Official Audit Portal
                        </span>
                        <h1>Facilitating Digital Audit in Government Departments</h1>
                        <p>
                            <b>CAMS</b> is a configurable platform enabling government departments like Hindu Religious Institutions Audit,
                             state Government Audit, Local Fund Audit, Cooperative, and Milk Audit to facilitate their audits and to
                             comply with the Director General of Audit (DGA).

                        </p>
                        <div class="cams-hero-actions">
                            <a href="{{ url('/login') }}" class="cams-btn-primary">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                Login to CAMS
                            </a>
                            <a href="{{ route('grievance.register') }}" class="cams-btn-secondary">
                                <i class="fas fa-comment-dots" aria-hidden="true"></i>
                                Public Grievance Portal
                            </a>
                        </div>
                        <div class="cams-hero-stats" aria-label="CAMS portal highlights">
                            <span><strong>6</strong><small>Audit process stages</small></span>
                            <span><strong>100%</strong><small>Traceable workflow</small></span>
                            <span><strong>24x7</strong><small>Digital access</small></span>
                        </div>
                    </div>

                    <aside class="cams-hero-services" id="about" aria-label="CAMS services">
                        <span class="cams-hero-services-label">Services</span>
                        <div class="cams-hero-service-list">
                            <span><i class="fas fa-calendar-check" aria-hidden="true"></i> Audit Planning</span>
                            <span><i class="fas fa-users" aria-hidden="true"></i> Team Allocation</span>
                            <span><i class="fas fa-clipboard-list" aria-hidden="true"></i> Audit Slips</span>
                            <span><i class="fas fa-folder-open" aria-hidden="true"></i> Evidence Records</span>
                            <span><i class="fas fa-file-alt" aria-hidden="true"></i> Reports</span>
                            <span><i class="fas fa-lock" aria-hidden="true"></i> Secure Login</span>
                        </div>
                    </aside>

                    <div class="cams-hero-slider" aria-label="CAMS audit portal highlights">
                        <div id="camsHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3600">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#camsHeroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Audit planning slide"></button>
                                <button type="button" data-bs-target="#camsHeroCarousel" data-bs-slide-to="1" aria-label="Digital audit workspace slide"></button>
                                <button type="button" data-bs-target="#camsHeroCarousel" data-bs-slide-to="2" aria-label="Audit reporting slide"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{ asset('site/image/carsoul1-min.jpg') }}" class="d-block w-100" alt="Digital audit planning workspace">
                                    <div class="cams-slide-caption">
                                        <span>Audit Planning</span>
                                        <strong>Plan, assign and monitor audit work with clarity.</strong>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('site/image/carsoul2-min.jpg') }}" class="d-block w-100" alt="Government audit execution dashboard">
                                    <div class="cams-slide-caption">
                                        <span>Audit Execution</span>
                                        <strong>Track observations, replies and evidence in one workspace.</strong>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('site/image/carsoul4-min.jpg') }}" class="d-block w-100" alt="Audit report review and approval">
                                    <div class="cams-slide-caption">
                                        <span>Report Review</span>
                                        <strong>Move inspection reports through official approval stages.</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cams-section cams-workflow" id="workflow">
            <div class="container-fluid cams-container">
                <div class="cams-workflow-showcase">
                    <div class="cams-workflow-intro">
                        <span class="cams-eyebrow">Audit Workflow</span>
                        <h2>Process Flow of Audit</h2>
                        <p>CAMS turns every audit movement into a visible, traceable journey from allocation to final report.</p>
                    </div>
                    <div class="cams-workflow-track" aria-label="CAMS audit workflow process">
                        <article class="cams-workflow-step step-green">
                            <div class="cams-workflow-node">
                                <b>01</b>
                                <i class="fas fa-random" aria-hidden="true"></i>
                            </div>
                            <div class="cams-workflow-copy">
                                <strong>Random Allocation of Audit Plan</strong>
                                <p>Audit plans are generated and allotted through a transparent allocation process.</p>
                            </div>
                        </article>
                        <article class="cams-workflow-step step-gold">
                            <div class="cams-workflow-node">
                                <b>02</b>
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            </div>
                            <div class="cams-workflow-copy">
                                <strong>Audit Scheduling and Intimation</strong>
                                <p>Audit dates are scheduled and official intimation is shared with the institution.</p>
                            </div>
                        </article>
                        <article class="cams-workflow-step step-rose">
                            <div class="cams-workflow-node">
                                <b>03</b>
                                <i class="fas fa-users" aria-hidden="true"></i>
                            </div>
                            <div class="cams-workflow-copy">
                                <strong>Random - Work Allocation</strong>
                                <p>Audit work is distributed to the assigned team with clear responsibility.</p>
                            </div>
                        </article>
                        <article class="cams-workflow-step step-teal">
                            <div class="cams-workflow-node">
                                <b>04</b>
                                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                            </div>
                            <div class="cams-workflow-copy">
                                <strong>Audit Slip Issue and Auditee Reply</strong>
                                <p>Audit slips are issued for observations and replies are captured from the auditee.</p>
                            </div>
                        </article>
                        <article class="cams-workflow-step step-yellow">
                            <div class="cams-workflow-node">
                                <b>05</b>
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </div>
                            <div class="cams-workflow-copy">
                                <strong>Audit Inspection</strong>
                                <p>Inspection findings, evidence and responses are reviewed by the audit team.</p>
                            </div>
                        </article>
                        <article class="cams-workflow-step step-forest">
                            <div class="cams-workflow-node">
                                <b>06</b>
                                <i class="fas fa-file-alt" aria-hidden="true"></i>
                            </div>
                            <div class="cams-workflow-copy">
                                <strong>Audit Report</strong>
                                <p>The inspection report is prepared for official review, follow-up and record.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="cams-section cams-updates" id="sitemap">
            <div class="container-fluid cams-container">
                <div class="cams-update-grid">
                    <article class="cams-update-card">
                        <div class="cams-section-heading">
                            <span class="cams-eyebrow">Latest Updates</span>
                            <h2>Important CAMS information</h2>
                        </div>
                        <div class="cams-update-scroll" aria-label="Latest CAMS updates">
                            <ul class="cams-update-list">
                                <li>
                                    <span class="cams-update-date">Portal</span>
                                    <div>
                                        <strong>CAMS public portal refreshed</strong>
                                        <p>Users can now access workflow, login and key portal links from a cleaner homepage.</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="cams-update-date">Access</span>
                                    <div>
                                        <strong>Role-wise login access</strong>
                                        <p>Auditor, auditee institution and HOD login links are available from the shared header.</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="cams-update-date">Support</span>
                                    <div>
                                        <strong>Grievance services</strong>
                                        <p>Register and track grievances through the official CAMS support workflow.</p>
                                    </div>
                                </li>
                                <li aria-hidden="true">
                                    <span class="cams-update-date">Portal</span>
                                    <div>
                                        <strong>CAMS public portal refreshed</strong>
                                        <p>Users can now access workflow, login and key portal links from a cleaner homepage.</p>
                                    </div>
                                </li>
                                <li aria-hidden="true">
                                    <span class="cams-update-date">Access</span>
                                    <div>
                                        <strong>Role-wise login access</strong>
                                        <p>Auditor, auditee institution and HOD login links are available from the shared header.</p>
                                    </div>
                                </li>
                                <li aria-hidden="true">
                                    <span class="cams-update-date">Support</span>
                                    <div>
                                        <strong>Grievance services</strong>
                                        <p>Register and track grievances through the official CAMS support workflow.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </article>

                    <aside class="cams-faq-panel" aria-label="Frequently asked questions about CAMS">
                        <div class="cams-faq-heading">
                            <span class="cams-eyebrow">Need clarity?</span>
                            <h2>Questions about this website</h2>
                            <p>Quick answers for officers, institutions and visitors using the CAMS public portal.</p>
                        </div>

                        <div class="cams-faq-list">
                            <details open>
                                <summary>
                                    <span><i class="fas fa-info-circle" aria-hidden="true"></i> What is the CAMS?</span>
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                </summary>
                                <p>CAMS is a secure Comprehensive Audit Management System for planning, executing, tracking and reporting government audit work.</p>
                            </details>
                            <details>
                                <summary>
                                    <span><i class="fas fa-sitemap" aria-hidden="true"></i> What are the Department Involved in CAMS?</span>
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                </summary>
                                <p>CAMS supports audit workflows for LFAD, SGAD, HRIA, DCA and DAMC departments through the official portal.</p>
                            </details>
                        </div>

                        <a class="cams-faq-action" href="{{ url('/login') }}">
                            <span>Continue to CAMS login</span>
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </aside>

                </div>
            </div>
        </section>
    </main>

    <button type="button" class="cams-back-to-top" aria-label="Back to top">
        <i class="fas fa-arrow-up" aria-hidden="true"></i>
    </button>

    @include('layouts.site_footer')

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('site/js/cams-public.js') }}"></script>
</body>

</html>
