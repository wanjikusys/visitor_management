<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Asset Details') }}
            </h2>
            <div class="flex space-x-2">
                @if($asset->status === 'available')
                    <a href="{{ route('admin.assets.checkout.create', $asset) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Checkout Asset
                    </a>
                @endif
                <a href="{{ route('admin.assets.edit', $asset) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('admin.assets.index') }}" class="text-gray-600 hover:text-gray-900">
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

            <!-- Asset Header -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-purple-600 to-pink-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            @if($asset->photo_path)
                                <img src="{{ Storage::url($asset->photo_path) }}" alt="{{ $asset->name }}" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-lg bg-purple-700 flex items-center justify-center border-4 border-white shadow-lg">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="text-white">
                                <p class="text-sm text-purple-200">{{ $asset->asset_code }}</p>
                                <h3 class="text-3xl font-bold">{{ $asset->name }}</h3>
                                <p class="text-lg text-purple-200">{{ $asset->category->name }}</p>
                                <div class="mt-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        @if($asset->status === 'available') bg-green-500 text-white
                                        @elseif($asset->status === 'checked_out') bg-yellow-500 text-white
                                        @elseif($asset->status === 'maintenance') bg-blue-500 text-white
                                        @else bg-gray-500 text-white
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Asset Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Asset Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Asset Code</dt>
                                <dd class="mt-1 text-lg font-bold text-gray-900">{{ $asset->asset_code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->serial_number ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->location ?? 'N/A' }}</dd>
                            </div>
                            @if($asset->purchase_price)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purchase Price</dt>
                                <dd class="mt-1 text-sm text-gray-900">${{ number_format($asset->purchase_price, 2) }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Current Checkout -->
                @if($asset->currentCheckout)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Current Checkout</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Checked Out To</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">
                                    @if($asset->currentCheckout->user)
                                        {{ $asset->currentCheckout->user->name }}
                                    @elseif($asset->currentCheckout->visitorVisit)
                                        {{ $asset->currentCheckout->visitorVisit->visitor->full_name }}
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Checkout Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->currentCheckout->checkout_time->format('M d, Y h:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Expected Return</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->currentCheckout->expected_return_time->format('M d, Y h:i A') }}</dd>
                            </div>
                            <div>
                                <a href="{{ route('admin.assets.checkout.return-form', $asset->currentCheckout) }}" class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-md hover:bg-green-700">
                                    Return Asset
                                </a>
                            </div>
                        </dl>
                    </div>
                </div>
                @endif

            </div>

            <!-- Checkout History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Checkout History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checked Out To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checkout Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($checkoutHistory as $checkout)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @if($checkout->user)
                                            {{ $checkout->user->name }}
                                        @elseif($checkout->visitorVisit)
                                            {{ $checkout->visitorVisit->visitor->full_name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $checkout->checkout_time->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $checkout->actual_return_time ? $checkout->actual_return_time->format('M d, Y h:i A') : 'Not returned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($checkout->checkout_condition) }}
                                        @if($checkout->return_condition)
                                            → {{ ucfirst($checkout->return_condition) }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($checkout->status === 'checked_out') bg-yellow-100 text-yellow-800
                                            @elseif($checkout->status === 'returned') bg-green-100 text-green-800
                                            @elseif($checkout->status === 'overdue') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $checkout->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No checkout history
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $checkoutHistory->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
