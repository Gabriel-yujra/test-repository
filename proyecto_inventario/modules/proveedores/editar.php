<?php
require_once '../../includes/header.php';

$id = $_GET['id'] ?? 0;
$conn = getDBConnection();
$error = '';

if (!$id) {
    header('Location: /proyecto_inventario/modules/proveedores/listar.php');
    exit();
}

$proveedor = getSingleRecord("SELECT * FROM proveedores WHERE id = ?", [$id]);

if (!$proveedor) {
    header('Location: /proyecto_inventario/modules/proveedores/listar.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razon_social = $_POST['razon_social'] ?? '';
    $nit = $_POST['nit'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $es_formal = isset($_POST['es_formal']) ? 1 : 0;
    
    if (empty($razon_social)) {
        $error = 'La razón social es obligatoria';
    } else {
        $stmt = $conn->prepare("UPDATE proveedores SET razon_social = ?, nit = ?, telefono = ?, es_formal = ? WHERE id = ?");
        $stmt->bind_param("sssii", $razon_social, $nit, $telefono, $es_formal, $id);
        
        if ($stmt->execute()) {
            header("Location: /proyecto_inventario/modules/proveedores/listar.php?success=Proveedor actualizado exitosamente");
            exit();
        } else {
            $error = 'Error al actualizar: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Editar Proveedor</h1>
    <p>Modificar información del proveedor</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-truck"></i> Información del Proveedor</h2>
    </div>
    <div class="card-body">
        <form method="POST" id="proveedorForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-building"></i> Razón Social *</label>
                    <input type="text" name="razon_social" value="<?php echo htmlspecialchars($proveedor['razon_social']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> NIT/RUC</label>
                    <input type="text" name="nit" value="<?php echo htmlspecialchars($proveedor['nit'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($proveedor['telefono'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="es_formal" value="1" <?php echo $proveedor['es_formal'] ? 'checked' : ''; ?>>
                        <i class="fas fa-check-circle"></i> Proveedor Formal
                    </label>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Proveedor
                </button>
                <a href="/proyecto_inventario/modules/proveedores/listar.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

