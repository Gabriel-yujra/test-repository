<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/auth.php';

if (!isAuthenticated()) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$almacen_id = $_GET['almacen_id'] ?? 0;
$material_id = $_GET['material_id'] ?? 0;

if ($almacen_id && $material_id) {
    $stock = getSingleRecord("
        SELECT cantidad FROM inventario_stock 
        WHERE almacen_id = ? AND material_id = ?
    ", [$almacen_id, $material_id]);
    
    echo json_encode(['stock' => $stock['cantidad'] ?? 0]);
} else {
    echo json_encode(['error' => 'Parámetros inválidos']);
}
?>

