<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

// Materiales con stock crítico
$stock_critico = getMultipleRecords("
    SELECT m.id, m.codigo, m.nombre, m.alerta_stock_minimo,
           i.almacen_id, i.cantidad,
           a.nombre as almacen_nombre,
           c.nombre as categoria_nombre
    FROM inventario_stock i
    JOIN materiales m ON i.material_id = m.id
    JOIN almacenes a ON i.almacen_id = a.id
    LEFT JOIN categorias c ON m.categoria_id = c.id
    WHERE i.cantidad <= m.alerta_stock_minimo
    ORDER BY i.cantidad ASC
");

// Materiales sin stock
$sin_stock = getMultipleRecords("
    SELECT m.id, m.codigo, m.nombre,
           a.nombre as almacen_nombre
    FROM inventario_stock i
    JOIN materiales m ON i.material_id = m.id
    JOIN almacenes a ON i.almacen_id = a.id
    WHERE i.cantidad = 0
    ORDER BY m.nombre
");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-exclamation-triangle"></i> Alertas de Stock</h1>
    <p>Materiales con stock crítico o sin stock</p>
</div>

<div class="dashboard-grid">
    <!-- Stock Crítico -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-exclamation-circle"></i> Stock Crítico (<?php echo count($stock_critico); ?>)</h2>
        </div>
        <div class="card-body">
            <?php if (empty($stock_critico)): ?>
                <p class="empty-state">No hay materiales con stock crítico</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Material</th>
                                <th>Almacén</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Diferencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stock_critico as $item): 
                                $diferencia = $item['cantidad'] - $item['alerta_stock_minimo'];
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['codigo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($item['almacen_nombre']); ?></td>
                                    <td>
                                        <span class="badge badge-danger">
                                            <?php echo number_format($item['cantidad'], 2); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($item['alerta_stock_minimo'], 2); ?></td>
                                    <td>
                                        <span style="color: var(--danger);">
                                            <?php echo $diferencia < 0 ? number_format($diferencia, 2) : '0.00'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sin Stock -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-times-circle"></i> Sin Stock (<?php echo count($sin_stock); ?>)</h2>
        </div>
        <div class="card-body">
            <?php if (empty($sin_stock)): ?>
                <p class="empty-state">Todos los materiales tienen stock</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Material</th>
                                <th>Almacén</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sin_stock as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['codigo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($item['almacen_nombre']); ?></td>
                                    <td>
                                        <a href="/proyecto_inventario/modules/movimientos/ingresos.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Registrar Ingreso
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

