<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard - ' . site_setting('site_name_highlight', 'Ayo') . site_setting('site_name_rest', 'buatbaik'))</title>

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset(site_setting('site_logo', 'img/icon_ABBI.png')) }}">

    <style>
        /* Sidebar transitions */
        .admin-sidebar {
            transition: all 0.3s ease;
        }

        /* Content offset for desktop sidebar */
        @media (min-width: 1024px) {
            .admin-content {
                margin-left: 16rem;
                transition: margin-left 0.3s ease;
            }
        }

        /* Collapsed mode */
        .sidebar-collapsed .admin-content {
            margin-left: 4rem;
        }

        /* Mobile sidebar slide */
        .admin-sidebar--mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .admin-sidebar--mobile.open {
            transform: translateX(0);
        }

        /* General layout */
        .admin-container {
            max-width: 100% !important;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }

        .truncate-10 {
            display: inline-block;
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .prose {
            max-width: 480px;
            /* Match .mobile-container di app.css */
            margin: 0 auto;
            /* Center jika perlu */
        }

        .prose-sm {
            font-size: 0.875rem;
            line-height: 1.7142857;
        }

        /* Sisanya sama seperti sebelumnya: h1/h2/p/img/mark */
        .prose h1,
        .prose h2,
        .prose h3 {
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }

        .prose p {
            margin-top: 1em;
            margin-bottom: 1em;
        }

        .prose img {
            max-width: 100%;
            height: auto;
            margin: 1em 0;
            display: block;
        }

        .prose mark {
            background-color: #fef08a;
            padding: 0.25em 0.5em;
            border-radius: 0.25em;
        }

        .prose *:last-child {
            margin-bottom: 0;
        }
    </style>
</head>

<body class="bg-gray-100 font-poppins" style="zoom: 80%">

    <!-- Overlay for Mobile -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <div class="admin-container flex">
        {{-- ✅ Desktop Sidebar --}}
        @include('components.layout.admin-sidebar', ['type' => 'desktop'])

        {{-- ✅ Mobile Sidebar --}}
        @include('components.layout.admin-sidebar', ['type' => 'mobile'])

        {{-- ✅ Main Content --}}
        <div class="admin-content flex-1 min-h-screen">
            {{-- Top Bar --}}
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center space-x-4">
                        <button id="mobile-menu-toggle" class="text-gray-600 hover:text-gray-900 lg:hidden">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                        <a href="{{ route('home') }}" target="_blank"
                            class="bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-goldDark transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Lihat Website
                        </a>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- JS for Sidebar --}}
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileSidebar = document.querySelector('.admin-sidebar--mobile');
            const mobileOverlay = document.getElementById('mobile-overlay');
            const sidebarCloseBtn = document.getElementById('mobile-sidebar-close');

            const openSidebar = () => {
                mobileSidebar.classList.add('open');
                mobileOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            const closeSidebar = () => {
                mobileSidebar.classList.remove('open');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            };

            // Toggle handlers
            mobileMenuToggle?.addEventListener('click', openSidebar);
            sidebarCloseBtn?.addEventListener('click', closeSidebar);
            mobileOverlay?.addEventListener('click', closeSidebar);

            // Reset on resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) closeSidebar();
            });
        });
    </script>
</body>

</html>
