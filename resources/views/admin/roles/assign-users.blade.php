<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                Assign Users to Role: {{ $role->display_name }}
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Back to Roles
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Assign New User -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Assign User to Role
                </h3>

                @if($availableUsers->count() > 0)
                    <form action="{{ route('admin.roles.attach-user', $role) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                            <select name="user_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Choose a user --</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Assign User
                        </button>
                    </form>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-gray-500">All users have been assigned to this role</p>
                    </div>
                @endif
            </div>

            <!-- Currently Assigned Users -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Assigned Users ({{ $assignedUsers->count() }})
                </h3>

                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($assignedUsers as $user)
                        <div class="flex items-center justify-between px-4 py-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                                    <span class="text-green-700 font-semibold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>

                            @if($role->name !== 'admin' || $assignedUsers->count() > 1)
                                <form action="{{ route('admin.roles.detach-user', [$role, $user]) }}" method="POST" onsubmit="return confirm('Remove {{ $user->name }} from this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium">
                                        Remove
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                    Last Admin
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="text-gray-500">No users assigned to this role yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Role Info Summary -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Role Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-sm text-blue-700 font-medium">Role Name</div>
                    <div class="text-gray-900">{{ $role->display_name }}</div>
                </div>
                <div>
                    <div class="text-sm text-blue-700 font-medium">Total Users</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $assignedUsers->count() }}</div>
                </div>
                <div>
                    <div class="text-sm text-blue-700 font-medium">Permissions</div>
                    <div class="text-2xl font-bold text-green-600">{{ $role->permissions->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
