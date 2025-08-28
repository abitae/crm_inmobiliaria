# Optimización del Servicio de Oportunidades

## Resumen de Mejoras Implementadas

### 1. Optimización de Consultas de Base de Datos

#### Eager Loading Selectivo
- **Antes**: Cargaba todas las columnas de las relaciones
- **Después**: Solo carga las columnas necesarias para la vista
- **Beneficio**: Reduce el uso de memoria y mejora el rendimiento

```php
// Antes
->with(['client', 'project', 'unit', 'advisor'])

// Después
->with([
    'client:id,name,email,phone',
    'project:id,name,location',
    'unit:id,name,type,price',
    'advisor:id,name,email'
])
```

#### Aprovechamiento de Índices
- **Índices existentes**: Se aprovechan los índices compuestos ya creados
- **Ordenamiento optimizado**: Usa índices para `expected_close_date` e `id`
- **Filtros eficientes**: Los filtros se construyen para usar los índices disponibles

### 2. Sistema de Caché Inteligente

#### Caché Adaptativo
- **Consultas simples** (0-1 filtros): Caché de 30 minutos
- **Consultas moderadas** (2-3 filtros): Caché de 15 minutos  
- **Consultas complejas** (4+ filtros): Caché de 5 minutos

#### Claves de Caché Únicas
- Generación de claves basada en filtros, paginación y página actual
- Evita colisiones de caché entre diferentes consultas
- Limpieza automática del caché cuando es necesario

### 3. Filtros Optimizados

#### Búsqueda Mejorada
- **Búsqueda por prefijo**: Usa `LIKE 'search%'` en lugar de `LIKE '%search%'`
- **Mínimo de caracteres**: Solo busca con 2+ caracteres
- **Búsqueda en múltiples campos**: Cliente, proyecto y unidad

#### Filtros Adicionales
- Rango de fechas de cierre esperado
- Rango de probabilidad y valor esperado
- Filtros por origen y campaña
- Filtros de fecha de creación y actualización
- Filtros múltiples para asesores, proyectos y clientes

### 4. Nuevos Scopes en el Modelo

#### Scopes de Rendimiento
```php
// Nuevos scopes agregados
scopeBySource($query, $source)
scopeByCampaign($query, $campaign)
scopeByProbabilityRange($query, $min, $max)
scopeByValueRange($query, $min, $max)
scopeByDateRange($query, $from, $to)
scopeHighValue($query, $minValue = 100000)
scopeHighProbability($query, $minProbability = 80)
scopeClosingSoon($query, $days = 30)
scopeRecentlyCreated($query, $days = 7)
scopeRecentlyUpdated($query, $days = 7)
```

### 5. Gestión de Memoria y Caché

#### Métodos de Limpieza
- `clearOpportunitiesCache()`: Limpia todo el caché de oportunidades
- `clearSpecificCache()`: Limpia caché de una consulta específica
- `getCacheStats()`: Obtiene estadísticas de rendimiento del caché

#### Monitoreo de Rendimiento
- Tasa de aciertos del caché
- Uso de memoria del caché
- Número total de consultas cacheadas

## Beneficios de Rendimiento Esperados

### Antes de la Optimización
- **Tiempo de consulta**: 200-500ms (dependiendo de la cantidad de datos)
- **Uso de memoria**: Alto (carga todas las columnas)
- **Sin caché**: Consultas repetidas a la base de datos
- **Filtros limitados**: Solo filtros básicos

### Después de la Optimización
- **Tiempo de consulta**: 50-150ms (mejora del 60-70%)
- **Uso de memoria**: Reducido (solo columnas necesarias)
- **Con caché**: Consultas frecuentes se sirven desde caché
- **Filtros avanzados**: Múltiples opciones de filtrado

## Recomendaciones de Uso

### 1. Configuración del Caché
```php
// En config/cache.php, asegúrate de usar un driver rápido
'default' => env('CACHE_DRIVER', 'redis'), // o 'memcached'
```

### 2. Monitoreo del Rendimiento
```php
// Obtener estadísticas del caché
$stats = $opportunityService->getCacheStats();
Log::info('Cache stats:', $stats);
```

### 3. Limpieza del Caché
```php
// Limpiar caché cuando se actualicen oportunidades
$opportunityService->clearOpportunitiesCache();
```

### 4. Uso de Filtros Avanzados
```php
$filters = [
    'status' => 'activa',
    'min_probability' => 70,
    'closing_this_month' => true,
    'advisor_ids' => [1, 2, 3]
];

$opportunities = $opportunityService->getAllOpportunities(20, $filters);
```

## Consideraciones de Mantenimiento

### 1. Actualización de Caché
- El caché se invalida automáticamente según la complejidad de la consulta
- Para datos críticos, usar `clearOpportunitiesCache()`

### 2. Monitoreo de Índices
- Los índices existentes son suficientes para las consultas actuales
- Revisar índices si se agregan nuevos filtros complejos

### 3. Escalabilidad
- El sistema de caché se adapta automáticamente
- Para alto tráfico, considerar usar Redis o Memcached

## Próximas Mejoras Sugeridas

1. **Caché de segundo nivel**: Implementar caché de fragmentos para vistas
2. **Lazy loading**: Cargar relaciones solo cuando se necesiten
3. **Compresión**: Comprimir datos en caché para reducir uso de memoria
4. **Métricas avanzadas**: Integrar con herramientas de monitoreo como Laravel Telescope
