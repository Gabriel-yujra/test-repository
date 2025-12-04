<?php
require_once '../../includes/header.php';

$conn = getDBConnection();

// Filtros
$search = $_GET['search'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';
$almacen_id = $_GET['almacen_id'] ?? '';

// Construir consulta
$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(m.nombre LIKE ? OR m.codigo LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if (!empty($categoria_id)) {
    $where[] = "m.categoria_id = ?";
    $params[] = $categoria_id;
    $types .= 'i';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Obtener materiales con stock
$query = "
    SELECT m.*, 
           c.nombre as categoria_nombre,
           u.abreviacion as unidad_abreviacion,
           COALESCE(SUM(i.cantidad), 0) as stock_total
    FROM materiales m
    LEFT JOIN categorias c ON m.categoria_id = c.id
    LEFT JOIN unidades u ON m.unidad_base_id = u.id
    LEFT JOIN inventario_stock i ON m.id = i.material_id
    $whereClause
    GROUP BY m.id
    ORDER BY m.nombre ASC
";

$materiales = [];
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $materiales[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $materiales[] = $row;
    }
}

// Obtener categorías para filtro
$categorias = getMultipleRecords("SELECT * FROM categorias ORDER BY nombre");
$almacenes = getMultipleRecords("SELECT * FROM almacenes ORDER BY nombre");

$conn->close();
?>

<div class="page-header">
    <h1><i class="fas fa-boxes"></i> Gestión de Materiales</h1>
    <p>Listado y administración de materiales</p>
</div>

<!-- Filtros -->
<div class="filters">
    <form method="GET" class="filters-row">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Buscar por código o nombre..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="form-group">
            <select name="categoria_id">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="/proyecto_inventario/modules/materiales/listar.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Acciones -->
<div style="margin-bottom: 1.5rem;">
    <a href="/proyecto_inventario/modules/materiales/crear.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Material
    </a>
    <a href="/proyecto_inventario/modules/materiales/categorias.php" class="btn btn-secondary">
        <i class="fas fa-tags"></i> Gestionar Categorías
    </a>
</div>

<!-- Tabla de materiales -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Materiales Registrados (<?php echo count($materiales); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($materiales)): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>No se encontraron materiales</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Unidad</th>
                            <th>Stock Total</th>
                            <th>Stock Mínimo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiales as $mat): 
                            $stock_critico = $mat['stock_total'] <= $mat['alerta_stock_minimo'];
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($mat['codigo']); ?></strong></td>
                                <td><?php echo htmlspecialchars($mat['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($mat['marca'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mat['categoria_nombre'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($mat['unidad_abreviacion'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge <?php echo $stock_critico ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo number_format($mat['stock_total'], 2); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($mat['alerta_stock_minimo'] ?? 0, 2); ?></td>
                                <td>
                                    <?php if ($stock_critico && $mat['stock_total'] > 0): ?>
                                        <span class="badge badge-warning">Crítico</span>
                                    <?php elseif ($mat['stock_total'] == 0): ?>
                                        <span class="badge badge-danger">Sin Stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="/proyecto_inventario/modules/materiales/ver.php?id=<?php echo $mat['id']; ?>" 
                                           class="action-btn view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/proyecto_inventario/modules/materiales/editar.php?id=<?php echo $mat['id']; ?>" 
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

