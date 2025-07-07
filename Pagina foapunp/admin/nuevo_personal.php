<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_admin.php';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];
    $dni = $_POST['dni'];
    $apellidos = $_POST['apellidos'];
    $nombres = $_POST['nombres'];
    $tipo_personal = $_POST['tipo_personal'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $socio = $_POST['socio'];

    // Validar que el código y DNI no existan
    $stmt = $conn->prepare("SELECT id FROM personal WHERE codigo = ? OR dni = ?");
    $stmt->bind_param("ss", $codigo, $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "El código laboral o DNI ya están registrados";
    } else {
        // Insertar nuevo registro
        $stmt = $conn->prepare("INSERT INTO personal (codigo, dni, apellidos, nombres, tipo_personal, tipo_contrato, socio) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $codigo, $dni, $apellidos, $nombres, $tipo_personal, $tipo_contrato, $socio);
        
        if ($stmt->execute()) {
            $success = "Personal registrado correctamente";
            // Limpiar campos
            $codigo = $dni = $apellidos = $nombres = '';
            $tipo_personal = $tipo_contrato = '';
            $socio = 'NO';
        } else {
            $error = "Error al registrar el personal: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Personal - FOAPUNP</title>
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
                        <h5 class="mb-0">Registrar Nuevo Personal</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="codigo" class="form-label">Código Laboral</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" 
                                           value="<?= isset($codigo) ? htmlspecialchars($codigo) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="dni" class="form-label">DNI</label>
                                    <input type="text" class="form-control" id="dni" name="dni" 
                                           value="<?= isset($dni) ? htmlspecialchars($dni) : '' ?>" required maxlength="8">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                           value="<?= isset($apellidos) ? htmlspecialchars($apellidos) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label">Nombres</label>
                                    <input type="text" class="form-control" id="nombres" name="nombres" 
                                           value="<?= isset($nombres) ? htmlspecialchars($nombres) : '' ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tipo_personal" class="form-label">Tipo de Personal</label>
                                    <select class="form-select" id="tipo_personal" name="tipo_personal" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Docente" <?= (isset($tipo_personal) && $tipo_personal == 'Docente') ? 'selected' : '' ?>>Docente</option>
                                        <option value="Administrativo" <?= (isset($tipo_personal) && $tipo_personal == 'Administrativo') ? 'selected' : '' ?>>Administrativo</option>
                                        <option value="Servicios" <?= (isset($tipo_personal) && $tipo_personal == 'Servicios') ? 'selected' : '' ?>>Servicios</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
                                    <select class="form-select" id="tipo_contrato" name="tipo_contrato" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Nombrado" <?= (isset($tipo_contrato) && $tipo_contrato == 'Nombrado') ? 'selected' : '' ?>>Nombrado</option>
                                        <option value="Contratado" <?= (isset($tipo_contrato) && $tipo_contrato == 'Contratado') ? 'selected' : '' ?>>Contratado</option>
                                        <option value="CAS" <?= (isset($tipo_contrato) && $tipo_contrato == 'CAS') ? 'selected' : '' ?>>CAS</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="socio" class="form-label">¿Es socio?</label>
                                    <select class="form-select" id="socio" name="socio" required>
                                        <option value="NO" <?= (isset($socio) && $socio == 'NO') ? 'selected' : '' ?>>NO</option>
                                        <option value="SI" <?= (isset($socio) && $socio == 'SI') ? 'selected' : '' ?>>SI</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="gestion_personal.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>