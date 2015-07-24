<?php
include_once(__DIR__.'/../config.php');


if (!isset($_POST['IDcurso'])) {
	$_POST['IDcurso'] = ( ($_GET['IDcurso'] != '') ? $_GET['IDcurso'] : '' );
}
if (!isset($_POST['IDtema'])) {
	$_POST['IDtema'] = ( ($_GET['IDtema'] != '') ? $_GET['IDtema'] : '' );
}
if (!isset($_POST['IDvideo'])) {
	$_POST['IDvideo'] = ( ($_GET['IDvideo'] != '') ? $_GET['IDvideo'] : '' );
}
if (!isset($_POST['IDadjunto'])) {
	$_POST['IDadjunto'] = ( ($_GET['IDadjunto'] != '') ? $_GET['IDadjunto'] : '' );
}

?>


<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Duplicar contenido</h4>
		</div>
		<div class="modal-body">
			<form name="duplicarContenido" method="POST" action="<?php echo _PORTALROOT; ?>forms/admin-duplicar.php">
				<p>Seleccione c&oacute;mo desea duplicar el contenido seleccionado.</p>
				<div class="form-error btn btn-danger"></div>
				<div class="modal-footer">
					<?php if ($_POST['IDadjunto'] == '') { ?>
					<button type="submit" name="btn-dup" value="duplicar-todo" class="btn btn-success">Duplicar con todo el contenido</button>
					<?php } ?>
					<button type="submit" name="btn-dup" value="duplicar-solo-reg" class="btn btn-warning">Duplicar solo registro</button>
					<button type="submit" name="btn-dup" value="cancel" class="btn btn-cancel btn-danger">Cancelar</button>
				</div>

				<input type="hidden" name="form" value="duplicar" />
				<input type="hidden" name="opt" value="<?php echo $_GET['opt']; ?>" />
				<input type="hidden" name="IDcurso" value="<?php echo $_POST['IDcurso']; ?>" />
				<input type="hidden" name="IDtema" value="<?php echo $_POST['IDtema']; ?>" />
				<input type="hidden" name="IDvideo" value="<?php echo $_POST['IDvideo']; ?>" />
				<input type="hidden" name="IDadjunto" value="<?php echo $_POST['IDadjunto']; ?>" />
			</form>
		</div>
	</div>
</div>
