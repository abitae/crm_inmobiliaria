# Implementación del Sistema de Roles con Spatie Laravel Permission

## Cambios Realizados

### 1. Migración para quitar el campo `role`
Se creó la migración `2025_01_27_000000_remove_role_from_users_table.php` para eliminar el campo `role` de la tabla `users`.

### 2. Seeder de Roles y Permisos
Se creó `RolePermissionSeeder.php` que define:
- **Permisos**: view_dashboard, view_clients, create_clients, etc.
- **Roles**: admin, lider, vendedor, cliente
- **Asignación de permisos** a cada rol

### 3. Actualización del Modelo User
- Se quitó el campo `role` del `$fillable`
- Se quitó el cast del campo `role`
- Se actualizaron los métodos para usar Spatie:
  - `isAdmin()` → `hasRole('admin')`
  - `isAdvisor()` → `hasRole('vendedor')`
  - `getAvailableAdvisors()` → obtiene usuarios que pueden ser asignados como asesores

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

## Jerarquía Organizacional

```
Admin (Acceso Completo)
    ↓
Lider (Jefe de Vendedores)
    ↓
Vendedor (Gestión de Ventas)
    ↓
Cliente (Acceso Básico)
```

## Estructura de Roles

### Admin
- **Acceso completo** a todas las funcionalidades del sistema
- Gestión de usuarios, roles y permisos
- Configuración del sistema
- Supervisión de todos los niveles

### Lider
- **Jefe de los vendedores** - supervisión directa
- Gestión de clientes, proyectos y oportunidades
- Creación y edición de reservas, comisiones
- Gestión de tareas y actividades
- Acceso a reportes y configuración
- **Gestión de usuarios vendedores** (crear, editar, supervisar)

### Vendedor
- Gestión de clientes, proyectos y oportunidades
- Creación y edición de reservas, comisiones
- Gestión de tareas y actividades
- Acceso a reportes
- Reporta al lider

### Cliente
- Visualización de información básica
- Acceso a sus propias oportunidades y reservas

## Asignación de Roles

### Principio de Un Solo Rol
- Cada usuario en el sistema tiene **únicamente un rol**
- Se usa `assignRole()` para asignar el rol único
- No se usan `syncRoles()` o métodos que permitan múltiples roles

### Asignación en Registro
```php
// En Register.php - línea 62
$user->assignRole('vendedor');
```

### Asignación en Seeders
```php
// En UserSeeder.php
$admin->assignRole('admin');
$user->assignRole('lider');
$user->assignRole('vendedor');
$user->assignRole('cliente');
```

## Uso en el Código

### Verificar Roles
```php
if ($user->hasRole('admin')) {
    // Lógica para admin
}

if ($user->hasRole('vendedor')) {
    // Lógica específica para vendedores
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

- **Cada usuario tiene un solo rol**: El sistema está diseñado para que cada usuario tenga únicamente un rol asignado
- Los usuarios existentes se les asigna el rol 'vendedor' por defecto
- Se recomienda revisar y ajustar los permisos según las necesidades específicas
- El sistema es compatible con Laravel 12 y Livewire
- Se mantiene la funcionalidad existente del CRM
- La función `getAvailableAdvisors()` obtiene usuarios que pueden actuar como asesores (admin y vendedor), aunque cada usuario individualmente solo tiene un rol
- El rol `admin` tiene acceso completo a todas las funcionalidades del sistema
