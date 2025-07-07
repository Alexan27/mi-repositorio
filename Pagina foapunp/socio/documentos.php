<?php
require '../includes/conexion.php';
require '../includes/verificar_sesion.php';
require '../includes/verificar_rol_socio.php';

$socio_id = $_SESSION['usuario']['personal_id'];
$prestamo_id = $_GET['prestamo_id'] ?? 0;

// Obtener documentos generales
$sql_docs = "SELECT * FROM documentos WHERE (personal_id IS NULL OR personal_id = ?) 
             AND (prestamo_id IS NULL OR prestamo_id = ?)
             ORDER BY fecha_publicacion DESC";
$stmt_docs = $conn->prepare($sql_docs);
$stmt_docs->bind_param("ii", $socio_id, $prestamo_id);
$stmt_docs->execute();
$result_docs = $stmt_docs->get_result();

// Obtener información del préstamo si está especificado
$prestamo = null;
if ($prestamo_id) {
    $sql_prestamo = "SELECT * FROM prestamos WHERE id = ? AND personal_id = ?";
    $stmt_prestamo = $conn->prepare($sql_prestamo);
    $stmt_prestamo->bind_param("ii", $prestamo_id, $socio_id);
    $stmt_prestamo->execute();
    $result_prestamo = $stmt_prestamo->get_result();
    $prestamo = $result_prestamo->fetch_assoc();
    
    if (!$prestamo) {
        header("Location: documentos.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos - FOAPUNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .document-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
            height: 100%;
        }
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .document-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
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
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="documentos.php">Documentos</a></li>
                        <?php if($prestamo): ?>
                        <li class="breadcrumb-item active" aria-current="page">Préstamo #<?= $prestamo['id'] ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <?= $prestamo ? 'Documentos del Préstamo #' . $prestamo['id'] : 'Mis Documentos' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if($result_docs->num_rows > 0): ?>
                        <div class="row">
                            <?php while($doc = $result_docs->fetch_assoc()): ?>
                            <div class="col-md-4">
                                <div class="card document-card">
                                    <div class="card-body text-center">
                                        <?php
                                        $icon = 'bi-file-earmark-text';
                                        $ext = pathinfo($doc['archivo'], PATHINFO_EXTENSION);
                                        
                                        switch(strtolower($ext)) {
                                            case 'pdf':
                                                $icon = 'bi-file-earmark-pdf';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $icon = 'bi-file-earmark-word';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $icon = 'bi-file-earmark-excel';
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                                $icon = 'bi-file-earmark-image';
                                                break;
                                        }
                                        ?>
                                        <i class="bi <?= $icon ?> text-primary document-icon"></i>
                                        <h5 class="card-title"><?= htmlspecialchars($doc['titulo']) ?></h5>
                                        <p class="card-text text-muted">
                                            <small>
                                                Publicado: <?= date('d/m/Y', strtotime($doc['fecha_publicacion'])) ?>
                                            </small>
                                        </p>
                                        <a href="../uploads/documentos/<?= $doc['archivo'] ?>" 
                                           class="btn btn-primary" 
                                           download="<?= htmlspecialchars($doc['titulo']) . '.' . $ext ?>">
                                            <i class="bi bi-download"></i> Descargar
                                        </a>
                                        <?php if($doc['prestamo_id']): ?>
                                        <span class="badge bg-info mt-2">Préstamo #<?= $doc['prestamo_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            No hay documentos disponibles.
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!$prestamo): ?>
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Documentos de Préstamos</h5>
                        <div class="row">
                            <?php
                            // Obtener préstamos con documentos
                            $sql_prestamos = "SELECT DISTINCT p.id, p.monto, p.fecha_solicitud 
                                              FROM prestamos p
                                              JOIN documentos d ON p.id = d.prestamo_id
                                              WHERE p.personal_id = ?";
                            $stmt_prestamos = $conn->prepare($sql_prestamos);
                            $stmt_prestamos->bind_param("i", $socio_id);
                            $stmt_prestamos->execute();
                            $result_prestamos = $stmt_prestamos->get_result();
                            
                            if ($result_prestamos->num_rows > 0):
                            while($prestamo_doc = $result_prestamos->fetch_assoc()): 
                            ?>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Préstamo #<?= $prestamo_doc['id'] ?></h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Monto: S/ <?= number_format($prestamo_doc['monto'], 2) ?> | 
                                                Fecha: <?= date('d/m/Y', strtotime($prestamo_doc['fecha_solicitud'])) ?>
                                            </small>
                                        </p>
                                        <a href="documentos.php?prestamo_id=<?= $prestamo_doc['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            Ver documentos <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            endwhile;
                            else:
                            ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No tienes documentos asociados a préstamos.
                                </div>
                            </div>
                            <?php endif; ?>
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