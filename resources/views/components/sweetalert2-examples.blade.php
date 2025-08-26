{{-- Componente de Ejemplos de SweetAlert2 --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ejemplos de SweetAlert2</h3>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Alerta de Éxito -->
        <button 
            onclick="window.showSuccess('¡Operación completada exitosamente!')"
            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
            Éxito
        </button>

        <!-- Alerta de Error -->
        <button 
            onclick="window.showError('Ha ocurrido un error inesperado.')"
            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
            Error
        </button>

        <!-- Confirmación -->
        <button 
            onclick="window.showConfirm('¿Estás seguro de continuar con esta acción?')"
            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
            Confirmar
        </button>

        <!-- Información -->
        <button 
            onclick="window.showInfo('Esta es una alerta informativa.')"
            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
            Info
        </button>
    </div>

    <div class="mt-6 p-4 bg-gray-50 rounded-md">
        <h4 class="font-medium text-gray-900 mb-2">Código de Ejemplo:</h4>
        <pre class="text-sm text-gray-600"><code>// Desde JavaScript
window.showSuccess('Mensaje de éxito');
window.showError('Mensaje de error');
window.showConfirm('¿Confirmar acción?');
window.showInfo('Información importante');

// Desde PHP (Livewire)
$this->dispatch('show-success', message: 'Operación exitosa');
$this->dispatch('show-error', message: 'Error en la operación');
$this->dispatch('show-confirm', message: '¿Confirmar?', action: 'metodo');
$this->dispatch('show-info', message: 'Información');
</code></pre>
    </div>
</div>
