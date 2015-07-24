<?php


/*
 getRutaOrSymlink: Obtiene el nombre del directorio o link simbolico de una carpeta
 Parámetros:
	ruta 				Directorio donde se encuentra la carpeta
	dir 				Nombre de la carpeta
 */
function getRutaOrSymlink($ruta, $dir) {
	if (!is_dir($ruta."/".$dir)) {
		// Si está en otra dirección, crear un enlace:
		$link = explode("/", $dir);
		$link = $link[count($link)-2];

		if (!is_link($ruta.$link)) {
			symlink($dir, $ruta.$link);
		}
		return $link.'/';
	} else {
		return $dir;
	}
}

/*
 getPortada: Obtiene la imagen de portada de un video
 Parámetros:
	nombre				Nombre del video
	ruta 				Directorio donde se encuentra el video
 */
function getPortada($nombre, $ruta) {
	//echo $nombre."<br />";
	//echo $ruta."<br />";

	$ffmpeg = "/usr/bin/ffmpeg";
	$video = $ruta."/".$nombre;
	$img = $ruta."/img/".str_replace(".mp4","",$nombre).".jpg";
	$cmd = "$ffmpeg -i ".$video." -ss 3 -vframes 1 -f image2 ".$img;
	//echo "<br />".$cmd."<br /><br />";
	
	if (!shell_exec($cmd)) {
		chmod($img, 0777);
	} else {
	}

	return str_replace($ruta."/img/","",$img);
}


/*
 createDir: Crea un directorio en la ruta indicada, con permisos 777
 Parámetros:
	rutaDir				Ruta + nombre del directorio a crear
 */
 function createDir($rutaDir) {
	mkdir($rutaDir);
	chmod($rutaDir, 0777);
}


/*
 removeDir: Elimina un directorio y todos sus archivos recursivamente
 Parámetros:
	rutaDir				Ruta + nombre del directorio a borrar
 */
function removeDir($rutaDir) { 
	if (is_dir($rutaDir)) { 
		$objects = scandir($rutaDir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (filetype($rutaDir."/".$object) == "dir") {
					removeDir($rutaDir."/".$object);
				} else {
					unlink($rutaDir."/".$object); 
				}
			} 
		} 
		reset($objects); 
		rmdir($rutaDir); 
	} 
} 


/*
 duplicateDir: Duplica un directorio y todos sus archivos recursivamente
 Parámetros:
	rutaORI				Ruta + nombre del directorio a duplicar
	rutaDir				Ruta + nombre del directorio destino
 */
function duplicateDir($rutaORI, $rutaDir) { 
	if ( (is_dir($rutaORI))&&(is_dir($rutaDir)) ) { 
		$objects = scandir($rutaORI); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (filetype($rutaORI."/".$object) == "dir") {
					createDir($rutaDir."/".$object);
					duplicateDir($rutaORI."/".$object, $rutaDir."/".$object);
				} else {
					// Comprobar que el archivo no exista, para no crearla dos veces:
					if (!file_exists($rutaDir."/".$object)) {
						copy($rutaORI."/".$object, $rutaDir."/".$object);
					}
				}
			} 
		} 
		reset($objects); 
	} 
} 



/*
 removeFile: Elimina un fichero
 Parámetros:
	rutaFile			Ruta + nombre del archivo a borrar
 */
function removeFile($rutaFile) { 
	if ( (!is_dir($rutaFile))&&(file_exists($rutaFile)) ) { 
		unlink($rutaFile); 
	} 
} 


/*
 clean: Elimina todos los caracteres no deseados de un string
 Parámetros:
	string				Cadena de texto a limpiar
 */
 function clean($string) {
	$string = strtolower($string);
	
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	
	$string = str_replace('á', 'a', $string); // Replaces á with a
	$string = str_replace('é', 'e', $string); // Replaces é with e
	$string = str_replace('í', 'i', $string); // Replaces í with i
	$string = str_replace('ó', 'o', $string); // Replaces ó with o
	$string = str_replace('ú', 'u', $string); // Replaces ú with u
	$string = str_replace('ü', 'u', $string); // Replaces ü with u
	$string = str_replace('ñ', 'n', $string); // Replaces ñ with n

	return preg_replace('/[^A-Za-z0-9\-\.]/', '', $string); // Removes special chars.
}


/*
 createOrRenameCursoDir: Crea o renombra las carpetas correspondientes para el curso
 Parámetros:
	listaDirs				Lista de ubicaciones existentes
	ubicacion				Ubicacion del curso
	rutaCurso				Carpeta del curso
	rutaCursoORI 			Carpeta del curso original
 */
function createOrRenameCursoDir($listaDirs, $ubicacion, $rutaCurso, $rutaCursoORI, $renombrar, $duplicar) {
	// Obtener la ubicacion del curso:
	if (is_int(array_search($ubicacion, array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($ubicacion, array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}

	// Si existe el curso original, renombrarlo:
	if ( ($renombrar == 1)&&($rutaCursoORI != '') ) {
		if (is_dir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCursoORI)) {
			rename(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCursoORI, _DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso);
		}
	}

	if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso)) {
		createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso);
	}

	if ( ($duplicar == 1)&&($rutaCursoORI != '') ) {
		duplicateDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCursoORI, _DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso);
	}
}

/*
 createOrRenameTemaDir: Crea o renombra las carpetas correspondientes para el tema
 Parámetros:
 	listaDirs				Lista de ubicaciones existentes
	ubicacion				Ubicacion del curso
	rutaCurso				Carpeta del curso
	rutaTema 				Carpeta del tema
	rutaTemaORI 			Carpeta del tema original
 */
function createOrRenameTemaDir($listaDirs, $ubicacion, $rutaCurso, $rutaTema, $rutaTemaORI, $renombrar, $duplicar) {
	// Obtener la ubicacion del curso:
	if (is_int(array_search($ubicacion, array_column($listaDirs, 'ID')))) {
		$dir = $listaDirs[array_search($ubicacion, array_column($listaDirs, 'ID'))]['ruta'];
		$dir = getRutaOrSymlink(_DOCUMENTROOT._DIRCURSOS, $dir);
	}
	
	// Si existe el tema original, renombrarlo:
	if ( ($renombrar == 1)&&($rutaTemaORI != '') ) {
		if (is_dir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTemaORI)) {
			rename(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTemaORI, _DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema);
		}
	}

	// Crear la carpeta del tema, y la de "docs" e "img":
	if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema)) {
		createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema);
	}

	if ($duplicar == 0) {
		// Comprobar si existen las siguientes carpetas; sino crearlas:
		if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema.'/img')) {
			createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema.'/img');
		}
		if (!file_exists(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema.'/docs')) {
			createDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema.'/docs');
		}
	}

	if ( ($duplicar == 1)&&($rutaTemaORI != '') ) {
		duplicateDir(_DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTemaORI, _DOCUMENTROOT._DIRCURSOS.$dir.$rutaCurso.'/'.$rutaTema);
	}
}

?>