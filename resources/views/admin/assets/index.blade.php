<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Assets') }}
            </h2>
            <a href="{{ route('admin.assets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Asset
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.assets.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, Code, Serial..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Statuses</option>
                                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ request('status') === 'retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 mr-2">
                                Filter
                            </button>
                            <a href="{{ route('admin.assets.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assets Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($assets as $asset)
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow">
                        @if($asset->photo_path)
                            <img src="{{ Storage::url($asset->photo_path) }}" alt="{{ $asset->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase">{{ $asset->asset_code }}</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($asset->status === 'available') bg-green-100 text-green-800
                                    @elseif($asset->status === 'checked_out') bg-yellow-100 text-yellow-800
                                    @elseif($asset->status === 'maintenance') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $asset->name }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $asset->category->name }}</p>
                            @if($asset->description)
                                <p class="text-xs text-gray-500 mb-4">{{ Str::limit($asset->description, 60) }}</p>
                            @endif
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.assets.show', $asset) }}" class="flex-1 px-4 py-2 bg-blue-600 text-white text-center text-sm font-semibold rounded-md hover:bg-blue-700">
                                    View
                                </a>
                                @if($asset->status === 'available')
                                    <a href="{{ route('admin.assets.checkout.create', $asset) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                                        Checkout
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No assets found</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $assets->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
