<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Visitor Management System') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-2 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Visitor Management</h1>
                            <p class="text-xs text-gray-600">Professional Access Control</p>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-900 transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2 border-2 border-gray-800 text-gray-800 font-semibold rounded-lg hover:bg-gray-800 hover:text-white transition">
                                Staff Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
                <div class="absolute top-40 right-10 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s"></div>
                <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 4s"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    
                    <!-- Left Content -->
                    <div class="text-white">
                        <h2 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                            Welcome to Our
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-pink-300">
                                Visitor Center
                            </span>
                        </h2>
                        <p class="text-xl text-blue-100 mb-8">
                            Experience seamless visitor management with our state-of-the-art system. Quick registration, secure access, and professional service.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('public.visitor.register') }}" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl shadow-2xl hover:shadow-pink-500/50 transition-all transform hover:scale-105">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Register as Visitor
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-pink-600 to-rose-600 rounded-xl blur opacity-0 group-hover:opacity-50 transition-opacity"></span>
                            </a>

                            <a href="#how-it-works" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white border-2 border-white rounded-xl hover:bg-white hover:text-blue-600 transition">
                                Learn More
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right Content - Live Stats -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-green-500/20 p-3 rounded-xl">
                                    <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-white mb-1">{{ $stats['active_visitors'] }}</p>
                            <p class="text-blue-200 text-sm">Visitors Now</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-blue-500/20 p-3 rounded-xl">
                                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-white mb-1">{{ $stats['total_today'] }}</p>
                            <p class="text-blue-200 text-sm">Today's Total</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-yellow-500/20 p-3 rounded-xl">
                                    <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-white mb-1">{{ $stats['parked_vehicles'] }}</p>
                            <p class="text-blue-200 text-sm">Parked Vehicles</p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 shadow-xl">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-purple-500/20 p-3 rounded-xl">
                                    <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-white mb-1">100%</p>
                            <p class="text-blue-200 text-sm">Secure</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white" id="features">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose Our System?</h2>
                    <p class="text-xl text-gray-600">Modern, secure, and user-friendly visitor management</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Feature 1 -->
                    <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500 rounded-full opacity-10 -mr-16 -mt-16"></div>
                        <div class="relative">
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Quick Registration</h3>
                            <p class="text-gray-600">Register in under 2 minutes with our streamlined process. No appointments needed!</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative bg-gradient-to-br from-green-50 to-teal-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-green-500 rounded-full opacity-10 -mr-16 -mt-16"></div>
                        <div class="relative">
                            <div class="bg-gradient-to-br from-green-600 to-teal-700 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Secure Access</h3>
                            <p class="text-gray-600">State-of-the-art security with badge system and real-time monitoring.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500 rounded-full opacity-10 -mr-16 -mt-16"></div>
                        <div class="relative">
                            <div class="bg-gradient-to-br from-purple-600 to-pink-700 w-16 h-16 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Easy to Use</h3>
                            <p class="text-gray-600">Intuitive interface designed for everyone. No technical knowledge required.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100" id="how-it-works">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                    <p class="text-xl text-gray-600">Three simple steps to get started</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="relative text-center">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
                            <span class="text-3xl font-bold text-white">1</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Fill Information</h3>
                        <p class="text-gray-600">Enter your basic details and purpose of visit in our secure form.</p>
                    </div>

                    <div class="relative text-center">
                        <div class="bg-gradient-to-br from-green-600 to-teal-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
                            <span class="text-3xl font-bold text-white">2</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Get Badge</h3>
                        <p class="text-gray-600">Receive your unique visitor badge number instantly upon registration.</p>
                    </div>

                    <div class="relative text-center">
                        <div class="bg-gradient-to-br from-purple-600 to-pink-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
                            <span class="text-3xl font-bold text-white">3</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Enjoy Visit</h3>
                        <p class="text-gray-600">Proceed to your destination with your badge. We'll handle the rest!</p>
                    </div>

                </div>

                <!-- CTA -->
                <div class="text-center mt-16">
                    <a href="{{ route('public.visitor.register') }}" class="inline-flex items-center px-10 py-5 text-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-2xl hover:shadow-blue-500/50 transition-all transform hover:scale-105">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Start Your Registration Now
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4">Visitor Management System</h3>
                        <p class="text-gray-400">Professional access control and visitor tracking solution.</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('public.visitor.register') }}" class="text-gray-400 hover:text-white transition">Register</a></li>
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Staff Login</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-4">Contact</h3>
                        <p class="text-gray-400">For assistance, please contact our reception desk.</p>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} Visitor Management System. All rights reserved.</p>
                </div>
            </div>
        </footer>

    </body>
</html>
