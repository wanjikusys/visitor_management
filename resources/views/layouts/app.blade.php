@props(['header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen" x-data="{ sidebarOpen: false }">

            <!-- Sidebar -->
            <div
                class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

                <!-- Logo -->
                <div class="flex items-center justify-center h-16 bg-gray-900 border-b border-gray-800">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-3 text-lg font-semibold text-white">Visitor MS</span>
                    </div>
                </div>

                <!-- User -->
                <div class="p-3 bg-gray-800/50 border-b border-gray-800">
                    <div class="flex items-center">
                        <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white truncate max-w-[160px]">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-400">
                                @if(auth()->user()->roles->count() > 0)
                                    {{ auth()->user()->roles->first()->display_name }}
                                @else
                                    Member
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Scrollable Nav -->
                <nav class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-3 py-3 space-y-1">

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Visitors -->
                    @if(auth()->user()->hasPermission('visitors.view'))
                        <div x-data="{ open: {{ request()->routeIs('admin.visitors.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ request()->routeIs('admin.visitors.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Visitors
                                </span>
                                <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                                <a href="{{ route('admin.visitors.index') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.visitors.index') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    All Visitors
                                </a>
                                @if(auth()->user()->hasPermission('visitors.create'))
                                    <a href="{{ route('admin.visitors.create') }}"
                                       class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.visitors.create') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                        Check-in Visitor
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Vehicles -->
                    @if(auth()->user()->hasPermission('vehicles.view'))
                        <div x-data="{ open: {{ request()->routeIs('admin.vehicles.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ request()->routeIs('admin.vehicles.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    Vehicles
                                </span>
                                <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                                <a href="{{ route('admin.vehicles.index') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.vehicles.index') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    All Vehicles
                                </a>
                                @if(auth()->user()->hasPermission('vehicles.create'))
                                    <a href="{{ route('admin.vehicles.create') }}"
                                       class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.vehicles.create') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                        Register Vehicle
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Assets -->
                    <div x-data="{ open: {{ request()->routeIs('admin.assets.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition
                                       {{ request()->routeIs('admin.assets.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Assets
                            </span>
                            <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                            <a href="{{ route('admin.assets.index') }}"
                               class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.assets.index') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                All Assets
                            </a>
                            <a href="{{ route('admin.assets.create') }}"
                               class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.assets.create') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                Add Asset
                            </a>
                        </div>
                    </div>

                    <!-- Parking Zones -->
                    <a href="{{ route('admin.parking-zones.index') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('admin.parking-zones.*') ? 'bg-blue-600 text-white shadow' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Parking Zones
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-800 my-3"></div>

                    <!-- HMIS -->
                    @if(auth()->user()->hasAnyPermission(['hmis.opd.view', 'hmis.ward.view', 'hmis.visitors.view', 'hmis.vehicles.view']))
                        <div x-data="{ open: {{ request()->routeIs('admin.hmis.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ request()->routeIs('admin.hmis.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H9L7 6v12a2 2 0 002 2z"></path>
                                    </svg>
                                    HMIS
                                </span>
                                <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                                @if(auth()->user()->hasPermission('hmis.opd.view'))
                                    <a href="{{ route('admin.hmis.opd.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.opd.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">OPD Register</a>
                                @endif
                                
                                @if(auth()->user()->hasPermission('hmis.ward.view'))
                                    <a href="{{ route('admin.hmis.ward.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.ward.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Ward Register</a>
                                @endif
                                
                                @if(auth()->user()->hasPermission('hmis.discharges.view'))
                                    <!-- Discharges Nested Submenu -->
                                    <div x-data="{ dischargesOpen: {{ request()->routeIs('admin.hmis.discharges.*') ? 'true' : 'false' }} }">
                                        <button @click="dischargesOpen = !dischargesOpen"
                                                class="w-full flex items-center justify-between px-3 py-1.5 text-sm rounded-md transition
                                                       {{ request()->routeIs('admin.hmis.discharges.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Discharges
                                            </span>
                                            <svg class="w-3 h-3 transition" :class="dischargesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="dischargesOpen" x-collapse class="ml-4 mt-1 space-y-1 pl-3 border-l border-gray-700">
                                            <a href="{{ route('admin.hmis.discharges.done') }}" class="block px-3 py-1.5 text-xs rounded-md transition {{ request()->routeIs('admin.hmis.discharges.done') ? 'text-emerald-400 bg-gray-700 font-medium' : 'text-gray-500 hover:text-white hover:bg-gray-700' }}">• Done Today</a>
                                            <a href="{{ route('admin.hmis.discharges.requested') }}" class="block px-3 py-1.5 text-xs rounded-md transition {{ request()->routeIs('admin.hmis.discharges.requested') ? 'text-orange-400 bg-gray-700 font-medium' : 'text-gray-500 hover:text-white hover:bg-gray-700' }}">• Requests Pending</a>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(auth()->user()->hasPermission('hmis.visitors.view'))
                                    <a href="{{ route('admin.hmis.visitors.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.visitors.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Inpatient Visitors</a>
                                @endif
                                
                                <a href="{{ route('admin.hmis.carers.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.carers.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Carers Info</a>
                                
                                @if(auth()->user()->hasPermission('hmis.vehicles.view'))
                                    <a href="{{ route('admin.hmis.vehicles.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.vehicles.*') && !request()->routeIs('admin.hmis.gatepass.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Vehicles</a>
                                @endif
                                
                                <a href="{{ route('admin.hmis.devices.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.devices.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Devices Movement</a>
                                <a href="{{ route('admin.hmis.gatepass.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.gatepass.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Vehicle Gatepass</a>
                                <a href="{{ route('admin.hmis.security.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.security.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Security Backoffice</a>
                                
                                @if(auth()->user()->hasPermission('reports.view'))
                                    <a href="{{ route('admin.hmis.reports.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.reports.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Reports</a>
                                @endif
                                
                                <a href="{{ route('admin.hmis.accounts.index') }}" class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.hmis.accounts.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">Account Operations</a>
                            </div>
                        </div>
                    @endif

                    <!-- Divider -->
                    <div class="border-t border-gray-800 my-3"></div>

                    <!-- Users & Roles (Admin only) -->
                    @if(auth()->user()->hasPermission('users.view'))
                        <div x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    User Management
                                </span>
                                <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                                <a href="{{ route('admin.users.index') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.users.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    Users
                                </a>
                                <a href="{{ route('admin.roles.index') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.roles.*') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    Roles & Permissions
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Reports -->
                    @if(auth()->user()->hasPermission('reports.view'))
                        <div x-data="{ open: {{ request()->routeIs('admin.reports.*') && !request()->routeIs('admin.hmis.reports.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ request()->routeIs('admin.reports.*') && !request()->routeIs('admin.hmis.reports.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Reports
                                </span>
                                <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
                                <a href="{{ route('admin.reports.index') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.reports.index') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    Overview
                                </a>
                                <a href="{{ route('admin.reports.visitors') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.reports.visitors') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    Visitor Reports
                                </a>
                                <a href="{{ route('admin.reports.vehicles') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.reports.vehicles') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    Vehicle Reports
                                </a>
                                <a href="{{ route('admin.reports.assets') }}"
                                   class="block px-3 py-2 text-sm rounded-md transition {{ request()->routeIs('admin.reports.assets') ? 'text-blue-400 bg-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                                    Asset Reports
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Divider -->
                    <div class="border-t border-gray-800 my-3"></div>

                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('profile.*') ? 'bg-blue-600 text-white shadow' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md transition hover:bg-red-600 hover:text-white">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Mobile topbar -->
            <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-12 px-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-600 hover:bg-gray-100" aria-label="Toggle sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ config('app.name', 'Laravel') }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="px-2 py-1 text-xs rounded-md text-white bg-red-600 hover:bg-red-700">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Desktop thin top bar -->
            <div class="hidden lg:block fixed top-0 left-64 right-0 z-40 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-12 px-4">
                    <div class="flex items-center gap-3 truncate">
                        @if ($header)
                            <div class="text-sm font-semibold text-gray-900 truncate">
                                {{ $header }}
                            </div>
                        @else
                            <div class="text-sm font-semibold text-gray-900 truncate">
                                {{ config('app.name', 'Laravel') }}
                            </div>
                        @endif
                        <span class="text-xs text-gray-500">|</span>
                        <span class="text-xs text-gray-600" x-data x-text="new Date().toLocaleString()"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden xl:block text-xs text-gray-500 truncate max-w-[260px]">
                            {{ auth()->user()->email ?? '' }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="px-3 py-1.5 text-xs rounded-md text-white bg-red-600 hover:bg-red-700">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Main -->
            <div class="lg:pl-64">
                <main class="pt-12 lg:pt-12">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
