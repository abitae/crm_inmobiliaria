# Guía de Uso de SweetAlert2 en el CRM

## Instalación

SweetAlert2 ya está instalado en el proyecto a través de npm y configurado en `resources/js/app.js`.

## Funciones Disponibles

### 1. Alertas de Éxito
```javascript
// Desde PHP (Livewire)
$this->dispatch('show-success', message: 'Operación completada exitosamente.');

// Desde JavaScript
window.showSuccess('Mensaje de éxito');
window.showSuccess('Mensaje personalizado', 'Título personalizado');
```

### 2. Alertas de Error
```javascript
// Desde PHP (Livewire)
$this->dispatch('show-error', message: 'Ha ocurrido un error.');

// Desde JavaScript
window.showError('Mensaje de error');
window.showError('Mensaje personalizado', 'Título personalizado');
```

### 3. Confirmaciones
```javascript
// Desde PHP (Livewire)
$this->dispatch('show-confirm', 
    message: '¿Estás seguro de continuar?',
    title: 'Confirmar acción',
    action: 'nombreMetodo'
);

// Desde JavaScript
window.showConfirm('¿Estás seguro?').then((result) => {
    if (result.isConfirmed) {
        // Usuario confirmó
        console.log('Confirmado');
    }
});
```

### 4. Alertas de Información
```javascript
// Desde PHP (Livewire)
$this->dispatch('show-info', message: 'Información importante.');

// Desde JavaScript
window.showInfo('Mensaje informativo');
window.showInfo('Mensaje personalizado', 'Título personalizado');
```

## Uso en Componentes Livewire

### Ejemplo Básico
```php
class MiComponente extends Component
{
    public function guardar()
    {
        try {
            // Lógica de guardado
            $this->dispatch('show-success', message: 'Datos guardados correctamente.');
        } catch (Exception $e) {
            $this->dispatch('show-error', message: 'Error al guardar: ' . $e->getMessage());
        }
    }
}
```

### Ejemplo con Confirmación
```php
class MiComponente extends Component
{
    public function confirmarEliminacion($id)
    {
        $this->dispatch('show-confirm', 
            message: '¿Estás seguro de eliminar este elemento?',
            title: 'Confirmar eliminación',
            action: 'eliminar'
        );
    }

    public function eliminar()
    {
        // Lógica de eliminación
        $this->dispatch('show-success', message: 'Elemento eliminado correctamente.');
    }
}
```

## Configuración Personalizada

### Colores y Estilos
Los colores están configurados para coincidir con el tema de Tailwind CSS:
- **Confirmar**: Azul (`#3b82f6`)
- **Cancelar**: Rojo (`#ef4444`)
- **Fondo**: Blanco (`#ffffff`)
- **Texto**: Gris oscuro (`#1f2937`)

### Personalización Adicional
Si necesitas personalizar más las alertas, puedes modificar las funciones en `resources/js/app.js`:

```javascript
// Ejemplo de personalización
window.showCustomAlert = (options) => {
    return Swal.fire({
        ...options,
        confirmButtonColor: '#10b981', // Verde personalizado
        customClass: {
            popup: 'rounded-xl shadow-2xl',
            confirmButton: 'px-6 py-3 rounded-lg font-semibold'
        }
    });
};
```

## Eventos de Livewire

El sistema está configurado para escuchar automáticamente estos eventos:
- `show-success`: Muestra alerta de éxito
- `show-error`: Muestra alerta de error
- `show-confirm`: Muestra confirmación
- `show-info`: Muestra alerta informativa

## Ventajas de SweetAlert2

1. **Mejor UX**: Alertas más atractivas y profesionales
2. **Responsive**: Se adapta a todos los dispositivos
3. **Personalizable**: Fácil de personalizar colores y estilos
4. **Accesible**: Mejor accesibilidad que alertas nativas
5. **Consistente**: Mismo estilo en todos los navegadores

## Notas Importantes

- Las alertas se muestran automáticamente cuando se disparan los eventos de Livewire
- Las confirmaciones esperan la respuesta del usuario antes de continuar
- Todas las funciones están disponibles globalmente a través de `window`
- El sistema está integrado con el tema de Tailwind CSS del proyecto
