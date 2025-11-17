# API REST - Proyectos Publicados

API REST pública para consultar proyectos inmobiliarios publicados. Esta API está diseñada para ser consumida por aplicaciones web, móviles o cualquier cliente que necesite mostrar información de proyectos disponibles.

## 🚀 Características

- ✅ **Rutas públicas** - No requieren autenticación
- ✅ Solo muestra proyectos con `is_published = true`
- ✅ Filtros avanzados por tipo, ubicación, etapa, estado legal, etc.
- ✅ Búsqueda por nombre, descripción o ubicación
- ✅ Paginación eficiente
- ✅ Rate limit: 120 solicitudes por minuto
- ✅ Respuestas estandarizadas y consistentes
- ✅ Optimizada para consumo web y móvil

## 📋 Tabla de Contenidos

1. [Configuración Base](#configuración-base)
2. [Endpoints](#endpoints)
   - [Listar Proyectos](#1-listar-proyectos-publicados-get)
   - [Ver Proyecto Específico](#2-ver-proyecto-publicado-específico-get)
   - [Obtener Unidades](#3-obtener-unidades-de-un-proyecto-get)
3. [Modelos de Datos](#modelos-de-datos)
4. [Filtros y Búsqueda](#filtros-y-búsqueda)
5. [Manejo de Errores](#manejo-de-errores)
6. [Rate Limiting](#rate-limiting)
7. [Ejemplos de Uso](#ejemplos-de-uso)

---

## 🔧 Configuración Base

### Base URL

```
https://crm_inmobiliaria.test/api
```

### Headers Comunes

Todas las peticiones requieren:

```
Content-Type: application/json
Accept: application/json
```

**Nota:** Estas rutas son públicas y no requieren autenticación.

### Formato de Respuesta Estándar

Todas las respuestas siguen este formato:

**Éxito:**
```json
{
    "success": true,
    "message": "Mensaje descriptivo",
    "data": { /* datos de la respuesta */ }
}
```

**Error:**
```json
{
    "success": false,
    "message": "Mensaje de error",
    "errors": { /* detalles de errores (opcional) */ }
}
```

---

## 📡 Endpoints

### 1. Listar Proyectos Publicados (GET)

Obtener lista paginada de proyectos publicados con filtros opcionales.

**Endpoint:** `GET /projects`

**Rate Limit:** 120 solicitudes por minuto

**Query Parameters (todos opcionales):**

| Parámetro | Tipo | Descripción | Valores |
|-----------|------|-------------|---------|
| `per_page` | integer | Elementos por página | 1-100 (default: 15) |
| `search` | string | Búsqueda en nombre, descripción o ubicación | Cualquier texto |
| `project_type` | string | Filtrar por tipo de proyecto | Ver [Tipos de Proyecto](#tipos-de-proyecto) |
| `lote_type` | string | Filtrar por tipo de lote | Ver [Tipos de Lote](#tipos-de-lote) |
| `stage` | string | Filtrar por etapa | Ver [Etapas](#etapas) |
| `legal_status` | string | Filtrar por estado legal | Ver [Estados Legales](#estados-legales) |
| `status` | string | Filtrar por estado | Ver [Estados](#estados) |
| `district` | string | Filtrar por distrito | Nombre del distrito |
| `province` | string | Filtrar por provincia | Nombre de la provincia |
| `region` | string | Filtrar por región | Nombre de la región |
| `has_available_units` | boolean | Solo proyectos con unidades disponibles | `true`, `false` (default: `false`) |

**Ejemplo de Request:**
```
GET /api/projects?per_page=20&search=Lima&stage=venta_activa&has_available_units=true
```

**Response 200:**
```json
{
    "success": true,
    "message": "Proyectos obtenidos exitosamente",
    "data": {
        "projects": [
            {
                "id": 1,
                "name": "Residencial Los Olivos",
                "description": "Moderno proyecto residencial en zona exclusiva",
                "project_type": "lotes",
                "lote_type": "normal",
                "stage": "venta_activa",
                "legal_status": "con_titulo",
                "address": "Av. Principal 123",
                "district": "San Isidro",
                "province": "Lima",
                "region": "Lima",
                "country": "Perú",
                "ubicacion": "https://maps.google.com/?q=-12.0969,-77.0338",
                "full_address": "Av. Principal 123, San Isidro, Lima, Lima, Perú",
                "coordinates": {
                    "lat": -12.0969,
                    "lng": -77.0338
                },
                "total_units": 50,
                "available_units": 15,
                "reserved_units": 10,
                "sold_units": 20,
                "blocked_units": 5,
                "progress_percentage": 60.0,
                "start_date": "2024-01-01",
                "end_date": "2025-12-31",
                "delivery_date": "2026-06-30",
                "status": "activo",
                "path_image_portada": "/storage/projects/1/portada.jpg",
                "path_video_portada": "/storage/projects/1/video.mp4",
                "path_images": [
                    "/storage/projects/1/image1.jpg",
                    "/storage/projects/1/image2.jpg"
                ],
                "path_videos": [
                    "/storage/projects/1/video1.mp4"
                ],
                "path_documents": [
                    "/storage/projects/1/documento1.pdf"
                ],
                "created_at": "2024-01-15 10:30:00",
                "updated_at": "2024-01-15 10:30:00"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 45,
            "last_page": 3,
            "from": 1,
            "to": 20
        }
    }
}
```

**Campos de la Respuesta:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | integer | ID único del proyecto |
| `name` | string | Nombre del proyecto |
| `description` | string | Descripción del proyecto |
| `project_type` | string | Tipo de proyecto |
| `lote_type` | string | Tipo de lote |
| `stage` | string | Etapa del proyecto |
| `legal_status` | string | Estado legal |
| `address` | string | Dirección |
| `district` | string | Distrito |
| `province` | string | Provincia |
| `region` | string | Región |
| `country` | string | País |
| `ubicacion` | string | URL de Google Maps |
| `full_address` | string | Dirección completa formateada |
| `coordinates` | object | Coordenadas GPS `{lat, lng}` |
| `total_units` | integer | Total de unidades |
| `available_units` | integer | Unidades disponibles |
| `reserved_units` | integer | Unidades reservadas |
| `sold_units` | integer | Unidades vendidas |
| `blocked_units` | integer | Unidades bloqueadas |
| `progress_percentage` | float | Porcentaje de avance (vendidas + reservadas) |
| `start_date` | date | Fecha de inicio (YYYY-MM-DD) |
| `end_date` | date | Fecha de fin (YYYY-MM-DD) |
| `delivery_date` | date | Fecha de entrega (YYYY-MM-DD) |
| `status` | string | Estado del proyecto |
| `path_image_portada` | string | Ruta de imagen de portada |
| `path_video_portada` | string | Ruta de video de portada |
| `path_images` | array | Array de rutas de imágenes |
| `path_videos` | array | Array de rutas de videos |
| `path_documents` | array | Array de rutas de documentos |
| `created_at` | datetime | Fecha de creación |
| `updated_at` | datetime | Fecha de actualización |

---

### 2. Ver Proyecto Publicado Específico (GET)

Obtener información detallada de un proyecto publicado.

**Endpoint:** `GET /projects/{id}`

**Parámetros de URL:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID del proyecto |

**Response 200:**
```json
{
    "success": true,
    "message": "Proyecto obtenido exitosamente",
    "data": {
        "project": {
            "id": 1,
            "name": "Residencial Los Olivos",
            "description": "Moderno proyecto residencial en zona exclusiva",
            "project_type": "lotes",
            "lote_type": "normal",
            "stage": "venta_activa",
            "legal_status": "con_titulo",
            "address": "Av. Principal 123",
            "district": "San Isidro",
            "province": "Lima",
            "region": "Lima",
            "country": "Perú",
            "ubicacion": "https://maps.google.com/?q=-12.0969,-77.0338",
            "full_address": "Av. Principal 123, San Isidro, Lima, Lima, Perú",
            "coordinates": {
                "lat": -12.0969,
                "lng": -77.0338
            },
            "total_units": 50,
            "available_units": 15,
            "reserved_units": 10,
            "sold_units": 20,
            "blocked_units": 5,
            "progress_percentage": 60.0,
            "start_date": "2024-01-01",
            "end_date": "2025-12-31",
            "delivery_date": "2026-06-30",
            "status": "activo",
            "path_image_portada": "/storage/projects/1/portada.jpg",
            "path_video_portada": "/storage/projects/1/video.mp4",
            "path_images": [
                "/storage/projects/1/image1.jpg",
                "/storage/projects/1/image2.jpg"
            ],
            "path_videos": [
                "/storage/projects/1/video1.mp4"
            ],
            "path_documents": [
                "/storage/projects/1/documento1.pdf"
            ],
            "created_at": "2024-01-15 10:30:00",
            "updated_at": "2024-01-15 10:30:00"
        }
    }
}
```

**Response 404:**
```json
{
    "success": false,
    "message": "Proyecto no encontrado"
}
```

**Nota:** Si el proyecto existe pero no está publicado (`is_published = false`), también retornará 404.

---

### 3. Obtener Unidades de un Proyecto (GET)

Obtener lista paginada de unidades de un proyecto publicado con filtros opcionales.

**Endpoint:** `GET /projects/{id}/units`

**Parámetros de URL:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `id` | integer | ID del proyecto |

**Query Parameters (todos opcionales):**

| Parámetro | Tipo | Descripción | Valores |
|-----------|------|-------------|---------|
| `per_page` | integer | Elementos por página | 1-100 (default: 15) |
| `status` | string | Filtrar por estado | Ver [Estados de Unidad](#estados-de-unidad) |
| `unit_type` | string | Filtrar por tipo de unidad | Ver [Tipos de Unidad](#tipos-de-unidad) |
| `min_price` | decimal | Precio mínimo | Número decimal |
| `max_price` | decimal | Precio máximo | Número decimal |
| `min_area` | decimal | Área mínima (m²) | Número decimal |
| `max_area` | decimal | Área máxima (m²) | Número decimal |
| `bedrooms` | integer | Mínimo número de dormitorios | Número entero |
| `only_available` | boolean | Solo unidades disponibles | `true`, `false` (default: `false`) |

**Ejemplo de Request:**
```
GET /api/projects/1/units?status=disponible&min_price=100000&max_price=500000&only_available=true
```

**Response 200:**
```json
{
    "success": true,
    "message": "Unidades obtenidas exitosamente",
    "data": {
        "project": {
            "id": 1,
            "name": "Residencial Los Olivos"
        },
        "units": [
            {
                "id": 1,
                "project_id": 1,
                "unit_manzana": "Manzana A",
                "unit_number": "A-101",
                "unit_type": "departamento",
                "floor": 1,
                "tower": "Torre 1",
                "block": null,
                "area": 85.50,
                "bedrooms": 2,
                "bathrooms": 2,
                "parking_spaces": 1,
                "storage_rooms": 1,
                "balcony_area": 10.00,
                "terrace_area": 0.00,
                "garden_area": 0.00,
                "total_area": 95.50,
                "status": "disponible",
                "base_price": 1500.00,
                "total_price": 128250.00,
                "discount_percentage": 5.00,
                "discount_amount": 6412.50,
                "final_price": 121837.50,
                "price_per_square_meter": 1425.00,
                "commission_percentage": 3.00,
                "commission_amount": 3655.13,
                "blocked_until": null,
                "blocked_reason": null,
                "is_blocked": false,
                "is_available": true,
                "full_identifier": "Residencial Los Olivos - Torre 1 - Piso 1 - Unidad A-101",
                "notes": "Vista al mar",
                "created_at": "2024-01-15 10:30:00",
                "updated_at": "2024-01-15 10:30:00"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 50,
            "last_page": 4,
            "from": 1,
            "to": 15
        }
    }
}
```

**Campos de la Respuesta:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | integer | ID único de la unidad |
| `project_id` | integer | ID del proyecto |
| `unit_manzana` | string | Manzana de la unidad |
| `unit_number` | string | Número de la unidad |
| `unit_type` | string | Tipo de unidad |
| `floor` | integer | Piso |
| `tower` | string | Torre |
| `block` | string | Bloque |
| `area` | decimal | Área en m² |
| `bedrooms` | integer | Número de dormitorios |
| `bathrooms` | integer | Número de baños |
| `parking_spaces` | integer | Espacios de estacionamiento |
| `storage_rooms` | integer | Cocheras |
| `balcony_area` | decimal | Área de balcón (m²) |
| `terrace_area` | decimal | Área de terraza (m²) |
| `garden_area` | decimal | Área de jardín (m²) |
| `total_area` | decimal | Área total (m²) |
| `status` | string | Estado de la unidad |
| `base_price` | decimal | Precio base por m² |
| `total_price` | decimal | Precio total |
| `discount_percentage` | decimal | Porcentaje de descuento |
| `discount_amount` | decimal | Monto de descuento |
| `final_price` | decimal | Precio final |
| `price_per_square_meter` | decimal | Precio por m² |
| `commission_percentage` | decimal | Porcentaje de comisión |
| `commission_amount` | decimal | Monto de comisión |
| `blocked_until` | datetime | Fecha hasta cuando está bloqueado |
| `blocked_reason` | string | Razón del bloqueo |
| `is_blocked` | boolean | Si está bloqueada |
| `is_available` | boolean | Si está disponible |
| `full_identifier` | string | Identificador completo |
| `notes` | string | Notas adicionales |
| `created_at` | datetime | Fecha de creación |
| `updated_at` | datetime | Fecha de actualización |

**Response 404:**
```json
{
    "success": false,
    "message": "Proyecto no encontrado"
}
```

**Nota:** Si el proyecto existe pero no está publicado (`is_published = false`), también retornará 404.

---

## 📊 Modelos de Datos

### Tipos de Proyecto

| Valor | Descripción |
|-------|-------------|
| `lotes` | Lotes |

### Tipos de Lote

| Valor | Descripción |
|-------|-------------|
| `normal` | Normal |
| `express` | Express |

### Etapas

| Valor | Descripción |
|-------|-------------|
| `preventa` | Preventa |
| `lanzamiento` | Lanzamiento |
| `venta_activa` | Venta Activa |
| `cierre` | Cierre |

### Estados Legales

| Valor | Descripción |
|-------|-------------|
| `con_titulo` | Con Título |
| `en_tramite` | En Trámite |
| `habilitado` | Habilitado |

### Estados

| Valor | Descripción |
|-------|-------------|
| `activo` | Activo |
| `inactivo` | Inactivo |
| `suspendido` | Suspendido |
| `finalizado` | Finalizado |

### Tipos de Unidad

| Valor | Descripción |
|-------|-------------|
| `lote` | Lote |
| `casa` | Casa |
| `departamento` | Departamento |
| `oficina` | Oficina |
| `local` | Local |

### Estados de Unidad

| Valor | Descripción |
|-------|-------------|
| `disponible` | Disponible |
| `reservado` | Reservado |
| `vendido` | Vendido |
| `bloqueado` | Bloqueado |
| `en_construccion` | En Construcción |

---

## 🔍 Filtros y Búsqueda

### Búsqueda por Texto

El parámetro `search` busca en los siguientes campos:
- `name` (nombre del proyecto)
- `description` (descripción)
- `address` (dirección)
- `district` (distrito)
- `province` (provincia)

**Ejemplo:**
```
GET /api/projects?search=San Isidro
```

### Filtros de Ubicación

Puedes filtrar por ubicación usando uno o más de estos parámetros:
- `district` - Distrito
- `province` - Provincia
- `region` - Región

**Ejemplo:**
```
GET /api/projects?province=Lima&district=San Isidro
```

### Filtros Combinados

Puedes combinar múltiples filtros para obtener resultados más específicos:

**Ejemplo:**
```
GET /api/projects?stage=venta_activa&legal_status=con_titulo&has_available_units=true&province=Lima
```

Este ejemplo busca proyectos en:
- Etapa: Venta Activa
- Estado Legal: Con Título
- Con unidades disponibles
- Provincia: Lima

### Filtros de Unidades

Para filtrar unidades de un proyecto, puedes usar los siguientes parámetros:

**Por Estado:**
```
GET /api/projects/1/units?status=disponible
```

**Por Tipo:**
```
GET /api/projects/1/units?unit_type=departamento
```

**Por Rango de Precio:**
```
GET /api/projects/1/units?min_price=100000&max_price=500000
```

**Por Rango de Área:**
```
GET /api/projects/1/units?min_area=80&max_area=120
```

**Por Dormitorios:**
```
GET /api/projects/1/units?bedrooms=2
```

**Solo Disponibles:**
```
GET /api/projects/1/units?only_available=true
```

**Filtros Combinados:**
```
GET /api/projects/1/units?status=disponible&unit_type=departamento&min_price=100000&max_price=500000&bedrooms=2
```

Este ejemplo busca unidades que sean:
- Disponibles
- Tipo: Departamento
- Precio entre $100,000 y $500,000
- Con al menos 2 dormitorios

---

## ⚠️ Manejo de Errores

### Códigos de Estado HTTP

| Código | Significado | Descripción |
|--------|-------------|-------------|
| `200` | OK | Solicitud exitosa |
| `404` | Not Found | Proyecto no encontrado o no publicado |
| `429` | Too Many Requests | Rate limit excedido |
| `500` | Internal Server Error | Error del servidor |

### Estructura de Error

```json
{
    "success": false,
    "message": "Mensaje descriptivo del error"
}
```

### Ejemplos de Errores

**404 - Proyecto no encontrado:**
```json
{
    "success": false,
    "message": "Proyecto no encontrado"
}
```

**429 - Rate limit excedido:**
```json
{
    "success": false,
    "message": "Too Many Requests"
}
```

**500 - Error del servidor:**
```json
{
    "success": false,
    "message": "Error al obtener los proyectos"
}
```

---

## 🚦 Rate Limiting

La API implementa rate limiting para proteger el servidor:

| Endpoint | Límite |
|----------|--------|
| `/projects/*` | 120 solicitudes por minuto |

**Respuesta 429 (Too Many Requests):**
```json
{
    "success": false,
    "message": "Too Many Requests"
}
```

**Recomendaciones:**
- Cachea las respuestas cuando sea posible
- Implementa retry con backoff exponencial
- No hagas polling agresivo; usa WebSockets o notificaciones push si es necesario

---

## 💡 Ejemplos de Uso

### Ejemplo 1: Obtener todos los proyectos en Lima

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects?province=Lima" \
  -H "Accept: application/json"
```

### Ejemplo 2: Buscar proyectos en venta activa con unidades disponibles

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects?stage=venta_activa&has_available_units=true" \
  -H "Accept: application/json"
```

### Ejemplo 3: Obtener proyecto específico

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects/1" \
  -H "Accept: application/json"
```

### Ejemplo 4: Búsqueda con paginación

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects?search=residencial&per_page=10&page=2" \
  -H "Accept: application/json"
```

### Ejemplo 5: Filtros múltiples

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects?province=Lima&stage=venta_activa&legal_status=con_titulo&has_available_units=true&per_page=20" \
  -H "Accept: application/json"
```

### Ejemplo 6: Obtener unidades de un proyecto

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects/1/units" \
  -H "Accept: application/json"
```

### Ejemplo 7: Filtrar unidades disponibles por rango de precio

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects/1/units?status=disponible&min_price=100000&max_price=500000&only_available=true" \
  -H "Accept: application/json"
```

### Ejemplo 8: Filtrar unidades por área y dormitorios

```bash
curl -X GET "https://crm_inmobiliaria.test/api/projects/1/units?min_area=80&max_area=120&bedrooms=2" \
  -H "Accept: application/json"
```

---

## 📝 Notas Importantes

1. **Solo proyectos publicados**: La API solo retorna proyectos con `is_published = true`
2. **Rutas públicas**: No se requiere autenticación para acceder a estos endpoints
3. **Paginación**: El máximo de elementos por página es 100
4. **Fechas**: Todas las fechas están en formato `YYYY-MM-DD`
5. **Coordenadas**: Las coordenadas se extraen automáticamente del campo `ubicacion` (Google Maps URL)
6. **Rutas de archivos**: Las rutas de imágenes, videos y documentos son relativas al dominio base

---

## 🔗 Integración con Frontend

### JavaScript/TypeScript

```javascript
// Ejemplo con fetch
async function getProjects(filters = {}) {
  const params = new URLSearchParams(filters);
  const response = await fetch(`https://crm_inmobiliaria.test/api/projects?${params}`);
  const data = await response.json();
  
  if (data.success) {
    return data.data.projects;
  }
  throw new Error(data.message);
}

// Uso
const projects = await getProjects({
  province: 'Lima',
  stage: 'venta_activa',
  has_available_units: true
});
```

### React Hook Example

```javascript
import { useState, useEffect } from 'react';

function useProjects(filters = {}) {
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    async function fetchProjects() {
      try {
        setLoading(true);
        const params = new URLSearchParams(filters);
        const response = await fetch(`/api/projects?${params}`);
        const data = await response.json();
        
        if (data.success) {
          setProjects(data.data.projects);
        } else {
          setError(data.message);
        }
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }

    fetchProjects();
  }, [JSON.stringify(filters)]);

  return { projects, loading, error };
}
```

---

## 📚 Referencias

- [Laravel API Documentation](https://laravel.com/docs/api)
- [REST API Best Practices](https://restfulapi.net/)

---

**Versión de API:** 1.0.0  
**Última actualización:** 2024-01-15  
**Mantenido por:** Equipo de Desarrollo CRM Inmobiliaria

