<div class="row">
	<div class="col-md-6 offset-md-3">
		<div class="card">
			<div class="card-header bg-danger text-white">
				<h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación</h4>
			</div>
			<div class="card-body">
				<form class="form" action="index.php?controller=tour&action=delete" method="POST">
					<input type="hidden" name="id" value="<?php echo htmlspecialchars($datos_a_vista["data"]["id"]); ?>" />
					<div class="alert alert-warning">
						<b>¿Confirma que desea eliminar este tour?</b>
						<hr>
						<p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($datos_a_vista["data"]["nombre"]); ?></p>
						<p class="mb-1"><strong>Categoría:</strong> <?php echo htmlspecialchars($datos_a_vista["data"]["categoria"]); ?></p>
						<p class="mb-0"><strong>Precio:</strong> Bs. <?php echo number_format($datos_a_vista["data"]["precio"], 2); ?></p>
					</div>
					<div class="alert alert-danger">
						<small><i class="bi bi-info-circle"></i> Esta acción no se puede deshacer.</small>
					</div>
					<div class="d-grid gap-2 d-md-flex justify-content-md-end">
						<input type="submit" value="Eliminar" class="btn btn-danger" />
						<a class="btn btn-secondary" href="index.php?controller=tour&action=list">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

