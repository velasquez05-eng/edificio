# API Planilla - Flujo Completo y Ejemplos Postman

## 📋 Descripción General

La API `planilla.php` gestiona las planillas de pago de empleados del edificio. Permite generar planillas completas, personalizadas y múltiples, consultar historiales y obtener estadísticas.

**URL Base:** `/public/api/planilla.php`

**Métodos soportados:** POST, GET

**Content-Type:** `application/json`

**Nota importante:** La API descifra automáticamente datos sensibles (nombres, apellidos) usando AES-256-CBC.

---

## 🔄 Flujo de la API

### 1. Inicialización
```
1. REQUEST ENTRANTE
   ↓
2. VERIFICACIÓN DE MÉTODO (POST/GET)
   ↓
3. DECODIFICACIÓN JSON (si es POST)
   ↓
4. VALIDACIÓN DE 'action'
   ↓
5. CARGA DE DEPENDENCIAS (Database + PlanillaModelo + PersonaModelo)
   ↓
6. EJECUCIÓN DEL HANDLER CORRESPONDIENTE
   ↓
7. DESCIFRADO DE DATOS SENSIBLES (nombres, apellidos)
   ↓
8. RESPUESTA JSON ESTRUCTURADA
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

### SECCIÓN 1: GENERACIÓN DE PLANILLAS

#### 1. **generarPlanillaCompleta**
Genera planillas para TODOS los empleados activos de un mes/año específico usando un stored procedure.

**Campos requeridos:**
- `mes` (number, 1-12, obligatorio)
- `anio` (number, 2020-2030, obligatorio)
- `metodo_pago` (string: "transferencia" | "qr" | "efectivo" | "cheque", obligatorio)

**Campos opcionales:**
- `forzar` (boolean, default: false) - Si true, regenera planilla aunque ya exista

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "generarPlanillaCompleta",
    "mes": 12,
    "anio": 2024,
    "metodo_pago": "transferencia",
    "forzar": false
}
```

**Response (201):**
```json
{
    "status": 201,
    "message": "Planilla completa generada exitosamente",
    "data": {
        "resumen": {
            "total_empleados": 10,
            "total_liquido": 35000.00
        },
        "detalles": [
            {
                "id_planilla_emp": 1,
                "id_persona": 1,
                "nombre_completo": "Juan Pérez García",
                "liquido_pagable": 3500.00
            }
        ]
    },
    "periodo": "2024-12"
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "El mes debe estar entre 1 y 12"
}
```

**Nota:** Este endpoint usa el stored procedure `GenerarPlanillaCompleta` que:
- Obtiene todos los empleados activos con salario base > 0
- Calcula días trabajados (30 por defecto)
- Calcula haber básico, total ganado, descuentos
- Calcula líquido pagable
- Previene duplicados (a menos que `forzar=true`)

---

#### 2. **generarPlanillaPersonalizada**
Genera planilla para UN empleado específico con descuentos personalizados.

**Campos requeridos:**
- `id_persona` (number, obligatorio)
- `mes` (number, 1-12, obligatorio)
- `anio` (number, 2020-2030, obligatorio)
- `dias_descuento` (number, 0-30, obligatorio) - Días a descontar del salario
- `metodo_pago` (string: "transferencia" | "qr" | "efectivo" | "cheque", obligatorio)

**Campos opcionales:**
- `forzar` (boolean, default: false)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "generarPlanillaPersonalizada",
    "id_persona": 1,
    "mes": 12,
    "anio": 2024,
    "dias_descuento": 5,
    "metodo_pago": "transferencia",
    "forzar": false
}
```

**Response (201):**
```json
{
    "status": 201,
    "message": "Planilla personalizada generada exitosamente",
    "data": {
        "id_planilla_emp": 1,
        "id_persona": 1,
        "periodo": "2024-12-01",
        "haber_basico": 3500.00,
        "dias_trabajados": 25,
        "total_ganado": 2916.67,
        "total_descuentos": 583.33,
        "liquido_pagable": 2333.34
    },
    "id_persona": 1,
    "periodo": "2024-12"
}
```

**Response (404):**
```json
{
    "status": 404,
    "message": "La persona especificada no existe"
}
```

**Nota:** Este endpoint usa el stored procedure `GenerarPlanillaPersonalizada` que:
- Calcula días trabajados = 30 - días_descuento
- Calcula proporcionalmente el salario
- Aplica descuentos según los días trabajados

---

#### 3. **generarPlanillaMultiple**
Genera planillas para múltiples empleados con descuentos personalizados por cada uno usando JSON.

**Campos requeridos:**
- `mes` (number, 1-12, obligatorio)
- `anio` (number, 2020-2030, obligatorio)
- `json_descuentos` (array/JSON string, obligatorio) - Formato: `[{"id_persona": 1, "dias_descuento": 5}, ...]`
- `metodo_pago` (string: "transferencia" | "qr" | "efectivo" | "cheque", obligatorio)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "generarPlanillaMultiple",
    "mes": 12,
    "anio": 2024,
    "json_descuentos": [
        {
            "id_persona": 1,
            "dias_descuento": 5
        },
        {
            "id_persona": 2,
            "dias_descuento": 0
        },
        {
            "id_persona": 3,
            "dias_descuento": 10
        }
    ],
    "metodo_pago": "transferencia"
}
```

**Response (201):**
```json
{
    "status": 201,
    "message": "Planillas múltiples generadas exitosamente",
    "data": [
        {
            "id_planilla_emp": 1,
            "id_persona": 1,
            "liquido_pagable": 2333.34
        },
        {
            "id_planilla_emp": 2,
            "id_persona": 2,
            "liquido_pagable": 3500.00
        },
        {
            "id_planilla_emp": 3,
            "id_persona": 3,
            "liquido_pagable": 1166.67
        }
    ],
    "total_empleados": 3,
    "periodo": "2024-12"
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "Formato JSON inválido"
}
```

**Nota:** Este endpoint usa el stored procedure `GenerarPlanillaMultipleAvanzada` que:
- Procesa múltiples empleados en una sola transacción
- Aplica descuentos personalizados por empleado
- Retorna resultados de todas las planillas generadas

---

### SECCIÓN 2: CONSULTAS

#### 4. **listarPlanillasCompleto**
Lista todas las planillas del sistema (para administración).

**Campos opcionales:**
- `mes` (number, 1-12) - Si se proporciona, también debe proporcionarse `anio`
- `anio` (number, 2020-2030) - Si se proporciona, también debe proporcionarse `mes`

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "listarPlanillasCompleto",
    "mes": 12,
    "anio": 2024
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Planillas listadas exitosamente",
    "data": [
        {
            "id_planilla_emp": 1,
            "periodo": "2024-12-01",
            "id_persona": 1,
            "nombre": "Juan",
            "apellido_paterno": "Pérez",
            "apellido_materno": "García",
            "nombre_completo": "Juan Pérez García",
            "rol": "Administrador",
            "haber_basico": 3500.00,
            "dias_trabajados": 30,
            "total_ganado": 3500.00,
            "descuento_gestora": 350.00,
            "total_descuentos": 350.00,
            "liquido_pagable": 3150.00,
            "estado": "pendiente",
            "metodo_pago": "transferencia",
            "fecha_pago": null,
            "fecha_creacion": "2024-12-01 10:00:00"
        }
    ],
    "total": 1,
    "filtros": {
        "mes": 12,
        "anio": 2024
    }
}
```

---

#### 5. **listarMiPlanilla**
Lista las planillas de un empleado específico.

**Campos requeridos:**
- `id_persona` (number, obligatorio)

**Campos opcionales:**
- `mes` (number, 1-12) - Si se proporciona, también debe proporcionarse `anio`
- `anio` (number, 2020-2030) - Si se proporciona, también debe proporcionarse `mes`

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "listarMiPlanilla",
    "id_persona": 1,
    "mes": 12,
    "anio": 2024
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Planillas del empleado listadas exitosamente",
    "data": [
        {
            "id_planilla_emp": 1,
            "periodo": "2024-12-01",
            "nombre_completo": "Juan Pérez García",
            "rol": "Administrador",
            "haber_basico": 3500.00,
            "dias_trabajados": 30,
            "total_ganado": 3500.00,
            "liquido_pagable": 3150.00,
            "estado": "pendiente",
            "observacion": "Tiempo completo"
        }
    ],
    "id_persona": 1,
    "total": 1,
    "filtros": {
        "mes": 12,
        "anio": 2024
    }
}
```

**Nota:** Este endpoint retorna máximo 12 registros ordenados por periodo DESC.

---

#### 6. **obtenerPlanillaPorId**
Obtiene una planilla específica por su ID y ID de persona.

**Campos requeridos:**
- `id_planilla_emp` (number, obligatorio)
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "obtenerPlanillaPorId",
    "id_planilla_emp": 1,
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Planilla obtenida exitosamente",
    "data": {
        "id_planilla_emp": 1,
        "periodo": "2024-12-01",
        "id_persona": 1,
        "nombre_completo": "Juan Pérez García",
        "rol": "Administrador",
        "haber_basico": 3500.00,
        "dias_trabajados": 30,
        "total_ganado": 3500.00,
        "descuento_gestora": 350.00,
        "total_descuentos": 350.00,
        "liquido_pagable": 3150.00,
        "estado": "pendiente",
        "metodo_pago": "transferencia",
        "observacion": "Tiempo completo"
    }
}
```

**Response (404):**
```json
{
    "status": 404,
    "message": "Planilla no encontrada"
}
```

---

#### 7. **obtenerDetallePlanilla**
Obtiene el detalle completo de una planilla por su ID (sin requerir id_persona).

**Campos requeridos:**
- `id_planilla_emp` (number, obligatorio)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "obtenerDetallePlanilla",
    "id_planilla_emp": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Detalle de planilla obtenido exitosamente",
    "data": {
        "id_planilla_emp": 1,
        "id_persona": 1,
        "id_rol": 1,
        "periodo": "2024-12-01",
        "haber_basico": 3500.00,
        "dias_trabajados": 30,
        "total_ganado": 3500.00,
        "descuento_gestora": 350.00,
        "total_descuentos": 350.00,
        "liquido_pagable": 3150.00,
        "estado": "pendiente",
        "metodo_pago": "transferencia",
        "fecha_pago": null,
        "fecha_creacion": "2024-12-01 10:00:00",
        "nombre_completo": "Juan Pérez García",
        "rol": "Administrador",
        "rol_descripcion": "Administrador del edificio"
    }
}
```

---

### SECCIÓN 3: ESTADÍSTICAS Y REPORTES

#### 8. **obtenerEstadisticas**
Obtiene estadísticas de planillas para un mes/año específico, incluyendo lista de empleados activos y verificación de planilla existente.

**Campos opcionales:**
- `mes` (number, 1-12, default: mes actual)
- `anio` (number, 2020-2030, default: año actual)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "obtenerEstadisticas",
    "mes": 12,
    "anio": 2024
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estadísticas obtenidas exitosamente",
    "data": {
        "estadisticas": {
            "total_empleados": 10,
            "total_salarios_base": 35000.00,
            "total_ganado": 35000.00,
            "total_gestora": 3500.00,
            "total_descuentos": 3500.00,
            "total_liquido": 31500.00,
            "promedio_dias_trabajados": 30.0,
            "minimo_liquido": 2800.00,
            "maximo_liquido": 3500.00
        },
        "empleados": [
            {
                "id_persona": 1,
                "nombre_completo": "Juan Pérez García",
                "rol": "Administrador",
                "salario_base": 3500.00
            }
        ],
        "existe_planilla": true,
        "periodo": "2024-12"
    }
}
```

---

#### 9. **obtenerResumenAnual**
Obtiene un resumen anual de planillas, agrupado por mes.

**Campos requeridos:**
- `anio` (number, 2020-2030, obligatorio)

**Campos opcionales:**
- `id_persona` (number) - Si se proporciona, filtra por empleado específico

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "obtenerResumenAnual",
    "anio": 2024,
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Resumen anual obtenido exitosamente",
    "data": [
        {
            "mes": 1,
            "total_planillas": 1,
            "total_ganado": 3500.00,
            "total_gestora": 350.00,
            "total_liquido": 3150.00
        },
        {
            "mes": 2,
            "total_planillas": 1,
            "total_ganado": 3500.00,
            "total_gestora": 350.00,
            "total_liquido": 3150.00
        }
    ],
    "anio": 2024,
    "id_persona": 1
}
```

---

### SECCIÓN 4: ENDPOINTS AUXILIARES

#### 10. **obtenerEmpleadosActivos**
Obtiene la lista de empleados activos con salario base > 0.

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "obtenerEmpleadosActivos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Empleados activos listados exitosamente",
    "data": [
        {
            "id_persona": 1,
            "nombre": "Juan",
            "apellido_paterno": "Pérez",
            "apellido_materno": "García",
            "nombre_completo": "Juan Pérez García",
            "rol": "Administrador",
            "salario_base": 3500.00,
            "estado": "activo"
        }
    ],
    "total": 1
}
```

---

#### 11. **verificarPlanillaExistente**
Verifica si ya existe una planilla para un mes/año específico.

**Campos requeridos:**
- `mes` (number, 1-12, obligatorio)
- `anio` (number, 2020-2030, obligatorio)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "verificarPlanillaExistente",
    "mes": 12,
    "anio": 2024
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Verificación completada",
    "data": {
        "existe": true,
        "mes": 12,
        "anio": 2024,
        "periodo": "2024-12"
    }
}
```

---

#### 12. **obtenerMetodosPago**
Obtiene la lista de métodos de pago disponibles.

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "obtenerMetodosPago"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Métodos de pago obtenidos exitosamente",
    "data": {
        "transferencia": "Transferencia Bancaria",
        "qr": "Pago QR",
        "efectivo": "Efectivo",
        "cheque": "Cheque"
    }
}
```

---

### SECCIÓN 5: GESTIÓN

#### 13. **actualizarEstadoPago**
Actualiza el estado de una planilla a "pagada" y registra la fecha de pago.

**Campos requeridos:**
- `id_planilla_emp` (number, obligatorio)
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "actualizarEstadoPago",
    "id_planilla_emp": 1,
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estado de pago actualizado exitosamente"
}
```

---

#### 14. **eliminarPlanillaPeriodo**
Elimina todas las planillas de un período específico (mes/año).

**Campos requeridos:**
- `mes` (number, 1-12, obligatorio)
- `anio` (number, 2020-2030, obligatorio)

**Request:**
```json
POST /public/api/planilla.php
Content-Type: application/json

{
    "action": "eliminarPlanillaPeriodo",
    "mes": 12,
    "anio": 2024
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Planillas del período eliminadas exitosamente",
    "periodo": "2024-12"
}
```

**⚠️ ADVERTENCIA:** Esta operación es irreversible. Elimina todas las planillas del período especificado.

---

## 🔧 Configuración en Postman

### 1. Crear una nueva Collection
- Nombre: "API Planilla"
- Base URL: `http://tu-dominio.com/public/api/planilla.php`

### 2. Headers comunes
Para todas las peticiones POST, configurar:
```
Content-Type: application/json
```

### 3. Estructura de Request
- **Method:** POST
- **URL:** `http://tu-dominio.com/public/api/planilla.php`
- **Headers:** 
  - `Content-Type: application/json`
- **Body:** Seleccionar `raw` y `JSON`, luego pegar el JSON correspondiente

---

## 📝 Ejemplos de Flujo Completo

### Flujo 1: Generación mensual de planillas
```
1. obtenerEmpleadosActivos → Ver empleados disponibles
2. verificarPlanillaExistente → Verificar si ya existe
3. generarPlanillaCompleta → Generar planillas para todos
4. listarPlanillasCompleto → Verificar planillas generadas
5. obtenerEstadisticas → Ver estadísticas del período
```

### Flujo 2: Planilla personalizada con descuentos
```
1. obtenerEmpleadosActivos → Ver empleados
2. generarPlanillaPersonalizada → Generar con descuentos
3. obtenerPlanillaPorId → Verificar planilla generada
4. actualizarEstadoPago → Marcar como pagada
```

### Flujo 3: Planillas múltiples con descuentos variados
```
1. obtenerEmpleadosActivos → Obtener IDs de empleados
2. Preparar JSON con descuentos por empleado
3. generarPlanillaMultiple → Generar todas las planillas
4. listarPlanillasCompleto → Verificar resultados
```

### Flujo 4: Consulta de historial y reportes
```
1. listarMiPlanilla → Ver planillas del empleado
2. obtenerResumenAnual → Ver resumen anual
3. obtenerEstadisticas → Estadísticas del período
```

---

## ⚠️ Códigos de Error Comunes

| Código | Significado | Causa |
|--------|-------------|-------|
| 400 | Bad Request | Campos faltantes, datos inválidos, validación fallida |
| 404 | Not Found | Planilla o persona no encontrada |
| 405 | Method Not Allowed | Método HTTP no permitido |
| 500 | Internal Server Error | Error en base de datos o stored procedure |

---

## 🔒 Validaciones Importantes

1. **Mes:** Debe estar entre 1 y 12
2. **Año:** Debe estar entre 2020 y 2030
3. **Días de descuento:** Debe estar entre 0 y 30
4. **Método de pago:** Solo acepta: "transferencia", "qr", "efectivo", "cheque"
5. **Filtros mes/año:** Si se proporciona uno, ambos deben proporcionarse
6. **JSON descuentos:** Debe ser un array válido con estructura `[{"id_persona": X, "dias_descuento": Y}]`

---

## 📊 Estructura de Base de Datos

La API interactúa con estas tablas:
- `planilla_empleado` - Almacena las planillas generadas
- `persona` - Información de empleados (datos cifrados)
- `rol` - Roles y salarios base de empleados

---

## 🔐 Seguridad y Cifrado

- Los datos sensibles (nombre, apellidos) se almacenan cifrados en la base de datos
- La API descifra automáticamente estos datos al consultarlos
- Se usa AES-256-CBC para el cifrado/descifrado
- Las respuestas JSON incluyen los datos ya descifrados

---

## 🚀 Notas Adicionales

- Todos los endpoints POST requieren el campo `action`
- Los endpoints también están disponibles vía GET (usando query params)
- La API maneja CORS automáticamente
- Los errores se registran en el log del servidor
- Las transacciones se usan en operaciones críticas (generación de planillas)
- Los stored procedures manejan la lógica de cálculo de salarios

---

## 📈 Estados de Planilla

- `pendiente` - Planilla generada, pendiente de pago
- `pagada` - Planilla pagada

---

## 💰 Cálculo de Planillas

### Fórmulas utilizadas:
- **Días trabajados:** 30 - días_descuento (en planilla personalizada)
- **Total ganado:** (haber_basico / 30) × dias_trabajados
- **Descuento gestora:** 10% del haber básico
- **Total descuentos:** descuento_gestora + otros descuentos
- **Líquido pagable:** total_ganado - total_descuentos

---

## 🔄 Stored Procedures Utilizados

1. **GenerarPlanillaCompleta** - Genera planillas para todos los empleados activos
2. **GenerarPlanillaPersonalizada** - Genera planilla para un empleado con descuentos
3. **GenerarPlanillaMultipleAvanzada** - Genera planillas múltiples con JSON de descuentos

---

## 📋 Formato JSON para Planillas Múltiples

El campo `json_descuentos` debe tener este formato:

```json
[
    {
        "id_persona": 1,
        "dias_descuento": 5
    },
    {
        "id_persona": 2,
        "dias_descuento": 0
    },
    {
        "id_persona": 3,
        "dias_descuento": 10
    }
]
```

Cada objeto debe tener:
- `id_persona` (number, obligatorio) - ID del empleado
- `dias_descuento` (number, 0-30, obligatorio) - Días a descontar


