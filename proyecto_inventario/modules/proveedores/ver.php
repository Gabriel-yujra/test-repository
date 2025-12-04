<?php
require_once '../../includes/header.php';

$id = $_GET['id'] ?? 0;
$conn = getDBConnection();

if (!$id) {
    header('Location: /proyecto_inventario/modules/proveedores/listar.php');
    exit();
}

$proveedor = getSingleRecord("SELECT * FROM proveedores WHERE id = ?", [$id]);

if (!$proveedor) {
    header('Location: /proyecto_inventario/modules/proveedores/listar.php');
    exit();
}

// Historial de compras
$ordenes = getMultipleRecords("
    SELECT oc.*, COUNT(doc.id) as total_items
    FROM ordenes_compra oc
    LEFT JOIN detalle_ordenes doc ON oc.id = doc.orden_compra_id
    WHERE oc.proveedor_id = ?
    GROUP BY oc.id
    ORDER BY oc.fecha_creacion DESC
", [$id]);

// Estadísticas
$stats = getSingleRecord("
    SELECT COUNT(DISTINCT oc.id) as total_ordenes,
           SUM(oc.monto_total) as total_compras,
           AVG(oc.monto_total) as promedio_orden
    FROM ordenes_compra oc
    WHERE oc.proveedor_id = ? AND oc.estado != 'cancelada'
", [$id]);

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-truck"></i> Detalles del Proveedor</h1>
    <p>Información completa del proveedor</p>
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
                    <th>Razón Social</th>
                    <td><strong><?php echo htmlspecialchars($proveedor['razon_social']); ?></strong></td>
                </tr>
                <tr>
                    <th>NIT/RUC</th>
                    <td><?php echo htmlspecialchars($proveedor['nit'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Teléfono</th>
                    <td><?php echo htmlspecialchars($proveedor['telefono'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Tipo</th>
                    <td>
                        <?php if ($proveedor['es_formal']): ?>
                            <span class="badge badge-success">Formal</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Informal</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <div style="margin-top: 1.5rem;">
                <a href="/proyecto_inventario/modules/proveedores/editar.php?id=<?php echo $id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Proveedor
                </a>
                <a href="/proyecto_inventario/modules/proveedores/listar.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-chart-bar"></i> Estadísticas</h2>
        </div>
        <div class="card-body">
            <div class="stats-grid" style="grid-template-columns: 1fr;">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_ordenes'] ?? 0; ?></h3>
                        <p>Órdenes de Compra</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon dark-blue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Bs. <?php echo number_format($stats['total_compras'] ?? 0, 2); ?></h3>
                        <p>Total en Compras</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon gray">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Bs. <?php echo number_format($stats['promedio_orden'] ?? 0, 2); ?></h3>
                        <p>Promedio por Orden</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historial de Compras -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-history"></i> Historial de Compras</h2>
    </div>
    <div class="card-body">
        <?php if (empty($ordenes)): ?>
            <p class="empty-state">No hay órdenes de compra registradas</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Items</th>
                            <th>Monto Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordenes as $orden): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($orden['codigo']); ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($orden['fecha_creacion'])); ?></td>
                                <td>
                                    <?php
                                    $estado_badge = [
                                        'borrador' => 'badge-secondary',
                                        'aprobada' => 'badge-info',
                                        'recepcionada' => 'badge-success',
                                        'cancelada' => 'badge-danger'
                                    ];
                                    $estado_class = $estado_badge[$orden['estado']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $estado_class; ?>">
                                        <?php echo ucfirst($orden['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo $orden['total_items']; ?></td>
                                <td><strong>Bs. <?php echo number_format($orden['monto_total'] ?? 0, 2); ?></strong></td>
                                <td>
                                    <a href="/proyecto_inventario/modules/compras/ordenes.php?ver=<?php echo $orden['id']; ?>" 
                                       class="action-btn view" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
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

<?php require_once '../../includes/footer.php'; ?>

