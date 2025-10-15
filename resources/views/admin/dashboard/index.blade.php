<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-3xl text-gray-900 leading-tight">
                    Dashboard
                </h2>
                <p class="text-gray-600 mt-1">Welcome back, <span class="font-semibold text-blue-600">{{ auth()->user()->name }}</span></p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-xs text-gray-500">Today</p>
                    <p class="text-sm font-bold text-gray-900">{{ now()->format('M d, Y') }}</p>
                </div>
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-4 py-2 rounded-xl shadow-lg">
                    <p class="text-xs text-blue-100">Time</p>
                    <p class="text-sm font-bold text-white" x-data x-text="new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })" x-init="setInterval(() => { $el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }, 1000)"></p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Welcome Banner with Glassmorphism -->
            <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 shadow-2xl" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 100)">
                <!-- Animated Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-pink-300 rounded-full mix-blend-overlay filter blur-3xl animate-pulse" style="animation-delay: 2s"></div>
                </div>
                
                <div class="relative px-8 py-12">
                    <div class="flex items-center justify-between">
                        <div class="flex-1" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition: all 0.6s ease-out">
                            <h3 class="text-4xl font-bold text-white mb-3">
                                👋 Welcome Back, {{ auth()->user()->name }}!
                            </h3>
                            <p class="text-xl text-blue-100 mb-6">
                                Here's what's happening with your visitor management system today.
                            </p>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('admin.visitors.create') }}" class="group inline-flex items-center px-6 py-3 bg-white text-blue-600 font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Check-in Visitor
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-bold rounded-xl hover:bg-white/30 transition-all duration-200 border border-white/30">
                                    View Reports
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="hidden lg:block" :class="mounted ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" style="transition: all 0.8s ease-out 0.3s">
                            <svg class="w-64 h-64" viewBox="0 0 200 200" fill="none">
                                <circle cx="100" cy="100" r="80" fill="white" opacity="0.1" class="animate-pulse"/>
                                <circle cx="100" cy="100" r="60" fill="white" opacity="0.2" class="animate-pulse" style="animation-delay: 0.5s"/>
                                <circle cx="100" cy="100" r="40" fill="white" opacity="0.3" class="animate-pulse" style="animation-delay: 1s"/>
                                <path d="M100 60 L100 100 L130 85" stroke="white" stroke-width="4" stroke-linecap="round" opacity="0.8"/>
                                <circle cx="100" cy="100" r="8" fill="white"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid with Animations -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 200)">
                
                <!-- Active Visitors -->
                <div class="group relative" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" style="transition: all 0.6s ease-out">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl transform group-hover:scale-105 transition-transform duration-300 opacity-75 blur-lg"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform group-hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-600 p-3 rounded-xl shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-green-100 text-green-800 text-xs font-semibold">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Live
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Active Visitors</p>
                            <p class="text-5xl font-extrabold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent" x-data="{ count: 0, target: {{ $stats['active_visitors'] }} }" x-init="let interval = setInterval(() => { if(count < target) { count++; } else { clearInterval(interval); } }, 50)">
                                <span x-text="count"></span>
                            </p>
                            <p class="text-sm text-gray-500 mt-2">{{ $stats['total_visitors_today'] }} checked in today</p>
                        </div>
                    </div>
                </div>

                <!-- Parked Vehicles -->
                <div class="group relative" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" style="transition: all 0.6s ease-out 0.1s">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl transform group-hover:scale-105 transition-transform duration-300 opacity-75 blur-lg"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform group-hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-xl shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Parked Vehicles</p>
                            <p class="text-5xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent" x-data="{ count: 0, target: {{ $stats['active_vehicles'] }} }" x-init="let interval = setInterval(() => { if(count < target) { count++; } else { clearInterval(interval); } }, 50)">
                                <span x-text="count"></span>
                            </p>
                            <a href="{{ route('admin.vehicles.index') }}" class="text-sm text-green-600 font-semibold mt-2 inline-flex items-center hover:underline">
                                View all vehicles
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Available Assets -->
                <div class="group relative" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" style="transition: all 0.6s ease-out 0.2s">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl transform group-hover:scale-105 transition-transform duration-300 opacity-75 blur-lg"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform group-hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-3 rounded-xl shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Checked Out</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['checked_out_assets'] }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Available Assets</p>
                            <p class="text-5xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" x-data="{ count: 0, target: {{ $stats['available_assets'] }} }" x-init="let interval = setInterval(() => { if(count < target) { count++; } else { clearInterval(interval); } }, 50)">
                                <span x-text="count"></span>
                            </p>
                            <p class="text-sm text-gray-500 mt-2">Ready for checkout</p>
                        </div>
                    </div>
                </div>

                <!-- Overdue Assets -->
                <div class="group relative" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" style="transition: all 0.6s ease-out 0.3s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl transform group-hover:scale-105 transition-transform duration-300 opacity-75 blur-lg"></div>
                    <div class="relative bg-white rounded-2xl shadow-xl p-6 border border-gray-100 transform group-hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-red-500 to-orange-600 p-3 rounded-xl shadow-lg transform group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            @if($stats['overdue_assets'] > 0)
                                <span class="flex h-4 w-4">
                                    <span class="animate-ping absolute inline-flex h-4 w-4 rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Overdue Assets</p>
                            <p class="text-5xl font-extrabold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent" x-data="{ count: 0, target: {{ $stats['overdue_assets'] }} }" x-init="let interval = setInterval(() => { if(count < target) { count++; } else { clearInterval(interval); } }, 50)">
                                <span x-text="count"></span>
                            </p>
                            @if($stats['overdue_assets'] > 0)
                                <p class="text-sm text-red-600 font-semibold mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Requires attention
                                </p>
                            @else
                                <p class="text-sm text-green-600 font-semibold mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    All on track
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Recent Check-ins - Takes 2 columns -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Recent Check-ins</h3>
                        </div>
                        <a href="{{ route('admin.visitors.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center group">
                            View all
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($recentVisitors->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentVisitors as $visit)
                                    <div class="group flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 cursor-pointer border border-transparent hover:border-blue-200">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                @if($visit->visitor->photo_path)
                                                    <img src="{{ Storage::url($visit->visitor->photo_path) }}" alt="{{ $visit->visitor->full_name }}" class="w-14 h-14 rounded-full object-cover ring-4 ring-white shadow-lg">
                                                @else
                                                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-white">
                                                        {{ substr($visit->visitor->full_name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $visit->visitor->full_name }}</p>
                                                <p class="text-xs text-gray-600">Host: <span class="font-semibold">{{ $visit->host->name }}</span></p>
                                                <p class="text-xs text-gray-500 flex items-center mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $visit->check_in_time->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                                @if($visit->status === 'active') bg-green-100 text-green-800 ring-2 ring-green-200
                                                @elseif($visit->status === 'completed') bg-gray-100 text-gray-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                @if($visit->status === 'active')
                                                    <span class="inline-block w-2 h-2 bg-green-600 rounded-full mr-1 animate-pulse"></span>
                                                @endif
                                                {{ ucfirst($visit->status) }}
                                            </span>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="mt-4 text-sm text-gray-500 font-medium">No recent visitors</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('admin.visitors.create') }}" class="group block p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-200 border-2 border-blue-200 hover:border-blue-400 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-blue-500 p-2 rounded-lg mr-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">Check-in Visitor</p>
                                        <p class="text-xs text-gray-600">Register new visitor</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-blue-600 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('admin.vehicles.create') }}" class="group block p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl hover:from-green-100 hover:to-emerald-100 transition-all duration-200 border-2 border-green-200 hover:border-green-400 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-green-500 p-2 rounded-lg mr-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">Register Vehicle</p>
                                        <p class="text-xs text-gray-600">Add new vehicle</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-green-600 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('admin.assets.index') }}" class="group block p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all duration-200 border-2 border-purple-200 hover:border-purple-400 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-purple-500 p-2 rounded-lg mr-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">Manage Assets</p>
                                        <p class="text-xs text-gray-600">Checkout/return</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-purple-600 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('admin.reports.index') }}" class="group block p-4 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl hover:from-orange-100 hover:to-yellow-100 transition-all duration-200 border-2 border-orange-200 hover:border-orange-400 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-orange-500 p-2 rounded-lg mr-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">View Reports</p>
                                        <p class="text-xs text-gray-600">Analytics & stats</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-orange-600 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Overdue Assets Alert (if any) -->
            @if($overdueCheckouts->count() > 0)
                <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl shadow-xl border-l-4 border-red-500 overflow-hidden mb-8">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-red-500 p-3 rounded-xl animate-pulse">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-red-900">⚠️ Overdue Asset Returns</h3>
                                <p class="text-sm text-red-700">{{ $overdueCheckouts->count() }} asset(s) are past their expected return time</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @foreach($overdueCheckouts as $checkout)
                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border-2 border-red-200 shadow-sm hover:shadow-md transition-all">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">{{ $checkout->asset->name }}</p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Checked out to: 
                                            <span class="font-semibold">
                                                @if($checkout->user)
                                                    {{ $checkout->user->name }}
                                                @elseif($checkout->visitorVisit)
                                                    {{ $checkout->visitorVisit->visitor->full_name }}
                                                @endif
                                            </span>
                                        </p>
                                        <p class="text-xs text-red-600 font-semibold mt-1 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Overdue by {{ $checkout->expected_return_time->diffForHumans(null, true) }}
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.assets.show', $checkout->asset) }}" class="ml-4 px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                                        Follow Up
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
