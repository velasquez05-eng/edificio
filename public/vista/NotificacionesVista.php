<?php include("../../includes/header.php");?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <div class="page-title">
                <h1>Centro de Notificaciones</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notificaciones</li>
                    </ol>
                </nav>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaNotificacion">
                    <i class="fas fa-bell me-2"></i> Nueva Notificación
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row fade-in">
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-celeste">
                    <div>
                        <h3>12</h3>
                        <p>No Leídas</p>
                    </div>
                    <i class="fas fa-envelope icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-verde">
                    <div>
                        <h3>45</h3>
                        <p>Leídas</p>
                    </div>
                    <i class="fas fa-envelope-open icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-azul">
                    <div>
                        <h3>8</h3>
                        <p>Importantes</p>
                    </div>
                    <i class="fas fa-exclamation-circle icon"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-card bg-gradient-oscuro">
                    <div>
                        <h3>65</h3>
                        <p>Total</p>
                    </div>
                    <i class="fas fa-bell icon"></i>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Filtrar Notificaciones</h5>
                    </div>
                    <div class="content-box-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <select class="form-select">
                                    <option>Todas las notificaciones</option>
                                    <option>No leídas</option>
                                    <option>Leídas</option>
                                    <option>Importantes</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select">
                                    <option>Todos los tipos</option>
                                    <option>Alertas de Pago</option>
                                    <option>Incidentes</option>
                                    <option>Mantenimiento</option>
                                    <option>Comunicados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" placeholder="Desde">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100">Aplicar Filtros</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Notificaciones -->
        <div class="row fade-in">
            <div class="col-12">
                <div class="content-box">
                    <div class="content-box-header">
                        <h5>Notificaciones Recientes</h5>
                        <div class="box-actions">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check-double me-1"></i> Marcar Todas Leídas
                            </button>
                        </div>
                    </div>
                    <div class="content-box-body">
                        <div class="notifications-list">
                            <!-- Notificación No Leída -->
                            <div class="notification-item unread" data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion">
                                <div class="notification-icon bg-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Alerta de Pago Vencido</h6>
                                    <p>El departamento 302 tiene una factura pendiente desde hace 5 días</p>
                                    <small class="text-muted">Hace 2 horas</small>
                                </div>
                                <div class="notification-actions">
                                    <span class="badge bg-danger">Importante</span>
                                </div>
                            </div>

                            <!-- Notificación Leída -->
                            <div class="notification-item" data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion">
                                <div class="notification-icon bg-warning">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Incidente en Proceso</h6>
                                    <p>El incidente #INC-7842 está siendo atendido por Juan Pérez</p>
                                    <small class="text-muted">Hace 5 horas</small>
                                </div>
                                <div class="notification-actions">
                                    <span class="badge bg-warning">En Proceso</span>
                                </div>
                            </div>

                            <!-- Notificación Leída -->
                            <div class="notification-item" data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion">
                                <div class="notification-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Incidente Resuelto</h6>
                                    <p>El incidente #INC-7841 ha sido marcado como resuelto</p>
                                    <small class="text-muted">Ayer, 15:30</small>
                                </div>
                                <div class="notification-actions">
                                    <span class="badge bg-success">Resuelto</span>
                                </div>
                            </div>

                            <!-- Notificación Leída -->
                            <div class="notification-item" data-bs-toggle="modal" data-bs-target="#modalDetalleNotificacion">
                                <div class="notification-icon bg-info">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <h6>Corte Programado de Agua</h6>
                                    <p>Este viernes 15 de marzo habrá corte de agua de 8:00 AM a 12:00 PM</p>
                                    <small class="text-muted">15/03/2024</small>
                                </div>
                                <div class="notification-actions">
                                    <span class="badge bg-info">Información</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Nueva Notificación -->
<div class="modal fade" id="modalNuevaNotificacion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nueva Notificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" placeholder="Ingrese el título de la notificación" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensaje</label>
                        <textarea class="form-control" rows="4" placeholder="Escriba el mensaje de la notificación..." required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option>Alerta de Pago</option>
                                    <option>Incidente</option>
                                    <option>Mantenimiento</option>
                                    <option>Comunicado General</option>
                                    <option>Información</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prioridad</label>
                                <select class="form-select" required>
                                    <option value="">Seleccionar prioridad</option>
                                    <option>Baja</option>
                                    <option>Normal</option>
                                    <option>Alta</option>
                                    <option>Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Destinatarios</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="todosUsuarios">
                            <label class="form-check-label" for="todosUsuarios">
                                Todos los usuarios
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="personalAdmin">
                            <label class="form-check-label" for="personalAdmin">
                                Personal administrativo
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="departamentosEspecificos">
                            <label class="form-check-label" for="departamentosEspecificos">
                                Departamentos específicos
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Notificación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalle Notificación -->
<div class="modal fade" id="modalDetalleNotificacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Notificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Alerta de Pago Vencido</strong>
                </div>
                
                <div class="mb-3">
                    <strong>Mensaje:</strong>
                    <p class="mt-2">El departamento 302 tiene una factura pendiente desde hace 5 días. El monto adeudado es de ₡45,200. Se recomienda contactar al residente para coordinar el pago.</p>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <strong>Fecha Envío:</strong>
                        <p>15/03/2024 14:30</p>
                    </div>
                    <div class="col-6">
                        <strong>Prioridad:</strong>
                        <p><span class="badge bg-danger">Alta</span></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Destinatarios:</strong>
                    <p>Administración y Personal Financiero</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Marcar como Leída</button>
                <button type="button" class="btn btn-outline-danger">Archivar</button>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f8ff;
    border-left: 4px solid #4e73df;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-content h6 {
    margin: 0;
    font-weight: 600;
}

.notification-content p {
    margin: 0.25rem 0;
    color: #6c757d;
}

.notification-actions {
    margin-left: auto;
}
</style>

<?php include("../../includes/footer.php");?>