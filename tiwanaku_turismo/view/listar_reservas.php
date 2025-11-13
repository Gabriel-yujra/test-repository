<div class="row mb-4">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-calendar-check"></i> Reservas</h5>
				<a href="index.php?controller=reservation&action=create" class="btn btn-success">
					<i class="bi bi-plus-circle"></i> Nueva Reserva
				</a>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php
	if(isset($datos_a_vista['data']) && count($datos_a_vista['data']) > 0){
		foreach($datos_a_vista['data'] as $reservation){
			$badgeClass = 'bg-secondary';
			if($reservation['estado'] == 'confirmada') $badgeClass = 'bg-success';
			if($reservation['estado'] == 'cancelada') $badgeClass = 'bg-danger';
			if($reservation['estado'] == 'pendiente') $badgeClass = 'bg-warning';
			?>
			<div class="col-md-6 mb-4">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0"><?php echo htmlspecialchars($reservation['tour_nombre']); ?></h5>
						<span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($reservation['estado']); ?></span>
					</div>
					<div class="card-body">
						<p class="mb-2"><strong>Cliente:</strong> <?php echo htmlspecialchars($reservation['cliente_nombre']); ?></p>
						<p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($reservation['cliente_email']); ?></p>
						<p class="mb-2"><strong>Teléfono:</strong> <?php echo htmlspecialchars($reservation['cliente_telefono']); ?></p>
						<hr>
						<p class="mb-2"><strong>Fecha del Tour:</strong> <?php echo date('d/m/Y', strtotime($reservation['fecha_tour'])); ?></p>
						<p class="mb-2"><strong>Cantidad de Personas:</strong> <?php echo $reservation['cantidad_personas']; ?></p>
						<p class="mb-2"><strong>Precio Total:</strong> Bs. <?php echo number_format($reservation['tour_precio'] * $reservation['cantidad_personas'], 2); ?></p>
						<p class="mb-0"><strong>Fecha de Reserva:</strong> <?php echo date('d/m/Y H:i', strtotime($reservation['fecha_reserva'])); ?></p>
					</div>
					<div class="card-footer">
						<div class="btn-group w-100" role="group">
							<a href="index.php?controller=reservation&action=edit&id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-warning">
								<i class="bi bi-pencil"></i> Editar
							</a>
							<?php if($reservation['estado'] != 'cancelada'): ?>
								<a href="index.php?controller=reservation&action=cancel&id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-danger" 
									onclick="return confirm('¿Está seguro de cancelar esta reserva?');">
									<i class="bi bi-x-circle"></i> Cancelar
								</a>
							<?php endif; ?>
							<a href="index.php?controller=reservation&action=confirmDelete&id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-danger">
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
				<i class="bi bi-info-circle"></i> No se encontraron reservas. <a href="index.php?controller=reservation&action=create">Crear nueva reserva</a>
			</div>
		</div>
		<?php
	}
	?>
</div>

