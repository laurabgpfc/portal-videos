<?php

include_once(_DOCUMENTROOT.'util/ws-connection.php');

/*********************************************************************
 buscarCursos: Rastrea una ruta en busca de cursos
 Parámetros:
	dir				Ruta a rastrear
 *********************************************************************/
function buscarCursos($IDdir, $dir, &$listaCursosExistentes) {
	logAction("Buscando cursos en ".$dir);
	
	$IDcurso = 0;
	$cont = 0;
	//$ubicacion = str_replace(_DOCUMENTROOT._DIRCURSOS, '', $dir);
	
	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				if (is_dir($dir."/".$filename)) {
					$cont++;

					// Limpiar el nombre de la carpeta de caracteres extraños y espacios
					$filenameNEW = clean($filename);
					rename($dir."/".$filename, $dir."/".$filenameNEW);
					
					// Guardar el curso en la BBDD:
					logAction("Encontrado curso ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
					$IDcurso = getIDcurso($filename, $filenameNEW, $IDdir, 1);
					
					if ($IDcurso != '') {
						array_push($listaCursosExistentes, $IDcurso);
						
						// Si el curso ya existia, y tiene curso de Moodle asociado, recargar usuarios:
						if (checkCurso('ID = '.decrypt($IDcurso).' AND IDcursoMoodle != 0')) {
							$cursoData = getCursoData($IDcurso);

							desregistrarUsuariosCurso($IDcurso);
							wsRegistrarUsuarios($IDcurso, $cursoData['IDcursoMoodle']);
						}

						// Buscar temas dentro del curso:
						buscarTemas($IDcurso, $dir.$filenameNEW);
					} else {
						logAction($dir."/".$filename." no se encuentra en la base de datos");
					}
				} else {
					logAction($dir."/".$filename." no se procesará, ya que no es un directorio");
				}
			}
		}

		if ($cont == 0) {
			logAction($dir." no contiene cursos");
		}
		
	} else {
		logAction("Error al leer ".$dir);
	}
}



/*********************************************************************
 buscarTemas: Rastrea la ruta de un curso en busca de temas
 Parámetros:
	IDcurso				ID del curso
	dir					Ruta del tema
 *********************************************************************/
function buscarTemas($IDcurso, $dir) {
	logAction("Buscando tema en ".$dir);

	$IDtema = 0;
	$cont = 0;
	//$ubicacion = str_replace(_DOCUMENTROOT._DIRCURSOS, '', $dir);

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				// Si se trata de una carpeta:
				if (is_dir($dir."/".$filename)) {
					$cont++;

					// Limpiar el nombre de la carpeta de caracteres extraños y espacios
					$filenameNEW = clean($filename);
					rename($dir."/".$filename, $dir."/".$filenameNEW);
					
					// Comprobar si existen las siguientes carpetas; sino crearlas:
					if (!file_exists($dir."/".$filenameNEW."/img")) {
						createDir($dir."/".$filenameNEW."/img");
						logAction("Crear carpeta tema ".$dir."/".$filenameNEW."/img");
					}
					if (!file_exists($dir."/".$filenameNEW."/docs")) {
						createDir($dir."/".$filenameNEW."/docs");
						logAction("Crear carpeta tema ".$dir."/".$filenameNEW."/docs");
					}

					// Guardar el tema
					logAction("Encontrado tema ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
					$IDtema = getIDtema($IDcurso, $filename, $filenameNEW, 1);
					
					if ($IDtema != '') {
						buscarVideos($IDcurso, $IDtema, $dir."/".$filenameNEW);
					} else {
						logAction($dir."/".$filename." no se encuentra en la base de datos");
					}
				} else {
					logAction($dir."/".$filename." no se procesará, ya que no es un directorio");
				//	echo "&nbsp;&nbsp;&nbsp;Los ficheros dentro de un curso no se procesarán <br />";
				}
			}
		}

		if ($cont == 0) {
			logAction($dir." no contiene temas");
		//	echo "&nbsp;&nbsp;&nbsp;No existen temas para ".$dir."<br />";
		}
	} else {
		logAction("Error al leer ".$dir);
	//	echo "&nbsp;&nbsp;&nbsp;Error al leer de ".$dir."<br />";
	}
}



/*********************************************************************
 buscarVideos: Rastrea la ruta de un tema en busca de vídeos
 Parámetros:
	IDcurso				ID del curso
	IDtema				ID del tema
	dir					Ruta del vídeo
 *********************************************************************/
function buscarVideos($IDcurso, $IDtema, $dir) {
	global $extensionesValidas;

	$cont = 0;
	//$ubicacion = str_replace(_DOCUMENTROOT._DIRCURSOS, '', $dir);

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				// Si existe la carpeta de INBOX, leer su contenido:
				if (is_dir($dir."/".$filename)) {
					logAction($dir."/".$filename." no se procesará, ya que no es un archivo");
				} else {
					// Comprobar si el archivo tiene una extensión válida:
					$extension = pathinfo($dir."/".$filename, PATHINFO_EXTENSION);
					//if (in_array($extension, $extensionesValidas)) {
					if (is_int(array_search($extension, array_column($extensionesValidas, 'nombre')))) {
						$cont++;
						
						// Limpiar el nombre de la carpeta de caracteres extraños y espacios
						$filenameNEW = clean($filename);
						rename($dir."/".$filename, $dir."/".$filenameNEW);

						// Guardar el vídeo:
						logAction("Encontrado vídeo ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
						$IDvideo = getIDvideo($IDcurso, $IDtema, $filename, $filenameNEW, 1);

						// Buscar adjuntos cuyo nombre comience por el nombre del video:
						if (is_dir($dir."/docs")) {
							buscarAdjuntosByName($IDcurso, $IDtema, $IDvideo, str_replace(".".$extension, "", $filenameNEW), $dir."/docs");
						}

						$img = getPortada($filenameNEW, $dir);
						logAction("Obtenida imagen ".$img." del video ".$filenameNEW);
						
						if ($img != '') {
							updateVideoIMG($IDvideo, $img);
						}
					} else {
						logAction($dir."/".$filename." no tiene una extensión válida");
					}
				}
			}
		}
		
		if ($cont == 0) {
			logAction($dir." no contiene vídeos");
		}
	} else {
		logAction("Error al leer ".$dir);
	}
}



/*********************************************************************
 buscarAdjuntosByName: Rastrea la ruta docs de un tema en busca de adjuntos
 Parámetros:
	IDcurso				ID del curso
	IDtema				ID del tema
	IDvideo				ID del video
	nombreVideo			Nombre del video, sin extension
	dir					Ruta del adjunto
 *********************************************************************/
function buscarAdjuntosByName($IDcurso, $IDtema, $IDvideo, $nombreVideo, $dir) {
	$cont = 0;
	//$ubicacion = str_replace(_DOCUMENTROOT._DIRCURSOS, '', $dir);

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				// Si existe la carpeta de INBOX, leer su contenido:
				if (is_dir($dir."/".$filename)) {
					logAction($dir."/".$filename." no se procesará, ya que no es un archivo");
				} else {
					// Limpiar el nombre de la carpeta de caracteres extraños y espacios
					$filenameNEW = clean($filename);
					rename($dir."/".$filename, $dir."/".$filenameNEW);
					
					// Si el nombre del adjunto comienza por el nombre del video:
					if ( is_int(strpos($filenameNEW, $nombreVideo))&&(strpos($filenameNEW, $nombreVideo) == 0) ) {
						// Guardar el adjunto:
						logAction("Encontrado adjunto ".$dir."/".$filename.". Renombrado a ".$filenameNEW);
						$IDadjunto = getIDadjunto($IDcurso, $IDtema, $IDvideo, $filename, $filenameNEW, 1);
					} else {
						logAction($dir."/".$filename." no hay ningun video al que asociar el adjunto");
					}
				}
			}
		}
		
		if ($cont == 0) {
			logAction($dir." no contiene adjuntos");
		}
	} else {
		logAction("Error al leer ".$dir);
	}
}




/*********************************************************************
		 						ROBOT.php
 *********************************************************************/

$db = null;
$dbLog = null;

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

dbCreate(_BBDD);
dbLogCreate(_BBDDLOG);

if ( (isset($_GET['rehacer']))&&($_GET['rehacer'] == 1) ) {
	resetDB();
	resetDBLog();
}

$listaCursosExistentes = array();

logAction("Inicio Robot");

foreach ($listaDirs as $dir) {
	logAction("Analizando ".$dir['ruta']);
	// Comprobar si se encuentra en la misma ruta que los cursos:
	if (!is_dir(_DOCUMENTROOT._DIRCURSOS."/".$dir['ruta'])) {
		logAction($dir['ruta']." no se encuentra en la misma ruta que el portal.");

		// Si está en otra dirección, crear un enlace:
		$link = explode("/", $dir['ruta']);
		$link = $link[count($link)-2];

		if (!is_link(_DOCUMENTROOT._DIRCURSOS.$link)) {
			logAction("Creando link de ".$dir['ruta']." en "._DOCUMENTROOT._DIRCURSOS.$link);
		//	echo $dir." - ".$link."<br />";
			symlink($dir['ruta'], _DOCUMENTROOT._DIRCURSOS.$link);
		}

		buscarCursos($dir['ID'], _DOCUMENTROOT._DIRCURSOS.$link."/", $listaCursosExistentes);
	} else {
		buscarCursos($dir['ID'], _DOCUMENTROOT._DIRCURSOS.$dir['ruta'], $listaCursosExistentes);
	}
}

logAction("Eliminando los cursos que ya no existen...");

$listaCursosORI = getListaCursos();

foreach ($listaCursosORI as $curso) {
	if (!is_int(array_search(decrypt($curso[0]), $listaCursosExistentes))) {
		deleteFullCurso($curso[0]);
		logAction('Eliminar curso '.$curso[1].'...');
	}
}

logAction("Fin Robot");

?>