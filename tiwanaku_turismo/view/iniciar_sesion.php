<div class="row justify-content-center">
	<div class="col-md-5">
		<div class="card">
			<div class="card-header bg-primary text-white text-center">
				<h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</h4>
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

				<form method="POST" action="index.php?controller=auth&action=doLogin">
					<div class="mb-3">
						<label class="form-label">Usuario</label>
						<input type="text" class="form-control" name="username" required autofocus>
					</div>
					<div class="mb-3">
						<label class="form-label">Contraseña</label>
						<input type="password" class="form-control" name="password" required>
					</div>
					<div class="d-grid">
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
						</button>
					</div>
				</form>
				<hr>
				<p class="text-center mb-0">
					¿No tiene cuenta? <a href="index.php?controller=auth&action=register">Registrarse</a>
				</p>
				<p class="text-center small text-muted mt-2">
					Usuario de prueba: <strong>admin</strong> / Contraseña: <strong>admin123</strong>
				</p>
			</div>
		</div>
	</div>
</div>

