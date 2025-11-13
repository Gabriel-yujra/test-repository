<div class="row">
	<div class="col-md-6 offset-md-3">
		<div class="card">
			<div class="card-header bg-danger text-white">
				<h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación</h4>
			</div>
			<div class="card-body">
				<form class="form" action="index.php?controller=reservation&action=delete" method="POST">
					<input type="hidden" name="id" value="<?php echo htmlspecialchars($datos_a_vista["data"]["id"]); ?>" />
					<div class="alert alert-warning">
						<b>¿Confirma que desea eliminar esta reserva?</b>
						<hr>
						<p class="mb-1"><strong>Tour:</strong> <?php echo htmlspecialchars($datos_a_vista["data"]["tour_nombre"]); ?></p>
						<p class="mb-1"><strong>Cliente:</strong> <?php echo htmlspecialchars($datos_a_vista["data"]["cliente_nombre"]); ?></p>
						<p class="mb-1"><strong>Fecha del Tour:</strong> <?php echo date('d/m/Y', strtotime($datos_a_vista["data"]["fecha_tour"])); ?></p>
						<p class="mb-0"><strong>Cantidad de Personas:</strong> <?php echo $datos_a_vista["data"]["cantidad_personas"]; ?></p>
					</div>
					<div class="alert alert-danger">
						<small><i class="bi bi-info-circle"></i> Esta acción no se puede deshacer.</small>
					</div>
					<div class="d-grid gap-2 d-md-flex justify-content-md-end">
						<input type="submit" value="Eliminar" class="btn btn-danger" />
						<a class="btn btn-secondary" href="index.php?controller=reservation&action=list">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

