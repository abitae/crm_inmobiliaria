# Migraciones del CRM Inmobiliario

Este documento describe el orden de ejecución de las migraciones y las relaciones de dependencia entre las tablas.

## Orden de Ejecución

Las migraciones deben ejecutarse en el siguiente orden debido a las dependencias de claves foráneas:

### 1. Tablas Base (Sin Dependencias)
- `0001_01_01_000000_create_users_table.php` - Usuarios del sistema

### 2. Entidades Principales (Dependen de Users)
- `2024_01_01_000001_create_clients_table.php` - Clientes
- `2024_01_01_000002_create_projects_table.php` - Proyectos inmobiliarios

### 3. Entidades Secundarias (Dependen de Projects y Users)
- `2024_01_01_000003_create_units_table.php` - Unidades inmobiliarias

### 4. Entidades de Negocio (Dependen de Clients, Projects, Units y Users)
- `2024_01_01_000004_create_opportunities_table.php` - Oportunidades de venta
- `2024_01_01_000005_create_reservations_table.php` - Reservas

### 5. Entidades de Comisiones (Dependen de Users, Projects, Units y Opportunities)
- `2024_01_01_000006_create_commissions_table.php` - Comisiones de asesores

### 6. Entidades de Seguimiento (Dependen de múltiples entidades)
- `2024_01_01_000007_create_activities_table.php` - Actividades y eventos
- `2024_01_01_000008_create_tasks_table.php` - Tareas
- `2024_01_01_000009_create_interactions_table.php` - Interacciones con clientes

### 7. Entidades de Documentos (Dependen de múltiples entidades)
- `2024_01_01_000010_create_documents_table.php` - Documentos del sistema

### 8. Tablas Pivot (Relaciones Many-to-Many)
- `2024_01_01_000011_create_client_project_interests_table.php` - Intereses de clientes en proyectos
- `2024_01_01_000012_create_client_unit_interests_table.php` - Intereses de clientes en unidades
- `2024_01_01_000013_create_advisor_project_assignments_table.php` - Asignaciones de asesores a proyectos

### 9. Tablas de Precios (Dependen de Projects y Units)
- `2024_01_01_000014_create_project_prices_table.php` - Precios de proyectos
- `2024_01_01_000015_create_unit_prices_table.php` - Precios de unidades

## Relaciones de Dependencia

### Users (Tabla Base)
- No tiene dependencias
- Es referenciada por todas las demás tablas

### Clients
- **Depende de:** Users (assigned_advisor_id, created_by, updated_by)
- **Referenciada por:** Opportunities, Reservations, Activities, Tasks, Interactions, Documents

### Projects
- **Depende de:** Users (created_by, updated_by)
- **Referenciada por:** Units, Opportunities, Reservations, Activities, Tasks, Documents, Commissions
- **Relación Many-to-Many con:** Clients, Users (advisors)

### Units
- **Depende de:** Projects, Users (blocked_by, created_by, updated_by)
- **Referenciada por:** Opportunities, Reservations, Activities, Tasks, Documents, Commissions
- **Relación Many-to-Many con:** Clients

### Opportunities
- **Depende de:** Clients, Projects, Units (opcional), Users (advisor_id, created_by, updated_by)
- **Referenciada por:** Activities, Tasks, Interactions, Documents, Commissions

### Reservations
- **Depende de:** Clients, Projects, Units, Users (advisor_id, created_by, updated_by)
- **Referenciada por:** Documents

### Commissions
- **Depende de:** Users (advisor_id, approved_by, paid_by, created_by, updated_by), Projects, Units (opcional), Opportunities (opcional)

### Activities
- **Depende de:** Users (advisor_id, assigned_to, created_by, updated_by), Clients (opcional), Projects (opcional), Units (opcional), Opportunities (opcional)

### Tasks
- **Depende de:** Users (assigned_to, created_by, updated_by), Clients (opcional), Projects (opcional), Units (opcional), Opportunities (opcional)

### Interactions
- **Depende de:** Clients, Opportunities (opcional), Users (created_by, updated_by)

### Documents
- **Depende de:** Users (created_by, updated_by, reviewed_by, approved_by, signed_by), Clients (opcional), Projects (opcional), Units (opcional), Opportunities (opcional), Activities (opcional)

## Comandos de Ejecución

Para ejecutar las migraciones en el orden correcto:

```bash
# Ejecutar todas las migraciones
php artisan migrate

# Revertir todas las migraciones
php artisan migrate:rollback

# Ejecutar migraciones específicas
php artisan migrate --path=database/migrations/2024_01_01_000001_create_clients_table.php
```

## Notas Importantes

1. **Soft Deletes:** Todas las tablas principales implementan soft deletes para mantener el historial de datos.

2. **Índices:** Se han creado índices compuestos para optimizar las consultas más comunes.

3. **Constraints:** Se utilizan constraints de base de datos para mantener la integridad referencial.

4. **Timestamps:** Todas las tablas incluyen timestamps estándar de Laravel.

5. **Auditoría:** Se incluyen campos de auditoría (created_by, updated_by) en todas las tablas principales.

## Estructura de la Base de Datos

La base de datos está diseñada para soportar:
- Gestión completa de clientes y leads
- Administración de proyectos inmobiliarios
- Control de unidades y precios
- Seguimiento de oportunidades de venta
- Sistema de reservas
- Gestión de comisiones
- Seguimiento de actividades y tareas
- Almacenamiento de documentos
- Auditoría completa de cambios
