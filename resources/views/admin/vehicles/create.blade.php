<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Register Vehicle') }}
            </h2>
            <a href="{{ route('admin.vehicles.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Vehicles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Vehicle Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div>
                            <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number *</label>
                            <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                            @error('plate_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="make" class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                                <input type="text" name="make" id="make" value="{{ old('make') }}" placeholder="Toyota" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('make')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                                <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Camry" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('model')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                <input type="text" name="color" id="color" value="{{ old('color') }}" placeholder="Black" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                <input type="text" name="year" id="year" value="{{ old('year') }}" placeholder="2023" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type *</label>
                            <select name="vehicle_type" id="vehicle_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="car">Car</option>
                                <option value="motorcycle">Motorcycle</option>
                                <option value="truck">Truck</option>
                                <option value="van">Van</option>
                                <option value="suv">SUV</option>
                                <option value="bus">Bus</option>
                                <option value="bicycle">Bicycle</option>
                                <option value="other">Other</option>
                            </select>
                            @error('vehicle_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Photo</label>
                            <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.vehicles.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Register Vehicle
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
