<?php
require_once '../../includes/header.php';

$conn = getDBConnection();
$error = '';
$success = '';

// Crear categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    
    if (empty($nombre)) {
        $error = 'El nombre es obligatorio';
    } else {
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $descripcion);
        
        if ($stmt->execute()) {
            $success = 'Categoría creada exitosamente';
        } else {
            $error = 'Error al crear la categoría';
        }
        $stmt->close();
    }
}

// Eliminar categoría
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = 'Categoría eliminada exitosamente';
    } else {
        $error = 'No se puede eliminar la categoría porque tiene materiales asociados';
    }
    $stmt->close();
}

$categorias = getMultipleRecords("SELECT c.*, COUNT(m.id) as total_materiales 
                                  FROM categorias c 
                                  LEFT JOIN materiales m ON c.id = m.categoria_id 
                                  GROUP BY c.id 
                                  ORDER BY c.nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-tags"></i> Gestión de Categorías</h1>
    <p>Organizar materiales por categorías</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Crear Categoría -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-plus"></i> Nueva Categoría</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nombre *</label>
                    <input type="text" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Descripción</label>
                    <textarea name="descripcion" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Lista de Categorías -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Categorías Existentes</h2>
        </div>
        <div class="card-body">
            <?php if (empty($categorias)): ?>
                <p class="empty-state">No hay categorías registradas</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Materiales</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cat['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cat['descripcion'] ?? '-'); ?></td>
                                <td><span class="badge badge-info"><?php echo $cat['total_materiales']; ?></span></td>
                                <td>
                                    <a href="?delete=<?php echo $cat['id']; ?>" 
                                       class="action-btn delete" 
                                       onclick="return confirmDelete('¿Eliminar esta categoría?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    <a href="/proyecto_inventario/modules/materiales/listar.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver a Materiales
    </a>
</div>

<?php require_once '../../includes/footer.php'; ?>

