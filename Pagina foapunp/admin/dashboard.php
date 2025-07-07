<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_admin.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - FOAPUNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header_admin.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include '../includes/sidebar_admin.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Resumen del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Personal</h5>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM personal";
                                        $result = $conn->query($sql);
                                        $row = $result->fetch_assoc();
                                        ?>
                                        <p class="card-text display-4"><?= $row['total'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Socios Activos</h5>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM personal WHERE socio='SI'";
                                        $result = $conn->query($sql);
                                        $row = $result->fetch_assoc();
                                        ?>
                                        <p class="card-text display-4"><?= $row['total'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-warning mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Préstamos Pendientes</h5>
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estado='Pendiente'";
                                        $result = $conn->query($sql);
                                        $row = $result->fetch_assoc();
                                        ?>
                                        <p class="card-text display-4"><?= $row['total'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Últimos Préstamos Solicitados</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.id, per.apellidos, per.nombres, p.monto, p.fecha_solicitud, p.estado 
                                        FROM prestamos p
                                        JOIN personal per ON p.personal_id = per.id
                                        ORDER BY p.fecha_solicitud DESC LIMIT 5";
                                $result = $conn->query($sql);
                                
                                while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['apellidos'] . ', ' . $row['nombres'] ?></td>
                                    <td>S/ <?= number_format($row['monto'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['fecha_solicitud'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $row['estado'] == 'Aprobado' ? 'success' : 
                                            ($row['estado'] == 'Rechazado' ? 'danger' : 'warning') ?>">
                                            <?= $row['estado'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="aprobar_prestamo.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Ver</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>