<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Dateros</h1>
                    <p class="text-sm text-gray-600">Lista de usuarios dateros</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar dateros..." />
                </div>
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="all">Todos los estados</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </flux:select>
                </div>
                @if ((Auth::user()->isAdmin() || Auth::user()->isLider()) && $vendedores && $vendedores->count() > 0)
                    <div>
                        <flux:select size="xs" wire:model.live="vendedorFilter">
                            <option value="">Todos los vendedores</option>
                            @foreach ($vendedores as $vendedor)
                                <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                @endif
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Dateros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Datero</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Contacto</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Vendedor</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Registro</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($dateros as $datero)
                            <tr wire:key="datero-{{ $datero->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 font-semibold text-xs">
                                                {{ $datero->initials() }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $datero->name }}</div>
                                            <div class="text-gray-500">{{ $datero->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($datero->phone)
                                        <div class="text-gray-900">{{ $datero->phone }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($datero->lider)
                                        <div class="flex items-center space-x-1">
                                            <span class="text-gray-900">{{ $datero->lider->name }}</span>
                                            @if($datero->lider->hasRole('vendedor'))
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Vendedor
                                                </span>
                                            @elseif($datero->lider->hasRole('lider'))
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Líder
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($datero->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-500">
                                    <div class="text-xs">
                                        {{ $datero->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $datero->created_at->format('H:i') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="user-group" class="w-12 h-12 text-gray-400 mb-2" />
                                        <p class="text-sm">No se encontraron dateros</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($dateros->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $dateros->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
