<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Ciudades</h1>
                    <p class="text-sm text-gray-600">Gestión de ciudades para clientes y usuarios</p>
                </div>
                <div>
                    <flux:button icon="plus" size="xs" variant="outline" wire:click="openCreateModal">
                        Nueva ciudad
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar ciudades..." />
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="$set('search', '')">
                        Limpiar búsqueda
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Ciudades -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ciudad
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($cities as $city)
                            <tr wire:key="city-{{ $city->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <flux:icon name="building-office" class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $city->name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $city->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openEditModal({{ $city->id }})">
                                            <flux:icon name="pencil" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="outline"
                                            wire:click="deleteCity({{ $city->id }})">
                                            <flux:icon name="trash" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="building-office" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron ciudades</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($cities->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $cities->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Crear/Editar -->
    <flux:modal variant="flyout" wire:model="showCityModal" size="md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">
                    {{ $editingCity ? 'Editar ciudad' : 'Nueva ciudad' }}
                </h3>
            </div>

            <form wire:submit.prevent="saveCity">
                <div class="grid grid-cols-1 gap-2">
                    <flux:input wire:model="name" size="xs" placeholder="Nombre de ciudad *" class="w-full" />
                    @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <flux:button type="button" variant="outline" size="xs" wire:click="closeCityModal">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" color="primary" size="xs" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove>
                            {{ $editingCity ? 'Actualizar' : 'Crear' }}
                        </span>
                        <span wire:loading>
                            <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                            {{ $editingCity ? 'Actualizando...' : 'Creando...' }}
                        </span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
