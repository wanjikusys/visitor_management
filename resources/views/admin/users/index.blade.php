<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Users Management
            </h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add User
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-6">
            <div class="px-6 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Filter Users</h3>
            </div>
            <div class="p-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name or Email..." class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="role" class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="security" {{ request('role') === 'security' ? 'selected' : '' }}>Security</option>
                            <option value="receptionist" {{ request('role') === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                            <option value="hmis_staff" {{ request('role') === 'hmis_staff' ? 'selected' : '' }}>HMIS Staff</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">System Users</h3>
                    <span class="text-xs text-gray-500">{{ $users->total() }} users</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Roles
                            </th>
                            <th scope="col" class="px-4 py-2 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Joined
                            </th>
                            <th scope="col" class="px-4 py-2 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex flex-wrap gap-1">
                                        @if($user->roles && $user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                                    @if($role->name === 'admin') bg-purple-100 text-purple-800
                                                    @elseif($role->name === 'security') bg-blue-100 text-blue-800
                                                    @elseif($role->name === 'receptionist') bg-green-100 text-green-800
                                                    @elseif($role->name === 'hmis_staff') bg-orange-100 text-orange-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $role->display_name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ ucfirst($user->role ?? 'User') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-center">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center p-1.5 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center p-1.5 bg-green-100 text-green-700 rounded hover:bg-green-200 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                    <p class="mt-1 text-xs text-gray-500">Try adjusting your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
