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

// Verificar si ya es socio
if ($empleado['socio'] == 'SI') {
    header("Location: dashboard.php");
    exit();
}

// Verificar si ya tiene solicitud
$sql_solicitud = "SELECT * FROM solicitudes_socio WHERE personal_id = ? ORDER BY fecha_solicitud DESC LIMIT 1";
$stmt_solicitud = $conn->prepare($sql_solicitud);
$stmt_solicitud->bind_param("i", $empleado_id);
$stmt_solicitud->execute();
$result_solicitud = $stmt_solicitud->get_result();
$solicitud = $result_solicitud->fetch_assoc();

// Procesar formulario de solicitud
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$solicitud) {
    $motivo = $_POST['motivo'];
    
    $sql = "INSERT INTO solicitudes_socio (personal_id, motivo, estado) 
            VALUES (?, ?, 'Pendiente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $empleado_id, $motivo);
    
    if ($stmt->execute()) {
        $success = "Solicitud enviada correctamente. Será revisada por el comité.";
        // Actualizar la variable $solicitud
        $solicitud = ['fecha_solicitud' => date('Y-m-d H:i:s'), 'estado' => 'Pendiente'];
    } else {
        $error = "Error al enviar la solicitud: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar ser Socio - FOAPUNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header_empleado.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../includes/sidebar_empleado.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Solicitud para ser Socio FOAPUNP</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if($solicitud): ?>
                        <div class="alert alert-info">
                            <h5>Estado de tu solicitud</h5>
                            <p><strong>Fecha de solicitud:</strong> <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?></p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-<?= $solicitud['estado'] == 'Aprobado' ? 'success' : 
                                                     ($solicitud['estado'] == 'Rechazado' ? 'danger' : 'warning') ?>">
                                    <?= $solicitud['estado'] ?>
                                </span>
                            </p>
                            <?php if(!empty($solicitud['comentarios'])): ?>
                            <p><strong>Comentarios:</strong> <?= nl2br(htmlspecialchars($solicitud['comentarios'])) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="mt-4">Beneficios de ser Socio</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="bi bi-cash-stack"></i> Préstamos
                                        </h6>
                                        <p class="card-text">Acceso a préstamos con tasas de interés preferenciales.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="bi bi-piggy-bank"></i> Ahorros
                                        </h6>
                                        <p class="card-text">Programa de ahorros con rendimientos atractivos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="bi bi-people"></i> Comunidad
                                        </h6>
                                        <p class="card-text">Participación en actividades y beneficios exclusivos.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php else: ?>
                        <div class="mb-4">
                            <h5>¿Por qué ser socio FOAPUNP?</h5>
                            <p>Como socio del Fondo de Ahorro Personal de Trabajadores de la UNP, tendrás acceso a:</p>
                            <ul>
                                <li>Préstamos con tasas de interés preferenciales</li>
                                <li>Programa de ahorros voluntarios</li>
                                <li>Asesoría financiera personalizada</li>
                                <li>Descuentos en convenios institucionales</li>
                            </ul>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="motivo" class="form-label">Motivo para ser socio</label>
                                <textarea class="form-control" id="motivo" name="motivo" rows="5" required
                                          placeholder="Explique por qué desea ser socio del FOAPUNP y cómo planea beneficiarse del fondo"></textarea>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="aceptoTerminos" required>
                                <label class="form-check-label" for="aceptoTerminos">
                                    Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#terminosModal">términos y condiciones</a> para ser socio FOAPUNP
                                </label>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Términos y Condiciones -->
    <div class="modal fade" id="terminosModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Términos y Condiciones para Socios FOAPUNP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Requisitos para ser socio:</h6>
                    <ul>
                        <li>Ser trabajador activo de la UNP</li>
                        <li>Aceptar el reglamento interno del FOAPUNP</li>
                        <li>Autorizar el descuento por planilla de las cuotas de ahorro</li>
                    </ul>
                    
                    <h6>2. Derechos del socio:</h6>
                    <ul>
                        <li>Acceder a préstamos según disponibilidad de fondos</li>
                        <li>Participar en la asamblea general de socios</li>
                        <li>Recibir rendimientos por sus ahorros</li>
                        <li>Acceder a los beneficios establecidos</li>
                    </ul>
                    
                    <h6>3. Obligaciones del socio:</h6>
                    <ul>
                        <li>Mantener al día sus aportaciones</li>
                        <li>Cumplir con los pagos de préstamos oportunamente</li>
                        <li>Informar cualquier cambio de datos personales</li>
                    </ul>
                    
                    <h6>4. Penalidades:</h6>
                    <p>El incumplimiento de las obligaciones puede resultar en la suspensión temporal o permanente de los beneficios.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>