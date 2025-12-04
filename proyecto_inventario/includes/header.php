<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireAuth();

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario - Material de Construcción</title>
    <link rel="stylesheet" href="/proyecto_inventario/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-warehouse"></i> Inventario</h2>
                <p class="user-info">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($currentUser['nombre_completo'] ?? $currentUser['nombre_usuario']); ?>
                    <span class="role-badge"><?php echo htmlspecialchars($currentUser['rol_nombre']); ?></span>
                </p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="/proyecto_inventario/index.php" class="nav-item <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-boxes"></i> Materiales
                    </div>
                    <a href="/proyecto_inventario/modules/materiales/listar.php" class="nav-item <?php echo strpos($currentPage, 'materiales') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-list"></i> Listar Materiales
                    </a>
                    <a href="/proyecto_inventario/modules/materiales/crear.php" class="nav-item">
                        <i class="fas fa-plus"></i> Nuevo Material
                    </a>
                    <a href="/proyecto_inventario/modules/materiales/categorias.php" class="nav-item">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-truck"></i> Proveedores
                    </div>
                    <a href="/proyecto_inventario/modules/proveedores/listar.php" class="nav-item <?php echo strpos($currentPage, 'proveedores') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-list"></i> Listar Proveedores
                    </a>
                    <a href="/proyecto_inventario/modules/proveedores/crear.php" class="nav-item">
                        <i class="fas fa-plus"></i> Nuevo Proveedor
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-exchange-alt"></i> Movimientos
                    </div>
                    <a href="/proyecto_inventario/modules/movimientos/ingresos.php" class="nav-item <?php echo strpos($currentPage, 'ingresos') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-arrow-down"></i> Ingresos
                    </a>
                    <a href="/proyecto_inventario/modules/movimientos/salidas.php" class="nav-item <?php echo strpos($currentPage, 'salidas') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-arrow-up"></i> Salidas
                    </a>
                    <a href="/proyecto_inventario/modules/movimientos/kardex.php" class="nav-item <?php echo strpos($currentPage, 'kardex') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-history"></i> Kardex
                    </a>
                    <a href="/proyecto_inventario/modules/movimientos/transferencias.php" class="nav-item">
                        <i class="fas fa-shipping-fast"></i> Transferencias
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-shopping-cart"></i> Compras
                    </div>
                    <a href="/proyecto_inventario/modules/compras/ordenes.php" class="nav-item">
                        <i class="fas fa-file-invoice"></i> Órdenes de Compra
                    </a>
                    <a href="/proyecto_inventario/modules/compras/facturas.php" class="nav-item">
                        <i class="fas fa-receipt"></i> Facturas
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </div>
                    <a href="/proyecto_inventario/modules/reportes/inventario.php" class="nav-item <?php echo strpos($currentPage, 'reportes') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list"></i> Reporte de Inventario
                    </a>
                    <a href="/proyecto_inventario/modules/reportes/consumos.php" class="nav-item">
                        <i class="fas fa-chart-line"></i> Consumos por Proyecto
                    </a>
                    <a href="/proyecto_inventario/modules/reportes/alertas.php" class="nav-item">
                        <i class="fas fa-exclamation-triangle"></i> Alertas de Stock
                    </a>
                </div>
                
                <?php if (hasRole(['Administrador'])): ?>
                <div class="nav-section">
                    <div class="nav-section-title">
                        <i class="fas fa-cog"></i> Administración
                    </div>
                    <a href="/proyecto_inventario/modules/admin/usuarios.php" class="nav-item">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                    <a href="/proyecto_inventario/modules/admin/almacenes.php" class="nav-item">
                        <i class="fas fa-warehouse"></i> Almacenes
                    </a>
                    <a href="/proyecto_inventario/modules/admin/proyectos.php" class="nav-item">
                        <i class="fas fa-building"></i> Proyectos
                    </a>
                </div>
                <?php endif; ?>
            </nav>
            
            <div class="sidebar-footer">
                <a href="/proyecto_inventario/logout.php" class="nav-item logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">

