<?php
$tour = isset($datos_a_vista["data"]['tour']) ? $datos_a_vista["data"]['tour'] : [];
$id = isset($tour["id"]) ? $tour["id"] : "";
$nombre = isset($tour["nombre"]) ? $tour["nombre"] : "";
$descripcion = isset($tour["descripcion"]) ? $tour["descripcion"] : "";
$categoria = isset($tour["categoria"]) ? $tour["categoria"] : "";
$precio = isset($tour["precio"]) ? $tour["precio"] : "";
$duracion = isset($tour["duracion"]) ? $tour["duracion"] : "";
$cupos_disponibles = isset($tour["cupos_disponibles"]) ? $tour["cupos_disponibles"] : "";
?>

<div class="row">
	<div class="col-md-8 offset-md-2">
		<?php
		if(isset($_GET["response"]) && $_GET["response"] === true){
			?>
			<div class="alert alert-success">
				<i class="bi bi-check-circle"></i> Operación realizada correctamente. 
				<a href="index.php?controller=tour&action=list">Volver al listado</a>
			</div>
			<?php
		}
		if(isset($_GET["error"]) && $_GET["error"] === true){
			?>
			<div class="alert alert-danger">
				<i class="bi bi-exclamation-triangle"></i> Error al guardar el tour. Por favor, verifique los datos.
			</div>
			<?php
		}
		?>
		<div class="card">
			<div class="card-header">
				<h4 class="mb-0"><?php echo $id ? 'Editar Tour' : 'Nuevo Tour'; ?></h4>
			</div>
			<div class="card-body">
				<form class="form" action="index.php?controller=tour&action=save" method="POST">
					<input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" />
					<div class="mb-3">
						<label class="form-label">Nombre del Tour *</label>
						<input class="form-control" type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required />
					</div>
					<div class="mb-3">
						<label class="form-label">Descripción *</label>
						<textarea class="form-control" name="descripcion" rows="5" required><?php echo htmlspecialchars($descripcion); ?></textarea>
					</div>
					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label">Categoría *</label>
							<select class="form-select" name="categoria" required>
								<option value="">Seleccione una categoría</option>
								<option value="Arqueológico" <?php echo $categoria == 'Arqueológico' ? 'selected' : ''; ?>>Arqueológico</option>
								<option value="Cultural" <?php echo $categoria == 'Cultural' ? 'selected' : ''; ?>>Cultural</option>
								<option value="Histórico" <?php echo $categoria == 'Histórico' ? 'selected' : ''; ?>>Histórico</option>
								<option value="Religioso" <?php echo $categoria == 'Religioso' ? 'selected' : ''; ?>>Religioso</option>
								<option value="Aventura" <?php echo $categoria == 'Aventura' ? 'selected' : ''; ?>>Aventura</option>
							</select>
						</div>
						<div class="col-md-4 mb-3">
							<label class="form-label">Precio (Bs.) *</label>
							<input class="form-control" type="number" name="precio" step="0.01" min="0" value="<?php echo htmlspecialchars($precio); ?>" required />
						</div>
						<div class="col-md-4 mb-3">
							<label class="form-label">Duración (horas) *</label>
							<input class="form-control" type="number" name="duracion" min="1" value="<?php echo htmlspecialchars($duracion); ?>" required />
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Cupos Disponibles *</label>
						<input class="form-control" type="number" name="cupos_disponibles" min="1" value="<?php echo htmlspecialchars($cupos_disponibles); ?>" required />
					</div>
					<div class="d-grid gap-2 d-md-flex justify-content-md-end">
						<input type="submit" value="Guardar" class="btn btn-primary" />
						<a class="btn btn-secondary" href="index.php?controller=tour&action=list">Cancelar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

