# API Quick Reference - Guía Rápida

## 🔑 Autenticación Rápida

### Datero
```bash
POST /api/datero/auth/login
Body: { "email": "...", "password": "..." }
```

### Cazador
```bash
POST /api/cazador/auth/login
Body: { "email": "...", "password": "..." }
```

---

## 📱 Aplicación Datero - Endpoints

### Autenticación
- `POST /api/datero/auth/login` - Login
- `GET /api/datero/auth/me` - Usuario actual
- `POST /api/datero/auth/refresh` - Renovar token
- `POST /api/datero/auth/logout` - Cerrar sesión

### Clientes
- `GET /api/datero/clients` - Listar (solo del datero)
- `POST /api/datero/clients` - Crear
- `GET /api/datero/clients/{id}` - Ver
- `PUT /api/datero/clients/{id}` - Actualizar
- `GET /api/datero/clients/options` - Opciones formulario

### Comisiones
- `GET /api/datero/commissions` - Listar
- `GET /api/datero/commissions/stats` - Estadísticas
- `GET /api/datero/commissions/{id}` - Ver

### Perfil
- `GET /api/datero/profile` - Ver perfil
- `PUT /api/datero/profile` - Actualizar perfil
- `POST /api/datero/profile/change-password` - Cambiar contraseña

---

## 🎯 Aplicación Cazador - Endpoints

### Autenticación
- `POST /api/cazador/auth/login` - Login
- `GET /api/cazador/auth/me` - Usuario actual
- `POST /api/cazador/auth/refresh` - Renovar token
- `POST /api/cazador/auth/logout` - Cerrar sesión

### Clientes
- `GET /api/cazador/clients` - Listar (asignados o creados)
- `POST /api/cazador/clients` - Crear
- `GET /api/cazador/clients/{id}` - Ver
- `PUT /api/cazador/clients/{id}` - Actualizar
- `GET /api/cazador/clients/options` - Opciones formulario

### Proyectos
- `GET /api/cazador/projects` - Listar todos (completos)
- `GET /api/cazador/projects/{id}` - Ver completo
- `GET /api/cazador/projects/{id}/units` - Ver unidades

---

## 🌐 Rutas Públicas

- `GET /api/projects` - Proyectos publicados
- `GET /api/projects/{id}` - Ver proyecto publicado
- `GET /api/projects/{id}/units` - Unidades de proyecto publicado

---

## 📋 Headers Requeridos

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  # Para rutas protegidas
```

---

## 🚦 Rate Limits

- Login: **5 req/min**
- Endpoints generales: **60 req/min**
- Opciones: **120 req/min**

---

## ✅ Códigos HTTP

- `200` - Éxito
- `201` - Creado
- `400` - Solicitud incorrecta
- `401` - No autenticado
- `403` - Acceso denegado
- `404` - No encontrado
- `422` - Error de validación
- `429` - Rate limit excedido
- `500` - Error servidor

---

## 📝 Formato Respuesta

**Éxito:**
```json
{
    "success": true,
    "message": "...",
    "data": { ... }
}
```

**Error:**
```json
{
    "success": false,
    "message": "...",
    "errors": { ... }
}
```

---

## 🔍 Filtros Comunes

### Clientes
- `?search=texto` - Búsqueda
- `?status=nuevo` - Por estado
- `?type=comprador` - Por tipo
- `?source=redes_sociales` - Por origen
- `?per_page=20` - Resultados por página

### Proyectos
- `?search=texto` - Búsqueda
- `?project_type=lotes` - Por tipo
- `?stage=venta_activa` - Por etapa
- `?status=activo` - Por estado
- `?has_available_units=true` - Con unidades disponibles

---

## 💡 Tips

1. **Tokens JWT:** Expiran en 60 minutos, usar refresh token
2. **Paginación:** Máximo 100 resultados por página
3. **Fechas:** Formato `YYYY-MM-DD` o `YYYY-MM-DD HH:mm:ss`
4. **Validación:** Siempre revisar `errors` en respuestas 422
5. **Seguridad:** Usar HTTPS en producción

---

**Versión:** 1.0  
**Última actualización:** 2025-11-24

