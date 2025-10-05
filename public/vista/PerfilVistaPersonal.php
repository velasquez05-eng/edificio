<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Panel de Administración</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Administración</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary me-2" style="background: var(--verde); border: none;" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                        <i class="fas fa-edit me-2"></i> Editar Perfil
                    </button>
                    <button class="btn btn-warning" style="background: var(--azul); border: none;" data-bs-toggle="modal" data-bs-target="#gestionSistemaModal">
                        <i class="fas fa-cog me-2"></i> Gestión del Sistema
                    </button>
                </div>
            </div>

            <!-- Admin Dashboard -->
            <div class="row fade-in">
                <!-- Admin Info -->
                <div class="col-lg-4">
                    <div class="content-box">
                        <div class="content-box-header bg-gradient-oscuro text-white">
                            <h5 class="text-white">Información del Administrador</h5>
                        </div>
                        <div class="content-box-body text-center">
                            <div class="user-avatar-large mb-3 mx-auto">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h4 class="mb-2" id="nombreCompletoAdmin">Máximo Decimo Meridio</h4>
                            <p class="text-muted mb-3">Administrador del Sistema</p>
                            
                            <div class="admin-stats">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h5 class="mb-1">5</h5>
                                        <small class="text-muted">Años de Servicio</small>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="mb-1">Admin</h5>
                                        <small class="text-muted">Rol</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Overview -->
                    <div class="content-box mt-4">
                        <div class="content-box-header">
                            <h5>Resumen del Sistema</h5>
                        </div>
                        <div class="content-box-body">
                            <div class="system-overview">
                                <div class="system-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Usuarios Registrados</span>
                                        <strong class="text-primary">142</strong>
                                    </div>
                                </div>
                                <div class="system-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Departamentos Activos</span>
                                        <strong class="text-success">85</strong>
                                    </div>
                                </div>
                                <div class="system-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Incidentes Pendientes</span>
                                        <strong class="text-warning">12</strong>
                                    </div>
                                </div>
                                <div class="system-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Facturas del Mes</span>
                                        <strong class="text-info">245</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Management -->
                <div class="col-lg-8">
                    <!-- All Departments -->
                    <div class="content-box">
                        <div class="content-box-header d-flex justify-content-between align-items-center">
                            <h5>Todos los Departamentos</h5>
                            <div>
                                <button class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#agregarDepartamentoModal">
                                    <i class="fas fa-plus me-1"></i> Agregar
                                </button>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync-alt me-1"></i> Actualizar
                                </button>
                            </div>
                        </div>
                        <div class="content-box-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Departamento</th>
                                            <th>Edificio</th>
                                            <th>Propietario</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Departamento 101</td>
                                            <td>Bilbao</td>
                                            <td>Ana García López</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Departamento 302</td>
                                            <td>Bilbao</td>
                                            <td>Juan Pérez García</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Departamento 302</td>
                                            <td>Bilbao</td>
                                            <td>Juan Pérez García</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Departamento 302</td>
                                            <td>Bilbao</td>
                                            <td>Juan Pérez García</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Departamento 302</td>
                                            <td>Bilbao</td>
                                            <td>Juan Pérez García</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Departamento 302</td>
                                            <td>Bilbao</td>
                                            <td>Juan Pérez García</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Departamento 205</td>
                                            <td>Bilbao</td>
                                            <td>-</td>
                                            <td><span class="status-badge badge-warning">Vacante</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- System Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="info-card bg-gradient-celeste">
                                <div>
                                    <h3>142</h3>
                                    <p>Usuarios Totales</p>
                                </div>
                                <i class="fas fa-users icon"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card bg-gradient-verde">
                                <div>
                                    <h3>85</h3>
                                    <p>Deptos. Activos</p>
                                </div>
                                <i class="fas fa-building icon"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card bg-gradient-azul">
                                <div>
                                    <h3>12</h3>
                                    <p>Incidentes Activos</p>
                                </div>
                                <i class="fas fa-exclamation-triangle icon"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card bg-gradient-oscuro">
                                <div>
                                    <h3>45</h3>
                                    <p>Facturas Pendientes</p>
                                </div>
                                <i class="fas fa-file-invoice-dollar icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Management -->
            <div class="row mt-4 fade-in">
                <div class="col-12">
                    <div class="content-box">
                        <div class="content-box-header d-flex justify-content-between align-items-center">
                            <h5>Gestión de Usuarios</h5>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#agregarUsuarioModal">
                                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                            </button>
                        </div>
                        <div class="content-box-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Usuario</th>
                                            <th>Nombre Completo</th>
                                            <th>Email</th>
                                            <th>Departamentos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>juan.perez</td>
                                            <td>Juan Pérez García</td>
                                            <td>juan.perez@email.com</td>
                                            <td>2</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger me-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>ana.garcia</td>
                                            <td>Ana García López</td>
                                            <td>ana.garcia@email.com</td>
                                            <td>1</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger me-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Editar Perfil Admin -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil de Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPerfilAdmin">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editNombreAdmin" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombreAdmin" value="Máximo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editApellidoPaternoAdmin" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="editApellidoPaternoAdmin" value="Decimo" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editApellidoMaternoAdmin" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="editApellidoMaternoAdmin" value="Meridio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editCIAdmin" class="form-label">Carnet de Identidad</label>
                                <input type="text" class="form-control" id="editCIAdmin" value="99999999" required readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editFechaNacimientoAdmin" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="editFechaNacimientoAdmin" value="1980-01-01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editTelefonoAdmin" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="editTelefonoAdmin" value="000000000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editEmailAdmin" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmailAdmin" value="admin@gmail.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCargoAdmin" class="form-label">Cargo</label>
                            <input type="text" class="form-control" id="editCargoAdmin" value="Administrador" required>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-2"></i>Deje los campos de contraseña en blanco si no desea cambiarla</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPasswordAdmin" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="editPasswordAdmin" placeholder="••••••••">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editConfirmPasswordAdmin" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="editConfirmPasswordAdmin" placeholder="••••••••">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" style="background: var(--verde); border: none;" onclick="guardarCambiosAdmin()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gestión del Sistema -->
    <div class="modal fade" id="gestionSistemaModal" tabindex="-1" aria-labelledby="gestionSistemaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gestionSistemaModalLabel">Gestión del Sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Configuración General</h6>
                            <div class="mb-3">
                                <label class="form-label">Nombre del Sistema</label>
                                <input type="text" class="form-control" value="Sistema de Gestión de Edificios">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Moneda</label>
                                <select class="form-select">
                                    <option>Bolivianos (Bs.)</option>
                                    <option>Dólares ($)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Opciones de Seguridad</h6>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="twoFactorAuth" checked>
                                <label class="form-check-label" for="twoFactorAuth">Autenticación de dos factores</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sessionTimeout" checked>
                                <label class="form-check-label" for="sessionTimeout">Timeout de sesión automático</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" style="background: var(--azul); border: none;">Guardar Configuración</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .user-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--celeste) 0%, var(--verde) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            margin: 0 auto 20px;
        }
        
        .admin-stats {
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .system-item {
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .system-item:last-child {
            border-bottom: none;
        }
    </style>

    <script>
        function guardarCambiosAdmin() {
            const password = document.getElementById('editPasswordAdmin').value;
            const confirmPassword = document.getElementById('editConfirmPasswordAdmin').value;
            
            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            // Simular guardado exitoso
            setTimeout(() => {
                document.getElementById('nombreCompletoAdmin').textContent = 
                    document.getElementById('editNombreAdmin').value + ' ' + 
                    document.getElementById('editApellidoPaternoAdmin').value + ' ' + 
                    document.getElementById('editApellidoMaternoAdmin').value;
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal'));
                modal.hide();
                
                alert('Perfil de administrador actualizado correctamente');
            }, 1000);
        }
    </script>

<?php include("../../includes/footer.php");?>