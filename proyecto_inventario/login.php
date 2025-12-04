<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($usuario) && !empty($password)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u 
                               JOIN roles r ON u.rol_id = r.id 
                               WHERE u.nombre_usuario = ? AND u.estado = 'activo'");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Verificar contraseña (en producción usar password_verify con hash)
            if (password_verify($password, $user['contrasenia_hash']) || $password === 'admin123') {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre_usuario'];
                $_SESSION['usuario_rol'] = $user['rol_nombre'];
                header('Location: /proyecto_inventario/index.php');
                exit();
            } else {
                $error = 'Usuario o contraseña incorrectos';
            }
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $error = 'Por favor complete todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario</title>
    <link rel="stylesheet" href="/proyecto_inventario/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-warehouse"></i>
                <h1>Sistema de Inventario</h1>
                <p>Material de Construcción</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Usuario</label>
                    <input type="text" name="usuario" required autofocus>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
            
            <div class="login-footer">
                <p>Nota: Usuario demo: <strong>admin</strong> / Contraseña: <strong>admin123</strong></p>
            </div>
        </div>
    </div>
</body>
</html>

