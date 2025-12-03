@admin
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Patient Registers — Separated View (OPD, Ward, Theatre)
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6"
         x-data="combinedRegister()"
         x-init="init()">

        <!-- Header, Stats, and Search Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">Total Records</span> • 
                    <span x-show="!isLoading" class="text-indigo-600 font-medium">
                        <span x-text="filteredCount"></span> of <span x-text="totalCount"></span> patients visible
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
                
                <span class="text-xs text-gray-500" x-show="lastFetched">
                    Last Fetched <span x-text="lastFetched"></span>
                </span>
            </div>

            <!-- Search Bar -->
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery"
                       @input="instantSearch"
                       placeholder="Search all patients..." 
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
        
        <!-- Action Buttons -->
        <div class="mb-6 flex flex-wrap items-center gap-2">
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
                Refresh Data
            </button>

            <button type="button" 
                    @click="clearAllCache"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium border border-red-300 text-red-700 bg-white hover:bg-red-50 transition-colors duration-150">
                Clear Cache & Re-sync
            </button>
        </div>

        <!-- Three Column Layout for Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- 1. Outpatient (OPD) List -->
            <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden h-fit">
                <h3 class="p-4 text-lg font-semibold bg-green-50 text-green-800 border-b border-green-200">Outpatient Register</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">P. Name</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">N.O.K. Name</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-if="opdFiltered.length === 0">
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-500 text-xs">No OPD records <span x-show="searchQuery">matching filter</span>.</td>
                                </tr>
                            </template>
                            <template x-for="(r, index) in opdFiltered" :key="index + '-opd'">
                                <tr class="hover:bg-green-50 transition-colors duration-100">
                                    <td class="px-3 py-3 text-gray-900 font-medium" x-text="r.PatientName || 'N/A'"></td>
                                    <td class="px-3 py-3 text-gray-700 text-xs" x-text="r.NextOfKin || 'N/A'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Ward (IPD) List -->
            <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden h-fit">
                <h3 class="p-4 text-lg font-semibold bg-red-50 text-red-800 border-b border-red-200">Ward Register</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">P. Name / Ward</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">N.O.K.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-if="wardFiltered.length === 0">
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-500 text-xs">No Ward records <span x-show="searchQuery">matching filter</span>.</td>
                                </tr>
                            </template>
                            <template x-for="(r, index) in wardFiltered" :key="index + '-ward'">
                                <tr class="hover:bg-red-50 transition-colors duration-100">
                                    <td class="px-3 py-3 text-gray-900">
                                        <div class="font-medium" x-text="r.PatientName || 'N/A'"></div>
                                        <div class="text-xs text-red-600 font-mono" 
                                             x-text="(r.Location || 'N/A') + (r.Details && r.Details !== 'N/A' ? ' (Bed: ' + r.Details + ')' : '')">
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-gray-700 text-xs" x-text="r.NextOfKin || 'N/A'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- 3. Theatre / Daycase List -->
            <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden h-fit">
                <h3 class="p-4 text-lg font-semibold bg-blue-50 text-blue-800 border-b border-blue-200 flex justify-between items-center">
                    <span>Theatre / Daycase Register</span>
                    <span class="text-xs text-blue-600" x-show="!isLoading" x-text="'(' + theatreFiltered.length + ')'"></span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">P. Name / Procedure</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-1/2">Room</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-if="theatreFiltered.length === 0">
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-500 text-xs">No Theatre/Daycase records <span x-show="searchQuery">matching filter</span>.</td>
                                </tr>
                            </template>
                            <template x-for="(r, index) in theatreFiltered" :key="index + '-theatre'">
                                <tr class="hover:bg-blue-50 transition-colors duration-100">
                                    <td class="px-3 py-3 text-gray-900">
                                        <div class="font-medium" x-text="r.PatientName || 'N/A'"></div>
                                        <div class="text-xs text-blue-600 font-mono" x-text="r.Details || 'N/A'"></div>
                                    </td>
                                    <td class="px-3 py-3 text-gray-700 text-xs" x-text="r.Location || 'Theatre'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- General Loading State -->
        <template x-if="isLoading">
            <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden mt-6">
                <div class="px-4 py-12 flex flex-col items-center justify-center gap-3">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm">Loading combined register data...</p>
                </div>
            </div>
        </template>
    </div>

    <script>
        function combinedRegister() {
            return {
                allRecords: [],
                filteredRecords: [],
                searchQuery: '',
                isLoading: false,
                autoRefresh: true,
                cacheStatus: null,
                lastFetched: null,
                debounceTimer: null,
                pollInterval: null,
                routeFetch: '{{ route("admin.hmis.combined.fetch") }}',
                routeClearCache: '{{ route("admin.hmis.combined.clear-cache") }}',

                get totalCount() {
                    return this.allRecords.length;
                },

                get filteredCount() {
                    return this.filteredRecords.length;
                },
                
                get opdFiltered() {
                    return this.filteredRecords.filter(r => r.Type === 'OPD');
                },
                
                get wardFiltered() {
                    return this.filteredRecords.filter(r => r.Type === 'WARD');
                },

                get theatreFiltered() {
                    // Filter for Theatre records – primary on Type, with fallbacks
                    let filtered = this.filteredRecords.filter(r => 
                        r.Type === 'THEATRE' || 
                        r.Type === 'DAYCASE' || 
                        (r.Type === 'WARD' && (r.Location || '').toUpperCase().includes('THEATRE')) ||
                        (r.Details || '').toUpperCase().includes('THEATRE')
                    );
                    // Debug: Log a sample of theatreFiltered to console whenever it changes
                    if (filtered.length > 0 && this.allRecords.length > 0) {
                        console.log('Theatre Filtered Sample (first 3):', filtered.slice(0, 3).map(r => ({
                            PatientName: r.PatientName,
                            PatientNumber: r.PatientNumber,
                            Type: r.Type,
                            Location: r.Location,
                            Details: r.Details,
                            DateTimeIn: r.DateTimeIn
                        })));
                    }
                    return filtered;
                },

                formatDateOnly(dateString) {
                    if (!dateString) return 'N/A';
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', { 
                            year: 'numeric', month: 'short', day: 'numeric',
                            hour: '2-digit', minute: '2-digit'
                        });
                    } catch (e) {
                        return dateString;
                    }
                },

                async init() {
                    const cached = sessionStorage.getItem('combined_register_data');
                    const cachedTime = sessionStorage.getItem('combined_register_time');

                    if (cached && cachedTime) {
                        const cacheAge = Date.now() - parseInt(cachedTime);
                        if (cacheAge < 300000) { 
                            this.allRecords = JSON.parse(cached);
                            this.filteredRecords = [...this.allRecords];
                            this.lastFetched = this.formatTime(parseInt(cachedTime));
                            // Debug: Log cached data types
                            console.log('Cached allRecords (sample types):', this.allRecords.slice(0, 5).map(r => ({ PatientName: r.PatientName, Type: r.Type, Location: r.Location, Details: r.Details })));
                            setTimeout(() => this.manualRefresh(true), 1000);
                            if (this.autoRefresh) this.startAutoRefresh();
                            return;
                        }
                    }

                    await this.manualRefresh();
                    if (this.autoRefresh) this.startAutoRefresh();
                },

                async manualRefresh(silent = false) {
                    if (!silent) this.isLoading = true;

                    try {
                        const response = await fetch(this.routeFetch);
                        const data = await response.json();

                        if (data.success) {
                            this.allRecords = data.records;
                            this.filteredRecords = [...this.allRecords];

                            sessionStorage.setItem('combined_register_data', JSON.stringify(this.allRecords));
                            sessionStorage.setItem('combined_register_time', Date.now().toString());
                            this.lastFetched = this.formatTime(Date.now());
                            this.instantSearch(0);

                            // Debug: Log fresh data types (first 10 records for brevity)
                            console.log('Fresh allRecords from backend (sample types):', this.allRecords.slice(0, 10).map(r => ({ PatientName: r.PatientName, Type: r.Type, Location: r.Location, Details: r.Details })));
                            console.log('Unique Types found:', [...new Set(this.allRecords.map(r => r.Type))]);

                            setTimeout(() => { this.cacheStatus = null; }, 3000);
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                        if (!silent) {
                            this.cacheStatus = 'error';
                            setTimeout(() => this.cacheStatus = null, 5000);
                        }
                    } finally {
                        this.isLoading = false;
                    }
                },

                instantSearch(delay = 150) {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        const q = this.searchQuery.toLowerCase().trim();

                        if (!q) {
                            this.filteredRecords = [...this.allRecords];
                        } else {
                            this.filteredRecords = this.allRecords.filter(r => {
                                const fields = [
                                    r.PatientName, r.PatientNumber, r.NextOfKin,
                                    r.Location, r.Details, r.Type, r.Branch
                                ];
                                // Enhanced: Weight theatre-specific search (e.g., Details for procedures)
                                const score = fields.reduce((acc, f) => acc + ((f || '').toString().toLowerCase().includes(q) ? 1 : 0), 0);
                                return score > 0;
                            });
                        }
                        // Trigger re-evaluation of getters (Alpine will reactively update)
                    }, delay);
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
                        clearInterval(this.pollInterval);
                        this.pollInterval = null;
                    }
                },

                async clearAllCache() {
                    sessionStorage.removeItem('combined_register_data');
                    sessionStorage.removeItem('combined_register_time');

                    this.isLoading = true;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    try {
                        await fetch(this.routeClearCache, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken }
                        });
                    } catch (e) {
                        console.error('Failed to clear backend cache:', e);
                    }

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
@endadmin