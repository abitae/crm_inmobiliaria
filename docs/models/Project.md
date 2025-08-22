# Modelo Project

## Descripción
El modelo `Project` representa a los proyectos inmobiliarios del CRM. Gestiona toda la información relacionada con los proyectos, incluyendo ubicación, características, unidades disponibles y estado del proyecto.

## Tabla en Base de Datos
`projects`

## Campos

### Campos Principales
| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `id` | bigint | ID único del proyecto | 1 |
| `name` | varchar(255) | Nombre del proyecto | "Residencial Los Pinos" |
| `description` | text | Descripción detallada | "Proyecto residencial de lujo..." |
| `project_type` | varchar(50) | Tipo de proyecto | `lotes`, `casas`, `departamentos`, `oficinas`, `mixto` |
| `stage` | varchar(50) | Etapa del proyecto | `preventa`, `lanzamiento`, `venta_activa`, `cierre` |
| `legal_status` | varchar(50) | Estado legal | `con_titulo`, `en_tramite`, `habilitado` |

### Campos de Ubicación
| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `address` | text | Dirección del proyecto | "Av. Javier Prado 1234" |
| `district` | varchar(100) | Distrito | "San Isidro" |
| `province` | varchar(100) | Provincia | "Lima" |
| `region` | varchar(100) | Región | "Lima" |
| `country` | varchar(100) | País | "Perú" |
| `latitude` | decimal(8,6) | Latitud GPS | -12.123456 |
| `longitude` | decimal(8,6) | Longitud GPS | -77.123456 |

### Campos de Unidades
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `total_units` | integer | Total de unidades del proyecto |
| `available_units` | integer | Unidades disponibles |
| `reserved_units` | integer | Unidades reservadas |
| `sold_units` | integer | Unidades vendidas |
| `blocked_units` | integer | Unidades bloqueadas |

### Campos de Fechas
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `start_date` | date | Fecha de inicio del proyecto |
| `end_date` | date | Fecha de finalización del proyecto |
| `delivery_date` | date | Fecha de entrega a clientes |

### Campos de Estado
| Campo | Tipo | Descripción | Valores Posibles |
|-------|------|-------------|------------------|
| `status` | varchar(50) | Estado general | `activo`, `inactivo`, `suspendido`, `finalizado` |

### Campos de Auditoría
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `created_by` | bigint | ID del usuario que creó el proyecto |
| `updated_by` | bigint | ID del usuario que actualizó por última vez |
| `created_at` | timestamp | Fecha y hora de creación |
| `updated_at` | timestamp | Fecha y hora de última actualización |
| `deleted_at` | timestamp | Fecha y hora de eliminación (soft delete) |

## Relaciones

### Relaciones Directas
- **`createdBy()`** → `User` - Usuario que creó el proyecto
- **`updatedBy()`** → `User` - Usuario que actualizó por última vez

### Relaciones Uno a Muchos
- **`units()`** → `Unit[]` - Unidades inmobiliarias del proyecto
- **`prices()`** → `ProjectPrice[]` - Historial de precios del proyecto
- **`documents()`** → `Document[]` - Documentos del proyecto
- **`activities()`** → `Activity[]` - Actividades relacionadas
- **`opportunities()`** → `Opportunity[]` - Oportunidades de venta
- **`commissions()`** → `Commission[]` - Comisiones del proyecto

### Relaciones Muchos a Muchos
- **`clients()`** → `Client[]` - Clientes interesados (con pivot)
- **`advisors()`** → `User[]` - Asesores asignados (con pivot)

## Scopes Disponibles

### Filtros por Estado
```php
// Proyectos activos
Project::active()->get();

// Por tipo específico
Project::byType('departamentos')->get();

// Por etapa del proyecto
Project::byStage('venta_activa')->get();

// Por estado general
Project::byStatus('activo')->get();

// Con unidades disponibles
Project::withAvailableUnits()->get();
```

### Filtros por Ubicación
```php
// Por distrito específico
Project::byLocation('Miraflores')->get();

// Por distrito y provincia
Project::byLocation('Miraflores', 'Lima')->get();

// Por región completa
Project::byLocation(null, null, 'Lima')->get();
```

## Accessors

### Información Formateada
```php
$project = Project::find(1);

// Dirección completa
echo $project->full_address; // "Av. Javier Prado 1234, San Isidro, Lima, Perú"

// Coordenadas GPS
$coordinates = $project->coordinates; // ['lat' => -12.123456, 'lng' => -77.123456]

// Porcentaje de progreso
echo $project->progress_percentage; // 75.5
```

## Métodos Principales

### Gestión de Unidades
```php
$project = Project::find(1);

// Actualizar conteo de unidades
$project->updateUnitCounts();

// Verificar si tiene unidades disponibles
if ($project->hasAvailableUnits()) {
    // Tiene unidades disponibles
}

// Verificar si puede aceptar reservas
if ($project->canAcceptReservations()) {
    // Puede aceptar reservas
}
```

### Gestión de Asesores
```php
$project = Project::find(1);

// Asignar asesor
$project->assignAdvisor($advisorId, true, 'Asesor principal');

// Remover asesor
$project->removeAdvisor($advisorId);

// Obtener precio actual
$currentPrice = $project->getCurrentPrice();
```

### Verificaciones
```php
$project = Project::find(1);

// Verificar si está activo
if ($project->isActive()) {
    // Proyecto activo
}
```

## Ejemplos de Uso

### Crear un Nuevo Proyecto
```php
$project = Project::create([
    'name' => 'Residencial Los Pinos',
    'description' => 'Proyecto residencial de lujo en San Isidro',
    'project_type' => 'departamentos',
    'stage' => 'preventa',
    'legal_status' => 'con_titulo',
    'address' => 'Av. Javier Prado 1234',
    'district' => 'San Isidro',
    'province' => 'Lima',
    'region' => 'Lima',
    'country' => 'Perú',
    'latitude' => -12.123456,
    'longitude' => -77.123456,
    'total_units' => 120,
    'available_units' => 120,
    'start_date' => '2025-01-01',
    'end_date' => '2027-12-31',
    'delivery_date' => '2028-06-30',
    'status' => 'activo',
    'created_by' => auth()->id(),
]);
```

### Buscar Proyectos por Criterios
```php
// Proyectos activos con unidades disponibles
$availableProjects = Project::active()
    ->withAvailableUnits()
    ->get();

// Proyectos por tipo y etapa
$apartmentProjects = Project::byType('departamentos')
    ->byStage('venta_activa')
    ->get();

// Proyectos por ubicación
$mirafloresProjects = Project::byLocation('Miraflores')
    ->active()
    ->get();
```

### Obtener Información Relacionada
```php
$project = Project::with([
    'units',
    'prices',
    'advisors',
    'clients'
])->find(1);

// Unidades disponibles
$availableUnits = $project->units()
    ->available()
    ->get();

// Asesores asignados
$assignedAdvisors = $project->advisors()
    ->wherePivot('is_primary', true)
    ->get();

// Clientes interesados
$interestedClients = $project->clients()
    ->wherePivot('interest_level', 'alto')
    ->get();
```

### Actualizar Información del Proyecto
```php
$project = Project::find(1);

// Cambiar etapa del proyecto
$project->update(['stage' => 'venta_activa']);

// Actualizar conteo de unidades
$project->updateUnitCounts();

// Asignar nuevo asesor
$project->assignAdvisor($newAdvisorId, false, 'Asesor secundario');
```

## Validaciones Recomendadas

### Reglas de Validación
```php
$rules = [
    'name' => 'required|string|max:255|unique:projects,name,' . $projectId,
    'description' => 'nullable|string',
    'project_type' => 'required|in:lotes,casas,departamentos,oficinas,mixto',
    'stage' => 'required|in:preventa,lanzamiento,venta_activa,cierre',
    'legal_status' => 'required|in:con_titulo,en_tramite,habilitado',
    'address' => 'required|string',
    'district' => 'required|string|max:100',
    'province' => 'required|string|max:100',
    'region' => 'required|string|max:100',
    'country' => 'required|string|max:100',
    'latitude' => 'nullable|numeric|between:-90,90',
    'longitude' => 'nullable|numeric|between:-180,180',
    'total_units' => 'required|integer|min:1',
    'start_date' => 'nullable|date',
    'end_date' => 'nullable|date|after:start_date',
    'delivery_date' => 'nullable|date|after:end_date',
    'status' => 'required|in:activo,inactivo,suspendido,finalizado',
];
```

## Eventos y Observers

### Eventos Recomendados
- `created` - Al crear un proyecto
- `updated` - Al actualizar un proyecto
- `deleted` - Al eliminar un proyecto
- `stage_changed` - Al cambiar la etapa del proyecto
- `status_changed` - Al cambiar el estado del proyecto

### Observers Recomendados
```php
class ProjectObserver
{
    public function created(Project $project)
    {
        // Crear unidades automáticamente
        // Asignar asesores por defecto
        // Crear documentos iniciales
    }

    public function updated(Project $project)
    {
        // Actualizar conteo de unidades si cambió
        // Notificar cambios importantes al equipo
    }

    public function deleted(Project $project)
    {
        // Archivar unidades relacionadas
        // Notificar al equipo de ventas
    }
}
```

## Consideraciones de Performance

### Índices Recomendados
```sql
-- Índices principales
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_projects_stage ON projects(stage);
CREATE INDEX idx_projects_type ON projects(project_type);
CREATE INDEX idx_projects_location ON projects(district, province, region);

-- Índices compuestos
CREATE INDEX idx_projects_status_stage ON projects(status, stage);
CREATE INDEX idx_projects_type_status ON projects(project_type, status);

-- Índices para búsquedas geográficas
CREATE INDEX idx_projects_coordinates ON projects(latitude, longitude);
CREATE INDEX idx_projects_units ON projects(available_units, total_units);
```

### Consultas Optimizadas
```php
// Usar eager loading para evitar N+1 queries
$projects = Project::with(['units', 'advisors', 'prices'])
    ->active()
    ->get();

// Usar scopes para filtros comunes
$projects = Project::byType('departamentos')
    ->byStage('venta_activa')
    ->withAvailableUnits()
    ->get();

// Paginar resultados para grandes volúmenes
$projects = Project::active()
    ->paginate(20);
```

## Casos de Uso Comunes

### 1. Lanzamiento de Proyecto
```php
// Crear proyecto en preventa
$project = Project::create([
    'name' => 'Nuevo Residencial',
    'project_type' => 'departamentos',
    'stage' => 'preventa',
    'status' => 'activo',
    // ... otros campos
]);

// Crear unidades automáticamente
for ($i = 1; $i <= 50; $i++) {
    Unit::create([
        'project_id' => $project->id,
        'unit_number' => $i,
        'unit_type' => 'departamento',
        'status' => 'disponible',
        // ... otros campos
    ]);
}

// Asignar asesores
$project->assignAdvisor($primaryAdvisorId, true, 'Asesor principal');
$project->assignAdvisor($secondaryAdvisorId, false, 'Asesor secundario');
```

### 2. Seguimiento de Ventas
```php
// Obtener proyectos con mejor rendimiento
$topProjects = Project::selectRaw('
        projects.*,
        COUNT(opportunities.id) as total_opportunities,
        SUM(CASE WHEN opportunities.status = "ganada" THEN 1 ELSE 0 END) as won_opportunities
    ')
    ->leftJoin('opportunities', 'projects.id', '=', 'opportunities.project_id')
    ->groupBy('projects.id')
    ->orderByRaw('won_opportunities / total_opportunities DESC')
    ->get();
```

### 3. Reportes de Proyectos
```php
// Progreso de ventas por proyecto
$salesProgress = Project::selectRaw('
        name,
        total_units,
        sold_units,
        reserved_units,
        ROUND((sold_units + reserved_units) / total_units * 100, 2) as progress_percentage
    ')
    ->active()
    ->get();

// Proyectos por etapa
$projectsByStage = Project::selectRaw('stage, COUNT(*) as total_projects')
    ->groupBy('stage')
    ->get();
```

## Integración con Mapas

### Coordenadas GPS
```php
// Obtener proyectos en un radio específico
$centerLat = -12.123456;
$centerLng = -77.123456;
$radiusKm = 5;

$nearbyProjects = Project::selectRaw('
        *,
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
         cos(radians(longitude) - radians(?)) + 
         sin(radians(?)) * sin(radians(latitude)))) AS distance
    ', [$centerLat, $centerLng, $centerLat])
    ->having('distance', '<=', $radiusKm)
    ->orderBy('distance')
    ->get();
```

## Notas de Implementación

- El modelo usa `SoftDeletes` para mantener historial
- Los campos `created_by` y `updated_by` se llenan automáticamente
- El conteo de unidades se actualiza automáticamente
- Las coordenadas GPS permiten integración con mapas
- Se recomienda usar eventos para automatizar flujos de trabajo
- El modelo soporta múltiples asesores por proyecto
