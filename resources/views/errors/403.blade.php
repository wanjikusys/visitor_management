<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="max-w-md w-full bg-white shadow-lg rounded-xl p-8 text-center">
            <div class="mb-6">
                <svg class="w-24 h-24 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Access Denied</h1>
            <h2 class="text-xl font-semibold text-red-600 mb-4">403 Forbidden</h2>
            
            <p class="text-gray-600 mb-6">
                {{ $message ?? 'You do not have permission to access this page.' }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Go to Dashboard
                </a>
                <button onclick="history.back()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Go Back
                </button>
            </div>

            <div class="mt-6 text-sm text-gray-500">
                <p>If you believe this is an error, please contact your administrator.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
