@if (!empty($content['services']))
    <section class="dga-section" id="services" aria-label="DGA services">
        <div class="container-fluid px-lg-5">
            <div class="dga-section-title text-center">
                <span>Services</span>
                <h3>What DGA offers</h3>
            </div>
            <div class="dga-portal-services-grid">
                @foreach ($content['services'] as $service)
                    <a class="dga-portal-service-card" href="{{ $service['href'] ?? '#' }}">
                        <span class="dga-portal-service-icon"><i class="{{ $service['icon'] ?? 'fas fa-star' }}" aria-hidden="true"></i></span>
                        <h4>{{ $service['title'] ?? '' }}</h4>
                        <p>{{ $service['description'] ?? '' }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    <style>
        .dga-portal-services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-top: 1.5rem; }
        .dga-portal-service-card { display: block; padding: 1.5rem; border-radius: 14px; background: #fff; border: 1px solid rgba(11, 95, 165, 0.12); box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06); text-decoration: none; color: inherit; transition: transform .2s ease, box-shadow .2s ease; }
        .dga-portal-service-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12); }
        .dga-portal-service-icon { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px; background: rgba(11, 95, 165, 0.1); color: #0b5fa5; font-size: 1.25rem; margin-bottom: .75rem; }
        .dga-portal-service-card h4 { font-size: 1.05rem; margin-bottom: .4rem; color: #0f172a; }
        .dga-portal-service-card p { font-size: .9rem; color: #475569; margin: 0; }
    </style>
@endif
