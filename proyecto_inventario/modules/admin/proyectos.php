<?php
require_once '../../includes/header.php';

if (!hasRole(['Administrador'])) {
    header('Location: /proyecto_inventario/index.php');
    exit();
}

$conn = getDBConnection();

$proyectos = getMultipleRecords("
    SELECT p.*, u.nombre_completo as residente_nombre, a.nombre as almacen_nombre
    FROM proyectos p
    LEFT JOIN usuarios u ON p.residente_usuario_id = u.id
    LEFT JOIN almacenes a ON p.almacen_id = a.id
    ORDER BY p.nombre
");

$usuarios = getMultipleRecords("SELECT * FROM usuarios WHERE estado = 'activo' ORDER BY nombre_completo");
$almacenes = getMultipleRecords("SELECT * FROM almacenes ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-building"></i> Gestión de Proyectos</h1>
    <p>Administración de proyectos</p>
</div>

<div style="margin-bottom: 1.5rem;">
    <a href="/proyecto_inventario/modules/admin/crear_proyecto.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Proyecto
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Proyectos Registrados (<?php echo count($proyectos); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($proyectos)): ?>
            <div class="empty-state">
                <i class="fas fa-building"></i>
                <p>No hay proyectos registrados</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Residente</th>
                            <th>Almacén</th>
                            <th>Presupuesto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proyectos as $proy): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($proy['codigo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($proy['nombre']); ?></td>
                                <td>
                                    <?php
                                    $estado_badge = [
                                        'activo' => 'badge-success',
                                        'pausado' => 'badge-warning',
                                        'finalizado' => 'badge-secondary'
                                    ];
                                    $estado_class = $estado_badge[$proy['estado']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?php echo $estado_class; ?>">
                                        <?php echo ucfirst($proy['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($proy['residente_nombre'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($proy['almacen_nombre'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($proy['presupuesto_limite']): ?>
                                        Bs. <?php echo number_format($proy['presupuesto_limite'], 2); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="/proyecto_inventario/modules/admin/editar_proyecto.php?id=<?php echo $proy['id']; ?>" 
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

