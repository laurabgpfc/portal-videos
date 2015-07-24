<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;

$renombrarTema = 0;
$dirORI = '';
$dir = '';
$msgError = '';
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";

if ($_POST['form'] == 'temas') {
	// Obtener la informacion del curso:
	$cursoData = getCursoData($_POST['IDcurso']);
	
	// Obtener la ubicacion del curso:
	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	// Obtener la ruta limpia del tema:
	$rutalimpia = clean($_POST['nombreTema']);
	
	// Eliminar el tema y todos sus videos y eliminar todas las carpetas hijas:
	if (isset($_POST['formDel'])) {
		// Elimiar la carpeta del tema y todos sus hijos:
		if (file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia)) {
			removeDir(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia);
		}

		deleteFullTema($_POST['IDtema']);

		$msgError = 'Tema eliminado correctamente';
		$error = 'danger';
	} else {
		if ($_POST['ocultar'] == 'on') {
			$_POST['ocultar'] = 1;
		} else {
			$_POST['ocultar'] = 0;
		}

		if ($error == 'success') {
			// Si el curso es nuevo
			if (!$_POST['IDtema']) {
				// Comprobar que no exista el nombre, ni la ruta:
				if ( ($_POST['nombreTema'] != '')&&( (checkTema('nombre = "'.$_POST['nombreTema'].'" AND IDcurso = '.decrypt($_POST['IDcurso'])) > 0)||(checkTema('ruta = "'.$rutalimpia.'" AND IDcurso = '.decrypt($_POST['IDcurso'])) > 0) ) )  {
					$msgError = 'El tema ya existe';
					$error = 'warning';
				}

				if ($error == 'success') {
					$msgError = 'Datos guardados correctamente';

					// Obtener la ubicacion del curso:
					if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
						$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
						$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
					}
					
					// Crear la carpeta del tema, y la de "docs" e "img":
					if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia)) {
						createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia);

						// Comprobar si existen las siguientes carpetas; sino crearlas:
						if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia.'/img')) {
							createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia.'/img');
						}
						if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia.'/docs')) {
							createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia.'/docs');
						}
					}
					
					// Crear el tema en la base de datos:
					createTema($_POST['IDcurso'], $_POST['nombreTema'], $_POST['descripcion'], $rutalimpia, $_POST['orden'], $_POST['ocultar']);
					
					$_POST['IDtema'] = getIDtema($_POST['IDcurso'], $_POST['nombreTema'], $rutalimpia, 0);
				}

			// Si se ha editado el tema:
			} else {
				// Comprobar que no exista el nombre, ni la ruta:
				if ( ($_POST['nombreTema'] != '')&&( (checkTema('ID != '.decrypt($_POST['IDtema']).' AND IDcurso = '.decrypt($_POST['IDcurso']).' AND nombre = "'.$_POST['nombreTema'].'"') > 0)||(checkTema('ID != '.decrypt($_POST['IDtema']).' AND IDcurso = '.decrypt($_POST['IDcurso']).' AND ruta = "'.$rutalimpia.'"') > 0) ) ) {
					$msgError = 'El tema ya existe';
					$error = 'warning';
				}

				if ($error == 'success') {
					$msgError = 'Datos actualizados correctamente';

					// Si ha cambiado la ruta, renombrar/mover la carpeta:
					if ($rutalimpia != $_POST['rutaTemaORI']) {
						$renombrarTema = 1;
						
						// Obtener la ubicacion del curso:
						if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
							$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
							$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
						}
					}

					if ($renombrarTema == 1) {
						// Si existe el tema original, renombrarlo:
						if (is_dir(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$_POST['rutaTemaORI'])) {
							rename(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$_POST['rutaTemaORI'], _DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia);
						}
					}

					// Actualizar el tema en la base de datos:
					updateTema($_POST['IDtema'], $_POST['IDcurso'], $_POST['nombreTema'], $_POST['descripcion'], $rutalimpia, $_POST['orden'], $_POST['ocultar']);
				}

				if ($_POST['cambiar-tema'] != '') {
					$cursoDestinoData = getCursoData($_POST['cambiar-tema']);

					// Obtener la ubicacion del curso:
					if (is_int(array_search($cursoDestinoData['ubicacion'], array_column($listaDirs, 'ID')))) {
						$dirDestino = $listaDirs[array_search($cursoDestinoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
						$dirDestino = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dirDestino);
						$dirDestino = _DOCUMENTROOT._DIRCURSOS.$dirDestino;
					}
					
					// Si existe el tema original, renombrarlo:
					if ( (is_dir(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia))&&(!is_dir($dirDestino.$cursoDestinoData['ruta'].'/'.$rutalimpia)) ) {
						rename(_DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$rutalimpia, $dirDestino.$cursoDestinoData['ruta'].'/'.$rutalimpia);

						temaCambiarIDcurso($_POST['IDcurso'], $_POST['cambiar-tema'], $_POST['IDtema']);
						videoCambiarIDcursoByTema($_POST['IDcurso'], $_POST['cambiar-tema'], $_POST['IDtema']);
						adjuntoCambiarIDcursoByTema($_POST['IDcurso'], $_POST['cambiar-tema'], $_POST['IDtema']);

						$_POST['IDcurso'] = $_POST['cambiar-tema'];

					} else if (is_dir($dirDestino.$cursoDestinoData['ruta'].'/'.$rutalimpia)) {
						$msgError = 'El tema ya existe en el curso destino';
						$error = 'warning';
					}
				}
			}
		}
	}
}

?>