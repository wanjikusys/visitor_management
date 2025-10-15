<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Visitor Registration - {{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-blue-500 to-indigo-700">
        <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Welcome!</h1>
                <p class="text-xl text-blue-100">Please register your visit</p>
            </div>

            <div class="max-w-2xl w-full">
                
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.visitor.store') }}" enctype="multipart/form-data" class="bg-white shadow-2xl rounded-lg p-8" x-data="{ hasVehicle: false }">
                    @csrf

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-2">Personal Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">ID Number *</label>
                                    <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" required class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                    @error('id_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="id_type" class="block text-sm font-medium text-gray-700 mb-2">ID Type *</label>
                                    <select name="id_type" id="id_type" required class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                        <option value="national_id">National ID</option>
                                        <option value="passport">Passport</option>
                                        <option value="driving_license">Driving License</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                    @error('phone_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                </div>
                            </div>

                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company/Organization</label>
                                <input type="text" name="company" id="company" value="{{ old('company') }}" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Visit Information -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-2">Visit Details</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="host_id" class="block text-sm font-medium text-gray-700 mb-2">Person to Visit *</label>
                                <select name="host_id" id="host_id" required class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                    <option value="">Select Person</option>
                                    @foreach($hosts as $host)
                                        <option value="{{ $host->id }}">{{ $host->name }}</option>
                                    @endforeach
                                </select>
                                @error('host_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="visit_purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit *</label>
                                <input type="text" name="visit_purpose" id="visit_purpose" value="{{ old('visit_purpose') }}" required class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                @error('visit_purpose')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                                <input type="number" step="0.1" name="temperature" id="temperature" value="{{ old('temperature') }}" placeholder="36.5" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4 border-b pb-2">
                            <h2 class="text-2xl font-bold text-gray-900">Vehicle Information</h2>
                            <label class="flex items-center">
                                <input type="checkbox" name="has_vehicle" value="1" x-model="hasVehicle" class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">I have a vehicle</span>
                            </label>
                        </div>

                        <div x-show="hasVehicle" x-collapse class="space-y-4">
                            <div>
                                <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                                <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number') }}" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg uppercase">
                            </div>

                            <div>
                                <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                                <select name="vehicle_type" id="vehicle_type" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                                    <option value="car">Car</option>
                                    <option value="motorcycle">Motorcycle</option>
                                    <option value="van">Van</option>
                                    <option value="suv">SUV</option>
                                    <option value="truck">Truck</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xl font-bold rounded-lg hover:from-blue-700 hover:to-indigo-800 shadow-lg transition-all transform hover:scale-105">
                        Register Visit
                    </button>

                </form>
            </div>

        </div>
    </body>
</html>
