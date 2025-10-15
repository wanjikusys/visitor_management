<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Edit User') }}
            </h2>
            <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                            <select name="role" id="role" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                <option value="receptionist" {{ $user->role === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                                <option value="security" {{ $user->role === 'security' ? 'selected' : '' }}>Security Officer</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Change Password (Optional)</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" name="password" id="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password. Minimum 8 characters.</p>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">User is Active</span>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete User
                        </button>
                    </form>
                    @else
                    <div></div>
                    @endif
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.users.show', $user) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update User
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
