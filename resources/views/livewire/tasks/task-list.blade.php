<div class="space-y-4">
    <div class="bg-white shadow rounded-lg border border-gray-100 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <flux:input size="xs" label="Buscar" placeholder="Título o descripción" wire:model.debounce.400ms="search" />
            </div>
            <div>
                <flux:select size="xs" label="Cliente" wire:model="clientFilter">
                    <option value="">Todos</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select size="xs" label="Estado" wire:model="statusFilter">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En progreso</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                </flux:select>
            </div>
            <div>
                <flux:select size="xs" label="Prioridad" wire:model="priorityFilter">
                    <option value="">Todas</option>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                </flux:select>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-600">{{ $task->due_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-800">{{ $task->title }}</td>
                            <td class="px-4 py-2 text-xs text-gray-600">{{ $task->client?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-xs text-gray-600">{{ ucfirst($task->priority) }}</td>
                            <td class="px-4 py-2 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">{{ ucfirst($task->status) }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <flux:button icon="eye" size="xs" variant="outline" wire:click="$dispatch('show-info', { message: 'Detalle aún no implementado' })" />
                                    <flux:button icon="trash" size="xs" variant="danger" color="danger" wire:click="confirmDelete({{ $task->id }})" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400 text-sm">No hay tareas para los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2 border-t border-gray-100">
            {{ $tasks->links() }}
        </div>
    </div>

    <flux:modal wire:model="showDeleteModal" class="w-96">
        <div class="p-4">
            <h3 class="text-base font-semibold text-gray-900 mb-2">Confirmar eliminación</h3>
            <p class="text-sm text-gray-600">¿Seguro que deseas eliminar esta tarea? Esta acción no se puede deshacer.</p>
            <div class="mt-4 flex justify-end space-x-2">
                <flux:button size="xs" variant="outline" icon="x-mark" wire:click="closeDeleteModal">Cancelar</flux:button>
                <flux:button size="xs" color="danger" icon="trash" wire:click="deleteTask">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
