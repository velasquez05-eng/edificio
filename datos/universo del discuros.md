
## Universo del Discurso – Sistema de Edificio Inteligente

### 1. **Personas y roles**

* La base del sistema son las **personas**, que pueden ser **residentes, personal de mantenimiento, administración u otros roles**.
* Cada persona tiene datos sensibles como nombre, apellidos, cédula de identidad, teléfono y email, que están cifrados.
* Las personas están asociadas a un **rol**, que define sus permisos y responsabilidades dentro del sistema (`rol`).

### 2. **Acceso y seguridad**

* Cada persona tiene un **login**, con usuario, contraseña cifrada, verificación de cuenta y control de bloqueos temporales por intentos fallidos.
* Se registra un **historial de login** que almacena intentos exitosos y fallidos, fecha, hora e IP, para auditoría y seguridad.

### 3. **Áreas comunes y reservas**

* El edificio cuenta con **áreas comunes** (salón de eventos, gimnasio, etc.), cada una con capacidad, descripción y estado (`disponible`, `mantenimiento`, `ocupada`).
* Las **reservas de áreas comunes** son realizadas por personas, con fechas y horarios específicos.
* Cada reserva pasa por un **estado**: pendiente, confirmada o cancelada, permitiendo control por parte del personal administrativo.

### 4. **Departamentos y residencias**

* Cada **departamento** tiene un número, piso, metros cuadrados y estado (`ocupado`, `disponible`, `mantenimiento`).
* Un departamento puede estar asociado a varias personas (`tiene_departamento`) y viceversa.
* Las personas vinculadas a un departamento son consideradas **propietarios activos** mientras el estado esté activo.

### 5. **Incidentes y mantenimiento**

* Los **incidentes** ocurren dentro de los departamentos y son reportados por los residentes.
* Cada incidente tiene descripción, fecha de registro y estado (`pendiente`, `en_proceso`, `resuelto`, `cancelado`).
* Los incidentes pueden ser asignados a personal específico para su atención (`incidente_asignado`).
* Cada acción sobre un incidente se registra en el **historial de incidentes**, documentando quién realizó la acción, tipo de acción (asignación, inicio de atención, actualización, resolución, cancelación), estado anterior y nuevo, fecha y observaciones.

### 6. **Servicios y consumos**

* Los departamentos cuentan con **medidores** asociados a servicios (agua, luz, gas).
* Cada medidor tiene un código único, estado (`activo`, `mantenimiento`, `baja`, `corte`) y fecha de instalación.
* Los medidores registran el **consumo horario** mediante `lector_sensor_consumo`.
* Se generan **historiales de consumo** por periodos, almacenando consumo total entre fechas de inicio y fin.

### 7. **Facturación y pagos**

* Las **facturas** se generan por departamento, servicio y periodo de consumo.
* Cada factura contiene fecha de emisión, fecha de vencimiento, monto total calculado y estado (`pendiente`, `pagada`, `vencida`).
* Las facturas pueden ser pagadas por **una o varias personas** asociadas al departamento, registrando pagos parciales (`persona_paga_factura`).
* Cada pago se registra en el **historial de pagos**, incluyendo monto, fecha y observaciones, permitiendo auditoría completa.

### 8. **Alertas predictivas y notificaciones**

* Se generan **alertas predictivas** para departamentos en riesgo de corte de servicio (`riesgo_corte`) o ya en corte (`corte`).
* Cada alerta está asociada a un departamento y un servicio, e indica el número de facturas vencidas que provocan la alerta.
* Las **notificaciones a personas** (`notificacion_persona`) registran el envío de la alerta a cada propietario, su estado (`enviado`, `recibido`, `leído`), medio de envío (email, SMS, app) y observaciones.

### 9. **Flujos principales**

1. **Seguridad:** Personas → login → historial_login.
2. **Gestión de espacios:** Área común → reservas → control de estados.
3. **Gestión de departamentos:** Departamento ↔ personas → medidores → consumo → historial de consumo → factura → pagos → historial de pagos.
4. **Mantenimiento:** Incidentes → asignación a personal → historial_incidente.
5. **Alertas y notificaciones:** Facturas vencidas → alerta_predictiva → notificacion_persona.

---

Este universo del discurso describe **todas las entidades, relaciones y flujos de tu DB**, incluyendo: **seguridad, residencias, áreas comunes, incidentes, servicios, consumos, facturación y alertas**.

