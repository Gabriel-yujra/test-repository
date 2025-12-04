<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

$almacen_id = $_GET['almacen_id'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';

$where = [];
$params = [];
$types = '';

if (!empty($almacen_id)) {
    $where[] = "i.almacen_id = ?";
    $params[] = $almacen_id;
    $types .= 'i';
}

if (!empty($categoria_id)) {
    $where[] = "m.categoria_id = ?";
    $params[] = $categoria_id;
    $types .= 'i';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
    SELECT m.codigo, m.nombre, m.marca,
           c.nombre as categoria,
           u.abreviacion as unidad,
           a.nombre as almacen,
           i.cantidad,
           i.costo_promedio,
           (i.cantidad * i.costo_promedio) as valor_total
    FROM inventario_stock i
    JOIN materiales m ON i.material_id = m.id
    LEFT JOIN categorias c ON m.categoria_id = c.id
    LEFT JOIN unidades u ON m.unidad_base_id = u.id
    JOIN almacenes a ON i.almacen_id = a.id
    $whereClause
    ORDER BY a.nombre, m.nombre
";

$inventario = [];
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $inventario[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $inventario[] = $row;
    }
}

// Calcular totales
$total_valor = array_sum(array_column($inventario, 'valor_total'));

$almacenes = getMultipleRecords("SELECT * FROM almacenes ORDER BY nombre");
$categorias = getMultipleRecords("SELECT * FROM categorias ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-clipboard-list"></i> Reporte de Inventario</h1>
    <p>Inventario valorizado por almacén</p>
</div>

<div class="filters">
    <form method="GET" class="filters-row">
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
            <label>Categoría</label>
            <select name="categoria_id">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/proyecto_inventario/modules/reportes/inventario.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Limpiar
            </a>
            <a href="/proyecto_inventario/modules/reportes/exportar_inventario.php?<?php echo http_build_query($_GET); ?>" 
               class="btn btn-success" target="_blank">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-boxes"></i> Inventario Valorizado</h2>
        <div style="font-size: 1.25rem; font-weight: 600; color: var(--blue-light);">
            Total: Bs. <?php echo number_format($total_valor, 2); ?>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($inventario)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>No hay inventario para mostrar</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Material</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Almacén</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Costo Promedio</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventario as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['codigo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($item['marca'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['categoria'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['almacen']); ?></td>
                                <td><?php echo number_format($item['cantidad'], 2); ?></td>
                                <td><?php echo htmlspecialchars($item['unidad']); ?></td>
                                <td>Bs. <?php echo number_format($item['costo_promedio'], 2); ?></td>
                                <td><strong>Bs. <?php echo number_format($item['valor_total'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background: var(--bg-tertiary); font-weight: 600;">
                            <td colspan="8" style="text-align: right;"><strong>TOTAL GENERAL</strong></td>
                            <td><strong>Bs. <?php echo number_format($total_valor, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

