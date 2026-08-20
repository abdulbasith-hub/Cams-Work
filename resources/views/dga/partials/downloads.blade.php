@if (!empty($content['downloads']))
    @php
        $resolveDownloadHref = function ($href) {
            if (empty($href)) {
                return '#';
            }
            if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://') || str_starts_with($href, '/')) {
                return $href;
            }
            return asset($href);
        };
    @endphp
    <section class="dga-section" id="downloads" aria-label="Downloads">
        <div class="container-fluid px-lg-5">
            <div class="dga-section-title text-center">
                <span>Downloads</span>
                <h3>Acts, rules, and guidance documents</h3>
            </div>
            <ul class="dga-portal-downloads-list">
                @foreach ($content['downloads'] as $download)
                    <li class="dga-portal-download-item">
                        <a href="{{ $resolveDownloadHref($download['href'] ?? '#') }}" target="_blank" rel="noopener">
                            <span class="dga-portal-download-icon"><i class="fas fa-file-download" aria-hidden="true"></i></span>
                            <span class="dga-portal-download-text">
                                <strong>{{ $download['title'] ?? '' }}</strong>
                                @if (!empty($download['meta']))
                                    <small>{{ $download['meta'] }}</small>
                                @endif
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
    <style>
        .dga-portal-downloads-list { list-style: none; margin: 1.5rem 0 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        .dga-portal-download-item a { display: flex; align-items: center; gap: .85rem; padding: 1rem 1.15rem; border-radius: 12px; background: #fff; border: 1px solid rgba(11, 95, 165, 0.12); box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06); text-decoration: none; color: inherit; transition: transform .2s ease, box-shadow .2s ease; }
        .dga-portal-download-item a:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12); }
        .dga-portal-download-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: rgba(11, 95, 165, 0.1); color: #0b5fa5; flex-shrink: 0; }
        .dga-portal-download-text { display: flex; flex-direction: column; }
        .dga-portal-download-text strong { color: #0f172a; font-size: .95rem; }
        .dga-portal-download-text small { color: #64748b; font-size: .8rem; }
    </style>
@endif
