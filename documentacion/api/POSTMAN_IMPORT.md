# Importar Colección de Postman

## Instrucciones para Importar

### 1. Importar la Colección

1. Abre Postman
2. Haz clic en **Import** (botón en la esquina superior izquierda)
3. Selecciona el archivo `CRM_Inmobiliaria_API.postman_collection.json`
4. Haz clic en **Import**

### 2. Importar el Entorno (Opcional pero Recomendado)

1. En Postman, haz clic en **Environments** (en el panel izquierdo)
2. Haz clic en **Import**
3. Selecciona el archivo `CRM_Inmobiliaria_API.postman_environment.json`
4. Haz clic en **Import**
5. Selecciona el entorno "CRM Inmobiliaria - Environment" en el selector de entornos (esquina superior derecha)

### 3. Configurar Variables de Entorno

Edita las variables del entorno según tu configuración:

- **base_url**: URL base de tu API (por defecto: `http://localhost:8000`)
- **auth_token**: Se llenará automáticamente después de hacer login
- **user_id**: ID del usuario autenticado (opcional)

### 4. Obtener Token de Autenticación

1. Ve a la carpeta **Cazador > Auth** o **Datero > Auth**
2. Ejecuta el request **Login**
3. Copia el `token` de la respuesta
4. Pega el token en la variable `auth_token` del entorno

**Nota**: Puedes automatizar esto usando un script de Postman que guarde el token automáticamente.

## Estructura de la Colección

### Cazador
- **Auth**: Login, Me, Logout, Refresh Token, Change Password
- **Clients**: List, Get, Create, Update, Options
- **Projects**: List, Get, Get Units
- **Reservations**: List, Get, Create, Update, Confirm, Cancel, Convert to Sale
- **Documents**: Search Document

### Datero
- **Auth**: Login, Me, Logout, Refresh Token, Change Password
- **Clients**: List, Get, Create, Update, Options
- **Commissions**: List, Get, Stats
- **Profile**: Get, Update, Change Password
- **Documents**: Search Document

### Público
- **Projects**: List, Get, Get Units (sin autenticación)

## Notas Importantes

1. **Todos los requests usan formato JSON (raw)** como se solicitó
2. **Autenticación**: La mayoría de endpoints requieren el header `Authorization: Bearer {token}`
3. **Confirm Reservation**: Este endpoint requiere subir una imagen. En Postman, cambia el body a `form-data` y agrega el campo `image` como archivo
4. **Variables**: Usa `{{base_url}}` y `{{auth_token}}` en los requests para facilitar el cambio de entorno

## Script para Auto-guardar Token

Puedes agregar este script en el request de Login (Tests tab) para guardar automáticamente el token:

```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.environment.set("auth_token", jsonData.data.token);
        console.log("Token guardado automáticamente");
    }
}
```

