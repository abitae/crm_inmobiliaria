# 📬 Importar Colección de Postman

Esta guía te ayudará a importar y configurar la colección de Postman para la API Cazador.

## 📦 Archivos Disponibles

1. **`Cazador_API.postman_collection.json`** - Colección completa con todos los endpoints
2. **`Cazador_API.postman_environment.json`** - Variables de entorno (opcional)

## 🚀 Pasos para Importar

### 1. Importar la Colección

1. Abre Postman
2. Haz clic en **Import** (botón en la esquina superior izquierda)
3. Selecciona el archivo `Cazador_API.postman_collection.json`
4. Haz clic en **Import**

### 2. Importar el Entorno (Opcional pero Recomendado)

1. En Postman, haz clic en **Environments** (lateral izquierdo)
2. Haz clic en **Import**
3. Selecciona el archivo `Cazador_API.postman_environment.json`
4. Haz clic en **Import**
5. Selecciona el entorno "API Cazador - Environment" en el selector de entornos (esquina superior derecha)

### 3. Configurar Variables de Entorno

1. Selecciona el entorno "API Cazador - Environment"
2. Haz clic en el ícono de ojo (👁️) para ver las variables
3. Edita la variable `base_url` con tu URL base:
   - Ejemplo: `https://api.tu-dominio.com` o `http://localhost:8000`
4. La variable `auth_token` se llenará automáticamente cuando inicies sesión

## 🔐 Autenticación

### Obtener Token

1. Ve a la carpeta **🔐 Autenticación**
2. Ejecuta la request **"Iniciar Sesión"**
3. El token se guardará automáticamente en la variable `auth_token`
4. Todas las demás requests usarán este token automáticamente

### Usar Token Manualmente

Si necesitas usar un token existente:

1. Selecciona el entorno "API Cazador - Environment"
2. Edita la variable `auth_token` y pega tu token JWT
3. Todas las requests usarán este token

## 📝 Estructura de la Colección

La colección está organizada en las siguientes carpetas:

### 🔐 Autenticación
- Iniciar Sesión
- Obtener Usuario Autenticado
- Cerrar Sesión
- Renovar Token
- Cambiar Contraseña

### 🏗️ Proyectos
- Listar Proyectos
- Obtener Proyecto Específico
- Obtener Unidades de un Proyecto

### 👥 Clientes
- Listar Clientes
- Obtener Cliente Específico
- Crear Cliente
- Actualizar Cliente
- Obtener Opciones para Formularios

### 🎫 Reservas
- Listar Reservas
- Obtener Reserva Específica
- Crear Reserva
- Actualizar Reserva
- Confirmar Reserva
- Cancelar Reserva
- Convertir Reserva a Venta

### 🔍 Documentos
- Buscar Documento (DNI/RUC)

## 🎯 Características

### Variables Automáticas

- **`auth_token`**: Se llena automáticamente al iniciar sesión
- **`user_id`**: Se llena automáticamente al iniciar sesión
- **`base_url`**: Debes configurarla manualmente

### Pre-request Scripts

Algunas requests incluyen scripts que:
- Guardan automáticamente el token después del login
- Renuevan el token automáticamente

### Tests Automáticos

Las requests de autenticación incluyen tests que:
- Verifican que la respuesta sea exitosa
- Guardan el token en variables de entorno
- Registran información en la consola

## 🔧 Personalización

### Cambiar URL Base

1. Selecciona el entorno
2. Edita la variable `base_url`
3. Ejemplo: `http://localhost:8000` para desarrollo local

### Agregar Nuevas Variables

1. Selecciona el entorno
2. Haz clic en **Add**
3. Agrega el nombre y valor de la variable
4. Las variables estarán disponibles en todas las requests como `{{variable_name}}`

## 📚 Uso de Parámetros

### Parámetros de Query

Muchas requests incluyen parámetros de query que puedes habilitar/deshabilitar:
- Haz clic en la request
- Ve a la pestaña **Params**
- Marca/desmarca los parámetros que necesites
- Edita los valores según sea necesario

### Variables de Path

Algunas requests usan variables de path (ej: `:id`):
- Edita el valor directamente en la URL
- O usa la pestaña **Params** para editarlo

### Body de Requests

Las requests POST/PUT incluyen ejemplos de body:
- Edita el JSON según tus necesidades
- Los campos marcados como requeridos son obligatorios

## ⚠️ Notas Importantes

1. **Rate Limiting**: Algunos endpoints tienen límites de requests por minuto:
   - Autenticación: 5 requests/minuto
   - Endpoints generales: 60 requests/minuto
   - Búsqueda de documentos: 30 requests/minuto

2. **Token Expiración**: Los tokens JWT expiran después de un tiempo (por defecto 60 minutos). Usa "Renovar Token" antes de que expire.

3. **Permisos**: Algunos endpoints solo están disponibles para ciertos roles (Administrador, Líder, Cazador).

4. **Confirmar Reserva**: Requiere subir un archivo de imagen. En Postman, selecciona el archivo en el campo `image` del body tipo `form-data`.

## 🐛 Solución de Problemas

### Error 401 (No autenticado)
- Verifica que el token esté configurado correctamente
- Intenta iniciar sesión nuevamente
- Verifica que el token no haya expirado

### Error 403 (Acceso denegado)
- Verifica que tu usuario tenga el rol correcto
- Algunos endpoints requieren roles específicos

### Error 404 (No encontrado)
- Verifica que la URL base sea correcta
- Verifica que el ID del recurso exista

### Variables no funcionan
- Verifica que el entorno esté seleccionado
- Verifica que las variables estén escritas correctamente: `{{variable_name}}`
- Verifica que las variables estén habilitadas

## 📖 Documentación Completa

Para más detalles sobre cada endpoint, consulta:
- [README.md](./README.md) - Introducción general
- [AUTH.md](./AUTH.md) - Autenticación
- [PROJECTS.md](./PROJECTS.md) - Proyectos
- [CLIENTS.md](./CLIENTS.md) - Clientes
- [RESERVATIONS.md](./RESERVATIONS.md) - Reservas
- [DOCUMENTS.md](./DOCUMENTS.md) - Búsqueda de documentos

---

**Última actualización**: 2024-12-19


