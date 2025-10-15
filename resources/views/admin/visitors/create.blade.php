<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Check-in Visitor') }}
            </h2>
            <a href="{{ route('admin.visitors.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Visitors
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.visitors.store') }}" enctype="multipart/form-data" x-data="{ hasVehicle: false }">
                @csrf

                <!-- Visitor Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Visitor Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">ID Number *</label>
                                <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('id_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                                <input type="text" name="company" id="company" value="{{ old('company') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="id_type" class="block text-sm font-medium text-gray-700 mb-2">ID Type *</label>
                                <select name="id_type" id="id_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="national_id">National ID</option>
                                    <option value="passport">Passport</option>
                                    <option value="driving_license">Driving License</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('id_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                            <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Visit Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Visit Details</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div>
                            <label for="host_id" class="block text-sm font-medium text-gray-700 mb-2">Host (Person to Visit) *</label>
                            <select name="host_id" id="host_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Host</option>
                                @foreach($hosts as $host)
                                    <option value="{{ $host->id }}">{{ $host->name }} ({{ $host->role }})</option>
                                @endforeach
                            </select>
                            @error('host_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="visit_purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit *</label>
                            <input type="text" name="visit_purpose" id="visit_purpose" value="{{ old('visit_purpose') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('visit_purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="visit_notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea name="visit_notes" id="visit_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('visit_notes') }}</textarea>
                            @error('visit_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="expected_checkout_time" class="block text-sm font-medium text-gray-700 mb-2">Expected Checkout Time</label>
                                <input type="datetime-local" name="expected_checkout_time" id="expected_checkout_time" value="{{ old('expected_checkout_time') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (°C)</label>
                                <input type="number" step="0.1" name="temperature" id="temperature" value="{{ old('temperature') }}" placeholder="36.5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Vehicle Information (Optional)</h3>
                            <label class="flex items-center">
                                <input type="checkbox" name="has_vehicle" value="1" x-model="hasVehicle" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Visitor has vehicle</span>
                            </label>
                        </div>
                    </div>
                    <div x-show="hasVehicle" x-collapse class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                                <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                                @error('plate_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                                <select name="vehicle_type" id="vehicle_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="car">Car</option>
                                    <option value="motorcycle">Motorcycle</option>
                                    <option value="truck">Truck</option>
                                    <option value="van">Van</option>
                                    <option value="suv">SUV</option>
                                    <option value="bus">Bus</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="vehicle_make" class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                                <input type="text" name="vehicle_make" id="vehicle_make" value="{{ old('vehicle_make') }}" placeholder="Toyota" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                                <input type="text" name="vehicle_model" id="vehicle_model" value="{{ old('vehicle_model') }}" placeholder="Camry" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="vehicle_color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                <input type="text" name="vehicle_color" id="vehicle_color" value="{{ old('vehicle_color') }}" placeholder="Black" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="parking_zone_id" class="block text-sm font-medium text-gray-700 mb-2">Parking Zone</label>
                                <select name="parking_zone_id" id="parking_zone_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Parking Zone</option>
                                    @foreach($parkingZones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $zone->available_slots }}/{{ $zone->total_slots }} available)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="parking_slot" class="block text-sm font-medium text-gray-700 mb-2">Parking Slot Number</label>
                                <input type="text" name="parking_slot" id="parking_slot" value="{{ old('parking_slot') }}" placeholder="A-12" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.visitors.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Check-in Visitor
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
