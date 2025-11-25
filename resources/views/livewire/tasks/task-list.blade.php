<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Tareas</h1>
                    <p class="text-sm text-gray-600">Gestión de tareas del CRM</p>
                </div>
                <div class="flex space-x-2">
                    <flux:button icon="plus" size="xs" color="primary" wire:click="$dispatch('show-info', { message: 'Crear tarea aún no implementado' })">
                        Nueva Tarea
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <flux:input size="xs" icon="magnifying-glass" type="search" label="Buscar" placeholder="Título o descripción" wire:model.debounce.400ms="search" />
                </div>
                <div>
                    <flux:select size="xs" icon="users" label="Cliente" wire:model.live="clientFilter">
                        <option value="">Todos los clientes</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" label="Estado" wire:model.live="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>
                <div>
                    <flux:select size="xs" label="Prioridad" wire:model.live="priorityFilter">
                        <option value="">Todas las prioridades</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </flux:select>
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Tareas Compacta -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Vencimiento</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Título</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Prioridad</th>
                            <th class="px-2 py-2 text-left font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-2 py-2 text-right font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($tasks as $task)
                            <tr wire:key="task-{{ $task->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    @if($task->due_date)
                                        <div class="font-medium {{ $task->due_date->isPast() && $task->status !== 'completada' ? 'text-red-600' : '' }}">
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </div>
                                        @if($task->due_date->isPast() && $task->status !== 'completada')
                                            <div class="text-[10px] text-red-400">Vencida</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Sin fecha</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <div class="font-medium text-gray-900">{{ $task->title }}</div>
                                    @if($task->description)
                                        <div class="text-[10px] text-gray-400 truncate max-w-xs">{{ Str::limit($task->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-600">
                                    {{ $task->client?->name ?? 'N/A' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                                        {{ $task->priority === 'urgente'
                                            ? 'bg-red-100 text-red-800'
                                            : ($task->priority === 'alta'
                                                ? 'bg-orange-100 text-orange-800'
                                                : ($task->priority === 'media'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                                        {{ $task->status === 'pendiente'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($task->status === 'en_progreso'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($task->status === 'completada'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="flex justify-end space-x-1">
                                        <flux:button size="xs" variant="outline" wire:click="$dispatch('show-info', { message: 'Detalle aún no implementado' })">
                                            <flux:icon name="eye" class="w-3 h-3" />
                                        </flux:button>
                                        <flux:button size="xs" variant="danger" color="danger" wire:click="confirmDelete({{ $task->id }})">
                                            <flux:icon name="trash" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="clipboard-document-list" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron tareas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            @if ($tasks->hasPages())
                <div class="bg-white px-2 py-2 border-t border-gray-200">
                    {{ $tasks->links() }}
                </div>
            @endif
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

    <!-- Notificaciones - Parte superior derecha -->
    <div id="notification-container" class="fixed top-4 right-4 z-[9999] hidden">
        <div id="notification-content" class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 translate-x-full opacity-0">
            <div class="p-6 border-l-4" id="notification-border">
                <div class="flex items-start space-x-4">
                    <div id="notification-icon" class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"></div>
                    </div>
                    <div class="flex-1">
                        <h3 id="notification-title" class="text-lg font-semibold mb-1"></h3>
                        <p id="notification-message" class="text-gray-600 text-sm"></p>
                    </div>
                    <button id="notification-close" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para notificaciones -->
    <script>
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notification-container');
            const content = document.getElementById('notification-content');
            const icon = document.getElementById('notification-icon').querySelector('div');
            const title = document.getElementById('notification-title');
            const messageEl = document.getElementById('notification-message');
            const closeBtn = document.getElementById('notification-close');
            const border = document.getElementById('notification-border');

            let iconClass, iconText, titleText, borderColor, titleColor;
            if (type === 'success') {
                iconClass = 'bg-green-100 text-green-600';
                iconText = '✓';
                titleText = '¡Éxito!';
                borderColor = 'border-green-500';
                titleColor = 'text-green-700';
            } else if (type === 'error') {
                iconClass = 'bg-red-100 text-red-600';
                iconText = '✕';
                titleText = 'Error';
                borderColor = 'border-red-500';
                titleColor = 'text-red-700';
            } else {
                iconClass = 'bg-blue-100 text-blue-600';
                iconText = 'ℹ';
                titleText = 'Información';
                borderColor = 'border-blue-500';
                titleColor = 'text-blue-700';
            }

            icon.className = `w-12 h-12 rounded-full flex items-center justify-center text-2xl ${iconClass}`;
            icon.textContent = iconText;
            title.textContent = titleText;
            title.className = `text-lg font-semibold mb-1 ${titleColor}`;
            messageEl.textContent = message;
            border.className = `p-6 border-l-4 ${borderColor}`;

            container.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-x-full', 'opacity-0');
                content.classList.add('translate-x-0', 'opacity-100');
            }, 10);

            const closeNotification = () => {
                content.classList.remove('translate-x-0', 'opacity-100');
                content.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    container.classList.add('hidden');
                }, 300);
            };

            closeBtn.onclick = closeNotification;
            setTimeout(closeNotification, 5000);
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('show-success', (event) => {
                showNotification(event.message, 'success');
            });

            Livewire.on('show-error', (event) => {
                showNotification(event.message, 'error');
            });

            Livewire.on('show-info', (event) => {
                showNotification(event.message, 'info');
            });
        });
    </script>
</div>
