<?php

class AgenteModelo
{
    private $db;
    private $geminiApiKey;
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent';

    public function __construct($db)
    {
        $this->db = $db;
        // API Key de Gemini proporcionada
        $this->geminiApiKey = 'AIzaSyDhGtEusdXekSCVOVtrzmsj_soaVmce29A';
    }

    /**
     * Obtener contexto del sistema según el rol del usuario
     */
    private function obtenerContextoSistema($id_rol, $id_persona = null)
    {
        $contexto = "Eres un asistente virtual experto del Sistema de Gestión de Edificio Inteligente (SEInt). ";
        $contexto .= "Tu función es ayudar a los usuarios a entender cómo usar el sistema y realizar diferentes procesos.\n\n";

        // Información general del sistema
        $contexto .= "## INFORMACIÓN DEL SISTEMA:\n";
        $contexto .= "- El sistema gestiona edificios residenciales\n";
        $contexto .= "- Maneja: personas, departamentos, áreas comunes, incidentes, facturas, reservas, servicios, comunicados\n";
        $contexto .= "- Los roles son: Administrador (id_rol=1), Residente (id_rol=2), Personal (id_rol=3)\n\n";

        // Contexto según rol
        switch ($id_rol) {
            case 1: // Administrador
                $contexto .= "## FUNCIONALIDADES PARA ADMINISTRADOR:\n\n";
                
                $contexto .= "### 👥 GESTIÓN DE PERSONAS:\n";
                $contexto .= "- Listar Personal: Menú 'Persona' → 'Listar Personal'\n";
                $contexto .= "- Listar Residentes: Menú 'Persona' → 'Listar Residente'\n";
                $contexto .= "- Registrar Persona: Menú 'Persona' → 'Registrar Persona'\n";
                $contexto .= "- Gestión de Roles: Menú 'Persona' → 'Listar Roles' o 'Crear Rol'\n\n";
                
                $contexto .= "### 🏠 GESTIÓN DE DEPARTAMENTOS:\n";
                $contexto .= "- Listar Departamentos: Menú 'Departamento' → 'Listar Departamentos'\n";
                $contexto .= "- Registrar Departamento: Menú 'Departamento' → 'Registrar Departamento'\n";
                $contexto .= "- Asignar Personas: Menú 'Departamento' → 'Asignar Departamento'\n\n";
                
                $contexto .= "### 📅 CÓMO APROBAR/RECHAZAR RESERVAS (Paso a paso):\n";
                $contexto .= "1. Ve al menú lateral → 'Área Común' → 'Listar Reservas'\n";
                $contexto .= "2. O puedes ir a 'Listar Áreas' y luego seleccionar 'Ver Reservas' de un área específica\n";
                $contexto .= "3. Encontrarás reservas con estado 'pendiente'\n";
                $contexto .= "4. Selecciona la reserva que deseas aprobar o rechazar\n";
                $contexto .= "5. Haz clic en 'Aprobar' o 'Rechazar' según corresponda\n";
                $contexto .= "6. El sistema cambiará el estado automáticamente\n";
                $contexto .= "7. El residente será notificado del cambio de estado\n\n";
                
                $contexto .= "### ⚠️ CÓMO ASIGNAR PERSONAL A UN INCIDENTE (Paso a paso):\n";
                $contexto .= "1. Ve al menú lateral → 'Incidentes' → 'Listar Incidentes'\n";
                $contexto .= "2. Encuentra el incidente con estado 'pendiente' que necesitas asignar\n";
                $contexto .= "3. Haz clic en el incidente o en el botón 'Asignar Personal'\n";
                $contexto .= "4. Selecciona el personal de mantenimiento disponible de la lista\n";
                $contexto .= "5. Proporciona observaciones sobre la asignación (OPCIONAL pero recomendado)\n";
                $contexto .= "6. Haz clic en 'Asignar' o 'Confirmar'\n";
                $contexto .= "7. El incidente se asignará y el personal será notificado automáticamente\n";
                $contexto .= "8. Para REASIGNAR: Selecciona el incidente ya asignado y elige 'Reasignar Personal'\n";
                $contexto .= "9. Para RESOLVER directamente: Selecciona el incidente y elige 'Resolver' (necesitas proporcionar observaciones y costo externo si aplica)\n\n";
                
                $contexto .= "### 💰 CÓMO GENERAR FACTURAS PARA UN MES (Paso a paso):\n";
                $contexto .= "1. Ve al menú lateral → 'Facturas' → 'Listar Facturas'\n";
                $contexto .= "2. Busca la opción 'Generar Facturas' o 'Crear Facturas' en la página\n";
                $contexto .= "3. Se abrirá un formulario donde debes seleccionar el mes y año\n";
                $contexto .= "4. Ingresa el mes en formato YYYY-MM (ejemplo: 2025-11 para noviembre 2025)\n";
                $contexto .= "5. Haz clic en 'Generar' o 'Confirmar'\n";
                $contexto .= "6. El sistema generará facturas para todos los departamentos activos\n";
                $contexto .= "7. Las facturas incluirán consumos de servicios y cargos fijos del mes seleccionado\n";
                $contexto .= "8. Cada factura tendrá estado 'pendiente' inicialmente\n";
                $contexto .= "9. Los residentes serán notificados de sus nuevas facturas automáticamente\n";
                $contexto .= "NOTA: No se pueden generar facturas duplicadas para el mismo mes y departamento\n\n";
                
                $contexto .= "### 📊 OTROS ACCESOS ADMINISTRATIVOS:\n";
                $contexto .= "- Listar Facturas: Menú 'Facturas' → 'Listar Facturas'\n";
                $contexto .= "- Historial de Pagos Completo: Menú 'Facturas' → 'Historial Pagos'\n";
                $contexto .= "- Conceptos Completos: Menú 'Facturas' → 'Conceptos'\n";
                $contexto .= "- Listar Áreas: Menú 'Área Común' → 'Listar Áreas'\n";
                $contexto .= "- Registrar Área: Menú 'Área Común' → 'Registrar Área'\n";
                $contexto .= "- Registrar Comunicado: Menú 'Comunicado' → 'Registrar Comunicado'\n";
                $contexto .= "- Gestión de Servicios: Menú 'Servicios' → 'Listar Servicios'\n";
                $contexto .= "- Gestión de Cargos Fijos: Menú 'Cargos Fijos' → 'Listar Cargos Fijos'\n";
                $contexto .= "- Generar Planilla: Menú 'Facturas' → 'Generar planillas'\n\n";
                break;

            case 2: // Residente
                $contexto .= "## FUNCIONALIDADES PARA RESIDENTE:\n\n";
                
                $contexto .= "### 📋 CÓMO REPORTAR UN INCIDENTE (Paso a paso):\n";
                $contexto .= "1. Accede al menú lateral y busca la opción 'Incidentes'\n";
                $contexto .= "2. Haz clic en 'Registrar Incidente'\n";
                $contexto .= "3. En el formulario que aparece, completa los siguientes campos:\n";
                $contexto .= "   - Descripción (OBLIGATORIO): Describe brevemente el problema\n";
                $contexto .= "   - Descripción detallada (OPCIONAL): Proporciona más información si es necesario\n";
                $contexto .= "4. El sistema automáticamente asignará el incidente a tu departamento\n";
                $contexto .= "5. Haz clic en 'Registrar' o 'Guardar'\n";
                $contexto .= "6. El incidente quedará en estado 'pendiente' hasta que el personal lo asigne\n";
                $contexto .= "7. Para ver tus incidentes después, ve al menú 'Incidentes' → 'Mis Incidentes'\n";
                $contexto .= "8. Para cancelar un incidente, ve a 'Mis Incidentes', selecciona el incidente y elige cancelar (necesitas proporcionar un motivo)\n\n";
                
                $contexto .= "### 📅 CÓMO HACER UNA RESERVA DE ÁREA COMÚN (Paso a paso):\n";
                $contexto .= "1. Accede al menú lateral y busca la opción 'Área Común'\n";
                $contexto .= "2. Haz clic en 'Reservar Área'\n";
                $contexto .= "3. En el formulario de reserva que aparece, deberás completar:\n";
                $contexto .= "   - Área común: Selecciona el área que deseas reservar (salón, gimnasio, piscina, etc.)\n";
                $contexto .= "   - Fecha de reserva: Selecciona una fecha FUTURA (no se permiten fechas pasadas)\n";
                $contexto .= "   - Hora de inicio: Selecciona la hora en que iniciará tu reserva (formato HH:MM, ejemplo: 14:00)\n";
                $contexto .= "   - Hora de fin: Selecciona la hora en que terminará (debe ser MAYOR que la hora de inicio, ejemplo: 16:00)\n";
                $contexto .= "   - Motivo: Describe para qué usarás el área (MÍNIMO 10 caracteres)\n";
                $contexto .= "4. Verifica que el horario esté disponible en el calendario (aparecerá marcado en gris si ya está ocupado)\n";
                $contexto .= "5. Haz clic en 'Reservar' o 'Confirmar'\n";
                $contexto .= "6. Tu reserva quedará en estado 'pendiente' hasta que el administrador la apruebe\n";
                $contexto .= "7. Para ver tus reservas después, ve al menú 'Área Común' → 'Listar Reservas'\n";
                $contexto .= "8. Para modificar una reserva: Ve a 'Listar Reservas', selecciona la reserva y elige 'Modificar'\n";
                $contexto .= "9. Para cancelar una reserva: Ve a 'Listar Reservas', selecciona la reserva y elige 'Cancelar'\n";
                $contexto .= "NOTA: No puedes hacer reservas para fechas pasadas ni con horarios inválidos\n\n";
                
                $contexto .= "### 💰 CÓMO VER MIS FACTURAS (Paso a paso):\n";
                $contexto .= "1. Accede al menú lateral y busca la opción 'Facturas'\n";
                $contexto .= "2. Haz clic en 'Mis Facturas'\n";
                $contexto .= "3. Verás una lista con todas tus facturas que incluye:\n";
                $contexto .= "   - Número de factura\n";
                $contexto .= "   - Fecha de emisión\n";
                $contexto .= "   - Fecha de vencimiento\n";
                $contexto .= "   - Monto total\n";
                $contexto .= "   - Estado: pendiente, pagada o vencida\n";
                $contexto .= "4. Para ver detalles de una factura específica, haz clic en ella\n";
                $contexto .= "5. En la vista de detalle podrás ver todos los conceptos (servicios, cargos fijos, etc.)\n";
                $contexto .= "6. También puedes descargar la factura en PDF haciendo clic en el botón 'Descargar'\n\n";
                
                $contexto .= "### 💳 CÓMO VER MI HISTORIAL DE PAGOS:\n";
                $contexto .= "1. Ve al menú lateral → 'Facturas' → 'Mi Historial de Pagos'\n";
                $contexto .= "2. Verás todos los pagos realizados con fecha, monto y factura asociada\n\n";
                
                $contexto .= "### 📊 CÓMO VER MIS CONCEPTOS:\n";
                $contexto .= "1. Ve al menú lateral → 'Facturas' → 'Mis Conceptos'\n";
                $contexto .= "2. Verás todos los conceptos asociados a tu departamento (servicios, mantenimiento, etc.)\n\n";
                
                $contexto .= "### 📰 CÓMO VER COMUNICADOS:\n";
                $contexto .= "1. Ve al menú lateral → 'Comunicado' → 'Comunicados Publicados'\n";
                $contexto .= "2. Verás todos los comunicados oficiales del edificio ordenados por fecha\n";
                $contexto .= "3. Haz clic en un comunicado para ver su contenido completo\n\n";
                
                $contexto .= "### 👤 CÓMO VER MI PERFIL:\n";
                $contexto .= "1. Haz clic en tu nombre o avatar en la esquina superior derecha del header\n";
                $contexto .= "2. Selecciona 'Mi Perfil' del menú desplegable\n";
                $contexto .= "3. Podrás ver y editar tu información personal\n\n";
                
                $contexto .= "### 📋 OTROS ACCESOS RÁPIDOS:\n";
                $contexto .= "- Ver Mis Incidentes: Menú 'Incidentes' → 'Mis Incidentes'\n";
                $contexto .= "- Ver Mis Reservas: Menú 'Área Común' → 'Listar Reservas'\n";
                $contexto .= "- Dashboard Principal: Haz clic en el logo 'SEInt' del header\n\n";
                break;

            case 3: // Personal
                $contexto .= "## FUNCIONALIDADES PARA PERSONAL:\n\n";
                
                $contexto .= "### 🔧 CÓMO ATENDER UN INCIDENTE ASIGNADO (Paso a paso):\n";
                $contexto .= "1. Accede al menú lateral y busca la opción 'Incidentes'\n";
                $contexto .= "2. Haz clic en 'Atender Incidente'\n";
                $contexto .= "3. Verás una lista de todos los incidentes asignados a ti\n";
                $contexto .= "4. Para INICIAR la atención de un incidente (primera vez):\n";
                $contexto .= "   - Selecciona el incidente que deseas atender\n";
                $contexto .= "   - Haz clic en 'Iniciar Atención'\n";
                $contexto .= "   - Debes proporcionar observaciones (OBLIGATORIO) sobre lo que harás\n";
                $contexto .= "   - El estado cambiará de 'pendiente' a 'en_proceso'\n";
                $contexto .= "5. Para ACTUALIZAR el progreso de un incidente en proceso:\n";
                $contexto .= "   - Ve a 'Incidentes' → 'Atender Incidente'\n";
                $contexto .= "   - Selecciona el incidente que estás atendiendo\n";
                $contexto .= "   - Haz clic en 'Actualizar Progreso'\n";
                $contexto .= "   - Proporciona observaciones sobre el avance\n";
                $contexto .= "6. Para RESOLVER un incidente:\n";
                $contexto .= "   - Ve a 'Incidentes' → 'Atender Incidente'\n";
                $contexto .= "   - Selecciona el incidente que ya completaste\n";
                $contexto .= "   - Haz clic en 'Resolver'\n";
                $contexto .= "   - Proporciona observaciones finales (OBLIGATORIO)\n";
                $contexto .= "   - Si hubo costo externo, indícalo (OPCIONAL)\n";
                $contexto .= "   - El estado cambiará a 'resuelto'\n";
                $contexto .= "7. Para SOLICITAR REASIGNACIÓN de un incidente:\n";
                $contexto .= "   - Ve a 'Incidentes' → 'Atender Incidente'\n";
                $contexto .= "   - Selecciona el incidente que no puedes atender\n";
                $contexto .= "   - Haz clic en 'Solicitar Reasignación'\n";
                $contexto .= "   - Proporciona observaciones y comentario de reasignación\n";
                $contexto .= "   - El administrador podrá reasignarlo a otro personal\n\n";
                
                $contexto .= "### 💼 CÓMO VER MI PLANILLA:\n";
                $contexto .= "1. Ve al menú lateral → 'Facturas' → 'Mis planillas'\n";
                $contexto .= "2. Verás tu planilla de empleado con todos los detalles\n\n";
                
                $contexto .= "### 📰 CÓMO VER COMUNICADOS:\n";
                $contexto .= "1. Ve al menú lateral → 'Comunicado' → 'Comunicados Publicados'\n";
                $contexto .= "2. Verás todos los comunicados oficiales del edificio\n\n";
                break;
        }

        $contexto .= "\n## INSTRUCCIONES IMPORTANTES PARA EL AGENTE:\n";
        $contexto .= "- Cuando el usuario pregunte '¿Cómo hacer X?' o 'Cómo hacer X', proporciona los pasos NUMERADOS y detallados\n";
        $contexto .= "- NUNCA muestres rutas técnicas como '../controlador/NombreControlador.php?action=accion' en tus respuestas\n";
        $contexto .= "- Solo explica dónde encontrar cada opción en el menú lateral del sistema usando lenguaje natural\n";
        $contexto .= "- Describe la navegación de forma clara: 'Menú lateral → Nombre del Menú → Opción específica'\n";
        $contexto .= "- Menciona campos OBLIGATORIOS y campos OPCIONALES claramente\n";
        $contexto .= "- Si hay validaciones importantes (fechas futuras, cantidad mínima de caracteres, etc.), menciónalas\n";
        $contexto .= "- Proporciona ejemplos cuando sea útil (ejemplo: formato de fecha, horarios válidos)\n";
        $contexto .= "- Explica qué pasará después de completar cada acción (estados, confirmaciones, etc.)\n";
        $contexto .= "- Sé amigable, claro, conciso y usa emojis cuando sea apropiado para hacer la explicación más visual\n";
        $contexto .= "- Si el usuario pregunta algo fuera del sistema, explica amablemente que solo puedes ayudar con el sistema SEInt\n";
        $contexto .= "- Cuando el usuario pregunte sobre un proceso específico (como 'cómo hacer una reserva'), proporciona TODOS los pasos desde el inicio hasta el final\n";
        $contexto .= "- Si mencionas navegación en el menú, sé específico sobre la ubicación (ejemplo: 'Menú lateral → Área Común → Reservar Área')\n";
        $contexto .= "- Las rutas técnicas son solo para tu conocimiento interno, NO las incluyas en las respuestas al usuario\n";
        $contexto .= "- Habla de forma natural como si estuvieras guiando a alguien paso a paso en la interfaz\n\n";

        return $contexto;
    }

    /**
     * Detectar intención del usuario y obtener acción correspondiente
     */
    private function detectarIntencion($mensaje, $id_rol)
    {
        $mensajeLower = strtolower($mensaje);
        $acciones = [];

        // Mapeo de intenciones a acciones según el rol
        if ($id_rol == 2) { // Residente
            if (preg_match('/\b(ver|mostrar|consultar|revisar|mirar).*factura/i', $mensajeLower)) {
                $acciones[] = ['action' => 'verMisFacturas', 'url' => '../controlador/FacturaControlador.php?action=verMisFacturas'];
            }
            if (preg_match('/\b(ver|mostrar|consultar).*historial.*pago/i', $mensajeLower)) {
                $acciones[] = ['action' => 'verMiHistorialPagos', 'url' => '../controlador/FacturaControlador.php?action=verMiHistorialPagos'];
            }
            if (preg_match('/\b(ver|mostrar|consultar).*concepto/i', $mensajeLower)) {
                $acciones[] = ['action' => 'misConceptos', 'url' => '../controlador/FacturaControlador.php?action=misConceptos'];
            }
            if (preg_match('/\b(hacer|crear|realizar|solicitar).*reserva/i', $mensajeLower)) {
                $acciones[] = ['action' => 'formularioReservaArea', 'url' => '../controlador/AreaComunControlador.php?action=formularioReservaArea'];
            }
            if (preg_match('/\b(ver|mostrar|consultar|listar).*reserva/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarReservas', 'url' => '../controlador/AreaComunControlador.php?action=listarReservas'];
            }
            if (preg_match('/\b(reportar|registrar|crear|abrir|solicitar).*incidente/i', $mensajeLower)) {
                $acciones[] = ['action' => 'formularioIncidente', 'url' => '../controlador/IncidenteControlador.php?action=formularioIncidente'];
            }
            if (preg_match('/\b(ver|mostrar|consultar).*incidente/i', $mensajeLower)) {
                $acciones[] = ['action' => 'verMisIncidentes', 'url' => '../controlador/IncidenteControlador.php?action=verMisIncidentes'];
            }
            if (preg_match('/\b(ver|mostrar|consultar|leer).*comunicado/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarPublicados', 'url' => '../controlador/ComunicadoControlador.php?action=listarPublicados'];
            }
            if (preg_match('/\b(ver|mostrar|editar|actualizar).*perfil/i', $mensajeLower)) {
                $acciones[] = ['action' => 'verMiPerfil', 'url' => '../controlador/PersonaControlador.php?action=verMiPerfil'];
            }
        } elseif ($id_rol == 1) { // Administrador
            if (preg_match('/\b(ver|mostrar|listar).*factura/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarFacturas', 'url' => '../controlador/FacturaControlador.php?action=listarFacturas'];
            }
            if (preg_match('/\b(generar|crear).*factura/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarFacturas', 'url' => '../controlador/FacturaControlador.php?action=listarFacturas'];
            }
            if (preg_match('/\b(ver|mostrar|listar).*incidente/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarIncidentes', 'url' => '../controlador/IncidenteControlador.php?action=listarIncidentes'];
            }
            if (preg_match('/\b(asignar|reasignar).*personal.*incidente/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarIncidentes', 'url' => '../controlador/IncidenteControlador.php?action=listarIncidentes'];
            }
            if (preg_match('/\b(aprobar|rechazar|ver|listar).*reserva/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarReservas', 'url' => '../controlador/AreaComunControlador.php?action=listarReservas'];
            }
            if (preg_match('/\b(registrar|crear|agregar).*persona/i', $mensajeLower)) {
                $acciones[] = ['action' => 'formularioPersona', 'url' => '../controlador/PersonaControlador.php?action=formularioPersona'];
            }
            if (preg_match('/\b(listar|ver).*persona|personal|residente/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarPersonal', 'url' => '../controlador/PersonaControlador.php?action=listarPersonal'];
            }
            if (preg_match('/\b(crear|registrar|agregar).*departamento/i', $mensajeLower)) {
                $acciones[] = ['action' => 'formularioDepartamento', 'url' => '../controlador/DepartamentoControlador.php?action=formularioDepartamento'];
            }
            if (preg_match('/\b(crear|registrar|agregar).*comunicado/i', $mensajeLower)) {
                $acciones[] = ['action' => 'formularioComunicado', 'url' => '../controlador/ComunicadoControlador.php?action=formularioComunicado'];
            }
        } elseif ($id_rol == 3) { // Personal
            if (preg_match('/\b(ver|mostrar|atender|listar).*incidente/i', $mensajeLower)) {
                $acciones[] = ['action' => 'verIncidentesAsignados', 'url' => '../controlador/IncidenteControlador.php?action=verIncidentesAsignados'];
            }
            if (preg_match('/\b(ver|mostrar|consultar).*planilla/i', $mensajeLower)) {
                $acciones[] = ['action' => 'verMiPlanilla', 'url' => '../controlador/PlanillaControlador.php?action=verMiPlanilla'];
            }
            if (preg_match('/\b(ver|mostrar|consultar|leer).*comunicado/i', $mensajeLower)) {
                $acciones[] = ['action' => 'listarPublicados', 'url' => '../controlador/ComunicadoControlador.php?action=listarPublicados'];
            }
        }

        return !empty($acciones) ? $acciones[0] : null;
    }

    /**
     * Enviar mensaje a Gemini AI
     */
    public function enviarMensaje($mensaje, $id_rol, $id_persona = null, $historial = [])
    {
        try {
            // Detectar intención y obtener acción
            $intencion = $this->detectarIntencion($mensaje, $id_rol);
            
            // Obtener contexto del sistema
            $contexto = $this->obtenerContextoSistema($id_rol, $id_persona);

            // Construir el prompt completo
            $contenidoCompleto = $contexto . "\n\n## PREGUNTA DEL USUARIO:\n" . $mensaje;
            
            // Si hay una intención detectada, agregar instrucción especial
            if ($intencion) {
                $contenidoCompleto .= "\n\nIMPORTANTE: Al final de tu respuesta, si el usuario está pidiendo abrir o ver una vista específica, incluye esta acción en formato especial: [ACCION: " . $intencion['action'] . "|URL: " . $intencion['url'] . "]";
            }

            // Construir el historial de conversación
            $contents = [];
            
            // Agregar mensajes del historial si existen
            if (!empty($historial) && is_array($historial)) {
                foreach ($historial as $item) {
                    if (isset($item['role']) && isset($item['parts']) && is_array($item['parts'])) {
                        $contents[] = [
                            'role' => $item['role'],
                            'parts' => $item['parts']
                        ];
                    }
                }
            }

            // Agregar el mensaje actual del usuario
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => $contenidoCompleto
                    ]
                ]
            ];

            // Preparar la solicitud
            $data = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ];

            // Hacer la solicitud a Gemini
            $url = $this->geminiApiUrl . '?key=' . $this->geminiApiKey;
            
            $jsonData = json_encode($data);
            
            // Log para depuración
            error_log("Agente - URL: " . str_replace($this->geminiApiKey, 'API_KEY_HIDDEN', $url));
            error_log("Agente - Data length: " . strlen($jsonData) . " bytes");
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);
            curl_close($ch);

            // Manejar errores de cURL
            if ($curlErrno !== 0) {
                error_log("Error cURL en Agente: [{$curlErrno}] {$curlError}");
                throw new Exception("Error de conexión: " . ($curlError ?: "Error desconocido de cURL"));
            }

            // Manejar errores HTTP
            if ($httpCode !== 200) {
                $errorMsg = "Error HTTP {$httpCode}";
                if ($response) {
                    $errorResponse = json_decode($response, true);
                    if (isset($errorResponse['error']['message'])) {
                        $errorMsg .= ": " . $errorResponse['error']['message'];
                    } else {
                        $errorMsg .= ": " . substr($response, 0, 200);
                    }
                }
                error_log("Error de Gemini API: {$errorMsg}");
                error_log("Respuesta completa: " . substr($response, 0, 500));
                throw new Exception($errorMsg);
            }

            // Verificar que hay respuesta
            if (empty($response)) {
                throw new Exception("La API de Gemini devolvió una respuesta vacía");
            }

            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Error JSON decode: " . json_last_error_msg() . " - Response: " . substr($response, 0, 200));
                throw new Exception("Error al decodificar respuesta JSON: " . json_last_error_msg());
            }

            // Verificar si hay error en la respuesta
            if (isset($responseData['error'])) {
                $errorMsg = $responseData['error']['message'] ?? 'Error desconocido de la API';
                error_log("Error en respuesta Gemini: " . json_encode($responseData['error']));
                throw new Exception($errorMsg);
            }

            // Extraer la respuesta del texto generado
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $respuestaTexto = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
                
                // Extraer acción si está presente en el formato [ACCION: nombre|URL: url]
                $accionDetectada = null;
                $urlDetectada = null;
                
                if (preg_match('/\[ACCION:\s*([^\|]+)\|URL:\s*([^\]]+)\]/', $respuestaTexto, $matches)) {
                    $accionDetectada = trim($matches[1]);
                    $urlDetectada = trim($matches[2]);
                    // Remover el marcador de acción de la respuesta
                    $respuestaTexto = preg_replace('/\s*\[ACCION:[^\]]+\]/', '', $respuestaTexto);
                    $respuestaTexto = trim($respuestaTexto);
                } else {
                    // Si no se detectó en la respuesta, usar la intención detectada antes
                    if ($intencion) {
                        $accionDetectada = $intencion['action'];
                        $urlDetectada = $intencion['url'];
                    }
                }
                
                $resultado = [
                    'success' => true,
                    'respuesta' => $respuestaTexto,
                ];
                
                // Agregar acción si se detectó
                if ($accionDetectada && $urlDetectada) {
                    $resultado['accion'] = $accionDetectada;
                    $resultado['url'] = $urlDetectada;
                }
                
                return $resultado;
            } else {
                // Log completo de la respuesta para debugging
                error_log("Respuesta inesperada de Gemini - Estructura: " . json_encode($responseData));
                
                // Intentar extraer mensaje de error si existe
                if (isset($responseData['candidates'][0]['finishReason'])) {
                    $finishReason = $responseData['candidates'][0]['finishReason'];
                    if ($finishReason !== 'STOP') {
                        throw new Exception("La respuesta fue detenida por: " . $finishReason);
                    }
                }
                
                throw new Exception("La API no devolvió texto en el formato esperado");
            }

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            error_log("Error completo en enviarMensaje: " . $errorMessage);
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Proporcionar mensaje más útil según el tipo de error
            $mensajeUsuario = "Lo siento, hubo un error al procesar tu mensaje. ";
            
            if (strpos($errorMessage, 'conexión') !== false || strpos($errorMessage, 'cURL') !== false) {
                $mensajeUsuario .= "Por favor verifica tu conexión a internet e intenta nuevamente.";
            } elseif (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
                $mensajeUsuario .= "Error de autenticación. Por favor contacta al administrador.";
            } elseif (strpos($errorMessage, '429') !== false) {
                $mensajeUsuario .= "Demasiadas solicitudes. Por favor espera un momento e intenta nuevamente.";
            } elseif (strpos($errorMessage, '500') !== false || strpos($errorMessage, '503') !== false) {
                $mensajeUsuario .= "El servicio está temporalmente no disponible. Por favor intenta más tarde.";
            } else {
                $mensajeUsuario .= "Por favor, intenta nuevamente.";
            }
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'respuesta' => $mensajeUsuario
            ];
        }
    }

    /**
     * Formatear historial de conversación para Gemini
     */
    public function formatearHistorial($historial)
    {
        $formateado = [];
        foreach ($historial as $mensaje) {
            if (isset($mensaje['mensaje']) && isset($mensaje['respuesta'])) {
                $formateado[] = [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $mensaje['mensaje']
                        ]
                    ]
                ];
                $formateado[] = [
                    'role' => 'model',
                    'parts' => [
                        [
                            'text' => $mensaje['respuesta']
                        ]
                    ]
                ];
            }
        }
        return $formateado;
    }
}

