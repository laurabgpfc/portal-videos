<?php
include_once(__DIR__.'/../config.php');
?>

<div class="modal fade" id="pedirEmail" tabindex="-1" role="dialog" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Necesario correo electr&oacute;nico</h4>
			</div>
			<form name="userSession" method="POST" action="<?php echo _PORTALROOT; ?>forms/login.php">
				<div class="modal-body">
					<p>Para poder acceder a los cursos en los que est&aacute; matriculado, es necesario que facilite el email con el que accede al portal Moodle.</p>
					<div class="form-error btn btn-danger"></div>
					<div class="form-group">
						<label for="email" class="control-label">Correo electr&oacute;nico:</label>
						<input name="email" type="text" placeholder="Correo electr&oacute;nico" class="form-control" />
					</div>
					<div class="modal-footer">
						<button type="submit" name="asociar-correo" class="btn btn-success">Enviar</button>
						<button type="submit" name="logout" class="btn btn-danger">Cerrar sesi&oacute;n</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>