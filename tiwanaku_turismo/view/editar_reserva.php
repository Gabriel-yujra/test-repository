<?php
$reservation = isset($datos_a_vista["data"]['reservation']) ? $datos_a_vista["data"]['reservation'] : [];
$tours = isset($datos_a_vista["data"]['tours']) ? $datos_a_vista["data"]['tours'] : [];
$id = isset($reservation["id"]) ? $reservation["id"] : "";
$tour_id = isset($reservation["tour_id"]) ? $reservation["tour_id"] : "";
$cliente_nombre = isset($reservation["cliente_nombre"]) ? $reservation["cliente_nombre"] : "";
$cliente_email = isset($reservation["cliente_email"]) ? $reservation["cliente_email"] : "";
$cliente_telefono = isset($reservation["cliente_telefono"]) ? $reservation["cliente_telefono"] : "";
$cantidad_personas = isset($reservation["cantidad_personas"]) ? $reservation["cantidad_personas"] : "";
$fecha_tour = isset($reservation["fecha_tour"]) ? $reservation["fecha_tour"] : "";
$estado = isset($reservation["estado"]) ? $reservation["estado"] : "pendiente";
?>

<div class="row">
	<div class="col-md-8 offset-md-2">
		<?php
		if(isset($_GET["response"]) && $_GET["response"] === true){
			?>
			<div class="alert alert-success">
				<i class="bi bi-check-circle"></i> Reserva guardada correctamente. 
				<a href="index.php?controller=reservation&action=list">Volver al listado</a>
			</div>
			<?php
		}
		if(isset($_GET["error"]) && $_GET["error"] === true){
			?>
			<div class="alert alert-danger">
				<i class="bi bi-exclamation-triangle"></i> Error al guardar la reserva. Por favor, verifique los datos.
			</div>
			<?php
		}
		?>
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0">Editar Reserva</h4>
			</div>
			<div class="card-body">
				<form class="form" action="index.php?controller=reservation&action=save" method="POST">
					<input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
					<div class="mb-3">
						<label class="form-label">Tour *</label>
						<select class="form-select" name="tour_id" required>
							<option value="">Seleccione un tour</option>
							<?php foreach($tours as $t): ?>
								<option value="<?php echo $t['id']; ?>" 
									<?php echo ($tour_id == $t['id']) ? 'selected' : ''; ?>>
									<?php echo htmlspecialchars($t['nombre']); ?> - Bs. <?php echo number_format($t['precio'], 2); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Nombre del Cliente *</label>
							<input class="form-control" type="text" name="cliente_nombre" value="<?php echo htmlspecialchars($cliente_nombre); ?>" required />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Email *</label>
							<input class="form-control" type="email" name="cliente_email" value="<?php echo htmlspecialchars($cliente_email); ?>" required />
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Teléfono *</label>
							<input class="form-control" type="text" name="cliente_telefono" value="<?php echo htmlspecialchars($cliente_telefono); ?>" required />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Fecha del Tour *</label>
							<input class="form-control" type="date" name="fecha_tour" value="<?php echo htmlspecialchars($fecha_tour); ?>" required />
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Cantidad de Personas *</label>
							<input class="form-control" type="number" name="cantidad_personas" min="1" value="<?php echo htmlspecialchars($cantidad_personas); ?>" required />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Estado *</label>
							<select class="form-select" name="estado" required>
								<option value="pendiente" <?php echo $estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
								<option value="confirmada" <?php echo $estado == 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
								<option value="cancelada" <?php echo $estado == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
							</select>
						</div>
					</div>
					<div class="d-grid gap-2 d-md-flex justify-content-md-end">
						<input type="submit" value="Guardar" class="btn btn-primary" />
						<a class="btn btn-secondary" href="index.php?controller=reservation&action=list">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

