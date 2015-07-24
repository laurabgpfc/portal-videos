<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

global $db;
global $extensionesValidas;

$renombrarVideo = 0;
$dir = '';
$msgError = '';
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".($key)." is ".($value)."<br>";

if ($_POST['form'] == 'videos') {
	// Obtener la informacion del curso:
	$cursoData = getCursoData($_POST['IDcurso']);

	// Obtener la informacion del tema:
	$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);

	// Obtener la ubicacion del curso:
	if (is_int(array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($cursoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	// Obtener el path completo del video:
	$dirORI = _DOCUMENTROOT._DIRCURSOS.$dir;
	$dir = _DOCUMENTROOT._DIRCURSOS.$dir.$cursoData['ruta'].'/'.$temaData['ruta'].'/';

	// Eliminar el video y la imagen de portada:
	if (isset($_POST['formDel'])) {
		// Eliminar el archivo de video:
		if (file_exists($dir.$_POST['rutaVideo'])) {
			removeFile($dir.$_POST['rutaVideo']);
		}

		// Eliminar la portada:
		if (file_exists($dir.'img/'.$_POST['img'])) {
			removeFile($dir.'img/'.$_POST['img']);
		}

		// Borrar el registro de la bbdd:
		deleteVideo($_POST['IDvideo']);

		$msgError = 'V&iacute;deo eliminado correctamente';
		$error = 'danger';

	} else {

		if ($_POST['ocultar'] == 'on') {
			$_POST['ocultar'] = 1;
		} else {
			$_POST['ocultar'] = 0;
		}

		// Comprobar que la extension es valida:
		if ( (isset($_POST['rutaVideo']))&&($_POST['rutaVideo'] != '') ) {
			$extension = pathinfo($dir.$_POST['rutaVideo'], PATHINFO_EXTENSION);
			if (!is_int(array_search($extension, array_column($extensionesValidas, 'nombre')))) {
				$msgError = 'La extensi&oacute;n del archivo de v&iacute;deo no es v&aacute;lida.';
				$error = 'warning';
			}
		}

		if ( (isset($_POST['obtenerCaptura']))&&( (_ALLOWIMGUPLOAD == 1)&&(!empty($_FILES['img'])) ) ) {
			$msgError = 'Ha marcado la opci&oacute;n de obtener captura y ha subido una imagen nueva. Tiene que quitar una de las dos opciones.';
			$error = 'warning';
		}

		if ($error == 'success') {
			// Si el video es nuevo:
			if (!$_POST['IDvideo']) {
				if (_ALLOWVIDEOUPLOAD == 1) {
					// Si hay un video nuevo que subir:
					if ( (!empty($_FILES['rutaVideo']))&&($_FILES['rutaVideo']['name'] != '')&&($_FILES['rutaVideo']['name'] != $_POST['rutaVideoORI']) ) {
						$_POST['rutaVideo'] = $_FILES['rutaVideo']['name'];
						
						// Comprobar que la imagen no exista, para no crearla dos veces:
						if (!file_exists($dir.$_POST['rutaVideo'])) {
						//	echo "crear el video<br />";
							move_uploaded_file($_FILES['rutaVideo']['tmp_name'], $dir.$_POST['rutaVideo']);
							
						} else {
						//	echo "el video ya existe, solo asociarlo<br />";
						}
						if ($_POST['obtenerCaptura'] == 'on') {
							$_POST['img'] = getPortada($_POST['rutaVideo'], $dir);
						}
					}
				} else {
					// Comprobar que el video existe:
					if ( ($_POST['rutaVideo'] != '')&&(!file_exists($dir.$_POST['rutaVideo'])) ) {
						$msgError = 'El archivo de v&iacute;deo no existe.';
						$error = 'warning';
					}
				}
				
				// Comprobar que no exista el nombre, ni la ruta en el mismo tema y curso:
				if ( ($_POST['nombreVideo'] != '')&&( (checkVideo('nombre = "'.$_POST['nombreVideo'].'" AND IDtema = '.decrypt($_POST['IDtema']).' AND IDcurso = '.decrypt($_POST['IDcurso'])) > 0)||(checkVideo('ruta = "'.$_POST['rutaVideo'].'" AND IDtema = '.decrypt($_POST['IDtema']).' AND IDcurso = '.decrypt($_POST['IDcurso'])) > 0) ) )  {
					$msgError = 'El v&iacute;deo ya existe';
					$error = 'warning';
				}

				if ($error == 'success') {
					$msgError = 'Datos guardados correctamente';

					// Obtener la portada del archivo de video:
					$_POST['img'] = getPortada($_POST['rutaVideo'], $dir);
					
					// Crear el video en la base de datos:
					createVideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion'], $_POST['rutaVideo'], $_POST['img'], $_POST['fechaCaducidad'], $_POST['orden'], $_POST['ocultar']);
					
					$_POST['IDvideo'] = getIDvideo($_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['rutaVideo'], 0);
				}

			// Si se ha editado el video:
			} else {
				if (_ALLOWVIDEOUPLOAD == 1) {
					// Si hay un video nuevo que subir:
					if ( (!empty($_FILES['rutaVideo']))&&($_FILES['rutaVideo']['name'] != '')&&($_FILES['rutaVideo']['name'] != $_POST['rutaVideoORI']) ) {
						$_POST['rutaVideo'] = $_FILES['rutaVideo']['name'];
						
						// Comprobar que la imagen no exista, para no crearla dos veces:
						if (!file_exists($dir.$_POST['rutaVideo'])) {
							move_uploaded_file($_FILES['rutaVideo']['tmp_name'], $dir.$_POST['rutaVideo']);
						}
						if ($_POST['obtenerCaptura'] == 'on') {
							$_POST['img'] = getPortada($_POST['rutaVideo'], $dir);
						}
					}
				} else {
					// Si se cambia de video:
					if ( ($_POST['rutaVideo'] != '')&&($_POST['rutaVideo'] != $_POST['rutaVideoORI'])&&($_POST['renombrarVideo'] == '') ) {
						// Comprobar que el video destino existe:
						if (!file_exists($dir.$_POST['rutaVideo'])) {
							$msgError = 'El archivo de v&iacute;deo no existe.';
							$error = 'warning';
						}

					// Si se renombra el video:
					} else if ( ($_POST['rutaVideo'] != '')&&($_POST['rutaVideo'] != $_POST['rutaVideoORI'])&&($_POST['renombrarVideo'] != '') ) {
						// Comprobar que el video destino NO existe:
						if (file_exists($dir.$_POST['rutaVideo'])) {
							$msgError = 'El archivo de v&iacute;deo ya existe.';
							$error = 'warning';

						// Comprobar que el video origen existe:
						} else if (!file_exists($dir.$_POST['rutaVideoORI'])) {
							$msgError = 'El archivo de v&iacute;deo que intenta renombrar no existe.';
							$error = 'warning';

						// Renombrarlo:
						} else {
							rename($dir.$_POST['rutaVideoORI'], $dir.$_POST['rutaVideo']);
						}
					}
				}
				
				// Comprobar que no exista el nombre, ni la ruta en el mismo tema y curso:
				if ( ($_POST['nombreVideo'] != '')&&( (checkVideo('ID != '.decrypt($_POST['IDvideo']).' AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND nombre = "'.$_POST['nombreVideo'].'"') > 0)||(checkVideo('ID != '.decrypt($_POST['IDvideo']).' AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND ruta = "'.$_POST['rutaVideo'].'"') > 0) ) ) {
					$msgError = 'El v&iacute;deo ya existe';
					$error = 'warning';
				}

				if ( (_ALLOWIMGUPLOAD == 1)&&(!isset($_POST['obtenerCaptura'])) ) {
					// Si hay una imagen nueva que subir:
					if ( (!empty($_FILES['img']))&&($_FILES['img']['name'] != '')&&($_FILES['img']['name'] != $_POST['imgORI']) ) {
						$_POST['img'] = $_FILES['img']['name'];

						// Comprobar que la imagen no exista, para no crearla dos veces:
						if (!file_exists($dir.'img/'.$_POST['img'])) {
							// Check if image file is a actual image or fake image
							if (getimagesize($_FILES['img']['tmp_name']) !== false) {
								move_uploaded_file($_FILES['img']['tmp_name'], $dir.'img/'.$_POST['img']);
							} else {
								$msgError = 'El archivo para la portada no es una imagen.';
								$error = 'warning';
							}
						}
					}

				} else {
					// Si se cambia de imagen:
					if ( ($_POST['img'] != '')&&($_POST['img'] != $_POST['imgORI'])&&($_POST['renombrarImg'] == '') ) {
						// Comprobar que la imagen destino existe:
						if (!file_exists($dir.'img/'.$_POST['img'])) {
							$msgError = 'El archivo de imagen para la portada no existe.';
							$error = 'warning';
						}

					// Si se renombra la imagen:
					} else if ( ($_POST['img'] != '')&&($_POST['img'] != $_POST['imgORI'])&&($_POST['renombrarImg'] != '') ) {
						// Comprobar que la imagen destino NO existe:
						if (file_exists($dir.'img/'.$_POST['img'])) {
							$msgError = 'El archivo de imagen para la portada ya existe.';
							$error = 'warning';

						// Comprobar que la imagen origen existe:
						} else if (!file_exists($dir.'img/'.$_POST['imgORI'])) {
							$msgError = 'El archivo de imagen que intenta renombrar no existe.';
							$error = 'warning';

						// Renombrarlo:
						} else {
							rename($dir.'img/'.$_POST['imgORI'], $dir.'img/'.$_POST['img']);
						}
					}
				}

				if ($error == 'success') {
					$msgError = 'Datos actualizados correctamente';

					if ($_POST['rutaVideo'] == '') {
						$_POST['rutaVideo'] = $_POST['rutaVideoORI'];
					}

					if ($_POST['img'] == '') {
						$_POST['img'] = $_POST['imgORI'];
					}

					// Actualizar el video en la base de datos:
					updateVideo($_POST['IDvideo'], $_POST['IDcurso'], $_POST['IDtema'], $_POST['nombreVideo'], $_POST['descripcion'], $_POST['rutaVideo'], $_POST['img'], $_POST['fechaCaducidad'], $_POST['orden'], $_POST['ocultar']);
				}
			}
			
			$videoData = getVideoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);

			if ( ($_POST['categorias'] != '')||(count($videoData['categorias']) > 0) ) {
				if ($_POST['categorias'] != '') {
					// Añadir las categorias nuevas:
					foreach ($_POST['categorias'] as $categoria) {
						$IDcategoria = getIDcategoria($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $categoria, 1);
					}
				}
				
				$borrarTodo = 0;
				if ( (count($videoData['categorias']) > 0)&&(count($_POST['categorias']) == 0) ) {
					$borrarTodo = 1;
				}
				
				// Recorrer las categorias originales, borrar las que no esten:
				foreach ($videoData['categorias'] as $categoriaDel) {
					if ($borrarTodo == 0) {
						if (!is_int(array_search($categoriaDel['nombre'], $_POST['categorias']))) {
							deleteCategoria($categoriaDel['IDcategoria']);
						}
					} else {
						deleteCategoria($categoriaDel['IDcategoria']);
					}
				}
			}

			if ($_POST['cambiar-video'] != '') {
				list($IDcursoNew, $IDtemaNew) = split('/-/', $_POST['cambiar-video']);

				$cursoDestinoData = getCursoData($IDcursoNew);

				// Obtener la ubicacion del curso:
				if (is_int(array_search($cursoDestinoData['ubicacion'], array_column($listaDirs, 'ID')))) {
					$dirDestino = $listaDirs[array_search($cursoDestinoData['ubicacion'], array_column($listaDirs, 'ID'))]['ruta'];
					$dirDestino = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dirDestino);
					$dirDestino = _DOCUMENTROOT._DIRCURSOS.$dirDestino;
				}
				
				$temaDestinoData = getTemaData($IDcursoNew, $IDtemaNew);
				
				// Si existe el archivo original y no existe en el destino, moverlo junto con su imagen y sus adjuntos:
				if ( (file_exists($dirORI.$cursoData['ruta'].'/'.$temaData['ruta'].'/'.$_POST['rutaVideo']))&&(!file_exists($dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/'.$_POST['rutaVideo'])) ) {
					// Mover el archivo de video:
					rename($dirORI.$cursoData['ruta'].'/'.$temaData['ruta'].'/'.$_POST['rutaVideo'], $dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/'.$_POST['rutaVideo']);
					// Mover la imagen:
					rename($dirORI.$cursoData['ruta'].'/'.$temaData['ruta'].'/img/'.$_POST['img'], $dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/img/'.$_POST['img']);

					// Recorrer los adjuntos y moverlos todos:
					foreach ($videoData['adjuntos'] as $adjunto) {
						// Mover la imagen:
						rename($dirORI.$cursoData['ruta'].'/'.$temaData['ruta'].'/docs/'.$adjunto['ruta'], $dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/docs/'.$adjunto['ruta']);
					}

					videoCambiarIDcursoTema($_POST['IDcurso'], $IDcursoNew, $_POST['IDtema'], $IDtemaNew, $_POST['IDvideo']);
					adjuntoCambiarIDcursoTemaByVideo($_POST['IDcurso'], $IDcursoNew, $_POST['IDtema'], $IDtemaNew, $_POST['IDvideo']);
					
					$_POST['IDcurso'] = $IDcursoNew;
					$_POST['IDtema'] = $IDtemaNew;

				} else if (file_exists($dirDestino.$cursoDestinoData['ruta'].'/'.$temaDestinoData['ruta'].'/'.$_POST['rutaVideo'])) {
					$msgError = 'El v&iacute;deo ya existe en el curso y tema destino';
					$error = 'warning';
				}
			}
		}
	}
}

?>