<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PathLAS') - Pathology Lab System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        accent: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { transform: translateX(4px); }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .btn-primary { transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    </style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 glass border-r border-gray-200/50">
                <!-- Logo -->
                <div class="flex items-center h-16 px-6 border-b border-gray-200/50 bg-gradient-to-r from-primary-600 to-primary-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">PathLAS</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
                    <!-- New Bill Button -->
                    <div class="mb-2">
                        <a href="{{ route('bookings.create') }}" class="flex items-center justify-center w-full px-4 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all transform hover:-translate-y-0.5 font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            New Bill
                        </a>
                    </div>
                    <div class="mb-6">
                        <a href="{{ route('patients.create') }}" class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            New Patient
                        </a>
                    </div>

                    <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('patients.index') }}" class="sidebar-link flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('patients.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Patients
                    </a>

                    <a href="{{ route('bookings.index') }}" class="sidebar-link flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('bookings.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Bookings
                    </a>

                    @if(auth()->user()->isTechnician() || auth()->user()->isAdmin())
                    <a href="{{ route('results.pending') }}" class="sidebar-link flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('results.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Result Entry
                    </a>
                    @endif

                    <a href="{{ route('reports.index') }}" class="sidebar-link flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Reports
                    </a>

                    <!-- Lab Section (Pure CSS hover dropdown) -->
                    <div class="group pt-2">
                        <div class="w-full flex items-center justify-between px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                Lab
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <!-- Hidden by default, shows on group hover -->
                        <div class="pl-4 space-y-1 mt-1 hidden group-hover:block">
                            <a href="{{ route('tests.index') }}" class="flex items-center px-3 py-2 text-gray-600 rounded-lg {{ request()->routeIs('tests.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Test Catalogue
                            </a>
                            <a href="{{ route('packages.index') }}" class="flex items-center px-3 py-2 text-gray-600 rounded-lg {{ request()->routeIs('packages.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Test Packages
                            </a>
                            <a href="{{ route('tests.categories') }}" class="flex items-center px-3 py-2 text-gray-600 rounded-lg {{ request()->routeIs('tests.categories') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Test Categories
                            </a>
                            <a href="{{ route('lab.report-customization') }}" class="flex items-center px-3 py-2 text-gray-600 rounded-lg {{ request()->routeIs('lab.report-customization*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Report Design
                            </a>
                        </div>
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    {{-- Super Admin Links --}}
                    <div class="pt-2 border-t border-gray-200 mt-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Super Admin</p>
                        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('superadmin.dashboard') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('superadmin.labs') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('superadmin.labs') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path>
                            </svg>
                            Manage Labs
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                    {{-- Lab Admin Links --}}
                    <div class="pt-2 border-t border-gray-200 mt-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Admin</p>
                        <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path>
                            </svg>
                            Users
                        </a>
                        <a href="{{ route('lab.settings') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('lab.settings*') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Lab Settings
                        </a>
                        <a href="{{ route('activity-logs') }}" class="flex items-center px-3 py-2 text-gray-700 rounded-lg {{ request()->routeIs('activity-logs') ? 'bg-primary-50 text-primary-700 font-medium' : 'hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Activity Logs
                        </a>
                    </div>
                    @endif
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-gray-200/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center text-white font-semibold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role?->display_name ?? 'User' }}</p>
                        </div>
                        <a href="{{ route('profile.login-activity') }}" class="p-2 text-gray-400 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition" title="Login Activity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Header -->
            <header class="glass border-b border-gray-200/50 h-16 flex items-center justify-between px-6">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                </div>
                
                <!-- Universal Search Bar (hidden on settings pages) -->
                @if(!request()->routeIs('lab.settings*') && !request()->routeIs('users.*') && !request()->routeIs('activity-logs'))
                <div class="flex-1 max-w-md mx-6 relative" id="universal-search-container">
                    <div class="relative">
                        <input type="text" id="universal-search" 
                            placeholder="Search patients by name, phone, reg no..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition"
                            autocomplete="off">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <div id="search-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 max-h-80 overflow-y-auto z-50">
                        <div id="search-results-content"></div>
                    </div>
                </div>
                @endif
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">{{ now()->format('l, M d, Y') }}</span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                <div class="mb-6 animate-fade-in">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 animate-fade-in">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 animate-fade-in">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <div class="animate-fade-in">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 safe-area-pb">
        <div class="flex justify-around items-center h-16">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center px-3 py-2 {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="{{ route('patients.index') }}" class="flex flex-col items-center px-3 py-2 {{ request()->routeIs('patients.*') ? 'text-primary-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-xs mt-1">Patients</span>
            </a>
            <a href="{{ route('bookings.create') }}" class="flex items-center justify-center w-12 h-12 -mt-6 bg-primary-600 rounded-full text-white shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </a>
            <a href="{{ route('reports.index') }}" class="flex flex-col items-center px-3 py-2 {{ request()->routeIs('reports.*') ? 'text-primary-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-xs mt-1">Reports</span>
            </a>
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="flex flex-col items-center px-3 py-2 text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span class="text-xs mt-1">Menu</span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="hidden md:hidden fixed inset-0 bg-black/50 z-50" onclick="this.classList.add('hidden')">
        <div class="absolute bottom-16 left-0 right-0 bg-white rounded-t-2xl p-4 max-h-[70vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800">Menu</h3>
                <button onclick="document.getElementById('mobile-menu').classList.add('hidden')" class="p-2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="space-y-2">
                <a href="{{ route('bookings.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Bookings
                </a>
                <a href="{{ route('results.pending') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Result Entry
                </a>
                <a href="{{ route('tests.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Test Catalogue
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Users
                </a>
                <a href="{{ route('lab.settings') }}" class="flex items-center px-4 py-3 rounded-xl hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Lab Settings
                </a>
                @endif
            </div>
            <div class="mt-4 pt-4 border-t">
                <div class="flex items-center px-4 py-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role?->display_name ?? 'User' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-red-600 rounded-xl hover:bg-red-50">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Add padding for mobile nav */
        @media (max-width: 768px) {
            main { padding-bottom: 5rem !important; }
        }
    </style>

    <!-- Universal Search Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('universal-search');
        const searchResults = document.getElementById('search-results');
        const searchResultsContent = document.getElementById('search-results-content');
        const searchSpinner = document.getElementById('search-spinner');
        
        if (!searchInput) return;
        
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            searchSpinner.classList.remove('hidden');
            
            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(query)}`);
                    const patients = await response.json();
                    
                    if (patients.length === 0) {
                        searchResultsContent.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                No patients found for "${query}"
                            </div>`;
                    } else {
                        searchResultsContent.innerHTML = patients.map(p => `
                            <a href="/patients/${p.id}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 font-semibold">
                                    ${(p.name || '?').charAt(0).toUpperCase()}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate">${p.name || 'Unknown Name'}</p>
                                    <p class="text-xs text-gray-500">${p.patient_id || ''} • ${p.phone || 'No phone'} • ${p.age || '?'}${p.gender ? p.gender.charAt(0).toUpperCase() : ''}</p>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <span class="px-2 py-1 bg-gray-100 rounded">View</span>
                                </div>
                            </a>
                        `).join('');
                    }
                    
                    searchResults.classList.remove('hidden');
                } catch (error) {
                    console.error('Search error:', error);
                    searchResultsContent.innerHTML = '<div class="p-4 text-center text-red-500">Search failed</div>';
                    searchResults.classList.remove('hidden');
                } finally {
                    searchSpinner.classList.add('hidden');
                }
            }, 300);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#universal-search-container')) {
                searchResults.classList.add('hidden');
            }
        });
        
        // Keyboard shortcut (Ctrl+K or /)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName))) {
                e.preventDefault();
                searchInput.focus();
            }
            if (e.key === 'Escape') {
                searchResults.classList.add('hidden');
                searchInput.blur();
            }
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
