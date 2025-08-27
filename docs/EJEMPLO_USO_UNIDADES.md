# Ejemplos de Uso - Creación Automática de Unidades

## Ejemplo 1: Proyecto de Departamentos

### Configuración del Proyecto
```
Nombre: "Residencial Los Pinos"
Tipo: Departamentos
Total de Unidades: 120
```

### Plantilla de Unidad
```
Tipo: Departamento
Área: 85 m²
Precio Base: 2,500 PEN/m²
Dormitorios: 2
Baños: 2
Estacionamientos: 1
Cocheras: 0
Área de Balcón: 8 m²
Área de Terraza: 0 m²
Área de Jardín: 0 m²
Descuento: 5%
Comisión: 6%
```

### Resultado
- Se crearán 120 departamentos numerados del 1 al 120
- **Manzanas**: A (1-10), B (11-20), C (21-30), ..., L (111-120)
- **Pisos**: 1-20 (se repiten cada 20 unidades)
- **Torres**: A (1-20), B (21-40), C (41-60), D (61-80), E (81-100), F (101-120)
- **Precio por Unidad**: S/ 212,500 (85 m² × 2,500 PEN/m²)
- **Precio Final**: S/ 201,875 (con 5% de descuento)
- **Comisión**: S/ 12,112.50 (6% del precio final)

## Ejemplo 2: Proyecto de Casas

### Configuración del Proyecto
```
Nombre: "Villa Garden"
Tipo: Casas
Total de Unidades: 25
```

### Plantilla de Unidad
```
Tipo: Casa
Área: 180 m²
Precio Base: 1,800 PEN/m²
Dormitorios: 3
Baños: 3
Estacionamientos: 2
Cocheras: 1
Área de Balcón: 0 m²
Área de Terraza: 25 m²
Área de Jardín: 80 m²
Descuento: 0%
Comisión: 4%
```

### Resultado
- Se crearán 25 casas numeradas del 1 al 25
- **Manzanas**: A (1-10), B (11-20), C (21-25)
- **Pisos**: 1 (todas las casas son de un piso)
- **Torres**: null (no aplica para casas)
- **Bloques**: Bloque 1 (1-5), Bloque 2 (6-10), Bloque 3 (11-15), Bloque 4 (16-20), Bloque 5 (21-25)
- **Precio por Unidad**: S/ 324,000 (180 m² × 1,800 PEN/m²)
- **Precio Final**: S/ 324,000 (sin descuento)
- **Comisión**: S/ 12,960 (4% del precio final)

## Ejemplo 3: Proyecto de Lotes

### Configuración del Proyecto
```
Nombre: "Lotes El Paraíso"
Tipo: Lotes
Total de Unidades: 50
```

### Plantilla de Unidad
```
Tipo: Lote
Área: 300 m²
Precio Base: 1,200 PEN/m²
Dormitorios: 0
Baños: 0
Estacionamientos: 0
Cocheras: 0
Área de Balcón: 0 m²
Área de Terraza: 0 m²
Área de Jardín: 0 m²
Descuento: 10%
Comisión: 3%
```

### Resultado
- Se crearán 50 lotes numerados del 1 al 50
- **Manzanas**: A (1-10), B (11-20), C (21-30), D (31-40), E (41-50)
- **Pisos**: null (no aplica para lotes)
- **Torres**: null (no aplica para lotes)
- **Bloques**: null (no aplica para lotes)
- **Precio por Unidad**: S/ 360,000 (300 m² × 1,200 PEN/m²)
- **Precio Final**: S/ 324,000 (con 10% de descuento)
- **Comisión**: S/ 9,720 (3% del precio final)

## Ejemplo 4: Proyecto de Oficinas

### Configuración del Proyecto
```
Nombre: "Centro Empresarial Plaza Mayor"
Tipo: Oficinas
Total de Unidades: 80
```

### Plantilla de Unidad
```
Tipo: Oficina
Área: 120 m²
Precio Base: 3,500 PEN/m²
Dormitorios: 0
Baños: 2
Estacionamientos: 3
Cocheras: 0
Área de Balcón: 0 m²
Área de Terraza: 0 m²
Área de Jardín: 0 m²
Descuento: 8%
Comisión: 7%
```

### Resultado
- Se crearán 80 oficinas numeradas del 1 al 80
- **Manzanas**: A (1-10), B (11-20), C (21-30), D (31-40), E (41-50), F (51-60), G (61-70), H (71-80)
- **Pisos**: 1-20 (se repiten cada 20 unidades)
- **Torres**: A (1-20), B (21-40), C (41-60), D (61-80)
- **Bloques**: null (no aplica para oficinas)
- **Precio por Unidad**: S/ 420,000 (120 m² × 3,500 PEN/m²)
- **Precio Final**: S/ 386,400 (con 8% de descuento)
- **Comisión**: S/ 27,048 (7% del precio final)

## Flujo de Trabajo Completo

### Paso 1: Crear Proyecto
1. Ir a la sección de Proyectos
2. Hacer clic en "Nuevo Proyecto"
3. Llenar información básica del proyecto
4. Especificar el número total de unidades

### Paso 2: Activar Creación Automática
1. En la sección "Unidades del Proyecto"
2. Hacer clic en "Activar" en "Crear Unidades Automáticamente"
3. Se mostrará la nueva sección "Configuración de Plantilla de Unidades"

### Paso 3: Configurar Plantilla
1. Seleccionar el tipo de unidad
2. Configurar área y precio base
3. Ajustar dormitorios, baños, estacionamientos
4. Configurar áreas adicionales (balcón, terraza, jardín)
5. Establecer porcentajes de descuento y comisión

### Paso 4: Revisar Vista Previa
1. Ver las unidades que se crearán
2. Verificar la numeración y asignación de manzanas/pisos/torres
3. Revisar los precios calculados automáticamente
4. Ajustar la plantilla si es necesario

### Paso 5: Guardar Proyecto
1. Hacer clic en "Crear Proyecto"
2. El sistema creará el proyecto
3. Se crearán automáticamente todas las unidades
4. Mensaje de confirmación con el número de unidades creadas

## Casos Especiales

### Proyecto Mixto
Para proyectos con diferentes tipos de unidades, se puede:
1. Crear el proyecto con un tipo base
2. Crear las unidades automáticamente
3. Editar individualmente las unidades que requieran configuración diferente

### Proyecto con Muchas Unidades
Para proyectos con más de 1000 unidades:
1. Considerar usar transacciones de base de datos
2. Implementar cola de trabajos para mejor performance
3. Dividir en lotes más pequeños si es necesario

### Personalización Post-Creación
Después de crear las unidades automáticamente:
1. Cada unidad puede ser editada individualmente
2. Se pueden modificar precios, descuentos, comisiones
3. Se pueden agregar características específicas
4. Se pueden cambiar estados (disponible, reservado, vendido)

## Consejos de Uso

### 1. Planificación
- Definir claramente el tipo de proyecto antes de empezar
- Calcular el área promedio por unidad
- Establecer el precio base por m² del mercado
- Considerar descuentos y comisiones estándar del mercado

### 2. Configuración
- Usar valores realistas para las áreas
- Configurar dormitorios y baños según el tipo de unidad
- Ajustar estacionamientos según la normativa local
- Considerar áreas adicionales para valor agregado

### 3. Revisión
- Siempre revisar la vista previa antes de crear
- Verificar que la numeración sea lógica
- Confirmar que los precios sean correctos
- Validar que las asignaciones de manzanas/pisos/torres sean coherentes

### 4. Mantenimiento
- Las unidades creadas automáticamente mantienen la trazabilidad
- Se puede identificar fácilmente cuáles fueron creadas automáticamente
- Los cambios posteriores se registran en el historial
- Se mantiene la integridad de los datos del proyecto
