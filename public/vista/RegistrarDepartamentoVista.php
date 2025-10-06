<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Registrar Nuevo Departamento</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item"><a href="../controlador/DepartamentoControlador.php?accion=listar">Departamentos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Registrar Departamento</li>
                        </ol>
                    </nav>
                </div>

            </div>

            <!-- Formulario de Registro -->
            <div class="row fade-in">
                <div class="col-lg-8">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Información del Departamento</h5>
                        </div>
                        <div class="content-box-body">
                            <form id="formRegistrarDepartamento" action="../controlador/DepartamentoControlador.php" method="POST">
                                <input type="hidden" name="action" value="registrar">
                                <input type="hidden" name="id_edificio" value=1>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="numero" class="form-label">
                                                <i class="fas fa-building text-verde me-2"></i>Departamento *
                                            </label>
                                            <input type="text"    class="form-control"    id="numero"    name="numero"    required    maxlength="10"   placeholder="DEP - 000">
                                            <div class="form-text">Número único identificador del departamento</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="piso" class="form-label">
                                                <i class="fas fa-layer-group text-azul me-2"></i>Piso *
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="piso" 
                                                   name="piso" 
                                                   required 
                                                   min="0" 
                                                   max="50"
                                                   placeholder="Ingrese el numero de piso">
                                            <div class="form-text">Nivel donde se encuentra (0-50)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="DepartamentoControlador.php?accion=listar" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" style="background: var(--verde); border: none;">
                                        <i class="fas fa-save me-2"></i>Registrar Departamento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Información Adicional -->
                <div class="col-lg-4">
                    <div class="content-box position-sticky" style="top: 100px;">
                        <div class="content-box-header">
                            <h5>Información Importante</h5>
                        </div>
                        <div class="content-box-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Instrucciones:</h6>
                                <ul class="mb-0 mt-2">
                                    <li>Todos los campos marcados con (*) son obligatorios</li>
                                    <li>El número de departamento debe ser único</li>
                                    <li>Verifique que el departamento no exista previamente</li>
                                    <li>El piso 0 corresponde a la planta baja area comun</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Notas:</h6>
                                <ul class="mb-0 mt-2">
                                    <li>No se permiten departamentos duplicados</li>
                                    <li>Una vez registrado, estará disponible inmediatamente</li>
                                    <li>Verifique la información antes de guardar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Script para validaciones -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const formRegistrar = document.getElementById('formRegistrarDepartamento');
        const btnRegistrar = formRegistrar.querySelector('button[type="submit"]');
        
        // Validación del formulario
        formRegistrar.addEventListener('submit', function(e) {
            const numero = document.getElementById('numero').value.trim();
            const piso = document.getElementById('piso').value;
            
            if (!numero) {
                e.preventDefault();
                alert('Por favor, ingrese el número del departamento');
                document.getElementById('numero').focus();
                return;
            }
            
            if (!piso || piso < 0 || piso > 50) {
                e.preventDefault();
                alert('Por favor, ingrese un piso válido (0-50)');
                document.getElementById('piso').focus();
                return;
            }

            // Mostrar loading en el botón
            btnRegistrar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
            btnRegistrar.disabled = true;
        });
    });

    // Auto-ocultar solo alertas de éxito y error después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-danger');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    </script>

<?php include("../../includes/footer.php");?>