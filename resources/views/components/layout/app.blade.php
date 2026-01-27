<!DOCTYPE html>
<html lang="id">

<head>
    @laravelPWA
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta property="og:site_name" content="{{ site_setting('site_name_highlight', 'Ayo') }}{{ site_setting('site_name_rest', 'buatbaik') }}">
    <meta property="og:title" content="@yield('og_title', site_setting('site_title', 'Platform Donasi Digital'))">
    <meta property="og:description" content="@yield('og_description', site_setting('site_description', 'Platform donasi digital'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('og_url', site_setting('site_url', 'https://example.com'))">
    <meta property="og:image" content="@yield('og_image', asset(site_setting('site_logo', '/img/icon_ABBI.png')))">

    <!-- Meta Pixel Code -->
    @php
        $pixelId = site_setting('meta_pixel_id', config('services.meta.pixel_id'));
    @endphp
    @if($pixelId)
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');

        @auth
            @php
                $userData = [];
                if (!empty(auth()->user()->email)) {
                    $userData['em'] = hash('sha256', strtolower(trim(auth()->user()->email)));
                }
                
                if (!empty(auth()->user()->phone)) {
                    $phone = preg_replace('/[^0-9]/', '', auth()->user()->phone);
                    if (strlen($phone) >= 9) {
                        $userData['ph'] = hash('sha256', $phone);
                    }
                }
            @endphp

            @if (!empty($userData))
                fbq('init', '{{ $pixelId }}', {!! json_encode($userData) !!});
            @else
                fbq('init', '{{ $pixelId }}');
            @endif
        @else
            fbq('init', '{{ $pixelId }}');
        @endauth

        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" /></noscript>
    @endif
    <!-- End Meta Pixel Code -->
    <title>@yield('title', site_setting('site_title', 'Platform Donasi Digital'))</title>
    <link rel="icon" type="image/png" href="{{ asset(site_setting('site_logo', 'img/icon_ABBI.png')) }}">

    {{-- Preconnect to Speed up Font Loading --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Font Awesome (Asynchronous Load) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          media="print" onload="this.media='all'" />
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    </noscript>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-primary: {{ hex2rgb(site_setting('theme_primary', '#242124')) }};
            --color-secondary: {{ hex2rgb(site_setting('theme_secondary', '#daaf65')) }};
            --color-hijau: {{ hex2rgb(site_setting('theme_hijau', '#16a34a')) }};
            --color-gold-light: {{ hex2rgb(site_setting('theme_gold_light', '#F7EF8A')) }};
            --color-gold-dark: {{ hex2rgb(site_setting('theme_gold_dark', '#B8860B')) }};
        }
    </style>
</head>

<body class="bg-grayLight font-poppins">
    {{-- Custom PWA Install Prompt --}}
    @include('components.pwa-install-prompt')

    <div class="mobile-container">
        <div class="content">
            @yield('header-content')
            <main>
                @yield('content')
            </main>
        </div>
        @include('components.layout.navigation')

        <!-- Floating WhatsApp Button -->
        @if(site_setting('whatsapp_number'))
        <a href="https://wa.me/{{ site_setting('whatsapp_number') }}?text={{ urlencode(site_setting('whatsapp_message', 'Halo')) }}" target="_blank"
            class="floating-wa" onclick="typeof fbq !== 'undefined' && fbq('track', 'Contact');">
            <i class="fab fa-whatsapp text-3xl"></i>
        </a>
        @endif
    </div>

    @yield('scripts')
</body>

</html>
