<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <!-- Mobile-first responsive container with gradient background -->
    <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <!-- Logo Section -->
        <div class="flex-shrink-0 mb-6 sm:mb-8">
            <a href="/" class="block">
                <div class="flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <x-application-logo class="w-8 h-8 sm:w-10 sm:h-10 fill-current text-white" />
                </div>
            </a>
        </div>

        <!-- Main Content Card -->
        <div class="w-full max-w-sm sm:max-w-md bg-white/80 backdrop-blur-sm shadow-2xl border border-white/20 rounded-2xl sm:rounded-3xl overflow-hidden">
            <!-- Card Inner Content -->
            <div class="px-6 sm:px-8 py-6 sm:py-8">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 sm:mt-8 text-center">
            <p class="text-xs sm:text-sm text-gray-500">
                © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Custom styles for enhanced mobile experience -->
    <style>
        /* Ensure proper touch targets on mobile */
        @media (max-width: 640px) {

            input,
            button,
            a {
                min-height: 44px;
            }
        }

        /* Smooth focus transitions */
        input:focus,
        button:focus {
            transition: all 0.2s ease-in-out;
        }

        /* Enhanced backdrop blur support */
        @supports (backdrop-filter: blur(10px)) {
            .backdrop-blur-sm {
                backdrop-filter: blur(10px);
            }
        }

        /* Prevent zoom on input focus for iOS */
        @media screen and (max-width: 767px) {

            input[type="email"],
            input[type="password"],
            input[type="text"] {
                font-size: 16px;
            }
        }
    </style>
</body>

</html>