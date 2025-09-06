<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Roles</h1>
                    <p class="text-sm text-gray-600">Gestión de roles y permisos del CRM</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Mensajes de éxito -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar roles..." />
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="$set('search', '')">
                        Limpiar búsqueda
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Roles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rol
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Permisos
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($roles as $role)
                            <tr wire:key="role-{{ $role->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <flux:icon name="shield-check" class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 capitalize">
                                                {{ $role->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: {{ $role->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $role->permissions->count() }} permisos asignados
                                    </div>
                                    @if ($role->permissions->count() > 0)
                                        <div class="text-xs text-gray-500 mt-1">
                                            @foreach ($role->permissions->take(3) as $permission)
                                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                                    {{ str_replace('_', ' ', $permission->name) }}
                                                </span>
                                            @endforeach
                                            @if ($role->permissions->count() > 3)
                                                <span class="text-gray-400">+{{ $role->permissions->count() - 3 }} más</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openPermissionsModal({{ $role->id }})">
                                            <flux:icon name="cog-6-tooth" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="shield-check" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron roles</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($roles->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>

    @if ($selectedRole)
    <!-- Modal de Permisos -->
    <flux:modal variant="flyout" wire:model="showPermissionsModal" size="lg">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">
                    Gestionar Permisos - {{ ucfirst($selectedRole->name) }}
                </h3>
            </div>

            @if ($showPermissionsModal && $selectedRole)
                <!-- Lista de permisos organizados por categorías -->
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @php
                        $permissionsByCategory = $permissions->groupBy(function ($permission) {
                            $parts = explode('_', $permission->name);
                            return $parts[0];
                        });
                    @endphp

                    @foreach ($permissionsByCategory as $category => $categoryPermissions)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-gray-900 mb-2 capitalize">
                                {{ str_replace('_', ' ', $category) }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach ($categoryPermissions as $permission)
                                    <label class="flex items-center space-x-2 p-2 rounded border border-gray-200 hover:bg-gray-50 cursor-pointer text-xs">
                                        <flux:checkbox 
                                            wire:model="selectedPermissions" 
                                            value="{{ $permission->id }}"
                                            size="xs"
                                        />
                                        <span class="text-xs font-medium text-gray-900">
                                            {{ str_replace('_', ' ', $permission->name) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <flux:button type="button" variant="outline" size="xs" wire:click="closePermissionsModal">
                        Cancelar
                    </flux:button>
                    <flux:button type="button" color="primary" size="xs" wire:click="savePermissions" 
                        wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove>Guardar Permisos</span>
                        <span wire:loading>
                            <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                            Guardando...
                        </span>
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
    @endif
</div>
