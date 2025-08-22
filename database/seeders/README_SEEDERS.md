# Seeders del CRM Inmobiliario

Este documento describe los seeders disponibles para poblar la base de datos con datos de prueba.

## 📋 Seeders Disponibles

### 1. **UserSeeder** - Usuarios del Sistema
- **Dependencias:** Ninguna
- **Datos creados:**
  - 1 Usuario Administrador
  - 5 Asesores de Ventas
  - 2 Usuarios Adicionales (Gerente, Supervisor)
- **Total:** 8 usuarios

### 2. **ClientSeeder** - Clientes y Leads
- **Dependencias:** UserSeeder
- **Datos creados:**
  - 8 Clientes realistas con datos completos
  - 20 Clientes adicionales usando factory
- **Total:** 28 clientes

### 3. **ProjectSeeder** - Proyectos Inmobiliarios
- **Dependencias:** UserSeeder
- **Datos creados:**
  - 6 Proyectos realistas (Miraflores, San Isidro, Barranco, Surco, Chorrillos, San Borja)
  - 8 Proyectos adicionales usando factory
- **Total:** 14 proyectos

### 4. **UnitSeeder** - Unidades Inmobiliarias
- **Dependencias:** ProjectSeeder, UserSeeder
- **Datos creados:**
  - Unidades basadas en la configuración de cada proyecto
  - Diferentes tipos: lotes, casas, departamentos, oficinas
  - Estados variados: disponible, reservado, vendido
- **Total:** Variable según proyectos

### 5. **OpportunitySeeder** - Oportunidades de Venta
- **Dependencias:** ClientSeeder, ProjectSeeder, UnitSeeder, UserSeeder
- **Datos creados:**
  - 7 Oportunidades realistas con datos completos
  - 25 Oportunidades adicionales aleatorias
- **Total:** 32 oportunidades

### 6. **ReservationSeeder** - Sistema de Reservas
- **Dependencias:** ClientSeeder, ProjectSeeder, UnitSeeder, UserSeeder
- **Datos creados:**
  - 5 Reservas realistas con datos completos
  - 20 Reservas adicionales aleatorias
- **Total:** 25 reservas

### 7. **CommissionSeeder** - Gestión de Comisiones
- **Dependencias:** UserSeeder, ProjectSeeder, UnitSeeder, OpportunitySeeder
- **Datos creados:**
  - 5 Comisiones realistas con datos completos
  - 30 Comisiones adicionales aleatorias
- **Total:** 35 comisiones

### 8. **ActivitySeeder** - Actividades y Seguimiento
- **Dependencias:** ClientSeeder, ProjectSeeder, UnitSeeder, OpportunitySeeder, UserSeeder
- **Datos creados:**
  - 5 Actividades realistas con datos completos
  - 40 Actividades adicionales aleatorias
- **Total:** 45 actividades

### 9. **RelationshipSeeder** - Relaciones y Precios
- **Dependencias:** Todos los seeders anteriores
- **Datos creados:**
  - Relaciones many-to-many entre clientes y proyectos
  - Relaciones many-to-many entre clientes y unidades
  - Asignaciones de asesores a proyectos
  - Historial de precios de proyectos
  - Historial de precios de unidades
- **Total:** Variable según entidades existentes

## 🚀 Cómo Ejecutar los Seeders

### Ejecutar Todos los Seeders
```bash
php artisan db:seed
```

### Ejecutar Seeders Específicos
```bash
# Solo usuarios
php artisan db:seed --class=UserSeeder

# Solo clientes
php artisan db:seed --class=ClientSeeder

# Solo proyectos
php artisan db:seed --class=ProjectSeeder
```

### Ejecutar en Orden Manual
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ClientSeeder
php artisan db:seed --class=ProjectSeeder
php artisan db:seed --class=UnitSeeder
php artisan db:seed --class=OpportunitySeeder
php artisan db:seed --class=ReservationSeeder
php artisan db:seed --class=CommissionSeeder
php artisan db:seed --class=ActivitySeeder
php artisan db:seed --class=RelationshipSeeder
```

## 🔑 Usuarios de Prueba Creados

### Administrador
- **Email:** admin@crm.com
- **Password:** password
- **Rol:** Administrador del sistema

### Asesores de Ventas
- **Email:** maria.gonzalez@crm.com
- **Password:** password
- **Rol:** Asesor de ventas

- **Email:** carlos.rodriguez@crm.com
- **Password:** password
- **Rol:** Asesor de ventas

- **Email:** ana.martinez@crm.com
- **Password:** password
- **Rol:** Asesor de ventas

- **Email:** luis.perez@crm.com
- **Password:** password
- **Rol:** Asesor de ventas

- **Email:** sofia.lopez@crm.com
- **Password:** password
- **Rol:** Asesor de ventas

### Usuarios Adicionales
- **Email:** gerente@crm.com
- **Password:** password
- **Rol:** Gerente de ventas

- **Email:** supervisor@crm.com
- **Password:** password
- **Rol:** Supervisor comercial

## 📊 Datos de Prueba Generados

### Clientes de Ejemplo
1. **Juan Carlos Vargas Mendoza** - Comprador interesado en departamentos
2. **María Elena Torres Ríos** - Inversora buscando propiedades para alquiler
3. **Roberto Silva Castro** - Empresa buscando oficinas corporativas
4. **Carmen Flores Díaz** - Cliente VIP buscando casa familiar
5. **Fernando Mendoza Ruiz** - Constructor interesado en lotes
6. **Patricia Ríos Morales** - Inversora en propiedades de playa
7. **Alberto García Paredes** - Cliente que ya compró
8. **Lucía Herrera Vega** - Interesada en propiedades con vista al mar

### Proyectos de Ejemplo
1. **Residencial Miraflores Park** - Departamentos de lujo en Miraflores
2. **Torres San Isidro Business** - Oficinas corporativas en San Isidro
3. **Lotes Barranco Golf** - Lotes residenciales con vista al mar
4. **Casas Surco Family** - Casas familiares en Surco
5. **Mixto Chorrillos Plaza** - Proyecto mixto en Chorrillos
6. **Oficinas San Borja Center** - Centro empresarial en San Borja

## 🔗 Relaciones Creadas

### Intereses de Clientes
- Cada cliente está interesado en 1-3 proyectos
- Cada cliente está interesado en 1-5 unidades específicas
- Niveles de interés: bajo, medio, alto, muy alto

### Asignaciones de Asesores
- Cada proyecto tiene 1-3 asesores asignados
- Un asesor principal por proyecto
- Asesores de apoyo adicionales

### Historial de Precios
- **Proyectos:** 3 versiones de precios con evolución temporal
- **Unidades:** 2 versiones de precios con variaciones
- Precios activos e históricos para análisis

## ⚠️ Notas Importantes

1. **Orden de Ejecución:** Los seeders están diseñados para ejecutarse en el orden especificado debido a las dependencias de claves foráneas.

2. **Datos Realistas:** Los seeders crean datos realistas que simulan un entorno de producción real.

3. **Relaciones:** Todas las entidades están correctamente relacionadas entre sí.

4. **Soft Deletes:** Los datos se crean con soft deletes habilitados para mantener el historial.

5. **Auditoría:** Todos los registros incluyen campos de auditoría (created_by, updated_by).

6. **Relaciones Many-to-Many:** Se crean automáticamente las relaciones entre entidades.

## 🧹 Limpiar la Base de Datos

Si necesitas limpiar la base de datos y volver a ejecutar los seeders:

```bash
# Revertir todas las migraciones
php artisan migrate:rollback

# Ejecutar migraciones nuevamente
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

## 🔄 Actualizar Datos Existentes

Si ya tienes datos y quieres agregar más:

```bash
# Ejecutar solo seeders específicos
php artisan db:seed --class=ClientSeeder
php artisan db:seed --class=ProjectSeeder
```

## 📈 Personalización

Puedes modificar los seeders para:
- Cambiar la cantidad de datos generados
- Modificar los valores por defecto
- Agregar nuevos tipos de datos
- Ajustar las relaciones entre entidades
- Modificar la lógica de creación de relaciones

Los seeders están diseñados para ser flexibles y fáciles de personalizar según tus necesidades específicas.

## 📊 Estadísticas de Datos Generados

Al ejecutar todos los seeders, se crean aproximadamente:
- **8 usuarios** del sistema
- **28 clientes** con perfiles variados
- **14 proyectos** inmobiliarios
- **Variable unidades** según proyectos
- **32 oportunidades** de venta
- **25 reservas** activas
- **35 comisiones** para asesores
- **45 actividades** de seguimiento
- **Relaciones many-to-many** entre entidades
- **Historial de precios** para análisis temporal
