# Creación Automática de Unidades desde Proyectos

## Descripción

Esta funcionalidad permite crear unidades (Unit.php) directamente desde el formulario de creación/edición de proyectos, automatizando el proceso de configuración masiva de unidades inmobiliarias.

## Características Principales

### 1. Activación de Creación Automática
- **Toggle Button**: Botón para activar/desactivar la creación automática de unidades
- **Condición**: Solo se muestra cuando se especifica un número total de unidades mayor a 0

### 2. Plantilla de Unidad
- **Configuración Base**: Define las características comunes para todas las unidades
- **Campos Incluidos**:
  - Tipo de unidad (lote, casa, departamento, oficina, local)
  - Área en metros cuadrados
  - Precio base por m²
  - Dormitorios y baños
  - Espacios de estacionamiento y cocheras
  - Áreas adicionales (balcón, terraza, jardín)
  - Porcentajes de descuento y comisión

### 3. Generación Automática
- **Numeración**: Las unidades se numeran automáticamente del 1 al total especificado
- **Asignación Inteligente**:
  - **Manzanas**: Se asignan automáticamente (A, B, C, D, E, F, G, H, I, J)
  - **Pisos**: Solo para departamentos y oficinas (1-20)
  - **Torres**: Solo para departamentos y oficinas (A, B, C, D)
  - **Bloques**: Solo para casas (Bloque 1, Bloque 2, etc.)

### 4. Cálculo Automático de Precios
- **Precio Total**: Área × Precio Base por m²
- **Descuento**: Precio Total × Porcentaje de Descuento
- **Precio Final**: Precio Total - Descuento
- **Comisión**: Precio Final × Porcentaje de Comisión

## Flujo de Trabajo

### 1. Crear Proyecto
```
1. Llenar información básica del proyecto
2. Especificar número total de unidades
3. Activar "Crear Unidades Automáticamente"
4. Configurar plantilla de unidad
5. Revisar vista previa de unidades
6. Guardar proyecto
```

### 2. Generación de Unidades
```
1. Se crea el proyecto en la base de datos
2. Se generan las unidades según la plantilla
3. Cada unidad se crea con:
   - project_id del proyecto creado
   - created_by y updated_by del usuario actual
   - Estado inicial: 'disponible'
   - Precios calculados automáticamente
```

## Archivos Modificados

### 1. `app/Livewire/Projects/ProjectList.php`
- **Nuevas Propiedades**:
  - `$createUnits`: Controla si se activa la creación automática
  - `$unitTemplate`: Plantilla de configuración de unidades
  - `$unitsToCreate`: Array de unidades a crear

- **Nuevos Métodos**:
  - `toggleCreateUnits()`: Activa/desactiva la creación automática
  - `generateUnitsToCreate()`: Genera el array de unidades
  - `updateUnitTemplate()`: Actualiza la plantilla
  - `updateUnitField()`: Actualiza campos individuales
  - `recalculateUnitPrices()`: Recalcula precios
  - `createUnitsForProject()`: Crea las unidades en la base de datos
  - Métodos auxiliares para asignación de manzanas, pisos, torres y bloques

### 2. `app/Services/ProjectService.php`
- **Nuevo Método**:
  - `getCurrentUserId()`: Obtiene el ID del usuario autenticado

### 3. `resources/views/livewire/projects/project-list.blade.php`
- **Nueva Sección**: "Configuración de Plantilla de Unidades"
- **Formulario de Plantilla**: Campos para configurar la unidad base
- **Vista Previa**: Muestra las unidades que se crearán
- **Información**: Explicación del proceso de creación automática

## Validaciones

### 1. Campos Requeridos
- Área: mínimo 1 m²
- Precio base: mínimo 0
- Baños: mínimo 1
- Porcentajes: entre 0 y 100

### 2. Lógica de Negocio
- Solo se crean unidades si `createUnits` está activado
- Solo se crean unidades si `total_units` > 0
- Los precios se calculan automáticamente

## Ventajas

### 1. Eficiencia
- **Ahorro de Tiempo**: No es necesario crear unidades una por una
- **Consistencia**: Todas las unidades siguen la misma plantilla base
- **Automatización**: Cálculos de precios automáticos

### 2. Flexibilidad
- **Personalización**: Se puede editar cada unidad individualmente después
- **Configuración**: Plantilla ajustable según el tipo de proyecto
- **Escalabilidad**: Funciona para cualquier número de unidades

### 3. Mantenibilidad
- **Código Limpio**: Lógica separada en métodos específicos
- **Reutilización**: Plantilla reutilizable para diferentes proyectos
- **Debugging**: Fácil identificación de problemas

## Casos de Uso

### 1. Proyectos Residenciales
- **Departamentos**: Configurar área, dormitorios, baños, estacionamientos
- **Casas**: Configurar área, dormitorios, baños, jardín, terraza

### 2. Proyectos Comerciales
- **Oficinas**: Configurar área, estacionamientos, pisos
- **Locales**: Configurar área, estacionamientos

### 3. Proyectos de Lotes
- **Lotes**: Configurar área, precio base, descuentos

## Consideraciones Técnicas

### 1. Base de Datos
- Las unidades se crean en la tabla `units`
- Se mantiene la integridad referencial con `projects`
- Se registra el usuario creador y modificador

### 2. Performance
- La creación se realiza en un bucle foreach
- Para proyectos con muchas unidades, considerar transacciones de base de datos
- La vista previa se actualiza en tiempo real

### 3. Seguridad
- Validación de campos en el frontend y backend
- Verificación de permisos de usuario
- Sanitización de datos de entrada

## Mejoras Futuras

### 1. Funcionalidades Adicionales
- **Importación Masiva**: Desde archivos Excel/CSV
- **Plantillas Predefinidas**: Para tipos de proyecto comunes
- **Validación Avanzada**: Reglas de negocio específicas por tipo de unidad

### 2. Optimizaciones
- **Transacciones de BD**: Para mejor performance con muchas unidades
- **Cola de Trabajos**: Para proyectos con miles de unidades
- **Cache**: Para plantillas frecuentemente utilizadas

### 3. Interfaz de Usuario
- **Drag & Drop**: Para reordenar unidades
- **Edición Inline**: Modificar unidades directamente en la vista previa
- **Filtros Avanzados**: Por tipo, área, precio, etc.
