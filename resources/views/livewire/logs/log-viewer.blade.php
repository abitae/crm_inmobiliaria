<div>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Visualizador de Logs</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Monitorea y analiza los logs del sistema</p>
            </div>
            <div class="flex space-x-2">
                <!-- Auto Refresh Toggle -->
                <flux:button icon="arrow-path" variant="outline" wire:click="toggleAutoRefresh"
                    :color="$autoRefresh ? 'green' : 'gray'" size="sm">
                    {{ $autoRefresh ? 'Auto ON' : 'Auto OFF' }}
                </flux:button>

                <!-- Refresh Button -->
                <flux:button icon="arrow-path" variant="outline" wire:click="refreshLogs" size="sm">
                    Actualizar
                </flux:button>

                <!-- Export Button -->
                <flux:button icon="arrow-down-tray" variant="outline" wire:click="exportLogs" :disabled="!$logFile"
                    size="sm">
                    Exportar CSV
                </flux:button>

                <!-- Download Button -->
                <flux:button icon="arrow-down-tray" variant="outline" wire:click="downloadLog" :disabled="!$logFile"
                    size="sm">
                    Descargar
                </flux:button>

                <!-- Clear Button -->
                <flux:button icon="trash" variant="outline" color="red" wire:click="clearLog"
                    :disabled="!$logFile" wire:confirm="¿Estás seguro de que quieres limpiar este archivo de log?"
                    size="sm">
                    Limpiar
                </flux:button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Archivo de Log -->
                <div>
                    <flux:field>
                        <flux:select label="Archivo de Log" wire:model.live="logFile">
                            @foreach ($availableLogFiles as $file)
                                <option value="{{ $file }}">{{ $file }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Búsqueda -->
                <div>
                    <flux:field>
                        <flux:input label="Buscar" wire:model.live.debounce.300ms="search"
                            placeholder="Buscar en logs..." />
                    </flux:field>
                </div>

                <!-- Nivel -->
                <div>
                    <flux:field>
                        <flux:select label="Nivel" wire:model.live="level">
                            <option value="">Todos los niveles</option>
                            @foreach ($this->levels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Fecha -->
                <div>
                    <flux:field>

                        <flux:select label="Fecha" wire:model.live="date">
                            <option value="">Todas las fechas</option>
                            @foreach ($this->availableDates as $date)
                                <option value="{{ $date }}">
                                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Elementos por página -->
                <div>
                    <flux:field>
                        <flux:select label="Por página" wire:model.live="perPage">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </flux:select>
                    </flux:field>
                </div>

                <!-- Botón Limpiar -->
                <div class="flex items-end">
                    <flux:button icon="x-mark" variant="outline" wire:click="clearFilters" class="w-full">
                        Limpiar Filtros
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <flux:icon icon="document" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Logs</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->logStats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <flux:icon icon="exclamation-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Errores</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->logStats['errors']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <flux:icon icon="exclamation-circle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Advertencias</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->logStats['warnings']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <flux:icon icon="information-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Información</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->logStats['info']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 dark:bg-gray-900 rounded-lg">
                        <flux:icon icon="bug-ant" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Debug</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->logStats['debug']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Registros de Log</h3>
                    <div class="flex items-center space-x-4">
                        <!-- Ordenar por -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Ordenar por:</span>
                            <flux:button icon="clock" variant="outline" size="sm"
                                wire:click="sortBy('timestamp')" :color="$sortBy === 'timestamp' ? 'blue' : 'gray'">
                                Fecha
                                @if ($sortBy === 'timestamp')
                                    <flux:icon icon="{{ $sortDirection === 'desc' ? 'chevron-down' : 'chevron-up' }}"
                                        class="w-3 h-3" />
                                @endif
                            </flux:button>
                            <flux:button icon="exclamation-circle" variant="outline" size="sm"
                                wire:click="sortBy('level')" :color="$sortBy === 'level' ? 'blue' : 'gray'">
                                Nivel
                                @if ($sortBy === 'level')
                                    <flux:icon icon="{{ $sortDirection === 'desc' ? 'chevron-down' : 'chevron-up' }}"
                                        class="w-3 h-3" />
                                @endif
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>

            @if ($this->paginatedLogs->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($this->paginatedLogs as $log)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                            wire:click="viewLog('{{ base64_encode(json_encode($log)) }}')">
                            <div class="flex items-start space-x-4">
                                <!-- Nivel -->
                                <div class="flex-shrink-0">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if (
                                            $log['level'] === 'ERROR' ||
                                                $log['level'] === 'CRITICAL' ||
                                                $log['level'] === 'ALERT' ||
                                                $log['level'] === 'EMERGENCY') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($log['level'] === 'WARNING')
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($log['level'] === 'INFO')
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($log['level'] === 'DEBUG')
                                            bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @else
                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif">
                                        {{ $log['level'] }}
                                    </span>
                                </div>

                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $log['formatted_time'] }}
                                        </p>
                                        <flux:icon icon="chevron-right" class="w-4 h-4 text-gray-400" />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate">
                                        {{ Str::limit($log['message'], 200) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Página {{ $currentPage }} de {{ $this->totalPages }} - Mostrando
                            {{ $this->paginatedLogs->count() }} de
                            {{ $this->totalLogs }} registros
                        </div>
                        <div class="flex space-x-2">
                            @if ($currentPage > 1)
                                <flux:button variant="outline" size="sm" wire:click="previousPage">
                                    Anterior
                                </flux:button>
                            @endif

                            @if ($currentPage < $this->totalPages)
                                <flux:button variant="outline" size="sm" wire:click="nextPage">
                                    Siguiente
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center">
                    <flux:icon icon="document" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay logs disponibles</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if ($search || $level || $date)
                            No se encontraron logs que coincidan con los filtros aplicados.
                        @else
                            No hay registros de log en el archivo seleccionado.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para ver log completo -->
    <flux:modal wire:model="showModal" max-width="4xl">

        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detalle del Log</h3>



        @if ($selectedLog && is_array($selectedLog))
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nivel</label>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if (
                                    isset($selectedLog['level']) && (
                                        $selectedLog['level'] === 'ERROR' ||
                                        $selectedLog['level'] === 'CRITICAL' ||
                                        $selectedLog['level'] === 'ALERT' ||
                                        $selectedLog['level'] === 'EMERGENCY')) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif(isset($selectedLog['level']) && $selectedLog['level'] === 'WARNING')
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif(isset($selectedLog['level']) && $selectedLog['level'] === 'INFO')
                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif(isset($selectedLog['level']) && $selectedLog['level'] === 'DEBUG')
                                    bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @else
                                    bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif">
                            {{ $selectedLog['level'] ?? 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha y
                            Hora</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $selectedLog['formatted_time'] ?? 'N/A' }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mensaje</label>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <pre class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $selectedLog['message'] ?? 'No hay mensaje disponible' }}</pre>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Log
                        Completo</label>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <pre class="text-xs text-gray-900 dark:text-white whitespace-pre-wrap">{{ $selectedLog['raw'] ?? 'No hay datos disponibles' }}</pre>
                    </div>
                </div>
            </div>
        @else
            <div class="p-8 text-center">
                <flux:icon icon="exclamation-triangle" class="w-12 h-12 text-yellow-400 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Error al cargar el log</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    No se pudo cargar la información del log seleccionado.
                </p>
            </div>
        @endif



        <flux:button variant="outline" wire:click="closeModal">
            Cerrar
        </flux:button>

    </flux:modal>

    @if ($autoRefresh)
        <script>
            setInterval(function() {
                @this.call('refreshLogs');
            }, {{ $refreshInterval * 1000 }});
        </script>
    @endif
</div>
