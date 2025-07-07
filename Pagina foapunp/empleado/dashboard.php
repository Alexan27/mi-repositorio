<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_empleado.php';

$empleado_id = $_SESSION['usuario']['personal_id'];

// Obtener datos del empleado
$sql_empleado = "SELECT * FROM personal WHERE id = ?";
$stmt_empleado = $conn->prepare($sql_empleado);
$stmt_empleado->bind_param("i", $empleado_id);
$stmt_empleado->execute();
$result_empleado = $stmt_empleado->get_result();
$empleado = $result_empleado->fetch_assoc();

// Verificar si tiene solicitud de socio pendiente
$sql_solicitud = "SELECT * FROM solicitudes_socio WHERE personal_id = ? AND estado = 'Pendiente'";
$stmt_solicitud = $conn->prepare($sql_solicitud);
$stmt_solicitud->bind_param("i", $empleado_id);
$stmt_solicitud->execute();
$result_solicitud = $stmt_solicitud->get_result();
$tiene_solicitud_pendiente = $result_solicitud->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Empleado - FOAPUNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/header_empleado.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../includes/sidebar_empleado.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Bienvenido, <?= htmlspecialchars($empleado['nombres']) ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Estado de Socio</h6>
                                        <p class="card-text display-5">
                                            <span class="badge bg-<?= $empleado['socio'] == 'SI' ? 'success' : 'secondary' ?>">
                                                <?= $empleado['socio'] ?>
                                            </span>
                                        </p>
                                        <?php if($empleado['socio'] == 'NO'): ?>
                                        <a href="solicitar_socio.php" class="btn btn-sm btn-primary">
                                            <?= $tiene_solicitud_pendiente ? 'Ver Solicitud' : 'Ser Socio' ?>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Beneficios FOAPUNP</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="bi bi-check-circle text-success"></i> Préstamos con bajos intereses</li>
                                            <li><i class="bi bi-check-circle text-success"></i> Programa de ahorros</li>
                                            <li><i class="bi bi-check-circle text-success"></i> Asesoría financiera</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Noticias y Anuncios</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql_anuncios = "SELECT * FROM anuncios 
                                        WHERE (publico = 'Todos' OR publico = 'Empleados')
                                        ORDER BY fecha_publicacion DESC LIMIT 3";
                        $result_anuncios = $conn->query($sql_anuncios);
                        
                        if ($result_anuncios->num_rows > 0):
                        while($anuncio = $result_anuncios->fetch_assoc()):
                        ?>
                        <div class="mb-4 pb-3 border-bottom">
                            <h5><?= htmlspecialchars($anuncio['titulo']) ?></h5>
                            <p class="text-muted">
                                <small>
                                    Publicado: <?= date('d/m/Y', strtotime($anuncio['fecha_publicacion'])) ?>
                                </small>
                            </p>
                            <p><?= nl2br(htmlspecialchars($anuncio['contenido'])) ?></p>
                            <?php if(!empty($anuncio['archivo'])): ?>
                            <a href="../uploads/anuncios/<?= $anuncio['archivo'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-download"></i> Descargar Archivo
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php
                        endwhile;
                        else:
                        ?>
                        <div class="alert alert-info">No hay anuncios recientes.</div>
                        <?php endif; ?>
                        
                        <a href="../anuncios.php" class="btn btn-primary">Ver todos los anuncios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>