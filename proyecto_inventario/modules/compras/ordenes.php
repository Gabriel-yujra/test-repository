<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

// Ver orden específica
$ver_id = $_GET['ver'] ?? 0;
if ($ver_id) {
    $orden = getSingleRecord("
        SELECT oc.*, p.razon_social as proveedor_nombre
        FROM ordenes_compra oc
        JOIN proveedores p ON oc.proveedor_id = p.id
        WHERE oc.id = ?
    ", [$ver_id]);
    
    if ($orden) {
        $detalles = getMultipleRecords("
            SELECT doc.*, m.nombre as material_nombre, m.codigo as material_codigo
            FROM detalle_ordenes doc
            JOIN materiales m ON doc.material_id = m.id
            WHERE doc.orden_compra_id = ?
        ", [$ver_id]);
    }
}

// Listar órdenes
if (!$ver_id) {
    $ordenes = getMultipleRecords("
        SELECT oc.*, p.razon_social as proveedor_nombre
        FROM ordenes_compra oc
        JOIN proveedores p ON oc.proveedor_id = p.id
        ORDER BY oc.fecha_creacion DESC
        LIMIT 50
    ");
}

$proveedores = getMultipleRecords("SELECT * FROM proveedores ORDER BY razon_social");
$materiales = getMultipleRecords("SELECT * FROM materiales ORDER BY nombre");

$conn->close();
?>

<?php if ($ver_id && $orden): ?>
    <div class="page-header">
        <h1><i class="fas fa-file-invoice"></i> Orden de Compra #<?php echo htmlspecialchars($orden['codigo']); ?></h1>
        <p>Detalles de la orden de compra</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-info-circle"></i> Información General</h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <tr>
                    <th>Código</th>
                    <td><strong><?php echo htmlspecialchars($orden['codigo']); ?></strong></td>
                </tr>
                <tr>
                    <th>Proveedor</th>
                    <td><?php echo htmlspecialchars($orden['proveedor_nombre']); ?></td>
                </tr>
                <tr>
                    <th>Estado</th>
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
                </tr>
                <tr>
                    <th>Monto Total</th>
                    <td><strong>Bs. <?php echo number_format($orden['monto_total'] ?? 0, 2); ?></strong></td>
                </tr>
                <tr>
                    <th>Fecha de Creación</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($orden['fecha_creacion'])); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-boxes"></i> Materiales</h2>
        </div>
        <div class="card-body">
            <?php if (empty($detalles)): ?>
                <p class="empty-state">No hay materiales en esta orden</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Cantidad Solicitada</th>
                            <th>Precio Pactado</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $det): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($det['material_codigo']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($det['material_nombre']); ?></small>
                                </td>
                                <td><?php echo number_format($det['cantidad_solicitada'], 2); ?></td>
                                <td>Bs. <?php echo number_format($det['precio_pactado'], 2); ?></td>
                                <td><strong>Bs. <?php echo number_format($det['cantidad_solicitada'] * $det['precio_pactado'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="margin-top: 1.5rem;">
        <a href="/proyecto_inventario/modules/compras/ordenes.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Órdenes
        </a>
    </div>
<?php else: ?>
    <div class="page-header">
        <h1><i class="fas fa-file-invoice"></i> Órdenes de Compra</h1>
        <p>Gestión de órdenes de compra</p>
    </div>
    
    <div style="margin-bottom: 1.5rem;">
        <a href="/proyecto_inventario/modules/compras/crear_orden.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Orden de Compra
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Órdenes Registradas</h2>
        </div>
        <div class="card-body">
            <?php if (empty($ordenes)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <p>No hay órdenes de compra registradas</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Monto Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ordenes as $ord): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($ord['codigo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($ord['proveedor_nombre']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($ord['fecha_creacion'])); ?></td>
                                    <td>
                                        <?php
                                        $estado_badge = [
                                            'borrador' => 'badge-secondary',
                                            'aprobada' => 'badge-info',
                                            'recepcionada' => 'badge-success',
                                            'cancelada' => 'badge-danger'
                                        ];
                                        $estado_class = $estado_badge[$ord['estado']] ?? 'badge-secondary';
                                        ?>
                                        <span class="badge <?php echo $estado_class; ?>">
                                            <?php echo ucfirst($ord['estado']); ?>
                                        </span>
                                    </td>
                                    <td><strong>Bs. <?php echo number_format($ord['monto_total'] ?? 0, 2); ?></strong></td>
                                    <td>
                                        <a href="?ver=<?php echo $ord['id']; ?>" class="action-btn view" title="Ver detalles">
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
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>

