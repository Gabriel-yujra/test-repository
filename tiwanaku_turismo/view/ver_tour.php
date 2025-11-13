<?php
$tour = $datos_a_vista["data"];
if(!$tour){
	echo '<div class="alert alert-danger">Tour no encontrado.</div>';
	echo '<a href="index.php?controller=tour&action=list" class="btn btn-primary">Volver al listado</a>';
	exit;
}
?>

<div class="row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<h3 class="mb-0"><?php echo htmlspecialchars($tour['nombre']); ?></h3>
			</div>
			<div class="card-body">
				<p class="text-muted">
					<span class="badge bg-info me-2"><?php echo htmlspecialchars($tour['categoria']); ?></span>
					<i class="bi bi-clock"></i> Duración: <?php echo $tour['duracion']; ?> horas
				</p>
				<hr>
				<h5>Descripción</h5>
				<p class="card-text"><?php echo nl2br(htmlspecialchars($tour['descripcion'])); ?></p>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<p><strong>Precio:</strong> <span class="text-primary h4">Bs. <?php echo number_format($tour['precio'], 2); ?></span></p>
					</div>
					<div class="col-md-6">
						<p><strong>Cupos disponibles:</strong> <?php echo $tour['cupos_disponibles']; ?></p>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-info btn-lg add-to-cart-btn" 
					data-tour-id="<?php echo $tour['id']; ?>"
					data-tour-nombre="<?php echo htmlspecialchars($tour['nombre']); ?>"
					data-tour-precio="<?php echo $tour['precio']; ?>"
					data-tour-categoria="<?php echo htmlspecialchars($tour['categoria']); ?>">
					<i class="bi bi-cart-plus"></i> Agregar al Carrito
				</button>
				<a href="index.php?controller=reservation&action=create&tour_id=<?php echo $tour['id']; ?>" class="btn btn-success btn-lg">
					<i class="bi bi-calendar-plus"></i> Reservar este Tour
				</a>
				<a href="index.php?controller=tour&action=list" class="btn btn-secondary">
					<i class="bi bi-arrow-left"></i> Volver al listado
				</a>
				<a href="index.php?controller=tour&action=edit&id=<?php echo $tour['id']; ?>" class="btn btn-warning">
					<i class="bi bi-pencil"></i> Editar
				</a>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">Información Adicional</h5>
			</div>
			<div class="card-body">
				<p><strong>Fecha de creación:</strong><br>
					<?php echo date('d/m/Y H:i', strtotime($tour['fecha_creacion'])); ?></p>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	actualizarInterfazCarrito();
});
</script>

