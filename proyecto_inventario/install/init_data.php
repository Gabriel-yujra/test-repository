<?php
/**
 * Script de inicialización de datos básicos
 * Ejecutar después de crear la base de datos
 */

require_once '../config/database.php';

$conn = getDBConnection();

echo "Inicializando datos básicos...\n\n";

// Insertar roles
$roles = [
    ['Administrador', 'Acceso total al sistema'],
    ['Almacenero', 'Registro de entradas y salidas'],
    ['Residente', 'Solo consulta y solicitud']
];

foreach ($roles as $rol) {
    $stmt = $conn->prepare("INSERT IGNORE INTO roles (nombre, descripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $rol[0], $rol[1]);
    $stmt->execute();
    echo "✓ Rol creado: {$rol[0]}\n";
}

// Insertar unidades de medida
$unidades = [
    ['Pieza', 'PZA'],
    ['Kilogramo', 'KG'],
    ['Metro Cúbico', 'M3'],
    ['Bolsa', 'BOLSA'],
    ['Metro Lineal', 'ML']
];

foreach ($unidades as $unidad) {
    $stmt = $conn->prepare("INSERT IGNORE INTO unidades (nombre, abreviacion) VALUES (?, ?)");
    $stmt->bind_param("ss", $unidad[0], $unidad[1]);
    $stmt->execute();
    echo "✓ Unidad creada: {$unidad[0]} ({$unidad[1]})\n";
}

// Crear usuario administrador por defecto
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO usuarios (nombre_usuario, contrasenia_hash, nombre_completo, rol_id) 
                       VALUES ('admin', ?, 'Administrador del Sistema', 1)");
$stmt->bind_param("s", $admin_password);
$stmt->execute();
echo "✓ Usuario administrador creado (usuario: admin, contraseña: admin123)\n";

// Crear almacén central por defecto
$stmt = $conn->prepare("INSERT IGNORE INTO almacenes (nombre, tipo, zona) VALUES ('Almacén Central', 'central', 'centro')");
$stmt->execute();
echo "✓ Almacén central creado\n";

// Crear categorías de ejemplo
$categorias = [
    'Obra Gruesa',
    'Cementos',
    'Herramientas',
    'Eléctricas',
    'Pinturas',
    'Aceros'
];

foreach ($categorias as $cat) {
    $stmt = $conn->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
    $stmt->bind_param("s", $cat);
    $stmt->execute();
    echo "✓ Categoría creada: $cat\n";
}

$stmt->close();
$conn->close();

echo "\n✓ Inicialización completada!\n";
?>

