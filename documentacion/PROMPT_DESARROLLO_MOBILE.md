# Prompt Profesional para Desarrollo de Aplicación Móvil Flutter - CRM Inmobiliario

## 🎯 Contexto del Proyecto

Necesito desarrollar una aplicación móvil Flutter para un sistema CRM inmobiliario. La aplicación está dirigida a usuarios con rol **"datero"** (captadores de clientes) que trabajan en campo y necesitan gestionar clientes desde sus dispositivos móviles.

### Información del Cliente

- **Sector:** Inmobiliario
- **Usuarios objetivo:** Dateros (captadores de leads)
- **Plataformas:** iOS y Android
- **Modo de operación:** Online/Offline (con sincronización)

---

## 📚 Documentación de la API

La documentación completa de la API REST está disponible en el archivo `API_DATERO.md`. Esta documentación incluye:

- ✅ Endpoints de autenticación JWT
- ✅ Endpoints de gestión de clientes (CRUD completo)
- ✅ Modelos de datos Dart incluidos
- ✅ Servicios Flutter pre-implementados
- ✅ Ejemplos de código funcionales
- ✅ Manejo de errores y validaciones
- ✅ Rate limiting y mejores prácticas

**IMPORTANTE:** Debes seguir estrictamente la documentación de la API. Todos los modelos de datos, servicios y estructuras ya están definidos en `API_DATERO.md`.

---

## 🛠️ Stack Tecnológico Requerido

### Framework y Lenguaje
- **Flutter SDK:** Versión 3.16.0 o superior
- **Dart:** Versión 3.2.0 o superior
- **Plataformas:** iOS 12.0+, Android API 21+

### Dependencias Principales
```yaml
dependencies:
  flutter:
    sdk: flutter
  
  # HTTP y Red
  dio: ^5.4.0
  http: ^1.1.0
  
  # Almacenamiento Local
  shared_preferences: ^2.2.2
  flutter_secure_storage: ^9.0.0
  hive: ^2.2.3
  hive_flutter: ^1.1.0
  
  # Estado y Arquitectura
  provider: ^6.1.1
  riverpod: ^2.4.9
  flutter_riverpod: ^2.4.9
  
  # UI y Componentes
  flutter_svg: ^2.0.9
  cached_network_image: ^3.3.0
  shimmer: ^3.0.0
  
  # Formularios y Validación
  flutter_form_builder: ^9.1.1
  form_builder_validators: ^9.1.1
  
  # Utilidades
  intl: ^0.19.0
  uuid: ^4.2.1
  connectivity_plus: ^5.0.2
  
  # Navegación
  go_router: ^13.0.0
  
  # Material Design
  material_design_icons_flutter: ^7.0.7296
  google_fonts: ^6.1.0
  
  # Iconos adicionales
  cupertino_icons: ^1.0.6
  font_awesome_flutter: ^10.6.0
```

---

## 📱 Requisitos Funcionales

### 1. Autenticación y Seguridad

#### 1.1 Login
- [ ] Pantalla de login con email y contraseña
- [ ] Validación de campos en tiempo real
- [ ] Manejo de errores de autenticación (401, 403)
- [ ] Indicadores de carga durante el proceso
- [ ] Opción "Recordarme" (persistencia de sesión)
- [ ] Manejo de cuenta inactiva
- [ ] Verificación de rol datero

#### 1.2 Gestión de Sesión
- [ ] Almacenamiento seguro de token JWT
- [ ] Refresh automático de token antes de expirar
- [ ] Logout con invalidación de token
- [ ] Persistencia de datos de usuario
- [ ] Verificación de sesión al iniciar app

#### 1.3 Configuración de API
- [ ] Pantalla de configuración de baseUrl accesible desde Settings
- [ ] Validación de formato URL (debe comenzar con http:// o https://)
- [ ] Almacenamiento persistente de baseUrl usando SharedPreferences
- [ ] URL por defecto para producción configurable
- [ ] Selector de entornos (Desarrollo, Staging, Producción)
- [ ] Input manual de URL personalizada con validación en tiempo real
- [ ] Test de conectividad con el servidor antes de guardar
- [ ] Indicador visual de estado de conexión (éxito/error)
- [ ] Opción de resetear a URL por defecto
- [ ] Actualización automática de servicios al cambiar URL
- [ ] Normalización de URL (agregar /api si falta)

### 2. Gestión de Clientes

#### 2.1 Listado de Clientes
- [ ] Vista de lista paginada de clientes
- [ ] Búsqueda en tiempo real (con debounce)
- [ ] Filtros por:
  - Estado (nuevo, contacto_inicial, en_seguimiento, cierre, perdido)
  - Tipo (inversor, comprador, empresa, constructor)
  - Origen (redes_sociales, ferias, referidos, formulario_web, publicidad)
- [ ] Pull-to-refresh
- [ ] Scroll infinito para paginación
- [ ] Indicadores de carga
- [ ] Vista vacía cuando no hay clientes
- [ ] Manejo de errores de red

#### 2.2 Detalle de Cliente
- [ ] Vista detallada del cliente
- [ ] Información completa con formato legible
- [ ] Contadores de oportunidades, actividades y tareas
- [ ] Botones de acción (editar, eliminar)
- [ ] Navegación desde el listado
- [ ] Compartir información del cliente

#### 2.3 Crear Cliente
- [ ] Formulario completo con validación
- [ ] Campos requeridos claramente marcados
- [ ] Selectores para:
  - Tipo de documento (DNI)
  - Tipo de cliente
  - Origen del lead
  - Estado inicial
- [ ] Validación de documento único en tiempo real
- [ ] Slider para puntuación (score 0-100)
- [ ] Campo de notas multilínea
- [ ] Indicadores de carga al guardar
- [ ] Mensajes de éxito/error
- [ ] Redirección después de crear exitosamente

#### 2.4 Editar Cliente
- [ ] Formulario pre-llenado con datos actuales
- [ ] Validación de permisos (solo propietario)
- [ ] Actualización parcial (PATCH)
- [ ] Confirmación antes de guardar cambios
- [ ] Historial de cambios (opcional)

#### 2.5 Opciones de Formularios
- [ ] Carga de opciones desde API al iniciar
- [ ] Cacheo local de opciones
- [ ] Sincronización periódica en background
- [ ] Manejo de fallo de red usando cache

### 3. Funcionalidades Offline

- [ ] Sincronización de datos en background
- [ ] Cola de operaciones pendientes
- [ ] Modo offline detectado automáticamente
- [ ] Indicador visual de estado de conexión
- [ ] Sincronización automática al reconectar
- [ ] Resolución de conflictos (último en ganar)

### 4. UX/UI

#### 4.1 Diseño
- [ ] Diseño moderno y profesional
- [ ] Paleta de colores consistente
- [ ] Tipografía legible y jerarquizada
- [ ] Iconografía clara y reconocible
- [ ] Espaciado y padding adecuados
- [ ] Animaciones fluidas
- [ ] Responsive para diferentes tamaños de pantalla

#### 4.2 Componentes Reutilizables
- [ ] Botones estilizados
- [ ] Campos de texto con validación visual
- [ ] Cards para clientes
- [ ] Dialogs de confirmación
- [ ] Snackbars para mensajes
- [ ] Loaders y skeletons
- [ ] Empty states
- [ ] Error states

### 5. Configuración y Settings

- [ ] Pantalla de configuración/ajustes
- [ ] Configuración de baseUrl del API
- [ ] Selector de entornos (Desarrollo, Staging, Producción)
- [ ] Input personalizado para URL custom
- [ ] Validación de URL antes de guardar
- [ ] Test de conectividad con el servidor
- [ ] Persistencia de configuración
- [ ] Opción de resetear configuración

### 6. Manejo de Errores

- [ ] Manejo de todos los códigos HTTP (401, 403, 404, 422, 429, 500)
- [ ] Mensajes de error amigables al usuario
- [ ] Logging de errores para debugging
- [ ] Retry automático para errores temporales
- [ ] Manejo de timeout de conexión
- [ ] Manejo de rate limiting (429)
- [ ] Manejo de errores de conectividad/configuración

---

## 🏗️ Arquitectura Sugerida

### Estructura de Carpetas

```
lib/
├── main.dart
├── app.dart
│
├── config/
│   ├── app_config.dart
│   ├── routes.dart
│   └── theme.dart
│
├── core/
│   ├── constants/
│   ├── utils/
│   ├── exceptions/
│   └── extensions/
│
├── data/
│   ├── models/
│   │   ├── user_model.dart
│   │   ├── client_model.dart
│   │   ├── api_response.dart
│   │   └── client_options.dart
│   │
│   ├── repositories/
│   │   ├── auth_repository.dart
│   │   └── client_repository.dart
│   │
│   ├── services/
│   │   ├── api_service.dart
│   │   ├── auth_service.dart
│   │   ├── client_service.dart
│   │   └── storage_service.dart
│   │
│   └── local/
│       ├── hive_boxes.dart
│       ├── cache_manager.dart
│       └── config_storage.dart
│
├── domain/
│   ├── entities/
│   └── usecases/
│
├── presentation/
│   ├── providers/
│   │   ├── auth_provider.dart
│   │   └── client_provider.dart
│   │
│   ├── screens/
│   │   ├── auth/
│   │   │   ├── login_screen.dart
│   │   │   └── splash_screen.dart
│   │   │
│   │   ├── clients/
│   │   │   ├── clients_list_screen.dart
│   │   │   ├── client_detail_screen.dart
│   │   │   ├── client_form_screen.dart
│   │   │   └── widgets/
│   │   │       ├── client_card.dart
│   │   │       ├── client_filters.dart
│   │   │       └── client_search_bar.dart
│   │   │
│   │   ├── settings/
│   │   │   ├── settings_screen.dart
│   │   │   ├── api_config_screen.dart
│   │   │   └── widgets/
│   │   │       ├── url_input_field.dart
│   │   │       └── environment_selector.dart
│   │   │
│   │   └── home/
│   │       └── home_screen.dart
│   │
│   ├── widgets/
│   │   ├── common/
│   │   │   ├── app_button.dart
│   │   │   ├── app_text_field.dart
│   │   │   ├── app_card.dart
│   │   │   ├── loading_indicator.dart
│   │   │   ├── error_widget.dart
│   │   │   └── empty_state.dart
│   │   │
│   │   └── layout/
│   │       ├── app_scaffold.dart
│   │       └── app_drawer.dart
│   │
│   └── theme/
│       ├── app_theme.dart
│       ├── app_colors.dart
│       ├── app_text_styles.dart
│       ├── app_dimensions.dart
│       ├── app_icons.dart
│       └── app_spacing.dart
│
└── utils/
    ├── validators.dart
    ├── formatters.dart
    └── helpers.dart
```

### Patrón de Arquitectura

**Recomendado:** Clean Architecture + Provider/Riverpod

- **Data Layer:** Servicios, Repositorios, Modelos
- **Domain Layer:** Entidades y Casos de Uso
- **Presentation Layer:** Screens, Widgets, Providers

---

## 📋 Especificaciones Técnicas Detalladas

### 1. Configuración de API

```dart
// config/app_config.dart
class AppConfig {
  static const Duration connectionTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  static const int maxRetryAttempts = 3;
  
  // URLs por defecto según entorno
  static const String defaultProductionUrl = 'https://crm_inmobiliaria.test/api';
  static const String defaultStagingUrl = 'https://crm_inmobiliaria.test/api';
  static const String defaultDevelopmentUrl = 'https://crm_inmobiliaria.test/api';
  
  // URL por defecto (puede ser sobreescrita por configuración del usuario)
  static String get defaultBaseUrl => defaultProductionUrl;
}

// config/api_config.dart
import 'package:shared_preferences/shared_preferences.dart';

enum ApiEnvironment {
  production('Producción', AppConfig.defaultProductionUrl),
  staging('Staging', AppConfig.defaultStagingUrl),
  development('Desarrollo', AppConfig.defaultDevelopmentUrl),
  custom('Personalizada', '');

  final String label;
  final String defaultUrl;
  
  const ApiEnvironment(this.label, this.defaultUrl);
}

class ApiConfigService {
  static const String _envKey = 'api_environment';
  static const String _customUrlKey = 'api_custom_url';
  
  // Obtener URL base configurada
  static Future<String> getBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    final envString = prefs.getString(_envKey) ?? ApiEnvironment.production.name;
    final environment = ApiEnvironment.values.firstWhere(
      (e) => e.name == envString,
      orElse: () => ApiEnvironment.production,
    );
    
    if (environment == ApiEnvironment.custom) {
      final customUrl = prefs.getString(_customUrlKey);
      if (customUrl != null && customUrl.isNotEmpty) {
        return customUrl;
      }
      return AppConfig.defaultProductionUrl;
    }
    
    return environment.defaultUrl;
  }
  
  // Guardar configuración de entorno
  static Future<void> setEnvironment(ApiEnvironment environment) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_envKey, environment.name);
  }
  
  // Guardar URL personalizada
  static Future<void> setCustomUrl(String url) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_customUrlKey, url);
  }
  
  // Obtener entorno actual
  static Future<ApiEnvironment> getCurrentEnvironment() async {
    final prefs = await SharedPreferences.getInstance();
    final envString = prefs.getString(_envKey) ?? ApiEnvironment.production.name;
    return ApiEnvironment.values.firstWhere(
      (e) => e.name == envString,
      orElse: () => ApiEnvironment.production,
    );
  }
  
  // Validar formato de URL
  static bool isValidUrl(String url) {
    try {
      final uri = Uri.parse(url);
      return uri.hasScheme && (uri.scheme == 'http' || uri.scheme == 'https');
    } catch (e) {
      return false;
    }
  }
  
  // Normalizar URL (agregar /api si no está presente)
  static String normalizeUrl(String url) {
    if (!url.endsWith('/api') && !url.endsWith('/api/')) {
      return url.endsWith('/') ? '${url}api' : '$url/api';
    }
    return url;
  }
  
  // Resetear a configuración por defecto
  static Future<void> resetToDefault() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_envKey);
    await prefs.remove(_customUrlKey);
  }
}
```

### 2. Manejo de Tokens

- Almacenar token en `flutter_secure_storage`
- Implementar interceptor en Dio para agregar token automáticamente
- Refresh token antes de que expire (5 minutos antes)
- Manejar 401 refrescando token y reintentando petición

### 3. Validaciones de Formularios

```dart
// Ejemplo de validaciones
- name: required, min 2 caracteres, max 255
- document_number: required, único, format según tipo
- phone: opcional, formato válido
- email: formato válido (si se agrega)
- score: 0-100, integer
- birth_date: formato YYYY-MM-DD
```

### 4. Estado de la Aplicación

- Usar Provider o Riverpod para gestión de estado
- Separar estado global (auth) de estado local (pantallas)
- Implementar estados de carga, éxito y error

### 5. Navegación

- Usar GoRouter para navegación declarativa
- Rutas protegidas con middleware de autenticación
- Deep linking preparado

### 6. Cache y Offline

- Cachear opciones de formularios en Hive
- Implementar cola de sincronización
- Detectar cambios de conectividad
- Mostrar estado offline en UI

---

## 🎨 Guía de Diseño UI - Material Design 3

La aplicación debe seguir estrictamente las **Material Design Guidelines** de Google, específicamente Material Design 3 (Material You).

### Principios de Material Design

1. **Material es metafórico:** Las superficies tienen elevación y sombras
2. **Bold, gráfico, intencional:** Uso de tipografía, grid y color
3. **Movimiento proporciona significado:** Animaciones con propósito
4. **Color, superficie e iconografía:** Consistencia visual en toda la app

### Sistema de Colores Material Design 3

```dart
import 'package:flutter/material.dart';

class AppColors {
  // Material Design 3 Color Scheme
  static const Color primary = Color(0xFF2196F3); // Material Blue
  static const Color primaryContainer = Color(0xFFBBDEFB);
  static const Color onPrimary = Color(0xFFFFFFFF);
  static const Color onPrimaryContainer = Color(0xFF0D47A1);
  
  static const Color secondary = Color(0xFF4CAF50); // Material Green
  static const Color secondaryContainer = Color(0xFFC8E6C9);
  static const Color onSecondary = Color(0xFFFFFFFF);
  static const Color onSecondaryContainer = Color(0xFF1B5E20);
  
  static const Color tertiary = Color(0xFFFF9800); // Material Orange
  static const Color tertiaryContainer = Color(0xFFFFE0B2);
  static const Color onTertiary = Color(0xFFFFFFFF);
  static const Color onTertiaryContainer = Color(0xFFE65100);
  
  // Estado y Semántica
  static const Color error = Color(0xFFB00020);
  static const Color errorContainer = Color(0xFFFFDAD6);
  static const Color onError = Color(0xFFFFFFFF);
  static const Color onErrorContainer = Color(0xFF410002);
  
  static const Color success = Color(0xFF4CAF50);
  static const Color warning = Color(0xFFFF9800);
  static const Color info = Color(0xFF2196F3);
  
  // Superficies y Fondos
  static const Color surface = Color(0xFFFFFFFF);
  static const Color surfaceVariant = Color(0xFFF5F5F5);
  static const Color onSurface = Color(0xFF212121);
  static const Color onSurfaceVariant = Color(0xFF757575);
  
  static const Color background = Color(0xFFFAFAFA);
  static const Color onBackground = Color(0xFF212121);
  
  // Outlines y Bordes
  static const Color outline = Color(0xFFBDBDBD);
  static const Color outlineVariant = Color(0xFFE0E0E0);
  
  // Elevación y Sombras (Material Design elevation)
  static List<BoxShadow> getElevation(int level) {
    switch (level) {
      case 1:
        return [BoxShadow(color: Colors.black12, blurRadius: 1, offset: Offset(0, 1))];
      case 2:
        return [BoxShadow(color: Colors.black12, blurRadius: 2, offset: Offset(0, 2))];
      case 3:
        return [BoxShadow(color: Colors.black26, blurRadius: 4, offset: Offset(0, 4))];
      case 4:
        return [BoxShadow(color: Colors.black26, blurRadius: 8, offset: Offset(0, 8))];
      default:
        return [];
    }
  }
}

// Material Design 3 Theme
class AppTheme {
  static ThemeData lightTheme = ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.light(
      primary: AppColors.primary,
      primaryContainer: AppColors.primaryContainer,
      onPrimary: AppColors.onPrimary,
      onPrimaryContainer: AppColors.onPrimaryContainer,
      secondary: AppColors.secondary,
      secondaryContainer: AppColors.secondaryContainer,
      onSecondary: AppColors.onSecondary,
      onSecondaryContainer: AppColors.onSecondaryContainer,
      tertiary: AppColors.tertiary,
      tertiaryContainer: AppColors.tertiaryContainer,
      onTertiary: AppColors.onTertiary,
      onTertiaryContainer: AppColors.onTertiaryContainer,
      error: AppColors.error,
      errorContainer: AppColors.errorContainer,
      onError: AppColors.onError,
      onErrorContainer: AppColors.onErrorContainer,
      surface: AppColors.surface,
      surfaceVariant: AppColors.surfaceVariant,
      onSurface: AppColors.onSurface,
      onSurfaceVariant: AppColors.onSurfaceVariant,
      background: AppColors.background,
      onBackground: AppColors.onBackground,
      outline: AppColors.outline,
      outlineVariant: AppColors.outlineVariant,
    ),
    typography: Typography.material2021(),
    textTheme: TextTheme(
      displayLarge: TextStyle(
        fontSize: 57,
        fontWeight: FontWeight.w400,
        letterSpacing: -0.25,
        fontFamily: 'Roboto',
      ),
      displayMedium: TextStyle(
        fontSize: 45,
        fontWeight: FontWeight.w400,
        letterSpacing: 0,
        fontFamily: 'Roboto',
      ),
      displaySmall: TextStyle(
        fontSize: 36,
        fontWeight: FontWeight.w400,
        letterSpacing: 0,
        fontFamily: 'Roboto',
      ),
      headlineLarge: TextStyle(
        fontSize: 32,
        fontWeight: FontWeight.w400,
        letterSpacing: 0,
        fontFamily: 'Roboto',
      ),
      headlineMedium: TextStyle(
        fontSize: 28,
        fontWeight: FontWeight.w400,
        letterSpacing: 0,
        fontFamily: 'Roboto',
      ),
      headlineSmall: TextStyle(
        fontSize: 24,
        fontWeight: FontWeight.w400,
        letterSpacing: 0,
        fontFamily: 'Roboto',
      ),
      titleLarge: TextStyle(
        fontSize: 22,
        fontWeight: FontWeight.w500,
        letterSpacing: 0,
        fontFamily: 'Roboto',
      ),
      titleMedium: TextStyle(
        fontSize: 16,
        fontWeight: FontWeight.w500,
        letterSpacing: 0.15,
        fontFamily: 'Roboto',
      ),
      titleSmall: TextStyle(
        fontSize: 14,
        fontWeight: FontWeight.w500,
        letterSpacing: 0.1,
        fontFamily: 'Roboto',
      ),
      bodyLarge: TextStyle(
        fontSize: 16,
        fontWeight: FontWeight.w400,
        letterSpacing: 0.5,
        fontFamily: 'Roboto',
      ),
      bodyMedium: TextStyle(
        fontSize: 14,
        fontWeight: FontWeight.w400,
        letterSpacing: 0.25,
        fontFamily: 'Roboto',
      ),
      bodySmall: TextStyle(
        fontSize: 12,
        fontWeight: FontWeight.w400,
        letterSpacing: 0.4,
        fontFamily: 'Roboto',
      ),
      labelLarge: TextStyle(
        fontSize: 14,
        fontWeight: FontWeight.w500,
        letterSpacing: 0.1,
        fontFamily: 'Roboto',
      ),
      labelMedium: TextStyle(
        fontSize: 12,
        fontWeight: FontWeight.w500,
        letterSpacing: 0.5,
        fontFamily: 'Roboto',
      ),
      labelSmall: TextStyle(
        fontSize: 11,
        fontWeight: FontWeight.w500,
        letterSpacing: 0.5,
        fontFamily: 'Roboto',
      ),
    ),
    cardTheme: CardTheme(
      elevation: 1,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      margin: EdgeInsets.all(8),
    ),
    buttonTheme: ButtonThemeData(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        elevation: 2,
        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),
    ),
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(
        padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: AppColors.surfaceVariant,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: AppColors.outline),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: AppColors.outline),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: AppColors.primary, width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: AppColors.error),
      ),
      contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
    ),
    chipTheme: ChipThemeData(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
      ),
      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
    ),
    appBarTheme: AppBarTheme(
      elevation: 0,
      centerTitle: false,
      backgroundColor: AppColors.surface,
      foregroundColor: AppColors.onSurface,
      titleTextStyle: TextStyle(
        fontSize: 20,
        fontWeight: FontWeight.w500,
        color: AppColors.onSurface,
        fontFamily: 'Roboto',
      ),
    ),
    floatingActionButtonTheme: FloatingActionButtonThemeData(
      elevation: 4,
      backgroundColor: AppColors.primary,
      foregroundColor: AppColors.onPrimary,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
    ),
  );
}
```

### Iconografía Material Design

Usar **Material Design Icons** como fuente principal de iconos:

```dart
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';

// Iconos principales de la app
class AppIcons {
  // Navegación
  static const IconData home = MdiIcons.home;
  static const IconData clients = MdiIcons.accountGroup;
  static const IconData profile = MdiIcons.account;
  static const IconData settings = MdiIcons.cog;
  
  // Acciones de Clientes
  static const IconData addClient = MdiIcons.accountPlus;
  static const IconData editClient = MdiIcons.accountEdit;
  static const IconData deleteClient = MdiIcons.accountRemove;
  static const IconData viewClient = MdiIcons.accountEye;
  
  // Filtros y Búsqueda
  static const IconData search = MdiIcons.magnify;
  static const IconData filter = MdiIcons.filter;
  static const IconData sort = MdiIcons.sort;
  static const IconData close = MdiIcons.close;
  
  // Estados y Tipos
  static const IconData newStatus = MdiIcons.starOutline;
  static const IconData contactInitial = MdiIcons.phoneInTalk;
  static const IconData followUp = MdiIcons.handshake;
  static const IconData closing = MdiIcons.checkCircle;
  static const IconData lost = MdiIcons.closeCircle;
  
  // Tipos de Cliente
  static const IconData investor = MdiIcons.chartLine;
  static const IconData buyer = MdiIcons.home;
  static const IconData company = MdiIcons.officeBuilding;
  static const IconData builder = MdiIcons.hammerWrench;
  
  // Documentos
  static const IconData dni = MdiIcons.creditCard;
  static const IconData document = MdiIcons.fileDocument;
  
  // Información
  static const IconData phone = MdiIcons.phone;
  static const IconData email = MdiIcons.email;
  static const IconData location = MdiIcons.mapMarker;
  static const IconData calendar = MdiIcons.calendar;
  static const IconData notes = MdiIcons.noteText;
  
  // Acciones Generales
  static const IconData save = MdiIcons.contentSave;
  static const IconData cancel = MdiIcons.cancel;
  static const IconData refresh = MdiIcons.refresh;
  static const IconData logout = MdiIcons.logout;
  static const IconData login = MdiIcons.login;
  static const IconData sync = MdiIcons.sync;
  
  // UI
  static const IconData menu = MdiIcons.menu;
  static const IconData more = MdiIcons.dotsVertical;
  static const IconData arrowBack = MdiIcons.arrowLeft;
  static const IconData arrowForward = MdiIcons.arrowRight;
  static const IconData chevronDown = MdiIcons.chevronDown;
  static const IconData chevronUp = MdiIcons.chevronUp;
  
  // Estados de Conexión
  static const IconData online = MdiIcons.wifi;
  static const IconData offline = MdiIcons.wifiOff;
  static const IconData syncPending = MdiIcons.cloudUpload;
  
  // Validación y Errores
  static const IconData check = MdiIcons.check;
  static const IconData error = MdiIcons.alertCircle;
  static const IconData warning = MdiIcons.alert;
  static const IconData info = MdiIcons.information;
  
  // Scoring
  static const IconData score = MdiIcons.star;
  static const IconData scoreHigh = MdiIcons.star;
  static const IconData scoreMedium = MdiIcons.starHalfFull;
  static const IconData scoreLow = MdiIcons.starOutline;
}
```

### Componentes Material Design

#### Cards de Cliente (Material Design 3)

```dart
import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import '../models/client_model.dart';
import '../theme/app_icons.dart';
import '../theme/app_colors.dart';

class ClientCard extends StatelessWidget {
  final ClientModel client;
  final VoidCallback onTap;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;

  const ClientCard({
    Key? key,
    required this.client,
    required this.onTap,
    this.onEdit,
    this.onDelete,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header con nombre y estado
              Row(
                children: [
                  Expanded(
                    child: Text(
                      client.name,
                      style: Theme.of(context).textTheme.titleLarge,
                    ),
                  ),
                  _StatusChip(status: client.status),
                ],
              ),
              SizedBox(height: 8),
              
              // Información del cliente
              Row(
                children: [
                  Icon(
                    AppIcons.dni,
                    size: 16,
                    color: AppColors.onSurfaceVariant,
                  ),
                  SizedBox(width: 8),
                  Text(
                    '${client.documentType}: ${client.documentNumber}',
                    style: Theme.of(context).textTheme.bodyMedium,
                  ),
                ],
              ),
              if (client.phone != null) ...[
                SizedBox(height: 4),
                Row(
                  children: [
                    Icon(
                      AppIcons.phone,
                      size: 16,
                      color: AppColors.onSurfaceVariant,
                    ),
                    SizedBox(width: 8),
                    Text(
                      client.phone!,
                      style: Theme.of(context).textTheme.bodyMedium,
                    ),
                  ],
                ),
              ],
              
              SizedBox(height: 12),
              
              // Footer con acciones y métricas
              Row(
                children: [
                  // Score
                  Container(
                    padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: _getScoreColor(client.score).withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          AppIcons.score,
                          size: 14,
                          color: _getScoreColor(client.score),
                        ),
                        SizedBox(width: 4),
                        Text(
                          '${client.score}',
                          style: TextStyle(
                            color: _getScoreColor(client.score),
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                  
                  Spacer(),
                  
                  // Contadores
                  _MetricChip(
                    icon: MdiIcons.briefcase,
                    count: client.opportunitiesCount ?? 0,
                  ),
                  SizedBox(width: 8),
                  _MetricChip(
                    icon: MdiIcons.calendarCheck,
                    count: client.activitiesCount ?? 0,
                  ),
                  SizedBox(width: 8),
                  _MetricChip(
                    icon: MdiIcons.checkCircle,
                    count: client.tasksCount ?? 0,
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Color _getScoreColor(int score) {
    if (score >= 70) return AppColors.success;
    if (score >= 40) return AppColors.warning;
    return AppColors.error;
  }
}

class _StatusChip extends StatelessWidget {
  final String status;

  const _StatusChip({required this.status});

  @override
  Widget build(BuildContext context) {
    return Chip(
      label: Text(
        _getStatusLabel(status),
        style: TextStyle(fontSize: 12),
      ),
      backgroundColor: _getStatusColor(status).withOpacity(0.1),
      labelStyle: TextStyle(color: _getStatusColor(status)),
      avatar: Icon(
        _getStatusIcon(status),
        size: 16,
        color: _getStatusColor(status),
      ),
      padding: EdgeInsets.symmetric(horizontal: 8),
      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
    );
  }

  String _getStatusLabel(String status) {
    final labels = {
      'nuevo': 'Nuevo',
      'contacto_inicial': 'Contacto Inicial',
      'en_seguimiento': 'En Seguimiento',
      'cierre': 'Cierre',
      'perdido': 'Perdido',
    };
    return labels[status] ?? status;
  }

  Color _getStatusColor(String status) {
    final colors = {
      'nuevo': AppColors.info,
      'contacto_inicial': AppColors.primary,
      'en_seguimiento': AppColors.warning,
      'cierre': AppColors.success,
      'perdido': AppColors.error,
    };
    return colors[status] ?? AppColors.onSurfaceVariant;
  }

  IconData _getStatusIcon(String status) {
    final icons = {
      'nuevo': AppIcons.newStatus,
      'contacto_inicial': AppIcons.contactInitial,
      'en_seguimiento': AppIcons.followUp,
      'cierre': AppIcons.closing,
      'perdido': AppIcons.lost,
    };
    return icons[status] ?? MdiIcons.circle;
  }
}

class _MetricChip extends StatelessWidget {
  final IconData icon;
  final int count;

  const _MetricChip({
    required this.icon,
    required this.count,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 14, color: AppColors.onSurfaceVariant),
        SizedBox(width: 4),
        Text(
          '$count',
          style: TextStyle(
            fontSize: 12,
            color: AppColors.onSurfaceVariant,
          ),
        ),
      ],
    );
  }
}
```

#### Botones Material Design

```dart
// Usar componentes Material Design nativos
ElevatedButton(
  onPressed: () {},
  child: Text('Guardar'),
  icon: Icon(AppIcons.save),
)

FilledButton(
  onPressed: () {},
  child: Text('Crear Cliente'),
  icon: Icon(AppIcons.addClient),
)

OutlinedButton(
  onPressed: () {},
  child: Text('Cancelar'),
  icon: Icon(AppIcons.cancel),
)

IconButton(
  onPressed: () {},
  icon: Icon(AppIcons.editClient),
  tooltip: 'Editar',
)
```

#### Text Fields Material Design

```dart
TextField(
  decoration: InputDecoration(
    labelText: 'Nombre completo',
    hintText: 'Ingrese el nombre',
    prefixIcon: Icon(AppIcons.account),
    suffixIcon: hasError ? Icon(AppIcons.error) : null,
  ),
  style: Theme.of(context).textTheme.bodyLarge,
)
```

### Espaciado y Dimensiones Material Design

```dart
class AppSpacing {
  // Material Design spacing system (4dp grid)
  static const double xs = 4.0;
  static const double sm = 8.0;
  static const double md = 16.0;
  static const double lg = 24.0;
  static const double xl = 32.0;
  static const double xxl = 48.0;
  
  // Component specific
  static const double cardPadding = 16.0;
  static const double screenPadding = 16.0;
  static const double buttonPadding = 12.0;
}

class AppDimensions {
  // Border Radius
  static const double radiusSmall = 8.0;
  static const double radiusMedium = 12.0;
  static const double radiusLarge = 16.0;
  static const double radiusXLarge = 24.0;
  
  // Icon Sizes
  static const double iconSmall = 16.0;
  static const double iconMedium = 24.0;
  static const double iconLarge = 32.0;
  
  // Elevation
  static const double elevationNone = 0.0;
  static const double elevationLow = 1.0;
  static const double elevationMedium = 2.0;
  static const double elevationHigh = 4.0;
}
```

### Componentes UI Material Design

#### Cards de Cliente
- Usar `Card` de Flutter con elevación Material Design (elevation: 2)
- Diseño limpio con información esencial
- Badge de estado usando `Chip` de Material Design con colores semánticos
- InkWell para feedback táctil (ripple effect)
- Border radius de 12dp (Material Design estándar)
- Swipe actions usando `Dismissible` widget

#### Formularios Material Design
- Usar `TextField` con `InputDecoration` estilo Material Design 3
- Labels flotantes automáticos (comportamiento estándar de TextField)
- Validación visual usando `errorText` y `errorBorder`
- Iconos usando Material Design Icons en `prefixIcon` y `suffixIcon`
- Botones con estados deshabilitados usando `onPressed: null`
- Usar `FilledButton` para acciones primarias
- Usar `OutlinedButton` para acciones secundarias
- Usar `TextButton` para acciones terciarias

#### Estados de Carga Material Design
- Shimmer effect usando el paquete `shimmer`
- `CircularProgressIndicator` para acciones de carga
- Skeleton screens usando `Card` con `SizedBox` placeholder
- `LinearProgressIndicator` para procesos lineales (opcional)

#### Dialogs y Bottom Sheets
- Usar `AlertDialog` o `Dialog` para confirmaciones
- Usar `BottomSheet` o `showModalBottomSheet` para acciones
- Seguir Material Design guidelines para diálogos

#### Navigation Material Design
- Usar `AppBar` con `useMaterial3: true`
- `FloatingActionButton` para acciones principales
- `BottomNavigationBar` o `NavigationRail` para navegación principal
- `Drawer` para menú lateral con Material Design styling

#### Snackbars y Notificaciones
- Usar `ScaffoldMessenger` con `SnackBar` para mensajes
- Usar `Banner` para errores persistentes
- Seguir Material Design motion para animaciones

### Iconografía Requerida

**OBLIGATORIO:** Usar Material Design Icons (MDI) para todos los iconos de la aplicación.

- ✅ Usar el paquete `material_design_icons_flutter`
- ✅ Todos los iconos deben seguir la paleta Material Design
- ✅ Tamaños estándar: 16dp (small), 24dp (medium), 32dp (large)
- ✅ Usar iconos con significado claro y universalmente reconocibles
- ❌ NO usar iconos personalizados a menos que sea absolutamente necesario
- ❌ NO mezclar diferentes familias de iconos

### Temas y Estilos Material Design

- **Habilitar Material Design 3:** `useMaterial3: true` en ThemeData
- **Color Scheme:** Usar ColorScheme completo de Material Design 3
- **Typography:** Usar las escalas de tipografía Material Design 2021
- **Shape:** Border radius siguiendo Material Design guidelines
- **Elevation:** Usar sistema de elevación Material Design (0-24dp)
- **Motion:** Animaciones siguiendo Material Design motion guidelines

---

## ✅ Criterios de Aceptación

### Funcionalidad
- [ ] Todos los endpoints de la API funcionan correctamente
- [ ] Autenticación JWT implementada y funcionando
- [ ] CRUD completo de clientes operativo
- [ ] Filtros y búsqueda funcionan correctamente
- [ ] Paginación implementada sin problemas de rendimiento
- [ ] Manejo de errores robusto en todos los casos

### UX/UI
- [ ] Material Design 3 completamente implementado (`useMaterial3: true`)
- [ ] Color Scheme Material Design 3 aplicado correctamente
- [ ] Iconografía 100% Material Design Icons (MDI)
- [ ] Tipografía Material Design 2021 (escalas correctas)
- [ ] Diseño coherente en toda la aplicación
- [ ] Navegación intuitiva y fluida
- [ ] Feedback visual Material Design (ripple effects, elevación)
- [ ] Mensajes de error claros y útiles
- [ ] Estados de carga apropiados
- [ ] Animaciones siguiendo Material Design motion
- [ ] Componentes Material Design nativos (Cards, Buttons, TextFields, etc.)

### Rendimiento
- [ ] Tiempo de inicio < 3 segundos
- [ ] Navegación entre pantallas < 200ms
- [ ] Listas renderizan sin lag (< 60fps)
- [ ] Imágenes optimizadas y cacheadas
- [ ] Memoria estable sin leaks

### Calidad de Código
- [ ] Código limpio y bien organizado
- [ ] Comentarios en funciones complejas
- [ ] Nombres descriptivos de variables/funciones
- [ ] Separación de responsabilidades clara
- [ ] Sin código duplicado
- [ ] Manejo adecuado de excepciones

### Testing
- [ ] Tests unitarios para lógica de negocio
- [ ] Tests de widgets para componentes clave
- [ ] Tests de integración para flujos principales

### Seguridad
- [ ] Token almacenado de forma segura
- [ ] Validación de entrada en cliente
- [ ] No almacenar contraseñas
- [ ] Manejo seguro de datos sensibles

---

## 🚀 Entregables

### Código
1. Repositorio Git completo con commits descriptivos
2. Documentación de código inline
3. README.md con instrucciones de setup
4. Changelog de versiones

### Documentación
1. Guía de instalación y configuración
2. Guía de arquitectura
3. Documentación de componentes principales
4. Troubleshooting común

### Testing
1. Suite de tests ejecutable
2. Coverage mínimo del 70%

---

## 📝 Notas Importantes para el Desarrollador

### ⚠️ Puntos Críticos

1. **Autenticación:**
   - Debes verificar que el usuario tenga rol "datero"
   - Solo se pueden acceder a clientes creados por el usuario autenticado
   - Implementar refresh automático de token es CRÍTICO

2. **Validaciones:**
   - El `document_number` debe ser único
   - Validar formato de fechas (YYYY-MM-DD)
   - Score debe estar entre 0-100

3. **Rate Limiting:**
   - Login: 5 intentos por minuto
   - Clientes: 60 solicitudes por minuto
   - Opciones: 120 solicitudes por minuto
   - Implementar retry con backoff exponencial

4. **Offline:**
   - La app debe funcionar parcialmente offline
   - Sincronizar cuando vuelva la conexión
   - Mostrar claramente cuando hay datos sin sincronizar

5. **Errores Comunes a Evitar:**
   - ❌ No hardcodear URLs
   - ❌ No almacenar tokens en texto plano
   - ❌ No hacer polling agresivo
   - ❌ No olvidar manejar estados de carga
   - ❌ No usar widgets pesados en listas largas

### 💡 Recomendaciones

1. **Performance:**
   - Usar `ListView.builder` para listas largas
   - Implementar lazy loading
   - Cachear imágenes y opciones
   - Debounce en búsquedas (300ms mínimo)

2. **UX y Material Design:**
   - Implementar Material Design 3 en toda la aplicación
   - Usar Material Design Icons exclusivamente
   - Mostrar skeletons Material Design mientras carga
   - Pull-to-refresh en todas las listas (Material Design motion)
   - Confirmar acciones destructivas con AlertDialog Material Design
   - Mensajes de éxito usando SnackBar Material Design
   - Ripple effects en todos los elementos interactivos
   - Elevación correcta según Material Design guidelines
   - Animaciones con Material Design motion principles

3. **Mantenibilidad:**
   - Usar constantes para strings
   - Separar lógica de UI
   - Implementar logging para debugging
   - Documentar funciones complejas

---

## 🔄 Proceso de Desarrollo Sugerido

### Fase 1: Setup y Autenticación (Semana 1)
1. Configurar proyecto Flutter
2. Instalar dependencias
3. Configurar estructura de carpetas
4. Implementar servicios de API base
5. Implementar autenticación completa
6. Pantallas de login y splash

### Fase 2: CRUD de Clientes (Semana 2-3)
1. Modelos de datos
2. Servicio de clientes
3. Listado de clientes con filtros
4. Detalle de cliente
5. Crear cliente
6. Editar cliente

### Fase 3: UX y Optimizaciones (Semana 4)
1. Mejorar diseño UI
2. Implementar offline mode
3. Optimizar rendimiento
4. Agregar animaciones
5. Testing básico

### Fase 4: Pulido y Entrega (Semana 5)
1. Testing completo
2. Corrección de bugs
3. Optimizaciones finales
4. Documentación
5. Preparación para producción

---

## 📞 Soporte y Preguntas

Si tienes dudas durante el desarrollo:

1. **Revisa primero:** La documentación API (`API_DATERO.md`)
2. **Verifica:** Los modelos y servicios de ejemplo incluidos
3. **Consulta:** Patrones de diseño Flutter estándar
4. **Prueba:** Endpoints con Postman antes de implementar

---

## 🎯 Objetivo Final

Crear una aplicación móvil Flutter robusta, moderna y fácil de usar que permita a los dateros gestionar sus clientes de forma eficiente desde cualquier lugar, con soporte offline y sincronización automática.

**Calidad esperada:** Lista para producción, bien documentada, mantenible y escalable.

---

**Versión del Prompt:** 1.0  
**Fecha:** 2024-01-15  
**Prioridad:** Alta

---

## 📎 Recursos Adicionales

- [Documentación Flutter Oficial](https://flutter.dev/docs)
- [Dio Package Documentation](https://pub.dev/packages/dio)
- [Provider Package](https://pub.dev/packages/provider)
- [Riverpod Documentation](https://riverpod.dev)
- [Material Design Guidelines](https://m3.material.io/)
- [Material Design 3 for Flutter](https://m3.material.io/develop/flutter)
- [Material Design Icons](https://materialdesignicons.com/)
- [Material Design Components](https://m3.material.io/components)
- [Material Design Motion](https://m3.material.io/styles/motion)
- [Flutter Material Design 3](https://docs.flutter.dev/ui/widgets/material)
- [Flutter Best Practices](https://docs.flutter.dev/development/ui/best-practices)

