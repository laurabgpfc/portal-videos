<?php
include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $dbConfig;

$msgError = '';
$error = '';
//foreach ($_POST as $key => $value)
//	print "Field ".$key." is ".$value."<br>";

if ( ($_POST['form'] == 'config')&&($_POST['action'] == 'save') ) {
	$msgError = 'Datos guardados correctamente.';
	$error = 'success';

	if ($_POST['showErrors'] == 'on') {
		updateAdminvar('showErrors', 1);
	} else {
		updateAdminvar('showErrors', 0);
	}

	if ($_POST['_OCULTO'] == 'on') {
		updateAdminvar('_OCULTO', 1);
	} else {
		updateAdminvar('_OCULTO', 0);
	}

	if ($_POST['_MOODLEALLUSERS'] == 'on') {
		updateAdminvar('_MOODLEALLUSERS', 1);
	} else {
		updateAdminvar('_MOODLEALLUSERS', 0);
	}

	if ($_POST['_ALLOWFILEUPLOAD'] == 'on') {
		updateAdminvar('_ALLOWFILEUPLOAD', 1);
	} else {
		updateAdminvar('_ALLOWFILEUPLOAD', 0);
	}

	if ($_POST['_ALLOWIMGUPLOAD'] == 'on') {
		updateAdminvar('_ALLOWIMGUPLOAD', 1);
	} else {
		updateAdminvar('_ALLOWIMGUPLOAD', 0);
	}

	if ($_POST['_ALLOWVIDEOUPLOAD'] == 'on') {
		updateAdminvar('_ALLOWVIDEOUPLOAD', 1);
	} else {
		updateAdminvar('_ALLOWVIDEOUPLOAD', 0);
	}

	if ($_POST['_ENCRIPTAR'] != $_POST['_ENCRIPTARORI']) {
		if ($_POST['_ENCRIPTAR'] == 'on') {
			$encriptar = 1;
		} else {
			$encriptar = 0;
		}

		updateAdminvar('_ENCRIPTAR', $encriptar);

		define('_ENCRIPTAR', getAdminvar('_ENCRIPTAR'));

		encriptarCursos($encriptar);
		encriptarTemas($encriptar);
		encriptarVideos($encriptar);
		encriptarCategorias($encriptar);
	}

	updateAdminvar('_DIRCURSOS', $_POST['_DIRCURSOS']);
	updateAdminvar('_ADMINDEF', $_POST['_ADMINDEF']);
	updateAdminvar('_ADMINPASS', $_POST['_ADMINPASS']);
	updateAdminvar('_MOODLEURL', $_POST['_MOODLEURL']);
	updateAdminvar('_WSTOKEN', $_POST['_WSTOKEN']);
	updateAdminvar('cronTime', $_POST['cronTime']);
	updateAdminvar('cronRepeat', $_POST['cronRepeat']);

	if ($_POST['_MOODLEURL'] == '') {
		$msgError .= ' Ha borrado la URL de Moodle. Ahora no se podr&aacute; acceder a los servicios web.';
		$error = 'warning';
	}

	if ($_POST['_WSTOKEN'] == '') {
		$msgError .= ' Ha borrado la Token de los servicios web de Moodle. Ahora no se podr&aacute; acceder a los servicios web.';
		$error = 'warning';
	}

	if (sizeof($_POST['ubicacion']) > 0) {
		foreach ($_POST['ubicacion'] as $ID => $ruta) {
			if (checkUbicacion('ruta = "'.$ruta.'" AND ID != '.$ID) == 0) {
				updateUbicacion($ID, $ruta);
			} else {
				$msgError .= ' No se ha actualizado la ruta a '.$ruta.' porque ya existe.';
				$error = 'warning';
			}
		}
	}
	
	if (sizeof($_POST['del-ubicacion']) > 0) {
		foreach ($_POST['del-ubicacion'] as $ub) {
			deleteUbicacion($ub);
		}
	}

	if (sizeof($_POST['ubicacion-new']) > 0) {
		foreach ($_POST['ubicacion-new'] as $ub) {
			if ($ub != '') {
				if (substr($ub, -1) != '/') {
					$ub = $ub.'/';
				}
				createUbicacion($ub);
			}
		}
	}

	if (sizeof($_POST['extension']) > 0) {
		foreach ($_POST['extension'] as $ID => $nombre) {
			if (checkExtension('nombre = "'.$nombre.'" AND ID != '.$ID) == 0) {
				updateExtension($ID, $nombre);
			} else {
				$msgError .= ' No se ha actualizado la extensi&oacute;n a '.$nombre.' porque ya existe.';
				$error = 'warning';
			}
		}
	}
	
	if (sizeof($_POST['del-extension']) > 0) {
		foreach ($_POST['del-extension'] as $ext) {
			deleteExtension($ext);
		}
	}

	if (sizeof($_POST['extension-new']) > 0) {
		foreach ($_POST['extension-new'] as $ext) {
			if ($ext != '') {
				createExtension($ext);
			}
		}
	}

	if (sizeof($_POST['moodleRole']) > 0) {
		foreach ($_POST['moodleRole'] as $ID => $nombre) {
			if ($nombre != 'student') {
				updateMoodleRol($ID, sizeof($_POST['esAdmin-moodleRole'][$ID]), sizeof($_POST['importar-moodleRole'][$ID]));
			}
		}

		// Recorrer todos los cursos:
		$configData = getConfigData();
		$listaCursos = getListaCursos();

		foreach ($listaCursos as $curso) {
			// Desregistrar primero todos los usuarios del curso:
			desregistrarUsuariosCurso($curso[0]);

			if ($curso[2] > 0) {
				// Obtener los usuarios inscritos al curso:
				$usuariosEnCurso = connect('core_enrol_get_enrolled_users', array( 'courseid' => $curso[2] ));
				foreach ($usuariosEnCurso as $user) {
					$insertar = 0;
					$esAdmin = 0;

					// Comprobar si el rol se puede importar:
					foreach ($user->roles as $rol) {
						if (checkMoodleRol('nombre = "'.$rol->shortname.'" AND importar = 1')) {
							$insertar = 1;
						}
						// Comprobar si el rol es de admin:
						if (is_int(array_search($rol->shortname, array_column($configData['listaMoodleRoles'], 'nombre')))) {
							$esAdmin = $configData['listaMoodleRoles'][array_search($rol->shortname, array_column($configData['listaMoodleRoles'], 'nombre'))]['esAdmin'];
						}
					}
					if ($insertar == 1) {
						registrarUsuarioCurso($curso[0], $curso[2], $user->fullname, $user->email, $esAdmin);
					}
				}
			}
		}
	}

} else if ( ($_POST['form'] == 'config')&&($_POST['action'] == 'ejecutar-robot') ) {
	$msgError = 'Robot ejecutado';
	$error = 'success';

	include_once(_DOCUMENTROOT.'robot/index.php');

} else if ( ($_POST['form'] == 'config')&&($_POST['action'] == 'programar-robot') ) {
	updateAdminvar('cronProgramado', 1);
	updateAdminvar('cronTime', $_POST['cronTime']);
	updateAdminvar('cronRepeat', $_POST['cronRepeat']);
	
	$cronConfig = '';

	if ($_POST['cronRepeat'] == 'daily') {
		$pos = strrpos($_POST['cronTime'], ':');
		if ($pos === false) { // nota: tres signos de igual
			$msgError = 'Para la programaci&oacute;n diaria, el valor de tiempo debe ser una hora. Ejemplo: 10:00.';
			$error = 'warning';
		} else {
			list($hora, $min) = split(':', $_POST['cronTime']);

			$cronConfig = $min.' '.$hora.' * * * wget http://'.$_SERVER['SERVER_NAME']._PORTALROOT.'robot/index.php';

			$msgError = 'Robot programado.';
			$error = 'success';
		}
		
	} else if ($_POST['cronRepeat'] == 'every-minute') {
		$pos = strrpos($_POST['cronTime'], ':');
		if ($pos === false) { // nota: tres signos de igual
			$cronConfig = '*/'.$_POST['cronTime'].' * * * * wget http://'.$_SERVER['SERVER_NAME']._PORTALROOT.'robot/index.php';
			
			$msgError = 'Robot programado.';
			$error = 'success';
		} else {
			$msgError = 'Para la programaci&oacute;n repetitiva, el valor de tiempo debe ser un n&uacute;mero entero.';
			$error = 'warning';
		}
		
	} else {
		$msgError = 'Esta opci&oacute;n no est&aacute; implementada ('.$_POST['cronRepeat'].')';
		$error = 'danger';
	}
	
	if ($cronConfig != '') {
		$output = shell_exec('crontab -l');
		$cron_file = '/tmp/crontab.txt';
		
		if ($cronConfig != $_POST['cronConfig']) {
			$output = str_replace($_POST['cronConfig'], '', $output);
			file_put_contents($cron_file, $output.PHP_EOL);

			updateAdminvar('cronConfig', $cronConfig);
		}
		
		file_put_contents($cron_file, $output.$cronConfig.PHP_EOL);
		exec("crontab $cron_file");
	}
	

} else if ( ($_POST['form'] == 'config')&&($_POST['action'] == 'desprogramar-robot') ) {
	updateAdminvar('cronProgramado', 0);

	// Quitar del cron el comando:
	$output = shell_exec('crontab -l');
	$cron_file = '/tmp/crontab.txt';
	
	$output = str_replace($_POST['cronConfig'], '', $output);
	
	file_put_contents($cron_file, $output.PHP_EOL);
	exec("crontab $cron_file");
	
	// Actualizar el registro en la tabla:
	updateAdminvar('cronConfig', '');
		
	$msgError = 'Robot desprogramado';
	$error = 'success';
}

?>