<?php
// Obtener preferencias de cookies (solo tema, sin mostrar datos al usuario)
$tema = isset($_COOKIE['pref_tema']) ? $_COOKIE['pref_tema'] : 'claro';

// Aplicar tema
$themeClass = $tema === 'oscuro' ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo isset($controlador->page_title) ? htmlspecialchars($controlador->page_title) : 'Turismo Tiwanaku'; ?> - <?php echo constant('APP_NAME'); ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<?php if($tema === 'oscuro'): ?>
		<link rel="stylesheet" href="assets/css/dark-theme.css">
	<?php endif; ?>
</head>
<body class="theme-<?php echo $themeClass; ?>" data-theme="<?php echo $tema; ?>">
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
		<div class="container">
			<a class="navbar-brand" href="index.php?controller=tour&action=list">
				<i class="bi bi-geo-alt-fill"></i> Turismo Tiwanaku
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<?php if(isset($_SESSION['user_id'])): ?>
					<ul class="navbar-nav me-auto">
						<li class="nav-item">
							<a class="nav-link" href="index.php?controller=tour&action=list">
								<i class="bi bi-list-ul"></i> Tours
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="index.php?controller=reservation&action=list">
								<i class="bi bi-calendar-check"></i> Reservas
							</a>
						</li>
					</ul>
				<?php endif; ?>
				<ul class="navbar-nav">
					<li class="nav-item">
						<button class="btn btn-link nav-link theme-toggle-btn" id="themeToggle" title="Cambiar tema" style="color: inherit; text-decoration: none; padding: 0.5rem 1rem;">
							<i class="bi <?php echo $tema === 'oscuro' ? 'bi-sun-fill' : 'bi-moon-fill'; ?>" id="themeIcon"></i>
						</button>
					</li>
					<?php if(isset($_SESSION['user_id'])): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="cartDropdown" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-cart-fill"></i> Carrito (<span id="cartCount">0</span>)
							</a>
							<ul class="dropdown-menu" id="cartList">
								<li><a class="dropdown-item text-center" href="#">El carrito está vacío</a></li>
							</ul>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="index.php?controller=tour&action=list">Tours</a></li>
								<li><a class="dropdown-item" href="index.php?controller=reservation&action=list">Mis Reservas</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="index.php?controller=auth&action=logout">
									<i class="bi bi-box-arrow-right"></i> Cerrar Sesión
								</a></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="index.php?controller=auth&action=login">
								<i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<header class="mb-4">
			<div class="p-4 bg-light rounded">
				<h1 class="mb-2"><?php echo isset($controlador->page_title) ? htmlspecialchars($controlador->page_title) : 'Turismo Tiwanaku'; ?></h1>
				<p class="text-muted mb-0">Explora los misterios de Tiwanaku, la cuna de la civilización andina</p>
			</div>
		</header>

