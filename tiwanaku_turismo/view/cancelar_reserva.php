<div class="row">
	<div class="col-md-6 offset-md-3">
		<?php if(isset($datos_a_vista['data']['success']) && $datos_a_vista['data']['success']): ?>
			<div class="alert alert-success">
				<i class="bi bi-check-circle"></i> Reserva cancelada correctamente. 
				<a href="index.php?controller=reservation&action=list" class="alert-link">Volver al listado</a>
			</div>
		<?php else: ?>
			<div class="alert alert-danger">
				<i class="bi bi-exclamation-triangle"></i> Error al cancelar la reserva. 
				<a href="index.php?controller=reservation&action=list" class="alert-link">Volver al listado</a>
			</div>
		<?php endif; ?>
	</div>
</div>

