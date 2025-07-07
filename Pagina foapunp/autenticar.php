<?php
session_start();
require __DIR__ . '/../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta para verificar las credenciales
    $sql = "SELECT u.*, p.codigo, p.dni, p.apellidos, p.nombres, p.tipo_personal, p.tipo_contrato, p.socio 
            FROM usuarios u 
            JOIN personal p ON u.personal_id = p.id 
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar si la cuenta está activa
        if (!$usuario['activo']) {
            header("Location: login.php?error=2");
            exit();
        }
        
        // Verificar la contraseña
        if (password_verify($password, $usuario['password'])) {
            // Almacenar datos del usuario en la sesión
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'personal_id' => $usuario['personal_id'],
                'username' => $usuario['username'],
                'rol' => $usuario['rol'],
                'codigo' => $usuario['codigo'],
                'dni' => $usuario['dni'],
                'nombres' => $usuario['nombres'],
                'apellidos' => $usuario['apellidos'],
                'tipo_personal' => $usuario['tipo_personal'],
                'tipo_contrato' => $usuario['tipo_contrato'],
                'socio' => $usuario['socio']
            ];
            
            // Redirigir según el rol
            switch($usuario['rol']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'socio':
                    header("Location: socio/dashboard.php");
                    break;
                default:
                    header("Location: empleado/dashboard.php");
            }
            exit();
        }
    }
    
    // Si las credenciales son incorrectas
    header("Location: login.php?error=1");
    exit();
}
?>