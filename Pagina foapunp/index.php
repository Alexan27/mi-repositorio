<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración básica
define('APP_NAME', 'FOAPUNP');
define('DEFAULT_REDIRECT', 'login.php');

// Verificar si el usuario está autenticado
if (isset($_SESSION['usuario'])) {
    // Redirigir según el rol del usuario
    switch ($_SESSION['usuario']['rol']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            exit();
        case 'socio':
            header("Location: socio/dashboard.php");
            exit();
        case 'empleado':
            header("Location: empleado/dashboard.php");
            exit();
        default:
            header("Location: " . DEFAULT_REDIRECT);
            exit();
    }
} else {
    // Página de inicio para usuarios no autenticados
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= APP_NAME ?> - Fondo de Ahorro Personal de Trabajadores UNP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .hero-section {
                background: linear-gradient(135deg, #003366 0%, #004080 100%);
                color: white;
                padding: 5rem 0;
                margin-bottom: 3rem;
            }
            .feature-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #003366;
            }
            .card-hover:hover {
                transform: translateY(-5px);
                transition: transform 0.3s ease;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>
    <body>
        <!-- Barra de navegación -->
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #003366;">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/images/logo_unp.png" alt="Logo UNP" height="40" class="d-inline-block align-top me-2">
                    <?= APP_NAME ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#beneficios">Beneficios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#como-funciona">Cómo funciona</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Sección Hero -->
        <section class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fw-bold mb-4">Fondo de Ahorro FOAPUNP</h1>
                <p class="lead mb-5">Sistema de ahorros y préstamos para los trabajadores de la Universidad Nacional de Piura</p>
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    <a href="login.php" class="btn btn-primary btn-lg px-4 gap-3">Acceder al Sistema</a>
                    <a href="#contacto" class="btn btn-outline-light btn-lg px-4">Más Información</a>
                </div>
            </div>
        </section>

        <!-- Beneficios -->
        <section id="beneficios" class="container mb-5">
            <h2 class="text-center mb-4">Beneficios para nuestros socios</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 card-hover">
                        <div class="card-body text-center">
                            <div class="feature-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h3 class="h4">Préstamos Accesibles</h3>
                            <p class="card-text">Obtén préstamos con tasas de interés preferenciales y plazos flexibles.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 card-hover">
                        <div class="card-body text-center">
                            <div class="feature-icon">
                                <i class="bi bi-piggy-bank"></i>
                            </div>
                            <h3 class="h4">Programa de Ahorros</h3>
                            <p class="card-text">Ahorra de manera voluntaria y obtén rendimientos atractivos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 card-hover">
                        <div class="card-body text-center">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="h4">Seguridad Financiera</h3>
                            <p class="card-text">Tu dinero está protegido y respaldado por la institución.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cómo funciona -->
        <section id="como-funciona" class="bg-light py-5">
            <div class="container">
                <h2 class="text-center mb-4">¿Cómo funciona el FOAPUNP?</h2>
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        ¿Quiénes pueden ser socios?
                                    </button>
                                </h3>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Todos los trabajadores de la UNP pueden ser socios del FOAPUNP, tanto docentes como administrativos.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        ¿Cómo solicito un préstamo?
                                    </button>
                                </h3>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Debes ser socio activo, iniciar sesión en el sistema y completar el formulario de solicitud de préstamo.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        ¿Cuáles son las tasas de interés?
                                    </button>
                                </h3>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Las tasas varían según el tipo de préstamo, pero son significativamente menores que las del mercado financiero tradicional.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="assets/images/finanzas.jpg" alt="Finanzas personales" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>

        <!-- Contacto -->
        <section id="contacto" class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Contacto</h2>
                <div class="row">
                    <div class="col-md-6">
                        <h3>Oficina FOAPUNP</h3>
                        <p><i class="bi bi-geo-alt"></i> Av. Ramón Mugica 131, Urb. San Eduardo - Piura</p>
                        <p><i class="bi bi-telephone"></i> (073) 123456</p>
                        <p><i class="bi bi-envelope"></i> foapunp@unp.edu.pe</p>
                        <p><i class="bi bi-clock"></i> Lunes a Viernes: 8:00 am - 4:00 pm</p>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">¿Necesitas ayuda?</h4>
                                <form>
                                    <div class="mb-3">
                                        <input type="text" class="form-control" placeholder="Nombre completo">
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" placeholder="Correo electrónico">
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" rows="3" placeholder="Mensaje"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pie de página -->
        <footer class="bg-dark text-white py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>FOAPUNP</h5>
                        <p>Fondo de Ahorro Personal de Trabajadores de la Universidad Nacional de Piura.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Enlaces rápidos</h5>
                        <ul class="list-unstyled">
                            <li><a href="login.php" class="text-white">Iniciar Sesión</a></li>
                            <li><a href="#beneficios" class="text-white">Beneficios</a></li>
                            <li><a href="#como-funciona" class="text-white">Preguntas Frecuentes</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Legal</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-white">Términos y condiciones</a></li>
                            <li><a href="#" class="text-white">Política de privacidad</a></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <p class="mb-0">&copy; <?= date('Y') ?> FOAPUNP - Todos los derechos reservados</p>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    </body>
    </html>
    <?php
}
?>