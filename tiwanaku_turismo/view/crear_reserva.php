<?php
$tour = isset($datos_a_vista["data"]['tour']) ? $datos_a_vista["data"]['tour'] : null;
$tours = isset($datos_a_vista["data"]['tours']) ? $datos_a_vista["data"]['tours'] : [];
$tour_id = isset($_GET['tour_id']) ? $_GET['tour_id'] : (isset($tour['id']) ? $tour['id'] : '');
?>

<div class="row">
	<div class="col-md-8 offset-md-2">
		<div class="card">
			<div class="card-header bg-success text-white">
				<h4 class="mb-0"><i class="bi bi-calendar-plus"></i> Nueva Reserva</h4>
			</div>
			<div class="card-body">
				<form class="form" action="index.php?controller=reservation&action=save" method="POST">
					<div class="mb-3">
						<label class="form-label">Tour *</label>
						<select class="form-select" name="tour_id" id="tour_id" required onchange="actualizarInfoTour()">
							<option value="">Seleccione un tour</option>
							<?php foreach($tours as $t): ?>
								<option value="<?php echo $t['id']; ?>" 
									<?php echo ($tour_id == $t['id']) ? 'selected' : ''; ?>
									data-precio="<?php echo $t['precio']; ?>"
									data-cupos="<?php echo $t['cupos_disponibles']; ?>">
									<?php echo htmlspecialchars($t['nombre']); ?> - Bs. <?php echo number_format($t['precio'], 2); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<small class="form-text text-muted" id="cupos-info"></small>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Nombre del Cliente *</label>
							<input class="form-control" type="text" name="cliente_nombre" required />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Email *</label>
							<input class="form-control" type="email" name="cliente_email" required />
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Teléfono *</label>
							<input class="form-control" type="text" name="cliente_telefono" required />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Fecha del Tour *</label>
							<input class="form-control" type="date" name="fecha_tour" 
								min="<?php echo date('Y-m-d'); ?>" required />
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Cantidad de Personas *</label>
						<input class="form-control" type="number" name="cantidad_personas" 
							id="cantidad_personas" min="1" value="1" required onchange="calcularTotal()" />
						<small class="form-text text-muted" id="total-info"></small>
					</div>
					<div class="d-grid gap-2 d-md-flex justify-content-md-end">
						<input type="submit" value="Reservar" class="btn btn-success" />
						<a class="btn btn-secondary" href="index.php?controller=tour&action=list">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
function actualizarInfoTour() {
	const seleccion = document.getElementById('tour_id');
	const opcion = seleccion.options[seleccion.selectedIndex];
	const cupos = opcion.getAttribute('data-cupos');
	const infoCupos = document.getElementById('cupos-info');
	
	if(cupos) {
		infoCupos.textContent = 'Cupos disponibles: ' + cupos;
		document.getElementById('cantidad_personas').max = cupos;
	} else {
		infoCupos.textContent = '';
	}
	calcularTotal();
}

function calcularTotal() {
	const seleccion = document.getElementById('tour_id');
	const cantidad = document.getElementById('cantidad_personas').value;
	const opcion = seleccion.options[seleccion.selectedIndex];
	const precio = opcion.getAttribute('data-precio');
	const infoTotal = document.getElementById('total-info');
	
	if(precio && cantidad) {
		const total = parseFloat(precio) * parseInt(cantidad);
		infoTotal.textContent = 'Total: Bs. ' + total.toFixed(2);
	}
}

document.addEventListener('DOMContentLoaded', function() {
	actualizarInfoTour();
});
</script>

