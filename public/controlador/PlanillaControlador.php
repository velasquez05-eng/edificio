<?php
/**
 * PlanillaControlador - Controlador para gestionar operaciones de planillas
 * Incluye generacion, listado y gestion de planillas de pago
 */
class PlanillaControlador {
    private $planillamodelo;
    private $personamodelo;

    /**
     * Constructor - Inicializa los modelos necesarios
     */
    public function __construct($db) {
        $this->planillamodelo = new PlanillaModelo($db);
        $this->personamodelo = new PersonaModelo($db);
    }

    // =============================================
    // METODOS DE VISUALIZACION
    // =============================================

    /**
     * Mostrar vista de planillas completas (Administrador)
     */
    public function listarPlanillasCompleto() {
        // Verificar permisos de administrador
        if (!$this->verificarPermisoAdministrador()) {
            $this->redirigirConError("No tiene permisos para acceder a esta sección");
        }

        // Obtener parámetros de filtro
        $mes = $_GET['mes'] ?? null;
        $anio = $_GET['anio'] ?? null;

        // Obtener datos
        $planillas = $this->planillamodelo->listarPlanillasCompleto($mes, $anio);
        $estadisticas = $mes && $anio ? $this->planillamodelo->obtenerEstadisticasPlanillas($mes, $anio) : [];
        $empleados = $this->planillamodelo->obtenerEmpleadosActivos();

        include '../vista/ListarPlanillasCompletoVista.php';
    }

    /**
     * Mostrar mi planilla (Empleado/Personal)
     */
    public function verMiPlanilla() {
        session_start();
        if (!isset($_SESSION['id_persona'])) {
            $this->redirigirConError("Debe iniciar sesión para ver su planilla");
        }

        $id_persona = $_SESSION['id_persona'];

        // Obtener parámetros de filtro
        $mes = $_GET['mes'] ?? null;
        $anio = $_GET['anio'] ?? null;

        // Obtener planillas filtradas
        $planillas = $this->planillamodelo->listarMiPlanilla($id_persona, $mes, $anio);

        include '../vista/VerMiPlanillaVista.php';
    }

    /**
     * Mostrar formulario de generación de planillas
     */
    public function formularioGenerarPlanilla() {
        // Verificar permisos de administrador
        if (!$this->verificarPermisoAdministrador()) {
            $this->redirigirConError("No tiene permisos para generar planillas");
        }

        $empleados = $this->planillamodelo->obtenerEmpleadosActivos();
        include '../vista/GenerarPlanillaVista.php';
    }

    // =============================================
    // METODOS DE GENERACION DE PLANILLAS
    // =============================================

    /**
     * Generar planilla completa para todos los empleados
     */
    public function generarPlanillaCompleta() {
        if ($_POST['action'] == "generarPlanillaCompleta") {
            try {
                // Verificar permisos de administrador
                if (!$this->verificarPermisoAdministrador()) {
                    throw new Exception("No tiene permisos para generar planillas");
                }

                // Validar campos requeridos
                $camposRequeridos = ['mes', 'anio', 'metodo_pago'];
                foreach($camposRequeridos as $campo) {
                    if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                        throw new Exception("El campo $campo es obligatorio");
                    }
                }

                // Sanitizar datos de entrada
                $mes = intval($_POST['mes']);
                $anio = intval($_POST['anio']);
                $metodo_pago = $_POST['metodo_pago'];
                $forzar = isset($_POST['forzar']) ? (bool)$_POST['forzar'] : false;

                // Validar rangos
                if ($mes < 1 || $mes > 12) {
                    throw new Exception("El mes debe estar entre 1 y 12");
                }

                if ($anio < 2020 || $anio > 2030) {
                    throw new Exception("El año debe estar entre 2020 y 2030");
                }

                // Validar método de pago
                $metodos_validos = ['transferencia', 'qr', 'efectivo', 'cheque'];
                if (!in_array($metodo_pago, $metodos_validos)) {
                    throw new Exception("Método de pago no válido");
                }

                // Generar planilla
                $resultado = $this->planillamodelo->generarPlanillaCompleta($mes, $anio, $metodo_pago, $forzar);

                if ($resultado) {
                    $this->redirigirConExito("Planilla completa generada exitosamente para " . date('F Y', mktime(0, 0, 0, $mes, 1, $anio)));
                } else {
                    throw new Exception("Error al generar la planilla completa");
                }

            } catch (Exception $e) {
                $this->redirigirConError("Error al generar planilla completa: " . $e->getMessage());
            }
        }
    }

    /**
     * Generar planilla personalizada para un empleado
     */
    public function generarPlanillaPersonalizada() {
        if ($_POST['action'] == "generarPlanillaPersonalizada") {
            try {
                // Verificar permisos de administrador
                if (!$this->verificarPermisoAdministrador()) {
                    throw new Exception("No tiene permisos para generar planillas");
                }

                // Validar campos requeridos
                $camposRequeridos = ['id_persona', 'mes', 'anio', 'dias_descuento', 'metodo_pago'];
                foreach($camposRequeridos as $campo) {
                    if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                        throw new Exception("El campo $campo es obligatorio");
                    }
                }

                // Sanitizar datos de entrada
                $id_persona = intval($_POST['id_persona']);
                $mes = intval($_POST['mes']);
                $anio = intval($_POST['anio']);
                $dias_descuento = floatval($_POST['dias_descuento']);
                $metodo_pago = $_POST['metodo_pago'];
                $forzar = isset($_POST['forzar']) ? (bool)$_POST['forzar'] : false;

                // Validar rangos
                if ($mes < 1 || $mes > 12) {
                    throw new Exception("El mes debe estar entre 1 y 12");
                }

                if ($anio < 2020 || $anio > 2030) {
                    throw new Exception("El año debe estar entre 2020 y 2030");
                }

                if ($dias_descuento < 0 || $dias_descuento > 30) {
                    throw new Exception("Los días de descuento deben estar entre 0 y 30");
                }

                // Validar método de pago
                $metodos_validos = ['transferencia', 'qr', 'efectivo', 'cheque'];
                if (!in_array($metodo_pago, $metodos_validos)) {
                    throw new Exception("Método de pago no válido");
                }

                // Verificar que la persona existe
                $persona = $this->personamodelo->obtenerPersonaPorId($id_persona);
                if (!$persona) {
                    throw new Exception("La persona especificada no existe");
                }

                // Generar planilla personalizada
                $resultado = $this->planillamodelo->generarPlanillaPersonalizada($id_persona, $mes, $anio, $dias_descuento, $metodo_pago, $forzar);

                if ($resultado) {
                    $nombre_completo = $persona['nombre'] . ' ' . $persona['apellido_paterno'] . ' ' . ($persona['apellido_materno'] ?? '');
                    $this->redirigirConExito("Planilla personalizada generada exitosamente para $nombre_completo");
                } else {
                    throw new Exception("Error al generar la planilla personalizada");
                }

            } catch (Exception $e) {
                $this->redirigirConError("Error al generar planilla personalizada: " . $e->getMessage());
            }
        }
    }

    /**
     * Generar planillas múltiples usando JSON
     */
    public function generarPlanillaMultiple() {
        if ($_POST['action'] == "generarPlanillaMultiple") {
            try {
                // Verificar permisos de administrador
                if (!$this->verificarPermisoAdministrador()) {
                    throw new Exception("No tiene permisos para generar planillas");
                }

                // Validar campos requeridos
                $camposRequeridos = ['mes', 'anio', 'json_descuentos', 'metodo_pago'];
                foreach($camposRequeridos as $campo) {
                    if(!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                        throw new Exception("El campo $campo es obligatorio");
                    }
                }

                // Sanitizar datos de entrada
                $mes = intval($_POST['mes']);
                $anio = intval($_POST['anio']);
                $json_descuentos = $_POST['json_descuentos'];
                $metodo_pago = $_POST['metodo_pago'];

                // Validar rangos
                if ($mes < 1 || $mes > 12) {
                    throw new Exception("El mes debe estar entre 1 y 12");
                }

                if ($anio < 2020 || $anio > 2030) {
                    throw new Exception("El año debe estar entre 2020 y 2030");
                }

                // Validar método de pago
                $metodos_validos = ['transferencia', 'qr', 'efectivo', 'cheque'];
                if (!in_array($metodo_pago, $metodos_validos)) {
                    throw new Exception("Método de pago no válido");
                }

                // Validar JSON
                $descuentos = json_decode($json_descuentos, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Formato JSON inválido");
                }

                if (empty($descuentos)) {
                    throw new Exception("El JSON de descuentos no puede estar vacío");
                }

                // Generar planillas múltiples
                $resultado = $this->planillamodelo->generarPlanillaMultiple($mes, $anio, $descuentos, $metodo_pago);

                if ($resultado) {
                    $total_personas = count($descuentos);
                    $this->redirigirConExito("Planillas múltiples generadas exitosamente para $total_personas empleados");
                } else {
                    throw new Exception("Error al generar las planillas múltiples");
                }

            } catch (Exception $e) {
                $this->redirigirConError("Error al generar planillas múltiples: " . $e->getMessage());
            }
        }
    }

    // =============================================
    // METODOS DE CONSULTA Y REPORTES (AJAX)
    // =============================================

    /**
     * Obtener estadísticas de planillas (AJAX)
     */
    public function obtenerEstadisticas() {
        try {
            // Verificar permisos de administrador
            if (!$this->verificarPermisoAdministrador()) {
                throw new Exception("No tiene permisos para acceder a esta información");
            }

            $mes = $_GET['mes'] ?? date('n');
            $anio = $_GET['anio'] ?? date('Y');

            $estadisticas = $this->planillamodelo->obtenerEstadisticasPlanillas($mes, $anio);
            $empleados = $this->planillamodelo->obtenerEmpleadosActivos();
            $existePlanilla = $this->planillamodelo->verificarPlanillaExistente($mes, $anio);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'estadisticas' => $estadisticas,
                'empleados' => $empleados,
                'existe_planilla' => $existePlanilla,
                'periodo' => sprintf("%04d-%02d", $anio, $mes)
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener empleados activos (AJAX)
     */
    public function obtenerEmpleadosActivos() {
        try {
            // Verificar permisos de administrador
            if (!$this->verificarPermisoAdministrador()) {
                throw new Exception("No tiene permisos para acceder a esta información");
            }

            $empleados = $this->planillamodelo->obtenerEmpleadosActivos();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $empleados
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener planillas completas (AJAX)
     */
    public function obtenerPlanillasCompletas() {
        try {
            // Verificar permisos de administrador
            if (!$this->verificarPermisoAdministrador()) {
                throw new Exception("No tiene permisos para acceder a esta información");
            }

            $mes = $_GET['mes'] ?? null;
            $anio = $_GET['anio'] ?? null;

            $planillas = $this->planillamodelo->listarPlanillasCompleto($mes, $anio);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $planillas
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener mi planilla (AJAX - para empleados)
     */
    public function obtenerMiPlanilla() {
        try {
            session_start();
            if (!isset($_SESSION['id_persona'])) {
                throw new Exception("Debe iniciar sesión para ver su planilla");
            }

            $id_persona = $_SESSION['id_persona'];
            $mes = $_GET['mes'] ?? null;
            $anio = $_GET['anio'] ?? null;

            $planillas = $this->planillamodelo->listarMiPlanilla($id_persona, $mes, $anio);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $planillas
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener resumen anual (AJAX)
     */
    public function obtenerResumenAnual() {
        try {
            session_start();
            if (!isset($_SESSION['id_persona'])) {
                throw new Exception("Debe iniciar sesión");
            }

            $anio = $_GET['anio'] ?? date('Y');
            $id_persona = $_SESSION['id_persona'];

            // Si es administrador, obtener resumen general
            if ($_SESSION['id_rol'] == 1) {
                $resumen = $this->planillamodelo->obtenerResumenAnual($anio);
            } else {
                $resumen = $this->planillamodelo->obtenerResumenAnual($anio, $id_persona);
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $resumen,
                'anio' => $anio
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // =============================================
    // METODOS AUXILIARES
    // =============================================

    /**
     * Verificar permisos de administrador
     */
    private function verificarPermisoAdministrador() {
        session_start();
        return isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1;
    }

    /**
     * Redirigir con mensaje de éxito
     */
    private function redirigirConExito($mensaje) {
        header('Location: PlanillaControlador.php?action=listarPlanillasCompleto&success=' . urlencode($mensaje));
        exit;
    }

    /**
     * Redirigir con mensaje de error
     */
    private function redirigirConError($mensaje) {
        header('Location: PlanillaControlador.php?action=listarPlanillasCompleto&error=' . urlencode($mensaje));
        exit;
    }

    /**
     * Descargar recibo en PDF
     */
    public function descargarReciboPDF() {
        try {
            // Iniciar sesión si no está iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['id_persona'])) {
                throw new Exception("Debe iniciar sesión para descargar el recibo");
            }

            if (!isset($_GET['id'])) {
                throw new Exception("ID de planilla no especificado");
            }

            $planillaId = $_GET['id'];
            $empleadoId = $_SESSION['id_persona'];

            // Obtener datos de la planilla
            $planilla = $this->planillamodelo->obtenerPlanillaPorId($planillaId, $empleadoId);

            if (!$planilla) {
                throw new Exception("Planilla no encontrada");
            }

            // Calcular valores adicionales
            $diasDescuento = 30 - $planilla['dias_trabajados'];
            $salarioDiario = $planilla['haber_basico'] / 30;
            $descuentoPorDias = $salarioDiario * $diasDescuento;
            $porcentajeGestora = 12.71;

            require_once '../../includes/tcpdf/tcpdf.php';

            $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
            $pdf->SetCreator('SEINT');
            $pdf->SetAuthor('SEINT');
            $pdf->SetTitle('Recibo de Pago - ' . date('F Y', strtotime($planilla['periodo'])));
            $pdf->SetMargins(15, 18, 15);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];

            $periodo = date('Y-m-d', strtotime($planilla['periodo']));
            $mes = $meses[date('n', strtotime($periodo))];
            $anio = date('Y', strtotime($periodo));

            $html = '
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #2c3e50;
            font-size: 9px;
            line-height: 1.4;
        }

        .header {
            border-bottom: 1.5px solid #1a5276;
            padding-bottom: 10px;
            margin-bottom: 8px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 8.5px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a5276;
            text-align: right;
            letter-spacing: 0.5px;
        }
        .invoice-number {
            font-size: 8.5px;
            color: #7f8c8d;
            text-align: right;
        }

        .client-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 18px;
        }
        .client-header {
            font-size: 10px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .client-name {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        .client-info {
            font-size: 8.5px;
            color: #34495e;
        }

        .period-box {
            background: #e8f4fd;
            border-left: 3px solid #3498db;
            padding: 7px 10px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 18px;
        }

        /* TABLA DE CONCEPTOS */
        .concepts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8px;
        }
        .concepts-table thead {
            background-color: #1a5276;
            color: #ffffff;
        }
        .concepts-table th {
            padding: 7px 4px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #154360;
            text-align: center;
            font-size: 8.3px;
            letter-spacing: 0.3px;
        }
        .concepts-table th:first-child {
            text-align: left;
            padding-left: 6px;
        }
        .concepts-table td {
            padding: 5px 4px;
            border-bottom: 0.4px solid #e5e8e8;
            vertical-align: top;
            color: #1c2833;
        }
        .concepts-table td:nth-child(2) {
            text-align: center;
        }
        .concepts-table td:nth-child(3) {
            text-align: right;
        }

        .concept-name {
            font-weight: bold;
            color: #17202a;
            font-size: 8.5px;
        }
        .concept-desc {
            color: #7f8c8d;
            font-size: 7.2px;
            margin-top: 1px;
            line-height: 1.2;
        }

        .total-box {
            width: 250px;
            margin-left: auto;
            padding: 10px;
            border-top: 2px solid #1a5276;
            text-align: right;
        }
        .total-label {
            font-size: 9px;
            font-weight: bold;
            color: #1a5276;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .total-amount {
            font-size: 11px;
            font-weight: bold;
            color: #145a32;
        }

        .calculation-box {
            background: #f9f9f9;
            border: 1px solid #e5e8e8;
            border-radius: 4px;
            padding: 8px;
            margin-top: 10px;
            font-size: 7.5px;
        }
        .calculation-item {
            margin-bottom: 3px;
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 7px;
            margin-top: 25px;
            border-top: 1px solid #ecf0f1;
            padding-top: 8px;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #27ae60;
        }
        .text-danger {
            color: #c0392b;
        }
        .text-warning {
            color: #f39c12;
        }
    </style>

    <!-- Encabezado -->
    <div class="header">
        <table width="100%">
            <tr>
                <td width="60%">
                    <div class="company-name">SEINT</div>
                    <div class="company-subtitle">Sistema de Edificio Inteligente</div>
                </td>
                <td width="40%" style="text-align: right;">
                    <div class="invoice-title">BOLETA DE PAGO</div>
                    <div class="invoice-number">
                        Periodo: ' . $mes . ' ' . $anio . '<br>
                        ' . date('d/m/Y', strtotime($planilla['fecha_creacion'])) . '
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Información del Empleado -->
    <div class="client-box">
        <div class="client-header">Datos del Empleado</div>
        <table width="100%">
            <tr>
                <td width="60%">
                    <div class="client-name">' . htmlspecialchars($planilla['nombre_completo']) . '</div>
                    <div class="client-info">
                        <strong>Cargo:</strong> ' . htmlspecialchars($planilla['rol']) . '
                    </div>
                </td>
                <td width="40%">
                    <div class="client-info">
                        <strong>Método de Pago:</strong> ' . ucfirst($planilla['metodo_pago']) . '<br>
                        <strong>Estado:</strong> ' . ucfirst($planilla['estado']) . '<br>
                        <strong>Fecha Pago:</strong> ' . ($planilla['fecha_pago'] ? date('d/m/Y', strtotime($planilla['fecha_pago'])) : 'Pendiente') . '
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Periodo -->
    <div class="period-box">
        <strong>Periodo de Pago:</strong> ' . $mes . ' ' . $anio . ' | 
        <strong>Días Trabajados:</strong> ' . $planilla['dias_trabajados'] . ' días de 30 |
        <strong>Estado:</strong> ' . ucfirst($planilla['estado'] ?? 'procesado') . '
    </div>

    <!-- Detalles de Pago -->
    <table class="concepts-table">
        <thead>
            <tr>
                <th width="60%">Concepto</th>
                <th width="15%">Días/Cant.</th>
                <th width="25%">Monto (Bs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="concept-name">Salario Base</div>
                    <div class="concept-desc">Remuneración mensual básica</div>
                </td>
                <td>30 días</td>
                <td class="text-right">' . number_format($planilla['haber_basico'], 2, '.', ',') . '</td>
            </tr>';

            // Mostrar descuento por días si aplica
            if ($diasDescuento > 0) {
                $html .= '
            <tr>
                <td>
                    <div class="concept-name text-danger">Descuento por Inasistencias</div>
                    <div class="concept-desc">' . $diasDescuento . ' día(s) no trabajado(s)</div>
                </td>
                <td>' . $diasDescuento . ' días</td>
                <td class="text-right text-danger">-' . number_format($descuentoPorDias, 2, '.', ',') . '</td>
            </tr>';
            }

            $html .= '
            <tr>
                <td>
                    <div class="concept-name">Total Ganado</div>
                    <div class="concept-desc">Salario proporcional a días trabajados</div>
                </td>
                <td>' . $planilla['dias_trabajados'] . ' días</td>
                <td class="text-right text-success">' . number_format($planilla['total_ganado'], 2, '.', ',') . '</td>
            </tr>
            <tr>
                <td>
                    <div class="concept-name text-danger">Descuento Gestora</div>
                    <div class="concept-desc">Aporte jubilatorio (' . $porcentajeGestora . '%)</div>
                </td>
                <td>' . $porcentajeGestora . '%</td>
                <td class="text-right text-danger">-' . number_format($planilla['descuento_gestora'], 2, '.', ',') . '</td>
            </tr>
        </tbody>
    </table>

    <!-- Total -->
    <div class="total-box">
        <div class="total-label">Líquido Pagable:</div>
        <div class="total-amount">Bs ' . number_format($planilla['liquido_pagable'], 2, '.', ',') . '</div>
    </div>

    <!-- Cálculos -->
    <div class="calculation-box">
        <div class="calculation-item"><strong>Desglose de Cálculos:</strong></div>
        <div class="calculation-item">• Salario diario: Bs ' . number_format($salarioDiario, 2, '.', ',') . ' (Bs ' . number_format($planilla['haber_basico'], 2, '.', ',') . ' ÷ 30 días)</div>';

            if ($diasDescuento > 0) {
                $html .= '<div class="calculation-item">• Descuento por días: Bs ' . number_format($descuentoPorDias, 2, '.', ',') . ' (Bs ' . number_format($salarioDiario, 2, '.', ',') . ' × ' . $diasDescuento . ' días)</div>';
            }

            $html .= '
        <div class="calculation-item">• Descuento gestora: Bs ' . number_format($planilla['descuento_gestora'], 2, '.', ',') . ' (Bs ' . number_format($planilla['haber_basico'], 2, '.', ',') . ' × ' . $porcentajeGestora . '%)</div>
        <div class="calculation-item">• Cálculo final: Bs ' . number_format($planilla['total_ganado'], 2, '.', ',') . ' - Bs ' . number_format($planilla['descuento_gestora'], 2, '.', ',') . ' = Bs ' . number_format($planilla['liquido_pagable'], 2, '.', ',') . '</div>
    </div>

    <!-- Observaciones -->
    <div style="margin-top: 15px; font-size: 8px;">
        <strong>Observaciones:</strong> ' . htmlspecialchars($planilla['observacion']) . '
    </div>

    <!-- Pie -->
    <div class="footer">
        SEINT - Sistema de Edificio Inteligente<br>
        Recibo generado el ' . date('d/m/Y \a \l\a\s H:i') . ' | Documento válido como comprobante de pago<br>
        Firma del Empleado: ___________________________
    </div>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('recibo_pago_' . $mes . '_' . $anio . '.pdf', 'D');

        } catch (Exception $e) {
            header('Location: PlanillaControlador.php?action=verMiPlanilla&error=' . urlencode('Error al generar PDF: ' . $e->getMessage()));
            exit();
        }
    }

    public function verificarPago()
    {
        session_start();
        if (!isset($_SESSION['id_persona'])) {
            $this->redirigirConError("Debe iniciar sesión para ver su planilla");
        }

        $id_persona = $_SESSION['id_persona'];
        $id_planilla = $_GET['id_planilla'] ?? null;

        // Validar que id_planilla esté presente
        if (!$id_planilla) {
            $this->redirigirConError("ID de planilla no especificado");
        }

        // Actualizar estado de pago
        $resultado = $this->planillamodelo->actualizarEstadoPago($id_planilla, $id_persona);

        if ($resultado) {
            // Obtener parámetros para mantener el filtro
            $mes = $_GET['mes'] ?? null;
            $anio = $_GET['anio'] ?? null;

            // Construir URL con parámetros
            $url = "PlanillaControlador.php?action=verMiPlanilla&success=pagoverificado";

            if ($mes) $url .= "&mes=" . urlencode($mes);
            if ($anio) $url .= "&anio=" . urlencode($anio);

            // Redirigir
            header("Location: " . $url);
            exit();
        } else {
            $this->redirigirConError("No se pudo verificar el pago. La planilla no existe o ya fue pagada.");
        }
    }
}

// =============================================
// MANEJO DE RUTAS - PETICIONES GET
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include_once "../../config/database.php";
    include_once "../modelo/PlanillaModelo.php";
    include_once "../modelo/PersonaModelo.php";

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PlanillaControlador($db);

    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'listarPlanillasCompleto':
                $controlador->listarPlanillasCompleto();
                break;
            case 'verMiPlanilla':
                $controlador->verMiPlanilla();
                break;
            case 'formularioGenerarPlanilla':
                $controlador->formularioGenerarPlanilla();
                break;
            case 'obtenerEstadisticas':
                $controlador->obtenerEstadisticas();
                break;
            case 'obtenerEmpleadosActivos':
                $controlador->obtenerEmpleadosActivos();
                break;
            case 'obtenerPlanillasCompletas':
                $controlador->obtenerPlanillasCompletas();
                break;
            case 'obtenerMiPlanilla':
                $controlador->obtenerMiPlanilla();
                break;
            case 'obtenerResumenAnual':
                $controlador->obtenerResumenAnual();
                break;
            case 'descargarReciboPDF':
                $controlador->descargarReciboPDF();
                break;
            case 'verificarPago':
                $controlador->verificarPago();
                break;
            default:
                header('Location: ../vista/DashboardVista.php?error=Accion no valida');
                exit;
        }
    }
}

// =============================================
// MANEJO DE RUTAS - PETICIONES POST
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../../config/database.php';
    require_once '../modelo/PlanillaModelo.php';
    require_once '../modelo/PersonaModelo.php';

    $database = new Database();
    $db = $database->getConnection();
    $controlador = new PlanillaControlador($db);

    switch($_POST['action']) {
        case 'generarPlanillaCompleta':
            $controlador->generarPlanillaCompleta();
            break;
        case 'generarPlanillaPersonalizada':
            $controlador->generarPlanillaPersonalizada();
            break;
        case 'generarPlanillaMultiple':
            $controlador->generarPlanillaMultiple();
            break;
        default:
            header('Location: ../vista/DashboardVista.php?error=Accion no valida');
            exit;
    }
}
?>