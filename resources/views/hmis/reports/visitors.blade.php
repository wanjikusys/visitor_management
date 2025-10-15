<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Inpatient Visitors Report — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Filters Section -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 shadow-lg ring-1 ring-purple-900/10 rounded-xl p-6">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Filter Report</h3>
            </div>
            
            <form method="GET" action="{{ route('admin.hmis.reports.visitors') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ward</label>
                        <select name="ward" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
                            <option value="">All Wards</option>
                            @foreach($wards ?? [] as $ward)
                                <option value="{{ $ward }}" {{ request('ward') === $ward ? 'selected' : '' }}>{{ $ward }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="flex flex-col justify-end">
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-md font-medium">
                                Apply
                            </button>
                            @if(request()->anyFilled(['start_date', 'end_date', 'ward', 'status']))
                                <a href="{{ route('admin.hmis.reports.visitors') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-gray-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</div>
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Visitors</div>
                </div>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-green-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active</div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Currently In</div>
                </div>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-blue-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Completed</div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['completed'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Checked Out</div>
                </div>
            </div>
            
            <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-purple-400 hover:shadow-xl transition">
                <div class="flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Time</div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['avg_duration'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Minutes</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.hmis.reports.visitors', array_merge(request()->all(), ['download' => 'excel'])) }}" 
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
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Inpatient Visitor Records</h3>
                    <span class="text-xs text-gray-500">{{ $visitors->count() }} records</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Ward/Bed</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Visitor</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Relationship</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Check-In</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Check-Out</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($visitors as $visitor)
                            <tr class="hover:bg-purple-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $visitor->patient_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $visitor->patient_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">Ward: <span class="font-medium">{{ $visitor->ward_number }}</span></div>
                                    <div class="text-xs text-gray-500">Bed: {{ $visitor->bed_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $visitor->visitor_name }}</div>
                                    @if($visitor->visitor_id_number)
                                        <div class="text-xs text-gray-500">ID: {{ $visitor->visitor_id_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 font-mono text-xs">{{ $visitor->visitor_phone ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        {{ $visitor->relationship ?? 'Not specified' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($visitor->check_in_time)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($visitor->check_in_time)->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($visitor->check_out_time)
                                        <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($visitor->check_out_time)->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($visitor->check_out_time)->format('h:i A') }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5 animate-pulse"></span>
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-purple-100 text-purple-800 rounded-md">
                                        {{ round($visitor->duration_minutes) }}m
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">No visitors found for selected filters</p>
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your filter criteria</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($visitors->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-900">{{ $visitors->count() }}</span> visitor records
                    </p>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</x-app-layout>
