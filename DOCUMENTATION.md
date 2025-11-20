# Estructura del Proyecto

Este documento explica la estructura del sistema, cómo funciona la API para la aplicación móvil y cómo se integra el Agente de Inteligencia Artificial.

---

## 1. La Estructura: Los Cimientos del Edificio

Nuestro sistema está organizado siguiendo una lógica llamada **MVC (Modelo-Vista-Controlador)**. Es una forma de mantener el orden: separamos los datos, la lógica y lo que ve el usuario.

### public/: Donde ocurre la magia
Esta es la carpeta más importante. Aquí vive el código que hace que la aplicación funcione día a día.

*   **controlador/ (El Cerebro):**
    Aquí es donde se toman las decisiones. Cuando un usuario intenta iniciar sesión o registrar un incidente, el "Controlador" es el primero en enterarse. Él verifica si tienes permiso, revisa los datos que enviaste y decide qué hacer.
    *   *Ejemplo:* `LoginControlador.php` decide si tu contraseña es correcta.

*   **modelo/ (La Memoria):**
    Los controladores son listos, pero no tienen memoria. Para eso están los "Modelos". Ellos son los únicos que tienen la llave para hablar con la Base de Datos. Si el Controlador necesita saber quién vive en el apartamento 501, se lo pide al Modelo.
    *   *Ejemplo:* `IncidenteModelo.php` sabe cómo guardar, leer o borrar incidentes en la base de datos.

*   **vista/ (La Interfaz):**
    Aquí están los archivos que crean las páginas web que ves en tu navegador. No toman decisiones ni tocan la base de datos; solo se encargan de mostrar la información de forma ordenada.
    *   *Ejemplo:* `DashboardResidenteVista.php` es la pantalla principal que ve un vecino al entrar.

*   **api/ (La Ventanilla de Atención):**
    Imagina que la aplicación móvil es un mensajero que viene a pedir información. La carpeta `api/` es la ventanilla especial para atender a ese mensajero. Aquí entregamos los datos en formato **JSON** (texto puro), sin adornos visuales, perfecto para que la app móvil los entienda.

### includes/: Las Herramientas Compartidas
Aquí guardamos cosas que se usan en todas partes para no repetir trabajo.
*   **css/, js/, img/:** Los estilos, scripts y logotipos que embellecen todas las páginas.
*   **header.php y footer.php:** El encabezado y pie de página que ves repetirse en cada pantalla.
*   **Librerías:** Herramientas externas como `phpmailer` (para enviar correos) o `tcpdf` (para crear recibos en PDF).

### config/: El Cuarto de Máquinas
Aquí están los secretos del sistema.
*   **database.php:** Contiene las contraseñas y direcciones para conectarse a la base de datos. ¡Es un archivo muy sensible!

---

## 2. Conectando tu App Móvil (API)

Si estás desarrollando la aplicación móvil, esta sección es para ti. Hemos creado una forma sencilla para que tu app "hable" con nuestro sistema.

### ¿Cómo funciona?
Piensa en la API como un camarero. Tú (la app) le pides algo (un "pedido") y él te trae la respuesta.

1.  **¿A dónde pedir?**
    Todos los pedidos se hacen a los archivos dentro de `public/api/`. Por ejemplo, si quieres ver incidentes, vas a `public/api/incidente.php`.

2.  **¿Cómo pedir?**
    Usamos dos formas básicas:
    *   **GET (Para ver):** "Oye, muéstrame la lista de incidentes".
        *   *Tu pedido:* `.../incidente.php?action=listarIncidentes`
    *   **POST (Para hacer):** "Toma estos datos y crea un nuevo reporte".
        *   *Tu pedido:* Envías un paquete JSON con los datos.

### Un Ejemplo Real: Reportar un Daño
Digamos que un vecino quiere reportar una lámpara rota desde su celular.

**Tu App envía esto (POST):**
```json
{
    "action": "crearIncidente",
    "id_departamento": 101,
    "id_residente": 5,
    "descripcion": "La lámpara del pasillo no prende"
}
```

**El Sistema responde esto:**
```json
{
    "status": 201,
    "message": "¡Listo! Incidente registrado con éxito.",
    "data": { "id_incidente": 42 }
}
```
¡Y listo! Así de fácil se comunican.

---

## 3. El Agente Inteligente (Tu Asistente Virtual)

Hemos contratado a un conserje digital muy listo (impulsado por Google Gemini) para ayudar a los usuarios las 24 horas.

### ¿Quién es y qué hace?
Es un chat que vive en la esquina de la pantalla. Los usuarios pueden preguntarle cosas como *"¿Cómo reservo el salón social?"* o *"¿Tengo pagos pendientes?"*.

### ¿Cómo funciona por dentro?
Es un trabajo en equipo:
1.  **El Usuario** escribe una pregunta en el chat.
2.  **AgenteControlador.php** recibe la pregunta, mira quién es el usuario (¿es administrador? ¿es residente?) y busca información útil para ayudarle.
3.  **AgenteModelo.php** toma esa información y se la envía a **Google Gemini** (la IA). Le dice: *"Eres un asistente útil del edificio, el usuario es un residente, contéstale esto..."*.
4.  **La IA** responde y nosotros le mostramos esa respuesta al usuario en el chat.

### Para la App Móvil
Si quieres poner este chat en tu app, solo tienes que enviar los mensajes a:
`public/controlador/AgenteControlador.php` con la acción `enviarMensaje`. ¡El sistema te devolverá la respuesta de la IA lista para mostrar!
