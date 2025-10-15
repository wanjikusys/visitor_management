<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
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

            <!-- User Header -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="w-24 h-24 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-4xl shadow-lg">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold mb-2">{{ $user->name }}</h3>
                                <p class="text-xl text-gray-300">{{ $user->email }}</p>
                                <div class="mt-3 space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        @if($user->role === 'admin') bg-purple-500 text-white
                                        @elseif($user->role === 'security') bg-blue-500 text-white
                                        @elseif($user->role === 'receptionist') bg-green-500 text-white
                                        @else bg-gray-500 text-white
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    @if($user->is_active)
                                        <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">Active</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- User Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Account Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Role</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        @if($user->role === 'admin') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'security') bg-blue-100 text-blue-800
                                        @elseif($user->role === 'receptionist') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                            ✓ Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                            ✗ Inactive
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Verification</dt>
                                <dd class="mt-1">
                                    @if($user->email_verified_at)
                                        <span class="text-sm text-green-600">✓ Verified on {{ $user->email_verified_at->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-sm text-red-600">✗ Not Verified</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Activity Summary -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Activity Summary</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div>
                                    <dt class="text-sm font-medium text-blue-900">Hosted Visitors</dt>
                                    <dd class="mt-1 text-3xl font-bold text-blue-600">{{ $user->hostedVisits->count() }}</dd>
                                </div>
                                <div class="bg-blue-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div>
                                    <dt class="text-sm font-medium text-green-900">Active Visitors</dt>
                                    <dd class="mt-1 text-3xl font-bold text-green-600">
                                        {{ $user->hostedVisits->where('status', 'active')->count() }}
                                    </dd>
                                </div>
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $user->updated_at->diffForHumans() }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>

            <!-- Recent Hosted Visitors -->
            @if($user->hostedVisits->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Hosted Visitors</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->hostedVisits->take(10) as $visit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $visit->visitor->full_name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ Str::limit($visit->visit_purpose, 40) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $visit->check_in_time->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($visit->status === 'active') bg-green-100 text-green-800
                                            @elseif($visit->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($visit->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
