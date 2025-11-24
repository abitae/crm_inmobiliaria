# 📚 Documentación del CRM Inmobiliario

## Índice de Documentación

### 📱 API REST

1. **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)** - Documentación completa de la API
   - Introducción y configuración
   - Autenticación JWT
   - Endpoints de aplicación Datero
   - Endpoints de aplicación Cazador
   - Rutas públicas
   - Modelos de datos
   - Manejo de errores
   - Ejemplos de integración

2. **[API_QUICK_REFERENCE.md](./API_QUICK_REFERENCE.md)** - Guía rápida de referencia
   - Endpoints principales
   - Headers requeridos
   - Códigos HTTP
   - Filtros comunes
   - Tips y mejores prácticas

3. **[API_POSTMAN_COLLECTION.json](./API_POSTMAN_COLLECTION.json)** - Colección de Postman
   - Importar en Postman para pruebas rápidas
   - Variables preconfiguradas
   - Ejemplos de requests
   - Auto-guardado de tokens

### 📖 Documentación Específica

4. **[API_DATERO.md](./API_DATERO.md)** - Documentación específica para Dateros (legacy)
5. **[API_PROYECTOS.md](./API_PROYECTOS.md)** - Documentación de proyectos (legacy)
6. **[PROMPT_DESARROLLO_MOBILE.md](./PROMPT_DESARROLLO_MOBILE.md)** - Guía para desarrollo móvil

---

## 🚀 Inicio Rápido

### Para Desarrolladores Móviles

1. **Leer primero:** [API_QUICK_REFERENCE.md](./API_QUICK_REFERENCE.md)
2. **Documentación completa:** [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
3. **Importar en Postman:** [API_POSTMAN_COLLECTION.json](./API_POSTMAN_COLLECTION.json)

### Estructura de la API

```
/api/datero/          → Aplicación para Dateros
/api/cazador/         → Aplicación para Cazadores (Vendedores)
/api/projects/        → Rutas públicas (proyectos publicados)
```

---

## 🔑 Autenticación

Ambas aplicaciones usan **JWT (JSON Web Tokens)**:

1. Hacer login en `/api/{app}/auth/login`
2. Obtener el token de la respuesta
3. Incluir token en header: `Authorization: Bearer {token}`
4. Token expira en 60 minutos (usar refresh token)

---

## 📋 Endpoints Principales

### Aplicación Datero
- Autenticación: `/api/datero/auth/*`
- Clientes: `/api/datero/clients/*`
- Comisiones: `/api/datero/commissions/*`
- Perfil: `/api/datero/profile/*`

### Aplicación Cazador
- Autenticación: `/api/cazador/auth/*`
- Clientes: `/api/cazador/clients/*`
- Proyectos: `/api/cazador/projects/*`

---

## 🛠️ Herramientas Recomendadas

- **Postman:** Para probar endpoints
- **Insomnia:** Alternativa a Postman
- **cURL:** Para pruebas desde terminal
- **Postman Collection:** Importar `API_POSTMAN_COLLECTION.json`

---

## 📞 Soporte

Para consultas sobre la API, revisar la documentación completa o contactar al equipo de desarrollo.

---

**Última actualización:** 2025-11-24  
**Versión API:** 1.0

