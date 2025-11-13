<div class="row justify-content-center">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header bg-success text-white text-center">
				<h4 class="mb-0"><i class="bi bi-person-plus"></i> Registrarse</h4>
			</div>
			<div class="card-body">
				<?php if(isset($_GET['error'])): ?>
					<div class="alert alert-danger">
						<i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
					</div>
				<?php endif; ?>
				
				<?php if(isset($_GET['success'])): ?>
					<div class="alert alert-success">
						<i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
					</div>
				<?php endif; ?>

				<form method="POST" action="index.php?controller=auth&action=doRegister">
					<div class="mb-3">
						<label class="form-label">Nombre Completo *</label>
						<input type="text" class="form-control" name="nombre_completo" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Usuario *</label>
						<input type="text" class="form-control" name="username" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Email *</label>
						<input type="email" class="form-control" name="email" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Contraseña *</label>
						<input type="password" class="form-control" name="password" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Confirmar Contraseña *</label>
						<input type="password" class="form-control" name="password_confirm" required>
					</div>
					<div class="d-grid">
						<button type="submit" class="btn btn-success">
							<i class="bi bi-person-plus"></i> Registrarse
						</button>
					</div>
				</form>
				<hr>
				<p class="text-center mb-0">
					¿Ya tiene cuenta? <a href="index.php?controller=auth&action=login">Iniciar Sesión</a>
				</p>
			</div>
		</div>
	</div>
</div>

