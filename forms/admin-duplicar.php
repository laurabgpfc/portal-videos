<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

$renombrarAdjunto = 0;
$dir = '';
$rsp = '';
$error = 'success';


//foreach ($_POST as $key => $value)
//	print "Field ".($key)." is ".($value)."<br>";

if ($_POST['form'] == 'duplicar') {
	if ( ($_POST['btn-dup'] == 'duplicar-solo-reg')||($_POST['btn-dup'] == 'duplicar-todo') ) {
		if ($_POST['opt'] == 'cursos') {
			$cursoData = getCursoData($_POST['IDcurso']);

			$ordenCurso = getNextOrdenCurso();
			$nuevoNombreCurso = $cursoData['nombre'].'-copy';
			
			if (checkCurso('nombre = "'.$nuevoNombreCurso.'"') > 0) {
				$rsp = 'Ya hay una copia de este curso';
				$error = 'warning';
			} else {
				$rutalimpia = clean($nuevoNombreCurso);

				
				// Crear el curso en la base de datos:	
				createCurso($nuevoNombreCurso, $cursoData['descripcion'], $rutalimpia, $cursoData['ubicacion'], $ordenCurso, $cursoData['ocultar'], 0, $cursoData['fechaIni'], $cursoData['fechaFin'], $cursoData['publico']);

				$_POST['IDcurso'] = getIDcurso($nuevoNombreCurso, $rutalimpia, $cursoData['ubicacion'], 0);

				if ($_POST['btn-dup'] == 'duplicar-todo') {
					createOrRenameCursoDir($listaDirs, $cursoData['ubicacion'], $rutalimpia, $cursoData['ruta'], 0, 1);
					
					duplicateTemas($cursoData['IDcurso'], $_POST['IDcurso']);
				//	duplicateVideosByCurso($cursoData['IDcurso'], $_POST['IDcurso']);
				//	duplicateAdjuntosByCurso($cursoData['IDcurso'], $_POST['IDcurso']);
				} else {
					createOrRenameCursoDir($listaDirs, $cursoData['ubicacion'], $rutalimpia, '', 0, 0);
				}
			}

		} else if ($_POST['opt'] == 'temas') {
			$cursoData = getCursoData($_POST['IDcurso']);
			$temaData = getTemaData($_POST['IDcurso'], $_POST['IDtema']);

			$ordenTema = getNextOrdenTema($_POST['IDcurso']);
			$nuevoNombreTema = $temaData['nombre'].'-copy';

			if (checkTema('nombre = "'.$nuevoNombreTema.'" AND IDcurso = '.decrypt($_POST['IDcurso'])) > 0) {
				$rsp = 'Ya hay una copia de este tema';
				$error = 'warning';
			} else {
				$rutalimpia = clean($nuevoNombreTema);
				
				// Crear el tema en la base de datos:
				createTema($_POST['IDcurso'], $nuevoNombreTema, $temaData['descripcion'], $rutalimpia, $ordenTema, $temaData['ocultar']);
				
				$_POST['IDtema'] = getIDtema($_POST['IDcurso'], $nuevoNombreTema, $rutalimpia, 0);

				if ($_POST['btn-dup'] == 'duplicar-todo') {
					createOrRenameTemaDir($listaDirs, $cursoData['ubicacion'], $cursoData['ruta'], $rutalimpia, $temaData['ruta'], 0, 1);
					
					duplicateVideos($temaData['IDtema'], $_POST['IDtema']);
					//duplicateAdjuntosByTema($temaData['IDtema'], $_POST['IDtema']);
				} else {
					createOrRenameTemaDir($listaDirs, $cursoData['ubicacion'], $cursoData['ruta'], $rutalimpia, '', 0, 0);
				}
			}

		} else if ($_POST['opt'] == 'videos') {
			$videoData = getVideoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);

			$ordenVideo = getNextOrdenVideo($_POST['IDcurso'], $_POST['IDtema']);
			$nuevoNombreVideo = $videoData['nombre'].'-copy';

			if (checkVideo('nombre = "'.$nuevoNombreVideo.'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema'])) > 0) {
				$rsp = 'Ya hay una copia de este video';
				$error = 'warning';
			} else {
				// Crear el video en la base de datos, con el mismo archivo de video e imagen:
				createVideo($videoData['IDcurso'], $videoData['IDtema'], $nuevoNombreVideo, $videoData['descripcion'], $videoData['ruta'], $videoData['img'], $videoData['fechaCaducidad'], $ordenVideo, $videoData['ocultar']);

				$_POST['IDvideo'] = getIDvideo($videoData['IDcurso'], $videoData['IDtema'], $nuevoNombreVideo, $videoData['ruta'], 0);

				if ($_POST['btn-dup'] == 'duplicar-todo') {
					duplicateAdjuntos($videoData['IDvideo'], $_POST['IDvideo']);
				}
			}
		
		} else if ($_POST['opt'] == 'adjuntos') {
			$adjuntoData = getAdjuntoData($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['IDadjunto']);

			$ordenAdjunto = getNextOrdenAdjunto($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo']);
			$nuevoNombreAdjunto = $adjuntoData['nombre'].'-copy';

			if (checkAdjunto('nombre = "'.$nuevoNombreAdjunto.'" AND IDcurso = '.decrypt($_POST['IDcurso']).' AND IDtema = '.decrypt($_POST['IDtema']).' AND IDvideo = '.decrypt($_POST['IDvideo'])) > 0) {
				$rsp = 'Ya hay una copia de este adjunto';
				$error = 'warning';
			} else {
				// Crear el adjunto en la base de datos, con el mismo archivo:
				createAdjunto($adjuntoData['IDcurso'], $adjuntoData['IDtema'], $adjuntoData['IDvideo'], $nuevoNombreAdjunto, $adjuntoData['descripcion'], $adjuntoData['ruta'], $adjuntoData['fechaCaducidad'], $ordenAdjunto, $adjuntoData['ocultar']);

				$_POST['IDadjunto'] = getIDadjunto($adjuntoData['IDcurso'], $adjuntoData['IDtema'], $adjuntoData['IDvideo'], $nuevoNombreAdjunto, $adjuntoData['ruta'], 0);
			}

		} else {
			$rsp = 'Duplicando solo el registro de '.$_POST['opt'];
			$error = 'warning';
		}
	} else if ($_POST['btn-dup'] == 'cancel') {
		$error = 'warning';
		$rsp = '';
	} else {
		$rsp = 'No se conoce la opci&oacute;n '.$_POST['btn-dup'];
		$error = 'danger';
	}

	if ($error == 'success') {
		$rsp = '?opt='.$_POST['opt'].'&IDcurso='.$_POST['IDcurso'].'&IDtema='.$_POST['IDtema'].'&IDvideo='.$_POST['IDvideo'].'&IDadjunto='.$_POST['IDadjunto'];
	}
}

echo $rsp;
?>