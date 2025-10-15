<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Ward Register (IPD) — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6"
         x-data="wardLive()"
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
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Branch</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient Name</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient No.</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NOK Details</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ward</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Bed</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Admitted</th>
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
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="r.Branch"></span>
                                    </td>
                                    <td class="px-3 py-3 text-gray-900 font-medium" x-text="r.PatientName"></td>
                                    <td class="px-3 py-3 text-gray-700 font-mono text-xs" x-text="r.PatientNumber"></td>
                                    <td class="px-3 py-3 text-gray-700 text-xs" x-text="r.NOKDetails"></td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800" x-text="r.WardNumber"></span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800" x-text="r.BedNumber"></span>
                                    </td>
                                    <td class="px-3 py-3 text-gray-700 whitespace-nowrap text-xs" x-text="formatDateOnly(r.AdmissionDate)"></td>
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
    </div>

    <script>
    function wardLive() {
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

            async init() {
                const cached = sessionStorage.getItem('ward_register_data');
                const cachedTime = sessionStorage.getItem('ward_register_time');

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
                    const response = await fetch('{{ route("admin.hmis.ward.fetch") }}');
                    const data = await response.json();

                    if (data.success) {
                        this.allRecords = data.records;
                        this.cacheStatus = 'refreshed';

                        sessionStorage.setItem('ward_register_data', JSON.stringify(this.allRecords));
                        sessionStorage.setItem('ward_register_time', Date.now().toString());

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
                sessionStorage.removeItem('ward_register_data');
                sessionStorage.removeItem('ward_register_time');

                try {
                    await fetch('{{ route("admin.hmis.ward.clear-cache") }}', {
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
