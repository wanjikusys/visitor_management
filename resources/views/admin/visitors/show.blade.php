<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Visitor Details') }}
            </h2>
            <a href="{{ route('admin.visitors.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Visitors
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Visitor Info Card -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700">
                    <div class="flex items-center space-x-6">
                        @if($visit->visitor->photo_path)
                            <img src="{{ Storage::url($visit->visitor->photo_path) }}" alt="{{ $visit->visitor->full_name }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-3xl shadow-lg">
                                {{ substr($visit->visitor->full_name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1 text-white">
                            <h3 class="text-2xl font-bold">{{ $visit->visitor->full_name }}</h3>
                            <p class="text-blue-100">{{ $visit->visitor->company ?? 'Individual Visitor' }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($visit->status === 'active') bg-green-500 text-white
                                    @elseif($visit->status === 'completed') bg-gray-500 text-white
                                    @else bg-red-500 text-white
                                    @endif">
                                    {{ ucfirst($visit->status) }}
                                </span>
                                <span class="ml-2 px-3 py-1 bg-white text-blue-600 rounded-full text-sm font-semibold">
                                    Badge: {{ $visit->badge_number }}
                                </span>
                            </div>
                        </div>
                        @if($visit->status === 'active')
                            <form method="POST" action="{{ route('admin.visitors.checkout', $visit->visitor) }}">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition" onclick="return confirm('Check out this visitor?')">
                                    Check Out
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Visitor Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Visitor Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->visitor->id_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $visit->visitor->id_type)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->visitor->phone_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->visitor->email ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Visit Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Visit Details</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Host</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->host->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purpose</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->visit_purpose }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Check-in Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->check_in_time->format('M d, Y h:i A') }}</dd>
                            </div>
                            @if($visit->actual_checkout_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Checkout Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->actual_checkout_time->format('M d, Y h:i A') }}</dd>
                            </div>
                            @endif
                            @if($visit->temperature)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Temperature</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $visit->temperature }}°C</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

            </div>

            <!-- Vehicle Information -->
            @if($visit->vehicleVisit)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Vehicle Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plate Number</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">{{ $visit->vehicleVisit->vehicle->plate_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Vehicle</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $visit->vehicleVisit->vehicle->make }} {{ $visit->vehicleVisit->vehicle->model }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Parking Zone</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $visit->vehicleVisit->parkingZone->name ?? 'N/A' }}
                                @if($visit->vehicleVisit->parking_slot)
                                    - Slot {{ $visit->vehicleVisit->parking_slot }}
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Asset Checkouts -->
            @if($visit->assetCheckouts->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Checked Out Assets</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($visit->assetCheckouts as $checkout)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $checkout->asset->name }}</p>
                                    <p class="text-sm text-gray-600">Code: {{ $checkout->asset->asset_code }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    @if($checkout->status === 'checked_out') bg-yellow-100 text-yellow-800
                                    @elseif($checkout->status === 'returned') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $checkout->status)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
