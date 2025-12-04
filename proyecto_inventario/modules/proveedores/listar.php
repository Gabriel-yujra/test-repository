<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

$search = $_GET['search'] ?? '';
$estado = $_GET['estado'] ?? '';

$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(razon_social LIKE ? OR nit LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT * FROM proveedores $whereClause ORDER BY razon_social";
$proveedores = [];

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
}

// Obtener estadísticas por proveedor
foreach ($proveedores as &$prov) {
    $stats = getSingleRecord("
        SELECT COUNT(DISTINCT oc.id) as total_ordenes,
               SUM(oc.monto_total) as total_compras
        FROM ordenes_compra oc
        WHERE oc.proveedor_id = ? AND oc.estado != 'cancelada'
    ", [$prov['id']]);
    
    $prov['total_ordenes'] = $stats['total_ordenes'] ?? 0;
    $prov['total_compras'] = $stats['total_compras'] ?? 0;
}

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-truck"></i> Gestión de Proveedores</h1>
    <p>Listado y administración de proveedores</p>
</div>

<div class="filters">
    <form method="GET" class="filters-row">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Buscar por nombre o NIT..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Buscar
            </button>
            <a href="/proyecto_inventario/modules/proveedores/listar.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Limpiar
            </a>
        </div>
    </form>
</div>

<div style="margin-bottom: 1.5rem;">
    <a href="/proyecto_inventario/modules/proveedores/crear.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Proveedor
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Proveedores Registrados (<?php echo count($proveedores); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($proveedores)): ?>
            <div class="empty-state">
                <i class="fas fa-truck"></i>
                <p>No se encontraron proveedores</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Razón Social</th>
                            <th>NIT</th>
                            <th>Teléfono</th>
                            <th>Tipo</th>
                            <th>Órdenes</th>
                            <th>Total Compras</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proveedores as $prov): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($prov['razon_social']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prov['nit'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($prov['telefono'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($prov['es_formal']): ?>
                                        <span class="badge badge-success">Formal</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Informal</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $prov['total_ordenes']; ?></td>
                                <td>Bs. <?php echo number_format($prov['total_compras'], 2); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="/proyecto_inventario/modules/proveedores/ver.php?id=<?php echo $prov['id']; ?>" 
                                           class="action-btn view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/proyecto_inventario/modules/proveedores/editar.php?id=<?php echo $prov['id']; ?>" 
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

