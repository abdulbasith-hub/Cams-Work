@if (!empty($content['gallery']))
    @php
        $resolveGallerySrc = function ($src) {
            if (empty($src)) {
                return '';
            }
            if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://') || str_starts_with($src, '/')) {
                return $src;
            }
            return asset($src);
        };
    @endphp
    <section class="dga-section" id="gallery" aria-label="DGA gallery">
        <div class="container-fluid px-lg-5">
            <div class="dga-section-title text-center">
                <span>Gallery</span>
                <h3>Moments from DGA and CAMS</h3>
            </div>
            <div class="dga-portal-gallery-grid">
                @foreach ($content['gallery'] as $image)
                    <figure class="dga-portal-gallery-item">
                        <img src="{{ $resolveGallerySrc($image['src'] ?? '') }}" alt="{{ $image['alt'] ?? '' }}" loading="lazy">
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
    <style>
        .dga-portal-gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .dga-portal-gallery-item { margin: 0; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08); aspect-ratio: 4 / 3; }
        .dga-portal-gallery-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s ease; }
        .dga-portal-gallery-item:hover img { transform: scale(1.05); }
    </style>
@endif
