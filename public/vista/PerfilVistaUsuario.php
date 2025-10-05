<?php include("../../includes/header.php");?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <div class="page-title">
                    <h1>Mi Perfil</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" style="background: var(--verde); border: none;" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                        <i class="fas fa-edit me-2"></i> Editar Perfil
                    </button>
                </div>
            </div>

            <!-- User Profile Section -->
            <div class="row fade-in">
                <!-- User Info Card -->
                <div class="col-lg-4">
                    <div class="content-box">
                        <div class="content-box-header">
                            <h5>Información Personal</h5>
                        </div>
                        <div class="content-box-body text-center">
                            <div class="user-avatar-large mb-3 mx-auto">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4 class="mb-2" id="nombreCompleto">Juan Pérez García</h4>
                            <p class="text-muted mb-3">Propietario</p>
                            
                            <div class="user-stats">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h5 class="mb-1" id="totalDepartamentos">2</h5>
                                        <small class="text-muted">Departamentos</small>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="mb-1" id="incidentesActivos">1</h5>
                                        <small class="text-muted">Incidentes</small>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="mb-1" id="facturasPendientes">2</h5>
                                        <small class="text-muted">Pendientes</small>
                                    </div>
                                </div>
                            </div>
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
                                            <p class="mb-0" id="emailUsuario">juan.perez@email.com</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone me-3 text-success"></i>
                                        <div>
                                            <small class="text-muted">Teléfono</small>
                                            <p class="mb-0" id="telefonoUsuario">+591 76543210</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card me-3 text-warning"></i>
                                        <div>
                                            <small class="text-muted">Carnet de Identidad</small>
                                            <p class="mb-0" id="ciUsuario">12345678</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-birthday-cake me-3 text-info"></i>
                                        <div>
                                            <small class="text-muted">Fecha de Nacimiento</small>
                                            <p class="mb-0" id="fechaNacimiento">15/03/1985</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-lg-8">
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

                    <!-- Quick Stats -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="info-card bg-gradient-celeste">
                                <div>
                                    <h3>24</h3>
                                    <p>Pagos Realizados</p>
                                </div>
                                <i class="fas fa-receipt icon"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-gradient-verde">
                                <div>
                                    <h3>3</h3>
                                    <p>Servicios Activos</p>
                                </div>
                                <i class="fas fa-bolt icon"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-gradient-azul">
                                <div>
                                    <h3>245</h3>
                                    <p>Días en el Sistema</p>
                                </div>
                                <i class="fas fa-calendar-alt icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPerfilModalLabel">Editar Mi Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPerfil">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombre" value="Juan" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editApellidoPaterno" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="editApellidoPaterno" value="Pérez" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editApellidoMaterno" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="editApellidoMaterno" value="García" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editCI" class="form-label">Carnet de Identidad</label>
                                <input type="text" class="form-control" id="editCI" value="12345678" required readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editFechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="editFechaNacimiento" value="1985-03-15" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editTelefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="editTelefono" value="76543210" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" value="juan.perez@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="editUsername" value="juan.perez" required>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-2"></i>Deje los campos de contraseña en blanco si no desea cambiarla</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPassword" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="editPassword" placeholder="••••••••">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editConfirmPassword" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="editConfirmPassword" placeholder="••••••••">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" style="background: var(--verde); border: none;" onclick="guardarCambios()">Guardar Cambios</button>
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
        
        .user-stats {
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .contact-item {
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .contact-item:last-child {
            border-bottom: none;
        }
        
        .contact-item i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
    </style>

    <script>
        function guardarCambios() {
            const password = document.getElementById('editPassword').value;
            const confirmPassword = document.getElementById('editConfirmPassword').value;
            
            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            // Simular guardado exitoso
            setTimeout(() => {
                document.getElementById('nombreCompleto').textContent = 
                    document.getElementById('editNombre').value + ' ' + 
                    document.getElementById('editApellidoPaterno').value + ' ' + 
                    document.getElementById('editApellidoMaterno').value;
                
                document.getElementById('emailUsuario').textContent = document.getElementById('editEmail').value;
                document.getElementById('telefonoUsuario').textContent = document.getElementById('editTelefono').value;
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal'));
                modal.hide();
                
                alert('Perfil actualizado correctamente');
            }, 1000);
        }
    </script>

<?php include("../../includes/footer.php");?>