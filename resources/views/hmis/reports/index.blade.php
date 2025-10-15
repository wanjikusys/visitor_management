<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            HMIS Reports — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Inpatient Visitors Report -->
            <a href="{{ route('admin.hmis.reports.visitors') }}" class="block bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden hover:shadow-xl transition">
                <div class="px-6 py-8">
                    <div class="flex items-center">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Inpatient Visitors</h3>
                            <p class="text-sm text-gray-500 mt-1">View and download visitor logs</p>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Vehicles Report -->
            <a href="{{ route('admin.hmis.reports.vehicles') }}" class="block bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden hover:shadow-xl transition">
                <div class="px-6 py-8">
                    <div class="flex items-center">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Vehicles Movement</h3>
                            <p class="text-sm text-gray-500 mt-1">View and download vehicle logs</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
