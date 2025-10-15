<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Vehicle Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Edit Vehicle
                </a>
                <a href="{{ route('admin.vehicles.index') }}" class="text-gray-600 hover:text-gray-900">
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

            <!-- Vehicle Header -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-gray-800 to-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            @if($vehicle->photo_path)
                                <img src="{{ Storage::url($vehicle->photo_path) }}" alt="{{ $vehicle->plate_number }}" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-lg bg-gray-700 flex items-center justify-center border-4 border-white shadow-lg">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="text-white">
                                <h3 class="text-3xl font-bold font-mono">{{ $vehicle->plate_number }}</h3>
                                <p class="text-xl text-gray-300">{{ $vehicle->make }} {{ $vehicle->model }}</p>
                                <div class="mt-2 space-x-2">
                                    @if($currentVisit)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-500 text-white">
                                            <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
                                            Currently Parked
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-500 text-white rounded-full text-sm font-semibold">
                                            Not Parked
                                        </span>
                                    @endif
                                    @if($vehicle->is_blacklisted)
                                        <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">
                                            ⚠️ Blacklisted
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Vehicle Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Vehicle Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Plate Number</dt>
                                <dd class="mt-1 text-lg font-bold text-gray-900 font-mono">{{ $vehicle->plate_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Make</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->make ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Model</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->model ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Color</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->color ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Year</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->year ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($vehicle->vehicle_type) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Current Visit -->
                @if($currentVisit)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Current Visit</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Visitor</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $currentVisit->visitorVisit->visitor->full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Entry Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $currentVisit->entry_time->format('M d, Y h:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Parking Zone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $currentVisit->parkingZone->name ?? 'N/A' }}</dd>
                            </div>
                            @if($currentVisit->parking_slot)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Parking Slot</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $currentVisit->parking_slot }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                @endif

            </div>

            <!-- Visit History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Visit History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exit Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parking Zone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($visitHistory as $visit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $visit->visitorVisit->visitor->full_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->entry_time->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->exit_time ? $visit->exit_time->format('M d, Y h:i A') : 'Still parked' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->parkingZone->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $visit->status === 'parked' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($visit->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No visit history
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $visitHistory->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
