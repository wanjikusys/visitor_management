<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Carers Registration — KIJABE Branch
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6"
         x-data="carersManagement()"
         x-init="init()">

        <!-- Registration Form Card -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">Register New Carer</h3>
            </div>

            <div class="px-6 py-5">
                <form @submit.prevent="submitCarer">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Patient Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Baby/Patient Admitted <span class="text-red-500">*</span>
                            </label>
                            <select x-model="carerForm.patient_number" 
                                    @change="selectPatient"
                                    required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition">
                                <option value="">-- Select Patient --</option>
                                <template x-for="patient in patients" :key="patient.PatientNumber">
                                    <option :value="patient.PatientNumber" 
                                            x-text="`${patient.PatientName} (${patient.PatientNumber}) - Ward: ${patient.WardNumber}, Bed: ${patient.BedNumber}`"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Carer Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Carer Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="carerForm.carer_name" 
                                   required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition"
                                   placeholder="Enter carer's full name">
                        </div>

                        <!-- Carer Contact -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Carer Contact <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   x-model="carerForm.carer_contact"
                                   required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition"
                                   placeholder="0712345678">
                        </div>

                        <!-- ID Number -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                ID Number
                            </label>
                            <input type="text" 
                                   x-model="carerForm.carer_id_number"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition"
                                   placeholder="National ID or Passport">
                        </div>

                        <!-- Relationship -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Relationship to Patient
                            </label>
                            <input type="text" 
                                   x-model="carerForm.relationship"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition"
                                   placeholder="e.g., Mother, Father, Guardian">
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Notes (Optional)
                            </label>
                            <textarea x-model="carerForm.notes"
                                      rows="2"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition"
                                      placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Register Carer
                            </span>
                            <span x-show="isSubmitting" class="flex items-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Registering...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Carers Table -->
        <div class="bg-white shadow-lg ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Registered Carers</h3>
                    <button @click="loadActiveCarers" 
                            class="px-4 py-2 text-sm font-medium text-purple-600 bg-white rounded-lg hover:bg-gray-100 transition">
                        Refresh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Patient No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ward/Bed</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Carer Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date In</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <!-- Loading State -->
                        <template x-if="isLoading">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-gray-600 text-sm mt-2">Loading carers...</p>
                                </td>
                            </tr>
                        </template>

                        <!-- Data Rows -->
                        <template x-if="!isLoading && activeCarers.length > 0">
                            <template x-for="carer in activeCarers" :key="carer.id">
                                <tr class="hover:bg-purple-50 transition-colors duration-100">
                                    <td class="px-4 py-3 text-gray-900 font-medium" x-text="carer.patient_name"></td>
                                    <td class="px-4 py-3 text-gray-700 font-mono text-xs" x-text="carer.patient_number"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800" x-text="carer.ward"></span>
                                            <span class="text-gray-400">/</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800" x-text="carer.bed_number"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 font-medium" x-text="carer.carer_name"></td>
                                    <td class="px-4 py-3 text-gray-700 font-mono text-xs" x-text="carer.carer_contact"></td>
                                    <td class="px-4 py-3 text-gray-700 text-xs" x-text="formatDateTime(carer.date_in)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="checkoutCarer(carer.id)" 
                                                class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition">
                                            Check-out
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </template>

                        <!-- Empty State -->
                        <template x-if="!isLoading && activeCarers.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-sm">No active carers registered</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function carersManagement() {
        return {
            patients: [],
            activeCarers: [],
            isLoading: false,
            isSubmitting: false,
            carerForm: {
                patient_number: '',
                patient_name: '',
                ward: '',
                bed_number: '',
                carer_name: '',
                carer_contact: '',
                carer_id_number: '',
                relationship: '',
                notes: ''
            },

            async init() {
                await this.loadPatients();
                await this.loadActiveCarers();
            },

            async loadPatients() {
                try {
                    const response = await fetch('{{ route("admin.hmis.carers.patients") }}');
                    const data = await response.json();
                    if (data.success) {
                        this.patients = data.patients;
                    }
                } catch (error) {
                    console.error('Error loading patients:', error);
                }
            },

            selectPatient() {
                const selected = this.patients.find(p => p.PatientNumber === this.carerForm.patient_number);
                if (selected) {
                    this.carerForm.patient_name = selected.PatientName;
                    this.carerForm.ward = selected.WardNumber;
                    this.carerForm.bed_number = selected.BedNumber;
                }
            },

            async submitCarer() {
                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route("admin.hmis.carers.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.carerForm)
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ Carer registered successfully!');
                        this.carerForm = {
                            patient_number: '',
                            patient_name: '',
                            ward: '',
                            bed_number: '',
                            carer_name: '',
                            carer_contact: '',
                            carer_id_number: '',
                            relationship: '',
                            notes: ''
                        };
                        await this.loadActiveCarers();
                    } else {
                        alert('❌ Failed to register carer');
                    }
                } catch (error) {
                    console.error('Registration error:', error);
                    alert('Error: ' + error.message);
                } finally {
                    this.isSubmitting = false;
                }
            },

            async loadActiveCarers() {
                this.isLoading = true;

                try {
                    const response = await fetch('{{ route("admin.hmis.carers.active") }}');
                    const data = await response.json();

                    if (data.success) {
                        this.activeCarers = data.carers;
                    }
                } catch (error) {
                    console.error('Error loading carers:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            async checkoutCarer(carerId) {
                if (!confirm('Are you sure you want to check-out this carer?')) {
                    return;
                }

                try {
                    const response = await fetch(`{{ url('admin/hmis/carers') }}/${carerId}/checkout`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ Carer checked out successfully!');
                        await this.loadActiveCarers();
                    } else {
                        alert('❌ Failed to check-out carer');
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
