# API Factura - Flujo Completo y Ejemplos Postman

## 📋 Descripción General

La API `factura.php` gestiona facturas, conceptos y pagos del edificio. Permite generar facturas mensuales, procesar pagos, y consultar historiales y estadísticas.

**URL Base:** `/public/api/factura.php`

**Métodos soportados:** POST, GET

**Content-Type:** `application/json`

**Nota importante:** La API descifra automáticamente datos sensibles (nombres, apellidos, CI) usando AES-256-CBC.

---

## 🔄 Flujo de la API

### 1. Inicialización
```
1. Request recibido → Verifica método HTTP (POST/GET)
2. Lee input JSON → Decodifica el body
3. Valida existencia de 'action' → Identifica la operación
4. Carga dependencias → Database y FacturaModelo
5. Ejecuta handler correspondiente → Procesa la acción
6. Descifra datos sensibles → Información de personas
7. Retorna respuesta JSON → Formato estándar
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

### SECCIÓN 1: GESTIÓN DE FACTURAS

#### 1. **listarFacturas**
Lista todas las facturas del sistema con información completa.

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "listarFacturas"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Facturas listadas exitosamente",
    "data": [
        {
            "id_factura": 1,
            "id_departamento": 1,
            "departamento": "101",
            "piso": 1,
            "nombre": "Juan",
            "apellido_paterno": "Pérez",
            "apellido_materno": "García",
            "ci": "12345678",
            "residente": "Juan Pérez García",
            "fecha_emision": "2024-12-01",
            "fecha_vencimiento": "2024-12-15",
            "monto_total": 350.50,
            "estado": "pendiente",
            "cantidad_conceptos": 3,
            "pagos_realizados": 0
        }
    ],
    "total": 1
}
```

---

#### 2. **obtenerFacturaCompleta**
Obtiene una factura con todos sus detalles, incluyendo conceptos asociados.

**Campos requeridos:**
- `id_factura` (number, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerFacturaCompleta",
    "id_factura": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Factura completa obtenida exitosamente",
    "data": {
        "factura": {
            "id_factura": 1,
            "id_departamento": 1,
            "departamento": "101",
            "piso": 1,
            "nombre": "Juan",
            "apellido_paterno": "Pérez",
            "apellido_materno": "García",
            "ci": "12345678",
            "residente": "Juan Pérez García",
            "fecha_emision": "2024-12-01",
            "fecha_vencimiento": "2024-12-15",
            "monto_total": 350.50,
            "estado": "pendiente",
            "email": "juan@example.com",
            "telefono": "123456789"
        },
        "conceptos": [
            {
                "concepto": "mantenimiento",
                "descripcion": "Mantenimiento - Diciembre 2024",
                "monto": 150.00,
                "cantidad": 1,
                "fecha_creacion": "2024-12-01 10:00:00"
            },
            {
                "concepto": "agua",
                "descripcion": "Consumo de agua",
                "monto": 100.50,
                "cantidad": 1,
                "fecha_creacion": "2024-12-01 10:00:00"
            }
        ]
    }
}
```

**Response (404):**
```json
{
    "status": 404,
    "message": "Factura no encontrada"
}
```

---

#### 3. **generarFacturas**
Genera facturas para todos los departamentos ocupados de un mes específico usando un stored procedure.

**Campos requeridos:**
- `mes_facturacion` (string, formato: YYYY-MM, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "generarFacturas",
    "mes_facturacion": "2024-12"
}
```

**Response (201):**
```json
{
    "status": 201,
    "message": "Facturas generadas exitosamente para el mes 2024-12",
    "mes": "2024-12"
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "El campo mes_facturacion es obligatorio"
}
```

```json
{
    "status": 400,
    "message": "Formato de mes inválido. Use YYYY-MM"
}
```

**Nota:** Este endpoint usa el stored procedure `generar_facturas_todos_departamentos` que:
- Agrupa conceptos pendientes por departamento
- Crea una factura por departamento
- Asigna conceptos a las facturas
- Calcula el monto total

---

#### 4. **pagarFactura**
Procesa el pago de una factura. Actualiza el estado de la factura y registra el pago en el historial.

**Campos requeridos:**
- `id_factura` (number, obligatorio)

**Campos opcionales:**
- `id_persona` (number, si no se proporciona se obtiene del departamento)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "pagarFactura",
    "id_factura": 1,
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Pago procesado exitosamente",
    "data": {
        "id_factura": 1,
        "id_persona": 1,
        "monto_pagado": 350.50
    }
}
```

**Response (400):**
```json
{
    "status": 400,
    "message": "La factura ya está pagada"
}
```

**Nota:** Este endpoint:
- Verifica que la factura exista y esté pendiente
- Registra el pago en `persona_paga_factura`
- Los triggers automáticamente actualizan `historial_pago` y el estado de la factura
- Usa transacciones para garantizar integridad

---

### SECCIÓN 2: FACTURAS DEL USUARIO

#### 5. **obtenerMisFacturas**
Obtiene todas las facturas de una persona específica.

**Campos requeridos:**
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerMisFacturas",
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Facturas del usuario listadas exitosamente",
    "data": [
        {
            "id_factura": 1,
            "id_departamento": 1,
            "departamento": "101",
            "piso": 1,
            "fecha_emision": "2024-12-01",
            "fecha_vencimiento": "2024-12-15",
            "monto_total": 350.50,
            "estado": "pendiente",
            "cantidad_conceptos": 3,
            "pagos_realizados": 0
        }
    ],
    "id_persona": 1,
    "total": 1
}
```

---

### SECCIÓN 3: HISTORIAL DE PAGOS

#### 6. **obtenerMiHistorialPagos**
Obtiene el historial de pagos de una persona específica con información detallada.

**Campos requeridos:**
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerMiHistorialPagos",
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Historial de pagos del usuario obtenido exitosamente",
    "data": [
        {
            "id_historial_pago": 1,
            "id_factura": 1,
            "id_departamento": 1,
            "departamento": "101",
            "piso": 1,
            "monto_pagado": 350.50,
            "fecha_pago": "2024-12-10 14:30:00",
            "observacion": "Pago realizado mediante QR",
            "monto_factura": 350.50,
            "estado_factura": "pagada",
            "fecha_emision": "2024-12-01",
            "fecha_vencimiento": "2024-12-15",
            "cantidad_conceptos": 3,
            "tipo_pago": "qr",
            "metodo": "QR",
            "icono": "fa-qrcode",
            "puntual": true,
            "monto_pagado_formateado": "350.50",
            "monto_factura_formateado": "350.50",
            "fecha_pago_formateada": "10/12/2024",
            "fecha_pago_hora": "14:30",
            "fecha_emision_formateada": "01/12/2024",
            "fecha_vencimiento_formateada": "15/12/2024"
        }
    ],
    "id_persona": 1,
    "total": 1
}
```

---

#### 7. **obtenerHistorialPagosCompleto**
Obtiene el historial completo de pagos de todos los usuarios del sistema.

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerHistorialPagosCompleto"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Historial completo de pagos obtenido exitosamente",
    "data": [
        {
            "id_historial_pago": 1,
            "id_factura": 1,
            "monto_pagado": 350.50,
            "fecha_pago": "2024-12-10 14:30:00",
            "observacion": "Pago realizado mediante QR",
            "id_departamento": 1,
            "departamento": "101",
            "piso": 1,
            "residente": "Juan Pérez García",
            "tipo_pago": "qr",
            "puntual": true,
            "fecha_pago_formateada": "10/12/2024",
            "fecha_pago_hora": "14:30"
        }
    ],
    "total": 1
}
```

---

#### 8. **obtenerEstadisticasMisPagos**
Obtiene estadísticas de pagos de una persona específica.

**Campos requeridos:**
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerEstadisticasMisPagos",
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estadísticas de pagos del usuario obtenidas exitosamente",
    "data": {
        "total_pagos": 10,
        "total_pagado": 3500.00,
        "total_pagado_formateado": "3,500.00",
        "promedio_pago": 350.00,
        "promedio_pago_formateado": "350.00",
        "primer_pago": "2024-01-15",
        "primer_pago_formateado": "15/01/2024",
        "ultimo_pago": "2024-12-10",
        "ultimo_pago_formateado": "10/12/2024",
        "pagos_puntuales": 8,
        "pagos_atrasados": 2,
        "pagos_qr": 5,
        "pagos_normales": 5,
        "porcentaje_puntual": 80.0,
        "porcentaje_atrasado": 20.0,
        "porcentaje_qr": 50.0,
        "porcentaje_normal": 50.0
    },
    "id_persona": 1
}
```

---

#### 9. **obtenerEstadisticasPagosCompletas**
Obtiene estadísticas generales de todos los pagos del sistema.

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerEstadisticasPagosCompletas"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estadísticas completas de pagos obtenidas exitosamente",
    "data": {
        "total_pagos": 150,
        "total_pagado": 52500.00,
        "total_pagado_formateado": "52,500.00",
        "promedio_pago": 350.00,
        "promedio_pago_formateado": "350.00",
        "primer_pago": "2024-01-15",
        "primer_pago_formateado": "15/01/2024",
        "ultimo_pago": "2024-12-10",
        "ultimo_pago_formateado": "10/12/2024",
        "pagos_puntuales": 120,
        "pagos_atrasados": 30,
        "pagos_qr": 75,
        "pagos_normales": 75,
        "departamentos_con_pagos": 25,
        "personas_que_pagaron": 20,
        "porcentaje_puntual": 80.0,
        "porcentaje_atrasado": 20.0,
        "porcentaje_qr": 50.0,
        "porcentaje_normal": 50.0
    }
}
```

---

### SECCIÓN 4: CONCEPTOS

#### 10. **obtenerMisConceptos**
Obtiene todos los conceptos de una persona específica con información detallada.

**Campos requeridos:**
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerMisConceptos",
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Conceptos del usuario listados exitosamente",
    "data": [
        {
            "id_concepto": 1,
            "id_factura": 1,
            "concepto": "mantenimiento",
            "monto": 150.00,
            "id_origen": 1,
            "tipo_origen": "mantenimiento",
            "cantidad": 1,
            "descripcion": "Mantenimiento - Diciembre 2024",
            "fecha_creacion": "2024-12-01 10:00:00",
            "estado": "facturado",
            "fecha_emision": "2024-12-01",
            "fecha_vencimiento": "2024-12-15",
            "estado_factura": "pendiente",
            "departamento": "101",
            "piso": 1,
            "origen_nombre": null,
            "origen_fecha": null,
            "monto_formateado": "150.00",
            "subtotal_formateado": "150.00",
            "fecha_creacion_formateada": "01/12/2024",
            "fecha_creacion_hora": "10:00",
            "fecha_emision_formateada": "01/12/2024",
            "fecha_vencimiento_formateada": "15/12/2024",
            "icono": "fa-tools",
            "color": "secondary",
            "badge_class": "bg-info",
            "estado_texto": "Facturado",
            "origen_info": "Directo"
        }
    ],
    "id_persona": 1,
    "total": 1
}
```

**Tipos de conceptos soportados:**
- `agua` - Consumo de agua
- `luz` - Consumo de luz
- `gas` - Consumo de gas
- `mantenimiento` - Cargo de mantenimiento
- `reserva_area` - Reserva de área común
- `incidente` - Cobro por incidente
- `multa` - Multa aplicada

---

#### 11. **obtenerConceptosCompletos**
Obtiene todos los conceptos del sistema con información completa de personas y departamentos.

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerConceptosCompletos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Conceptos completos listados exitosamente",
    "data": [
        {
            "id_concepto": 1,
            "id_factura": 1,
            "id_persona": 1,
            "concepto": "mantenimiento",
            "monto": 150.00,
            "persona_completa": "Juan Pérez García",
            "departamento": "101",
            "piso": 1,
            "departamento_info": "D101-P1",
            "concepto_texto": "Mantenimiento",
            "monto_formateado": "150.00",
            "estado_texto": "Facturado",
            "origen_info": "Directo",
            "origen_detalle": "Sin origen específico"
        }
    ],
    "total": 1
}
```

---

#### 12. **obtenerEstadisticasMisConceptos**
Obtiene estadísticas de conceptos de una persona específica.

**Campos requeridos:**
- `id_persona` (number, obligatorio)

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerEstadisticasMisConceptos",
    "id_persona": 1
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estadísticas de conceptos del usuario obtenidas exitosamente",
    "data": {
        "total_conceptos": 25,
        "total_monto": 8750.00,
        "total_monto_formateado": "8,750.00",
        "promedio_monto": 350.00,
        "promedio_monto_formateado": "350.00",
        "primer_concepto": "2024-01-15",
        "primer_concepto_formateado": "15/01/2024",
        "ultimo_concepto": "2024-12-01",
        "ultimo_concepto_formateado": "01/12/2024",
        "conceptos_pendientes": 5,
        "conceptos_facturados": 18,
        "conceptos_cancelados": 2,
        "conceptos_sin_factura": 5,
        "conceptos_con_factura": 20,
        "porcentaje_pendientes": 20.0,
        "porcentaje_facturados": 72.0,
        "porcentaje_cancelados": 8.0,
        "porcentaje_sin_factura": 20.0,
        "porcentaje_con_factura": 80.0
    },
    "id_persona": 1
}
```

---

#### 13. **obtenerEstadisticasConceptosCompletos**
Obtiene estadísticas generales de todos los conceptos del sistema.

**Request:**
```json
POST /public/api/factura.php
Content-Type: application/json

{
    "action": "obtenerEstadisticasConceptosCompletos"
}
```

**Response (200):**
```json
{
    "status": 200,
    "message": "Estadísticas completas de conceptos obtenidas exitosamente",
    "data": {
        "total_conceptos": 500,
        "total_monto": 175000.00,
        "total_monto_formateado": "175,000.00",
        "promedio_monto": 350.00,
        "promedio_monto_formateado": "350.00",
        "conceptos_pendientes": 100,
        "conceptos_facturados": 380,
        "conceptos_cancelados": 20,
        "conceptos_sin_factura": 100,
        "conceptos_con_factura": 400,
        "personas_con_conceptos": 50,
        "departamentos_con_conceptos": 45,
        "conceptos_agua": 150,
        "conceptos_luz": 120,
        "conceptos_gas": 80,
        "conceptos_mantenimiento": 100,
        "conceptos_reserva": 30,
        "conceptos_incidente": 15,
        "conceptos_multa": 5,
        "origenes_reserva": 30,
        "origenes_consumo": 350,
        "origenes_incidente": 15,
        "origenes_directos": 105,
        "porcentaje_pendientes": 20.0,
        "porcentaje_facturados": 76.0,
        "porcentaje_cancelados": 4.0,
        "porcentaje_agua": 30.0,
        "porcentaje_luz": 24.0,
        "porcentaje_gas": 16.0,
        "porcentaje_mantenimiento": 20.0
    }
}
```

---

## 🔧 Configuración en Postman

### 1. Crear una nueva Collection
- Nombre: "API Factura"
- Base URL: `http://tu-dominio.com/public/api/factura.php`

### 2. Headers comunes
Para todas las peticiones POST, configurar:
```
Content-Type: application/json
```

### 3. Estructura de Request
- **Method:** POST
- **URL:** `http://tu-dominio.com/public/api/factura.php`
- **Headers:** 
  - `Content-Type: application/json`
- **Body:** Seleccionar `raw` y `JSON`, luego pegar el JSON correspondiente

---

## 📝 Ejemplos de Flujo Completo

### Flujo 1: Generación y pago de factura mensual
```
1. generarFacturas → Generar facturas para el mes
2. listarFacturas → Verificar facturas generadas
3. obtenerFacturaCompleta → Ver detalles de una factura
4. pagarFactura → Procesar el pago
5. obtenerMiHistorialPagos → Verificar pago registrado
6. obtenerEstadisticasMisPagos → Ver estadísticas del usuario
```

### Flujo 2: Consulta de conceptos y facturas
```
1. obtenerMisConceptos → Ver conceptos del usuario
2. obtenerMisFacturas → Ver facturas del usuario
3. obtenerEstadisticasMisConceptos → Estadísticas de conceptos
4. obtenerConceptosCompletos → Ver todos los conceptos (admin)
5. obtenerEstadisticasConceptosCompletos → Estadísticas generales
```

### Flujo 3: Análisis de pagos
```
1. obtenerHistorialPagosCompleto → Ver todos los pagos
2. obtenerEstadisticasPagosCompletas → Estadísticas generales
3. obtenerMiHistorialPagos → Historial personal
4. obtenerEstadisticasMisPagos → Estadísticas personales
```

---

## ⚠️ Códigos de Error Comunes

| Código | Significado | Causa |
|--------|-------------|-------|
| 400 | Bad Request | Campos faltantes, datos inválidos, factura ya pagada |
| 404 | Not Found | Factura no encontrada |
| 405 | Method Not Allowed | Método HTTP no permitido |
| 500 | Internal Server Error | Error en base de datos o procesamiento |

---

## 🔒 Validaciones Importantes

1. **ID Factura:** Debe ser un número entero positivo
2. **ID Persona:** Debe ser un número entero positivo
3. **Mes Facturación:** Formato YYYY-MM (ej: "2024-12")
4. **Estado Factura:** Solo puede pagarse si está en estado "pendiente" o "vencida"
5. **Datos Sensibles:** Se descifran automáticamente (nombres, apellidos, CI)

---

## 📊 Estructura de Base de Datos

La API interactúa con estas tablas:
- `factura` - Almacena las facturas
- `conceptos` - Conceptos que se facturan
- `historial_pago` - Historial de pagos
- `persona_paga_factura` - Relación entre personas y pagos
- `departamento` - Información de departamentos
- `tiene_departamento` - Relación persona-departamento
- `persona` - Información de personas (datos cifrados)

---

## 🔐 Seguridad y Cifrado

- Los datos sensibles (nombre, apellidos, CI) se almacenan cifrados en la base de datos
- La API descifra automáticamente estos datos al consultarlos
- Se usa AES-256-CBC para el cifrado/descifrado
- Las respuestas JSON incluyen los datos ya descifrados

---

## 🚀 Notas Adicionales

- Todos los endpoints POST requieren el campo `action`
- Los endpoints también están disponibles vía GET (usando query params)
- La API maneja CORS automáticamente
- Los errores se registran en el log del servidor
- Las transacciones se usan en operaciones críticas (pago de facturas)
- Los triggers de la base de datos actualizan automáticamente el estado de las facturas al pagar

---

## 📈 Estados de Factura

- `pendiente` - Factura generada, pendiente de pago
- `pagada` - Factura pagada completamente
- `vencida` - Factura vencida sin pagar
- `cancelada` - Factura cancelada

---

## 📈 Estados de Concepto

- `pendiente` - Concepto creado, no asignado a factura
- `facturado` - Concepto asignado a una factura
- `cancelado` - Concepto cancelado

---

## 🔄 Tipos de Origen de Conceptos

- `mantenimiento` - Generado desde cargos fijos
- `reserva` - Generado por reserva de área común
- `consumo` - Generado por consumo de servicios (agua, luz, gas)
- `incidente` - Generado por incidente reportado
- `null` - Concepto directo sin origen específico

