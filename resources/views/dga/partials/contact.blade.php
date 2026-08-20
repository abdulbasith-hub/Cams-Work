@if (!empty($content['contact']))
    <section class="dga-section" id="contact" aria-label="Contact DGA">
        <div class="container-fluid px-lg-5">
            <div class="dga-section-title text-center">
                <span>Contact</span>
                <h3>Reach the Office of the DGA</h3>
            </div>
            <div class="dga-portal-contact-grid">
                <div class="dga-portal-contact-details">
                    @if (!empty($content['contact']['office']))
                        <h4>{{ $content['contact']['office'] }}</h4>
                    @endif
                    @if (!empty($content['contact']['address']))
                        <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $content['contact']['address'] }}</p>
                    @endif
                    @if (!empty($content['contact']['phone']))
                        <p><i class="fas fa-phone" aria-hidden="true"></i> {{ $content['contact']['phone'] }}</p>
                    @endif
                    @if (!empty($content['contact']['email']))
                        <p><i class="fas fa-envelope" aria-hidden="true"></i> <a href="mailto:{{ $content['contact']['email'] }}">{{ $content['contact']['email'] }}</a></p>
                    @endif
                </div>
                @if (!empty($content['contact']['map_embed']))
                    <div class="dga-portal-contact-map">
                        <iframe src="{{ $content['contact']['map_embed'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="DGA office location"></iframe>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <style>
        .dga-portal-contact-grid { display: grid; grid-template-columns: minmax(240px, 1fr) minmax(240px, 1.4fr); gap: 1.5rem; margin-top: 1.5rem; align-items: stretch; }
        .dga-portal-contact-details { padding: 1.5rem; border-radius: 14px; background: #fff; border: 1px solid rgba(11, 95, 165, 0.12); box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06); }
        .dga-portal-contact-details h4 { color: #0f172a; margin-bottom: .85rem; }
        .dga-portal-contact-details p { display: flex; align-items: flex-start; gap: .6rem; color: #334155; margin-bottom: .6rem; }
        .dga-portal-contact-details i { color: #0b5fa5; margin-top: .2rem; }
        .dga-portal-contact-map { border-radius: 14px; overflow: hidden; min-height: 220px; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06); }
        .dga-portal-contact-map iframe { width: 100%; height: 100%; min-height: 220px; border: 0; }
        @media (max-width: 767px) {
            .dga-portal-contact-grid { grid-template-columns: 1fr; }
        }
    </style>
@endif
