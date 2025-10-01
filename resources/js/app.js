import './bootstrap';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

// Configuración global de SweetAlert2
window.Swal = Swal;
window.Chart = Chart;

// Configuración personalizada para el tema de la aplicación
Swal.mixin({
    confirmButtonColor: '#3b82f6', // Color azul de Tailwind
    cancelButtonColor: '#ef4444',  // Color rojo de Tailwind
    background: '#ffffff',
    color: '#1f2937',
    customClass: {
        popup: 'rounded-lg shadow-xl',
        confirmButton: 'px-4 py-2 rounded-md font-medium',
        cancelButton: 'px-4 py-2 rounded-md font-medium'
    }
});

// Función global para mostrar alertas de éxito
window.showSuccess = (message, title = '¡Éxito!') => {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
};

// Función global para mostrar alertas de error
window.showError = (message, title = 'Error') => {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: 'Entendido'
    });
};

// Función global para confirmaciones
window.showConfirm = (message, title = 'Confirmar') => {
    return Swal.fire({
        icon: 'question',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    });
};

// Función global para alertas de información
window.showInfo = (message, title = 'Información') => {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonText: 'Entendido'
    });
};

// Listeners para eventos de Livewire
document.addEventListener('livewire:init', () => {
    // Listener para alertas de éxito (solo toasts)
    Livewire.on('show-success', (event) => {
        showSuccess(event.message, event.title);
    });

    // Listener para alertas de información (solo toasts)
    Livewire.on('show-info', (event) => {
        showInfo(event.message, event.title);
    });

    // Los errores se manejan directamente en los campos del formulario
    // No se muestran en toasts para evitar duplicación
});
