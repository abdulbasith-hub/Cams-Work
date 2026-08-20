@php
    $pages = [
        'disclaimer' => [
            'title' => 'Disclaimer',
            'titleKey' => 'disclaimer',
            'eyebrow' => 'Official Portal Information',
            'icon' => 'fas fa-shield-alt',
            'summary' => 'Important usage information for content, links and online material published through CAMS.',
            'sections' => [
                [
                    'heading' => 'Government Websites',
                    'headingKey' => 'goverment_title',
                    'items' => [
                        ['key' => 'disclaimer_p1', 'text' => 'The contents of this website are owned and maintained by the Director General of Audit Department - Finance. While every care has been taken to ensure the accuracy of information furnished, the Director General of Audit Department / NIC does not accept any responsibility for any damage or loss from direct/indirect use of this information. Please report errors/omissions for correction.'],
                        ['key' => 'disclaimer_p2', 'text' => 'Certain links may lead to third-party resources we do not control. We accept no responsibility for their content.'],
                    ],
                ],
                [
                    'heading' => 'Online Content',
                    'headingKey' => 'onlinecontent',
                    'items' => [
                        ['key' => 'disclaimer_p3', 'text' => 'The content on this website is for general informational purposes. While we aim to keep it accurate and up to date, we make no warranties of any kind regarding accuracy, reliability, or availability. Use this information at your own risk.'],
                        ['key' => 'disclaimer_p4', 'text' => 'We are not liable for any loss or damage, including indirect or consequential losses, or loss of data or profits arising from the use of this website.'],
                        ['key' => 'disclaimer_p5', 'text' => 'Links to external websites are provided for convenience. We have no control over the content or availability of those sites.'],
                    ],
                ],
            ],
        ],
        'privacy' => [
            'title' => 'Privacy & Copyright Policy',
            'titleKey' => 'privacycopy',
            'eyebrow' => 'Official Portal Policy',
            'icon' => 'fas fa-lock',
            'summary' => 'How CAMS protects user information, copyright material and external linking practices.',
            'sections' => [
                [
                    'heading' => 'Copyright Policy',
                    'headingKey' => 'copyright',
                    'paragraphs' => [
                        ['key' => 'copyright_p1', 'text' => 'The contents published on this portal are primarily owned by the Director General of Audit Department. Reproduction of material must acknowledge the source, but permission does not extend to material identified as third-party copyright.'],
                    ],
                ],
                [
                    'heading' => 'Privacy Policy',
                    'headingKey' => 'privacy_head',
                    'paragraphs' => [
                        ['key' => 'privacy_head_p1', 'text' => 'Comprehensive Audit Management System (CAMS) does not automatically capture specific personal information without consent. Any personal information provided is protected from loss, misuse, unauthorized access, disclosure, alteration, or destruction. We do not sell or share personally identifiable information with any third party.'],
                    ],
                ],
                [
                    'heading' => 'Hyperlinking Policy',
                    'headingKey' => 'hyperlink_head',
                    'callouts' => [
                        ['heading' => 'Links to External Websites/Portals:', 'headingKey' => 'hyperlink_head_1', 'key' => 'hyperlink_head_p1', 'text' => 'Links on this portal to other websites/portals are for user convenience. NIC or Director General of Audit Department is not responsible for the contents and reliability of linked websites. The presence of a link on this portal should not be assumed as an endorsement.'],
                        ['heading' => 'Links to Comprehensive Audit Management System (CAMS) by Other Websites:', 'headingKey' => 'hyperlink_head_2', 'key' => 'hyperlink_head_p2', 'text' => 'Direct linking to information on Comprehensive Audit Management System (CAMS) is allowed without prior permission. Informing us of any links is appreciated to keep you informed of any changes. Loading our pages into frames on your site is not permitted; they must open in a new browser window.'],
                    ],
                ],
            ],
        ],
        'terms' => [
            'title' => 'Terms & Conditions',
            'titleKey' => 'term',
            'eyebrow' => 'Official Portal Terms',
            'icon' => 'fas fa-file-contract',
            'summary' => 'Conditions governing access to and use of the Comprehensive Audit Management System portal.',
            'sections' => [
                [
                    'heading' => 'Terms of Use',
                    'headingKey' => 'terms_head',
                    'items' => [
                        ['key' => 'terms_head_p1', 'text' => 'This website, Comprehensive Audit Management System (CAMS), is hosted by the Director General of Audit Department - Finance, Government of Tamil Nadu. It has been designed and developed by the National Informatics Centre, Government of India, in consultation with the Director General of Audit Department. The content is owned and maintained by the Director General of Audit Department.'],
                        ['key' => 'terms_head_p2', 'text' => 'Non-compliance with the terms of use and privacy policy may result in the immediate blocking of the violated user\'s access rights to this system upon authentic proof provided through the Contact Us link. In no event will the Organization/Department or NIC be liable for any expense, loss, or damage arising from the use of this portal.'],
                        ['key' => 'terms_head_p3', 'text' => 'These terms and conditions are governed by Indian laws, and any dispute shall be subject to the exclusive jurisdiction of the Madras High Court.'],
                    ],
                ],
            ],
        ],
        'screenreader' => [
            'title' => 'Screen Reader',
            'titleKey' => 'screenreaderheading',
            'eyebrow' => 'Accessibility Support',
            'icon' => 'fas fa-universal-access',
            'summary' => 'Guidance for enabling screen reader support while accessing the CAMS public portal.',
            'sections' => [
                [
                    'heading' => 'Screen Reader - NVDA',
                    'headingKey' => 'screenreaderhead',
                    'items' => [
                        ['key' => 'screen_point1', 'text' => '1. Download the Screen Reader Access software from the below link.'],
                        ['key' => 'screen_point2', 'text' => '2. https://www.nvaccess.org/download/', 'href' => 'https://www.nvaccess.org/download/'],
                        ['key' => 'screen_point3', 'text' => '3. Screen reader Access to enable people with visual impairments access the website using assistive technologies, such as screen readers.'],
                    ],
                ],
                [
                    'heading' => 'Steps to enable the screen reader',
                    'headingKey' => 'screen_head2',
                    'items' => [
                        ['key' => 'screen_head_1', 'text' => '1. Download the software from the link provided.'],
                        ['key' => 'screen_head_2', 'text' => '2. Run the executable file'],
                        ['key' => 'screen_head_3', 'text' => '3. Screen reader will be enabled automatically.'],
                        ['key' => 'screen_head_4', 'text' => '4. It can be configured to function on loading of Windows OS itself.'],
                    ],
                ],
                [
                    'heading' => 'Control Keys',
                    'headingKey' => 'screen_headcontrol',
                    'shortcuts' => [
                        ['key' => 'screen_head3_1', 'label' => 'Ctrl', 'text' => "a. 'Ctrl' key will 'Stop' the audio."],
                        ['key' => 'screen_head3_2', 'label' => 'Shift', 'text' => "b. 'Shift' key will 'Pause' the audio."],
                        ['key' => 'screen_head3_3', 'label' => 'Insert', 'text' => "c. 'Insert' key will 'Restart' the audio again."],
                    ],
                ],
            ],
        ],
    ];

    $policy = $pages[$page ?? 'disclaimer'] ?? $pages['disclaimer'];
@endphp

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAMS - {{ $policy['title'] }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('site/image/tn__logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/font-awesome/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/cams-public.css') }}">
</head>

<body class="cams-public-page cams-policy-page">
    @include('layouts.site_header')

    <main class="cams-policy-main">
        <section class="cams-policy-hero">
            <div class="container-fluid cams-container">
                <span class="cams-eyebrow">
                    <i class="{{ $policy['icon'] }}" aria-hidden="true"></i>
                    {{ $policy['eyebrow'] }}
                </span>
                <h1 class="lang" key="{{ $policy['titleKey'] }}">{{ $policy['title'] }}</h1>
                <p>{{ $policy['summary'] }}</p>
            </div>
        </section>

        <section class="cams-policy-section">
            <div class="container-fluid cams-container">
                <article class="cams-policy-card">
                    @foreach ($policy['sections'] as $section)
                        <section class="cams-policy-content-block">
                            <h2 class="lang" key="{{ $section['headingKey'] }}">{{ $section['heading'] }}</h2>

                            @isset($section['paragraphs'])
                                @foreach ($section['paragraphs'] as $paragraph)
                                    <p class="lang" key="{{ $paragraph['key'] }}">{{ $paragraph['text'] }}</p>
                                @endforeach
                            @endisset

                            @isset($section['items'])
                                <ul class="cams-policy-list">
                                    @foreach ($section['items'] as $item)
                                        <li>
                                            @isset($item['href'])
                                                <a href="{{ $item['href'] }}" target="_blank" rel="noopener" class="cams-policy-link lang" key="{{ $item['key'] }}">{{ $item['text'] }}</a>
                                            @else
                                                <span class="lang" key="{{ $item['key'] }}">{{ $item['text'] }}</span>
                                            @endisset
                                        </li>
                                    @endforeach
                                </ul>
                            @endisset

                            @isset($section['callouts'])
                                @foreach ($section['callouts'] as $callout)
                                    <div class="cams-policy-callout">
                                        <h3 class="lang" key="{{ $callout['headingKey'] }}">{{ $callout['heading'] }}</h3>
                                        <p class="lang" key="{{ $callout['key'] }}">{{ $callout['text'] }}</p>
                                    </div>
                                @endforeach
                            @endisset

                            @isset($section['shortcuts'])
                                <div class="cams-policy-shortcuts">
                                    @foreach ($section['shortcuts'] as $shortcut)
                                        <span class="lang" key="{{ $shortcut['key'] }}"><b>{{ $shortcut['label'] }}</b> {{ $shortcut['text'] }}</span>
                                    @endforeach
                                </div>
                            @endisset
                        </section>
                    @endforeach
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
