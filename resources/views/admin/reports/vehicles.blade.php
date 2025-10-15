<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Vehicle Reports') }}
            </h2>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Total Vehicles</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_vehicles'] }}</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Currently Parked</p>
                                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['currently_parked'] }}</p>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Avg Parking Time</p>
                                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['average_parking_time'] }} min</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.reports.vehicles') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Apply
                            </button>
                            <a href="{{ route('admin.reports.vehicles') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vehicle Visits Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Vehicle Visit Details</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plate Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parking Zone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exit Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($vehicleVisits as $visit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 font-mono">{{ $visit->vehicle->plate_number }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->vehicle->make }} {{ $visit->vehicle->model }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $visit->visitorVisit->visitor->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $visit->visitorVisit->visitor->phone_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->parkingZone->name ?? 'N/A' }}
                                        @if($visit->parking_slot)
                                            <br><span class="text-xs text-gray-400">Slot: {{ $visit->parking_slot }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->entry_time->format('M d, h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->exit_time ? $visit->exit_time->format('M d, h:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($visit->duration_minutes)
                                            {{ $visit->duration_minutes }} min
                                        @else
                                            {{ $visit->entry_time->diffInMinutes(now()) }} min
                                        @endif
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
                                    <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                        No vehicle data found for the selected filters
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $vehicleVisits->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
