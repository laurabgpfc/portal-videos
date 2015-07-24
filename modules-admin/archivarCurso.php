<?php
include_once(__DIR__.'/../config.php');

if (!isset($_POST['IDcurso'])) {
	$_POST['IDcurso'] = ( ($_GET['IDcurso'] != '') ? $_GET['IDcurso'] : '' );
}

$date1 = new Datetime();
$year = $date1->format('Y');
if ( ($date1->format('n') >= 1)&&($date1->format('n') < 9) ) {
	$txt = 'Año Acad&eacute;mico '.($year-1).'-'.$year;
} else {
	$txt = 'Año Acad&eacute;mico '.$year.'-'.($year+1);
}
?>

<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Archivar curso</h4>
		</div>
		<div class="modal-body">
			<form name="archivarCurso" method="POST" action="<?php echo _PORTALROOT; ?>forms/admin-archivar.php">
				<p>Introduzca el nombre con el que desea archivar el curso.</p>
				<p><strong>NOTA: </strong>Si archiva un curso se eliminar&aacute; el curso de Moodle asociado y la lista de usuarios matriculados. Los registros de visualizaci&oacute;n de v&iacute;deos y descargas se mantendr&aacute;n.</p>
				<div class="form-error btn btn-danger"></div>
				<div class="form-group">
					<label for="anoAcademico" class="control-label">Año acad&eacute;mico:</label>
					<input name="anoAcademico" type="text" placeholder="Año Acad&eacute;mico" class="form-control" value="<?php echo $txt; ?>" />
				</div>
				<div class="modal-footer">
					<button type="submit" name="btn-arch" value="archivar" class="btn btn-success">Archivar</button>
					<button type="submit" name="btn-arch" value="cancel" class="btn btn-cancel btn-danger">Cancelar</button>
				</div>

				<input type="hidden" name="form" value="archivarCurso" />
				<input type="hidden" name="opt" value="<?php echo $_GET['opt']; ?>" />
				<input type="hidden" name="IDcurso" value="<?php echo $_POST['IDcurso']; ?>" />
			</form>
		</div>
	</div>
</div>
