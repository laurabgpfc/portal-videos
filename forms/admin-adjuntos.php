<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

$renombrarAdjunto = 0;
$dir = '';
$msgError = '';
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".($key)." is ".($value)."<br>";

//if ( (!empty($_FILES['rutaAdjunto']))&&($_FILES['rutaAdjunto']['name'] != '')&&($_FILES['rutaAdjunto']['name'] != $_POST['rutaAdjuntoORI']) ) {
//	echo 'Hay un adjunto para subir!!! '.$_FILES['rutaAdjunto']['name'].' de '.($_FILES['rutaAdjunto']['size']/1024).' kb<br />';
//}

if ($_POST['form'] == 'adjuntos') {
	// Obtener la informacion del curso:
	$cursoData = getCursoData($_POST['IDcurso']);

	// Obtener la informacion del tema:
	$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);

	// Obtener la ubicacion del curso:
	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	// Obtener el path completo del adjunto:
	$dirORI = _DOCUMENTROOT._DIRCURSOS.$dir;
	$dir = _DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/docs/';

	// Eliminar el adjunto:
	if (isset($_POST['formDel'])) {
		// Eliminar el archivo:
		if (file_exists($dir.$_POST['rutaAdjunto'])) {
			removeFile($dir.$_POST['rutaAdjunto']);
		}

		// Borrar el registro de la bbdd:
		deleteAdjunto($_POST['IDadjunto']);

		$msgError = 'Adjunto eliminado correctamente';
		$error = 'danger';

	} else {
		if ($_POST['ocultar'] == 'on') {
			$_POST['ocultar'] = 1;
		} else {
			$_POST['ocultar'] = 0;
		}

		if ($error == 'success') {
			// Si el adjunto es nuevo:
			if (!$_POST['IDadjunto']) {
				// Comprobar que no exista el nombre, ni la ruta en el mismo tema y curso:
				if ( ($_POST['nombreAdjunto'] != '')&&( (checkAdjunto('nombre = "'.$_POST['nombreAdjunto'].'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND IDvideo = '.decrypt($_POST['IDvideo'])) > 0)||(checkAdjunto('ruta = "'.$_POST['rutaAdjunto'].'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND IDvideo = '.decrypt($_POST['IDvideo'])) > 0) ) )  {
					$msgError = 'El adjunto ya existe';
					$error = 'warning';
				}

				if (_ALLOWFILEUPLOAD == 1) {
					// Si hay un adjunto nuevo que subir:
					if ( (!empty($_FILES['rutaAdjunto']))&&($_FILES['rutaAdjunto']['name'] != '')&&($_FILES['rutaAdjunto']['name'] != $_POST['rutaAdjuntoORI']) ) {
						$_POST['rutaAdjunto'] = $_FILES['rutaAdjunto']['name'];
						
						// Comprobar que el adjunto no exista, para no crearlo dos veces:
						if (!file_exists($dir.$_POST['rutaAdjunto'])) {
							move_uploaded_file($_FILES['rutaAdjunto']['tmp_name'], $dir.$_POST['rutaAdjunto']);
						}
					}
				} else {
					// Comprobar que el adjunto existe:
					if ( ($_POST['rutaAdjunto'] != '')&&(!file_exists($dir.$_POST['rutaAdjunto'])) ) {
						$msgError = 'El archivo adjunto no existe.';
						$error = 'warning';
					}
				}

				if ($error == 'success') {
					$msgError = 'Datos guardados correctamente';

					// Crear el adjunto en la base de datos:
					createAdjunto($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['nombreAdjunto'], $_POST['descripcion'], $_POST['rutaAdjunto'], $_POST['fechaCaducidad'], $_POST['orden'], $_POST['ocultar']);
					
					$_POST['IDadjunto'] = getIDadjunto($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['nombreAdjunto'], $_POST['rutaAdjunto'], 0);
				}

			// Si se ha editado el adjunto:
			} else {
				if (_ALLOWFILEUPLOAD == 1) {
					// Si hay un adjunto nuevo que subir:
					if ( (!empty($_FILES['rutaAdjunto']))&&($_FILES['rutaAdjunto']['name'] != '')&&($_FILES['rutaAdjunto']['name'] != $_POST['rutaAdjuntoORI']) ) {
						$_POST['rutaAdjunto'] = $_FILES['rutaAdjunto']['name'];
						
						// Comprobar que el adjunto no exista, para no crearlo dos veces:
						if (!file_exists($dir.$_POST['rutaAdjunto'])) {
							move_uploaded_file($_FILES['rutaAdjunto']['tmp_name'], $dir.$_POST['rutaAdjunto']);
						}
					}
				} else {
					// Si se cambia de adjunto:
					if ( ($_POST['rutaAdjunto'] != '')&&($_POST['rutaAdjunto'] != $_POST['rutaAdjuntoORI'])&&($_POST['renombrarAdjunto'] == '') ) {
						// Comprobar que el adjunto destino existe:
						if (!file_exists($dir.$_POST['rutaAdjunto'])) {
							$msgError = 'El adjunto no existe.';
							$error = 'warning';
						}

					// Si se renombra el adjunto:
					} else if ( ($_POST['rutaAdjunto'] != '')&&($_POST['rutaAdjunto'] != $_POST['rutaAdjuntoORI'])&&($_POST['renombrarAdjunto'] != '') ) {
						// Comprobar que el adjunto destino NO existe:
						if (file_exists($dir.$_POST['rutaAdjunto'])) {
							$msgError = 'El adjunto ya existe.';
							$error = 'warning';

						// Comprobar que el adjunto origen existe:
						} else if (!file_exists($dir.$_POST['rutaAdjuntoORI'])) {
							$msgError = 'El adjunto que intenta renombrar no existe.';
							$error = 'warning';

						// Renombrarlo:
						} else {
							rename($dir.$_POST['rutaAdjuntoORI'], $dir.$_POST['rutaAdjunto']);
						}
					}
				}

				if ( ($_POST['rutaAdjunto'] == '')&&($_POST['rutaAdjuntoORI'] != '') ) {
					$_POST['rutaAdjunto'] = $_POST['rutaAdjuntoORI'];
				}
				
				// Comprobar que no exista el nombre, ni la ruta en el mismo tema y curso:
				if ( ($_POST['nombreAdjunto'] != '')&&( (checkAdjunto('ID != '.$_POST['IDadjunto'].' AND nombre = "'.$_POST['nombreAdjunto'].'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND IDvideo = '.decrypt($_POST['IDvideo'])) > 0)||(checkAdjunto('ID != '.$_POST['IDadjunto'].' AND ruta = "'.$_POST['rutaAdjunto'].'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND IDvideo = '.decrypt($_POST['IDvideo'])) > 0) ) ) {
					$msgError = 'El adjunto ya existe';
					$error = 'warning';
				}

				if ($error == 'success') {
					$msgError = 'Datos actualizados correctamente';

					// Actualizar el adjunto en la base de datos:
					updateAdjunto($_POST['IDadjunto'], $_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['nombreAdjunto'], $_POST['descripcion'], $_POST['rutaAdjunto'], $_POST['fechaCaducidad'], $_POST['orden'], $_POST['ocultar']);
				}

				if ($_POST['cambiar-adjunto'] != '') {
					list($IDcursoNew, $IDtemaNew, $IDvideoNew) = split('/-/', $_POST['cambiar-adjunto']);

					$cursoDestinoData = getCursoData($IDcursoNew);

					// Obtener la ubicacion del curso destino:
					if (is_int(array_search($cursoDestinoData['ubicacion'], array_column($listaDirs, 'ID')))) {
						$dirDestino = $listaDirs[array_search($cursoDestinoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
						$dirDestino = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dirDestino);
						$dirDestino = _DOCUMENTROOT._DIRCURSOS.$dirDestino;
					}
					
					$temaDestinoData = getTemaData($IDcursoNew, $IDtemaNew);
					$videoDestinoData = getVideoData($IDcursoNew, $IDtemaNew, $IDvideoNew);
					
					// Si existe el archivo original y no existe en el destino, moverlo:
					if ( (file_exists($dirORI.$cursoData['ruta'].'/'.$temaData['ruta'].'/docs/'.$_POST['rutaAdjunto']))&&(!file_exists($dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/docs/'.$_POST['rutaAdjunto'])) ) {
						rename($dirORI.$cursoData['ruta'].'/'.$temaData['ruta'].'/docs/'.$_POST['rutaAdjunto'], $dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/docs/'.$_POST['rutaAdjunto']);
						
						adjuntoCambiarIDcursoTemaVideo($_POST['IDcurso'], $IDcursoNew, $_POST['IDtema'], $IDtemaNew, $_POST['IDvideo'], $IDvideoNew, $_POST['IDadjunto']);
						
						$_POST['IDcurso'] = $IDcursoNew;
						$_POST['IDtema'] = $IDtemaNew;
						$_POST['IDvideo'] = $IDvideoNew;

					// Si existe el archivo en el destino, pero el tema origen y destino son el mismo, moverlo:
					} else if ( ($cursoData['ruta'].'/'.$temaData['ruta'] == $cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'])&&(file_exists($dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/docs/'.$_POST['rutaAdjunto'])) ) {
						adjuntoCambiarIDcursoTemaVideo($_POST['IDcurso'], $IDcursoNew, $_POST['IDtema'], $IDtemaNew, $_POST['IDvideo'], $IDvideoNew, $_POST['IDadjunto']);
						
						$_POST['IDcurso'] = $IDcursoNew;
						$_POST['IDtema'] = $IDtemaNew;
						$_POST['IDvideo'] = $IDvideoNew;

					// Si existe el archivo en el destino, pero el tema origen y destino son distintos mismo, NO moverlo:
					} else if ( ($cursoData['ruta'].'/'.$temaData['ruta'] != $cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'])&&(file_exists($dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/docs/'.$_POST['rutaAdjunto'])) ) {
						$msgError = 'El adjunto ya existe en el curso, tema y v&iacute;deo destino';
						$error = 'warning';
						
					}
				}
			}
		}
	}
}

?>