<?php include("../../includes/header.php"); ?>

    <!-- Page Header -->
    <div class="page-header fade-in">
        <div class="page-title">
            <h1>Detalle de Factura</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="FacturaControlador.php?action=listarFacturas">Facturas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalle de Factura</li>
                </ol>
            </nav>
        </div>
        <div class="page-actions">
            <a href="FacturaControlador.php?action=listarFacturas" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Facturas
            </a>
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

    <!-- Información de la Factura -->
    <div class="row fade-in mb-4">
        <div class="col-12">
            <div class="content-box">
                <div class="content-box-header d-flex justify-content-between align-items-center">
                    <h5>Información General de la Factura</h5>
                    <?php if ($facturaCompleta): ?>
                        <?php
                        $factura = $facturaCompleta['factura'];
                        $estadoClase = '';
                        switch($factura['estado']) {
                            case 'pagada': $estadoClase = 'bg-success'; break;
                            case 'pendiente': $estadoClase = 'bg-warning'; break;
                            case 'vencida': $estadoClase = 'bg-danger'; break;
                            default: $estadoClase = 'bg-secondary';
                        }
                        ?>
                        <span class="badge <?php echo $estadoClase; ?>">
                            <?php echo ucfirst(htmlspecialchars($factura['estado'])); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="content-box-body">
                    <?php if ($facturaCompleta): ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Número de Factura:</label>
                                    <h4>#<?php echo htmlspecialchars($factura['id_factura']); ?></h4>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Fecha de Emisión:</label>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar-alt text-info me-2"></i>
                                        <?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Departamento:</label>
                                    <h4>
                                        <i class="fas fa-building text-warning me-2"></i>
                                        Piso <?php echo htmlspecialchars($factura['piso']); ?> - N°<?php echo htmlspecialchars($factura['departamento']); ?>
                                    </h4>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Fecha de Vencimiento:</label>
                                    <p class="mb-0">
                                        <i class="fas fa-clock text-warning me-2"></i>
                                        <?php echo date('d/m/Y', strtotime($factura['fecha_vencimiento'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Residente:</label>
                                    <h4><?php echo htmlspecialchars($factura['residente']); ?></h4>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Cédula de Identidad:</label>
                                    <p class="mb-0">
                                        <i class="fas fa-id-card text-secondary me-2"></i>
                                        <?php echo htmlspecialchars($factura['ci']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">Monto Total:</label>
                                    <h4 class="text-success">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Bs. <?php echo number_format($factura['monto_total'], 2); ?>
                                    </h4>
                                </div>
                                <div class="actions-grid">
                                    <a href="FacturaControlador.php?action=descargarFactura&id_factura=<?php echo $factura['id_factura']; ?>"
                                       class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-download me-1"></i>PDF
                                    </a>
                                    <?php if (!empty($factura['email'])): ?>
                                    <button type="button" 
                                            class="btn btn-outline-info btn-sm"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEnviarFactura"
                                            data-email="<?php echo htmlspecialchars($factura['email']); ?>"
                                            data-residente="<?php echo htmlspecialchars($factura['residente']); ?>"
                                            data-numero-factura="<?php echo str_pad($factura['id_factura'], 6, '0', STR_PAD_LEFT); ?>">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Factura no encontrada</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php if ($facturaCompleta): ?>
    <!-- Sección de Conceptos y Pago -->
    <div class="row fade-in">
        <?php if ($factura['estado'] === 'pendiente' || $factura['estado'] === 'vencida'): ?>
            <!-- Estado PENDIENTE o VENCIDA: Métodos de Pago y Conceptos -->
            <div class="col-md-6">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Método de Pago</h5>
                    </div>
                    <div class="content-box-body">
                        <div class="secure-notice bg-light p-3 rounded mb-3">
                            <i class="fas fa-lock text-success me-2"></i>
                            Conexión segura y cifrada
                        </div>

                        <div class="payment-methods mb-4">
                            <div class="payment-method active" data-method="card">
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card fa-2x"></i>
                                </div>
                                <div class="payment-title">Tarjeta</div>
                                <div class="payment-subtitle">VISA • MASTERCARD • AMEX</div>
                            </div>
                            <div class="payment-method" data-method="qr">
                                <div class="payment-icon">
                                    <i class="fas fa-qrcode fa-2x"></i>
                                </div>
                                <div class="payment-title">Código QR</div>
                                <div class="payment-subtitle">Pago móvil</div>
                            </div>
                        </div>

                        <div id="card-fields">
                            <div class="mb-3">
                                <label class="form-label">Número de tarjeta</label>
                                <input type="text" class="form-control" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha de vencimiento</label>
                                        <input type="text" class="form-control" id="expiry" placeholder="MM/AA" maxlength="5">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">CVC</label>
                                        <input type="text" class="form-control" id="cvc" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre del titular</label>
                                <input type="text" class="form-control" placeholder="Como aparece en la tarjeta">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" checked id="save-card">
                                <label class="form-check-label" for="save-card">
                                    Guardar tarjeta para futuros pagos
                                </label>
                            </div>

                            <!-- Botón de Pago con Tarjeta -->
                            <form method="POST" action="../controlador/FacturaControlador.php" id="formPagoTarjeta">
                                <input type="hidden" name="action" value="pagarFactura">
                                <input type="hidden" name="id_factura" value="<?php echo $factura['id_factura']; ?>">
                                <input type="hidden" name="metodo_pago" value="tarjeta">
                                <input type="hidden" name="origen" value="pago_tarjeta">
                                <button type="submit" class="btn btn-success btn-lg w-100" id="pay-button">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Pagar Bs <?php echo number_format($factura['monto_total'], 2, '.', ','); ?>
                                </button>
                            </form>
                        </div>

                        <div id="qr-fields" class="qr-container text-center" style="display: none;">
                            <div class="qr-code bg-white p-3 rounded border mb-3">
                                <div id="qrcode"></div>
                                <div class="dino-icon">
                                    <img src="dino.png" alt="Dinosaurio" onerror="this.style.display='none'">
                                </div>
                            </div>
                            <p class="text-muted">Escanea este código con tu aplicación de pagos móvil</p>

                            <!-- Botón para verificar estado después del pago QR -->
                            <div class="mt-3">
                                <a href="FacturaControlador.php?action=verFactura&id_factura=<?php echo $factura['id_factura']; ?>&origen=pago_qr"
                                   class="btn btn-success btn-lg w-100" id="btnVerificarPagoQR">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Verificar Pago con QR
                                </a>
                                <p class="text-muted small mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Después de escanear y pagar, haz clic aquí para actualizar el estado
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Conceptos de Pago</h5>
                    </div>
                    <div class="content-box-body">
                        <?php
                        $conceptos = $facturaCompleta['conceptos'];
                        if (!empty($conceptos)):
                            $total = 0;
                            ?>
                            <?php foreach ($conceptos as $concepto):
                            $subtotal = $concepto['monto'] * $concepto['cantidad'];
                            $total += $subtotal;
                            ?>
                            <div class="concept-item d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($concepto['concepto']); ?></h6>
                                    <?php if (!empty($concepto['descripcion'])): ?>
                                        <p class="text-muted small mb-1">
                                            <?php echo htmlspecialchars($concepto['descripcion']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($concepto['cantidad'] > 1): ?>
                                        <span class="badge bg-light text-dark">
                                                    <?php echo $concepto['cantidad']; ?> x Bs <?php echo number_format($concepto['monto'], 2, '.', ','); ?>
                                                </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">Bs <?php echo number_format($subtotal, 2, '.', ','); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>

                            <div class="total mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-5">Total a pagar</span>
                                    <span class="fw-bold fs-5 text-success">Bs <?php echo number_format($total, 2, '.', ','); ?></span>
                                </div>
                            </div>

                            <p class="text-center text-muted small mt-3">
                                Al completar el pago aceptas las <a href="#" class="text-decoration-none">Condiciones de uso</a>
                            </p>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron conceptos para esta factura.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Estado PAGADO: Conceptos ocupan toda la pantalla -->
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Detalles de la Factura</h5>
                    </div>
                    <div class="content-box-body">
                        <?php if ($factura['estado'] === 'pagada' && (isset($_GET['origen']) || isset($_POST['origen']))): ?>
                            <?php
                            $origen = isset($_GET['origen']) ? $_GET['origen'] : (isset($_POST['origen']) ? $_POST['origen'] : '');
                            $mensaje = '';
                            if ($origen === 'pago_tarjeta') {
                                $mensaje = 'Pago con tarjeta procesado exitosamente';
                            } elseif ($origen === 'pago_qr') {
                                $mensaje = 'Pago con QR verificado exitosamente';
                            }
                            ?>
                            <?php if ($mensaje): ?>
                                <div class="alert alert-success text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-check-circle fa-2x me-3"></i>
                                        <div>
                                            <h4 class="mb-1">Pago Completado</h4>
                                            <p class="mb-0"><?php echo $mensaje; ?>. Gracias por su pago puntual.</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <strong>Monto pagado: Bs <?php echo number_format($factura['monto_total'], 2, '.', ','); ?></strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <h6 class="mb-3">Conceptos de pago</h6>
                        <?php
                        $conceptos = $facturaCompleta['conceptos'];
                        if (!empty($conceptos)):
                            $total = 0;
                            ?>
                            <?php foreach ($conceptos as $concepto):
                            $subtotal = $concepto['monto'] * $concepto['cantidad'];
                            $total += $subtotal;
                            ?>
                            <div class="concept-item d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($concepto['concepto']); ?></h6>
                                    <?php if (!empty($concepto['descripcion'])): ?>
                                        <p class="text-muted small mb-1">
                                            <?php echo htmlspecialchars($concepto['descripcion']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($concepto['cantidad'] > 1): ?>
                                        <span class="badge bg-light text-dark">
                                                    <?php echo $concepto['cantidad']; ?> x Bs <?php echo number_format($concepto['monto'], 2, '.', ','); ?>
                                                </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">Bs <?php echo number_format($subtotal, 2, '.', ','); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>

                            <div class="total mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold fs-5">
                                            <?php echo $factura['estado'] === 'pagada' ? 'Total pagado' : 'Total'; ?>
                                        </span>
                                    <span class="fw-bold fs-5 text-success">Bs <?php echo number_format($total, 2, '.', ','); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron conceptos para esta factura.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <!-- Incluir CryptoJS y QRCodeJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Scripts para funcionalidades de pago -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            const encryptionKey = "QuivoApp2024Key!";
            let currentQRCode = null;
            let currentFacturaId = "<?php echo $facturaCompleta ? $factura['id_factura'] : ''; ?>";

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

            // Formateo de campos de tarjeta
            const cardNumber = document.getElementById('card-number');
            const expiry = document.getElementById('expiry');
            const cvc = document.getElementById('cvc');

            if (cardNumber) {
                cardNumber.addEventListener('input', e => {
                    let v = e.target.value.replace(/\D/g, '').match(/.{1,4}/g);
                    e.target.value = v ? v.join(' ') : '';
                });
            }

            if (expiry) {
                expiry.addEventListener('input', e => {
                    let v = e.target.value.replace(/\D/g, '');
                    if (v.length >= 2) v = v.slice(0, 2) + '/' + v.slice(2, 4);
                    e.target.value = v;
                });
            }

            if (cvc) {
                cvc.addEventListener('input', e => {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });
            }

            // Cambio de método de pago
            const methods = document.querySelectorAll('.payment-method');
            const cardF = document.getElementById('card-fields');
            const qrF = document.getElementById('qr-fields');

            if (methods.length > 0) {
                methods.forEach(m => m.addEventListener('click', () => {
                    methods.forEach(x => x.classList.remove('active'));
                    m.classList.add('active');

                    if (m.dataset.method === 'qr') {
                        cardF.style.display = 'none';
                        qrF.style.display = 'block';

                        // Generar QR cuando se selecciona esta opción
                        if (currentFacturaId) {
                            generarQRParaFactura(currentFacturaId);
                        }
                    } else {
                        cardF.style.display = 'block';
                        qrF.style.display = 'none';
                    }
                }));
            }

            // Validación de formulario de pago con tarjeta
            const formPagoTarjeta = document.getElementById('formPagoTarjeta');
            if (formPagoTarjeta) {
                formPagoTarjeta.addEventListener('submit', function(e) {
                    if (!cardNumber.value || !expiry.value || !cvc.value) {
                        e.preventDefault();
                        alert('Por favor, complete todos los campos de la tarjeta');
                        return;
                    }

                    // Mostrar loading
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                    submitBtn.disabled = true;
                });
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
        .payment-methods {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-method {
            flex: 1;
            padding: 1.5rem 1rem;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .payment-method.active {
            border-color: var(--azul-oscuro);
            background: rgba(37, 99, 235, 0.05);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .payment-method:hover:not(.active) {
            border-color: var(--azul-oscuro);
            transform: translateY(-2px);
        }

        .payment-icon {
            margin-bottom: 0.5rem;
            color: var(--azul-oscuro);
        }

        .payment-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .payment-subtitle {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .qr-container {
            display: none;
        }

        .qr-code {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 1rem;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .secure-notice {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: rgba(25, 135, 84, 0.1);
            border-radius: 8px;
            font-size: 0.875rem;
            color: #198754;
        }

        .concept-item {
            transition: background-color 0.2s;
        }

        .concept-item:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .payment-methods {
                flex-direction: column;
            }

            .actions-grid {
                text-align: center;
            }

            .actions-grid .btn {
                margin-bottom: 0.5rem;
                width: 100%;
            }
        }
    </style>

    <!-- Modal para confirmar envío de factura por correo -->
    <div class="modal fade" id="modalEnviarFactura" tabindex="-1" aria-labelledby="modalEnviarFacturaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalEnviarFacturaLabel">
                        <i class="fas fa-envelope me-2"></i>Enviar Factura por Correo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="fas fa-info-circle me-2 fa-lg"></i>
                        <div>
                            <strong>Confirmación de envío</strong>
                            <p class="mb-0 small">Se enviará la factura en formato PDF al correo electrónico del residente.</p>
                        </div>
                    </div>
                    
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Información del Destinatario
                            </h6>
                            <div class="mb-2">
                                <strong>Residente:</strong>
                                <span id="modalResidente" class="text-dark"></span>
                            </div>
                            <div class="mb-2">
                                <strong>Correo Electrónico:</strong>
                                <span id="modalEmail" class="text-dark"></span>
                            </div>
                            <div>
                                <strong>Número de Factura:</strong>
                                <span id="modalNumeroFactura" class="text-dark"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>¿Está seguro de que desea enviar esta factura al correo electrónico indicado?</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <a href="#" id="btnConfirmarEnvio" class="btn btn-info">
                        <i class="fas fa-paper-plane me-2"></i>Confirmar Envío
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configurar modal cuando se abre
        document.addEventListener('DOMContentLoaded', function() {
            const modalEnviarFactura = document.getElementById('modalEnviarFactura');
            const btnConfirmarEnvio = document.getElementById('btnConfirmarEnvio');
            
            if (!modalEnviarFactura || !btnConfirmarEnvio) return;
            
            modalEnviarFactura.addEventListener('show.bs.modal', function (event) {
                // Botón que activó el modal
                const button = event.relatedTarget;
                
                if (!button) return;
                
                // Extraer información de los atributos data-*
                const email = button.getAttribute('data-email') || '';
                const residente = button.getAttribute('data-residente') || '';
                const numeroFactura = button.getAttribute('data-numero-factura') || '';
                const idFactura = <?php echo isset($facturaCompleta) && $facturaCompleta ? $factura['id_factura'] : 'null'; ?>;
                
                // Actualizar contenido del modal
                const modalEmailEl = document.getElementById('modalEmail');
                const modalResidenteEl = document.getElementById('modalResidente');
                const modalNumeroFacturaEl = document.getElementById('modalNumeroFactura');
                
                if (modalEmailEl) modalEmailEl.textContent = email;
                if (modalResidenteEl) modalResidenteEl.textContent = residente;
                if (modalNumeroFacturaEl) modalNumeroFacturaEl.textContent = '#' + numeroFactura;
                
                // Configurar URL de confirmación
                if (idFactura && btnConfirmarEnvio) {
                    btnConfirmarEnvio.href = 'FacturaControlador.php?action=enviarFactura&id_factura=' + idFactura;
                } else if (btnConfirmarEnvio) {
                    btnConfirmarEnvio.href = '#';
                }
            });
        });
    </script>

<?php include("../../includes/footer.php"); ?>