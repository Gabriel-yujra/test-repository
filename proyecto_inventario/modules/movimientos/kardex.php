<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

$material_id = $_GET['material_id'] ?? '';
$almacen_id = $_GET['almacen_id'] ?? '';
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

$where = [];
$params = [];
$types = '';

if (!empty($material_id)) {
    $where[] = "dm.material_id = ?";
    $params[] = $material_id;
    $types .= 'i';
}

if (!empty($almacen_id)) {
    $where[] = "m.almacen_id = ?";
    $params[] = $almacen_id;
    $types .= 'i';
}

if (!empty($fecha_desde)) {
    $where[] = "DATE(m.fecha) >= ?";
    $params[] = $fecha_desde;
    $types .= 's';
}

if (!empty($fecha_hasta)) {
    $where[] = "DATE(m.fecha) <= ?";
    $params[] = $fecha_hasta;
    $types .= 's';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
    SELECT m.*, 
           dm.cantidad, dm.costo_unitario, dm.costo_total,
           mat.nombre as material_nombre, mat.codigo as material_codigo,
           a.nombre as almacen_nombre,
           u.nombre_completo as usuario_nombre,
           p.nombre as proyecto_nombre
    FROM movimientos m
    JOIN detalle_movimientos dm ON m.id = dm.movimiento_id
    JOIN materiales mat ON dm.material_id = mat.id
    JOIN almacenes a ON m.almacen_id = a.id
    JOIN usuarios u ON m.usuario_id = u.id
    LEFT JOIN proyectos p ON m.proyecto_id = p.id
    $whereClause
    ORDER BY m.fecha DESC, m.id DESC
    LIMIT 500
";

$movimientos = [];
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $movimientos[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $movimientos[] = $row;
    }
}

// Datos para filtros
$materiales = getMultipleRecords("SELECT * FROM materiales ORDER BY nombre");
$almacenes = getMultipleRecords("SELECT * FROM almacenes ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-history"></i> Kardex de Movimientos</h1>
    <p>Historial completo de movimientos de inventario</p>
</div>

<div class="filters">
    <form method="GET" class="filters-row">
        <div class="form-group">
            <label>Material</label>
            <select name="material_id">
                <option value="">Todos los materiales</option>
                <?php foreach ($materiales as $mat): ?>
                    <option value="<?php echo $mat['id']; ?>" <?php echo $material_id == $mat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mat['nombre'] . ' (' . $mat['codigo'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Almacén</label>
            <select name="almacen_id">
                <option value="">Todos los almacenes</option>
                <?php foreach ($almacenes as $alm): ?>
                    <option value="<?php echo $alm['id']; ?>" <?php echo $almacen_id == $alm['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($alm['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Fecha Desde</label>
            <input type="date" name="fecha_desde" value="<?php echo htmlspecialchars($fecha_desde); ?>">
        </div>
        
        <div class="form-group">
            <label>Fecha Hasta</label>
            <input type="date" name="fecha_hasta" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/proyecto_inventario/modules/movimientos/kardex.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Limpiar
            </a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Movimientos (<?php echo count($movimientos); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($movimientos)): ?>
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <p>No se encontraron movimientos</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Material</th>
                            <th>Tipo</th>
                            <th>Almacén</th>
                            <th>Proyecto</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Total</th>
                            <th>Usuario</th>
                            <th>Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $mov): 
                            $is_entrada = in_array($mov['tipo'], ['compra', 'devolucion', 'transferencia_entrada', 'ajuste_positivo']);
                        ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($mov['material_codigo']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($mov['material_nombre']); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $is_entrada ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $mov['tipo'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($mov['almacen_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($mov['proyecto_nombre'] ?? '-'); ?></td>
                                <td>
                                    <strong style="color: <?php echo $is_entrada ? 'var(--success)' : 'var(--danger)'; ?>;">
                                        <?php echo $is_entrada ? '+' : '-'; ?><?php echo number_format($mov['cantidad'], 2); ?>
                                    </strong>
                                </td>
                                <td>Bs. <?php echo number_format($mov['costo_unitario'], 2); ?></td>
                                <td><strong>Bs. <?php echo number_format($mov['costo_total'], 2); ?></strong></td>
                                <td><?php echo htmlspecialchars($mov['usuario_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($mov['documento_referencia'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

