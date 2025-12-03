{{-- resources/views/admin/roles/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Role: ') }} <span class="font-mono text-blue-600">{{ $role->display_name }}</span>
            </h2>
            <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Back to Roles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success / Error Messages -->
            @if(session('status'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" x-data="{ selectAll: false, moduleStates: {} }">
                @csrf
                @method('PUT')

                <!-- Role Basic Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Role Information</h3>
                    </div>
                    <div class="p-6 space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Display Name <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="display_name" id="display_name" required
                                       value="{{ old('display_name', $role->display_name) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('display_name') border-red-500 @enderror">
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    System Name (slug) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $role->name) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Lowercase, no spaces (e.g. admin, editor)</p>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" id="description" rows="3"
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $role->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Permissions</h3>

                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-gray-700">Select / Deselect All</span>
                        </label>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($permissions as $module => $permissionList)
                                <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow"
                                     x-data="{ moduleChecked: {{ in_array(true, $permissionList->pluck('id')->map(fn($id) => in_array($id, $rolePermissions))->toArray()) ? 'true' : 'false' }} }">

                                    <div class="flex justify-between items-center mb-4">
                                        <h5 class="text-lg font-semibold text-primary">
                                            {{ ucwords(str_replace('_', ' ', $module)) }}
                                        </h5>
                                        <input type="checkbox"
                                               :checked="allModulePermissionsChecked('{{ $module }}')"
                                               @change="toggleModule('{{ $module }}', $event.target.checked)"
                                               class="rounded border-gray-300 text-blue-600">
                                    </div>

                                    <div class="space-y-3">
                                        @foreach ($permissionList as $permission)
                                            <label class="flex items-center space-x-3 cursor-pointer">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission->id }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                       class="permission-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                       x-on:change="updateModuleState('{{ $module }}')">
                                                <span class="text-sm text-gray-700">{{ $permission->display_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.roles.index') }}"
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        <i class="fas fa-save mr-2"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateSelectAllState() {
                const all = document.querySelectorAll('input[name="permissions[]"]');
                const checked = document.querySelectorAll('input[name="permissions[]"]:checked');
                const selectAll = document.querySelector('[x-model="selectAll"]');
                selectAll.checked = all.length === checked.length;
                selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
            }

            document.addEventListener('alpine:init', () => {
                Alpine.data('roleForm', () => ({
                    selectAll: false,

                    toggleAll() {
                        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                            cb.checked = this.selectAll;
                        });
                    },

                    allModulePermissionsChecked(module) {
                        const perms = document.querySelectorAll(`input[name="permissions[]"][value*="${module}"]`);
                        return perms.length > 0 && [...perms].every(cb => cb.checked);
                    },

                    toggleModule(module, checked) {
                        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                            @foreach ($permissions as $mod => $list)
                                if ('{{ $mod }}' === module && cb.closest('.border') === cb.closest('.border')) {
                                    cb.checked = checked;
                                }
                            @endforeach
                        });
                        updateSelectAllState();
                    },

                    updateModuleState(module) {
                        updateSelectAllState();
                    },

                    init() {
                        updateSelectAllState();
                        // Watch any permission change
                        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                            cb.addEventListener('change', updateSelectAllState);
                        });
                    }
                }));
            });

            document.addEventListener('DOMContentLoaded', () => {
                updateSelectAllState();
            });
        </script>
    @endpush
</x-app-layout>