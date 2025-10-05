<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Perfil Administrativo</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Perfil</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary me-2" style="background: var(--verde); border: none;" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                        <i class="fas fa-edit me-2"></i> Editar Perfil
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
                            <h4 class="mb-2" id="nombreCompletoAdmin"><?php echo $_SESSION['nombre']." ".$_SESSION['appaterno']." ".$_SESSION['apmaterno'];?></h4>
                            <p class="text-muted mb-3"><?php echo $_SESSION['rol'];?></p>
                            <p class="text-muted mb-3"><?php echo $_SESSION['descripcion_rol'];?></p>
                            

                            <?php if($_SESSION['tipo_usuario'] == 'Personal'): ?>
                            
                            <div class="admin-stats">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h5 class="mb-1"><?php echo date('Y', time()) - date('Y', strtotime($_SESSION['fecha_contratacion'])); ?></h5>
                                        <small class="text-muted">Años de Servicio</small>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="mb-1"><?php echo $_SESSION['cargo'];?></h5>
                                        <small class="text-muted">Cargo</small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                
                             <?php if($_SESSION['tipo_usuario'] == 'Usuario'): ?>

                            <div class="admin-stats">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h5 class="mb-1">5</h5>
                                        <small class="text-muted">Pagos realizados</small>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="mb-1">3</h5>
                                        <small class="text-muted">Servicios Activos</small>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                
                        </div>
                    </div>


                    <!-- Contact Information -->
                    <div class="content-box mt-4">
                        <div class="content-box-header">
                            <h5>Información de Contacto</h5>
                        </div>
                        <div class="content-box-body">
                            <div class="contact-info">
                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope me-3 text-primary"></i>
                                        <div>
                                            <small class="text-muted">Email</small>
                                            <p class="mb-0" id="emailUsuario"><?php echo $_SESSION['email'];?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone me-3 text-success"></i>
                                        <div>
                                            <small class="text-muted">Teléfono</small>
                                            <p class="mb-0" id="telefonoUsuario"><?php echo $_SESSION['telefono'];?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card me-3 text-warning"></i>
                                        <div>
                                            <small class="text-muted">Carnet de Identidad</small>
                                            <p class="mb-0" id="ciUsuario"><?php echo $_SESSION['ci'];?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-birthday-cake me-3 text-info"></i>
                                        <div>
                                            <small class="text-muted">Fecha de Nacimiento</small>
                                            <p class="mb-0" id="fechaNacimiento"><?php echo $_SESSION['fecha_naci'];?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                </div>

                <!-- System Management -->
                <div class="col-lg-8">
                                        <?php if($_SESSION['tipo_usuario'] == 'Personal'): ?>
                            
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
                    <?php endif; ?>
                                        <?php if($_SESSION['tipo_usuario'] == 'Usuario'): ?>
                            
                    <!-- My Departments -->
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Mis Departamentos</h5>
                        </div>
                        <div class="content-box-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Departamento</th>
                                            <th>Edificio</th>
                                            <th>Estado</th>
                                            <th>Fecha Inicio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Departamento 302</td>
                                            <td>Bilbao</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>01/01/2023</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Departamento 105</td>
                                            <td>Bilbao</td>
                                            <td><span class="status-badge badge-success">Activo</span></td>
                                            <td>15/06/2023</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Activity -->
                    <div class="content-box mt-4">
                        <div class="content-box-header">
                            <h5>Mi Actividad Reciente</h5>
                        </div>
                        <div class="content-box-body">
                            <div class="activity-timeline">
                                <div class="timeline-item">
                                    <div class="timeline-time">Hace 2 horas</div>
                                    <div class="timeline-content">
                                        <strong>Pago realizado</strong>
                                        <p>Factura #FAC-7842 - Agua</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">Ayer</div>
                                    <div class="timeline-content">
                                        <strong>Incidente reportado</strong>
                                        <p>Problema eléctrico en departamento 302</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-time">Hace 3 días</div>
                                    <div class="timeline-content">
                                        <strong>Perfil actualizado</strong>
                                        <p>Información de contacto modificada</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

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