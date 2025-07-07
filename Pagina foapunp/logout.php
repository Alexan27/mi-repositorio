<?php
// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Registrar el cierre de sesión (opcional para auditoría)
require '../includes/conexion.php';
if (isset($_SESSION['usuario'])) {
    $usuario_id = $_SESSION['usuario']['id'];
    $sql = "INSERT INTO logs_acceso (usuario_id, accion, detalle) 
            VALUES (?, 'logout', 'Cierre de sesión')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir a la página de login con mensaje opcional
header("Location: login.php?msg=logout_success");
exit();
?>