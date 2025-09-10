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
                    <flux:button size="xs" variant="outline" wire:click="createUser">
                        Crear Usuario
                    </flux:button>
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
                    <flux:input size="xs" wire:model.live="search" placeholder="Buscar usuarios..." />
                </div>
                <div>
                    <flux:button size="xs" variant="outline" wire:click="$set('search', '')">
                        Limpiar búsqueda
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
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50">
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
                                    @if ($user->roles->count() > 0)
                                        @foreach ($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($role->name === 'admin') bg-red-100 text-red-800
                                                @elseif($role->name === 'lider') bg-blue-100 text-blue-800
                                                @elseif($role->name === 'vendedor') bg-green-100 text-green-800
                                                @elseif($role->name === 'cliente') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800 @endif">
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
                                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs font-medium text-blue-600">
                                                    {{ substr($user->lider->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->lider->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->lider->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        @if ($user->hasRole(['admin', 'lider']))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Es Líder
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">Sin líder asignado</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <flux:button size="xs" variant="outline"
                                            wire:click="openUserModal({{ $user->id }})">
                                            <flux:icon name="pencil" class="w-3 h-3" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
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
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-base font-semibold text-gray-900">
                    @if($isCreating)
                        Crear Usuario
                    @else
                        Editar Usuario
                    @endif
                </h3>
            </div>

            @if ($showUserModal)
                <form wire:submit.prevent="saveUser">
                    <!-- Información del usuario -->
                    @if(!$isCreating && $selectedUser)
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
                            <flux:input id="name" wire:model="name" size="xs" placeholder="Nombre completo *"
                                class="w-full" />
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-2">
                            <flux:input id="email" type="email" wire:model="email" size="xs"
                                placeholder="Correo electrónico *" class="w-full" />
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="col-span-2">
                            <flux:input id="phone" wire:model="phone" size="xs" placeholder="Teléfono"
                                class="w-full" />
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Líder Asignado -->
                        <div class="col-span-2">
                            <flux:select id="lider_id" wire:model="lider_id" size="xs" class="w-full">
                                <option value="">Sin líder asignado</option>
                                @foreach ($leaders as $lider)
                                    <option value="{{ $lider->id }}">{{ $lider->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('lider_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Rol -->
                        <div class="col-span-2">
                            <flux:select id="selectedRole" wire:model="selectedRole" size="xs" class="w-full">
                                <option value="">Selecciona un rol *</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </flux:select>
                            @error('selectedRole') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Campos de contraseña solo para creación -->
                        @if($isCreating)
                            <div class="col-span-2">
                                <flux:input id="password" type="password" wire:model="password" size="xs" 
                                    placeholder="Contraseña *" class="w-full" />
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <flux:input id="password_confirmation" type="password" wire:model="password_confirmation" size="xs" 
                                    placeholder="Confirmar contraseña *" class="w-full" />
                                @error('password_confirmation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                        <flux:button type="button" variant="outline" size="xs" wire:click="closeUserModal">
                            Cancelar
                        </flux:button>
                        <flux:button type="submit" color="primary" size="xs" wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                            <span wire:loading.remove>
                                @if($isCreating)
                                    Crear Usuario
                                @else
                                    Actualizar Usuario
                                @endif
                            </span>
                            <span wire:loading>
                                <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                                @if($isCreating)
                                    Creando...
                                @else
                                    Actualizando...
                                @endif
                            </span>
                        </flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>