<?php
require_once '../../includes/header.php';

$conn = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $almacen_id = $_POST['almacen_id'] ?? 0;
    $proyecto_id = $_POST['proyecto_id'] ?? 0;
    $tipo_salida = $_POST['tipo_salida'] ?? '';
    $comentarios = $_POST['comentarios'] ?? '';
    $materiales = $_POST['materiales'] ?? [];
    
    if (empty($almacen_id) || empty($proyecto_id) || empty($tipo_salida) || empty($materiales)) {
        $error = 'Complete todos los campos obligatorios';
    } else {
        $conn->begin_transaction();
        
        try {
            // Validar stock disponible
            foreach ($materiales as $mat) {
                $material_id = $mat['material_id'] ?? 0;
                $cantidad = $mat['cantidad'] ?? 0;
                
                if ($material_id > 0 && $cantidad > 0) {
                    $stock = getSingleRecord("
                        SELECT cantidad FROM inventario_stock 
                        WHERE almacen_id = ? AND material_id = ?
                    ", [$almacen_id, $material_id]);
                    
                    $stock_disponible = $stock['cantidad'] ?? 0;
                    
                    if ($cantidad > $stock_disponible) {
                        throw new Exception("Stock insuficiente para el material seleccionado. Disponible: $stock_disponible");
                    }
                }
            }
            
            // Crear movimiento
            $tipo_movimiento = $tipo_salida === 'consumo' ? 'consumo_obra' : 'prestamo';
            $stmt = $conn->prepare("INSERT INTO movimientos (almacen_id, proyecto_id, usuario_id, tipo, comentarios) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiss", $almacen_id, $proyecto_id, $_SESSION['usuario_id'], $tipo_movimiento, $comentarios);
            $stmt->execute();
            $movimiento_id = $conn->insert_id;
            $stmt->close();
            
            // Procesar cada material
            foreach ($materiales as $mat) {
                $material_id = $mat['material_id'] ?? 0;
                $cantidad = $mat['cantidad'] ?? 0;
                
                if ($material_id > 0 && $cantidad > 0) {
                    // Obtener costo promedio
                    $stock_info = getSingleRecord("
                        SELECT costo_promedio FROM inventario_stock 
                        WHERE almacen_id = ? AND material_id = ?
                    ", [$almacen_id, $material_id]);
                    
                    $costo_unitario = $stock_info['costo_promedio'] ?? 0;
                    $costo_total = $cantidad * $costo_unitario;
                    
                    // Insertar detalle
                    $stmt = $conn->prepare("INSERT INTO detalle_movimientos (movimiento_id, material_id, cantidad, costo_unitario, costo_total) 
                                           VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iiddd", $movimiento_id, $material_id, $cantidad, $costo_unitario, $costo_total);
                    $stmt->execute();
                    $stmt->close();
                    
                    // Actualizar inventario (reducir stock)
                    $stmt = $conn->prepare("UPDATE inventario_stock 
                                           SET cantidad = cantidad - ? 
                                           WHERE almacen_id = ? AND material_id = ?");
                    $stmt->bind_param("dii", $cantidad, $almacen_id, $material_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            
            $conn->commit();
            $success = 'Salida registrada exitosamente';
            header("Location: /proyecto_inventario/modules/movimientos/salidas.php?success=" . urlencode($success));
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}

$almacenes = getMultipleRecords("SELECT * FROM almacenes ORDER BY nombre");
$proyectos = getMultipleRecords("SELECT * FROM proyectos WHERE estado = 'activo' ORDER BY nombre");
$materiales = getMultipleRecords("SELECT * FROM materiales ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-arrow-up"></i> Registro de Salidas</h1>
    <p>Registrar salida de materiales del almacén</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-minus-circle"></i> Nueva Salida</h2>
    </div>
    <div class="card-body">
        <form method="POST" id="salidaForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-warehouse"></i> Almacén *</label>
                    <select name="almacen_id" id="almacen_id" required>
                        <option value="">Seleccionar almacén</option>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?php echo $alm['id']; ?>">
                                <?php echo htmlspecialchars($alm['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Proyecto Destino *</label>
                    <select name="proyecto_id" required>
                        <option value="">Seleccionar proyecto</option>
                        <?php foreach ($proyectos as $proy): ?>
                            <option value="<?php echo $proy['id']; ?>">
                                <?php echo htmlspecialchars($proy['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Tipo de Salida *</label>
                    <select name="tipo_salida" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="consumo">Consumo (Se gasta en la obra)</option>
                        <option value="prestamo">Préstamo (Debe ser devuelto)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-comment"></i> Comentarios</label>
                    <input type="text" name="comentarios">
                </div>
            </div>
            
            <hr style="margin: 2rem 0; border-color: var(--border-color);">
            
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-boxes"></i> Materiales</h3>
            
            <div id="materiales_container">
                <div class="material-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;">
                    <div class="form-group">
                        <label>Material *</label>
                        <select name="materiales[0][material_id]" class="material-select" required>
                            <option value="">Seleccionar material</option>
                            <?php foreach ($materiales as $mat): ?>
                                <option value="<?php echo $mat['id']; ?>">
                                    <?php echo htmlspecialchars($mat['nombre'] . ' (' . $mat['codigo'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;" class="stock-info">Stock disponible: -</small>
                    </div>
                    <div class="form-group">
                        <label>Cantidad *</label>
                        <input type="number" name="materiales[0][cantidad]" step="0.01" min="0.01" required class="cantidad-input">
                    </div>
                    <div class="form-group">
                        <label>Stock Disponible</label>
                        <input type="text" class="stock-disponible" readonly style="background: var(--bg-secondary);">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger btn-sm remove-material">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add_material" class="btn btn-secondary">
                <i class="fas fa-plus"></i> Agregar Material
            </button>
            
            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Salida
                </button>
                <a href="/proyecto_inventario/index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let materialIndex = 1;

// Actualizar stock disponible cuando se selecciona material o almacén
function updateStockInfo(selectElement) {
    const item = selectElement.closest('.material-item');
    const materialId = item.querySelector('.material-select').value;
    const almacenId = document.getElementById('almacen_id').value;
    const stockInfo = item.querySelector('.stock-info');
    const stockDisponible = item.querySelector('.stock-disponible');
    
    if (materialId && almacenId) {
        fetch(`/proyecto_inventario/api/get_stock.php?almacen_id=${almacenId}&material_id=${materialId}`)
            .then(response => response.json())
            .then(data => {
                const stock = parseFloat(data.stock || 0);
                stockInfo.textContent = `Stock disponible: ${stock.toFixed(2)}`;
                stockDisponible.value = stock.toFixed(2);
                
                if (stock <= 0) {
                    stockInfo.style.color = 'var(--danger)';
                } else {
                    stockInfo.style.color = 'var(--text-muted)';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        stockInfo.textContent = 'Stock disponible: -';
        stockDisponible.value = '';
    }
}

document.getElementById('almacen_id').addEventListener('change', function() {
    document.querySelectorAll('.material-select').forEach(select => {
        if (select.value) {
            updateStockInfo(select);
        }
    });
});

document.getElementById('add_material').addEventListener('click', function() {
    const container = document.getElementById('materiales_container');
    const newItem = container.firstElementChild.cloneNode(true);
    
    newItem.querySelectorAll('select, input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/\[0\]/, `[${materialIndex}]`);
        }
        if (input.type !== 'button') {
            input.value = '';
        }
    });
    
    container.appendChild(newItem);
    materialIndex++;
    
    attachMaterialEvents(newItem);
});

function attachMaterialEvents(item) {
    const select = item.querySelector('.material-select');
    select.addEventListener('change', function() {
        updateStockInfo(this);
    });
    
    item.querySelector('.remove-material').addEventListener('click', function() {
        if (document.getElementById('materiales_container').children.length > 1) {
            item.remove();
        } else {
            alert('Debe tener al menos un material');
        }
    });
}

attachMaterialEvents(document.getElementById('materiales_container').firstElementChild);
</script>

<?php require_once '../../includes/footer.php'; ?>

