<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_socio.php';

// Obtener datos del socio
$socio_id = $_SESSION['usuario']['personal_id'];
$sql_socio = "SELECT * FROM personal WHERE id = ?";
$stmt_socio = $conn->prepare($sql_socio);
$stmt_socio->bind_param("i", $socio_id);
$stmt_socio->execute();
$result_socio = $stmt_socio->get_result();
$socio = $result_socio->fetch_assoc();

// Obtener préstamos activos
$sql_prestamos = "SELECT * FROM prestamos 
                 WHERE personal_id = ? AND estado = 'Aprobado'
                 ORDER BY fecha_solicitud DESC LIMIT 3";
$stmt_prestamos = $conn->prepare($sql_prestamos);
$stmt_prestamos->bind_param("i", $socio_id);
$stmt_prestamos->execute();
$result_prestamos = $stmt_prestamos->get_result();

// Obtener total ahorros
$sql_ahorros = "SELECT SUM(monto) as total_ahorros FROM ahorros WHERE personal_id = ?";
$stmt_ahorros = $conn->prepare($sql_ahorros);
$stmt_ahorros->bind_param("i", $socio_id);
$stmt_ahorros->execute();
$result_ahorros = $stmt_ahorros->get_result();
$ahorros = $result_ahorros->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Socio - FOAPUNP</title>
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
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Bienvenido, <?= htmlspecialchars($socio['nombres']) ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Mis Ahorros</h6>
                                        <p class="card-text display-4 text-success">S/ <?= number_format($ahorros['total_ahorros'] ?? 0, 2) ?></p>
                                        <a href="ahorros.php" class="btn btn-outline-success btn-sm">Ver detalle</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Préstamos Activos</h6>
                                        <p class="card-text display-4 text-primary"><?= $result_prestamos->num_rows ?></p>
                                        <a href="mis_prestamos.php" class="btn btn-outline-primary btn-sm">Ver mis préstamos</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Últimos Préstamos</h5>
                    </div>
                    <div class="card-body">
                        <?php if($result_prestamos->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Cuotas</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($prestamo = $result_prestamos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($prestamo['fecha_solicitud'])) ?></td>
                                        <td>S/ <?= number_format($prestamo['monto'], 2) ?></td>
                                        <td><?= $prestamo['cuotas'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $prestamo['estado'] == 'Aprobado' ? 'success' : 
                                                ($prestamo['estado'] == 'Rechazado' ? 'danger' : 'warning') ?>">
                                                <?= $prestamo['estado'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="detalle_prestamo.php?id=<?= $prestamo['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">No tienes préstamos registrados</div>
                        <a href="prestamos.php" class="btn btn-primary">Solicitar primer préstamo</a>
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