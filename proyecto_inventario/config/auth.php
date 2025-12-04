<?php
session_start();

// Verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_nombre']);
}

// Requerir autenticación
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /proyecto_inventario/login.php');
        exit();
    }
}

// Obtener información del usuario actual
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u 
                           JOIN roles r ON u.rol_id = r.id 
                           WHERE u.id = ? AND u.estado = 'activo'");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $user;
}

// Verificar permisos por rol
function hasRole($roles) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    if (is_array($roles)) {
        return in_array($user['rol_nombre'], $roles);
    }
    return $user['rol_nombre'] === $roles;
}

// Cerrar sesión
function logout() {
    session_unset();
    session_destroy();
    header('Location: /proyecto_inventario/login.php');
    exit();
}
?>

