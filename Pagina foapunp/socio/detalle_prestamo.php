<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_socio.php';

$prestamo_id = $_GET['id'] ?? 0;
$socio_id = $_SESSION['usuario']['personal_id'];

// Obtener información del préstamo
$sql = "SELECT p.*, per.apellidos, per.nombres 
        FROM prestamos p
        JOIN personal per ON p.personal_id = per.id
        WHERE p.id = ? AND p.personal_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $prestamo_id, $socio_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: mis_prestamos.php");
    exit();
}

$prestamo = $result->fetch_assoc();

// Obtener pagos del préstamo
$sql_pagos = "SELECT * FROM pagos WHERE prestamo_id = ? ORDER BY fecha_pago DESC";
$stmt_pagos = $conn->prepare($sql_pagos);
$stmt_pagos->bind_param("i", $prestamo_id);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();

// Calcular saldo pendiente
$total_pagado = 0;
while ($pago = $result_pagos->fetch_assoc()) {
    $total_pagado += $pago['monto'];
}
$saldo_pendiente = $prestamo['monto'] + ($prestamo['monto'] * $prestamo['tasa_interes'] / 100) - $total_pagado;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Préstamo - FOAPUNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        .prestamo-header {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .estado-badge {
            font-size: 1rem;
            padding: 0.5em 1em;
        }
        .tabla-pagos th {
            background-color: #198754;
            color: white;
        }
    </style>
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
                        <h5 class="mb-0">Detalle del Préstamo #<?= $prestamo['id'] ?></h5>
                        <span class="badge estado-badge bg-<?= 
                            $prestamo['estado'] == 'Aprobado' ? 'success' : 
                            ($prestamo['estado'] == 'Rechazado' ? 'danger' : 'warning') ?>">
                            <?= $prestamo['estado'] ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="prestamo-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Socio:</strong> <?= htmlspecialchars($prestamo['apellidos']) ?>, <?= htmlspecialchars($prestamo['nombres']) ?></p>
                                    <p><strong>Fecha de solicitud:</strong> <?= date('d/m/Y', strtotime($prestamo['fecha_solicitud'])) ?></p>
                                    <?php if($prestamo['estado'] == 'Aprobado'): ?>
                                    <p><strong>Fecha de aprobación:</strong> <?= date('d/m/Y', strtotime($prestamo['fecha_aprobacion'])) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Monto:</strong> S/ <?= number_format($prestamo['monto'], 2) ?></p>
                                    <p><strong>Tasa de interés:</strong> <?= $prestamo['tasa_interes'] ?>% anual</p>
                                    <p><strong>Cuota mensual:</strong> S/ <?= number_format($prestamo['cuota_mensual'], 2) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Pagado</h6>
                                        <p class="card-text display-5">S/ <?= number_format($total_pagado, 2) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Saldo Pendiente</h6>
                                        <p class="card-text display-5">S/ <?= number_format($saldo_pendiente, 2) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-primary mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Próximo Pago</h6>
                                        <p class="card-text display-5"><?= date('d/m/Y', strtotime('+1 month', strtotime($prestamo['fecha_aprobacion']))) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Historial de Pagos</h5>
                        <?php if($result_pagos->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped tabla-pagos">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Referencia</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $result_pagos->data_seek(0); // Reiniciar el puntero del resultado
                                    while($pago = $result_pagos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?></td>
                                        <td>S/ <?= number_format($pago['monto'], 2) ?></td>
                                        <td><?= $pago['metodo_pago'] ?></td>
                                        <td><?= $pago['referencia'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $pago['estado'] == 'Completo' ? 'success' : 'warning' ?>">
                                                <?= $pago['estado'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">No se han registrado pagos para este préstamo.</div>
                        <?php endif; ?>

                        <?php if($prestamo['estado'] == 'Aprobado'): ?>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <a href="documentos.php?prestamo_id=<?= $prestamo['id'] ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-text"></i> Ver Documentos
                            </a>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pagoModal">
                                <i class="bi bi-cash"></i> Registrar Pago
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para registrar pago -->
    <div class="modal fade" id="pagoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="registrar_pago.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="prestamo_id" value="<?= $prestamo['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto (S/)</label>
                            <input type="number" class="form-control" id="monto" name="monto" 
                                   min="1" max="<?= $saldo_pendiente ?>" step="0.01" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                <option value="">Seleccionar...</option>
                                <option value="Descuento por planilla">Descuento por planilla</option>
                                <option value="Transferencia bancaria">Transferencia bancaria</option>
                                <option value="Depósito">Depósito</option>
                                <option value="Efectivo">Efectivo</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="referencia" class="form-label">Referencia/N° Operación</label>
                            <input type="text" class="form-control" id="referencia" name="referencia">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>