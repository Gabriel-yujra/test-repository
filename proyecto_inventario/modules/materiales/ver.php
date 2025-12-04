<?php
require_once '../../includes/header.php';

$id = $_GET['id'] ?? 0;
$conn = getDBConnection();

if (!$id) {
    header('Location: /proyecto_inventario/modules/materiales/listar.php');
    exit();
}

$material = getSingleRecord("
    SELECT m.*, 
           c.nombre as categoria_nombre,
           u.nombre as unidad_nombre,
           u.abreviacion as unidad_abreviacion
    FROM materiales m
    LEFT JOIN categorias c ON m.categoria_id = c.id
    LEFT JOIN unidades u ON m.unidad_base_id = u.id
    WHERE m.id = ?
", [$id]);

if (!$material) {
    header('Location: /proyecto_inventario/modules/materiales/listar.php');
    exit();
}

// Stock por almacén
$stock_almacenes = getMultipleRecords("
    SELECT a.nombre as almacen, i.cantidad, i.costo_promedio
    FROM inventario_stock i
    JOIN almacenes a ON i.almacen_id = a.id
    WHERE i.material_id = ?
    ORDER BY i.cantidad DESC
", [$id]);

// Movimientos recientes
$movimientos = getMultipleRecords("
    SELECT m.fecha, m.tipo, m.documento_referencia,
           dm.cantidad, dm.costo_unitario, dm.costo_total,
           a.nombre as almacen, u.nombre_completo as usuario
    FROM detalle_movimientos dm
    JOIN movimientos m ON dm.movimiento_id = m.id
    JOIN almacenes a ON m.almacen_id = a.id
    JOIN usuarios u ON m.usuario_id = u.id
    WHERE dm.material_id = ?
    ORDER BY m.fecha DESC
    LIMIT 20
", [$id]);

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-box"></i> Detalles del Material</h1>
    <p>Información completa del material</p>
</div>

<div class="dashboard-grid">
    <!-- Información General -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-info-circle"></i> Información General</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <tr>
                    <th>Código</th>
                    <td><strong><?php echo htmlspecialchars($material['codigo']); ?></strong></td>
                </tr>
                <tr>
                    <th>Nombre</th>
                    <td><?php echo htmlspecialchars($material['nombre']); ?></td>
                </tr>
                <tr>
                    <th>Marca</th>
                    <td><?php echo htmlspecialchars($material['marca'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Categoría</th>
                    <td><?php echo htmlspecialchars($material['categoria_nombre'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Unidad de Medida</th>
                    <td><?php echo htmlspecialchars($material['unidad_nombre'] . ' (' . $material['unidad_abreviacion'] . ')'); ?></td>
                </tr>
                <tr>
                    <th>Stock Mínimo</th>
                    <td><?php echo number_format($material['alerta_stock_minimo'] ?? 0, 2); ?></td>
                </tr>
                <tr>
                    <th>Perecedero</th>
                    <td><?php echo $material['es_perecedero'] ? '<span class="badge badge-warning">Sí</span>' : '<span class="badge badge-success">No</span>'; ?></td>
                </tr>
            </table>
            
            <div style="margin-top: 1.5rem;">
                <a href="/proyecto_inventario/modules/materiales/editar.php?id=<?php echo $id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Material
                </a>
                <a href="/proyecto_inventario/modules/materiales/listar.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    
    <!-- Stock por Almacén -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-warehouse"></i> Stock por Almacén</h2>
        </div>
        <div class="card-body">
            <?php if (empty($stock_almacenes)): ?>
                <p class="empty-state">No hay stock registrado</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Almacén</th>
                            <th>Cantidad</th>
                            <th>Costo Promedio</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_cantidad = 0;
                        $total_valor = 0;
                        foreach ($stock_almacenes as $stock): 
                            $valor = $stock['cantidad'] * $stock['costo_promedio'];
                            $total_cantidad += $stock['cantidad'];
                            $total_valor += $valor;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stock['almacen']); ?></td>
                                <td><?php echo number_format($stock['cantidad'], 2); ?></td>
                                <td>Bs. <?php echo number_format($stock['costo_promedio'], 2); ?></td>
                                <td>Bs. <?php echo number_format($valor, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background: var(--bg-tertiary); font-weight: 600;">
                            <td><strong>TOTAL</strong></td>
                            <td><strong><?php echo number_format($total_cantidad, 2); ?></strong></td>
                            <td>-</td>
                            <td><strong>Bs. <?php echo number_format($total_valor, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Movimientos Recientes -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-history"></i> Movimientos Recientes (Kardex)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($movimientos)): ?>
            <p class="empty-state">No hay movimientos registrados</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Almacén</th>
                            <th>Cantidad</th>
                            <th>Costo Unitario</th>
                            <th>Costo Total</th>
                            <th>Usuario</th>
                            <th>Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos as $mov): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha'])); ?></td>
                                <td><span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $mov['tipo'])); ?></span></td>
                                <td><?php echo htmlspecialchars($mov['almacen']); ?></td>
                                <td><?php echo number_format($mov['cantidad'], 2); ?></td>
                                <td>Bs. <?php echo number_format($mov['costo_unitario'], 2); ?></td>
                                <td>Bs. <?php echo number_format($mov['costo_total'], 2); ?></td>
                                <td><?php echo htmlspecialchars($mov['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($mov['documento_referencia'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem; text-align: center;">
                <a href="/proyecto_inventario/modules/movimientos/kardex.php?material_id=<?php echo $id; ?>" class="btn btn-primary">
                    <i class="fas fa-list"></i> Ver Kardex Completo
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

