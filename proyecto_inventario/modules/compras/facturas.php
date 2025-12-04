<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

$facturas = getMultipleRecords("
    SELECT fc.*, p.razon_social as proveedor_nombre, oc.codigo as orden_codigo
    FROM facturas_compra fc
    JOIN proveedores p ON fc.proveedor_id = p.id
    JOIN ordenes_compra oc ON fc.orden_compra_id = oc.id
    ORDER BY fc.fecha_factura DESC
    LIMIT 50
");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-receipt"></i> Facturas de Compra</h1>
    <p>Registro de facturas recibidas</p>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Facturas Registradas (<?php echo count($facturas); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($facturas)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p>No hay facturas registradas</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>N° Factura</th>
                            <th>Proveedor</th>
                            <th>Orden de Compra</th>
                            <th>Fecha</th>
                            <th>Monto Total</th>
                            <th>CUF</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facturas as $fac): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($fac['numero_factura']); ?></strong></td>
                                <td><?php echo htmlspecialchars($fac['proveedor_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($fac['orden_codigo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($fac['fecha_factura'])); ?></td>
                                <td><strong>Bs. <?php echo number_format($fac['monto_total'], 2); ?></strong></td>
                                <td>
                                    <small style="color: var(--text-muted);">
                                        <?php echo htmlspecialchars(substr($fac['cuf'] ?? '', 0, 20) . '...'); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($fac['pdf_url']): ?>
                                        <a href="<?php echo htmlspecialchars($fac['pdf_url']); ?>" 
                                           target="_blank" class="action-btn view" title="Ver PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    <?php endif; ?>
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

