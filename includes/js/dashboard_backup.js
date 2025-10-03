
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

        // User Dropdown
        document.querySelector('.user-profile').addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            document.querySelector('.user-profile').classList.remove('active');
        });

        // Menu items toggle - MEJORADO
        document.querySelectorAll('.nav-link:not(.nav-treeview .nav-link)').forEach(link => {
            link.addEventListener('click', function(e) {
                // Si el sidebar está colapsado, expandirlo al hacer clic en cualquier menú
                if (document.querySelector('.sidebar').classList.contains('collapsed')) {
                    e.preventDefault();
                    document.querySelector('.sidebar').classList.remove('collapsed');
                    document.querySelector('.main-content').classList.remove('expanded');
                    document.querySelector('.sidebar-toggle i').classList.replace('fa-chevron-left', 'fa-chevron-right');
                    
                    // Esperar a que se expanda el sidebar antes de mostrar el submenú
                    setTimeout(() => {
                        if (this.nextElementSibling && this.nextElementSibling.classList.contains('nav-treeview')) {
                            const treeview = this.nextElementSibling;
                            const isShowing = treeview.classList.contains('show');
                            
                            // Close all other treeviews
                            document.querySelectorAll('.nav-treeview').forEach(tv => {
                                tv.classList.remove('show');
                            });
                            
                            // Remove active from all parent links
                            document.querySelectorAll('.nav-link').forEach(navLink => {
                                navLink.classList.remove('active');
                            });
                            
                            // Toggle current treeview
                            if (!isShowing) {
                                treeview.classList.add('show');
                                this.classList.add('active');
                            }
                            
                            // Rotate arrow
                            const arrow = this.querySelector('.nav-arrow');
                            if (arrow) {
                                if (!isShowing) {
                                    arrow.style.transform = 'rotate(90deg)';
                                } else {
                                    arrow.style.transform = 'rotate(0deg)';
                                }
                            }
                        }
                    }, 300);
                    return;
                }
                
                // Comportamiento normal cuando el sidebar está expandido
                if (this.nextElementSibling && this.nextElementSibling.classList.contains('nav-treeview')) {
                    e.preventDefault();
                    const treeview = this.nextElementSibling;
                    const isShowing = treeview.classList.contains('show');
                    
                    // Close all other treeviews
                    document.querySelectorAll('.nav-treeview').forEach(tv => {
                        tv.classList.remove('show');
                    });
                    
                    // Remove active from all parent links
                    document.querySelectorAll('.nav-link').forEach(navLink => {
                        navLink.classList.remove('active');
                    });
                    
                    // Toggle current treeview
                    if (!isShowing) {
                        treeview.classList.add('show');
                        this.classList.add('active');
                    }
                    
                    // Rotate arrow
                    const arrow = this.querySelector('.nav-arrow');
                    if (arrow) {
                        if (!isShowing) {
                            arrow.style.transform = 'rotate(90deg)';
                        } else {
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    }
                }
            });
        });

        // Incidents Chart
        const incidentsCtx = document.getElementById('incidentsChart').getContext('2d');
        const incidentsChart = new Chart(incidentsCtx, {
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

        document.querySelectorAll('.content-box, .info-card').forEach(el => {
            observer.observe(el);
        });