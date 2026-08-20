<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAMS - Latest Circular</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('site/image/tn__logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/cams-public.css') }}">
</head>

<body class="cams-public-page cams-notification-page">
    @include('layouts.site_header')

    <main class="cams-policy-main">
        <section class="cams-policy-hero">
            <div class="container-fluid cams-container">
                <span class="cams-eyebrow">
                    <i class="fas fa-bell" aria-hidden="true"></i>
                    Official Updates
                </span>
                <h1 class="lang" key="noti">Latest Circular</h1>
                <p>Important circulars and portal notifications published for CAMS users.</p>
            </div>
        </section>

        <section class="cams-policy-section">
            <div class="container-fluid cams-container">
                <article class="cams-policy-card">
                    <section class="cams-policy-content-block">
                        <h2 class="lang" key="noti">Latest Circular</h2>
                        <div class="cams-policy-callout">
                            <h3>Notice</h3>
                            <p key="construct" class="lang">Site is Under Construction.</p>
                        </div>
                    </section>
                </article>
            </div>
        </section>
    </main>

    @include('layouts.site_footer')

    <script src="{{ asset('assets/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('common/custom.js') }}"></script>
    <script src="{{ asset('site/js/cams-public.js') }}"></script>
</body>

</html>
