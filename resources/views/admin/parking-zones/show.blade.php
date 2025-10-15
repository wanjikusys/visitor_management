<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Parking Zone Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.parking-zones.edit', $parkingZone) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Edit Zone
                </a>
                <a href="{{ route('admin.parking-zones.index') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Zone Header -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-4xl font-bold mb-2">{{ $parkingZone->code }}</h3>
                            <p class="text-2xl text-indigo-100 mb-4">{{ $parkingZone->name }}</p>
                            <p class="text-indigo-200">{{ $parkingZone->description }}</p>
                        </div>
                        <div class="text-right">
                            @if($parkingZone->is_active)
                                <span class="px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold">Active</span>
                            @else
                                <span class="px-4 py-2 bg-gray-500 text-white rounded-full text-sm font-semibold">Inactive</span>
                            @endif
                            <div class="mt-4">
                                <span class="block text-sm text-indigo-200">Zone Type</span>
                                <span class="text-xl font-bold capitalize">{{ str_replace('_', ' ', $parkingZone->zone_type) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Total Slots -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Total Slots</p>
                                <p class="text-4xl font-bold text-gray-900 mt-2">{{ $parkingZone->total_slots }}</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Slots -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Available</p>
                                <p class="text-4xl font-bold text-green-600 mt-2">{{ $parkingZone->available_slots }}</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Occupied Slots -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Occupied</p>
                                <p class="text-4xl font-bold text-yellow-600 mt-2">{{ $parkingZone->total_slots - $parkingZone->available_slots }}</p>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Occupancy Progress -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Occupancy Rate</h3>
                    <div class="relative">
                        <div class="w-full bg-gray-200 rounded-full h-8 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-blue-600 h-8 rounded-full transition-all duration-500 flex items-center justify-center text-white text-sm font-bold" 
                                 style="width: {{ (($parkingZone->total_slots - $parkingZone->available_slots) / $parkingZone->total_slots) * 100 }}%">
                                {{ number_format((($parkingZone->total_slots - $parkingZone->available_slots) / $parkingZone->total_slots) * 100, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currently Parked Vehicles -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Currently Parked Vehicles</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plate Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($currentVehicles as $vehicleVisit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 font-mono">{{ $vehicleVisit->vehicle->plate_number }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $vehicleVisit->vehicle->make }} {{ $vehicleVisit->vehicle->model }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $vehicleVisit->visitorVisit->visitor->full_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $vehicleVisit->parking_slot ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $vehicleVisit->entry_time->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $vehicleVisit->entry_time->diffForHumans(null, true) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No vehicles currently parked in this zone</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
