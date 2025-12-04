<?php
require_once '../../includes/header.php';

$id = $_GET['id'] ?? 0;
$conn = getDBConnection();
$error = '';

if (!$id) {
    header('Location: /proyecto_inventario/modules/materiales/listar.php');
    exit();
}

$material = getSingleRecord("SELECT * FROM materiales WHERE id = ?", [$id]);

if (!$material) {
    header('Location: /proyecto_inventario/modules/materiales/listar.php');
    exit();
}

// Verificar si tiene historial de movimientos
$tieneHistorial = getSingleRecord("SELECT COUNT(*) as total FROM detalle_movimientos WHERE material_id = ?", [$id]);
$puedeCambiarUnidad = $tieneHistorial['total'] == 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? null;
    $unidad_base_id = $_POST['unidad_base_id'] ?? null;
    $alerta_stock_minimo = $_POST['alerta_stock_minimo'] ?? 0;
    $es_perecedero = isset($_POST['es_perecedero']) ? 1 : 0;
    
    // Validar nombre único (excepto el actual)
    $check = getSingleRecord("SELECT id FROM materiales WHERE nombre = ? AND id != ?", [$nombre, $id]);
    
    if ($check) {
        $error = 'Ya existe otro material con ese nombre';
    } elseif (empty($nombre)) {
        $error = 'El nombre es obligatorio';
    } elseif (!$puedeCambiarUnidad && $unidad_base_id != $material['unidad_base_id']) {
        $error = 'No se puede cambiar la unidad de medida porque el material tiene historial de movimientos';
    } else {
        $stmt = $conn->prepare("UPDATE materiales SET codigo = ?, nombre = ?, marca = ?, categoria_id = ?, 
                                unidad_base_id = ?, alerta_stock_minimo = ?, es_perecedero = ? 
                                WHERE id = ?");
        $stmt->bind_param("sssiiiii", $codigo, $nombre, $marca, $categoria_id, $unidad_base_id, 
                         $alerta_stock_minimo, $es_perecedero, $id);
        
        if ($stmt->execute()) {
            header("Location: /proyecto_inventario/modules/materiales/listar.php?success=Material actualizado exitosamente");
            exit();
        } else {
            $error = 'Error al actualizar: ' . $conn->error;
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
    <h1><i class="fas fa-edit"></i> Editar Material</h1>
    <p>Modificar información del material</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if (!$puedeCambiarUnidad): ?>
    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i> Este material tiene historial de movimientos. No se puede cambiar la unidad de medida.
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
                    <input type="text" name="codigo" value="<?php echo htmlspecialchars($material['codigo']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nombre *</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($material['nombre']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-industry"></i> Marca</label>
                    <input type="text" name="marca" value="<?php echo htmlspecialchars($material['marca'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tags"></i> Categoría</label>
                    <select name="categoria_id">
                        <option value="">Seleccionar categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $material['categoria_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-ruler"></i> Unidad de Medida *</label>
                    <select name="unidad_base_id" required <?php echo !$puedeCambiarUnidad ? 'disabled' : ''; ?>>
                        <option value="">Seleccionar unidad</option>
                        <?php foreach ($unidades as $unidad): ?>
                            <option value="<?php echo $unidad['id']; ?>" 
                                <?php echo $material['unidad_base_id'] == $unidad['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($unidad['nombre'] . ' (' . $unidad['abreviacion'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$puedeCambiarUnidad): ?>
                        <input type="hidden" name="unidad_base_id" value="<?php echo $material['unidad_base_id']; ?>">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-exclamation-triangle"></i> Stock Mínimo (Alerta)</label>
                    <input type="number" name="alerta_stock_minimo" min="0" step="0.01" 
                           value="<?php echo $material['alerta_stock_minimo'] ?? 0; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="es_perecedero" value="1" 
                           <?php echo $material['es_perecedero'] ? 'checked' : ''; ?>>
                    <i class="fas fa-calendar-times"></i> Material Perecedero
                </label>
            </div>
            
            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Material
                </button>
                <a href="/proyecto_inventario/modules/materiales/listar.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

