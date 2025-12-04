<?php
require_once '../../includes/header.php';

if (!hasRole(['Administrador'])) {
    header('Location: /proyecto_inventario/index.php');
    exit();
}

$conn = getDBConnection();

$almacenes = getMultipleRecords("
    SELECT a.*, u.nombre_completo as responsable_nombre
    FROM almacenes a
    LEFT JOIN usuarios u ON a.usuario_responsable_id = u.id
    ORDER BY a.nombre
");

$usuarios = getMultipleRecords("SELECT * FROM usuarios WHERE estado = 'activo' ORDER BY nombre_completo");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-warehouse"></i> Gestión de Almacenes</h1>
    <p>Administración de almacenes</p>
</div>

<div style="margin-bottom: 1.5rem;">
    <a href="/proyecto_inventario/modules/admin/crear_almacen.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Almacén
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Almacenes Registrados (<?php echo count($almacenes); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($almacenes)): ?>
            <div class="empty-state">
                <i class="fas fa-warehouse"></i>
                <p>No hay almacenes registrados</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Zona</th>
                            <th>Dirección</th>
                            <th>Responsable</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($almacenes as $alm): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($alm['nombre']); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $alm['tipo'] === 'central' ? 'badge-info' : 'badge-warning'; ?>">
                                        <?php echo ucfirst($alm['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $alm['zona'] ?? '-')); ?></td>
                                <td><?php echo htmlspecialchars($alm['direccion'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($alm['responsable_nombre'] ?? '-'); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="/proyecto_inventario/modules/admin/editar_almacen.php?id=<?php echo $alm['id']; ?>" 
                                           class="action-btn edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
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

