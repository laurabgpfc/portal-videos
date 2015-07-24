<h1 class="page-header">Configuración</h1>
<div class="row">
	<div class="col-12">

<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'forms/admin-config.php');

$configData = getConfigData();

$_POST['listaUbicaciones'] = $configData['listaUbicaciones'];
$_POST['listaExtensiones'] = $configData['listaExtensiones'];
$_POST['listaMoodleRoles'] = $configData['listaMoodleRoles'];
$_POST['showErrors'] = $configData['showErrors'];
$_POST['_OCULTO'] = $configData['_OCULTO'];
$_POST['_MOODLEALLUSERS'] = $configData['_MOODLEALLUSERS'];
$_POST['_DIRCURSOS'] = $configData['_DIRCURSOS'];
$_POST['_ADMINDEF'] = $configData['_ADMINDEF'];
$_POST['_ADMINPASS'] = $configData['_ADMINPASS'];
$_POST['_MOODLEURL'] = $configData['_MOODLEURL'];
$_POST['_WSTOKEN'] = $configData['_WSTOKEN'];
$_POST['_ALLOWFILEUPLOAD'] = $configData['_ALLOWFILEUPLOAD'];
$_POST['_ALLOWIMGUPLOAD'] = $configData['_ALLOWIMGUPLOAD'];
$_POST['_ALLOWVIDEOUPLOAD'] = $configData['_ALLOWVIDEOUPLOAD'];
$_POST['_ENCRIPTAR'] = $configData['_ENCRIPTAR'];
$_POST['cronProgramado'] = $configData['cronProgramado'];
$_POST['cronTime'] = $configData['cronTime'];
$_POST['cronRepeat'] = $configData['cronRepeat'];
$_POST['cronConfig'] = $configData['cronConfig'];

$OUT = '';

if ($msgError != '') {
	$OUT .= '<div class="alert alert-'.$error.'">'.$msgError.'</div>';
}

$OUT .= '<form name="config" role="form" method="POST" action="'._PORTALROOT.'modules-admin/config.php">';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="showErrors" type="checkbox"';
		if ($_POST['showErrors'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Mostrar errores de PHP</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="_OCULTO" type="checkbox"';
		if ($_POST['_OCULTO'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Al crear un curso, mostrarlo oculto</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group ubicaciones">';
		$OUT .= '<label for="ubicaciones">Listado de roles v&aacute;lidos de Moodle: </label>';
		$OUT .= '<div class="listado listaMoodleRoles">';
		foreach ($_POST['listaMoodleRoles'] as $rol) {
			$OUT .= '<div class="row">';
				$OUT .= '<div class="col-md-3">';
					$OUT .= '<input type="text" class="txt-as-label pull-right" readonly name="moodleRole['.$rol['ID'].']" value="'.$rol['nombre'].'" />';
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-2">';
					if ($rol['nombre'] != 'student') {
						$OUT .= '<input type="checkbox" name="esAdmin-moodleRole['.$rol['ID'].']" value="'.$rol['ID'].'"';
						if ($rol['esAdmin'] == 1) {
							$OUT .= ' checked';
						}
						$OUT .= ' /> Es administrador';
					}
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-2">';
					$OUT .= '<input '.( ($rol['nombre'] == 'student') ? 'disabled ' : '' ).'type="checkbox" name="importar-moodleRole['.$rol['ID'].']" value="'.$rol['ID'].'"';
					if ($rol['importar'] == 1) {
						$OUT .= ' checked';
					}
					$OUT .= ' /> Importar';
				$OUT .= '</div>';
				
			$OUT .= '</div>';
		}
		$OUT .= '</div>';
	$OUT .= '</div>';


	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_DIRCURSOS">Directorio donde se almacenar&aacute;n y linkar&aacute;n los cursos:</label>';
		$OUT .= '<input type="text" name="_DIRCURSOS" class="form-control" id="_DIRCURSOS" placeholder="Directorio donde se almacenar&aacute;n y linkar&aacute;n los cursos" value="'.$_POST['_DIRCURSOS'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_ADMINDEF">P&aacute;gina por defecto para mostrar en la Administraci&oacute;n:</label>';
		$OUT .= '<input type="text" name="_ADMINDEF" class="form-control" id="_ADMINDEF" placeholder="P&aacute;gina por defecto para mostrar en la Administraci&oacute;n" value="'.$_POST['_ADMINDEF'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_ADMINPASS">Contraseña usuario administrador:</label>';
		$OUT .= '<input type="text" name="_ADMINPASS" class="form-control" id="_ADMINPASS" placeholder="Contraseña usuario administrador" value="'.$_POST['_ADMINPASS'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_MOODLEURL">URL de Moodle:</label>';
		$OUT .= '<input type="text" name="_MOODLEURL" class="form-control" id="_MOODLEURL" placeholder="URL de Moodle" value="'.$_POST['_MOODLEURL'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="_WSTOKEN">Token para servicio web de Moodle:</label>';
		$OUT .= '<input type="text" name="_WSTOKEN" class="form-control" id="_WSTOKEN" placeholder="Token para servicio web de Moodle" value="'.$_POST['_WSTOKEN'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="_ALLOWFILEUPLOAD" type="checkbox"';
		if ($_POST['_ALLOWFILEUPLOAD'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Permitir subir ficheros en Administraci&oacute;n</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="_ALLOWIMGUPLOAD" type="checkbox"';
		if ($_POST['_ALLOWIMGUPLOAD'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Permitir subir im&aacute;genes en Administraci&oacute;n</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="_ALLOWVIDEOUPLOAD" type="checkbox"';
		if ($_POST['_ALLOWVIDEOUPLOAD'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Permitir subir v&iacute;deos en Administraci&oacute;n</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="checkbox">';
		$OUT .= '<input name="_ENCRIPTAR" type="checkbox"';
		if ($_POST['_ENCRIPTAR'] == 1) {
			$OUT .= ' checked';
		}
		$OUT .= '> Encriptar los identificadores</label>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="cronTime">Hora a la que ejecutar el robot:</label> <em>Este valor solo se actualizar&aacute; cuando se pulse el bot&oacute;n "Programar Robot" o "Reprogramar Robot"</em>';
		$OUT .= '<input type="text" name="cronTime" class="form-control" id="cronTime" placeholder="Hora a la que ejecutar el robot" value="'.$_POST['cronTime'].'" />';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group">';
		$OUT .= '<label for="cronRepeat">Ejecutar el robot cada:</label> <em>Este valor solo se actualizar&aacute; cuando se pulse el bot&oacute;n "Programar Robot" o "Reprogramar Robot"</em>';
		$OUT .= '<select class="form-control" name="cronRepeat">';
			$OUT .= '<option value="daily"'.( $_POST['cronRepeat']=='daily' ? ' selected' : '' ).'>Diariamente</option>';
			$OUT .= '<option value="every-minute"'.( $_POST['cronRepeat']=='every-minute' ? ' selected' : '' ).'>Cada X minutos</option>';
		$OUT .= '</select>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group ubicaciones">';
		$OUT .= '<label for="ubicaciones">Listado de ubicaciones: </label>';
		$OUT .= '<div class="listado listaUbicaciones">';
		foreach ($_POST['listaUbicaciones'] as $ub) {
			$OUT .= '<div class="row">';
				$OUT .= '<div class="col-md-2">';
					$OUT .= '<input class="check-delete" type="checkbox" name="del-ubicacion[]" value="'.$ub['ID'].'" /> Eliminar';
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-10">';
					$OUT .= '<input type="text" class="form-control" name="ubicacion['.$ub['ID'].']" id="ubicacion" value="'.$ub['ruta'].'" />';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}
		$OUT .= '</div>';
		$OUT .= '<button type="button" class="add-ub btn btn-success">Añadir una ubicaci&oacute;n</button>';
	$OUT .= '</div>';
	$OUT .= '<div class="form-group ubicaciones">';
		$OUT .= '<label for="ubicaciones">Listado de extensiones v&aacute;lidas: </label>';
		$OUT .= '<div class="listado listaExtensiones">';
		foreach ($_POST['listaExtensiones'] as $ext) {
			$OUT .= '<div class="row">';
				$OUT .= '<div class="col-md-2">';
					$OUT .= '<input class="check-delete" type="checkbox" name="del-extension[]" value="'.$ext['ID'].'" /> Eliminar';
				$OUT .= '</div>';
				$OUT .= '<div class="col-md-10">';
					$OUT .= '<input type="text" class="form-control" name="extension['.$ext['ID'].']" id="extension" value="'.$ext['nombre'].'" />';
				$OUT .= '</div>';
			$OUT .= '</div>';
		}
		$OUT .= '</div>';
		$OUT .= '<button type="button" class="add-ext btn btn-success">Añadir una extensi&oacute;n</button>';
	$OUT .= '</div>';
	$OUT .= '<button type="submit"name="action"  value="save" class="btn btn-default">Guardar</button>';
	$OUT .= '<button type="button" value="cancel" class="btn btn-default btn-cancel">Cancelar</button>';
	$OUT .= '<button type="submit" name="action" value="ejecutar-robot" class="btn btn-primary">Ejecutar Robot</button>';
	if ($_POST['cronProgramado'] == 0) {
		$OUT .= '<button type="submit" name="action" value="programar-robot" class="btn btn-success">Programar Robot</button>';
	} else {
		$OUT .= '<button type="submit" name="action" value="programar-robot" class="btn btn-success">Reprogramar Robot</button>';
		$OUT .= '<button type="submit" name="action" value="desprogramar-robot" class="btn btn-warning">Desprogramar Robot</button>';
	}
	$OUT .= '<input type="hidden" value="config" name="form" />';
	$OUT .= '<input type="hidden" value="'.$_POST['cronConfig'].'" name="cronConfig" />';
	$OUT .= '<input type="hidden" value="'.( $_POST['_ENCRIPTAR']==1 ? 'on' : '' ).'" name="_ENCRIPTARORI" />';
$OUT .= '</form>';

print($OUT);
?>

	</div>
</div>