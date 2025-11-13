<div class="row mb-4">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-filter"></i> Filtros de Búsqueda</h5>
				<a href="index.php?controller=tour&action=edit" class="btn btn-primary">
					<i class="bi bi-plus-circle"></i> Nuevo Tour
				</a>
			</div>
			<div class="card-body">
				<form method="GET" action="index.php" class="row g-3">
					<input type="hidden" name="controller" value="tour">
					<input type="hidden" name="action" value="list">
					<div class="col-md-10">
						<label class="form-label">Buscar Tour</label>
						<input type="text" class="form-control" name="search" 
							value="<?php echo isset($datos_a_vista['data']['filters']['search']) ? htmlspecialchars($datos_a_vista['data']['filters']['search']) : ''; ?>" 
							placeholder="Buscar por nombre o descripción...">
					</div>
					<div class="col-md-2 d-flex align-items-end">
						<button type="submit" class="btn btn-primary w-100">
							<i class="bi bi-search"></i> Buscar
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php
	if(isset($datos_a_vista['data']['tours']) && count($datos_a_vista['data']['tours']) > 0){
		foreach($datos_a_vista['data']['tours'] as $tour){
			?>
			<div class="col-md-4 mb-4">
				<div class="card h-100 tour-card" data-tour-id="<?php echo $tour['id']; ?>">
					<div class="card-header bg-primary text-white">
						<h5 class="mb-0"><?php echo htmlspecialchars($tour['nombre']); ?></h5>
					</div>
					<div class="card-body">
						<p class="text-muted mb-2">
							<span class="badge bg-info"><?php echo htmlspecialchars($tour['categoria']); ?></span>
						</p>
						<p class="card-text"><?php echo nl2br(htmlspecialchars(substr($tour['descripcion'], 0, 150))); ?>...</p>
						<hr>
						<div class="d-flex justify-content-between align-items-center mb-2">
							<strong class="text-primary">Precio: Bs. <?php echo number_format($tour['precio'], 2); ?></strong>
							<small class="text-muted"><i class="bi bi-clock"></i> <?php echo $tour['duracion']; ?> hrs</small>
						</div>
						<p class="mb-2">
							<small class="text-muted">
								<i class="bi bi-people"></i> Cupos disponibles: <?php echo $tour['cupos_disponibles']; ?>
							</small>
						</p>
					</div>
					<div class="card-footer bg-light">
						<div class="btn-group w-100" role="group">
							<a href="index.php?controller=tour&action=view&id=<?php echo $tour['id']; ?>" class="btn btn-sm btn-outline-primary">
								<i class="bi bi-eye"></i> Ver
							</a>
							<button class="btn btn-sm btn-info add-to-cart-btn" 
								data-tour-id="<?php echo $tour['id']; ?>"
								data-tour-nombre="<?php echo htmlspecialchars($tour['nombre']); ?>"
								data-tour-precio="<?php echo $tour['precio']; ?>"
								data-tour-categoria="<?php echo htmlspecialchars($tour['categoria']); ?>">
								<i class="bi bi-cart-plus"></i> Carrito
							</button>
							<a href="index.php?controller=reservation&action=create&tour_id=<?php echo $tour['id']; ?>" class="btn btn-sm btn-success">
								<i class="bi bi-calendar-plus"></i> Reservar
							</a>
							<a href="index.php?controller=tour&action=edit&id=<?php echo $tour['id']; ?>" class="btn btn-sm btn-warning">
								<i class="bi bi-pencil"></i> Editar
							</a>
							<a href="index.php?controller=tour&action=confirmDelete&id=<?php echo $tour['id']; ?>" class="btn btn-sm btn-danger">
								<i class="bi bi-trash"></i> Eliminar
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}else{
		?>
		<div class="col-md-12">
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i> No se encontraron tours. <a href="index.php?controller=tour&action=edit">Crear nuevo tour</a>
			</div>
		</div>
		<?php
	}
	?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	actualizarInterfazCarrito();
});
</script>

