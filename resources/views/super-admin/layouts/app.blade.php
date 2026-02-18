<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Permissions-Policy" content="speculation-rules=(), interest-cohort=(), browsing-topics=()">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="msapplication-TileImage" content="/images/icons/icon-144x144.png">
    <meta name="msapplication-TileColor" content="#4F46E5">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-192x192.png">



    <title>{{ config('app.name', 'eLEB') }} - Super Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 2px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 1px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.8);
        }

        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .scrollbar-thin:not(:hover)::-webkit-scrollbar-thumb {
            background: transparent;
        }

        .scrollbar-thin:not(:hover) {
            scrollbar-color: transparent transparent;
        }

        .nav-item {
            transform-style: preserve-3d;
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .nav-item:active {
            transform: translateY(1px) scale(0.98);
        }
    </style>
</head>

<body class="font-sans antialiased" style="background-color: #E1E6F1;" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">

        <!-- Mobile Toggle Button -->
        <div @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden fixed bottom-4 right-4 z-50 cursor-pointer transform transition-transform duration-300 hover:scale-105 active:scale-95">
            <div class="flex flex-col gap-1.5 bg-gradient-to-br from-gray-700 to-gray-900 p-3 rounded-lg shadow-lg border border-gray-700 relative overflow-hidden"
                style="transform-style: preserve-3d; transform: perspective(500px) rotateX(10deg);">
                <!-- Top bar -->
                <div class="w-8 h-1.5 bg-gray-200 rounded-full transform transition-all duration-300"></div>
                <!-- Middle bar - blue accent -->
                <div class="w-8 h-1.5 bg-blue-600 rounded-full transform transition-all duration-300"></div>
                <!-- Bottom bar -->
                <div class="w-8 h-1.5 bg-gray-200 rounded-full transform transition-all duration-300"></div>
                <!-- 3D effect elements -->
                <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-30 rounded-lg"></div>
                <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-black opacity-20 rounded-b-lg"></div>
                <div class="absolute top-0 left-0 w-full h-1/4 bg-white opacity-10 rounded-t-lg"></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div :class="{'translate-x-0': sidebarOpen, 'translate-x-full': !sidebarOpen}"
            class="fixed top-0 right-0 z-40 h-screen w-20 transform transition-transform duration-300 ease-in-out lg:left-0 lg:translate-x-0">

            <!-- Sidebar Content -->
            <div class="h-screen bg-white border-r border-gray-200 shadow-lg overflow-y-auto scrollbar-thin flex flex-col">

                <!-- Top Navigation Links -->
                <nav class="flex-1 py-4 space-y-2">

                    <!-- Dashboard -->
                    <a href="{{ route('super-admin.dashboard') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.dashboard') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">CONSOLE</span>
                    </a>

                    <!-- Business Management -->
                    <a href="{{ route('super-admin.businesses.index') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.businesses.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">HOSPITALS</span>
                    </a>

                    <!-- Admin Management -->
                    <a href="{{ route('super-admin.admins.index') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.admins.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">ADMINS</span>
                    </a>

                    <!-- Medicine Management -->
                    <!-- <a href="{{ route('super-admin.common-medicines.index') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.common-medicines.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">MEDICINES</span>
                    </a> -->

                    <!-- Subscription Management -->
                    <div x-data="{ open: {{ request()->routeIs('super-admin.subscriptions.*') ? 'true' : 'false' }} }" class="relative">
                        <button @click="open = !open"
                            class="nav-item w-full flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.subscriptions.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="mt-2 text-xs font-medium">SUBSCRIPTIONS</span>
                        </button>

                        <!-- Subscription Dropdown Menu -->
                        <div x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            style="z-index: 50;"
                            class="fixed lg:left-20 right-20 lg:right-auto top-1/2 transform -translate-y-1/2 w-48 bg-white rounded-lg shadow-xl py-2 border border-gray-100">

                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">Subscription Management</p>
                            </div>

                            <a href="{{ route('super-admin.subscriptions.settings') }}"
                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150 {{ request()->routeIs('super-admin.subscriptions.settings') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Settings</span>
                            </a>

                            <a href="{{ route('super-admin.subscriptions.pending-payments') }}"
                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150 {{ request()->routeIs('super-admin.subscriptions.pending-payments') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <span>Pending Payments</span>
                            </a>

                            <a href="{{ route('super-admin.subscriptions.businesses') }}"
                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150 {{ request()->routeIs('super-admin.subscriptions.businesses') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Business Management</span>
                            </a>

                            <a href="{{ route('super-admin.subscriptions.payment-history') }}"
                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150 {{ request()->routeIs('super-admin.subscriptions.payment-history') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Payment History</span>
                            </a>
                        </div>
                    </div>

                    <!-- Letterhead Management -->
                    <a href="{{ route('super-admin.letterheads.index') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.letterheads.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">LETTERHEADS</span>
                    </a>

                    <!-- System Settings -->
                    <a href="{{ route('super-admin.settings.index') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.settings.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">SETTINGS</span>
                    </a>

                    <!-- System Reports -->
                    <!-- <a href="#"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.reports.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">REPORTS</span>
                    </a> -->

                    <!-- System Logs -->
                    <!-- <a href="#"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('super-admin.logs.*') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">LOGS</span>
                    </a> -->

                    <!-- HELP -->
                    <a href="https://wa.me/8801684048203"
                        target="_blank"
                        title="CONTACT US ON: 01684048203"
                        class="nav-item flex flex-col items-center justify-center p-4 hover:bg-green-50 transition-all duration-200 rounded-lg group">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm group-hover:bg-green-100">
                            <svg class="w-7 h-7 text-green-600 group-hover:text-green-700" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.63" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium group-hover:text-green-700">HELP</span>
                    </a>

                </nav>

                <!-- Bottom Section with Settings and Logout -->
                <div class="border-t border-gray-200 py-4 space-y-2">

                    <!-- Settings/Profile -->
                    <a href="{{ route('profile.edit') }}"
                        class="nav-item flex flex-col items-center justify-center p-4 {{ request()->routeIs('profile.edit') ? 'text-blue-700 bg-blue-50' : 'text-gray-700' }} hover:bg-gray-100 transition-all duration-200 rounded-lg">
                        <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-50 shadow-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="mt-2 text-xs font-medium">PROFILE</span>
                    </a>

                    <!-- Logout with Dropdown -->
                    <div x-data="{ profileOpen: false }" class="w-full relative">
                        <button @click="profileOpen = !profileOpen"
                            class="nav-item w-full flex flex-col items-center justify-center p-4 text-gray-700 hover:bg-gray-100 transition-all duration-200 rounded-lg">
                            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 shadow-sm">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="mt-2 text-xs font-medium">LOGOUT</span>
                        </button>

                        <!-- Enhanced Dropdown Menu with Responsive Positioning -->
                        <div x-show="profileOpen"
                            @click.away="profileOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            style="z-index: 50;"
                            class="fixed lg:left-20 right-20 lg:right-auto bottom-24 w-48 bg-white rounded-lg shadow-xl py-2 border border-gray-100">

                            <!-- User Info Section -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Profile Link -->
                            <a href="{{ route('profile.edit') }}"
                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Edit Profile</span>
                            </a>

                            <!-- Business Settings Link -->
                            <a href="{{ route('super-admin.businesses.edit', auth()->user()->business_id ?? 1) }}"
                                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50 flex items-center transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Business Settings</span>
                            </a>

                            <!-- Logout Option -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-red-50 flex items-center transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div x-show="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden">
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen">
            <div class="p-4 lg:p-10 lg:ml-16 flex-1 overflow-y-auto" style="background-color: #E1E6F1;">
                <div class="container mx-auto">

                    <!-- Flash Messages -->
                    @if (session('success'))
                    <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl shadow-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                    @endif

                    @if (session('warning'))
                    <div class="mb-6 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-2xl shadow-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="font-medium">{{ session('warning') }}</span>
                        </div>
                    </div>
                    @endif

                    @if (session('info'))
                    <div class="mb-6 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-2xl shadow-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ session('info') }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileSidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 md:hidden">

        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="mobileSidebarOpen = false"></div>

        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="mobileSidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Sidebar Content -->
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">Super Admin</h1>
                            <p class="text-xs text-gray-500">System Management</p>
                        </div>
                    </div>
                </div>

                <nav class="mt-5 px-2 space-y-1">
                    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-md group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Subscription Management (Mobile) -->
                    <div x-data="{ open: {{ request()->routeIs('super-admin.subscriptions.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                            class="w-full flex items-center px-2 py-2 text-sm font-medium {{ request()->routeIs('super-admin.subscriptions.*') ? 'text-red-600 bg-red-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-md group">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="flex-1 text-left">Subscriptions</span>
                            <svg x-show="open" :class="{'rotate-90': open}"
                                class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-1 space-y-1">
                            <a href="{{ route('super-admin.subscriptions.settings') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('super-admin.subscriptions.settings') ? 'text-red-600 bg-red-50' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg">
                                Settings
                            </a>
                            <a href="{{ route('super-admin.subscriptions.pending-payments') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('super-admin.subscriptions.pending-payments') ? 'text-red-600 bg-red-50' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg">
                                Pending Payments
                            </a>
                            <a href="{{ route('super-admin.subscriptions.businesses') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('super-admin.subscriptions.businesses') ? 'text-red-600 bg-red-50' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg">
                                Business Management
                            </a>
                            <a href="{{ route('super-admin.subscriptions.payment-history') }}"
                                class="block px-3 py-2 text-sm {{ request()->routeIs('super-admin.subscriptions.payment-history') ? 'text-red-600 bg-red-50' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg">
                                Payment History
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('super-admin.businesses.index') }}" class="flex items-center px-2 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hospitals
                    </a>
                    <a href="{{ route('super-admin.admins.index') }}" class="flex items-center px-2 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Admins
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Additional Scripts -->
    <script>
        // Add loading states for navigation links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript:"])');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Add loading state
                    const icon = this.querySelector('svg');
                    if (icon && !icon.classList.contains('animate-spin')) {
                        const originalHTML = icon.innerHTML;
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />';
                        icon.classList.add('animate-spin');

                        // Reset after 2 seconds in case navigation fails
                        setTimeout(() => {
                            icon.innerHTML = originalHTML;
                            icon.classList.remove('animate-spin');
                        }, 2000);
                    }
                });
            });
        });

        // Add smooth scroll behavior for in-page navigation
        document.documentElement.style.scrollBehavior = 'smooth';

        // Add keyboard navigation support
        document.addEventListener('keydown', function(e) {
            // Alt + D for Dashboard
            if (e.altKey && e.key === 'd') {
                e.preventDefault();
                window.location.href = "{{ route('super-admin.dashboard') }}";
            }

            // Alt + H for Hospitals
            if (e.altKey && e.key === 'h') {
                e.preventDefault();
                window.location.href = "{{ route('super-admin.businesses.index') }}";
            }

            // Alt + A for Admins
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                window.location.href = "{{ route('super-admin.admins.index') }}";
            }

            // Alt + M for Medicines
            if (e.altKey && e.key === 'm') {
                e.preventDefault();
                window.location.href = "{{ route('super-admin.common-medicines.index') }}";
            }

            // Alt + S for Subscriptions
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                window.location.href = "{{ route('super-admin.subscriptions.settings') }}";
            }
        });

        // Add notification for keyboard shortcuts
        if (!localStorage.getItem('super-admin-shortcut-notification-shown')) {
            setTimeout(() => {
                console.log('💡 Super Admin Keyboard shortcuts: Alt+D (Dashboard), Alt+H (Hospitals), Alt+A (Admins), Alt+M (Medicines), Alt+S (Subscriptions)');
                localStorage.setItem('super-admin-shortcut-notification-shown', 'true');
            }, 3000);
        }

        // Connection status indicator
        window.addEventListener('online', function() {
            console.log('✅ Super Admin Connection restored');
        });

        window.addEventListener('offline', function() {
            console.log('❌ Super Admin Connection lost');
        });

        // Preload critical routes for better performance
        document.addEventListener('DOMContentLoaded', function() {
            const criticalRoutes = [
                "{{ route('super-admin.dashboard') }}",
                "{{ route('super-admin.businesses.index') }}",
                "{{ route('super-admin.admins.index') }}",
                "{{ route('super-admin.common-medicines.index') }}",
                "{{ route('super-admin.subscriptions.settings') }}"
            ];

            criticalRoutes.forEach(route => {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = route;
                document.head.appendChild(link);
            });
        });

        // Mobile sidebar touch gestures
        document.addEventListener('DOMContentLoaded', function() {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;

            const sidebar = document.querySelector('[x-data]');

            if (sidebar) {
                sidebar.addEventListener('touchstart', function(e) {
                    startX = e.touches[0].clientX;
                    isDragging = true;
                });

                sidebar.addEventListener('touchmove', function(e) {
                    if (!isDragging) return;
                    currentX = e.touches[0].clientX;
                });

                sidebar.addEventListener('touchend', function(e) {
                    if (!isDragging) return;
                    isDragging = false;

                    const diffX = startX - currentX;

                    // If swiped right to left more than 50px, close sidebar
                    if (diffX > 50) {
                        // Trigger Alpine.js to close sidebar
                        this.__x.$data.sidebarOpen = false;
                    }
                    // If swiped left to right more than 50px, open sidebar
                    else if (diffX < -50) {
                        this.__x.$data.sidebarOpen = true;
                    }
                });
            }
        });

        // PWA WORKER INSTALLATION
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('PWA Service Worker registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('PWA Service Worker registration failed: ', registrationError);
                    });
            });
        }
    </script>

    @stack('scripts')
</body>

</html>