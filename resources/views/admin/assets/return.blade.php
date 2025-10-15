<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Return Asset') }}
            </h2>
            <a href="{{ route('admin.assets.show', $checkout->asset) }}" class="text-gray-600 hover:text-gray-900">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Asset & Checkout Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $checkout->asset->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $checkout->asset->asset_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Checked out to:</p>
                            <p class="text-sm font-semibold text-gray-900">
                                @if($checkout->user)
                                    {{ $checkout->user->name }}
                                @elseif($checkout->visitorVisit)
                                    {{ $checkout->visitorVisit->visitor->full_name }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                On {{ $checkout->checkout_time->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.assets.checkout.return', $checkout) }}">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Return Details</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div>
                            <label for="return_condition" class="block text-sm font-medium text-gray-700 mb-2">Asset Condition on Return *</label>
                            <select name="return_condition" id="return_condition" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="excellent">Excellent - Like new</option>
                                <option value="good" selected>Good - Normal wear</option>
                                <option value="fair">Fair - Noticeable wear</option>
                                <option value="poor">Poor - Needs attention</option>
                                <option value="damaged">Damaged - Requires repair</option>
                            </select>
                            @error('return_condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="return_notes" class="block text-sm font-medium text-gray-700 mb-2">Return Notes</label>
                            <textarea name="return_notes" id="return_notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Any damage, missing parts, or other observations...">{{ old('return_notes') }}</textarea>
                            @error('return_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Checkout Info -->
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Checkout Information</h4>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-gray-500">Checkout Condition:</dt>
                                    <dd class="font-medium text-gray-900">{{ ucfirst($checkout->checkout_condition) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Expected Return:</dt>
                                    <dd class="font-medium text-gray-900">{{ $checkout->expected_return_time->format('M d, Y h:i A') }}</dd>
                                </div>
                                @if($checkout->checkout_notes)
                                <div class="col-span-2">
                                    <dt class="text-gray-500">Checkout Notes:</dt>
                                    <dd class="font-medium text-gray-900">{{ $checkout->checkout_notes }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.assets.show', $checkout->asset) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Process Return
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
