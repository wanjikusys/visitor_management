<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Add Parking Zone') }}
            </h2>
            <a href="{{ route('admin.parking-zones.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Parking Zones
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <form method="POST" action="{{ route('admin.parking-zones.store') }}">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Parking Zone Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Zone Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Visitor Parking A" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Zone Code *</label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="VPA" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="total_slots" class="block text-sm font-medium text-gray-700 mb-2">Total Parking Slots *</label>
                                <input type="number" name="total_slots" id="total_slots" value="{{ old('total_slots') }}" required min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('total_slots')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="zone_type" class="block text-sm font-medium text-gray-700 mb-2">Zone Type *</label>
                                <select name="zone_type" id="zone_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="visitor">Visitor Parking</option>
                                    <option value="vip">VIP Parking</option>
                                    <option value="staff">Staff Parking</option>
                                    <option value="loading">Loading Zone</option>
                                    <option value="disabled">Disabled Parking</option>
                                    <option value="motorcycle">Motorcycle Parking</option>
                                </select>
                                @error('zone_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-800">
                                <strong>ℹ️ Note:</strong> Available slots will be automatically set to match total slots. This will be updated as vehicles are parked and removed.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.parking-zones.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Parking Zone
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
