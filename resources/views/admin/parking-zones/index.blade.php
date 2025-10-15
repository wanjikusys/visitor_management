<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Parking Zones') }}
            </h2>
            <a href="{{ route('admin.parking-zones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Parking Zone
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Parking Zones Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($zones as $zone)
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow">
                        <div class="p-6 bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold">{{ $zone->code }}</h3>
                                    <p class="text-indigo-100">{{ $zone->name }}</p>
                                </div>
                                @if($zone->is_active)
                                    <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">Active</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-semibold">Inactive</span>
                                @endif
                            </div>
                            
                            <!-- Slot Availability -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span>Availability</span>
                                    <span class="font-bold">{{ $zone->available_slots }}/{{ $zone->total_slots }}</span>
                                </div>
                                <div class="w-full bg-indigo-700 rounded-full h-3 overflow-hidden">
                                    <div class="bg-white h-3 rounded-full transition-all duration-300" 
                                         style="width: {{ ($zone->available_slots / $zone->total_slots) * 100 }}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <span class="inline-flex items-center px-3 py-1 bg-indigo-700 rounded-full">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    {{ $zone->vehicle_visits_count ?? 0 }} Parked
                                </span>
                                <span class="text-xs capitalize">{{ str_replace('_', ' ', $zone->zone_type) }}</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            @if($zone->description)
                                <p class="text-sm text-gray-600 mb-4">{{ $zone->description }}</p>
                            @endif
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.parking-zones.show', $zone) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white text-center text-sm font-semibold rounded-md hover:bg-blue-700">
                                    View Details
                                </a>
                                <a href="{{ route('admin.parking-zones.edit', $zone) }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-300">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No parking zones found</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
