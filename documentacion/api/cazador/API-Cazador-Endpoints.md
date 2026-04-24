# API Cazador – Documentación de endpoints

Documentación de referencia de la API para la aplicación Cazador (vendedores/asesores). Incluye descripción de rutas, parámetros, validaciones y ejemplos de request/response.

---

## Información general

| Concepto | Valor |
|----------|--------|
| **Base URL** | `{{base_url}}/api/cazador` (ejemplo prueba: `https://v1.lotesenremate.pe/api/cazador`; producción: `https://crm.lotesenremate.pe/api/cazador`). Las mismas rutas están duplicadas bajo **`/api/v1/cazador`** si usas el prefijo versionado. |
| **Autenticación** | JWT en header: `Authorization: Bearer <token>` |
| **Content-Type** | `application/json` para bodies JSON |
| **Middleware** | Rutas protegidas: `auth:api` + `cazador` (solo Admin, Líder, Cazador/vendedor) |

**Formato de respuesta estándar:**

- **Éxito:** `{ "success": true, "message": "...", "data": { ... } }`
- **Error de validación (422):** `{ "success": false, "message": "...", "errors": { "campo": ["mensaje"] } }`
- **Error genérico (4xx/5xx):** `{ "success": false, "message": "..." }` (opcionalmente `errors`)

**Paginación:** Las listas devuelven en `data` un objeto `pagination` con: `current_page`, `per_page`, `total`, `last_page`, `from`, `to`, `links` (`first`, `last`, `prev`, `next`).

**Health check (fuera de Cazador):** `GET /api/health` — público; retorna estado del sistema.

---

## 1. Autenticación (Auth)

Todas las rutas bajo el prefijo `/auth`. Login es público con rate limit 5 req/min; el resto requiere JWT.

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| POST | `/auth/login` | Inicio de sesión (email + PIN 6 dígitos) | Público |
| GET | `/auth/me` | Usuario autenticado | JWT |
| POST | `/auth/logout` | Invalida el token actual | JWT |
| POST | `/auth/refresh` | Renueva el token | JWT |
| POST | `/auth/change-password` | Cambia PIN/contraseña | JWT |

### POST `/auth/login`

Inicia sesión con email y PIN de 6 dígitos numéricos. Solo pueden acceder usuarios con rol Administrador, Líder o Cazador (vendedor); los dateros reciben 403.

**Request (body JSON):**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| email | string | Sí | Correo electrónico (formato válido) |
| password | string | Sí | PIN de 6 dígitos numéricos |

**Ejemplo request:**

```json
{
  "email": "vendedor@ejemplo.com",
  "password": "123456"
}
```

**Respuesta 200 (éxito):**

```json
{
  "success": true,
  "message": "Inicio de sesión exitoso",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 43200,
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "vendedor@ejemplo.com",
      "phone": "987654321",
      "role": "vendedor",
      "is_active": true
    }
  }
}
```

**Posibles errores:** 401 (credenciales inválidas), 403 (rol no permitido o cuenta inactiva), 422 (validación).

---

### GET `/auth/me`

Devuelve los datos del usuario autenticado según el token JWT.

**Headers:** `Authorization: Bearer <token>`

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "vendedor@ejemplo.com",
    "phone": "987654321",
    "role": "vendedor",
    "is_active": true
  }
}
```

---

### POST `/auth/logout`

Invalida el token actual. No requiere body.

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

---

### POST `/auth/refresh`

Devuelve un nuevo token renovado. Header: `Authorization: Bearer <token_actual>`.

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 43200
  }
}
```

---

### POST `/auth/change-password`

Cambia el PIN/contraseña del usuario autenticado.

**Request (body JSON):**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| current_password | string | Sí | PIN actual (6 dígitos) |
| new_password | string | Sí | Nuevo PIN (6 dígitos) |
| new_password_confirmation | string | Sí | Confirmación del nuevo PIN |

**Ejemplo:**

```json
{
  "current_password": "123456",
  "new_password": "654321",
  "new_password_confirmation": "654321"
}
```

**Respuesta 200:** `{ "success": true, "message": "..." }`  
**Errores:** 422 (validación), 403 (PIN actual incorrecto).

---

## 2. Clientes (Clients)

Prefijo: `/clients`. Todas las rutas requieren JWT. El cazador solo ve y gestiona clientes cuyo `assigned_advisor_id` sea su propio `id`. Throttle: 60 req/min (options y suggestions pueden tener cache y límites distintos).

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/clients` | Lista clientes (paginada, filtros, include) |
| GET | `/clients/options` | Opciones para formularios (tipos, estados, fuentes, documento) |
| GET | `/clients/suggestions` | Sugerencias por texto (autocompletado) |
| GET | `/clients/batch` | Múltiples clientes por lista de IDs |
| GET | `/clients/export` | Exportar clientes a CSV |
| POST | `/clients/validate` | Valida datos de cliente sin crear (misma validación que POST /clients) |
| POST | `/clients` | Crear un cliente |
| POST | `/clients/batch` | Crear o actualizar clientes en lote |
| GET | `/clients/{id}` | Detalle de un cliente |
| PUT / PATCH | `/clients/{id}` | Actualizar cliente (solo reasignación de asesor) |
| GET | `/clients/{client}/activities` | Lista actividades del cliente |
| POST | `/clients/{client}/activities` | Crear actividad |
| PUT / PATCH | `/clients/{client}/activities/{activity}` | Actualizar actividad |

---

### GET `/clients`

Lista paginada de clientes asignados al cazador autenticado.

**Query params:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| per_page | int | Resultados por página (default 15, max 100) |
| page | int | Página (default 1) |
| search | string | Búsqueda en nombre, teléfono, documento |
| status | string | Filtrar por estado del cliente |
| type | string | Filtrar por tipo de cliente |
| source | string | Filtrar por fuente |
| create_type | string | Filtrar por tipo de creación (propio, datero) |
| include | string | Relaciones extra: `assignedAdvisor`, `createdBy`, `activities`, `reservations`, `tasks`, `opportunities` (separadas por coma) |

**Ejemplo request:**

```
GET /api/cazador/clients?per_page=10&search=maria&status=nuevo
Authorization: Bearer <token>
```

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Clientes obtenidos exitosamente",
  "data": {
    "clients": [
      {
        "id": 1,
        "name": "María García López",
        "phone": "987654321",
        "document_type": "DNI",
        "document_number": "12345678",
        "address": "Av. Ejemplo 123",
        "city_id": 1,
        "birth_date": "1990-05-15",
        "client_type": "comprador",
        "source": "referidos",
        "status": "nuevo",
        "create_type": "propio",
        "create_mode": "dni",
        "score": 0,
        "notes": null,
        "created_at": "2026-02-20 10:00:00",
        "updated_at": "2026-02-20 10:00:00",
        "city": { "id": 1, "name": "Lima" },
        "assigned_advisor": { "id": 2, "name": "Juan Pérez", "email": "vendedor@ejemplo.com" }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 25,
      "last_page": 3,
      "from": 1,
      "to": 10,
      "links": {
        "first": "https://api.ejemplo.com/api/cazador/clients?page=1",
        "last": "https://api.ejemplo.com/api/cazador/clients?page=3",
        "prev": null,
        "next": "https://api.ejemplo.com/api/cazador/clients?page=2"
      }
    }
  }
}
```

---

### GET `/clients/options`

Devuelve opciones para combos/selects de formularios de cliente. Cache recomendado: 300 s.

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Opciones obtenidas exitosamente",
  "data": {
    "document_types": { "DNI": "DNI" },
    "client_types": {
      "inversor": "Inversor",
      "comprador": "Comprador",
      "empresa": "Empresa",
      "constructor": "Constructor"
    },
    "sources": {
      "redes_sociales": "Redes Sociales",
      "ferias": "Ferias",
      "referidos": "Referidos",
      "formulario_web": "Formulario Web",
      "publicidad": "Publicidad"
    },
    "statuses": {
      "nuevo": "Nuevo",
      "contacto_inicial": "Contacto Inicial",
      "en_seguimiento": "En Seguimiento",
      "cierre": "Cierre",
      "perdido": "Perdido"
    }
  }
}
```

---

### GET `/clients/suggestions`

Sugerencias para autocompletado por nombre, teléfono o documento.

**Query params:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| q | string | Texto de búsqueda (mín. 2 caracteres) |
| limit | int | Máximo de sugerencias (default 10, max 20) |

**Ejemplo:** `GET /api/cazador/clients/suggestions?q=mar&limit=5`

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Sugerencias obtenidas",
  "data": {
    "suggestions": [
      { "id": 1, "name": "María García", "phone": "987654321", "document_number": "12345678" }
    ]
  }
}
```

Si `q` tiene menos de 2 caracteres, `suggestions` viene vacío.

---

### POST `/clients/validate`

Valida los datos del formulario de cliente **sin crear** el cliente. Pensado para que la app móvil valide antes de enviar el formulario definitivo. Mismo body que POST `/clients`; mismas reglas (teléfono obligatorio y único, documento opcional y único si se envía, tipo solo DNI, etc.).

**Request (body JSON):** Los mismos campos que POST `/clients` (name, phone, document_type, document_number, address, city_id, birth_date, client_type, source, status, score, notes, create_mode, etc.).

**Respuesta 200 (válido):**

```json
{
  "success": true,
  "message": "Validación de cliente exitosa",
  "data": { "valid": true }
}
```

**Respuesta 200 (inválido):** Se devuelve 200 con `valid: false` y los errores (y opcionalmente quién tiene el teléfono/documento si ya existe).

```json
{
  "success": true,
  "message": "Telefono registrado por \"Ana López\"",
  "data": {
    "valid": false,
    "errors": { "phone": ["El teléfono ya está en uso."] },
    "duplicate_owner": { "name": "Ana López", "user_id": 3, "client_id": 10, "field": "phone" }
  }
}
```

---

### POST `/clients`

Crea un cliente asignado al cazador autenticado (`assigned_advisor_id` y `created_by` = usuario actual). El tipo de documento admitido es solo **DNI**; `document_type` y `document_number` son **opcionales**. El **teléfono es obligatorio** y debe ser único (9 dígitos, empezando por 9). Si se envía `document_number`, debe ser único (se excluyen valor null y `00000000`).

**Request (body JSON):**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| name | string | Sí | Nombre completo (max 255) |
| phone | string | Sí | Teléfono 9 dígitos, empieza en 9, único |
| document_type | string | No | Solo aceptado: `DNI` (opcional) |
| document_number | string | No | Número de documento (max 20). Si se envía, único (null y 00000000 excluidos) |
| address | string | No | Dirección (max 500) |
| city_id | int | Sí | ID de ciudad (exists en `cities`) |
| birth_date | string | Sí | Fecha nacimiento (Y-m-d) |
| client_type | string | Sí | `inversor`, `comprador`, `empresa`, `constructor` |
| source | string | Sí | `redes_sociales`, `ferias`, `referidos`, `formulario_web`, `publicidad` |
| status | string | Sí | `nuevo`, `contacto_inicial`, `en_seguimiento`, `cierre`, `perdido` |
| score | int | Sí | 0–100 |
| notes | string | No | Notas |
| create_mode | string | No | `dni` o `phone`. Si no se envía: `phone` si no hay document_number, sino `dni` |
| create_type | string | No | No necesario; el servicio asigna según rol |

**Ejemplo request (con DNI opcional):**

```json
{
  "name": "Rosa Martínez",
  "phone": "987654321",
  "document_type": "DNI",
  "document_number": "87654321",
  "address": "Calle Los Pinos 456",
  "city_id": 1,
  "birth_date": "1985-03-20",
  "client_type": "comprador",
  "source": "referidos",
  "status": "nuevo",
  "score": 0,
  "notes": "Cliente referido por María"
}
```

**Ejemplo request (sin documento):**

```json
{
  "name": "Luis Fernández",
  "phone": "912345678",
  "address": null,
  "city_id": 2,
  "birth_date": "1992-08-10",
  "client_type": "comprador",
  "source": "redes_sociales",
  "status": "nuevo",
  "score": 0,
  "notes": null
}
```

**Respuesta 201 (éxito):**

```json
{
  "success": true,
  "message": "Cliente creado exitosamente",
  "data": {
    "client": {
      "id": 42,
      "name": "Rosa Martínez",
      "phone": "987654321",
      "document_type": "DNI",
      "document_number": "87654321",
      "address": "Calle Los Pinos 456",
      "city_id": 1,
      "birth_date": "1985-03-20",
      "client_type": "comprador",
      "source": "referidos",
      "status": "nuevo",
      "create_type": "propio",
      "create_mode": "dni",
      "score": 0,
      "notes": "Cliente referido por María",
      "created_at": "2026-02-20 14:30:00",
      "updated_at": "2026-02-20 14:30:00",
      "city": { "id": 1, "name": "Lima" },
      "assigned_advisor": { "id": 2, "name": "Juan Pérez", "email": "vendedor@ejemplo.com" }
    }
  }
}
```

**Respuesta 422 (validación – duplicado):** Si el teléfono o el documento ya existen, se incluye información del titular:

```json
{
  "success": false,
  "message": "Telefono registrado por \"Ana López\"",
  "errors": {
    "phone": ["El teléfono ya está en uso."]
  },
  "duplicate_owner": {
    "name": "Ana López",
    "user_id": 3,
    "client_id": 10,
    "field": "phone"
  }
}
```

---

### POST `/clients/batch`

Crea y/o actualiza varios clientes en una sola petición. Cada ítem del array puede ser creación (sin `id`) o actualización (con `id`). En actualización solo se permite cambiar `assigned_advisor_id`.

**Request (body JSON):**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| clients | array | Lista de objetos cliente (ver campos de POST /clients para creación; para actualización: `id` y opcionalmente `assigned_advisor_id`) |

**Ejemplo request:**

```json
{
  "clients": [
    {
      "name": "Cliente Nuevo 1",
      "phone": "998877665",
      "city_id": 1,
      "birth_date": "1990-01-01",
      "client_type": "comprador",
      "source": "referidos",
      "status": "nuevo",
      "score": 0
    },
    {
      "id": 5,
      "assigned_advisor_id": 2
    }
  ]
}
```

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Batch de clientes procesado",
  "data": {
    "created": [
      { "id": 43, "name": "Cliente Nuevo 1", "phone": "998877665", ... }
    ],
    "updated": [
      { "id": 5, "name": "...", "assigned_advisor_id": 2, ... }
    ],
    "errors": [
      { "index": 2, "errors": { "phone": ["El teléfono ya está en uso."] }, "duplicate_owner": { ... }, "message": "..." }
    ]
  }
}
```

`errors` contiene por cada ítem fallido: `index`, `errors` y opcionalmente `duplicate_owner` y `message`.

---

### GET `/clients/batch`

Obtiene varios clientes por una lista de IDs. Solo se devuelven clientes asignados al cazador.

**Query params:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| ids | string | IDs separados por coma (ej. `1,2,3`) |

**Ejemplo:** `GET /api/cazador/clients/batch?ids=1,2,5`

**Respuesta 200:** `data.clients` es un array de objetos cliente con el mismo formato que en GET `/clients`.

---

### GET `/clients/{id}`

Detalle de un cliente. Solo se permite si el cliente está asignado al cazador.

**Query params:** `include` (opcional), mismos valores que en GET `/clients`.

**Respuesta 200:** `data.client` incluye los mismos campos que en la lista, más (si aplica) `opportunities_count`, `activities_count`, `tasks_count`.

**Errores:** 404 (cliente no encontrado), 403 (no asignado al cazador).

---

### PUT / PATCH `/clients/{id}`

Actualiza un cliente. En la API Cazador **solo se puede cambiar el asesor asignado** (`assigned_advisor_id`). El cliente debe estar asignado al cazador autenticado.

**Request (body JSON):**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| assigned_advisor_id | int | ID del usuario (asesor) al que se reasigna el cliente (opcional) |

**Ejemplo:**

```json
{
  "assigned_advisor_id": 3
}
```

**Respuesta 200:** `data.client` con el cliente actualizado (incluyendo `assigned_advisor`).

**Errores:** 404, 403, 422 (si `assigned_advisor_id` no existe).

---

### GET `/clients/export`

Exporta los clientes del cazador a **CSV** (descarga de archivo). Mismos filtros que en listado, según implementación actual: `search`, `status`, `type` (tipo de cliente), `source`. No incluye `create_type` en el export del controlador.

**Respuesta:** `Content-Type: text/csv` con columnas: `id`, `name`, `phone`, `status`, `client_type`, `source`, `document_number`.

---

### Actividades del cliente

Rutas bajo `/clients/{client}/...` donde `{client}` es el **ID numérico** del cliente. El cliente debe tener `assigned_advisor_id` igual al usuario autenticado.

#### GET `/clients/{client}/activities`

Lista paginada.

| Query param | Descripción |
|-------------|---------------|
| per_page | Por página (default 15, max 100) |
| page | Número de página |
| status | Filtrar por estado de la actividad |
| activity_type | Filtrar por tipo |
| priority | Filtrar por prioridad |
| start_date_from, start_date_to | Rango de fechas (fecha de inicio) |
| search | Texto en título, descripción o notas |

#### POST `/clients/{client}/activities`

Crea una actividad. El controlador asigna `client_id` automáticamente. Por defecto el servicio aplica `status`: `programada`, `priority`: `media`, `created_by` / `updated_by`: usuario actual.

**Body JSON (campos validados):**

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| title | Sí | Título (max 255) |
| activity_type | Sí | `llamada`, `reunion`, `visita`, `seguimiento`, `tarea` |
| status | Sí* | `programada`, `en_progreso`, `completada`, `cancelada` (*si se omite, default `programada`) |
| priority | Sí* | `baja`, `media`, `alta`, `urgente` (*si se omite, default `media`) |
| start_date | Sí | Fecha/hora de inicio (formato date/datetime aceptado por Laravel) |
| project_id | No | ID proyecto |
| unit_id | No | ID unidad |
| opportunity_id | No | ID oportunidad |
| advisor_id | No | ID usuario asesor |
| assigned_to | No | ID usuario asignado |
| notes | No | Notas |
| description | No | Descripción |
| duration | No | Entero minutos (min 1) |
| location | No | Ubicación (max 255) |
| reminder_before | No | Recordatorio N minutos antes (min 1) |

**Ejemplo mínimo:**

```json
{
  "title": "Llamada de seguimiento",
  "activity_type": "llamada",
  "start_date": "2026-04-25 10:00:00"
}
```

#### PUT / PATCH `/clients/{client}/activities/{activity}`

Actualiza campos parciales:

| Campo | Reglas |
|-------|--------|
| status | `programada`, `en_progreso`, `completada`, `cancelada` |
| result | string opcional |
| notes | string opcional |
| start_date | date opcional |
| assigned_to | ID usuario existente o null |

**Ejemplo:**

```json
{
  "status": "completada",
  "result": "Cliente confirmó interés en visita",
  "notes": "Reagendar para la próxima semana"
}
```

---

## 3. Ciudades (Cities)

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| GET | `/cities` | Lista ciudades (paginada, búsqueda) | JWT |

### GET `/cities`

Lista de ciudades para formularios y filtros.

**Query params:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| search | string | Filtro por nombre (LIKE) |
| per_page | int | Por página (default 100, max 500) |

**Ejemplo:** `GET /api/cazador/cities?search=lim&per_page=50`

**Respuesta 200:**

```json
{
  "success": true,
  "message": "Ciudades obtenidas exitosamente",
  "data": {
    "cities": [
      { "id": 1, "name": "Lima" },
      { "id": 2, "name": "Lima Metropolitana" }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 50,
      "total": 2,
      "last_page": 1,
      "from": 1,
      "to": 2,
      "links": { "first": "...", "last": "...", "prev": null, "next": null }
    }
  }
}
```

Cache recomendado: 300 s.

---

## 4. Proyectos (Projects)

Prefijo: `/projects`. Acceso a lista completa de proyectos (no solo publicados).

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/projects` | Lista proyectos (filtros, include) |
| GET | `/projects/suggestions` | Sugerencias por texto (`q`, `limit`) |
| GET | `/projects/{id}` | Detalle de proyecto |
| GET | `/projects/{id}/units` | Unidades disponibles del proyecto (paginado) |

**GET `/projects`** — Query: `per_page` (default 15, max 100), `search`, `project_type`, `lote_type`, `stage`, `legal_status`, `status`, `district`, `province`, `region`, `has_available_units` (boolean), `include` (`advisors`, `reservations`, separados por coma).

**GET `/projects/suggestions`** — `q` (mín. 2 caracteres), `limit` (default 10, max 20).

**GET `/projects/{id}`** — Query: `include_units` (default true), `units_per_page` (default 15, max 100; solo si `include_units` es true), `include` (`advisors`, `reservations`). La respuesta incluye `project` con unidades paginadas en `units` y `units_pagination` cuando aplica.

**GET `/projects/{id}/units`** — Solo unidades con estado disponible. Query: `per_page` (default 15, max 100), `page`.

---

## 5. Dateros

Prefijo: `/dateros`. Solo usuarios con acceso a API Cazador (Admin, Líder, Cazador).

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/dateros` | Lista dateros (per_page, search, is_active) |
| POST | `/dateros` | Registra datero (lider_id = usuario autenticado) |
| GET | `/dateros/{id}` | Detalle de un datero |
| PUT / PATCH | `/dateros/{id}` | Actualiza datero |

**POST `/dateros`** — Registra un usuario con rol Datero. Body JSON:

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| name | Sí | Nombre completo |
| email | Sí | Email único en `users` |
| phone | Sí | Teléfono (max 20) |
| dni | Sí | 8 dígitos numéricos, único |
| pin | Sí | PIN 6 dígitos numéricos (se guarda como PIN y contraseña) |
| ocupacion, banco, cuenta_bancaria, cci_bancaria | No | Opcionales |

El datero queda con `lider_id` = ID del usuario autenticado.

**Ejemplo:**

```json
{
  "name": "Pedro Datero",
  "email": "datero.ejemplo@mail.com",
  "phone": "999888777",
  "dni": "40123456",
  "pin": "123456",
  "ocupacion": "Independiente",
  "banco": "BCP",
  "cuenta_bancaria": "191-1234567890-1",
  "cci_bancaria": "00219111234567890123"
}
```

**PUT / PATCH `/dateros/{id}`** — Actualización parcial. Campos: `name`, `email`, `phone`, `dni`, `pin` o `password` (6 dígitos; ambos actualizan PIN+password), `ocupacion`, `banco`, `cuenta_bancaria`, `cci_bancaria`, `is_active` (boolean). Solo dateros cuyo `lider_id` coincide con el usuario autenticado.

---

## 6. Dashboard

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| GET | `/dashboard/stats` | Estadísticas (clientes, dateros, proyectos, reservas) | JWT |

**GET `/dashboard/stats`** — Sin query params en la implementación actual. Respuesta en `data`: contadores de `clients` (total, by_status, by_type), `dateros` (total, active, inactive), `projects` (total, with_available_units), `reservations` (total, by_status, by_payment_status). Para vendedores solo se consideran sus clientes/reservas y dateros bajo su `lider_id`; admin/líder ven ámbito global. Respuesta cacheada ~300 s por usuario.

---

## 7. Reservas (Reservations)

Prefijo: `/reservations`. Si el usuario no es admin/líder, solo ve sus propias reservas (`advisor_id` = usuario actual).

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/reservations` | Lista reservas (filtros) |
| GET | `/reservations/export` | Exporta reservas a CSV |
| POST | `/reservations` | Crea reserva (estado activa) |
| POST | `/reservations/batch` | Crea reservas en lote |
| GET | `/reservations/{id}` | Detalle |
| PUT / PATCH | `/reservations/{id}` | Actualiza reserva **solo si status = activa** |
| POST | `/reservations/{id}/confirm` | Confirma (sube comprobante, multipart) |
| POST | `/reservations/{id}/cancel` | Cancela (body: cancel_note) |
| POST | `/reservations/{id}/convert-to-sale` | Convierte a venta (sin body) |

**GET `/reservations`** — Query validados: `per_page` (1–100), `search` (min 2 caracteres si se envía), `include` (max 200; ej. `client,project,unit,advisor`), `status` (`activa`, `confirmada`, `cancelada`, `vencida`, `convertida_venta`), `payment_status` (`pendiente`, `pagado`, `parcial`), `project_id`, `client_id`, `advisor_id` (solo admin/líder suelen filtrar por otro asesor).

**GET `/reservations/export`** — Filtros query: `search`, `status`, `payment_status`, `project_id`, `client_id`. CSV con columnas de reserva (id, número, cliente, proyecto, unidad, estados, fechas, monto, etc. según implementación).

**POST `/reservations`** — Body JSON:

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| client_id | Sí | Debe existir en `clients` |
| project_id | Sí | Debe existir |
| unit_id | Sí | Unidad **disponible** y perteneciente al `project_id` |
| reservation_amount | Sí | numérico ≥ 0 |
| payment_method | No | string max 255 |
| payment_reference | No | string max 255 |
| notes | No | string |
| terms_conditions | No | string |

`advisor_id` se asigna al usuario autenticado.

**Ejemplo:**

```json
{
  "client_id": 1,
  "project_id": 2,
  "unit_id": 15,
  "reservation_amount": 5000,
  "payment_method": "Transferencia",
  "payment_reference": "OP-2026-001",
  "notes": "Cliente prioriza entrega 2027",
  "terms_conditions": "Acepta condiciones estándar"
}
```

**POST `/reservations/batch`** — Body: `{ "reservations": [ { ...mismos campos que POST /reservations... }, ... ] }`. Respuesta: `created` (array) y `errors` (array con `index` y `errors`).

**PUT / PATCH `/reservations/{id}`** — Solo reserva en estado `activa`. Campos opcionales/sometimes según validación: `client_id`, `advisor_id`, `reservation_type` (`pre_reserva`, `reserva_firmada`, `reserva_confirmada`), `reservation_date`, `expiration_date` (debe ser posterior a `reservation_date` si ambos se envían), `reservation_amount`, `payment_method`, `payment_status`, `payment_reference`, `notes`, `terms_conditions`.

**POST `/reservations/{id}/confirm`** — `multipart/form-data`: campo archivo **`image`** obligatorio (jpeg, png, jpg, gif, webp; max 10 MB). Opcionales como campos de formulario: `reservation_date`, `expiration_date`, `reservation_amount`, `payment_method`, `payment_status` (`pendiente`, `pagado`, `parcial`), `payment_reference`.

**POST `/reservations/{id}/cancel`** — Body JSON: `cancel_note` (string, **min 10**, max 500).

**POST `/reservations/{id}/convert-to-sale`** — Sin body. Solo si la reserva puede convertirse (p. ej. estado confirmada) y la unidad puede venderse.

---

## 8. Documentos

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| POST | `/documents/search` | Consulta DNI/RUC en servicio externo (Facturalahoy) | JWT |

Prefijo: `/documents`. Throttle: 30 req/min.

**Body JSON:**

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| document_type | Sí | `dni` o `ruc` (minúsculas en validación) |
| document_number | Sí | Solo dígitos; **8** dígitos para DNI, **11** para RUC |

**Ejemplo DNI:**

```json
{
  "document_type": "dni",
  "document_number": "40123456"
}
```

**Ejemplo RUC:**

```json
{
  "document_type": "ruc",
  "document_number": "20123456789"
}
```

Si el documento ya existe como cliente en el CRM, la API puede responder **409** con datos del asesor asignado (`client_registered`, `assigned_advisor`, etc.). Si la consulta externa falla, se devuelve error con el mensaje del proveedor.

---

## 9. Sincronización (Sync)

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| GET | `/sync` | Sincroniza cambios desde una fecha | JWT |

**Query:** `since` — **obligatorio**. Fecha/hora parseable por Carbon (ej. `2026-01-01T00:00:00Z`, `2026-04-20 08:00:00`).

**Ejemplo:** `GET /api/cazador/sync?since=2026-04-01T00:00:00-05:00`

**Respuesta 200:** `data` incluye registros con `updated_at` posterior a `since`: `clients` (solo asignados al cazador si no es admin/líder), `reservations` (misma regla por `advisor_id`), `projects` (todos los actualizados) y `sync_timestamp` (ISO8601 del servidor).

**Errores:** 422 si falta `since`. Throttle: 30 req/min.

---

## 10. Reportes

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| GET | `/reports/sales` | Reporte de ventas en CSV | JWT |

**Query:** `date_from`, `date_to` (opcionales, formato fecha aceptado por la aplicación). Respuesta: descarga CSV. Throttle: 30 req/min.

---

## Colección Postman

Se incluye el archivo importable **`Cazador-API.postman_collection.json`** (misma carpeta que este documento) con variables `base_url` y `token`, carpetas por módulo y ejemplos de body (JSON o form-data donde aplica). Tras **Login**, puedes copiar el `token` de la respuesta a la variable de colección o usar el script de prueba del request si está habilitado.

---

## Índice de rutas (resumen)

Todas relativas a la base URL de la API Cazador (ej. `/api/cazador`).

```
POST   /auth/login
GET    /auth/me
POST   /auth/logout
POST   /auth/refresh
POST   /auth/change-password

GET    /clients
GET    /clients/options
GET    /clients/suggestions
GET    /clients/batch
GET    /clients/export
POST   /clients/validate
POST   /clients
POST   /clients/batch
GET    /clients/{id}
PUT    /clients/{id}
PATCH  /clients/{id}
GET    /clients/{client}/activities
POST   /clients/{client}/activities
PUT    /clients/{client}/activities/{activity}
PATCH  /clients/{client}/activities/{activity}

GET    /cities

GET    /projects
GET    /projects/suggestions
GET    /projects/{id}
GET    /projects/{id}/units

GET    /dateros
POST   /dateros
GET    /dateros/{id}
PUT    /dateros/{id}
PATCH  /dateros/{id}

GET    /dashboard/stats

GET    /reservations
GET    /reservations/export
POST   /reservations
POST   /reservations/batch
GET    /reservations/{id}
PUT    /reservations/{id}
PATCH  /reservations/{id}
POST   /reservations/{id}/confirm
POST   /reservations/{id}/cancel
POST   /reservations/{id}/convert-to-sale

POST   /documents/search

GET    /sync

GET    /reports/sales
```

Health: `GET /api/health` (sin prefijo cazador).
