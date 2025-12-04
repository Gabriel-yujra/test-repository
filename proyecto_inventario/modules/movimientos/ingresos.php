<?php
require_once '../../includes/header.php';

$conn = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $almacen_id = $_POST['almacen_id'] ?? 0;
    $tipo_ingreso = $_POST['tipo_ingreso'] ?? '';
    $proveedor_id = $_POST['proveedor_id'] ?? null;
    $proyecto_id = $_POST['proyecto_id'] ?? null;
    $documento_referencia = $_POST['documento_referencia'] ?? '';
    $comentarios = $_POST['comentarios'] ?? '';
    $materiales = $_POST['materiales'] ?? [];
    
    if (empty($almacen_id) || empty($tipo_ingreso) || empty($materiales)) {
        $error = 'Complete todos los campos obligatorios';
    } elseif ($tipo_ingreso === 'compra' && empty($proveedor_id)) {
        $error = 'Seleccione un proveedor para compras';
    } elseif ($tipo_ingreso === 'devolucion' && empty($proyecto_id)) {
        $error = 'Seleccione un proyecto para devoluciones';
    } else {
        $conn->begin_transaction();
        
        try {
            // Crear movimiento
            $tipo_movimiento = $tipo_ingreso === 'compra' ? 'compra' : 'devolucion';
            $stmt = $conn->prepare("INSERT INTO movimientos (almacen_id, proyecto_id, usuario_id, tipo, documento_referencia, comentarios) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisss", $almacen_id, $proyecto_id, $_SESSION['usuario_id'], $tipo_movimiento, $documento_referencia, $comentarios);
            $stmt->execute();
            $movimiento_id = $conn->insert_id;
            $stmt->close();
            
            // Procesar cada material
            foreach ($materiales as $mat) {
                $material_id = $mat['material_id'] ?? 0;
                $cantidad = $mat['cantidad'] ?? 0;
                $costo_unitario = $mat['costo_unitario'] ?? 0;
                
                if ($material_id > 0 && $cantidad > 0) {
                    $costo_total = $cantidad * $costo_unitario;
                    
                    // Insertar detalle
                    $stmt = $conn->prepare("INSERT INTO detalle_movimientos (movimiento_id, material_id, cantidad, costo_unitario, costo_total) 
                                           VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iiddd", $movimiento_id, $material_id, $cantidad, $costo_unitario, $costo_total);
                    $stmt->execute();
                    $stmt->close();
                    
                    // Actualizar inventario
                    $stmt = $conn->prepare("INSERT INTO inventario_stock (almacen_id, material_id, cantidad, costo_promedio) 
                                           VALUES (?, ?, ?, ?)
                                           ON DUPLICATE KEY UPDATE 
                                           cantidad = cantidad + ?,
                                           costo_promedio = ((costo_promedio * cantidad) + (? * ?)) / (cantidad + ?)");
                    $stmt->bind_param("iidddddd", $almacen_id, $material_id, $cantidad, $costo_unitario, 
                                     $cantidad, $costo_unitario, $cantidad, $cantidad);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            
            $conn->commit();
            $success = 'Ingreso registrado exitosamente';
            header("Location: /proyecto_inventario/modules/movimientos/ingresos.php?success=" . urlencode($success));
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Error al registrar el ingreso: ' . $e->getMessage();
        }
    }
}

$almacenes = getMultipleRecords("SELECT * FROM almacenes ORDER BY nombre");
$proveedores = getMultipleRecords("SELECT * FROM proveedores ORDER BY razon_social");
$proyectos = getMultipleRecords("SELECT * FROM proyectos WHERE estado = 'activo' ORDER BY nombre");
$materiales = getMultipleRecords("SELECT * FROM materiales ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-arrow-down"></i> Registro de Ingresos</h1>
    <p>Registrar entrada de materiales al almacén</p>
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
        <h2><i class="fas fa-plus-circle"></i> Nuevo Ingreso</h2>
    </div>
    <div class="card-body">
        <form method="POST" id="ingresoForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-warehouse"></i> Almacén *</label>
                    <select name="almacen_id" required>
                        <option value="">Seleccionar almacén</option>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?php echo $alm['id']; ?>">
                                <?php echo htmlspecialchars($alm['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Tipo de Ingreso *</label>
                    <select name="tipo_ingreso" id="tipo_ingreso" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="compra">Compra a Proveedor</option>
                        <option value="devolucion">Devolución de Obra</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row" id="proveedor_row" style="display: none;">
                <div class="form-group">
                    <label><i class="fas fa-truck"></i> Proveedor *</label>
                    <select name="proveedor_id" id="proveedor_id">
                        <option value="">Seleccionar proveedor</option>
                        <?php foreach ($proveedores as $prov): ?>
                            <option value="<?php echo $prov['id']; ?>">
                                <?php echo htmlspecialchars($prov['razon_social']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row" id="proyecto_row" style="display: none;">
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Proyecto Origen *</label>
                    <select name="proyecto_id" id="proyecto_id">
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
                    <label><i class="fas fa-file-invoice"></i> Documento de Referencia</label>
                    <input type="text" name="documento_referencia" placeholder="N° Factura, Guía, etc.">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-comment"></i> Comentarios</label>
                    <input type="text" name="comentarios">
                </div>
            </div>
            
            <hr style="margin: 2rem 0; border-color: var(--border-color);">
            
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-boxes"></i> Materiales</h3>
            
            <div id="materiales_container">
                <div class="material-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;">
                    <div class="form-group">
                        <label>Material *</label>
                        <select name="materiales[0][material_id]" class="material-select" required>
                            <option value="">Seleccionar material</option>
                            <?php foreach ($materiales as $mat): ?>
                                <option value="<?php echo $mat['id']; ?>" data-unidad="<?php echo $mat['unidad_base_id']; ?>">
                                    <?php echo htmlspecialchars($mat['nombre'] . ' (' . $mat['codigo'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad *</label>
                        <input type="number" name="materiales[0][cantidad]" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Costo Unitario (Bs.) *</label>
                        <input type="number" name="materiales[0][costo_unitario]" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Total</label>
                        <input type="text" class="total-field" readonly style="background: var(--bg-secondary);">
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
                    <i class="fas fa-save"></i> Registrar Ingreso
                </button>
                <a href="/proyecto_inventario/index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('tipo_ingreso').addEventListener('change', function() {
    const tipo = this.value;
    document.getElementById('proveedor_row').style.display = tipo === 'compra' ? 'block' : 'none';
    document.getElementById('proyecto_row').style.display = tipo === 'devolucion' ? 'block' : 'none';
    
    if (tipo === 'compra') {
        document.getElementById('proveedor_id').required = true;
        document.getElementById('proyecto_id').required = false;
    } else if (tipo === 'devolucion') {
        document.getElementById('proyecto_id').required = true;
        document.getElementById('proveedor_id').required = false;
    } else {
        document.getElementById('proveedor_id').required = false;
        document.getElementById('proyecto_id').required = false;
    }
});

let materialIndex = 1;

document.getElementById('add_material').addEventListener('click', function() {
    const container = document.getElementById('materiales_container');
    const newItem = container.firstElementChild.cloneNode(true);
    
    // Actualizar índices
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
    
    // Agregar evento para calcular total
    attachMaterialEvents(newItem);
});

// Calcular totales
function attachMaterialEvents(item) {
    const cantidad = item.querySelector('input[name*="[cantidad]"]');
    const costo = item.querySelector('input[name*="[costo_unitario]"]');
    const total = item.querySelector('.total-field');
    
    function updateTotal() {
        const qty = parseFloat(cantidad.value) || 0;
        const cost = parseFloat(costo.value) || 0;
        total.value = 'Bs. ' + (qty * cost).toFixed(2);
    }
    
    cantidad.addEventListener('input', updateTotal);
    costo.addEventListener('input', updateTotal);
    
    // Botón eliminar
    item.querySelector('.remove-material').addEventListener('click', function() {
        if (document.getElementById('materiales_container').children.length > 1) {
            item.remove();
        } else {
            alert('Debe tener al menos un material');
        }
    });
}

// Inicializar eventos para el primer material
attachMaterialEvents(document.getElementById('materiales_container').firstElementChild);
</script>

<?php require_once '../../includes/footer.php'; ?>

