<?php
require_once 'includes/header.php';

$conn = getDBConnection();

// Estadísticas generales
$stats = [
    'materiales' => getSingleRecord("SELECT COUNT(*) as total FROM materiales")['total'],
    'proveedores' => getSingleRecord("SELECT COUNT(*) as total FROM proveedores")['total'],
    'almacenes' => getSingleRecord("SELECT COUNT(*) as total FROM almacenes")['total'],
    'proyectos' => getSingleRecord("SELECT COUNT(*) as total FROM proyectos WHERE estado = 'activo'")['total'],
];

// Stock crítico
$stock_critico = getMultipleRecords("
    SELECT m.nombre, m.codigo, i.cantidad, m.alerta_stock_minimo, a.nombre as almacen
    FROM inventario_stock i
    JOIN materiales m ON i.material_id = m.id
    JOIN almacenes a ON i.almacen_id = a.id
    WHERE i.cantidad <= m.alerta_stock_minimo
    ORDER BY i.cantidad ASC
    LIMIT 10
");

// Movimientos recientes
$movimientos_recientes = getMultipleRecords("
    SELECT m.*, u.nombre_completo, a.nombre as almacen_nombre, p.nombre as proyecto_nombre
    FROM movimientos m
    JOIN usuarios u ON m.usuario_id = u.id
    JOIN almacenes a ON m.almacen_id = a.id
    LEFT JOIN proyectos p ON m.proyecto_id = p.id
    ORDER BY m.fecha DESC
    LIMIT 10
");

// Consumo por proyecto (últimos 30 días)
$consumo_proyectos = getMultipleRecords("
    SELECT p.nombre, SUM(dm.cantidad * dm.costo_unitario) as total
    FROM movimientos m
    JOIN detalle_movimientos dm ON m.id = dm.movimiento_id
    JOIN proyectos p ON m.proyecto_id = p.id
    WHERE m.tipo = 'consumo_obra' 
    AND m.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.id, p.nombre
    ORDER BY total DESC
    LIMIT 5
");

$conn->close();
?>

<div class="dashboard">
    <div class="page-header">
        <h1><i class="fas fa-home"></i> Dashboard</h1>
        <p>Resumen general del sistema</p>
    </div>
    
    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['materiales']; ?></h3>
                <p>Materiales Registrados</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon dark-blue">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['proveedores']; ?></h3>
                <p>Proveedores Activos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon gray">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['almacenes']; ?></h3>
                <p>Almacenes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['proyectos']; ?></h3>
                <p>Proyectos Activos</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Stock Crítico -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Stock Crítico</h2>
                <a href="/proyecto_inventario/modules/reportes/alertas.php" class="btn-link">Ver todas</a>
            </div>
            <div class="card-body">
                <?php if (empty($stock_critico)): ?>
                    <p class="empty-state">No hay materiales con stock crítico</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Material</th>
                                <th>Almacén</th>
                                <th>Stock Actual</th>
                                <th>Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stock_critico as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($item['almacen']); ?></td>
                                    <td><span class="badge badge-danger"><?php echo number_format($item['cantidad'], 2); ?></span></td>
                                    <td><?php echo number_format($item['alerta_stock_minimo'], 2); ?></td>
                                    <td><span class="badge badge-warning">Crítico</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Movimientos Recientes -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2><i class="fas fa-history"></i> Movimientos Recientes</h2>
            </div>
            <div class="card-body">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Usuario</th>
                            <th>Almacén</th>
                            <th>Proyecto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimientos_recientes as $mov): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha'])); ?></td>
                                <td><span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $mov['tipo'])); ?></span></td>
                                <td><?php echo htmlspecialchars($mov['nombre_completo'] ?? $mov['usuario_id']); ?></td>
                                <td><?php echo htmlspecialchars($mov['almacen_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($mov['proyecto_nombre'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Consumo por Proyecto -->
    <?php if (!empty($consumo_proyectos)): ?>
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-chart-pie"></i> Consumo por Proyecto (Últimos 30 días)</h2>
        </div>
        <div class="card-body">
            <canvas id="consumoChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
<?php if (!empty($consumo_proyectos)): ?>
const ctx = document.getElementById('consumoChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [<?php echo implode(',', array_map(function($p) { return "'" . htmlspecialchars($p['nombre']) . "'"; }, $consumo_proyectos)); ?>],
        datasets: [{
            data: [<?php echo implode(',', array_column($consumo_proyectos, 'total')); ?>],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(30, 58, 138, 0.8)',
                'rgba(55, 65, 81, 0.8)',
                'rgba(75, 85, 99, 0.8)',
                'rgba(107, 114, 128, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>

