# API Datero - Gestión de Clientes

## 📋 Descripción

Endpoints para gestionar clientes. Los dateros solo pueden crear, editar y ver los clientes que ellos mismos han creado.

## 👥 Endpoints

### 1. Listar Clientes

Obtiene una lista paginada de los clientes creados por el datero autenticado.

**Endpoint**: `GET /api/datero/clients`

**URL Completa**: `https://tu-dominio.com/api/datero/clients`

**Autenticación**: Requerida (Bearer Token)

#### Parámetros de Consulta

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `page` | integer | No | Número de página (default: 1) |
| `per_page` | integer | No | Elementos por página (default: 15, máximo: 100) |
| `search` | string | No | Búsqueda general (nombre, teléfono, DNI) |
| `dni` | string | No | Búsqueda específica por DNI del cliente |
| `status` | string | No | Filtrar por estado (nuevo, contacto_inicial, en_seguimiento, cierre, perdido) |
| `type` | string | No | Filtrar por tipo (inversor, comprador, empresa, constructor) |
| `source` | string | No | Filtrar por origen (redes_sociales, ferias, referidos, formulario_web, publicidad) |

#### Headers

```
Authorization: Bearer {token}
```

#### Ejemplo de Solicitud

```bash
# Listar todos los clientes
curl -X GET "https://tu-dominio.com/api/datero/clients" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."

# Buscar por DNI
curl -X GET "https://tu-dominio.com/api/datero/clients?dni=12345678" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."

# Buscar con filtros
curl -X GET "https://tu-dominio.com/api/datero/clients?search=Juan&status=nuevo&per_page=20" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

#### Respuesta Exitosa (200)

```json
{
  "success": true,
  "message": "Clientes obtenidos exitosamente",
  "data": {
    "clients": [
      {
        "id": 1,
        "name": "María González",
        "phone": "987654321",
        "document_type": "DNI",
        "document_number": "12345678",
        "address": "Av. Principal 123",
        "birth_date": "1990-05-15",
        "client_type": "comprador",
        "source": "redes_sociales",
        "status": "nuevo",
        "score": 75,
        "notes": "Cliente interesado en departamentos",
        "assigned_advisor": {
          "id": 5,
          "name": "Carlos García",
          "email": "carlos@example.com"
        },
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 45,
      "last_page": 3,
      "from": 1,
      "to": 15
    }
  }
}
```

---

### 2. Obtener Cliente Específico

Obtiene los detalles de un cliente específico creado por el datero.

**Endpoint**: `GET /api/datero/clients/{id}`

**URL Completa**: `https://tu-dominio.com/api/datero/clients/1`

**Autenticación**: Requerida (Bearer Token)

#### Parámetros de Ruta

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `id` | integer | Sí | ID del cliente |

#### Headers

```
Authorization: Bearer {token}
```

#### Ejemplo de Solicitud

```bash
curl -X GET "https://tu-dominio.com/api/datero/clients/1" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

#### Respuesta Exitosa (200)

```json
{
  "success": true,
  "message": "Cliente obtenido exitosamente",
  "data": {
    "client": {
      "id": 1,
      "name": "María González",
      "phone": "987654321",
      "document_type": "DNI",
      "document_number": "12345678",
      "address": "Av. Principal 123",
      "birth_date": "1990-05-15",
      "client_type": "comprador",
      "source": "redes_sociales",
      "status": "nuevo",
      "score": 75,
      "notes": "Cliente interesado en departamentos",
      "assigned_advisor": {
        "id": 5,
        "name": "Carlos García",
        "email": "carlos@example.com"
      },
      "opportunities_count": 2,
      "activities_count": 5,
      "tasks_count": 1,
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  }
}
```

#### Respuesta de Error (403)

```json
{
  "success": false,
  "message": "No tienes permiso para acceder a este cliente"
}
```

---

### 3. Crear Cliente

Crea un nuevo cliente. El cliente será automáticamente asignado al datero autenticado como creador.

**Endpoint**: `POST /api/datero/clients`

**URL Completa**: `https://tu-dominio.com/api/datero/clients`

**Autenticación**: Requerida (Bearer Token)

#### Parámetros

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `name` | string | Sí | Nombre completo del cliente |
| `phone` | string | Sí | Teléfono de contacto |
| `document_type` | string | Sí | Tipo de documento (DNI, RUC, CE, PASAPORTE) |
| `document_number` | string | Sí | Número de documento |
| `address` | string | No | Dirección del cliente |
| `birth_date` | date | No | Fecha de nacimiento (formato: YYYY-MM-DD) |
| `client_type` | string | Sí | Tipo de cliente (inversor, comprador, empresa, constructor) |
| `source` | string | Sí | Origen del cliente (redes_sociales, ferias, referidos, formulario_web, publicidad) |
| `status` | string | No | Estado del cliente (default: nuevo) |
| `score` | integer | No | Puntuación del cliente (0-100, default: 0) |
| `notes` | string | No | Notas adicionales |
| `assigned_advisor_id` | integer | No | ID del asesor asignado |

#### Headers

```
Authorization: Bearer {token}
```

#### Ejemplo de Solicitud

```bash
curl -X POST "https://tu-dominio.com/api/datero/clients" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "María González",
    "phone": "987654321",
    "document_type": "DNI",
    "document_number": "12345678",
    "address": "Av. Principal 123",
    "birth_date": "1990-05-15",
    "client_type": "comprador",
    "source": "redes_sociales",
    "status": "nuevo",
    "score": 75,
    "notes": "Cliente interesado en departamentos"
  }'
```

#### Respuesta Exitosa (201)

```json
{
  "success": true,
  "message": "Cliente creado exitosamente",
  "data": {
    "client": {
      "id": 1,
      "name": "María González",
      "phone": "987654321",
      "document_type": "DNI",
      "document_number": "12345678",
      "address": "Av. Principal 123",
      "birth_date": "1990-05-15",
      "client_type": "comprador",
      "source": "redes_sociales",
      "status": "nuevo",
      "score": 75,
      "notes": "Cliente interesado en departamentos",
      "assigned_advisor": null,
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  }
}
```

#### Respuesta de Error (422)

```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "name": ["El nombre es obligatorio."],
    "document_number": ["Este número de documento ya está registrado."]
  }
}
```

Si el error es por **teléfono o documento duplicado**, el cuerpo puede incluir el mismo patrón que la API Cazador: `message` con una sola frase (nombre del cliente ya registrado + fecha de registro en `d/m/Y H:i`) y, en creación, `errors.duplicate_owner` con `client_id`, `client_name`, `registered_at` (ISO8601), `field` (`phone` | `document_number`), más `owner_name` / `owner_user_id` y alias legacy `name` / `user_id` cuando exista usuario asociado.

---

### 4. Actualizar Cliente

Actualiza un cliente existente. Solo se pueden actualizar clientes creados por el datero autenticado.

**Endpoint**: `PUT /api/datero/clients/{id}` o `PATCH /api/datero/clients/{id}`

**URL Completa**: `https://tu-dominio.com/api/datero/clients/1`

**Autenticación**: Requerida (Bearer Token)

#### Parámetros de Ruta

| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `id` | integer | Sí | ID del cliente |

#### Parámetros (todos opcionales, solo enviar los que se desean actualizar)

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `name` | string | No | Nombre completo del cliente |
| `phone` | string | No | Teléfono de contacto |
| `document_type` | string | No | Tipo de documento |
| `document_number` | string | No | Número de documento |
| `address` | string | No | Dirección del cliente |
| `birth_date` | date | No | Fecha de nacimiento |
| `client_type` | string | No | Tipo de cliente |
| `source` | string | No | Origen del cliente |
| `status` | string | No | Estado del cliente |
| `score` | integer | No | Puntuación del cliente (0-100) |
| `notes` | string | No | Notas adicionales |
| `assigned_advisor_id` | integer | No | ID del asesor asignado |

#### Headers

```
Authorization: Bearer {token}
```

#### Ejemplo de Solicitud

```bash
curl -X PUT "https://tu-dominio.com/api/datero/clients/1" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "María González López",
    "phone": "987654322",
    "status": "en_seguimiento",
    "score": 85,
    "notes": "Cliente muy interesado, requiere seguimiento"
  }'
```

#### Respuesta Exitosa (200)

```json
{
  "success": true,
  "message": "Cliente actualizado exitosamente",
  "data": {
    "client": {
      "id": 1,
      "name": "María González López",
      "phone": "987654322",
      "document_type": "DNI",
      "document_number": "12345678",
      "address": "Av. Principal 123",
      "birth_date": "1990-05-15",
      "client_type": "comprador",
      "source": "redes_sociales",
      "status": "en_seguimiento",
      "score": 85,
      "notes": "Cliente muy interesado, requiere seguimiento",
      "assigned_advisor": null,
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 11:45:00"
    }
  }
}
```

#### Respuesta de Error (403)

```json
{
  "success": false,
  "message": "No tienes permiso para actualizar este cliente"
}
```

---

### 5. Obtener Opciones de Formulario

Obtiene las opciones disponibles para los campos de formulario (tipos, estados, orígenes, etc.).

**Endpoint**: `GET /api/datero/clients/options`

**URL Completa**: `https://tu-dominio.com/api/datero/clients/options`

**Autenticación**: Requerida (Bearer Token)

**Rate Limit**: 120 requests por minuto

#### Headers

```
Authorization: Bearer {token}
```

#### Ejemplo de Solicitud

```bash
curl -X GET "https://tu-dominio.com/api/datero/clients/options" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

#### Respuesta Exitosa (200)

```json
{
  "success": true,
  "message": "Opciones obtenidas exitosamente",
  "data": {
    "document_types": ["DNI", "RUC", "CE", "PASAPORTE"],
    "client_types": ["inversor", "comprador", "empresa", "constructor"],
    "sources": ["redes_sociales", "ferias", "referidos", "formulario_web", "publicidad"],
    "statuses": ["nuevo", "contacto_inicial", "en_seguimiento", "cierre", "perdido"]
  }
}
```

---

## 🔒 Permisos y Restricciones

- Los dateros **solo pueden ver y editar** los clientes que ellos mismos han creado
- El campo `created_by` se establece automáticamente al crear un cliente
- Si intentas acceder a un cliente que no creaste, recibirás un error 403

## 📝 Notas Importantes

1. **Búsqueda por DNI**: Usa el parámetro `dni` para búsqueda específica por número de documento
2. **Búsqueda general**: El parámetro `search` busca en nombre, teléfono y DNI
3. **Paginación**: Por defecto se muestran 15 clientes por página, máximo 100
4. **Validación de documento**: El número de documento debe ser único en el sistema
5. **Asignación automática**: Al crear un cliente, el datero autenticado se asigna como creador automáticamente

