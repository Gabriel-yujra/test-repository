<?php
require_once '../../includes/header.php';

if (!hasRole(['Administrador'])) {
    header('Location: /proyecto_inventario/index.php');
    exit();
}

$conn = getDBConnection();

$usuarios = getMultipleRecords("
    SELECT u.*, r.nombre as rol_nombre
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    ORDER BY u.nombre_completo
");

$roles = getMultipleRecords("SELECT * FROM roles ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
    <p>Administración de usuarios del sistema</p>
</div>

<div style="margin-bottom: 1.5rem;">
    <a href="/proyecto_inventario/modules/admin/crear_usuario.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Usuarios Registrados (<?php echo count($usuarios); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($usuarios)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No hay usuarios registrados</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['nombre_usuario']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['nombre_completo'] ?? '-'); ?></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($user['rol_nombre']); ?></span></td>
                                <td>
                                    <?php if ($user['estado'] === 'activo'): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['fecha_creacion'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="/proyecto_inventario/modules/admin/editar_usuario.php?id=<?php echo $user['id']; ?>" 
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

