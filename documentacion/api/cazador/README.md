# API Cazador - Documentación

## 📋 Introducción

La API Cazador está diseñada para usuarios con rol **Cazador** (vendedores/asesores), **Líder** y **Administrador**. Esta API permite gestionar clientes, consultar proyectos y unidades disponibles, y realizar operaciones de autenticación.

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

---

**Última actualización**: 2024-01-01

