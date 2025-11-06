
            </div>
        </main>
<!-- Footer Minimalista -->
<footer class="footer mt-auto py-2 bg-dark border-top">
    <div class="container-fluid text-center">
        <span class="text-light small">
            &copy; 2025 Sistema Edificio <i class="fas fa-building"></i> Bilbao   
        </span>
    </div>
</footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    

      <!-- Vincular el archivo JavaScript -->
  <script src="../../includes/js/dashboard.js"></script>
  
  <!-- Script para notificaciones -->
  <script>
      (function() {
          const btnNotifications = document.getElementById('btnNotifications');
          const notificationDropdown = document.getElementById('notificationDropdown');
          const notificationBody = document.getElementById('notificationBody');
          const notificationBadge = document.getElementById('notificationBadge');
          const btnCloseNotifications = document.getElementById('btnCloseNotifications');
          
          let comunicados = [];
          
          // Cargar comunicados
          function cargarComunicados() {
              fetch('../api/notificaciones.php')
                  .then(response => response.json())
                  .then(data => {
                      if (data.status === 200) {
                          comunicados = data.comunicados || [];
                          actualizarBadge(comunicados.length);
                          if (notificationDropdown && notificationDropdown.style.display !== 'none') {
                              mostrarComunicados(comunicados);
                          }
                      } else {
                          console.error('Error al cargar comunicados:', data.message);
                          actualizarBadge(0);
                      }
                  })
                  .catch(error => {
                      console.error('Error:', error);
                      actualizarBadge(0);
                  });
          }
          
          // Actualizar badge
          function actualizarBadge(total) {
              if (notificationBadge) {
                  notificationBadge.textContent = total;
                  if (total === 0) {
                      notificationBadge.style.display = 'none';
                  } else {
                      notificationBadge.style.display = 'flex';
                  }
              }
          }
          
          // Función auxiliar para escapar HTML
          function escapeHtml(text) {
              if (!text) return '';
              const div = document.createElement('div');
              div.textContent = text;
              return div.innerHTML;
          }
          
          // Mostrar comunicados en el dropdown
          function mostrarComunicados(comunicadosList) {
              if (!notificationBody) return;
              
              if (comunicadosList.length === 0) {
                  notificationBody.innerHTML = `
                      <div class="notification-empty">
                          <i class="fas fa-bell-slash"></i>
                          <p>No hay comunicados disponibles</p>
                      </div>
                  `;
                  return;
              }
              
              let html = '';
              comunicadosList.forEach(comunicado => {
                  const prioridadColor = comunicado.prioridad_color || 'secondary';
                  const prioridad = (comunicado.prioridad || 'media').toLowerCase();
                  const fechaRelativa = comunicado.fecha_relativa || comunicado.fecha_formateada || '';
                  const contenido = (comunicado.contenido || '').substring(0, 150);
                  const titulo = comunicado.titulo || 'Sin título';
                  
                  html += `
                      <div class="notification-item ${prioridad}">
                          <div class="notification-title">
                              <span class="notification-priority ${prioridadColor}">${comunicado.prioridad || 'media'}</span>
                              <span style="flex: 1;">${escapeHtml(titulo)}</span>
                          </div>
                          <div class="notification-content">${escapeHtml(contenido)}${contenido.length >= 150 ? '...' : ''}</div>
                          <div class="notification-meta">
                              <span><i class="fas fa-user"></i>${escapeHtml(comunicado.autor || 'Sistema')}</span>
                              <span><i class="fas fa-clock"></i>${fechaRelativa}</span>
                          </div>
                      </div>
                  `;
              });
              
              notificationBody.innerHTML = html;
          }
          
          // Toggle dropdown
          if (btnNotifications) {
              btnNotifications.addEventListener('click', function(e) {
                  e.stopPropagation();
                  const isVisible = notificationDropdown.style.display !== 'none';
                  
                  if (isVisible) {
                      notificationDropdown.style.display = 'none';
                  } else {
                      notificationDropdown.style.display = 'block';
                      if (comunicados.length === 0) {
                          cargarComunicados();
                      } else {
                          mostrarComunicados(comunicados);
                      }
                  }
              });
          }
          
          // Cerrar dropdown
          if (btnCloseNotifications) {
              btnCloseNotifications.addEventListener('click', function(e) {
                  e.stopPropagation();
                  notificationDropdown.style.display = 'none';
              });
          }
          
          // Cerrar al hacer clic fuera
          document.addEventListener('click', function(e) {
              if (notificationDropdown && btnNotifications &&
                  !notificationDropdown.contains(e.target) && 
                  !btnNotifications.contains(e.target)) {
                  notificationDropdown.style.display = 'none';
              }
          });
          
          // Cargar comunicados al iniciar
          if (document.readyState === 'loading') {
              document.addEventListener('DOMContentLoaded', cargarComunicados);
          } else {
              cargarComunicados();
          }
          
          // Actualizar cada 5 minutos
          setInterval(cargarComunicados, 300000);
      })();
  </script>

</body>
</html>