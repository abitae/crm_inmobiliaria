# Modelos del CRM Inmobiliario

## Descripción General
Esta carpeta contiene la documentación completa de todos los modelos del CRM Inmobiliario Integral. Cada modelo está diseñado para gestionar una entidad específica del negocio inmobiliario, con relaciones bien definidas y funcionalidades robustas.

## Estructura de Modelos

### Modelos Core (Entidades Principales)

#### 1. **Client** - Gestión de Clientes y Prospectos
- **Archivo**: [Client.md](./Client.md)
- **Descripción**: Modelo principal para la gestión del ciclo de vida completo del cliente
- **Funcionalidades**:
  - Registro de clientes con información personal completa
  - Clasificación por tipo (inversor, comprador, empresa, constructor)
  - Sistema de scoring y calificación de leads
  - Asignación automática de asesores
  - Seguimiento de interacciones y oportunidades

#### 2. **Project** - Proyectos Inmobiliarios
- **Archivo**: [Project.md](./Project.md)
- **Descripción**: Administración completa de proyectos inmobiliarios
- **Funcionalidades**:
  - Gestión de proyectos con soporte geoespacial
  - Control de etapas del proyecto (preventa, lanzamiento, venta activa, cierre)
  - Asignación de asesores y seguimiento de unidades
  - Integración con mapas y coordenadas GPS

#### 3. **Unit** - Unidades Inmobiliarias
- **Archivo**: [Unit.md](./Unit.md)
- **Descripción**: Gestión individual de unidades dentro de los proyectos
- **Funcionalidades**:
  - Características detalladas de cada unidad
  - Sistema de precios y descuentos
  - Estados de disponibilidad (disponible, reservado, vendido, bloqueado)
  - Cálculo automático de comisiones

### Modelos de Negocio

#### 4. **Opportunity** - Oportunidades de Venta
- **Archivo**: [Opportunity.md](./Opportunity.md)
- **Descripción**: Sistema de embudo de ventas y seguimiento de oportunidades
- **Funcionalidades**:
  - Embudo configurable con etapas personalizables
  - Cálculo de probabilidades y valores esperados
  - Seguimiento de conversiones y cierres
  - Automatización de flujos de venta

#### 5. **Activity** - Agenda y Actividades
- **Archivo**: [Activity.md](./Activity.md)
- **Descripción**: Sistema de gestión de agenda para asesores
- **Funcionalidades**:
  - Programación de actividades (llamadas, reuniones, visitas)
  - Sistema de recordatorios y notificaciones
  - Integración con calendarios externos
  - Seguimiento de actividades por cliente

#### 6. **Document** - Gestión Documental
- **Archivo**: [Document.md](./Document.md)
- **Descripción**: Sistema de gestión documental con control de versiones
- **Funcionalidades**:
  - Carga y gestión de archivos múltiples
  - Control de versiones y comentarios
  - Generación automática de contratos
  - Seguimiento de cumplimiento documental

#### 7. **Commission** - Sistema de Comisiones
- **Archivo**: [Commission.md](./Commission.md)
- **Descripción**: Cálculo y seguimiento de comisiones de asesores
- **Funcionalidades**:
  - Cálculo automático de comisiones por venta
  - Sistema de bonos y incentivos
  - Consolidado mensual de comisiones
  - Seguimiento de pagos y aprobaciones

#### 8. **Reservation** - Sistema de Reservas
- **Archivo**: [Reservation.md](./Reservation.md)
- **Descripción**: Gestión de reservas de unidades inmobiliarias
- **Funcionalidades**:
  - Pre-reservas con formularios online
  - Sistema de pagos y confirmaciones
  - Control de fechas de expiración
  - Conversión automática a ventas

### Modelos de Soporte

#### 9. **Interaction** - Interacciones con Clientes
- **Archivo**: [Interaction.md](./Interaction.md)
- **Descripción**: Historial completo de interacciones con clientes
- **Funcionalidades**:
  - Registro de llamadas, emails, mensajes
  - Seguimiento de canales de comunicación
  - Métricas de engagement por cliente

#### 10. **Task** - Gestión de Tareas
- **Archivo**: [Task.md](./Task.md)
- **Descripción**: Sistema de tareas y recordatorios para asesores
- **Funcionalidades**:
  - Creación y asignación de tareas
  - Sistema de prioridades y fechas límite
  - Seguimiento de cumplimiento
  - Integración con actividades y clientes

## Arquitectura de Relaciones

### Diagrama de Relaciones Principales
```
Client ←→ Project ←→ Unit
   ↓         ↓         ↓
Opportunity Activity Document
   ↓         ↓         ↓
Commission Reservation Task
```

### Relaciones Clave

#### Cliente (Centro del Sistema)
- **Un cliente** puede tener **múltiples oportunidades**
- **Un cliente** puede estar interesado en **múltiples proyectos**
- **Un cliente** puede tener **múltiples interacciones**
- **Un cliente** puede tener **múltiples tareas**

#### Proyecto (Entidad de Negocio)
- **Un proyecto** tiene **múltiples unidades**
- **Un proyecto** puede tener **múltiples asesores**
- **Un proyecto** puede tener **múltiples clientes interesados**
- **Un proyecto** puede generar **múltiples comisiones**

#### Unidad (Producto de Venta)
- **Una unidad** pertenece a **un proyecto**
- **Una unidad** puede tener **múltiples reservas**
- **Una unidad** puede generar **múltiples oportunidades**
- **Una unidad** puede tener **múltiples precios**

## Características Comunes

### Soft Deletes
Todos los modelos implementan `SoftDeletes` para mantener historial completo de datos.

### Auditoría
- `created_by` - Usuario que creó el registro
- `updated_by` - Usuario que actualizó por última vez
- `created_at` - Fecha y hora de creación
- `updated_at` - Fecha y hora de última actualización

### Scopes y Accessors
- **Scopes**: Filtros predefinidos para consultas comunes
- **Accessors**: Métodos para formatear y calcular datos derivados
- **Métodos**: Funcionalidades específicas de cada modelo

### Relaciones Optimizadas
- Uso de `eager loading` para evitar consultas N+1
- Relaciones con `pivot` para tablas intermedias
- Scopes para filtros de uso frecuente

## Convenciones de Nomenclatura

### Campos de Base de Datos
- **Snake_case** para nombres de campos
- **Descriptivos** y claros en su propósito
- **Consistentes** en todo el sistema

### Métodos de Modelo
- **CamelCase** para nombres de métodos
- **Verbos** que describen la acción
- **Específicos** en su funcionalidad

### Relaciones
- **Singular** para relaciones uno a uno
- **Plural** para relaciones uno a muchos
- **Descriptivos** del tipo de relación

## Consideraciones de Performance

### Índices de Base de Datos
- Índices en campos de búsqueda frecuente
- Índices compuestos para consultas complejas
- Índices en campos de relaciones

### Consultas Optimizadas
- Uso de scopes para filtros comunes
- Eager loading para evitar N+1 queries
- Paginación para grandes volúmenes de datos

### Cache y Optimización
- Cache de consultas frecuentes
- Optimización de consultas complejas
- Uso de vistas materializadas para reportes

## Casos de Uso Principales

### 1. **Captura de Leads**
- Creación automática de clientes desde formularios web
- Asignación automática de asesores
- Sistema de scoring inicial

### 2. **Gestión de Ventas**
- Seguimiento de oportunidades en el embudo
- Gestión de reservas y conversiones
- Cálculo automático de comisiones

### 3. **Seguimiento de Clientes**
- Historial completo de interacciones
- Sistema de tareas y recordatorios
- Reportes de rendimiento

### 4. **Gestión de Proyectos**
- Control de inventario de unidades
- Asignación de asesores
- Seguimiento de ventas por proyecto

## Implementación y Uso

### Instalación
Los modelos están listos para usar en Laravel 10+ con las siguientes características:
- PHP 8.1+
- MySQL 8.0+ o PostgreSQL 13+
- Laravel Eloquent ORM

### Configuración
- Configurar conexión a base de datos
- Ejecutar migraciones para crear tablas
- Configurar seeders para datos de prueba

### Uso en Controladores
```php
use App\Models\Client;
use App\Models\Project;

// Ejemplo de uso básico
$clients = Client::active()->with('opportunities')->get();
$projects = Project::withAvailableUnits()->get();
```

## Mantenimiento y Evolución

### Versionado
- Los modelos siguen el versionado del proyecto
- Cambios importantes se documentan en cada modelo
- Compatibilidad hacia atrás cuando sea posible

### Testing
- Tests unitarios para cada modelo
- Tests de integración para relaciones
- Tests de performance para consultas complejas

### Documentación
- Documentación actualizada con cada cambio
- Ejemplos de uso prácticos
- Casos de uso comunes documentados

## Soporte y Contacto

Para consultas sobre los modelos o sugerencias de mejora:
- **Equipo de Desarrollo**: [Email del equipo]
- **Documentación**: [Enlace a documentación completa]
- **Issues**: [Repositorio de GitHub]

---

*Documentación generada el [Fecha] - Versión 1.0*
*Última actualización: [Fecha]*
