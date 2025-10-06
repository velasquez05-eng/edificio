<?php include("../../includes/header.php");?>

<!-- Main Content -->
<main class="main-content">
    <!-- Jumbotron Principal -->
    <div class="jumbotron jumbotron-custom text-center">
        <div class="container">
            <div class="logo-container">
                <!-- Logo del edificio -->
                <img src="../../includes/img/logo.png" alt="Logo Torres del Parque" class="logo">
            </div>
            <h1 class="building-name"><?php echo htmlspecialchars($edificio['nombre'] ?? 'Torres del Parque'); ?></h1>
            <p class="location">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-geo-alt-fill location-icon" viewBox="0 0 16 16">
                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                </svg>
                <?php echo htmlspecialchars($edificio['direccion'] ?? 'Avenida Principal 123, Ciudad Central'); ?>
            </p>
            <a href="#" class="btn btn-custom">Conoce más</a>
        </div>
    </div>
    
    <!-- Sección de características -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="feature-title">Diseño Moderno</h3>
                    <p>Arquitectura contemporánea con acabados de primera calidad y espacios optimizados.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Seguridad 24/7</h3>
                    <p>Sistema de vigilancia avanzado con control de acceso y personal de seguridad las 24 horas.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Áreas Comunes</h3>
                    <p>Amplias zonas ajardinadas y áreas de recreación para disfrutar del entorno natural.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    .jumbotron-custom {
        background: linear-gradient(rgba(13, 61, 71, 0.8), rgba(42, 117, 149, 0.8)), 
                    url('../../includes/img/edificio.jpeg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 6rem 2rem;
        border-radius: 0;
        margin-bottom: 0;
    }
    
    .logo-container {
        background-color: rgba(175, 239, 206, 0.9);
        border-radius: 50%;
        width: 150px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .logo {
        max-width: 200px;
        max-height: 200px;
        border-radius: 50%;
    }
    
    .building-name {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 3.5rem;
        margin-bottom: 0rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .location {
        font-size: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .location-icon {
        margin-right: 10px;
        color: var(--celeste);
    }
    
    .btn-custom {
        background: linear-gradient(135deg, var(--celeste) 0%, var(--verde) 100%);
        border: none;
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s ease;
        color: var(--azul-oscuro);
    }
    
    .btn-custom:hover {
        background: linear-gradient(135deg, var(--verde) 0%, var(--azul) 100%);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        color: white;
    }
    
    .features-section {
        padding: 4rem 0;
        background-color: white;
    }
    
    .feature-icon {
        font-size: 2.5rem;
        color: var(--azul);
        margin-bottom: 1rem;
    }
    
    .feature-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--azul-oscuro);
    }
    
    @media (max-width: 768px) {
        .building-name {
            font-size: 2.5rem;
        }
        
        .location {
            font-size: 1.2rem;
        }
    }
</style>

<?php include("../../includes/footer.php");?>