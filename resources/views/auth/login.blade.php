<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Staff Login - {{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            @keyframes pulse-slow {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            .animate-float { animation: float 6s ease-in-out infinite; }
            .animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
        </style>
    </head>
    <body class="font-sans antialiased">
        
        <div class="min-h-screen flex">
            
            <!-- Left Side - Login Form -->
            <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white relative overflow-hidden">
                
                <!-- Decorative Elements -->
                <div class="absolute top-0 left-0 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse-slow"></div>
                <div class="absolute bottom-0 right-0 w-64 h-64 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse-slow" style="animation-delay: 2s"></div>
                
                <div class="max-w-md w-full space-y-8 relative z-10">
                    
                    <!-- Back to Home Link -->
                    <div class="absolute -top-20 left-0">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Home
                        </a>
                    </div>

                    <!-- Logo & Header -->
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-6">
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-4 rounded-2xl shadow-2xl transform hover:rotate-12 transition-transform duration-300">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-4xl font-extrabold text-gray-900 mb-2">
                            Welcome Back!
                        </h2>
                        <p class="text-gray-600 text-lg">
                            Staff Portal - Secure Login
                        </p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6" x-data="{ showPassword: false }">
                        @csrf

                        <div class="space-y-5">
                            
                            <!-- Email -->
                            <div class="group">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                    <input id="email" 
                                           name="email" 
                                           type="email" 
                                           value="{{ old('email') }}"
                                           required 
                                           autofocus 
                                           autocomplete="username"
                                           class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200 hover:border-blue-300"
                                           placeholder="john@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="group">
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input id="password" 
                                           name="password" 
                                           :type="showPassword ? 'text' : 'password'"
                                           required 
                                           autocomplete="current-password"
                                           class="block w-full pl-12 pr-12 py-4 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all duration-200 hover:border-blue-300"
                                           placeholder="••••••••">
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" 
                                       name="remember" 
                                       type="checkbox"
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer hover:text-gray-900">
                                    Remember me
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                                <svg class="h-6 w-6 text-blue-300 group-hover:text-blue-200 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </span>
                            Sign In
                        </button>

                    </form>

                    <!-- Security Badge -->
                    <div class="mt-6 flex items-center justify-center space-x-2 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span class="text-sm">Secure Login Protected</span>
                    </div>

                </div>
            </div>

            <!-- Right Side - Illustration/Info -->
            <div class="hidden lg:flex flex-1 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 relative overflow-hidden">
                
                <!-- Animated Background Circles -->
                <div class="absolute inset-0">
                    <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-float"></div>
                    <div class="absolute top-40 right-20 w-96 h-96 bg-pink-300 rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s"></div>
                    <div class="absolute bottom-20 left-40 w-80 h-80 bg-yellow-300 rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s"></div>
                </div>

                <div class="relative z-10 flex flex-col justify-center items-center px-12 text-white">
                    
                    <!-- Main Illustration -->
                    <div class="mb-8 transform hover:scale-110 transition-transform duration-500">
                        <svg class="w-64 h-64" viewBox="0 0 200 200" fill="none">
                            <circle cx="100" cy="100" r="90" fill="white" opacity="0.1"/>
                            <circle cx="100" cy="100" r="70" fill="white" opacity="0.2"/>
                            <path d="M100 50 L100 100 L130 85" stroke="white" stroke-width="4" stroke-linecap="round" opacity="0.8"/>
                            <circle cx="100" cy="100" r="10" fill="white"/>
                        </svg>
                    </div>

                    <h2 class="text-5xl font-bold mb-6 text-center leading-tight">
                        Visitor Management
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-pink-300">
                            Made Simple
                        </span>
                    </h2>
                    
                    <p class="text-xl text-blue-100 text-center mb-12 max-w-md">
                        Secure, efficient, and modern visitor tracking for your organization
                    </p>

                    <!-- Feature List -->
                    <div class="space-y-4 w-full max-w-md">
                        <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold">Real-time Tracking</h4>
                                <p class="text-sm text-blue-200">Monitor all visitors in real-time</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold">Comprehensive Reports</h4>
                                <p class="text-sm text-blue-200">Generate detailed analytics</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold">Secure Access Control</h4>
                                <p class="text-sm text-blue-200">Enterprise-grade security</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </body>
</html>
