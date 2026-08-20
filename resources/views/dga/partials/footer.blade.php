@php
    $footerTitle = $footerTitle ?? 'Director General of Audit';
    $footerSubtitle = $footerSubtitle ?? 'Government of Tamil Nadu';
    $footerCredit = $footerCredit ?? 'Designed and Developed by NIC <strong>&copy; 2026</strong>';
    $footerStyle = $footerStyle ?? 'simple';
    $footerNote = $footerNote ?? 'For CAMS portal support, DGA page updates, and technical assistance.';
    $footerMail = $footerMail ?? 'cams.dga@tn.gov.in';
    $footerSupportHeading = $footerSupportHeading ?? 'Training and Support';
    $footerSupportIcon = $footerSupportIcon ?? 'graduation-cap';
    $footerSupportItems = $footerSupportItems ?? [];
@endphp

<footer class="dga-footer">
    <div class="container">
        @if ($footerStyle === 'home-strip')
            <section class="dga-home-footer-strip" aria-label="DGA footer">
                <div class="dga-home-footer-center">
                    <span class="dga-home-footer-heading"><i class="fas fa-phone" aria-hidden="true"></i> Website Queries</span>
                    <span class="dga-home-footer-mail"><i class="fas fa-envelope" aria-hidden="true"></i> {{ $footerMail }}</span>
                    <p class="dga-home-footer-note">{{ $footerNote }}</p>
                </div>
                <div class="dga-home-footer-meta">
                    <span class="dga-home-footer-heading"><i class="fas fa-{{ $footerSupportIcon }}" aria-hidden="true"></i> {{ $footerSupportHeading }}</span>
                    <ul class="dga-footer-support-list">
                        @forelse ($footerSupportItems as $item)
                            <li>{{ $item }}</li>
                        @empty
                            <li>Foundation Training for new recruits</li>
                            <li>Refresher Courses for serving personnel</li>
                            <li>Specialized Training in Performance Audit across Audit Departments</li>
                        @endforelse
                    </ul>
                </div>
                <a class="dga-footer-top-link" href="#dga-page-top" data-dga-scroll-top aria-label="Back to top">
                    <i class="fas fa-arrow-up" aria-hidden="true"></i>
                </a>
            </section>
            <div class="dga-home-footer-credit-row">
                <span>{!! $footerCredit !!}</span>
            </div>
        @else
            <div class="dga-footer-main-row d-flex justify-content-between align-items-center flex-wrap">
                <span>{!! $footerTitle !!}</span>
                <span>{!! $footerSubtitle !!}</span>
            </div>
            <div class="dga-footer-credit-row">
                <span>{!! $footerCredit !!}</span>
            </div>
        @endif
    </div>
</footer>
