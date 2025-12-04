<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

$transferencias = getMultipleRecords("
    SELECT t.*,
           ao.nombre as almacen_origen,
           ad.nombre as almacen_destino
    FROM transferencias t
    JOIN almacenes ao ON t.almacen_origen_id = ao.id
    JOIN almacenes ad ON t.almacen_destino_id = ad.id
    ORDER BY t.fecha_creacion DESC
    LIMIT 50
");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-shipping-fast"></i> Transferencias entre Almacenes</h1>
    <p>Gestión de transferencias de materiales</p>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Transferencias Registradas (<?php echo count($transferencias); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($transferencias)): ?>
            <div class="empty-state">
                <i class="fas fa-shipping-fast"></i>
                <p>No hay transferencias registradas</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Almacén Origen</th>
                            <th>Almacén Destino</th>
                            <th>Estado</th>
                            <th>Fecha Recepción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transferencias as $trans): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($trans['fecha_creacion'])); ?></td>
                                <td><?php echo htmlspecialchars($trans['almacen_origen']); ?></td>
                                <td><?php echo htmlspecialchars($trans['almacen_destino']); ?></td>
                                <td>
                                    <?php
                                    $estado_badge = [
                                        'pendiente' => 'badge-warning',
                                        'en_transito' => 'badge-info',
                                        'recibido' => 'badge-success',
                                        'rechazado' => 'badge-danger'
                                    ];
                                    $estado_class = $estado_badge[$trans['estado']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $estado_class; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $trans['estado'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $trans['fecha_recepcion'] ? date('d/m/Y H:i', strtotime($trans['fecha_recepcion'])) : '-'; ?>
                                </td>
                                <td>
                                    <a href="?ver=<?php echo $trans['id']; ?>" class="action-btn view" title="Ver detalles">
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

