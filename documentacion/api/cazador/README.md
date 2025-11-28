# API Cazador - Documentación

## 📋 Introducción

La API Cazador está diseñada para usuarios con rol **Cazador** (vendedores/asesores), **Líder** y **Administrador**. Esta API permite gestionar clientes, consultar proyectos y unidades disponibles, gestionar reservas, buscar documentos y realizar operaciones de autenticación.

## 🔐 Autenticación

La API utiliza **JWT (JSON Web Tokens)** para autenticación. Todas las rutas protegidas requieren un token válido en el header de la petición.

### Header de Autenticación

```
Authorization: Bearer {token}
```

### Base URL

```
https://tu-dominio.com/api/cazador
```

## 📚 Índice de Documentación

- **[AUTH.md](./AUTH.md)** - Autenticación y gestión de sesión
- **[PROJECTS.md](./PROJECTS.md)** - Gestión de proyectos y unidades
- **[CLIENTS.md](./CLIENTS.md)** - Gestión de clientes
- **[RESERVATIONS.md](./RESERVATIONS.md)** - Gestión de reservas
- **[DOCUMENTS.md](./DOCUMENTS.md)** - Búsqueda de documentos (DNI/RUC)

## 🎯 Roles Permitidos

- **Administrador**
- **Líder**
- **Cazador** (Vendedor/Asesor)

> ⚠️ **Nota**: Los usuarios con rol **Datero** NO pueden acceder a esta API.

## 📊 Formato de Respuesta

Todas las respuestas siguen un formato estándar:

### Respuesta Exitosa

```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {
    // Datos de la respuesta
  }
}
```

### Respuesta de Error

```json
{
  "success": false,
  "message": "Mensaje de error",
  "errors": {
    // Detalles del error (opcional)
  }
}
```

## 📄 Códigos de Estado HTTP

- `200` - Éxito
- `201` - Creado exitosamente
- `400` - Solicitud incorrecta
- `401` - No autenticado
- `403` - Acceso denegado
- `404` - Recurso no encontrado
- `409` - Conflicto (recurso ya existe)
- `422` - Error de validación
- `500` - Error del servidor

## 🔒 Rate Limiting

- **Autenticación**: 5 requests por minuto
- **Endpoints generales**: 60 requests por minuto
- **Búsqueda de documentos**: 30 requests por minuto
- **Opciones de formularios**: 120 requests por minuto

## 📝 Paginación

Los endpoints que devuelven listas utilizan paginación. El formato de respuesta incluye:

```json
{
  "data": {
    "items": [...],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7,
      "from": 1,
      "to": 15
    }
  }
}
```

### Parámetros de Paginación

- `per_page`: Número de elementos por página (máximo 100, por defecto 15)
- `page`: Número de página (por defecto 1)

## 📬 Colección de Postman

Para facilitar las pruebas de la API, se ha creado una colección completa de Postman que incluye todos los endpoints documentados.

### Archivos Disponibles

- **`Cazador_API.postman_collection.json`** - Colección completa con todos los endpoints
- **`Cazador_API.postman_environment.json`** - Variables de entorno (opcional)
- **`POSTMAN_IMPORT.md`** - Guía detallada de importación y uso

### Importar en Postman

1. Abre Postman
2. Haz clic en **Import**
3. Selecciona `Cazador_API.postman_collection.json`
4. (Opcional) Importa también `Cazador_API.postman_environment.json`
5. Configura la variable `base_url` con tu URL base

Para más detalles, consulta [POSTMAN_IMPORT.md](./POSTMAN_IMPORT.md).

## 🚀 Inicio Rápido

1. **Autenticarse**: Obtener token JWT
   ```bash
   POST /api/cazador/auth/login
   ```

2. **Usar el token**: Incluir en todas las peticiones
   ```bash
   Authorization: Bearer {tu_token}
   ```

3. **Consultar recursos**: Usar los endpoints documentados

## 📖 Documentación Detallada

Consulta los archivos específicos para cada módulo:

- [Autenticación](./AUTH.md)
- [Proyectos](./PROJECTS.md)
- [Clientes](./CLIENTS.md)
- [Reservas](./RESERVATIONS.md)
- [Búsqueda de Documentos](./DOCUMENTS.md)

---

**Última actualización**: 2024-12-19

