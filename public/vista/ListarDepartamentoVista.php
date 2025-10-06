<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Listado de Departamentos</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Departamentos</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <a href="../vista/RegistrarDepartamentoVista.php" class="btn btn-success">
                        Registrar <i class="fas fa-arrow-right me-2"></i> 
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
            
            <!-- departamentos -->
            <div class="row fade-in">
                <div class="col-12">
                    <div class="content-box">
                        <div class="content-box-header d-flex justify-content-between align-items-center">
                            <h5>Departamentos Registrados</h5>
                            <span class="badge bg-primary"><?php echo count($departamentos); ?> departamentos</span>
                        </div>
                        <div class="content-box-body">
                            <?php if (empty($departamentos)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay departamentos registrados</p>
                                </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th># ID</th>
                                            <th>Departamento</th>
                                            <th>Piso</th>
                                            <th>Estado</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($departamentos as $departamento): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($departamento['id_departamento']); ?></strong></td>
                                            <td>
                                                <i class="fas fa-door-open text-primary me-2"></i>
                                                <?php echo htmlspecialchars($departamento['numero']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-layer-group me-1"></i>
                                                    Piso <?php echo htmlspecialchars($departamento['piso']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Activo
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editarDepartamentoModal"
                                                        data-id="<?php echo htmlspecialchars($departamento['id_departamento']); ?>"
                                                        data-numero="<?php echo htmlspecialchars($departamento['numero']); ?>"
                                                        data-piso="<?php echo htmlspecialchars($departamento['piso']); ?>"
                                                        title="Editar departamento">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
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
        </div>
    </main>

    <!-- Modal Editar Departamento -->
    <div class="modal fade" id="editarDepartamentoModal" tabindex="-1" aria-labelledby="editarDepartamentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDepartamentoModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
             <form method="POST" action="DepartamentoControlador.php" id="formEditarDepartamento">
                    <input type="hidden" name="action" value="actualizar">
                    <input type="hidden" id="id_departamento" name="id_departamento">
                    
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="numero" class="form-label">
                                            <i class="fas fa-hashtag text-verde me-2"></i># Departamento
                                        </label>
                                        <input type="text" class="form-control" id="numero" name="numero" required 
                                               placeholder="Ej: 101, 202..." maxlength="10">
                                        <div class="form-text">Número identificador único</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="piso" class="form-label">
                                            <i class="fas fa-building text-azul me-2"></i>Piso
                                        </label>
                                        <input type="number" class="form-control" id="piso" name="piso" required 
                                               min="0" max="50" placeholder="Ej: 1, 2, 3...">
                                        <div class="form-text">Nivel donde se encuentra (0-50)</div>
                                    </div>
                                </div>
                            </div>
                            
                          
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning" id="btnGuardarCambios">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para cargar datos en el modal y validaciones -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editarModal = document.getElementById('editarDepartamentoModal');
        const formEditar = document.getElementById('formEditarDepartamento');
        const btnGuardar = document.getElementById('btnGuardarCambios');
        
        // Cargar datos en el modal
        editarModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const numero = button.getAttribute('data-numero');
            const piso = button.getAttribute('data-piso');
            
            document.getElementById('id_departamento').value = id;
            document.getElementById('numero').value = numero;
            document.getElementById('piso').value = piso;
        });

        // Validación del formulario
        formEditar.addEventListener('submit', function(e) {
            const numero = document.getElementById('numero').value.trim();
            const piso = document.getElementById('piso').value;
            
            if (!numero) {
                e.preventDefault();
                alert('Por favor, ingrese el número de departamento');
                document.getElementById('numero').focus();
                return;
            }
            
            if (!piso || piso < 0 || piso > 50) {
                e.preventDefault();
                alert('El piso debe ser un número entre 0 y 50');
                document.getElementById('piso').focus();
                return;
            }

            // Mostrar loading en el botón
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            btnGuardar.disabled = true;
        });

        // Limpiar el modal cuando se cierre
        editarModal.addEventListener('hidden.bs.modal', function() {
            formEditar.reset();
            btnGuardar.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Cambios';
            btnGuardar.disabled = false;
        });
    });

    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    </script>

    <!-- Estilos para el modal -->
    <style>
    .modal-content {11
        border: none;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(13, 61, 71, 0.3);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(135deg, var(--azul-oscuro) 0%, var(--azul) 100%);
        color: white;
        border-bottom: 2px solid rgba(175, 239, 206, 0.3);
        padding: 20px 25px;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 1.3rem;
    }
    
    .btn-close {
        filter: invert(1);
        opacity: 0.8;
        transition: all 0.3s;
    }
    
    .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }
    
    .modal-body {
        padding: 30px;
    }
    
    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 20px 25px;
        gap: 10px;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--azul-oscuro);
        margin-bottom: 8px;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: var(--verde);
        box-shadow: 0 0 0 0.2rem rgba(54, 137, 121, 0.25);
    }
    
    .form-text {
        font-size: 0.85rem;
        color: var(--verde-suave);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, var(--verde) 0%, var(--azul) 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, var(--azul) 0%, var(--azul-oscuro) 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(42, 117, 149, 0.4);
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
    
    .text-verde { color: var(--verde); }
    .text-azul { color: var(--azul); }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: var(--azul-oscuro);
    }
    
    .content-box-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }
    </style>
   
<?php include("../../includes/footer.php");?>