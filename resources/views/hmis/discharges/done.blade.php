<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Discharges Done Today — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6"
         x-data="dischargesLive()"
         x-init="init()">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">Today's Discharges</span> • 
                    KIJABE • 
                    <span x-show="!isLoading" class="text-[ #159ed5 ] font-medium" style="color:#159ed5;">
                        <span class="font-medium" x-text="filteredCount"></span> of <span class="font-medium" x-text="totalCount"></span> patients
                    </span>
                    <span x-show="isLoading" class="text-gray-400">Loading...</span>
                </div>
                
                <div x-show="autoRefresh" class="flex items-center gap-1.5 text-xs" style="color:#159ed5;">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full" style="background:#cfeef9; opacity:.75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2" style="background:#159ed5"></span>
                    </span>
                    <span>Auto-refresh ON</span>
                    <span class="text-gray-500" x-show="nextPollIn">(next in <span x-text="nextPollIn"></span>s)</span>
                </div>

                <div x-show="cacheStatus" x-transition class="flex items-center gap-1.5 text-xs" :class="cacheStatus === 'cached' ? 'text-blue-600' : 'text-[ #159ed5 ]'">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="cacheStatus === 'cached' ? 'Using cached data' : 'Data refreshed'"></span>
                </div>
            </div>

            <div class="relative">
                <input type="text"
                       x-model="searchQuery"
                       @input="instantSearch()"
                       placeholder="Search patient, ward, bed..."
                       class="border border-gray-300 rounded-lg pl-10 pr-10 py-2 text-sm w-full sm:w-80 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#159ed5] focus:border-[#159ed5] hover:border-gray-400" />
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button x-show="searchQuery" 
                        @click="searchQuery = ''; instantSearch();"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button"
                        @click="toggleAutoRefresh()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border transition-colors duration-150"
                        :class="autoRefresh ? 'bg-[#e6f4fb] border-[#bfeaf9] text-[#0b7ea5] hover:bg-[#dff5ff]' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span x-text="autoRefresh ? 'Auto ON (2min)' : 'Auto OFF'"></span>
                </button>

                <button type="button"
                        @click="manualRefresh()"
                        :disabled="isLoading"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" :class="isLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh All Data
                </button>

                <button type="button"
                        @click="checkForNewRecords()"
                        :disabled="isPolling"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium border border-[#bfeaf9] text-[#0b7ea5] bg-[#e6f4fb] hover:bg-[#dff5ff] disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150">
                    <svg class="w-4 h-4 mr-2" :class="isPolling ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Check New Discharges
                </button>

                <button type="button"
                        @click="clearAllCache()"
                        class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium border border-red-300 text-red-700 bg-white hover:bg-red-50 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Cache
                </button>

                <span class="text-xs text-gray-500" x-show="lastFetched">
                    Updated: <span x-text="lastFetched"></span>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-600">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button @click="previousPage()" 
                        :disabled="currentPage === 1"
                        class="px-3 py-1 rounded border border-gray-300 text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    Prev
                </button>
                <button @click="nextPage()" 
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 rounded border border-gray-300 text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
        </div>

        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
            <div class="overflow-x-auto" style="max-height: 70vh;">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gradient-to-r from-[#e6f4fb] to-[#dff7ff] sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Branch</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Next of Kin</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ward</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Bed</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Discharge Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Discharged By</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        <template x-if="isLoading">
                            <tr>
                                <td colspan="7" class="px-4 py-12">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <svg class="animate-spin h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-gray-600 text-sm">Loading today's discharges...</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="!isLoading && paginatedRecords.length > 0">
                            <template x-for="r in paginatedRecords" :key="r.PatientNumber + r.ActualDischargeDate">
                                <tr class="hover:bg-[#e6f4fb] transition-colors duration-100"
                                    x-show
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100">
                                    <td class="px-4 py-3 text-gray-900 font-medium">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background:#dff7ff;color:#063a49" x-text="r.BranchID"></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 font-medium" x-text="r.PatientName"></td>
                                    <td class="px-4 py-3 text-gray-600 text-xs" x-text="r.NextOfKin"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background:#eef7fb;color:#06475e" x-text="r.WardNumber"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background:#f0fbf7;color:#06475e" x-text="r.BedNumber"></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap font-mono text-xs" x-text="formatDisplayDate(r.ActualDischargeDate)"></td>
                                    <td class="px-4 py-3 text-gray-700 text-xs" x-text="r.DischargedBy"></td>
                                </tr>
                            </template>
                        </template>

                        <template x-if="!isLoading && paginatedRecords.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No discharges found for today</p>
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
        function dischargesLive() {
            return {
                allRecords: [],
                filteredRecords: [],
                paginatedRecords: [],
                newRecords: [],
                newRecordsCount: 0,
                searchQuery: '',
                isLoading: false,
                isPolling: false,
                autoRefresh: true,
                cacheStatus: null,
                lastFetched: null,
                lastCheckTime: null,
                currentPage: 1,
                perPage: 20,
                debounceTimer: null,
                pollInterval: null,
                countdownInterval: null,
                nextPollIn: null,
                seenPatients: new Set(),

                get totalCount() {
                    return this.allRecords.length;
                },

                get filteredCount() {
                    return this.filteredRecords.length;
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredRecords.length / this.perPage));
                },

                async init() {
                    const cached = sessionStorage.getItem('discharges_kijabe_data');
                    const cachedTime = sessionStorage.getItem('discharges_kijabe_time');

                    if (cached && cachedTime) {
                        const cacheAge = Date.now() - parseInt(cachedTime);
                        if (cacheAge < 120000) {
                            this.allRecords = JSON.parse(cached);
                            this.allRecords.forEach(r => this.seenPatients.add(r.PatientNumber + r.ActualDischargeDate));
                            this.cacheStatus = 'cached';
                            this.instantSearch();
                            this.lastFetched = this.formatTime(parseInt(cachedTime));
                            this.lastCheckTime = this.getMaxDateTime(this.allRecords);

                            // background refresh to ensure freshness
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
                    if (!silent) this.isLoading = true;

                    try {
                        const response = await fetch('{{ route('admin.hmis.discharges.fetch') }}');
                        const data = await response.json();

                        if (data.success) {
                            this.allRecords = data.records;
                            this.seenPatients = new Set(data.records.map(r => r.PatientNumber + r.ActualDischargeDate));
                            this.cacheStatus = 'refreshed';

                            sessionStorage.setItem('discharges_kijabe_data', JSON.stringify(data.records));
                            sessionStorage.setItem('discharges_kijabe_time', Date.now().toString());

                            this.lastFetched = this.formatTime(Date.now());
                            this.lastCheckTime = this.getMaxDateTime(this.allRecords);
                            this.instantSearch();

                            setTimeout(() => this.cacheStatus = null, 3000);
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                        if (!silent) alert('Failed to fetch data');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async checkForNewRecords() {
                    if (this.isPolling) return;
                    this.isPolling = true;

                    try {
                        const params = new URLSearchParams({
                            last_check: this.lastCheckTime || ''
                        });

                        const response = await fetch('{{ route('admin.hmis.discharges.poll') }}?' + params);
                        const data = await response.json();

                        if (data.success && data.count > 0) {
                            const trulyNew = data.records.filter(r => !this.seenPatients.has(r.PatientNumber + r.ActualDischargeDate));

                            if (trulyNew.length > 0) {
                                this.newRecords = trulyNew;
                                this.newRecordsCount = trulyNew.length;
                                trulyNew.forEach(r => this.seenPatients.add(r.PatientNumber + r.ActualDischargeDate));
                                this.loadNewRecords();
                            }
                        }

                        this.lastCheckTime = this.getMaxDateTime(this.allRecords);
                    } catch (error) {
                        console.error('Poll error:', error);
                    } finally {
                        this.isPolling = false;
                    }
                },

                loadNewRecords() {
                    this.allRecords = [...this.newRecords, ...this.allRecords];
                    this.newRecords = [];
                    this.newRecordsCount = 0;

                    sessionStorage.setItem('discharges_kijabe_data', JSON.stringify(this.allRecords));
                    sessionStorage.setItem('discharges_kijabe_time', Date.now().toString());

                    this.instantSearch();
                },

                startAutoRefresh() {
                    this.nextPollIn = 120;

                    this.pollInterval = setInterval(() => {
                        this.checkForNewRecords();
                    }, 120000);

                    this.countdownInterval = setInterval(() => {
                        this.nextPollIn--;
                        if (this.nextPollIn <= 0) {
                            this.nextPollIn = 120;
                        }
                    }, 1000);
                },

                stopAutoRefresh() {
                    if (this.pollInterval) {
                        clearInterval(this.pollInterval);
                        this.pollInterval = null;
                    }
                    if (this.countdownInterval) {
                        clearInterval(this.countdownInterval);
                        this.countdownInterval = null;
                    }
                    this.nextPollIn = null;
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                    if (this.autoRefresh) {
                        this.startAutoRefresh();
                        this.checkForNewRecords();
                    } else {
                        this.stopAutoRefresh();
                    }
                },

                instantSearch() {
                    clearTimeout(this.debounceTimer);

                    this.debounceTimer = setTimeout(() => {
                        const query = this.searchQuery.toLowerCase().trim();

                        if (!query) {
                            this.filteredRecords = [...this.allRecords];
                        } else {
                            this.filteredRecords = this.allRecords.filter(r =>
                                (r.PatientName || '').toLowerCase().includes(query) ||
                                (r.PatientNumber || '').toLowerCase().includes(query) ||
                                (r.WardNumber || '').toLowerCase().includes(query) ||
                                (r.BedNumber || '').toLowerCase().includes(query) ||
                                (r.DischargedBy || '').toLowerCase().includes(query) ||
                                (r.NextOfKin || '').toLowerCase().includes(query)
                            );
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

                async clearAllCache() {
                    sessionStorage.removeItem('discharges_kijabe_data');
                    sessionStorage.removeItem('discharges_kijabe_time');

                    try {
                        await fetch('{{ route('admin.hmis.discharges.clear-cache') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                    } catch (e) {}
                    
                    await this.manualRefresh();
                },

                getMaxDateTime(records) {
                    if (!records || records.length === 0) return null;
                    return records.reduce((max, r) => r.ActualDischargeDate > max ? r.ActualDischargeDate : max, records[0].ActualDischargeDate);
                },

                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                formatDisplayDate(datetimeStr) {
                    if (!datetimeStr) return '';
                    // datetimeStr is "yyyy-mm-dd hh:mi:ss"
                    // show "yyyy-mm-dd hh:mm:ss"
                    return datetimeStr;
                }
            };
        }
    </script>
</x-app-layout>
