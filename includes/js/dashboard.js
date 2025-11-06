// Toggle Sidebar - MEJORADO
document.querySelector('.sidebar-toggle').addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const icon = this.querySelector('i');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
    
    // Rotate the toggle icon correctly
    if (sidebar.classList.contains('collapsed')) {
        icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
    } else {
        icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
    }
});

// Mobile Menu Toggle
document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('mobile-open');
});

// User Dropdown - MODIFICADO para no interferir con modales
document.querySelector('.user-profile').addEventListener('click', function(e) {
    // No activar si es un clic en un enlace que abre modal
    if (!e.target.closest('a[data-bs-toggle="modal"]')) {
        e.stopPropagation();
        this.classList.toggle('active');
    }
});

// Close dropdown when clicking outside - MODIFICADO
document.addEventListener('click', function(e) {
    // No cerrar si es clic en modal o enlace de modal
    if (!e.target.closest('.modal') && !e.target.closest('a[data-bs-toggle="modal"]')) {
        document.querySelector('.user-profile').classList.remove('active');
    }
});

// Cachear elementos del DOM para mejor rendimiento
const sidebar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.main-content');
const sidebarToggleIcon = document.querySelector('.sidebar-toggle i');

// Menu items toggle - OPTIMIZADO para mejor rendimiento
document.addEventListener('click', function(e) {
    const link = e.target.closest('.nav-link:not(.nav-treeview .nav-link)');
    if (!link) return;
    
    // No procesar si el clic viene de un enlace dentro del submenú
    if (e.target.closest('.nav-treeview')) {
        return;
    }
    
    // Permitir que los enlaces con data-bs-toggle (modales) funcionen normalmente
    if (link.hasAttribute('data-bs-toggle') || link.closest('[data-bs-toggle]')) {
        return; // Dejar que Bootstrap maneje el modal
    }
    
    // Si el sidebar está colapsado, expandirlo al hacer clic en cualquier menú
    if (sidebar && sidebar.classList.contains('collapsed')) {
        e.preventDefault();
        sidebar.classList.remove('collapsed');
        if (mainContent) mainContent.classList.remove('expanded');
        if (sidebarToggleIcon) sidebarToggleIcon.classList.replace('fa-chevron-left', 'fa-chevron-right');
        
        // Reducir delay para mejor respuesta (de 300ms a 150ms)
        setTimeout(() => {
            const treeview = link.nextElementSibling;
            if (treeview && treeview.classList.contains('nav-treeview')) {
                const isShowing = treeview.classList.contains('show');
                
                // Solo cerrar otros treeviews si hay alguno abierto (optimización)
                if (!isShowing) {
                    // Cachear queries
                    const allTreeviews = document.querySelectorAll('.nav-treeview');
                    const allParentLinks = document.querySelectorAll('.nav-link:not(.nav-treeview .nav-link)');
                    
                    // Close all other treeviews
                    allTreeviews.forEach(tv => {
                        if (tv !== treeview) {
                            tv.classList.remove('show');
                        }
                    });
                    
                    // Remove active from all parent links except current
                    allParentLinks.forEach(navLink => {
                        if (navLink !== link) {
                            navLink.classList.remove('active');
                        }
                    });
                    
                    // Abrir el menú actual
                    treeview.classList.add('show');
                    link.classList.add('active');
                    
                    // Rotate arrow
                    const arrow = link.querySelector('.nav-arrow');
                    if (arrow) {
                        arrow.style.transform = 'rotate(90deg)';
                    }
                }
            }
        }, 150); // Reducido de 300ms a 150ms
        return;
    }
    
    // Comportamiento normal cuando el sidebar está expandido
    const treeview = link.nextElementSibling;
    if (treeview && treeview.classList.contains('nav-treeview')) {
        e.preventDefault();
        const isShowing = treeview.classList.contains('show');
        
        // Solo hacer cambios si es necesario (optimización)
        if (!isShowing) {
            // Cachear queries para mejor rendimiento
            const allTreeviews = document.querySelectorAll('.nav-treeview');
            const allParentLinks = document.querySelectorAll('.nav-link:not(.nav-treeview .nav-link)');
            
            // Close all other treeviews
            allTreeviews.forEach(tv => {
                if (tv !== treeview) {
                    tv.classList.remove('show');
                }
            });
            
            // Remove active from all parent links except current
            allParentLinks.forEach(navLink => {
                if (navLink !== link) {
                    navLink.classList.remove('active');
                }
            });
            
            // Abrir el menú actual
            treeview.classList.add('show');
            link.classList.add('active');
            
            // Rotate arrow
            const arrow = link.querySelector('.nav-arrow');
            if (arrow) {
                arrow.style.transform = 'rotate(90deg)';
            }
        } else {
            // Si ya está abierto, cerrarlo
            treeview.classList.remove('show');
            link.classList.remove('active');
            
            // Rotate arrow
            const arrow = link.querySelector('.nav-arrow');
            if (arrow) {
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    }
});

// Prevenir que los enlaces del submenú cierren el menú
// Optimizado: solo verificar si el menú necesita mantenerse abierto (ya está abierto desde PHP)
document.addEventListener('click', function(e) {
    const submenuLink = e.target.closest('.nav-treeview .nav-link');
    if (!submenuLink) return;
    
    // Detener la propagación para evitar que cierre el menú padre
    e.stopPropagation();
    
    // El menú ya está abierto desde PHP, solo asegurar que permanezca abierto
    const treeview = submenuLink.closest('.nav-treeview');
    if (treeview && !treeview.classList.contains('show')) {
        // Solo si por alguna razón no está abierto, abrirlo
        const parentNavItem = treeview.parentElement;
        if (parentNavItem && parentNavItem.classList.contains('nav-item')) {
            const parentLink = parentNavItem.querySelector('> .nav-link');
            if (parentLink) {
                treeview.classList.add('show');
                parentLink.classList.add('active');
                const arrow = parentLink.querySelector('.nav-arrow');
                if (arrow) {
                    arrow.style.transform = 'rotate(90deg)';
                }
            }
        }
    }
    
    // Si el enlace tiene un href válido (no es #), permitir la navegación normal
    const href = submenuLink.getAttribute('href');
    if (href && href !== '#') {
        return true; // Permitir navegación
    }
    
    // Si es un enlace sin href o con #, prevenir el comportamiento por defecto
    e.preventDefault();
});

// NOTA: La detección de página actual y activación del menú ahora se maneja desde PHP en el header
// Esto es más rápido y eficiente ya que el menú se renderiza correctamente desde el servidor
// El código JavaScript solo maneja los eventos de interacción del usuario

// Incidents Chart - Solo crear si el elemento existe
const incidentsCtx = document.getElementById('incidentsChart');
let incidentsChart = null;
if (incidentsCtx) {
    incidentsChart = new Chart(incidentsCtx.getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Planta Baja', 'Primer Piso', 'Segundo Piso', 'Tercer Piso', 'Cuarto Piso', 'Quinto Piso'],
        datasets: [{
            label: 'Incidentes Reportados',
            data: [3, 5, 7, 4, 6, 2],
            backgroundColor: [
                'rgba(54, 137, 121, 0.7)',
                'rgba(54, 137, 121, 0.7)',
                'rgba(54, 137, 121, 0.7)',
                'rgba(54, 137, 121, 0.7)',
                'rgba(54, 137, 121, 0.7)',
                'rgba(54, 137, 121, 0.7)'
            ],
            borderColor: [
                'rgba(54, 137, 121, 1)',
                'rgba(54, 137, 121, 1)',
                'rgba(54, 137, 121, 1)',
                'rgba(54, 137, 121, 1)',
                'rgba(54, 137, 121, 1)',
                'rgba(54, 137, 121, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false
                },
                title: {
                    display: true,
                    text: 'Número de Incidentes'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
    });
}

// Add animation to cards on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

// Solo ejecutar el observer si hay elementos (evitar trabajo innecesario)
const animatedElements = document.querySelectorAll('.content-box, .info-card');
if (animatedElements.length > 0) {
    animatedElements.forEach(el => {
        observer.observe(el);
    });
}