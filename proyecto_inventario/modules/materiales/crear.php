<?php
require_once '../../includes/header.php';

$conn = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? null;
    $unidad_base_id = $_POST['unidad_base_id'] ?? null;
    $alerta_stock_minimo = $_POST['alerta_stock_minimo'] ?? 0;
    $es_perecedero = isset($_POST['es_perecedero']) ? 1 : 0;
    
    // Validar que no exista un material con el mismo nombre
    $check = getSingleRecord("SELECT id FROM materiales WHERE nombre = ?", [$nombre]);
    
    if ($check) {
        $error = 'Ya existe un material con ese nombre';
    } elseif (empty($nombre)) {
        $error = 'El nombre es obligatorio';
    } else {
        // Generar código si no se proporciona
        if (empty($codigo)) {
            $codigo = 'MAT-' . strtoupper(substr($nombre, 0, 3)) . '-' . date('YmdHis');
        }
        
        $stmt = $conn->prepare("INSERT INTO materiales (codigo, nombre, marca, categoria_id, unidad_base_id, alerta_stock_minimo, es_perecedero) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiii", $codigo, $nombre, $marca, $categoria_id, $unidad_base_id, $alerta_stock_minimo, $es_perecedero);
        
        if ($stmt->execute()) {
            $success = 'Material creado exitosamente';
            header("Location: /proyecto_inventario/modules/materiales/listar.php?success=" . urlencode($success));
            exit();
        } else {
            $error = 'Error al crear el material: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

// Obtener datos para formulario
$categorias = getMultipleRecords("SELECT * FROM categorias ORDER BY nombre");
$unidades = getMultipleRecords("SELECT * FROM unidades ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-plus"></i> Nuevo Material</h1>
    <p>Registrar un nuevo material en el catálogo</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-box"></i> Información del Material</h2>
    </div>
    <div class="card-body">
        <form method="POST" id="materialForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-barcode"></i> Código</label>
                    <input type="text" name="codigo" placeholder="Se generará automáticamente si se deja vacío">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nombre *</label>
                    <input type="text" name="nombre" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-industry"></i> Marca</label>
                    <input type="text" name="marca">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tags"></i> Categoría</label>
                    <select name="categoria_id">
                        <option value="">Seleccionar categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-ruler"></i> Unidad de Medida *</label>
                    <select name="unidad_base_id" required>
                        <option value="">Seleccionar unidad</option>
                        <?php foreach ($unidades as $unidad): ?>
                            <option value="<?php echo $unidad['id']; ?>">
                                <?php echo htmlspecialchars($unidad['nombre'] . ' (' . $unidad['abreviacion'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-exclamation-triangle"></i> Stock Mínimo (Alerta)</label>
                    <input type="number" name="alerta_stock_minimo" min="0" step="0.01" value="0">
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="es_perecedero" value="1">
                    <i class="fas fa-calendar-times"></i> Material Perecedero
                </label>
            </div>
            
            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Material
                </button>
                <a href="/proyecto_inventario/modules/materiales/listar.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

