<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

$proyecto_id = $_GET['proyecto_id'] ?? '';
$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

$where = ["m.tipo = 'consumo_obra'"];
$params = [];
$types = '';

if (!empty($proyecto_id)) {
    $where[] = "m.proyecto_id = ?";
    $params[] = $proyecto_id;
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

$whereClause = 'WHERE ' . implode(' AND ', $where);

$query = "
    SELECT p.nombre as proyecto_nombre,
           m.codigo as material_codigo,
           m.nombre as material_nombre,
           SUM(dm.cantidad) as total_cantidad,
           SUM(dm.costo_total) as total_costo
    FROM movimientos mov
    JOIN detalle_movimientos dm ON mov.id = dm.movimiento_id
    JOIN materiales m ON dm.material_id = m.id
    JOIN proyectos p ON mov.proyecto_id = p.id
    $whereClause
    GROUP BY p.id, m.id
    ORDER BY p.nombre, total_costo DESC
";

$consumos = [];
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $consumos[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $consumos[] = $row;
    }
}

$proyectos = getMultipleRecords("SELECT * FROM proyectos WHERE estado = 'activo' ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-chart-line"></i> Consumos por Proyecto</h1>
    <p>Análisis de materiales consumidos por proyecto</p>
</div>

<div class="filters">
    <form method="GET" class="filters-row">
        <div class="form-group">
            <label>Proyecto</label>
            <select name="proyecto_id">
                <option value="">Todos los proyectos</option>
                <?php foreach ($proyectos as $proy): ?>
                    <option value="<?php echo $proy['id']; ?>" <?php echo $proyecto_id == $proy['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($proy['nombre']); ?>
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
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Consumos Registrados</h2>
    </div>
    <div class="card-body">
        <?php if (empty($consumos)): ?>
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <p>No hay consumos para el período seleccionado</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Proyecto</th>
                            <th>Material</th>
                            <th>Cantidad Total</th>
                            <th>Costo Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_general = 0;
                        foreach ($consumos as $cons): 
                            $total_general += $cons['total_costo'];
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cons['proyecto_nombre']); ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($cons['material_codigo']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($cons['material_nombre']); ?></small>
                                </td>
                                <td><?php echo number_format($cons['total_cantidad'], 2); ?></td>
                                <td><strong>Bs. <?php echo number_format($cons['total_costo'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background: var(--bg-tertiary); font-weight: 600;">
                            <td colspan="3" style="text-align: right;"><strong>TOTAL GENERAL</strong></td>
                            <td><strong>Bs. <?php echo number_format($total_general, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

