<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'util/ws-connection.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$changeCursoMoodle = 0;
$renombrarCurso = 0;
$dirORI = '';
$dir = '';
$msgError = '';
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'cursos') {
	if ( (isset($_POST['archivar-curso']))&&($_POST['archivar-curso'] == 'recuperar') ) {
		$msgError = 'Curso recuperado';
		$error = 'success';

		recuperarCursoArchivado($_POST['IDcurso']);

	} else {
		$rutalimpia = clean($_POST['nombreCurso']);
		
		// Eliminar el curso, todos sus temas y videos, desregistrar usuarios y eliminar todas las carpetas hijas:
		if (isset($_POST['formDel'])) {
			// Crear la carpeta del curso:
			if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
				$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
				$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
			}
			
			if (file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia)) {
				removeDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
			}

			deleteFullCurso($_POST['IDcurso']);

			$msgError = 'Curso eliminado correctamente';
			$error = 'danger';
			
		} else {
			if ($_POST['publico'] == 'on') {
				$_POST['publico'] = 1;
			} else {
				$_POST['publico'] = 0;
			}
			
			if ($_POST['ocultar'] == 'on') {
				$_POST['ocultar'] = 1;
			} else {
				$_POST['ocultar'] = 0;
			}

			// Si se especifica una fecha, comprobar que esten las dos:
			if ( ( ($_POST['fechaIni'] != '')&&($_POST['fechaFin'] == '') )||( ($_POST['fechaIni'] == '')&&($_POST['fechaFin'] != '') ) ) {
				$msgError = 'Si especifica una fecha, debe poner las dos';
				$error = 'warning';

			// Comprobar que la de inicio no es mayor a la de fin:
			} else if ( ($_POST['fechaIni'] != '')&&($_POST['fechaFin'] != '') ) {
				$date1 = new DateTime($_POST['fechaIni']);
				$date2 = new DateTime($_POST['fechaFin']);

				if ($date1 == $date2) {
					$msgError = 'La fecha de inicio y la de fin no pueden ser iguales';
					$error = 'warning';
				} else if ($date1 > $date2) {
					$msgError = 'La fecha de inicio no puede ser superior a la fecha de fin';
					$error = 'warning';
				}
			}

			if ($error == 'success') {
				// Si el curso es nuevo
				if (!$_POST['IDcurso']) {
					// Comprobar que no exista el nombre, ni la ruta:
					if ( ($_POST['nombreCurso'] != '')&&( (checkCurso('nombre = "'.$_POST['nombreCurso'].'"') > 0)||(checkCurso('ruta = "'.$rutalimpia.'"') > 0) ) )  {
						$msgError = 'El curso ya existe';
						$error = 'warning';
					}

					// Comprobar que no este asociado el curso de Moodle a otro curso:
					if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
						$msgError = 'Este curso de Moodle ya está asociado a otro curso';
						$error = 'warning';
					}
					
					if ($error == 'success') {
						$msgError = 'Datos guardados correctamente';

						// Crear la carpeta del curso:
						if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
							$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
							$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
						}
						
						if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia)) {
							createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
						}
						
						// Crear el curso en la base de datos:
						createCurso($_POST['nombreCurso'], $_POST['descripcion'], $rutalimpia, $_POST['ubicacion'], $_POST['orden'], $_POST['ocultar'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);

						$_POST['IDcurso'] = getIDcurso($_POST['nombreCurso'], $rutalimpia, $_POST['ubicacion'], 0);

						if ($_POST['IDcursoMoodle'] != '') {
							wsRegistrarUsuarios($_POST['IDcurso'], $_POST['IDcursoMoodle']);
						}
					}

				// Si se ha editado el curso:
				} else {
					// Comprobar que no exista el nombre, ni la ruta:
					if ( ($_POST['nombreCurso'] != '')&&( (checkCurso('ID != '.decrypt($_POST['IDcurso']).' AND nombre = "'.$_POST['nombreCurso'].'"') > 0)||(checkCurso('ID != '.decrypt($_POST['IDcurso']).' AND ruta = "'.$rutalimpia.'"') > 0) ) ) {
						$msgError = 'El curso ya existe';
						$error = 'warning';
					}

					// Comprobar que no este asociado el curso de Moodle a otro curso:
					if ( ($_POST['IDcursoMoodle'] != '')&&(checkCurso('ID != '.decrypt($_POST['IDcurso']).' AND IDcursoMoodle = '.$_POST['IDcursoMoodle']) > 0) ) {
						$msgError = 'Este curso de Moodle ya está asociado a otro curso';
						$error = 'warning';
					}

					if ($error == 'success') {
						$msgError = 'Datos actualizados correctamente';

						// Si ha cambiado la ubicacion o la ruta, renombrar/mover la carpeta:
						if ( ($rutalimpia != $_POST['rutaCursoORI'])||($_POST['ubicacion'] != $_POST['ubicacionORI']) ) {
							$renombrarCurso = 1;
							
							if (is_int(array_search($_POST['ubicacion'], array_column($listaDirs, 'ID')))) {
								$dir = $listaDirs[array_search($_POST['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
								$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
							}
						}

						if ($renombrarCurso == 1) {
							// Renombrar/mover la carpeta en la ubicacion original:
							if (is_int(array_search($_POST['ubicacionORI'], array_column($listaDirs, 'ID')))) {
								$dirORI = $listaDirs[array_search($_POST['ubicacionORI'], array_column($listaDirs, 'ID'))]['ruta'];
								$dirORI = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dirORI);
							}
							if ($dir == '') {
								$dir = $dirORI;
							}

							if ( ($dirORI != '')&&($dir != '') ) {
								// Si existe el curso original, renombrarlo:
								if (is_dir(_DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'])) {
									rename(_DOCUMENTROOT._DIRCURSOS.$dirORI.$_POST['rutaCursoORI'], _DOCUMENTROOT._DIRCURSOS.$dir.$rutalimpia);
								}
							}
						}

						if ( ($_POST['IDcursoMoodle'] != '')&&($_POST['IDcursoMoodle'] != $_POST['IDcursoMoodleORI']) ) {
							desregistrarUsuariosCurso($_POST['IDcurso']);
							
							wsRegistrarUsuarios($_POST['IDcurso'], $_POST['IDcursoMoodle']);
						}

						// Actualizar el curso en la base de datos:
						updateCurso($_POST['IDcurso'], $_POST['nombreCurso'], $_POST['descripcion'], $rutalimpia, $_POST['ubicacion'], $_POST['orden'], $_POST['ocultar'], $_POST['IDcursoMoodle'], $_POST['fechaIni'], $_POST['fechaFin'], $_POST['publico']);
					}
				}
			}
		}
	}
}

?>