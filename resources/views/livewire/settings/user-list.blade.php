<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Usuarios</h1>
                    <p class="text-sm text-gray-600">Gestión de usuarios y asignación de roles del CRM</p>
                </div>
                <div>
                    <flux:button icon="user-plus" size="xs" variant="outline" wire:click="createUser">
                        Crear Usuario
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Los mensajes ahora se muestran con SweetAlert2 -->

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Búsqueda -->
                <div>
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar usuarios..." />
                </div>

                <!-- Filtro por Estado -->
                <div>
                    <flux:select size="xs" wire:model.live="statusFilter">
                        <option value="all">Todos los estados</option>
                        <option value="active">Solo activos</option>
                        <option value="inactive">Solo inactivos</option>
                    </flux:select>
                </div>

                <!-- Filtro por Rol -->
                <div>
                    <flux:select size="xs" wire:model.live="roleFilter">
                        <option value="all">Todos los roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ Str::title($role->name) }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Filtro por Líder -->
                <div>
                    <flux:select size="xs" wire:model.live="leaderFilter">
                        <option value="all">Todos los líderes</option>
                        <option value="no_leader">Sin líder asignado</option>
                        @foreach ($leaders as $leader)
                            <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Botón Limpiar -->
                <div>
                    <flux:button icon="x-mark" size="xs" variant="outline" wire:click="clearFilters">
                        Limpiar filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuario
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contacto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rol Actual
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Líder Asignado
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}"
                                class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-green-600">
                                                {{ $user->initials() }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    @if ($user->phone)
                                        <div class="text-xs text-gray-500">{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($user->roles->isNotEmpty())
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ match ($role->name) {
                                                    'admin' => 'bg-red-100 text-red-800',
                                                    'lider' => 'bg-blue-100 text-blue-800',
                                                    'vendedor' => 'bg-green-100 text-green-800',
                                                    'cliente' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                } }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-gray-500">Sin rol asignado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($user->lider)
                                        <div class="flex items-center">
                                            <div
                                                class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs font-medium text-blue-600">
                                                    {{ Str::substr($user->lider->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->lider->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $user->lider->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        @if ($user->hasRole(['admin', 'lider']))
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Es Líder
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">Sin líder asignado</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($user->isActive())
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <flux:icon name="check-circle" class="w-3 h-3 mr-1" />
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <flux:icon name="x-circle" class="w-3 h-3 mr-1" />
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-1">
                                        @if($user->isDatero())
                                            <flux:button icon="qr-code" size="xs" variant="outline"
                                                wire:click="verQR({{ $user->id }})" title="Ver QR">
                                            </flux:button>
                                        @endif
                                        <flux:button icon="pencil" size="xs" variant="outline"
                                            wire:click="openUserModal({{ $user->id }})" title="Editar usuario">
                                        </flux:button>

                                        @if ($user->isActive())
                                            <flux:button icon="x-circle" size="xs" variant="outline" color="red"
                                                wire:click="confirmDeactivate({{ $user->id }})"
                                                title="Desactivar usuario">
                                            </flux:button>
                                        @else
                                            <flux:button icon="check-circle" size="xs" variant="outline"
                                                color="green" wire:click="confirmActivate({{ $user->id }})"
                                                title="Activar usuario">
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <flux:icon name="users" class="w-12 h-12 text-gray-300 mb-2" />
                                        <p>No se encontraron usuarios</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($users->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Unificado de Usuario -->
    <flux:modal variant="flyout" wire:model="showUserModal" size="lg">
        <div class="p-4">
            @if ($isConfirming)
                <!-- Modal de Confirmación -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-semibold text-gray-900">
                        Confirmar Acción
                    </h3>
                </div>

                <div class="text-center py-6">
                    <div
                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-{{ $confirmAction === 'activate' ? 'green' : 'red' }}-100 mb-4">
                        <flux:icon name="{{ $confirmAction === 'activate' ? 'check-circle' : 'x-circle' }}"
                            class="h-6 w-6 text-{{ $confirmAction === 'activate' ? 'green' : 'red' }}-600" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        {{ $confirmMessage }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        Esta acción {{ $confirmAction === 'activate' ? 'activará' : 'desactivará' }} el usuario en el
                        sistema.
                    </p>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button icon="x-circle" type="button" variant="outline" size="sm"
                        wire:click="closeConfirmModal">
                        Cancelar
                    </flux:button>
                    <flux:button icon="x-circle" type="button"
                        color="{{ $confirmAction === 'activate' ? 'green' : 'red' }}" size="sm"
                        wire:click="executeConfirmAction">
                        {{ $confirmAction === 'activate' ? 'Activar' : 'Desactivar' }} Usuario
                    </flux:button>
                </div>
            @else
                <!-- Modal de Crear/Editar Usuario -->
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-base font-semibold text-gray-900">
                        @if ($isCreating)
                            Crear Usuario
                        @else
                            Editar Usuario
                        @endif
                    </h3>
                </div>

                @if ($showUserModal)
                    <form wire:submit.prevent="saveUser">
                        <!-- Información del usuario -->
                        @if (!$isCreating && $selectedUser)
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-green-600">
                                            {{ $selectedUser->initials() }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $selectedUser->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $selectedUser->email }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <!-- Nombre -->
                            <div class="col-span-2">
                                <flux:input id="name" wire:model="name" size="xs"
                                    placeholder="Nombre completo *" class="w-full" />
                                @error('name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-span-2">
                                <flux:input id="email" type="email" wire:model="email" size="xs"
                                    placeholder="Correo electrónico *" class="w-full" />
                                @error('email')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div class="col-span-2">
                                <flux:input id="phone" wire:model="phone" size="xs" placeholder="Teléfono"
                                    class="w-full" />
                                @error('phone')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Líder Asignado -->
                            <div class="col-span-2">
                                <flux:select id="lider_id" wire:model="lider_id" size="xs" class="w-full">
                                    <option value="">Sin líder asignado</option>
                                    @foreach ($leaders as $lider)
                                        <option value="{{ $lider->id }}">{{ $lider->name }}</option>
                                    @endforeach
                                </flux:select>
                                @error('lider_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Rol -->
                            <div class="col-span-2">
                                <flux:select id="selectedRole" wire:model="selectedRole" size="xs"
                                    class="w-full">
                                    <option value="">Selecciona un rol *</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ Str::title($role->name) }}</option>
                                    @endforeach
                                </flux:select>
                                @error('selectedRole')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Campos de contraseña solo para creación -->
                            @if ($isCreating)
                                <div class="col-span-2">
                                    <flux:input id="password" type="password" wire:model="password" size="xs"
                                        placeholder="Contraseña *" class="w-full" />
                                    @error('password')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-span-2">
                                    <flux:input id="password_confirmation" type="password"
                                        wire:model="password_confirmation" size="xs"
                                        placeholder="Confirmar contraseña *" class="w-full" />
                                    @error('password_confirmation')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                            <flux:button icon="x-circle" type="button" variant="outline" size="xs"
                                wire:click="closeUserModal">
                                Cancelar
                            </flux:button>
                            <flux:button icon="user-plus" type="submit" color="primary" size="xs"
                                wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed">
                                <span wire:loading.remove>
                                    @if ($isCreating)
                                        Crear Usuario
                                    @else
                                        Actualizar Usuario
                                    @endif
                                </span>
                                <span wire:loading>
                                    <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                                    @if ($isCreating)
                                        Creando...
                                    @else
                                        Actualizando...
                                    @endif
                                </span>
                            </flux:button>
                        </div>
                    </form>
                @endif
            @endif
        </div>
    </flux:modal>

    <!-- Modal de Activación/Desactivación de Usuario -->
    <flux:modal wire:model="showDeactivateModal" size="md">
        <div class="p-6">
            <div
                class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full
                {{ $actionType === 'activate' ? 'bg-green-100' : 'bg-red-100' }}">
                <flux:icon name="{{ $actionType === 'activate' ? 'check-circle' : 'exclamation-triangle' }}"
                    class="w-6 h-6 {{ $actionType === 'activate' ? 'text-green-600' : 'text-red-600' }}" />
            </div>

            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ $actionType === 'activate' ? 'Activar Usuario' : 'Desactivar Usuario' }}
                </h3>

                @if ($userToModify)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-green-600">
                                    {{ $userToModify->initials() }}
                                </span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium text-gray-900">{{ $userToModify->name }}</div>
                                <div class="text-xs text-gray-500">{{ $userToModify->email }}</div>
                                @if ($userToModify->roles->isNotEmpty())
                                    <div class="text-xs text-gray-500">
                                        Rol: {{ $userToModify->roles->first()->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-6">
                    @if ($actionType === 'activate')
                        ¿Estás seguro de que deseas activar este usuario?
                        <span class="font-medium text-green-600">Esta acción permitirá que el usuario acceda al
                            sistema.</span>
                    @else
                        ¿Estás seguro de que deseas desactivar este usuario?
                        <span class="font-medium text-red-600">Esta acción impedirá que el usuario acceda al
                            sistema.</span>
                    @endif
                </p>

                @if ($actionType === 'activate')
                    <div class="bg-green-50 border border-green-200 rounded-md p-3 mb-6">
                        <div class="flex">
                            <flux:icon name="information-circle" class="w-5 h-5 text-green-400 mr-2 mt-0.5" />
                            <div class="text-sm text-green-800">
                                <p class="font-medium">Consecuencias de la activación:</p>
                                <ul class="mt-1 text-xs space-y-1">
                                    <li>• El usuario podrá iniciar sesión</li>
                                    <li>• Tendrá acceso a todas las funcionalidades</li>
                                    <li>• Podrá realizar acciones según su rol</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-6">
                        <div class="flex">
                            <flux:icon name="information-circle" class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" />
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Consecuencias de la desactivación:</p>
                                <ul class="mt-1 text-xs space-y-1">
                                    <li>• El usuario no podrá iniciar sesión</li>
                                    <li>• Se perderá el acceso a todas las funcionalidades</li>
                                    <li>• Los datos del usuario se mantendrán intactos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:button icon="x-circle" type="button" variant="outline" size="sm"
                    wire:click="closeDeactivateModal">
                    Cancelar
                </flux:button>
                <flux:button icon="{{ $actionType === 'activate' ? 'check-circle' : 'x-circle' }}" type="button"
                    color="{{ $actionType === 'activate' ? 'green' : 'red' }}" size="sm"
                    wire:click="executeDeactivate">
                    {{ $actionType === 'activate' ? 'Activar' : 'Desactivar' }} Usuario
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal de QR -->
    <flux:modal wire:model="showQRModal" size="md">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2 flex items-center justify-center gap-2">
                <flux:icon name="qr-code" class="w-5 h-5 text-gray-600" />
                QR del Usuario
            </h3>
            <div class="bg-white p-4 rounded-lg border border-gray-200 flex justify-center items-center">
                {!! $qrcode !!}
            </div>
            <flux:button icon="x-circle" type="button" variant="outline" size="sm"
                wire:click="closeQRModal" class="mt-4 w-full">
                Cerrar
            </flux:button>

        </div>
    </flux:modal>
</div>

<script type="module">
    import Swal from 'sweetalert2';

    // Función helper para mostrar notificaciones de éxito
    const showSuccess = (title, message) => {
        Swal.fire({
            icon: 'success',
            title,
            text: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };

    // Función helper para mostrar errores
    const showError = (message) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'Entendido'
        });
    };

    // Escuchar eventos de notificaciones de éxito
    window.addEventListener('user-activated', event => {
        showSuccess('¡Usuario Activado!', event.detail.message);
    });

    window.addEventListener('user-deactivated', event => {
        showSuccess('¡Usuario Desactivado!', event.detail.message);
    });

    window.addEventListener('user-created', event => {
        showSuccess('¡Usuario Creado!', event.detail.message);
    });

    window.addEventListener('user-updated', event => {
        showSuccess('¡Usuario Actualizado!', event.detail.message);
    });

    // Escuchar errores de Livewire
    window.addEventListener('livewire:error', event => {
        showError('Ocurrió un error al procesar la solicitud. Por favor, inténtalo de nuevo.');
    });

    // Escuchar errores personalizados
    window.addEventListener('show-error', event => {
        showError(event.detail.message);
    });
</script>
