<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Asset Reports') }}
            </h2>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Total Checkouts</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_checkouts'] }}</p>
                            </div>
                            <div class="bg-purple-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Checked Out</p>
                                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['currently_checked_out'] }}</p>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Overdue</p>
                                <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['overdue_checkouts'] }}</p>
                            </div>
                            <div class="bg-red-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 uppercase">Available</p>
                                <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['available_assets'] }}</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.reports.assets') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Statuses</option>
                                <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                                <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Apply
                            </button>
                            <a href="{{ route('admin.reports.assets') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Asset Checkouts Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Asset Checkout Details</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checked Out To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checkout Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expected Return</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actual Return</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($checkouts as $checkout)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $checkout->asset->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $checkout->asset->asset_code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($checkout->user)
                                                {{ $checkout->user->name }}
                                                <span class="text-xs text-gray-500">(Staff)</span>
                                            @elseif($checkout->visitorVisit)
                                                {{ $checkout->visitorVisit->visitor->full_name }}
                                                <span class="text-xs text-gray-500">(Visitor)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $checkout->checkout_time->format('M d, h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $checkout->expected_return_time->format('M d, h:i A') }}
                                        @if($checkout->status === 'checked_out' && $checkout->expected_return_time < now())
                                            <br><span class="text-xs text-red-600 font-semibold">⚠️ Overdue</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $checkout->actual_return_time ? $checkout->actual_return_time->format('M d, h:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>Out: {{ ucfirst($checkout->checkout_condition) }}</div>
                                        @if($checkout->return_condition)
                                            <div>In: {{ ucfirst($checkout->return_condition) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($checkout->status === 'checked_out') bg-yellow-100 text-yellow-800
                                            @elseif($checkout->status === 'returned') bg-green-100 text-green-800
                                            @elseif($checkout->status === 'pending_approval') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $checkout->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                        No asset checkout data found for the selected filters
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $checkouts->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
