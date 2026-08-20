<footer class="cams-site-footer" id="contact">
    <div class="container-fluid cams-container">
        <div class="cams-footer-simple">
            <img src="{{ asset('site/image/niclogo.png') }}" alt="National Informatics Centre logo">
            <p>Designed and Developed by NIC &copy; {{ date('Y') }}. All Rights Reserved</p>
            <p>Queries/Comments regarding the content on this site may be sent to cams[dot]dga[at]tn[dot]gov[dot]in</p>
            <nav class="cams-footer-links" aria-label="Footer policy links">
                <a href="{{ url('disclaimer') }}" class="{{ request()->is('disclaimer') ? 'is-active' : '' }}" @if(request()->is('disclaimer')) aria-current="page" @endif>Disclaimer</a>
                <a href="{{ url('privacy') }}" class="{{ request()->is('privacy') ? 'is-active' : '' }}" @if(request()->is('privacy')) aria-current="page" @endif>Privacy &amp; Copyright Policy</a>
                <a href="{{ url('terms') }}" class="{{ request()->is('terms') ? 'is-active' : '' }}" @if(request()->is('terms')) aria-current="page" @endif>Terms &amp; Conditions</a>
                {{-- <a href="{{ url('screenreader') }}" class="{{ request()->is('screenreader') ? 'is-active' : '' }}" @if(request()->is('screenreader')) aria-current="page" @endif>Screen Reader</a> --}}
            </nav>
        </div>
    </div>
</footer>
