<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_socio.php';

$socio_id = $_SESSION['usuario']['personal_id'];

// Obtener todos los préstamos del socio
$sql = "SELECT * FROM prestamos WHERE personal_id = ? ORDER BY fecha_solicitud DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $socio_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Préstamos - FOAPUNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header_socio.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../includes/sidebar_socio.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Mis Préstamos</h5>
                        <a href="prestamos.php" class="btn btn-light btn-sm">Solicitar Nuevo</a>
                    </div>
                    <div class="card-body">
                        <?php if($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Cuotas</th>
                                        <th>Cuota Mensual</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($prestamo = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($prestamo['fecha_solicitud'])) ?></td>
                                        <td>S/ <?= number_format($prestamo['monto'], 2) ?></td>
                                        <td><?= $prestamo['cuotas'] ?></td>
                                        <td>S/ <?= number_format($prestamo['cuota_mensual'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $prestamo['estado'] == 'Aprobado' ? 'success' : 
                                                ($prestamo['estado'] == 'Rechazado' ? 'danger' : 'warning') ?>">
                                                <?= $prestamo['estado'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="detalle_prestamo.php?id=<?= $prestamo['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                            <?php if($prestamo['estado'] == 'Pendiente'): ?>
                                            <a href="cancelar_prestamo.php?id=<?= $prestamo['id'] ?>" class="btn btn-sm btn-danger">Cancelar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No tienes préstamos registrados. <a href="prestamos.php" class="alert-link">Solicita tu primer préstamo</a>.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>