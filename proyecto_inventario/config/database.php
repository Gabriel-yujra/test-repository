<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventario_material_construccion');

// Conexión a la base de datos
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Función para ejecutar consultas
function executeQuery($query, $params = []) {
    $conn = getDBConnection();
    
    if (empty($params)) {
        $result = $conn->query($query);
        $conn->close();
        return $result;
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $conn->close();
        return false;
    }
    
    // Detectar tipos automáticamente
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_float($param) || is_double($param)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para obtener un solo registro
function getSingleRecord($query, $params = []) {
    $result = executeQuery($query, $params);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Función para obtener múltiples registros
function getMultipleRecords($query, $params = []) {
    $result = executeQuery($query, $params);
    $records = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }
    return $records;
}
?>

