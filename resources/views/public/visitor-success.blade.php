<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Check-in Successful - {{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-green-500 to-teal-700">
        <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4">
            
            <div class="max-w-2xl w-full bg-white shadow-2xl rounded-lg p-12 text-center">
                
                <!-- Success Icon -->
                <div class="mb-8">
                    <div class="mx-auto w-24 h-24 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome!</h1>
                <p class="text-xl text-gray-600 mb-8">You have been successfully checked in</p>

                <!-- Badge Information -->
                <div class="bg-blue-50 rounded-lg p-8 mb-8">
                    <p class="text-sm text-gray-600 mb-2">Your Badge Number</p>
                    <p class="text-5xl font-bold text-blue-600 mb-4">{{ $visit->badge_number }}</p>
                    <p class="text-sm text-gray-600">Please keep this number with you</p>
                </div>

                <!-- Visit Details -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Visit Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Visitor:</span>
                            <span class="font-semibold text-gray-900">{{ $visit->visitor->full_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Host:</span>
                            <span class="font-semibold text-gray-900">{{ $visit->host->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Purpose:</span>
                            <span class="font-semibold text-gray-900">{{ $visit->visit_purpose }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Check-in Time:</span>
                            <span class="font-semibold text-gray-900">{{ $visit->check_in_time->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <p class="text-gray-600 mb-8">
                    Thank you for registering. Please proceed to the designated area.
                </p>

                <a href="{{ route('public.visitor.register') }}" class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Register Another Visitor
                </a>

            </div>

        </div>
    </body>
</html>
