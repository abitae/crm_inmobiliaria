# Implementación del Sistema de Roles con Spatie Laravel Permission

## Cambios Realizados

### 1. Migración para quitar el campo `role`
Se creó la migración `2025_01_27_000000_remove_role_from_users_table.php` para eliminar el campo `role` de la tabla `users`.

### 2. Seeder de Roles y Permisos
Se creó `RolePermissionSeeder.php` que define:
- **Permisos**: view_dashboard, view_clients, create_clients, etc.
- **Roles**: admin, advisor, user
- **Asignación de permisos** a cada rol

### 3. Actualización del Modelo User
- Se quitó el campo `role` del `$fillable`
- Se quitó el cast del campo `role`
- Se actualizaron los métodos para usar Spatie:
  - `isAdmin()` → `hasRole('admin')`
  - `isAdvisor()` → `hasRole('advisor')`
  - `isAdminOrAdvisor()` → `hasAnyRole(['admin', 'advisor'])`

### 4. Middleware de Permisos
Se creó `CheckPermission` para verificar permisos en las rutas.

### 5. Actualización de Rutas
Se agregaron middlewares de permisos a todas las rutas del CRM.

### 6. Sidebar Dinámico
El sidebar ahora muestra opciones basadas en los permisos del usuario usando directivas `@can`.

### 7. Comando Artisan
Se creó `users:assign-roles` para asignar roles a usuarios existentes.

## Instrucciones de Implementación

### Paso 1: Ejecutar Migraciones
```bash
php artisan migrate
```

### Paso 2: Ejecutar Seeders
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Paso 3: Asignar Roles a Usuarios Existentes
```bash
php artisan users:assign-roles
```

### Paso 4: Ejecutar Seeder Completo (Opcional)
```bash
php artisan db:seed
```

## Estructura de Roles

### Admin
- Acceso completo a todas las funcionalidades
- Gestión de usuarios, roles y permisos
- Configuración del sistema

### Advisor
- Gestión de clientes, proyectos y oportunidades
- Creación y edición de reservas, comisiones
- Gestión de tareas y actividades
- Acceso a reportes

### User
- Visualización de información básica
- Creación y edición de tareas personales
- Acceso limitado a reportes

## Uso en el Código

### Verificar Roles
```php
if ($user->hasRole('admin')) {
    // Lógica para admin
}

if ($user->hasAnyRole(['admin', 'advisor'])) {
    // Lógica para admin o advisor
}
```

### Verificar Permisos
```php
if ($user->hasPermissionTo('create_clients')) {
    // Usuario puede crear clientes
}

if ($user->can('edit_projects')) {
    // Usuario puede editar proyectos
}
```

### En Vistas Blade
```blade
@can('view_clients')
    <a href="{{ route('clients.index') }}">Clientes</a>
@endcan

@role('admin')
    <a href="{{ route('admin.users') }}">Usuarios</a>
@endrole
```

### En Rutas
```php
Route::get('/clients', ClientList::class)
    ->middleware('permission:view_clients')
    ->name('clients.index');
```

## Beneficios de la Implementación

1. **Seguridad**: Control granular de acceso basado en permisos
2. **Flexibilidad**: Fácil asignación y modificación de roles
3. **Escalabilidad**: Sistema preparado para futuras funcionalidades
4. **Mantenibilidad**: Código más limpio y organizado
5. **Auditoría**: Seguimiento de permisos y roles

## Notas Importantes

- Los usuarios existentes se les asigna el rol 'user' por defecto
- Se recomienda revisar y ajustar los permisos según las necesidades específicas
- El sistema es compatible con Laravel 12 y Livewire
- Se mantiene la funcionalidad existente del CRM
