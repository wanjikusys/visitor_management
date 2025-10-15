<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Vehicle Management — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6"
         x-data="vehicleManagement()"
         x-init="init()">

        <!-- Header Actions -->
        <div class="mb-4 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                <span class="font-medium text-gray-900">Checked-in Vehicles:</span> 
                <span class="text-indigo-600 font-medium" x-text="vehicles.length"></span>
            </div>
            <button @click="openAddModal" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Vehicle
            </button>
        </div>

        <!-- Vehicles Table -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Card No / Driver</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Registration</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Purpose</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Time In</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Passengers</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider" style="min-width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <!-- Loading State -->
                        <template x-if="isLoading">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-gray-600 text-sm mt-2">Loading vehicles...</p>
                                </td>
                            </tr>
                        </template>

                        <!-- Data Rows -->
                        <template x-if="!isLoading && vehicles.length > 0">
                            <template x-for="vehicle in vehicles" :key="vehicle.id">
                                <tr class="hover:bg-blue-50 transition-colors duration-100">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900" x-text="vehicle.card_no + ' - ' + vehicle.driver_name"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="vehicle.registration"></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 font-mono text-xs" x-text="vehicle.phone_number"></td>
                                    <td class="px-4 py-3 text-gray-700 text-xs" x-text="vehicle.visit_purpose"></td>
                                    <td class="px-4 py-3 text-gray-700 text-xs" x-text="formatDateTime(vehicle.time_in)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-text="vehicle.passengers"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openDetailsModal(vehicle)" 
                                                    class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Details
                                            </button>
                                            <button @click="openEditModal(vehicle)" 
                                                    class="px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button @click="checkoutVehicle(vehicle.id)" 
                                                    class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition whitespace-nowrap">
                                                Check-out
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>

                        <!-- Empty State -->
                        <template x-if="!isLoading && vehicles.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-gray-500 text-sm">No vehicles checked in</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Vehicle Modal -->
        <div x-show="showModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                     @click="showModal = false"></div>

                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-lg w-full">
                    
                    <form @submit.prevent="submitVehicle">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white" x-text="isEdit ? 'Edit Vehicle' : 'Add Vehicle'"></h3>
                                <button type="button" @click="showModal = false" class="text-white hover:text-gray-200 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Card No <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="vehicleForm.card_no" 
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                           placeholder="Enter card number">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Driver Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="vehicleForm.driver_name" 
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                           placeholder="Driver's full name">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Registration <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="vehicleForm.registration" 
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                           placeholder="KXX 123Y">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                           x-model="vehicleForm.phone_number"
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                           placeholder="0712345678">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Passengers <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           x-model="vehicleForm.passengers"
                                           required
                                           min="1"
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                           placeholder="1">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Visit Purpose <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="vehicleForm.visit_purpose"
                                           required
                                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                           placeholder="Purpose of visit">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Notes (Optional)
                                    </label>
                                    <textarea x-model="vehicleForm.notes"
                                              rows="2"
                                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                              placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                            <button type="button" 
                                    @click="showModal = false"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting" x-text="isEdit ? 'Update Vehicle' : 'Add Vehicle'"></span>
                                <span x-show="isSubmitting">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div x-show="showDetailsModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div x-show="showDetailsModal"
                     x-transition
                     class="fixed inset-0 bg-gray-900 bg-opacity-75"
                     @click="showDetailsModal = false"></div>

                <div x-show="showDetailsModal"
                     x-transition
                     class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-lg w-full">
                    
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white">Vehicle Details</h3>
                            <button type="button" @click="showDetailsModal = false" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Card No / Driver</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900" x-text="selectedVehicle?.card_no + ' - ' + selectedVehicle?.driver_name"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Registration</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900" x-text="selectedVehicle?.registration"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Contact</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900" x-text="selectedVehicle?.phone_number"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Purpose</dt>
                                <dd class="mt-1 text-sm text-gray-900" x-text="selectedVehicle?.visit_purpose"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Passengers</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900" x-text="selectedVehicle?.passengers"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Time In</dt>
                                <dd class="mt-1 text-sm text-gray-900" x-text="formatDateTime(selectedVehicle?.time_in)"></dd>
                            </div>
                            <div x-show="selectedVehicle?.notes">
                                <dt class="text-xs font-semibold text-gray-500 uppercase">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900" x-text="selectedVehicle?.notes"></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end">
                        <button type="button" 
                                @click="showDetailsModal = false"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function vehicleManagement() {
        return {
            vehicles: [],
            isLoading: false,
            isSubmitting: false,
            showModal: false,
            showDetailsModal: false,
            isEdit: false,
            editingId: null,
            selectedVehicle: null,
            vehicleForm: {
                card_no: '',
                driver_name: '',
                registration: '',
                phone_number: '',
                visit_purpose: '',
                passengers: 1,
                notes: ''
            },

            async init() {
                await this.loadVehicles();
            },

            async loadVehicles() {
                this.isLoading = true;
                try {
                    const response = await fetch('{{ route("admin.hmis.vehicles.checked-in") }}');
                    const data = await response.json();
                    if (data.success) {
                        this.vehicles = data.vehicles;
                    }
                } catch (error) {
                    console.error('Error loading vehicles:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            openAddModal() {
                this.isEdit = false;
                this.editingId = null;
                this.vehicleForm = {
                    card_no: '',
                    driver_name: '',
                    registration: '',
                    phone_number: '',
                    visit_purpose: '',
                    passengers: 1,
                    notes: ''
                };
                this.showModal = true;
            },

            openEditModal(vehicle) {
                this.isEdit = true;
                this.editingId = vehicle.id;
                this.vehicleForm = {
                    card_no: vehicle.card_no,
                    driver_name: vehicle.driver_name,
                    registration: vehicle.registration,
                    phone_number: vehicle.phone_number,
                    visit_purpose: vehicle.visit_purpose,
                    passengers: vehicle.passengers,
                    notes: vehicle.notes || ''
                };
                this.showModal = true;
            },

            openDetailsModal(vehicle) {
                this.selectedVehicle = vehicle;
                this.showDetailsModal = true;
            },

            async submitVehicle() {
                this.isSubmitting = true;

                try {
                    const url = this.isEdit 
                        ? `{{ url('admin/hmis/vehicles') }}/${this.editingId}`
                        : '{{ route("admin.hmis.vehicles.store") }}';
                    
                    const method = this.isEdit ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.vehicleForm)
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(this.isEdit ? '✅ Vehicle updated successfully!' : '✅ Vehicle checked in successfully!');
                        this.showModal = false;
                        await this.loadVehicles();
                    } else {
                        alert('❌ Operation failed');
                    }
                } catch (error) {
                    console.error('Submission error:', error);
                    alert('Error: ' + error.message);
                } finally {
                    this.isSubmitting = false;
                }
            },

            async checkoutVehicle(vehicleId) {
                if (!confirm('Are you sure you want to check-out this vehicle?')) {
                    return;
                }

                try {
                    const response = await fetch(`{{ url('admin/hmis/vehicles') }}/${vehicleId}/checkout`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ Vehicle checked out successfully!');
                        await this.loadVehicles();
                    } else {
                        alert('❌ Failed to check-out vehicle');
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    alert('Error: ' + error.message);
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
            }
        }
    }
    </script>
</x-app-layout>
