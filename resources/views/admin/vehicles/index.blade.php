<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Vehicles') }}
            </h2>
            <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Register Vehicle
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.vehicles.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Plate, Make, Model..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                            <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Types</option>
                                <option value="car" {{ request('type') === 'car' ? 'selected' : '' }}>Car</option>
                                <option value="motorcycle" {{ request('type') === 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                <option value="truck" {{ request('type') === 'truck' ? 'selected' : '' }}>Truck</option>
                                <option value="van" {{ request('type') === 'van' ? 'selected' : '' }}>Van</option>
                                <option value="suv" {{ request('type') === 'suv' ? 'selected' : '' }}>SUV</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 mr-2">
                                Filter
                            </button>
                            <a href="{{ route('admin.vehicles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vehicles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($vehicles as $vehicle)
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow">
                        @if($vehicle->photo_path)
                            <img src="{{ Storage::url($vehicle->photo_path) }}" alt="{{ $vehicle->plate_number }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-gray-900 font-mono">{{ $vehicle->plate_number }}</h3>
                                @if($vehicle->currentVisit)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-600 rounded-full mr-1"></span>
                                        Parked
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Available
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600"><span class="font-semibold">Make:</span> {{ $vehicle->make ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600"><span class="font-semibold">Model:</span> {{ $vehicle->model ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600"><span class="font-semibold">Color:</span> {{ $vehicle->color ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600"><span class="font-semibold">Type:</span> {{ ucfirst($vehicle->vehicle_type) }}</p>
                            </div>
                            @if($vehicle->is_blacklisted)
                                <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded-md">
                                    <p class="text-xs text-red-800 font-semibold">⚠️ BLACKLISTED</p>
                                </div>
                            @endif
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white text-center text-sm font-semibold rounded-md hover:bg-blue-700">
                                    View Details
                                </a>
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-300">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No vehicles found</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $vehicles->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
