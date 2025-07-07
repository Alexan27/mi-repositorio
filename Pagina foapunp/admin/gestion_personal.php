<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_admin.php';

// Búsqueda y filtrado
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_socio = isset($_GET['socio']) ? $_GET['socio'] : '';

$sql = "SELECT * FROM personal WHERE 1=1";
$params = [];
$types = '';

if (!empty($busqueda)) {
    $sql .= " AND (apellidos LIKE ? OR nombres LIKE ? OR codigo LIKE ? OR dni LIKE ?)";
    $busqueda_param = "%$busqueda%";
    $params = array_merge($params, [$busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param]);
    $types .= 'ssss';
}

if (!empty($filtro_socio)) {
    $sql .= " AND socio = ?";
    $params[] = $filtro_socio;
    $types .= 's';
}

$sql .= " ORDER BY apellidos, nombres";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal - FOAPUNP</title>
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
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Gestión de Personal</h5>
                        <a href="nuevo_personal.php" class="btn btn-light btn-sm">Nuevo Personal</a>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="busqueda" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
                                        <button class="btn btn-primary" type="submit">Buscar</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" name="socio" onchange="this.form.submit()">
                                        <option value="">Todos los estados</option>
                                        <option value="SI" <?= $filtro_socio == 'SI' ? 'selected' : '' ?>>Socios</option>
                                        <option value="NO" <?= $filtro_socio == 'NO' ? 'selected' : '' ?>>No socios</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <a href="gestion_personal.php" class="btn btn-outline-secondary">Limpiar</a>
                                </div>
                            </div>
                        </form>

                        <!-- Tabla de resultados -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Código</th>
                                        <th>DNI</th>
                                        <th>Apellidos y Nombres</th>
                                        <th>Tipo</th>
                                        <th>Contrato</th>
                                        <th>Socio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['codigo']) ?></td>
                                        <td><?= htmlspecialchars($row['dni']) ?></td>
                                        <td><?= htmlspecialchars($row['apellidos']) . ', ' . htmlspecialchars($row['nombres']) ?></td>
                                        <td><?= htmlspecialchars($row['tipo_personal']) ?></td>
                                        <td><?= htmlspecialchars($row['tipo_contrato']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $row['socio'] == 'SI' ? 'success' : 'secondary' ?>">
                                                <?= $row['socio'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="editar_personal.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                            <button onclick="confirmarEliminacion(<?= $row['id'] ?>)" class="btn btn-sm btn-danger">Eliminar</button>
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
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Está seguro que desea eliminar este registro de personal? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a id="deleteButton" href="#" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminacion(id) {
            document.getElementById('deleteButton').href = 'eliminar_personal.php?id=' + id;
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }
    </script>
</body>
</html>