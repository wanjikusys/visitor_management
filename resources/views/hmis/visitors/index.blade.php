<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Inpatient Visitors (Ward Register) — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6"
         x-data="visitorsLive()"
         x-init="init()">

        <!-- Header Stats -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">Ward Register</span> • KIJABE • 
                    <span x-show="!isLoading" class="text-indigo-600 font-medium">
                        <span x-text="filteredCount"></span> of <span x-text="totalCount"></span> admitted
                    </span>
                    <span x-show="isLoading" class="text-gray-400">Loading...</span>
                </div>
                
                <div x-show="autoRefresh" class="flex items-center gap-1.5 text-xs text-indigo-600">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    <span>Auto-refresh ON</span>
                </div>

                <div x-show="cacheStatus" x-transition class="flex items-center gap-1.5 text-xs" :class="cacheStatus === 'cached' ? 'text-blue-600' : 'text-indigo-600'">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="cacheStatus === 'cached' ? 'Using cached data' : 'Data refreshed'"></span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery"
                       @input="instantSearch"
                       placeholder="Search patient, ward, bed..." 
                       class="border border-gray-300 rounded-lg pl-10 pr-10 py-2 text-sm w-full sm:w-80 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 hover:border-gray-400">
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button x-show="searchQuery" @click="searchQuery = ''; instantSearch()" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Action Buttons & Pagination -->
        <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" 
                        @click="toggleAutoRefresh"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border transition-colors duration-150"
                        :class="autoRefresh ? 'bg-indigo-50 border-indigo-300 text-indigo-700 hover:bg-indigo-100' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'">
                    <span x-text="autoRefresh ? 'Auto ON (2min)' : 'Auto OFF'"></span>
                </button>

                <button type="button" 
                        @click="manualRefresh" 
                        :disabled="isLoading"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150">
                    Refresh All Data
                </button>

                <button type="button" 
                        @click="clearAllCache"
                        class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium border border-red-300 text-red-700 bg-white hover:bg-red-50 transition-colors duration-150">
                    Clear Cache
                </button>

                <span class="text-xs text-gray-500" x-show="lastFetched">
                    Updated <span x-text="lastFetched"></span>
                </span>
            </div>

            <!-- Pagination -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-600">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button @click="previousPage" :disabled="currentPage === 1" class="px-3 py-1 rounded border border-gray-300 text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    Prev
                </button>
                <button @click="nextPage" :disabled="currentPage >= totalPages" class="px-3 py-1 rounded border border-gray-300 text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gradient-to-r from-indigo-50 to-indigo-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NOK Details</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ward/Bed</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Admitted</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Visitors</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <!-- Loading State -->
                        <template x-if="isLoading">
                            <tr>
                                <td colspan="7" class="px-4 py-12">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-gray-600 text-sm">Loading ward register...</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Data Rows -->
                        <template x-if="!isLoading && paginatedRecords.length > 0">
                            <template x-for="r in paginatedRecords" :key="r.PatientNumber + '-' + r.WardNumber + '-' + r.BedNumber">
                                <tr class="hover:bg-indigo-50 transition-colors duration-100">
                                    <td class="px-4 py-3 text-gray-900 font-medium" x-text="r.PatientName"></td>
                                    <td class="px-4 py-3 text-gray-700 font-mono text-xs" x-text="r.PatientNumber"></td>
                                    <td class="px-4 py-3 text-gray-700 text-xs" x-text="r.NOKDetails"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800" x-text="r.WardNumber"></span>
                                            <span class="text-gray-400">/</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800" x-text="r.BedNumber"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap text-xs" x-text="formatDateOnly(r.AdmissionDate)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span x-show="r.ActiveVisitors > 0" 
                                              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" 
                                              x-text="r.ActiveVisitors"></span>
                                        <span x-show="!r.ActiveVisitors || r.ActiveVisitors === 0" 
                                              class="text-xs text-gray-400">0</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openCheckInModal(r)" 
                                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                                </svg>
                                                Check-in
                                            </button>
                                            
                                            <button @click="viewVisitors(r)" 
                                                    x-show="r.ActiveVisitors > 0"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>

                        <!-- Empty State -->
                        <template x-if="!isLoading && paginatedRecords.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No admitted patients</p>
                                        <p class="text-gray-400 text-xs mt-1" x-show="searchQuery">Try a different search term</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Check-in Modal -->
        <div x-show="showCheckInModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div x-show="showCheckInModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                     @click="showCheckInModal = false"></div>

                <div x-show="showCheckInModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-lg w-full">
                    
                    <form @submit.prevent="submitCheckIn">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">Check-in Visitor</h3>
                                </div>
                                <button type="button" @click="showCheckInModal = false" class="text-white hover:text-gray-200 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-4 mb-6 border border-indigo-100">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900 mb-1" x-text="selectedPatient?.PatientName"></p>
                                        <div class="flex flex-wrap gap-2 text-xs">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-gray-700 font-mono">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                                </svg>
                                                <span x-text="selectedPatient?.PatientNumber"></span>
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                                Ward: <span class="ml-1 font-medium" x-text="selectedPatient?.WardNumber"></span>
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-700">
                                                Bed: <span class="ml-1 font-medium" x-text="selectedPatient?.BedNumber"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Visitor Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="visitorForm.visitor_name" 
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                           placeholder="Enter visitor's full name">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                           x-model="visitorForm.visitor_phone"
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                           placeholder="0712345678">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        ID Number
                                    </label>
                                    <input type="text" 
                                           x-model="visitorForm.visitor_id_number"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                           placeholder="National ID or Passport">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Relationship to Patient
                                    </label>
                                    <input type="text" 
                                           x-model="visitorForm.relationship"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                           placeholder="e.g., Spouse, Child, Friend">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Notes (Optional)
                                    </label>
                                    <textarea x-model="visitorForm.notes"
                                              rows="2"
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition"
                                              placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                            <button type="button" 
                                    @click="showCheckInModal = false"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Check-in Visitor
                                </span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Checking in...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Visitors Modal -->
        <div x-show="showVisitorsModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div x-show="showVisitorsModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                     @click="showVisitorsModal = false"></div>

                <div x-show="showVisitorsModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-2xl w-full">
                    
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Active Visitors</h3>
                                    <p class="text-indigo-100 text-sm" x-text="selectedPatientForView?.PatientName"></p>
                                </div>
                            </div>
                            <button type="button" @click="showVisitorsModal = false" class="text-white hover:text-gray-200 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-3 mb-4 border border-indigo-100">
                            <div class="flex flex-wrap gap-2 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white text-gray-700 font-mono">
                                    <span x-text="selectedPatientForView?.PatientNumber"></span>
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                    Ward: <span class="ml-1 font-medium" x-text="selectedPatientForView?.WardNumber"></span>
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-teal-100 text-teal-700">
                                    Bed: <span class="ml-1 font-medium" x-text="selectedPatientForView?.BedNumber"></span>
                                </span>
                            </div>
                        </div>

                        <div x-show="loadingVisitors" class="text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-600 text-sm mt-2">Loading visitors...</p>
                        </div>

                        <div x-show="!loadingVisitors" class="space-y-3 max-h-96 overflow-y-auto">
                            <template x-for="visitor in activeVisitors" :key="visitor.id">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900" x-text="visitor.visitor_name"></h4>
                                                    <p class="text-xs text-gray-500" x-text="visitor.relationship || 'N/A'"></p>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-2">
                                                <div>
                                                    <span class="font-medium">Phone:</span> 
                                                    <span x-text="visitor.visitor_phone"></span>
                                                </div>
                                                <div x-show="visitor.visitor_id_number">
                                                    <span class="font-medium">ID:</span> 
                                                    <span x-text="visitor.visitor_id_number"></span>
                                                </div>
                                                <div class="col-span-2">
                                                    <span class="font-medium">Checked in:</span> 
                                                    <span x-text="formatDateTime(visitor.check_in_time)"></span>
                                                </div>
                                                <div x-show="visitor.notes" class="col-span-2">
                                                    <span class="font-medium">Notes:</span> 
                                                    <span x-text="visitor.notes"></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button @click="checkoutVisitor(visitor.id)" 
                                                class="ml-4 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition whitespace-nowrap">
                                            Check-out
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div x-show="activeVisitors.length === 0" class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No active visitors</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end">
                        <button type="button" 
                                @click="showVisitorsModal = false"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function visitorsLive() {
        return {
            allRecords: [],
            filteredRecords: [],
            paginatedRecords: [],
            searchQuery: '',
            isLoading: false,
            autoRefresh: true,
            cacheStatus: null,
            lastFetched: null,
            currentPage: 1,
            perPage: 20,
            debounceTimer: null,
            pollInterval: null,
            showCheckInModal: false,
            selectedPatient: null,
            isSubmitting: false,
            showVisitorsModal: false,
            selectedPatientForView: null,
            activeVisitors: [],
            loadingVisitors: false,
            visitorForm: {
                visitor_name: '',
                visitor_id_number: '',
                visitor_phone: '',
                relationship: '',
                notes: ''
            },

            get totalCount() {
                return this.allRecords.length;
            },

            get filteredCount() {
                return this.filteredRecords.length;
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredRecords.length / this.perPage));
            },

            formatDateOnly(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-GB', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                } catch (e) {
                    return dateString;
                }
            },

            formatDateTime(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleString('en-GB', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    return dateString;
                }
            },

            async init() {
                const cached = sessionStorage.getItem('visitors_kijabe_data');
                const cachedTime = sessionStorage.getItem('visitors_kijabe_time');

                if (cached && cachedTime) {
                    const cacheAge = Date.now() - parseInt(cachedTime);
                    if (cacheAge < 120000) {
                        this.allRecords = JSON.parse(cached);
                        this.cacheStatus = 'cached';
                        this.filteredRecords = [...this.allRecords];
                        this.updatePagination();
                        this.lastFetched = this.formatTime(parseInt(cachedTime));
                        
                        setTimeout(() => this.manualRefresh(true), 1000);
                        
                        if (this.autoRefresh) {
                            this.startAutoRefresh();
                        }
                        return;
                    }
                }

                await this.manualRefresh();
                if (this.autoRefresh) {
                    this.startAutoRefresh();
                }
            },

            async manualRefresh(silent = false) {
                if (!silent) {
                    this.isLoading = true;
                }

                try {
                    const response = await fetch('{{ route("admin.hmis.visitors.fetch") }}');
                    const data = await response.json();

                    if (data.success) {
                        this.allRecords = data.records;
                        this.cacheStatus = 'refreshed';

                        sessionStorage.setItem('visitors_kijabe_data', JSON.stringify(this.allRecords));
                        sessionStorage.setItem('visitors_kijabe_time', Date.now().toString());

                        this.lastFetched = this.formatTime(Date.now());

                        this.filteredRecords = [...this.allRecords];
                        this.currentPage = 1;
                        this.updatePagination();

                        setTimeout(() => {
                            this.cacheStatus = null;
                        }, 3000);
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    if (!silent) {
                        alert('Failed to fetch data');
                    }
                } finally {
                    this.isLoading = false;
                }
            },

            openCheckInModal(patient) {
                this.selectedPatient = patient;
                this.showCheckInModal = true;
                this.visitorForm = {
                    visitor_name: '',
                    visitor_id_number: '',
                    visitor_phone: '',
                    relationship: '',
                    notes: ''
                };
            },

            async submitCheckIn() {
                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route("admin.hmis.visitors.check-in") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            patient_number: this.selectedPatient.PatientNumber,
                            patient_name: this.selectedPatient.PatientName,
                            ward_number: this.selectedPatient.WardNumber,
                            bed_number: this.selectedPatient.BedNumber,
                            ...this.visitorForm
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ Visitor checked in successfully!');
                        this.showCheckInModal = false;
                        await this.manualRefresh(true);
                    } else {
                        alert('❌ Failed to check-in visitor');
                    }
                } catch (error) {
                    console.error('Check-in error:', error);
                    alert('Error: ' + error.message);
                } finally {
                    this.isSubmitting = false;
                }
            },

            async viewVisitors(patient) {
                this.selectedPatientForView = patient;
                this.showVisitorsModal = true;
                this.loadingVisitors = true;
                
                try {
                    const response = await fetch(`{{ url('admin/hmis/inpatient-visitors') }}/${patient.PatientNumber}/active`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.activeVisitors = data.visitors;
                    }
                } catch (error) {
                    console.error('Error fetching visitors:', error);
                    alert('Failed to load visitors');
                } finally {
                    this.loadingVisitors = false;
                }
            },

            async checkoutVisitor(visitorId) {
                if (!confirm('Are you sure you want to check-out this visitor?')) {
                    return;
                }
                
                try {
                    const response = await fetch(`{{ url('admin/hmis/inpatient-visitors') }}/${visitorId}/check-out`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('✅ Visitor checked out successfully!');
                        await this.viewVisitors(this.selectedPatientForView);
                        await this.manualRefresh(true);
                    } else {
                        alert('❌ Failed to check-out visitor');
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    alert('Error: ' + error.message);
                }
            },

            instantSearch() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    const q = this.searchQuery.toLowerCase().trim();

                    if (!q) {
                        this.filteredRecords = [...this.allRecords];
                    } else {
                        this.filteredRecords = this.allRecords.filter(r => {
                            const patient = (r.PatientName || '').toString().toLowerCase();
                            const patNo = (r.PatientNumber || '').toString().toLowerCase();
                            const nok = (r.NOKDetails || '').toString().toLowerCase();
                            const ward = (r.WardNumber || '').toString().toLowerCase();
                            const bed = (r.BedNumber || '').toString().toLowerCase();

                            return patient.includes(q) || patNo.includes(q) || nok.includes(q) || ward.includes(q) || bed.includes(q);
                        });
                    }

                    this.currentPage = 1;
                    this.updatePagination();
                }, 150);
            },

            updatePagination() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                this.paginatedRecords = this.filteredRecords.slice(start, end);
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.updatePagination();
                }
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.updatePagination();
                }
            },

            startAutoRefresh() {
                this.pollInterval = setInterval(() => {
                    this.manualRefresh(true);
                }, 120000);
            },

            toggleAutoRefresh() {
                this.autoRefresh = !this.autoRefresh;

                if (this.autoRefresh) {
                    this.startAutoRefresh();
                    this.manualRefresh(true);
                } else {
                    if (this.pollInterval) {
                        clearInterval(this.pollInterval);
                        this.pollInterval = null;
                    }
                }
            },

            async clearAllCache() {
                sessionStorage.removeItem('visitors_kijabe_data');
                sessionStorage.removeItem('visitors_kijabe_time');

                try {
                    await fetch('{{ route("admin.hmis.visitors.clear-cache") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                } catch (e) {}

                await this.manualRefresh();
            },

            formatTime(timestamp) {
                const date = new Date(timestamp);
                return date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }
    }
    </script>
</x-app-layout>
