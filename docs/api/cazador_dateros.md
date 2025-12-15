## API Cazador - Gestión de Dateros

**Base URL** (por defecto Laravel):  
`https://tu-dominio.com/api/cazador`

Todas las rutas de este módulo:
- Están protegidas con **JWT** (`Authorization: Bearer <token>`).
- Requieren pasar el middleware `cazador` (roles: admin, líder o vendedor).
- Operan siempre sobre dateros cuyo `lider_id` es el **usuario autenticado**.

---

## Autenticación previa (Cazador)

Antes de consumir estas rutas, el cazador debe iniciar sesión:

- **POST** `/api/cazador/auth/login`
- Body:
  - `email` (string)
  - `password` (string)
- Respuesta: devuelve `token` (JWT) que usarás en `Authorization: Bearer <token>`.

---

## 1. Listar dateros del cazador

- **GET** `/api/cazador/dateros`
- **Headers**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

### Parámetros query (opcionales)

- `per_page` (int, 1–100, por defecto 15): Cantidad de registros por página.
- `search` (string): Filtra por `name`, `email`, `phone`, `dni`.
- `is_active` (bool): `true` o `false` para filtrar por estado.

### Respuesta 200 (ejemplo)

```json
{
  "success": true,
  "message": "Dateros obtenidos exitosamente",
  "data": {
    "dateros": [
      {
        "id": 10,
        "name": "Juan Pérez",
        "email": "juan@example.com",
        "phone": "999999999",
        "dni": "12345678",
        "role": "datero",
        "is_active": true,
        "banco": "BCP",
        "cuenta_bancaria": "123-4567890-0-12",
        "cci_bancaria": "00212345678901234567",
        "lider": {
          "id": 3,
          "name": "Líder Cazador",
          "email": "lider@example.com"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 1,
      "last_page": 1,
      "from": 1,
      "to": 1
    }
  }
}
```

### Ejemplo Flutter (http)

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

Future<void> fetchDateros(String token) async {
  final uri = Uri.parse('https://tu-dominio.com/api/cazador/dateros?per_page=20');

  final response = await http.get(
    uri,
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    // data['data']['dateros'] -> lista de dateros
  } else {
    // manejar error
  }
}
```

---

## 2. Registrar un nuevo datero

- **POST** `/api/cazador/dateros`
- **Headers**:
  - `Authorization: Bearer <token>`
  - `Content-Type: application/json`
  - `Accept: application/json`

### Body JSON

```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "phone": "999999999",
  "dni": "12345678",
  "pin": "123456",
  "banco": "BCP",
  "cuenta_bancaria": "123-4567890-0-12",
  "cci_bancaria": "00212345678901234567"
}
```

### Validaciones clave

- `dni`: exactamente **8 dígitos numéricos**, único en `users`.
- `pin`: exactamente **6 dígitos numéricos**.
- `email`: formato válido y único en `users`.

### Respuesta 201 (ejemplo)

```json
{
  "success": true,
  "message": "Datero registrado exitosamente.",
  "data": {
    "user": {
      "id": 10,
      "name": "Juan Pérez",
      "email": "juan@example.com",
      "phone": "999999999",
      "dni": "12345678",
      "role": "datero",
      "is_active": true,
      "lider": {
        "id": 3,
        "name": "Líder Cazador",
        "email": "lider@example.com"
      }
    }
  }
}
```

### Ejemplo Flutter

```dart
Future<void> createDatero(String token) async {
  final uri = Uri.parse('https://tu-dominio.com/api/cazador/dateros');

  final body = {
    'name': 'Juan Pérez',
    'email': 'juan@example.com',
    'phone': '999999999',
    'dni': '12345678',
    'pin': '123456',
    'banco': 'BCP',
    'cuenta_bancaria': '123-4567890-0-12',
    'cci_bancaria': '00212345678901234567',
  };

  final response = await http.post(
    uri,
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode(body),
  );

  if (response.statusCode == 201) {
    final data = jsonDecode(response.body);
    // data['data']['user'] -> datero creado
  } else if (response.statusCode == 422) {
    // errores de validación
    final errors = jsonDecode(response.body);
  } else {
    // otros errores
  }
}
```

---

## 3. Ver detalle de un datero

- **GET** `/api/cazador/dateros/{id}`
- **Headers**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

El datero debe:
- Existir.
- Tener `role = 'datero'`.
- Tener `lider_id` igual al ID del usuario autenticado.

### Respuesta 200 (ejemplo)

```json
{
  "success": true,
  "message": "Datero obtenido exitosamente",
  "data": {
    "user": {
      "id": 10,
      "name": "Juan Pérez",
      "email": "juan@example.com",
      "phone": "999999999",
      "dni": "12345678",
      "role": "datero",
      "is_active": true,
      "banco": "BCP",
      "cuenta_bancaria": "123-4567890-0-12",
      "cci_bancaria": "00212345678901234567",
      "lider": {
        "id": 3,
        "name": "Líder Cazador",
        "email": "lider@example.com"
      }
    }
  }
}
```

### Ejemplo Flutter

```dart
Future<void> getDatero(String token, int id) async {
  final uri = Uri.parse('https://tu-dominio.com/api/cazador/dateros/$id');

  final response = await http.get(
    uri,
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    // data['data']['user']
  } else if (response.statusCode == 404) {
    // no encontrado
  } else if (response.statusCode == 403) {
    // no pertenece a este cazador
  }
}
```

---

## 4. Actualizar un datero

- **PUT** `/api/cazador/dateros/{id}`  
  o  
  **PATCH** `/api/cazador/dateros/{id}`

- **Headers**:
  - `Authorization: Bearer <token>`
  - `Content-Type: application/json`
  - `Accept: application/json`

### Body JSON (todos opcionales, pero si se envían deben ser válidos)

```json
{
  "name": "Nombre actualizado",
  "email": "nuevo-correo@example.com",
  "phone": "988888888",
  "dni": "87654321",
  "pin": "654321",
  "banco": "BBVA",
  "cuenta_bancaria": "321-0987654-0-98",
  "cci_bancaria": "00232109876540987654",
  "is_active": true
}
```

### Validaciones clave

- `dni` (si viene): `size:8`, `regex:/^[0-9]{8}$/`, único en `users` (ignorando el propio ID).
- `pin` (si viene): `size:6`, `regex:/^[0-9]{6}$/`.
- `email` (si viene): formato válido y único (ignorando el propio ID).

Si se envía un nuevo `pin`, se actualizan:
- `pin` (hash)
- `password` (hash del mismo PIN)

### Respuesta 200 (ejemplo)

```json
{
  "success": true,
  "message": "Datero actualizado exitosamente",
  "data": {
    "user": {
      "id": 10,
      "name": "Nombre actualizado",
      "email": "nuevo-correo@example.com",
      "phone": "988888888",
      "dni": "87654321",
      "role": "datero",
      "is_active": true,
      "banco": "BBVA",
      "cuenta_bancaria": "321-0987654-0-98",
      "cci_bancaria": "00232109876540987654",
      "lider": {
        "id": 3,
        "name": "Líder Cazador",
        "email": "lider@example.com"
      }
    }
  }
}
```

### Ejemplo Flutter

```dart
Future<void> updateDatero(String token, int id, Map<String, dynamic> updates) async {
  final uri = Uri.parse('https://tu-dominio.com/api/cazador/dateros/$id');

  final response = await http.patch(
    uri,
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode(updates),
  );

  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    // data['data']['user'] -> datero actualizado
  } else if (response.statusCode == 422) {
    // errores de validación (dni/email duplicados, formatos, etc.)
  } else {
    // otros errores
  }
}
```

---

## Manejo de errores comunes en Flutter

- **401 Unauthorized**: token inexistente, inválido o expirado.
  - Debes redirigir al login y pedir nuevas credenciales.
- **403 Forbidden**: el usuario no tiene permiso (por rol o porque el datero no le pertenece).
- **404 Not Found**: el ID del datero no existe.
- **422 Unprocessable Entity**: errores de validación.  
  - El backend devuelve normalmente un objeto `errors` con los campos y mensajes.

En Flutter, típicamente:

```dart
void handleError(http.Response response) {
  if (response.statusCode == 422) {
    final body = jsonDecode(response.body);
    final errors = body['errors'] ?? {};
    // mostrar mensajes específicos por campo
  } else {
    // mostrar mensaje genérico body['message'] o similar
  }
}
```


