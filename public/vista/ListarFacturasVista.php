<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Gestión de Facturas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Facturas</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Alertas -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

    <!-- Panel de Generación de Facturas -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Generar Facturas</h5>
                    <i class="fas fa-file-invoice-dollar text-primary"></i>
                </div>
                <div class="content-box-body">
                    <form method="POST" action="../controlador/FacturaControlador.php" class="row g-3">
                        <input type="hidden" name="action" value="generarFacturas">

                        <div class="col-md-6">
                            <label for="mes_facturacion" class="form-label fw-bold">Mes de Facturación:</label>
                            <input type="month"
                                   class="form-control"
                                   id="mes_facturacion"
                                   name="mes_facturacion"
                                   required
                                   min="2024-01"
                                   max="2025-12">
                            <div class="form-text">Seleccione el mes para el cual generar las facturas (formato: YYYY-MM)</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cog me-2"></i>Generar Facturas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Facturas -->
<?php
$estadisticas = [
        'total_facturas' => count($facturas),
        'pendientes' => count(array_filter($facturas, function($f) { return $f['estado'] == 'pendiente'; })),
        'pagadas' => count(array_filter($facturas, function($f) { return $f['estado'] == 'pagada'; })),
        'vencidas' => count(array_filter($facturas, function($f) { return $f['estado'] == 'vencida'; }))
];
?>

    <div class="row fade-in mb-4">
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['total_facturas']; ?></h4>
                    <p class="text-muted mb-0">Total Facturas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['pendientes']; ?></h4>
                    <p class="text-muted mb-0">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['pagadas']; ?></h4>
                    <p class="text-muted mb-0">Pagadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-box text-center">
                <div class="content-box-body">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?php echo $estadisticas['vencidas']; ?></h4>
                    <p class="text-muted mb-0">Vencidas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Facturas -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Lista de Facturas</h5>
                    <span class="badge bg-primary"><?php echo count($facturas); ?> facturas</span>
                </div>
                <div class="content-box-body">
                    <?php if (empty($facturas)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay facturas registradas en el sistema</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table id="tablaFacturas" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-building text-warning me-2"></i>Departamento</th>
                                    <th><i class="fas fa-user text-info me-2"></i>Residente</th>
                                    <th><i class="fas fa-calendar-day text-success me-2"></i>Emisión</th>
                                    <th><i class="fas fa-calendar-times text-danger me-2"></i>Vencimiento</th>
                                    <th><i class="fas fa-money-bill-wave text-primary me-2"></i>Monto</th>
                                    <th><i class="fas fa-cubes text-secondary me-2"></i>Conceptos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($facturas as $index => $factura): ?>
                                    <tr>
                                        <td><strong><?php echo $factura['id_factura']; ?></strong></td>
                                        <td>
                                            <strong>D<?php echo htmlspecialchars($factura['departamento']); ?>-P<?php echo htmlspecialchars($factura['piso']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($factura['residente']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?></td>
                                        <td>
                                            <?php
                                            $fecha_vencimiento = strtotime($factura['fecha_vencimiento']);
                                            $hoy = strtotime('today');
                                            $clase_fecha = ($fecha_vencimiento < $hoy && $factura['estado'] != 'pagada') ? 'text-danger fw-bold' : '';
                                            ?>
                                            <span class="<?php echo $clase_fecha; ?>">
                                                <?php echo date('d/m/Y', $fecha_vencimiento); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>Bs. <?php echo number_format($factura['monto_total'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-cube me-1"></i>
                                                <?php echo $factura['cantidad_conceptos']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            $icon = '';
                                            switch($factura['estado']) {
                                                case 'pagada':
                                                    $badge_class = 'bg-success';
                                                    $icon = 'fa-check-circle';
                                                    break;
                                                case 'pendiente':
                                                    $badge_class = 'bg-warning';
                                                    $icon = 'fa-clock';
                                                    break;
                                                case 'vencida':
                                                    $badge_class = 'bg-danger';
                                                    $icon = 'fa-exclamation-triangle';
                                                    break;
                                                default:
                                                    $badge_class = 'bg-secondary';
                                                    $icon = 'fa-question-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($factura['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Ver Factura -->
                                                <button class="btn btn-info btn-sm ver-factura"
                                                        data-id="<?php echo $factura['id_factura']; ?>"
                                                        data-departamento="D<?php echo $factura['departamento']; ?>-P<?php echo $factura['piso']; ?>"
                                                        data-residente="<?php echo htmlspecialchars($factura['residente']); ?>"
                                                        data-monto="Bs. <?php echo number_format($factura['monto_total'], 2); ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmarVerFacturaModal"
                                                        title="Ver factura">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>

                                                <!-- Pago QR -->
                                                <button class="btn btn-success btn-sm generar-qr"
                                                        data-id="<?php echo $factura['id_factura']; ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#generarQRModal"
                                                        title="Pago con QR">
                                                    <i class="fas fa-qrcode"></i> QR
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Ver Factura -->
    <div class="modal fade" id="confirmarVerFacturaModal" tabindex="-1" aria-labelledby="confirmarVerFacturaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarVerFacturaModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar Apertura
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Advertencia:</strong> Está a punto de abrir la factura en una nueva vista.
                    </div>

                    <div class="factura-info p-3 border rounded">
                        <p><strong>Factura #:</strong> <span id="confirmFacturaNumero"></span></p>
                        <p><strong>Departamento:</strong> <span id="confirmDepartamento"></span></p>
                        <p><strong>Residente:</strong> <span id="confirmResidente"></span></p>
                        <p><strong>Monto Total:</strong> <span id="confirmMonto"></span></p>
                    </div>

                    <p class="mt-3 mb-0 text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Será redirigido a la vista detallada de la factura.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarVerFactura">
                        <i class="fas fa-external-link-alt me-2"></i>Abrir Factura
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pago QR -->
    <div class="modal fade" id="generarQRModal" tabindex="-1" aria-labelledby="generarQRModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generarQRModalLabel">
                        <i class="fas fa-qrcode me-2"></i>Pago con QR - Factura #<span id="qrFacturaNumero"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <p class="text-muted">Escanea este código QR para realizar el pago de la factura</p>
                    </div>

                    <div class="qr-container position-relative d-inline-block">
                        <div id="qrcode"></div>
                        <div class="dino-icon">
                            <img src="dino.png" alt="Dinosaurio" onerror="this.style.display='none'">
                        </div>
                    </div>

                    <div class="mt-3">
                        <p class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Este QR contiene información cifrada para el pago seguro
                        </p>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir DataTables CSS y JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Incluir CryptoJS y QRCodeJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Script para DataTable y funcionalidades -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            var tabla = $('#tablaFacturas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [8]
                    },
                    {
                        searchable: false,
                        targets: [8]
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_filter input').attr('placeholder', 'Buscar...');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Variables globales
            const encryptionKey = "QuivoApp2024Key!";
            let currentQRCode = null;
            let currentFacturaId = null;
            let facturaParaVer = null;

            // Función para cifrar datos
            function encryptData(data, key) {
                const keyUtf8 = CryptoJS.enc.Utf8.parse(key);
                const encrypted = CryptoJS.AES.encrypt(data, keyUtf8, {
                    mode: CryptoJS.mode.ECB,
                    padding: CryptoJS.pad.Pkcs7
                });
                return encrypted.toString();
            }

            // Función para generar QR
            function generarQR(datosCifrados) {
                const qrContainer = document.getElementById("qrcode");

                // Limpiar QR anterior
                qrContainer.innerHTML = '';

                // Generar nuevo QR
                currentQRCode = new QRCode(qrContainer, {
                    text: datosCifrados,
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }

            // Función para descargar QR
            function descargarQR() {
                const canvas = document.querySelector('#qrcode canvas');
                if (canvas) {
                    const link = document.createElement('a');
                    link.download = `qr-factura-${currentFacturaId}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                }
            }

            // Modal Confirmar Ver Factura
            const confirmarVerFacturaModal = document.getElementById('confirmarVerFacturaModal');
            confirmarVerFacturaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                facturaParaVer = {
                    id: button.getAttribute('data-id'),
                    departamento: button.getAttribute('data-departamento'),
                    residente: button.getAttribute('data-residente'),
                    monto: button.getAttribute('data-monto')
                };

                // Actualizar información en el modal
                document.getElementById('confirmFacturaNumero').textContent = facturaParaVer.id;
                document.getElementById('confirmDepartamento').textContent = facturaParaVer.departamento;
                document.getElementById('confirmResidente').textContent = facturaParaVer.residente;
                document.getElementById('confirmMonto').textContent = facturaParaVer.monto;
            });

            // Botón confirmar ver factura
            document.getElementById('btnConfirmarVerFactura').addEventListener('click', function() {
                if (facturaParaVer && facturaParaVer.id) {
                    // Redirigir al controlador para ver la factura
                    window.location.href = `../controlador/FacturaControlador.php?action=verFactura&id_factura=${facturaParaVer.id}`;
                }
            });

            // Modal Pago QR
            const generarQRModal = document.getElementById('generarQRModal');
            generarQRModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const idFactura = button.getAttribute('data-id');

                // Guardar ID de factura actual
                currentFacturaId = idFactura;

                // Actualizar número de factura en el modal
                document.getElementById('qrFacturaNumero').textContent = idFactura;

                // Generar QR inmediatamente
                generarQRParaFactura(idFactura);
            });

            // Función para generar QR para una factura específica
            function generarQRParaFactura(idFactura) {
                // Obtener configuración desde database.php
                <?php
                require_once '../../config/database.php';
                $database = new Database();
                $ipServidor = Database::getServerIP();
                $dbname = $database->getDbName();
                $dbuser = $database->getUsername();
                ?>
                
                // Configuración de la base de datos desde config/database.php
                const ipServidor = '<?php echo htmlspecialchars($ipServidor, ENT_QUOTES, 'UTF-8'); ?>';
                const dbname = '<?php echo htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8'); ?>';
                const dbuser = '<?php echo htmlspecialchars($dbuser, ENT_QUOTES, 'UTF-8'); ?>';

                // Datos a cifrar: IP;DBNAME;USER;ID_FACTURA
                const datos = `${ipServidor};${dbname};${dbuser};${idFactura}`;

                console.log("Datos originales para QR:", datos);

                // Cifrar datos
                const datosCifrados = encryptData(datos, encryptionKey);
                console.log("Datos cifrados:", datosCifrados);

                // Generar QR
                generarQR(datosCifrados);
            }

            // Auto-ocultar alertas después de 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        .content-box-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--azul-oscuro);
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            margin: 0 2px;
            min-width: 70px;
        }

        .content-box.text-center {
            transition: transform 0.2s;
        }

        .content-box.text-center:hover {
            transform: translateY(-5px);
        }

        .dino-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dino-icon img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        .qr-container {
            position: relative;
            display: inline-block;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .factura-info {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .qr-container {
                transform: scale(0.8);
            }

            .btn-group .btn {
                min-width: 60px;
                font-size: 0.8rem;
            }
        }
    </style>

<?php include("../../includes/footer.php"); ?>