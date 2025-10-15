<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Checkout Asset') }}
            </h2>
            <a href="{{ route('admin.assets.show', $asset) }}" class="text-gray-600 hover:text-gray-900">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Asset Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        @if($asset->photo_path)
                            <img src="{{ Storage::url($asset->photo_path) }}" alt="{{ $asset->name }}" class="w-20 h-20 rounded-lg object-cover">
                        @else
                            <div class="w-20 h-20 rounded-lg bg-purple-500 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $asset->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $asset->asset_code }} - {{ $asset->category->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.assets.checkout.store', $asset) }}" x-data="{ checkoutType: 'staff' }">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Checkout Details</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <!-- Checkout Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Checkout To *</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="checkout_type" value="staff" x-model="checkoutType" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                    <span class="ml-2 text-sm text-gray-700">Staff Member</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="checkout_type" value="visitor" x-model="checkoutType" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Visitor</span>
                                </label>
                            </div>
                        </div>

                        <!-- Staff Selection -->
                        <div x-show="checkoutType === 'staff'">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Select Staff Member *</label>
                            <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Staff</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Visitor Selection -->
                        <div x-show="checkoutType === 'visitor'">
                            <label for="visitor_visit_id" class="block text-sm font-medium text-gray-700 mb-2">Select Active Visitor *</label>
                            <select name="visitor_visit_id" id="visitor_visit_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Visitor</option>
                                @foreach($activeVisits as $visit)
                                    <option value="{{ $visit->id }}">
                                        {{ $visit->visitor->full_name }} - Badge: {{ $visit->badge_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('visitor_visit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expected_return_time" class="block text-sm font-medium text-gray-700 mb-2">Expected Return Time *</label>
                            <input type="datetime-local" name="expected_return_time" id="expected_return_time" value="{{ old('expected_return_time') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('expected_return_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="checkout_condition" class="block text-sm font-medium text-gray-700 mb-2">Asset Condition *</label>
                            <select name="checkout_condition" id="checkout_condition" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="excellent">Excellent</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                            @error('checkout_condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="checkout_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="checkout_notes" id="checkout_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('checkout_notes') }}</textarea>
                            @error('checkout_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($asset->category->requires_approval)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <p class="text-sm text-yellow-800">
                                <strong>⚠️ Note:</strong> This asset requires approval before checkout. Your request will be sent to an administrator.
                            </p>
                        </div>
                        @endif

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.assets.show', $asset) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        {{ $asset->category->requires_approval ? 'Submit for Approval' : 'Checkout Asset' }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
