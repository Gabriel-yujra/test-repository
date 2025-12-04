<?php
require_once '../../includes/header.php';

$conn = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razon_social = $_POST['razon_social'] ?? '';
    $nit = $_POST['nit'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $es_formal = isset($_POST['es_formal']) ? 1 : 0;
    
    if (empty($razon_social)) {
        $error = 'La razón social es obligatoria';
    } else {
        $stmt = $conn->prepare("INSERT INTO proveedores (razon_social, nit, telefono, es_formal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $razon_social, $nit, $telefono, $es_formal);
        
        if ($stmt->execute()) {
            $success = 'Proveedor creado exitosamente';
            header("Location: /proyecto_inventario/modules/proveedores/listar.php?success=" . urlencode($success));
            exit();
        } else {
            $error = 'Error al crear el proveedor: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-plus"></i> Nuevo Proveedor</h1>
    <p>Registrar un nuevo proveedor</p>
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
                    <input type="text" name="razon_social" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> NIT/RUC</label>
                    <input type="text" name="nit">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="text" name="telefono">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="es_formal" value="1" checked>
                        <i class="fas fa-check-circle"></i> Proveedor Formal
                    </label>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Proveedor
                </button>
                <a href="/proyecto_inventario/modules/proveedores/listar.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

