<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Vehicle') }}
            </h2>
            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="text-gray-600 hover:text-gray-900">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Vehicle Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div>
                            <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Plate Number *</label>
                            <input type="text" name="plate_number" id="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                            @error('plate_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="make" class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                                <input type="text" name="make" id="make" value="{{ old('make', $vehicle->make) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                                <input type="text" name="model" id="model" value="{{ old('model', $vehicle->model) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                <input type="text" name="color" id="color" value="{{ old('color', $vehicle->color) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                <input type="text" name="year" id="year" value="{{ old('year', $vehicle->year) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type *</label>
                            <select name="vehicle_type" id="vehicle_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="car" {{ $vehicle->vehicle_type === 'car' ? 'selected' : '' }}>Car</option>
                                <option value="motorcycle" {{ $vehicle->vehicle_type === 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                <option value="truck" {{ $vehicle->vehicle_type === 'truck' ? 'selected' : '' }}>Truck</option>
                                <option value="van" {{ $vehicle->vehicle_type === 'van' ? 'selected' : '' }}>Van</option>
                                <option value="suv" {{ $vehicle->vehicle_type === 'suv' ? 'selected' : '' }}>SUV</option>
                                <option value="bus" {{ $vehicle->vehicle_type === 'bus' ? 'selected' : '' }}>Bus</option>
                                <option value="bicycle" {{ $vehicle->vehicle_type === 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                                <option value="other" {{ $vehicle->vehicle_type === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        @if($vehicle->photo_path)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo</label>
                            <img src="{{ Storage::url($vehicle->photo_path) }}" alt="{{ $vehicle->plate_number }}" class="w-48 h-32 object-cover rounded-lg">
                        </div>
                        @endif

                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Update Photo</label>
                            <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete Vehicle
                        </button>
                    </form>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Vehicle
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
