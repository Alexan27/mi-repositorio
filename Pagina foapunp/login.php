<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOAPUNP - Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: linear-gradient(to bottom, #003366, #004080);
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        .login-header {
            background-color: #003366;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #003366;
            border-color: #003366;
        }
        .btn-primary:hover {
            background-color: #004080;
            border-color: #004080;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="logo_unp.png" alt="Logo UNP" class="logo">
                <h4>FOAPUNP</h4>
                <p class="mb-0">Fondo de Ahorro Personal de Trabajadores</p>
            </div>
            <div class="login-body">
                <form action="autenticar.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </div>
                </form>
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <?php 
                        switch($_GET['error']) {
                            case 1: echo "Usuario o contraseña incorrectos"; break;
                            case 2: echo "Cuenta inactiva"; break;
                            default: echo "Error al iniciar sesión";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>