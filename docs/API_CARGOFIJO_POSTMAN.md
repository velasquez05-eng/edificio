# API Cargofijo - Flujo Completo y Ejemplos Postman

## 📋 Descripción General

La API `cargofijo.php` gestiona los cargos fijos del edificio y permite generar conceptos de mantenimiento mensuales para todos los departamentos ocupados.

**URL Base:** `/public/api/cargofijo.php`

**Métodos soportados:** POST, GET

**Content-Type:** `application/json`

---

## 🔄 Flujo de la API

### 1. Inicialización
```
1. Request recibido → Verifica método HTTP (POST/GET)
2. Lee input JSON → Decodifica el body
3. Valida existencia de 'action' → Identifica la operación
4. Carga dependencias → Database y CargosFijosModelo
5. Ejecuta handler correspondiente → Procesa la acción
6. Retorna respuesta JSON → Formato estándar
```

### 2. Estructura de Respuesta
Todas las respuestas tienen el formato:
```json
{
    "status": 200|201|400|404|500,
    "message": "Mensaje descriptivo",
    "data": { ... } // (opcional)
}
```

---

## 📮 Endpoints POST Disponibles

### 1. **listarCargosFijos**
Lista todos los cargos fijos (activos e inactivos).

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "listarCargosFijos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Cargos fijos listados exitosamente",
    "data": [
        {
            "id_cargo": 1,
            "nombre_cargo": "Mantenimiento",
            "monto": 150.00,
            "descripcion": "Cargo mensual de mantenimiento",
            "estado": "activo"
        }
    ],
    "total": 1
}
```

---

### 2. **obtenerCargosActivos**
Obtiene solo los cargos fijos con estado 'activo'.

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "obtenerCargosActivos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Cargos activos obtenidos exitosamente",
    "data": [
        {
            "id_cargo": 1,
            "nombre_cargo": "Mantenimiento",
            "monto": 150.00,
            "descripcion": "Cargo mensual de mantenimiento",
            "estado": "activo"
        }
    ],
    "total": 1
}
```

---

### 3. **obtenerCargoPorId**
Obtiene un cargo fijo específico por su ID.

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "obtenerCargoPorId",
    "id_cargo": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Cargo obtenido exitosamente",
    "data": {
        "id_cargo": 1,
        "nombre_cargo": "Mantenimiento",
        "monto": 150.00,
        "descripcion": "Cargo mensual de mantenimiento",
        "estado": "activo"
    }
}
```

**Response (404):**
```json
{
    "status": 404,
    "message": "Cargo no encontrado"
}
```

---

### 4. **crearCargo**
Crea un nuevo cargo fijo.

**Campos requeridos:**
- `nombre_cargo` (string, obligatorio)
- `monto` (number, obligatorio, > 0)

**Campos opcionales:**
- `descripcion` (string)
- `estado` (string: "activo" | "inactivo", default: "activo")

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "crearCargo",
    "nombre_cargo": "Administración",
    "monto": 200.50,
    "descripcion": "Cargo por administración del edificio",
    "estado": "activo"
}
```

**Response (201):**
```json
{
    "status": 201,
    "message": "Cargo fijo creado exitosamente"
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "El campo nombre_cargo es obligatorio"
}
```

```json
{
    "status": 400,
    "message": "El monto debe ser mayor a cero"
}
```

---

### 5. **actualizarCargo**
Actualiza un cargo fijo existente.

**Campos requeridos:**
- `id_cargo` (number, obligatorio)
- `nombre_cargo` (string, obligatorio)
- `monto` (number, obligatorio, > 0)
- `estado` (string: "activo" | "inactivo", obligatorio)

**Campos opcionales:**
- `descripcion` (string)

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "actualizarCargo",
    "id_cargo": 1,
    "nombre_cargo": "Mantenimiento Actualizado",
    "monto": 175.00,
    "descripcion": "Nueva descripción",
    "estado": "activo"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Cargo fijo actualizado exitosamente"
}
```

**Response (404):**
```json
{
    "status": 404,
    "message": "Cargo no encontrado"
}
```

---

### 6. **cambiarEstadoCargo**
Cambia el estado (activo/inactivo) de un cargo fijo.

**Campos requeridos:**
- `id_cargo` (number, obligatorio)
- `estado` (string: "activo" | "inactivo", obligatorio)

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "cambiarEstadoCargo",
    "id_cargo": 1,
    "estado": "inactivo"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estado del cargo actualizado exitosamente"
}
```

---

### 7. **eliminarCargo**
Elimina un cargo fijo (solo si no está en uso).

**Campos requeridos:**
- `id_cargo` (number, obligatorio)

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "eliminarCargo",
    "id_cargo": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Cargo fijo eliminado exitosamente"
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "No se puede eliminar el cargo porque está siendo usado en conceptos de mantenimiento"
}
```

---

### 8. **verificarCargoEnUso**
Verifica si un cargo fijo está siendo usado en conceptos de mantenimiento.

**Campos requeridos:**
- `id_cargo` (number, obligatorio)

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "verificarCargoEnUso",
    "id_cargo": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Verificación completada",
    "data": {
        "id_cargo": 1,
        "en_uso": true
    }
}
```

---

### 9. **obtenerTotalCargosActivos**
Obtiene la suma total de montos de todos los cargos activos.

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "obtenerTotalCargosActivos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Total de cargos activos obtenido exitosamente",
    "data": {
        "total": 350.50
    }
}
```

---

### 10. **obtenerDepartamentosOcupados**
Obtiene la lista de departamentos ocupados (con estado 'activo').

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "obtenerDepartamentosOcupados"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Departamentos ocupados listados exitosamente",
    "data": [
        {
            "id_departamento": 1,
            "numero": "101",
            "piso": 1
        },
        {
            "id_departamento": 2,
            "numero": "201",
            "piso": 2
        }
    ],
    "total": 2
}
```

---

### 11. **generarConceptosMantenimiento**
Genera conceptos de mantenimiento para todos los departamentos ocupados basándose en los cargos fijos activos.

**Campos requeridos:**
- `year` (number, 2020-2100, obligatorio)
- `month` (number, 1-12, obligatorio)

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "generarConceptosMantenimiento",
    "year": 2024,
    "month": 12
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Conceptos de mantenimiento generados exitosamente",
    "data": {
        "success": true,
        "total_conceptos": 20,
        "total_monto": 3000.00,
        "departamentos": 10,
        "cargos": 2
    }
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "Ya se generaron conceptos de mantenimiento para este mes"
}
```

**Nota:** Este endpoint:
- Obtiene todos los cargos fijos activos
- Obtiene todos los departamentos ocupados
- Crea un concepto por cada combinación cargo-departamento
- Usa transacciones para garantizar integridad
- Previene duplicados verificando si ya existen conceptos para ese mes

---

### 12. **verificarConceptosGenerados**
Verifica si ya se generaron conceptos para un mes específico.

**Campos requeridos:**
- `year` (number, obligatorio)
- `month` (number, 1-12, obligatorio)

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "verificarConceptosGenerados",
    "year": 2024,
    "month": 12
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Verificación completada",
    "data": {
        "year": 2024,
        "month": 12,
        "ya_generados": true
    }
}
```

---

### 13. **obtenerEstadisticasCargos**
Obtiene estadísticas generales de los cargos fijos.

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "obtenerEstadisticasCargos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estadísticas obtenidas exitosamente",
    "data": {
        "total_cargos": 5,
        "cargos_activos": 3,
        "cargos_inactivos": 2,
        "monto_total": 500.00,
        "monto_activos": 350.00,
        "total_departamentos": 10,
        "monto_mensual_total": 3500.00
    }
}
```

---

### 14. **obtenerUltimaGeneracionConceptos**
Obtiene la fecha de la última generación de conceptos de mantenimiento.

**Request:**
```json
POST /public/api/cargofijo.php
Content-Type: application/json

{
    "action": "obtenerUltimaGeneracionConceptos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Última generación obtenida exitosamente",
    "data": {
        "ultima_generacion": "2024-12-01 10:30:00"
    }
}
```

---

## 🔧 Configuración en Postman

### 1. Crear una nueva Collection
- Nombre: "API Cargofijo"
- Base URL: `http://tu-dominio.com/public/api/cargofijo.php`

### 2. Headers comunes
Para todas las peticiones POST, configurar:
```
Content-Type: application/json
```

### 3. Estructura de Request
- **Method:** POST
- **URL:** `http://tu-dominio.com/public/api/cargofijo.php`
- **Headers:** 
  - `Content-Type: application/json`
- **Body:** Seleccionar `raw` y `JSON`, luego pegar el JSON correspondiente

---

## 📝 Ejemplos de Flujo Completo

### Flujo 1: Crear y usar un cargo fijo
```
1. crearCargo → Crear nuevo cargo
2. obtenerCargoPorId → Verificar creación
3. obtenerCargosActivos → Listar cargos activos
4. generarConceptosMantenimiento → Generar conceptos mensuales
5. verificarConceptosGenerados → Verificar generación
```

### Flujo 2: Actualizar y gestionar estado
```
1. listarCargosFijos → Ver todos los cargos
2. actualizarCargo → Actualizar información
3. cambiarEstadoCargo → Desactivar temporalmente
4. verificarCargoEnUso → Verificar si se puede eliminar
5. eliminarCargo → Eliminar (si no está en uso)
```

### Flujo 3: Generación mensual de conceptos
```
1. obtenerEstadisticasCargos → Ver resumen
2. obtenerDepartamentosOcupados → Ver departamentos
3. obtenerCargosActivos → Ver cargos activos
4. verificarConceptosGenerados → Verificar si ya se generó
5. generarConceptosMantenimiento → Generar conceptos
6. obtenerUltimaGeneracionConceptos → Confirmar generación
```

---

## ⚠️ Códigos de Error Comunes

| Código | Significado | Causa |
|--------|-------------|-------|
| 400 | Bad Request | Campos faltantes, datos inválidos, validación fallida |
| 404 | Not Found | Cargo no encontrado |
| 405 | Method Not Allowed | Método HTTP no permitido |
| 500 | Internal Server Error | Error en base de datos o procesamiento |

---

## 🔒 Validaciones Importantes

1. **Monto:** Debe ser mayor a 0
2. **Estado:** Solo acepta "activo" o "inactivo"
3. **ID Cargo:** Debe ser un número entero positivo
4. **Año:** Rango válido 2020-2100
5. **Mes:** Rango válido 1-12
6. **Cargo en uso:** No se puede eliminar si está en conceptos
7. **Conceptos duplicados:** No se pueden generar dos veces para el mismo mes

---

## 📊 Estructura de Base de Datos

La API interactúa con estas tablas:
- `cargos_fijos` - Almacena los cargos fijos
- `conceptos` - Almacena los conceptos generados
- `departamento` - Información de departamentos
- `tiene_departamento` - Relación persona-departamento

---

## 🚀 Notas Adicionales

- Todos los endpoints POST requieren el campo `action`
- Los endpoints también están disponibles vía GET (usando query params)
- La API maneja CORS automáticamente
- Los errores se registran en el log del servidor
- Las transacciones se usan en operaciones críticas (generación de conceptos)


