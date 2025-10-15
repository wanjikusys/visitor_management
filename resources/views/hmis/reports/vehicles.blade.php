<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Vehicles Movement Report — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Filters Section -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 shadow-lg ring-1 ring-blue-900/10 rounded-xl p-6">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Filter Report</h3>
            </div>
            
            <form method="GET" action="{{ route('admin.hmis.reports.vehicles') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Start Date
                        </label>
                        <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            End Date
                        </label>
                        <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status
                        </label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Checked In</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Checked Out</option>
                        </select>
                    </div>
                    <div class="flex flex-col justify-end">
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Apply
                            </button>
                            @if(request()->anyFilled(['start_date', 'end_date', 'status']))
                                <a href="{{ route('admin.hmis.reports.vehicles') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Cards - FIXED HORIZONTAL LAYOUT -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Total Vehicles -->
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-gray-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</div>
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Vehicles</div>
                </div>
            </div>
            
            <!-- Checked In -->
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-green-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked In</div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Active Now</div>
                </div>
            </div>
            
            <!-- Checked Out -->
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-blue-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked Out</div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['completed'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Completed</div>
                </div>
            </div>
            
            <!-- Total Passengers -->
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-purple-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Passengers</div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total_passengers'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Total Count</div>
                </div>
            </div>
            
            <!-- Average Duration -->
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-orange-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Time</div>
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['avg_duration'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Minutes</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.hmis.reports.vehicles', array_merge(request()->all(), ['download' => 'excel'])) }}" 
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-md font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Excel
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition shadow-md font-medium no-print">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Vehicle Movement Records</h3>
                    <span class="text-xs text-gray-500">{{ $vehicles->count() }} records</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Card / Driver</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Registration</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Purpose</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Passengers</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time In</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time Out</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($vehicles as $vehicle)
                            <tr class="hover:bg-blue-50 transition duration-100">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 truncate">{{ $vehicle->driver_name }}</div>
                                            <div class="text-xs text-gray-500 truncate">Card: {{ $vehicle->card_no }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-md whitespace-nowrap">
                                        {{ $vehicle->registration }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-mono text-xs">{{ $vehicle->phone_number }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="max-w-xs truncate" title="{{ $vehicle->visit_purpose }}">
                                        {{ Str::limit($vehicle->visit_purpose, 30) }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full whitespace-nowrap">
                                        {{ $vehicle->passengers }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($vehicle->time_in)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($vehicle->time_in)->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($vehicle->time_out)
                                        <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($vehicle->time_out)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($vehicle->time_out)->format('h:i A') }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5 animate-pulse"></span>
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-purple-100 text-purple-800 rounded-md">
                                        {{ round($vehicle->duration_minutes) }}m
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">No vehicles found for selected filters</p>
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your filter criteria</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($vehicles->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-900">{{ $vehicles->count() }}</span> records
                        @if(request('start_date') || request('end_date'))
                            from <span class="font-semibold text-gray-900">{{ request('start_date', 'beginning') }}</span> 
                            to <span class="font-semibold text-gray-900">{{ request('end_date', 'today') }}</span>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .shadow-lg { box-shadow: none !important; }
        }
    </style>
</x-app-layout>
