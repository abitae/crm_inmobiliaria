# Modelo Client

## Descripción
El modelo `Client` representa a los clientes y prospectos del CRM inmobiliario. Gestiona toda la información relacionada con los clientes, desde datos personales hasta su historial de interacciones y oportunidades.

## Tabla en Base de Datos
`clients`

## Campos

### Campos Principales
| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `id` | bigint | ID único del cliente | 1 |
| `first_name` | varchar(255) | Nombre del cliente | "Juan Carlos" |
| `last_name` | varchar(255) | Apellido del cliente | "García López" |
| `email` | varchar(255) | Correo electrónico | "juan.garcia@email.com" |
| `phone` | varchar(255) | Teléfono de contacto | "+51 999 123 456" |
| `document_type` | varchar(50) | Tipo de documento | "DNI", "RUC", "CE" |
| `document_number` | varchar(50) | Número de documento | "12345678" |
| `address` | text | Dirección del cliente | "Av. Arequipa 123" |
| `district` | varchar(100) | Distrito | "Miraflores" |
| `province` | varchar(100) | Provincia | "Lima" |
| `region` | varchar(100) | Región | "Lima" |
| `country` | varchar(100) | País | "Perú" |

### Campos de Clasificación
| Campo | Tipo | Descripción | Valores Posibles |
|-------|------|-------------|------------------|
| `client_type` | varchar(50) | Tipo de cliente | `inversor`, `comprador`, `empresa`, `constructor` |
| `source` | varchar(50) | Fuente de captación | `redes_sociales`, `ferias`, `referidos`, `formulario_web`, `publicidad` |
| `status` | varchar(50) | Estado del contacto | `nuevo`, `contacto_inicial`, `en_seguimiento`, `cierre`, `perdido` |
| `score` | integer | Puntuación del lead | 0-100 |

### Campos de Asignación
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `assigned_advisor_id` | bigint | ID del asesor asignado |
| `created_by` | bigint | ID del usuario que creó el cliente |
| `updated_by` | bigint | ID del usuario que actualizó por última vez |

### Campos de Auditoría
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `created_at` | timestamp | Fecha y hora de creación |
| `updated_at` | timestamp | Fecha y hora de última actualización |
| `deleted_at` | timestamp | Fecha y hora de eliminación (soft delete) |

## Relaciones

### Relaciones Directas
- **`assignedAdvisor()`** → `User` - Asesor asignado al cliente
- **`createdBy()`** → `User` - Usuario que creó el cliente
- **`updatedBy()`** → `User` - Usuario que actualizó por última vez

### Relaciones Uno a Muchos
- **`interactions()`** → `Interaction[]` - Historial de interacciones
- **`tasks()`** → `Task[]` - Tareas pendientes del cliente
- **`opportunities()`** → `Opportunity[]` - Oportunidades de venta
- **`activities()`** → `Activity[]` - Actividades programadas
- **`documents()`** → `Document[]` - Documentos asociados
- **`reservations()`** → `Reservation[]` - Reservas realizadas

### Relaciones Muchos a Muchos
- **`projects()`** → `Project[]` - Proyectos de interés (con pivot)
- **`units()`** → `Unit[]` - Unidades de interés (con pivot)

## Scopes Disponibles

### Filtros por Estado
```php
// Clientes activos (no perdidos)
Client::active()->get();

// Por estado específico
Client::byStatus('en_seguimiento')->get();

// Por tipo de cliente
Client::byType('inversor')->get();

// Por fuente de captación
Client::bySource('formulario_web')->get();

// Por asesor asignado
Client::byAdvisor($advisorId)->get();
```

## Accessors

### Información Formateada
```php
$client = Client::find(1);

// Nombre completo
echo $client->full_name; // "Juan Carlos García López"

// Dirección completa
echo $client->full_address; // "Av. Arequipa 123, Miraflores, Lima, Perú"
```

## Métodos Principales

### Gestión de Estado
```php
$client = Client::find(1);

// Actualizar puntuación
$client->updateScore(85);

// Cambiar estado
$client->changeStatus('en_seguimiento');

// Asignar asesor
$client->assignAdvisor($advisorId);
```

### Verificaciones
```php
$client = Client::find(1);

// Verificar si está activo
if ($client->isActive()) {
    // Cliente activo
}

// Verificar si tiene oportunidades activas
if ($client->hasActiveOpportunities()) {
    // Tiene oportunidades
}
```

## Ejemplos de Uso

### Crear un Nuevo Cliente
```php
$client = Client::create([
    'first_name' => 'María',
    'last_name' => 'Rodríguez',
    'email' => 'maria.rodriguez@email.com',
    'phone' => '+51 999 789 456',
    'document_type' => 'DNI',
    'document_number' => '87654321',
    'address' => 'Jr. Tacna 456',
    'district' => 'San Isidro',
    'province' => 'Lima',
    'region' => 'Lima',
    'country' => 'Perú',
    'client_type' => 'comprador',
    'source' => 'formulario_web',
    'status' => 'nuevo',
    'score' => 75,
    'assigned_advisor_id' => $advisorId,
    'created_by' => auth()->id(),
]);
```

### Buscar Clientes por Criterios
```php
// Clientes en seguimiento con puntuación alta
$highScoreClients = Client::byStatus('en_seguimiento')
    ->where('score', '>=', 80)
    ->get();

// Clientes por distrito específico
$mirafloresClients = Client::where('district', 'Miraflores')
    ->active()
    ->get();

// Clientes captados por redes sociales
$socialMediaClients = Client::bySource('redes_sociales')
    ->byStatus('nuevo')
    ->get();
```

### Obtener Información Relacionada
```php
$client = Client::with([
    'assignedAdvisor',
    'opportunities',
    'activities',
    'documents'
])->find(1);

// Asesor asignado
echo $client->assignedAdvisor->name;

// Oportunidades activas
$activeOpportunities = $client->opportunities()
    ->where('status', 'activa')
    ->get();

// Actividades de hoy
$todayActivities = $client->activities()
    ->today()
    ->get();
```

### Actualizar Información del Cliente
```php
$client = Client::find(1);

// Actualizar datos de contacto
$client->update([
    'phone' => '+51 999 999 999',
    'address' => 'Nueva dirección 789',
    'status' => 'en_seguimiento'
]);

// Actualizar puntuación
$client->updateScore(90);

// Cambiar asesor
$client->assignAdvisor($newAdvisorId);
```

## Validaciones Recomendadas

### Reglas de Validación
```php
$rules = [
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'email' => 'required|email|unique:clients,email,' . $clientId,
    'phone' => 'required|string|max:255',
    'document_type' => 'required|in:DNI,RUC,CE,PASAPORTE',
    'document_number' => 'required|string|max:50|unique:clients,document_number,' . $clientId,
    'client_type' => 'required|in:inversor,comprador,empresa,constructor',
    'source' => 'required|in:redes_sociales,ferias,referidos,formulario_web,publicidad',
    'status' => 'required|in:nuevo,contacto_inicial,en_seguimiento,cierre,perdido',
    'score' => 'nullable|integer|min:0|max:100',
];
```

## Eventos y Observers

### Eventos Recomendados
- `created` - Al crear un cliente
- `updated` - Al actualizar un cliente
- `deleted` - Al eliminar un cliente
- `status_changed` - Al cambiar el estado del cliente

### Observers Recomendados
```php
class ClientObserver
{
    public function created(Client $client)
    {
        // Asignar automáticamente a un asesor disponible
        // Enviar email de bienvenida
        // Crear tareas de seguimiento inicial
    }

    public function updated(Client $client)
    {
        // Registrar cambios en el historial
        // Notificar al asesor asignado si cambió
    }

    public function deleted(Client $client)
    {
        // Archivar documentos relacionados
        // Notificar al equipo de ventas
    }
}
```

## Consideraciones de Performance

### Índices Recomendados
```sql
-- Índices principales
CREATE INDEX idx_clients_status ON clients(status);
CREATE INDEX idx_clients_assigned_advisor ON clients(assigned_advisor_id);
CREATE INDEX idx_clients_source ON clients(source);
CREATE INDEX idx_clients_client_type ON clients(client_type);

-- Índices compuestos
CREATE INDEX idx_clients_status_advisor ON clients(status, assigned_advisor_id);
CREATE INDEX idx_clients_type_source ON clients(client_type, source);

-- Índices para búsquedas
CREATE INDEX idx_clients_email ON clients(email);
CREATE INDEX idx_clients_document ON clients(document_type, document_number);
```

### Consultas Optimizadas
```php
// Usar eager loading para evitar N+1 queries
$clients = Client::with(['assignedAdvisor', 'opportunities'])
    ->active()
    ->get();

// Usar scopes para filtros comunes
$clients = Client::byStatus('en_seguimiento')
    ->byAdvisor($advisorId)
    ->get();

// Paginar resultados para grandes volúmenes
$clients = Client::active()
    ->paginate(50);
```

## Casos de Uso Comunes

### 1. Captura de Leads
```php
// Crear cliente desde formulario web
$client = Client::create([
    'first_name' => $request->first_name,
    'last_name' => $request->last_name,
    'email' => $request->email,
    'phone' => $request->phone,
    'source' => 'formulario_web',
    'status' => 'nuevo',
    'score' => $this->calculateLeadScore($request),
]);

// Asignar automáticamente a asesor disponible
$availableAdvisor = User::advisors()->available()->first();
$client->assignAdvisor($availableAdvisor->id);
```

### 2. Seguimiento de Clientes
```php
// Obtener clientes que necesitan seguimiento
$clientsNeedingFollowUp = Client::byStatus('en_seguimiento')
    ->where('updated_at', '<', now()->subDays(7))
    ->get();

foreach ($clientsNeedingFollowUp as $client) {
    // Crear tarea de seguimiento
    Task::create([
        'title' => 'Seguimiento cliente: ' . $client->full_name,
        'task_type' => 'seguimiento',
        'client_id' => $client->id,
        'assigned_to' => $client->assigned_advisor_id,
        'due_date' => now()->addDays(2),
    ]);
}
```

### 3. Reportes de Clientes
```php
// Clientes por fuente de captación
$clientsBySource = Client::selectRaw('source, COUNT(*) as total')
    ->groupBy('source')
    ->get();

// Conversión por estado
$conversionByStatus = Client::selectRaw('status, COUNT(*) as total')
    ->groupBy('status')
    ->get();

// Rendimiento por asesor
$performanceByAdvisor = Client::selectRaw('assigned_advisor_id, COUNT(*) as total_clients')
    ->byStatus('cierre')
    ->groupBy('assigned_advisor_id')
    ->get();
```

## Notas de Implementación

- El modelo usa `SoftDeletes` para mantener historial
- Los campos `created_by` y `updated_by` se llenan automáticamente
- El scoring se puede calcular automáticamente basado en interacciones
- Las relaciones están optimizadas para evitar consultas N+1
- Se recomienda usar eventos para automatizar flujos de trabajo
