# Análisis Funcional: Sistema de Reservas

## 📋 Índice
1. [Visión General](#visión-general)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Modelo de Datos](#modelo-de-datos)
4. [Estados y Transiciones](#estados-y-transiciones)
5. [Flujos de Trabajo Completos](#flujos-de-trabajo-completos)
6. [Validaciones y Reglas de Negocio](#validaciones-y-reglas-de-negocio)
7. [Integraciones con Otros Módulos](#integraciones-con-otros-módulos)
8. [Interfaz de Usuario](#interfaz-de-usuario)
9. [Características y Funcionalidades](#características-y-funcionalidades)

---

## 🎯 Visión General

El sistema de reservas es un módulo completo para gestionar reservas inmobiliarias que permite:

- Crear reservas para unidades disponibles
- Confirmar reservas mediante comprobantes de pago
- Editar información de reservas activas
- Cancelar reservas con notas obligatorias
- Convertir reservas confirmadas a ventas
- Visualizar detalles completos de reservas
- Filtrar y buscar reservas por múltiples criterios

El sistema garantiza que las unidades solo se reserven cuando hay comprobante de pago confirmado, manteniendo la disponibilidad real de las unidades.

---

## 🏗️ Arquitectura del Sistema

### Componentes Principales

1. **Modelo Reservation** (`app/Models/Reservation.php`)
   - Modelo Eloquent con SoftDeletes
   - 48 campos fillable
   - 6 relaciones BelongsTo (Client, Project, Unit, Advisor, CreatedBy, UpdatedBy)
   - 15 scopes de consulta
   - 12 accessors para formateo y validación
   - 15 métodos de negocio

2. **Componente Livewire** (`app/Livewire/Reservations/ReservationList.php`)
   - 667 líneas de código
   - 9 filtros de búsqueda
   - 4 modales diferentes (Creación/Edición, Detalle, Confirmación, Cancelación)
   - Manejo de subida de archivos
   - Validaciones en tiempo real

3. **Vista Blade** (`resources/views/livewire/reservations/reservation-list.blade.php`)
   - 521 líneas
   - Tabla compacta con columnas combinadas
   - 4 modales interactivos
   - Sistema de filtros avanzado con actualización en tiempo real

---

## 📊 Modelo de Datos

### Estructura de la Tabla `reservations`

#### Campos Principales

**Identificadores:**
- `id`: Identificador único
- `reservation_number`: Número único de reserva (formato: RES-YYYY-NNNNNN)

**Relaciones:**
- `client_id`: Cliente asociado
- `project_id`: Proyecto asociado
- `unit_id`: Unidad asociada
- `advisor_id`: Asesor responsable
- `created_by`: Usuario que creó la reserva
- `updated_by`: Usuario que actualizó la reserva

**Estados y Tipos:**
- `reservation_type`: Tipo de reserva (`pre_reserva`, `reserva_firmada`, `reserva_confirmada`)
- `status`: Estado de la reserva (`activa`, `confirmada`, `cancelada`, `vencida`, `convertida_venta`)
- `payment_status`: Estado de pago (`pendiente`, `pagado`, `parcial`)

**Fechas:**
- `reservation_date`: Fecha de la reserva (requerida)
- `expiration_date`: Fecha de vencimiento (opcional)

**Información Financiera:**
- `reservation_amount`: Monto de la reserva (decimal 12,2)
- `reservation_percentage`: Porcentaje del precio total (decimal 5,2)
- `payment_method`: Método de pago (nullable)
- `payment_reference`: Referencia de pago (nullable)

**Documentación:**
- `notes`: Notas de la reserva
- `terms_conditions`: Términos y condiciones
- `image`: Ruta de la imagen del comprobante de pago

**Firmas:**
- `client_signature`: Firma del cliente (boolean)
- `advisor_signature`: Firma del asesor (boolean)

**Auditoría:**
- `created_at`, `updated_at`: Timestamps
- `deleted_at`: Soft delete

### Índices de Base de Datos

```sql
- status + reservation_type
- client_id + status
- project_id + status
- unit_id + status
- advisor_id + status
- expiration_date + status
- reservation_date + status
```

### Relaciones Eloquent

```php
// Reservation pertenece a:
- Client (belongsTo)
- Project (belongsTo)
- Unit (belongsTo)
- User/Advisor (belongsTo)
- User/CreatedBy (belongsTo)
- User/UpdatedBy (belongsTo)
```

---

## 🔄 Estados y Transiciones

### Estados de Reserva (`status`)

#### 1. `activa` (Estado Inicial)
- **Descripción**: Reserva creada sin comprobante de pago
- **Características**:
  - Se crea automáticamente al crear una nueva reserva
  - Unidad permanece disponible (no se reserva)
  - Permite edición de campos (excepto proyecto, unidad, estado)
  - Permite subir imagen de confirmación
  - Permite cancelación
- **Transiciones posibles**:
  - → `confirmada` (al subir imagen de comprobante)
  - → `cancelada` (al cancelar con nota)
  - → `vencida` (automáticamente si expira)

#### 2. `confirmada` (Estado con Comprobante)
- **Descripción**: Reserva con comprobante de pago subido
- **Características**:
  - Se alcanza al subir imagen del comprobante
  - Unidad se marca como `reservado`
  - No permite edición (solo visualización)
  - Permite conversión a venta
  - Permite cancelación
- **Transiciones posibles**:
  - → `convertida_venta` (al convertir a venta)
  - → `cancelada` (al cancelar con nota)

#### 3. `cancelada` (Estado Final)
- **Descripción**: Reserva cancelada con nota obligatoria
- **Características**:
  - Requiere nota obligatoria (mínimo 10 caracteres)
  - Unidad se libera a `disponible`
  - No permite cambios
  - Estado final (no puede cambiar)

#### 4. `vencida` (Estado Automático)
- **Descripción**: Reserva que expiró sin confirmar
- **Características**:
  - Se marca cuando `expiration_date` pasa y status es `activa`
  - Unidad se libera
  - Estado final (no puede cambiar)

#### 5. `convertida_venta` (Estado Final)
- **Descripción**: Reserva convertida a venta
- **Características**:
  - Solo disponible desde estado `confirmada`
  - Crea/actualiza Opportunity con status `pagado`
  - Unidad se marca como `vendido`
  - Estado final (no puede cambiar)

### Tipos de Reserva (`reservation_type`)

1. **`pre_reserva`** (Valor por defecto)
   - Todas las reservas nuevas inician con este tipo
   - Se mantiene durante todo el ciclo de vida

2. **`reserva_firmada`**
   - Disponible en el sistema pero no se usa actualmente

3. **`reserva_confirmada`**
   - Disponible en el sistema pero no se usa actualmente

### Estados de Pago (`payment_status`)

- **`pendiente`**: Pago pendiente (valor por defecto al crear)
- **`pagado`**: Pago completado
- **`parcial`**: Pago parcial

---

## 🔀 Flujos de Trabajo Completos

### 1. Creación de Reserva

**Proceso Completo:**

1. **Inicio**: Usuario hace clic en "Nueva Reserva"
2. **Modal de Creación**: Se abre formulario con campos requeridos
3. **Selección de Proyecto**: 
   - Usuario selecciona proyecto
   - Sistema carga automáticamente unidades disponibles del proyecto
   - Unidades ordenadas por manzana y luego por número
4. **Completar Formulario**:
   - Cliente (requerido, selección de lista)
   - Proyecto (requerido, selección de lista)
   - Unidad (requerido, solo unidades disponibles)
   - Asesor (requerido, según permisos del usuario)
   - Fecha de reserva (requerida, default: hoy)
   - Fecha de vencimiento (opcional, debe ser después de reserva)
   - Monto de reserva (requerido, mínimo 0)
   - Porcentaje (opcional, 0-100)
   - Método de pago (opcional)
   - Estado de pago (siempre 'pendiente' al crear, forzado)
   - Referencia de pago (opcional)
   - Notas (opcional)
   - Términos y condiciones (opcional)

5. **Validaciones Automáticas**:
   - Unidad debe estar disponible
   - Fecha vencimiento > fecha reserva
   - Monto >= 0
   - Porcentaje 0-100
   - Todos los campos requeridos completos

6. **Procesamiento**:
   - Genera número de reserva automático (RES-YYYY-NNNNNN)
   - Establece `status = 'activa'` (forzado)
   - Establece `payment_status = 'pendiente'` (forzado)
   - Establece `reservation_type = 'pre_reserva'` (forzado)
   - Establece `image = null` (no se sube imagen al crear)
   - **Unidad NO se reserva** (permanece disponible)

7. **Resultado**:
   - Reserva creada exitosamente
   - Unidad sigue disponible para otras reservas
   - Mensaje: "Para confirmarla, use el botón 'Subir imagen de confirmación'"

### 2. Confirmación de Reserva (Subir Comprobante)

**Proceso Completo:**

1. **Inicio**: Usuario hace clic en botón "Subir imagen de confirmación" (solo visible para status='activa')
2. **Modal de Confirmación**: Se abre con campos prellenados
3. **Información Mostrada**:
   - Número de reserva
   - Cliente
   - Proyecto
   - Unidad
4. **Campos Editables**:
   - Imagen del comprobante (requerida, jpeg, png, jpg, gif, webp, max 10MB)
   - Fecha de reserva
   - Fecha de vencimiento
   - Monto de reserva
   - Porcentaje
   - Método de pago
   - Estado de pago
   - Referencia de pago
5. **Validaciones**:
   - Imagen requerida
   - Fecha vencimiento > fecha reserva
   - Monto >= 0
   - Porcentaje 0-100
6. **Procesamiento**:
   - Elimina imagen anterior si existe
   - Guarda nueva imagen en `storage/app/public/reservations`
   - Actualiza todos los campos editables
   - Cambia `status = 'confirmada'` (automático)
   - **Marca unidad como 'reservado'**
   - Actualiza contadores del proyecto
7. **Resultado**:
   - Reserva confirmada exitosamente
   - Unidad bloqueada (reservada)
   - Mensaje de confirmación

### 3. Edición de Reserva

**Proceso Completo:**

1. **Inicio**: Usuario hace clic en botón "Editar" (solo visible para status='activa')
2. **Modal de Edición**: Se abre con datos actuales
3. **Campos Editables**:
   - Cliente
   - Asesor
   - Tipo de reserva
   - Fechas (reserva, vencimiento)
   - Montos y porcentajes
   - Información de pago
   - Notas y términos
4. **Campos NO Editables** (deshabilitados):
   - Proyecto (mantiene valor original)
   - Unidad (mantiene valor original)
   - Estado (mantiene valor actual, se actualiza automáticamente)
   - Imagen (solo desde modal de confirmación)
5. **Validaciones**:
   - Mismas validaciones que creación
   - No valida cambios de proyecto/unidad (porque no se pueden cambiar)
6. **Procesamiento**:
   - Mantiene proyecto y unidad originales
   - Mantiene estado actual
   - Mantiene imagen existente
   - Actualiza campos editables
7. **Resultado**:
   - Reserva actualizada exitosamente
   - Sin cambios en proyecto, unidad o estado

### 4. Cancelación de Reserva

**Proceso Completo:**

1. **Inicio**: Usuario hace clic en botón "Cancelar" (visible para status='activa' o 'confirmada')
2. **Modal de Cancelación**: Se abre con información de la reserva
3. **Información Mostrada**:
   - Número de reserva
   - Cliente
   - Proyecto
   - Unidad
   - Advertencia sobre liberación de unidad
4. **Campo Requerido**:
   - Nota de cancelación (obligatoria, 10-500 caracteres)
5. **Validaciones**:
   - Nota obligatoria
   - Mínimo 10 caracteres
   - Máximo 500 caracteres
   - Reserva debe poder cancelarse (`canBeCancelled()`)
6. **Procesamiento**:
   - Cambia `status = 'cancelada'`
   - Agrega nota a `notes` con prefijo "[Cancelada]"
   - **Marca unidad como 'disponible'**
   - Actualiza contadores del proyecto
7. **Resultado**:
   - Reserva cancelada exitosamente
   - Unidad liberada (disponible)
   - Mensaje de confirmación

### 5. Conversión a Venta

**Proceso Completo:**

1. **Inicio**: Usuario hace clic en botón "Convertir a venta" (solo visible para status='confirmada')
2. **Validaciones**:
   - Reserva debe estar en estado 'confirmada' (`canBeConverted()`)
   - Unidad debe poder venderse (`unit->canBeSold()`)
3. **Procesamiento** (en transacción DB):
   - Cambia `status = 'convertida_venta'`
   - Busca Opportunity existente relacionada (mismo cliente, proyecto, unidad, asesor, status='registrado')
   - Si existe Opportunity:
     * Actualiza a status='pagado'
     * Establece close_value (precio de unidad o calculado)
     * Registra número de reserva en close_reason
   - Si no existe:
     * Crea nueva Opportunity con status='pagado'
     * Establece todos los datos desde la reserva
     * Marca source='reserva'
   - **Marca unidad como 'vendido'** (usando `unit->sell()`)
   - Actualiza contadores del proyecto
4. **Resultado**:
   - Reserva convertida a venta exitosamente
   - Opportunity creada/actualizada
   - Unidad marcada como vendida
   - Mensaje de confirmación

### 6. Visualización de Detalle

**Proceso Completo:**

1. **Inicio**: Usuario hace clic en botón "Ver detalle" (visible para todos los estados)
2. **Modal de Detalle**: Se abre modal ancho (100vw) con información completa
3. **Secciones de Información**:
   - **Header**: Título y badge de estado con color
   - **Imagen del Comprobante**: Si existe, se muestra centrada
   - **Información Principal** (fondo azul):
     * Número de reserva
     * Tipo
     * Cliente
     * Proyecto
     * Unidad
     * Asesor
   - **Información Financiera** (fondo verde):
     * Monto de reserva
     * Porcentaje
     * Estado de pago (con badge)
     * Método de pago
     * Referencia de pago
   - **Fechas** (fondo púrpura):
     * Fecha de reserva
     * Fecha de vencimiento (con indicadores si está vencida o por vencer)
   - **Notas y Términos**: Si existen, se muestran en secciones separadas
   - **Auditoría** (fondo gris):
     * Creado por (usuario y fecha)
     * Actualizado por (usuario y fecha, si aplica)

### 7. Búsqueda y Filtrado

**Funcionalidades:**

1. **Búsqueda por Texto**:
   - Busca en número de reserva
   - Busca en nombre de cliente
   - Busca en nombre de proyecto
   - Actualización en tiempo real (`wire:model.live`)

2. **Filtros Disponibles**:
   - Estado de reserva (activa, confirmada, cancelada, vencida, convertida_venta)
   - Estado de pago (pendiente, pagado, parcial)
   - Proyecto (lista de proyectos activos)
   - Cliente (lista de clientes activos)
   - Asesor (solo para admin/líder, lista de asesores)
   - Botón "Limpiar" para resetear todos los filtros

3. **Paginación**:
   - 15 registros por página
   - Se resetea automáticamente al cambiar filtros

---

## ✅ Validaciones y Reglas de Negocio

### Validaciones del Formulario

```php
'client_id' => 'required|exists:clients,id'
'project_id' => 'required|exists:projects,id'
'unit_id' => 'required|exists:units,id'
'advisor_id' => 'required|exists:users,id'
'reservation_type' => 'required|in:pre_reserva,reserva_firmada,reserva_confirmada'
'status' => 'required|in:activa,confirmada,cancelada,vencida,convertida_venta'
'reservation_date' => 'required|date'
'expiration_date' => 'nullable|date|after:reservation_date'
'reservation_amount' => 'required|numeric|min:0'
'reservation_percentage' => 'nullable|numeric|min:0|max:100'
'payment_method' => 'nullable|string|max:255'
'payment_status' => 'required|in:pendiente,pagado,parcial'
'payment_reference' => 'nullable|string|max:255'
'notes' => 'nullable|string'
'terms_conditions' => 'nullable|string'
// Imagen solo se valida en modal de confirmación
'confirmation_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
```

### Reglas de Negocio Implementadas

1. **Generación Automática de Número de Reserva**
   - Formato: RES-YYYY-NNNNNN
   - Secuencial por año
   - Único en la base de datos
   - Se genera automáticamente al crear

2. **Validación de Disponibilidad de Unidad**
   - Solo unidades con status='disponible' pueden ser reservadas
   - Al editar, incluye unidad actual aunque esté reservada (para visualización)

3. **Actualización Automática de Estado**
   - Al crear: siempre 'activa'
   - Al confirmar con imagen: 'confirmada'
   - Al cancelar: 'cancelada'
   - Al convertir a venta: 'convertida_venta'
   - Al vencer: 'vencida'

4. **Gestión de Estado de Unidad**
   - Al crear: unidad NO se reserva (permanece disponible)
   - Al confirmar con imagen: unidad se marca como 'reservado'
   - Al cancelar: unidad se marca como 'disponible'
   - Al convertir a venta: unidad se marca como 'vendido'
   - Actualiza contadores del proyecto automáticamente

5. **Valores Forzados al Crear**
   - `status = 'activa'` (siempre)
   - `payment_status = 'pendiente'` (siempre)
   - `reservation_type = 'pre_reserva'` (siempre)
   - `image = null` (siempre)

6. **Campos No Editables en Edición**
   - Proyecto (mantiene valor original)
   - Unidad (mantiene valor original)
   - Estado (mantiene valor actual, se actualiza automáticamente)
   - Imagen (solo desde modal de confirmación)

7. **Ordenamiento de Unidades**
   - Al seleccionar proyecto, unidades se ordenan por:
     1. `unit_manzana` (ascendente)
     2. `unit_number` (ascendente)

---

## 🔗 Integraciones con Otros Módulos

### 1. Módulo de Unidades

**Integración:**
- Al crear reserva: unidad NO cambia de estado (permanece disponible)
- Al confirmar con imagen: `unidad.status = 'reservado'`
- Al cancelar: `unidad.status = 'disponible'`
- Al convertir a venta: `unidad.status = 'vendido'` (usando `unit->sell()`)
- Actualiza contadores del proyecto automáticamente (`project->updateUnitCounts()`)

**Validaciones:**
- Solo unidades disponibles pueden ser seleccionadas al crear
- Valida `unit->canBeSold()` antes de convertir a venta

### 2. Módulo de Proyectos

**Integración:**
- Filtra proyectos activos al cargar lista
- Actualiza contadores de unidades cuando cambia estado de unidad
- Carga unidades del proyecto seleccionado dinámicamente

### 3. Módulo de Clientes

**Integración:**
- Filtra clientes activos al cargar lista
- Relación BelongsTo establecida
- Muestra información del cliente en detalles y listados

### 4. Módulo de Usuarios/Asesores

**Integración:**
- Filtra asesores disponibles según rol del usuario
- Asesores normales: Solo ven sus propias reservas
- Admin/Líder: Ven todas las reservas
- Auditoría completa: `created_by`, `updated_by` con timestamps

### 5. Módulo de Ventas (Opportunity)

**Integración Completa:**
- Al convertir reserva a venta:
  - Busca Opportunity existente relacionada
  - Si existe: la actualiza a status='pagado'
  - Si no existe: crea nueva Opportunity con status='pagado'
  - Calcula precio de venta desde unidad o reserva
  - Registra número de reserva en `close_reason`
  - Marca source='reserva'
  - Establece stage='cierre' y probability=100

---

## 🎨 Interfaz de Usuario

### Tabla de Reservas

**Características:**
- Columnas compactas combinadas:
  - **Número / Cliente**: Número de reserva y nombre del cliente
  - **Proyecto / Unidad**: Nombre del proyecto y número de unidad
  - **Monto / Estado**: Monto formateado y badge de estado con color
  - **Vencimiento**: Fecha de vencimiento formateada
  - **Acciones**: Botones de acción según estado
- Paginación: 15 registros por página
- Filtros en tiempo real (`wire:model.live`)
- Badges de estado con colores semánticos

**Botones de Acción por Estado:**

| Estado | Ver Detalle | Editar | Subir Imagen | Convertir a Venta | Cancelar |
|--------|-------------|--------|--------------|------------------|----------|
| activa | ✅ | ✅ | ✅ | ❌ | ✅ |
| confirmada | ✅ | ❌ | ❌ | ✅ | ✅ |
| cancelada | ✅ | ❌ | ❌ | ❌ | ❌ |
| vencida | ✅ | ❌ | ❌ | ❌ | ❌ |
| convertida_venta | ✅ | ❌ | ❌ | ❌ | ❌ |

### Modales

#### 1. Modal de Creación/Edición
- **Tipo**: Flyout
- **Características**:
  - Formulario completo con validaciones
  - Carga dinámica de unidades al seleccionar proyecto
  - Campos deshabilitados según contexto (edición)
  - Mensajes informativos
  - Scroll interno para formularios largos

#### 2. Modal de Confirmación
- **Tipo**: Flyout
- **Características**:
  - Subida de imagen obligatoria
  - Campos prellenados con datos actuales
  - Preview de imagen en tiempo real
  - Actualiza múltiples campos simultáneamente
  - Mensaje informativo sobre cambios automáticos

#### 3. Modal de Cancelación
- **Tipo**: Centrado
- **Características**:
  - Nota obligatoria (10-500 caracteres)
  - Información de la reserva visible
  - Advertencia sobre liberación de unidad
  - Validación en tiempo real

#### 4. Modal de Detalle
- **Tipo**: Ancho (100vw)
- **Características**:
  - Vista completa de información
  - Secciones organizadas por colores
  - Imagen centrada si existe
  - Indicadores visuales (vencida, por vencer)
  - Información de auditoría

### Filtros

**Sistema de Filtros:**
- Búsqueda por texto (número, cliente, proyecto)
- Filtro por estado de reserva
- Filtro por estado de pago
- Filtro por proyecto
- Filtro por cliente
- Filtro por asesor (solo admin/líder)
- Botón "Limpiar" para resetear todos los filtros
- Actualización en tiempo real
- Reseteo automático de paginación

---

## ⚡ Características y Funcionalidades

### Características Principales

1. **Gestión Completa del Ciclo de Vida**
   - Creación → Confirmación → Conversión a Venta
   - Cancelación en cualquier momento (activa o confirmada)
   - Vencimiento automático (preparado)

2. **Control de Disponibilidad de Unidades**
   - Unidades solo se reservan cuando hay comprobante confirmado
   - Unidades permanecen disponibles hasta confirmación
   - Liberación automática al cancelar

3. **Integración con Ventas**
   - Conversión directa a Opportunity
   - Cálculo automático de precio de venta
   - Actualización de estado de unidad a vendido

4. **Auditoría Completa**
   - Registro de usuario creador
   - Registro de usuario actualizador
   - Timestamps de creación y actualización
   - Soft deletes para recuperación

5. **Validaciones Robustas**
   - Validación de disponibilidad de unidad
   - Validación de fechas
   - Validación de montos y porcentajes
   - Validación de archivos (tipo, tamaño)

6. **Interfaz Intuitiva**
   - Tabla compacta con información esencial
   - Modales organizados y claros
   - Mensajes informativos
   - Feedback visual (badges, colores)

7. **Búsqueda y Filtrado Avanzado**
   - Múltiples criterios de búsqueda
   - Filtros combinables
   - Actualización en tiempo real
   - Paginación eficiente

### Funcionalidades Especiales

1. **Generación Automática de Números**
   - Formato único: RES-YYYY-NNNNNN
   - Secuencial por año
   - Sin intervención manual

2. **Ordenamiento Inteligente de Unidades**
   - Por manzana primero
   - Por número de unidad segundo
   - Facilita selección para usuarios

3. **Gestión de Imágenes**
   - Almacenamiento en `storage/app/public/reservations`
   - Eliminación automática de imágenes anteriores
   - Preview en tiempo real
   - Validación de tipo y tamaño

4. **Transacciones de Base de Datos**
   - Operaciones críticas en transacciones
   - Rollback automático en caso de error
   - Consistencia de datos garantizada

5. **Permisos y Seguridad**
   - Filtrado automático por asesor
   - Admin/Líder ven todas las reservas
   - Validación de existencia de registros
   - Validación de tipos de datos

---

## 📈 Métricas y Estadísticas Disponibles

### Scopes para Consultas

```php
Reservation::active()                    // Reservas activas
Reservation::confirmed()                 // Reservas confirmadas
Reservation::cancelled()                 // Reservas canceladas
Reservation::expired()                   // Reservas vencidas
Reservation::converted()                 // Convertidas a venta
Reservation::byClient($id)                // Por cliente
Reservation::byProject($id)              // Por proyecto
Reservation::byUnit($id)                 // Por unidad
Reservation::byAdvisor($id)              // Por asesor
Reservation::byStatus($status)           // Por estado
Reservation::byType($type)               // Por tipo
Reservation::expiringSoon($days)          // Por vencer (próximos N días)
Reservation::expiredByDate()              // Vencidas por fecha
Reservation::byDateRange($start, $end)    // Por rango de fechas
Reservation::byPaymentStatus($status)     // Por estado de pago
```

### Accessors Útiles

```php
$reservation->is_active              // bool
$reservation->is_confirmed           // bool
$reservation->is_cancelled           // bool
$reservation->is_expired             // bool
$reservation->is_converted           // bool
$reservation->is_expiring_soon       // bool
$reservation->days_until_expiration  // int
$reservation->formatted_reservation_amount    // string
$reservation->formatted_reservation_percentage // string
$reservation->status_color           // string (green, blue, red, gray, purple)
$reservation->payment_status_color   // string (yellow, green, blue, gray)
$reservation->image_url              // string|null
```

### Métodos de Negocio

```php
$reservation->confirm()              // Confirmar reserva
$reservation->cancel($reason)        // Cancelar con motivo
$reservation->markAsExpired()        // Marcar como vencida
$reservation->convertToSale($userId) // Convertir a venta
$reservation->extendExpiration($date) // Extender vencimiento
$reservation->canBeConfirmed()       // Verificar si puede confirmarse
$reservation->canBeCancelled()       // Verificar si puede cancelarse
$reservation->canBeConverted()       // Verificar si puede convertirse
$reservation->needsRenewal()         // Verificar si necesita renovación
```

---

## 🔒 Seguridad y Auditoría

### Campos de Auditoría

- `created_by`: Usuario que creó la reserva
- `updated_by`: Usuario que actualizó la reserva
- `created_at`: Fecha y hora de creación
- `updated_at`: Fecha y hora de última actualización
- `deleted_at`: Fecha de eliminación lógica (soft delete)

### Permisos

- **Asesores normales**: Solo ven sus propias reservas
- **Admin/Líder**: Ven todas las reservas
- Filtro automático por `advisorFilter` según rol

### Validaciones de Seguridad

- Validación de existencia de registros relacionados
- Validación de tipos de datos
- Validación de rangos numéricos
- Validación de archivos (tipo, tamaño)
- Transacciones DB para operaciones críticas
- Rollback automático en caso de error

---

## 📝 Conclusión

El sistema de reservas es una solución completa y robusta para gestionar reservas inmobiliarias. Implementa un flujo de trabajo claro desde la creación hasta la conversión a venta, con validaciones exhaustivas, integraciones con otros módulos, y una interfaz de usuario intuitiva.

**Características Destacadas:**
- Control preciso del estado de unidades
- Integración completa con módulo de ventas
- Validaciones robustas en cada paso
- Interfaz clara y organizada
- Auditoría completa de operaciones
- Búsqueda y filtrado avanzado

El sistema garantiza la integridad de los datos y proporciona una experiencia de usuario fluida para la gestión de reservas inmobiliarias.

---

**Última actualización**: 2025-01-27
**Versión del análisis**: 1.0

