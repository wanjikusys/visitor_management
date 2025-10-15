<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Add Asset') }}
            </h2>
            <a href="{{ route('admin.assets.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Assets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <form method="POST" action="{{ route('admin.assets.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Asset Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="asset_code" class="block text-sm font-medium text-gray-700 mb-2">Asset Code *</label>
                                <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase" placeholder="AST-001">
                                @error('asset_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="asset_category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                <select name="asset_category_id" id="asset_category_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('asset_category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Asset Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('serial_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Purchase Price</label>
                                <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
                                <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Building A, Floor 2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="specifications" class="block text-sm font-medium text-gray-700 mb-2">Specifications</label>
                            <textarea name="specifications" id="specifications" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('specifications') }}</textarea>
                        </div>

                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Asset Photo</label>
                            <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.assets.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Asset
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
