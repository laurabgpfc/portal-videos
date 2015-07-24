<?php

/*
 createAdjunto: Crea un adjunto a un video con los parámetros que se facilitan
 */
function createAdjunto($IDcurso, $IDtema, $IDvideo, $nombre, $descripcion, $ruta, $fechaCaducidad, $orden, $ocultar) {
	global $db;

	$SQL = 'INSERT INTO videosAdjuntos (IDcurso, IDtema, IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar) ';
	$SQL .= 'VALUES ('.decrypt($IDcurso).', '.decrypt($IDtema).', '.decrypt($IDvideo).', "'.$nombre.'", "'.$descripcion.'", "'.$ruta.'", "'.$fechaCaducidad.'", '.$orden.', '.$ocultar.')';
	
	$db->exec($SQL);
}

/*
 updateAdjunto: Actualiza un adjunto existente
 */
function updateAdjunto($IDadjunto, $IDcurso, $IDtema, $IDvideo, $nombre, $descripcion, $ruta, $fechaCaducidad, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE videosAdjuntos SET ';
	$SQL .= 'IDcurso = '.decrypt($IDcurso);
	$SQL .= ', IDtema = '.decrypt($IDtema);
	$SQL .= ', IDvideo = '.decrypt($IDvideo);
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ', descripcion = "'.$descripcion.'"';
	$SQL .= ( ($ruta != '') ? ', ruta = "'.$ruta.'"' : '' );
	$SQL .= ', fechaCaducidad = "'.$fechaCaducidad.'"';
	$SQL .= ', orden = '.$orden;
	$SQL .= ', ocultar = '.$ocultar;
	$SQL .= ' WHERE ID = '.$IDadjunto;
	
	$db->exec($SQL);
}

/*
 updateAdjuntoOrden: Actualiza el orden de un adjunto
 */
function updateAdjuntoOrden($IDadjunto, $orden) {
	global $db;

	$db->exec('UPDATE videosAdjuntos SET orden = '.$orden.' WHERE ID = '.decrypt($IDadjunto));
}

/*
 deleteAdjunto: Elimina un adjunto
 */
function deleteAdjunto($IDadjunto) {
	global $db;

	$db->exec('DELETE FROM videosAdjuntos WHERE ID = '.$IDadjunto);
}

/*
 duplicateAdjuntos: Duplica los registros de adjuntos de un video en otro
 */
function duplicateAdjuntos($IDvideoORI, $IDvideo) {
	global $db;

	$SQL = 'INSERT INTO videosAdjuntos (IDcurso, IDtema, IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar) ';
	$SQL .= 'SELECT IDcurso, IDtema, '.decrypt($IDvideo).' AS IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar FROM videosAdjuntos WHERE IDvideo = '.decrypt($IDvideoORI);
	
	$db->exec($SQL);
}

/*
 duplicateAdjuntosByTema: Duplica los registros de adjuntos de un tema en otro
 */
function duplicateAdjuntosByTema($IDtema, $IDvideoORI, $IDvideo) {
	global $db;

	$SQL = 'INSERT INTO videosAdjuntos (IDcurso, IDtema, IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar) ';
	$SQL .= 'SELECT IDcurso, '.decrypt($IDtema).' AS IDtema, '.decrypt($IDvideo).' AS IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar FROM videosAdjuntos WHERE IDvideo = '.decrypt($IDvideoORI);
	
	$db->exec($SQL);
}

/*
 duplicateAdjuntosByCurso: Duplica los registros de adjuntos de un curso en otro
 */
function duplicateAdjuntosByCurso($IDcurso, $IDtema, $IDvideoORI, $IDvideo) {
	global $db;

	$SQL = 'INSERT INTO videosAdjuntos (IDcurso, IDtema, IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar) ';
	$SQL .= 'SELECT '.decrypt($IDcurso).' AS IDcurso, '.decrypt($IDtema).' AS IDtema, '.decrypt($IDvideo).' AS IDvideo, nombre, descripcion, ruta, fechaCaducidad, orden, ocultar FROM videosAdjuntos WHERE IDvideo = '.decrypt($IDvideoORI);
	
	$db->exec($SQL);
}

/*
 checkAdjunto: Devuelve true si el adjunto existe, y false si no.
 */
function checkAdjunto($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM videosAdjuntos WHERE '.$condicion) > 0);
}

/*
 getIDadjunto: Devuelve el ID del adjunto.
 */
function getIDadjunto($IDcurso, $IDtema, $IDvideo, $nombre, $ruta, $crearAdjunto) {
	global $db;

	if ( ($crearAdjunto == 1)&&(checkAdjunto('nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo)) == 0) ) {
		$orden = getNextOrdenAdjunto($IDcurso, $IDtema, $IDvideo);

		createAdjunto($IDcurso, $IDtema, $IDvideo, $nombre, '', $ruta, '', $orden, _OCULTO);
	}

	return $db->querySingle('SELECT ID FROM videosAdjuntos WHERE nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo));
}

/*
 * getNextOrdenAdjunto: devuelve el siguiente orden en la tabla videosAdjuntos para un curso, tema y video
 */
function getNextOrdenAdjunto($IDcurso, $IDtema, $IDvideo) {
	global $db;

	$orden = $db->querySingle('SELECT MAX(orden)+1 FROM videosAdjuntos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo));
	if (!$orden) {
		$orden = 1;
	}
	return $orden;
}

/*
 * getAllAdjuntos: devuelve un array con todos los adjuntos de un video:
 */
function getAllAdjuntos($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$listaAdjuntos = array();

	$res = $db->query('SELECT * FROM videosAdjuntos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' ORDER BY orden, nombre');
	
	while ($row = $res->fetchArray()) {
		array_push($listaAdjuntos, array(
			'IDcurso' => $IDcurso,
			'IDtema' => $IDtema,
			'IDvideo' => $IDvideo,
			'IDadjunto' => $row['ID'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'fechaCaducidad' => $row['fechaCaducidad'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar']
		));
	}

	return $listaAdjuntos;
}

/*
 * getAdjuntoData: devuelve un array con toda la información de un adjunto:
 */
function getAdjuntoData($IDcurso, $IDtema, $IDvideo, $IDadjunto) {
	global $db;
	
	$adjunto = array();

	$res = $db->query('SELECT * FROM videosAdjuntos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' AND ID = '.$IDadjunto.' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		$adjunto = array(
			'IDcurso' => $IDcurso,
			'IDtema' => $IDtema,
			'IDvideo' => $IDvideo,
			'IDadjunto' => $row['ID'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'fechaCaducidad' => $row['fechaCaducidad'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar']
		);
	}

	return $adjunto;
}

/*
 * getListaAdjuntosByVideoTemaCurso: devuelve un array con todos los adjuntos de un tema, video y un curso:
 */
function getListaAdjuntosByVideoTemaCurso($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$listaAdjuntos = array();

	$res = $db->query('SELECT * FROM videosAdjuntos WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaAdjuntos, array($row['ID'], $row['nombre']));
	}

	return $listaAdjuntos;
}

/*
 * adjuntoCambiarIDcursoTemaVideo: Cambia el ID curso, ID tema e ID video asociado a un adjunto
 */
function adjuntoCambiarIDcursoTemaVideo($IDcurso, $IDcursoNew, $IDtema, $IDtemaNew, $IDvideo, $IDvideoNew, $IDadjunto) {
	global $db;
	
	$db->exec('UPDATE videosAdjuntos SET IDcurso = '.decrypt($IDcursoNew).', IDtema = '.decrypt($IDtemaNew).', IDvideo = '.decrypt($IDvideoNew).' WHERE ID = '.$IDadjunto.' AND IDvideo = '.decrypt($IDvideo).' AND IDtema = '.decrypt($IDtema).' AND IDcurso = '.decrypt($IDcurso));
}

/*
 * adjuntoCambiarIDcursoByTema: Cambia el ID curso asociado a un adjunto
 */
function adjuntoCambiarIDcursoByTema($IDcurso, $IDcursoNew, $IDtema) {
	global $db;
	
	$db->exec('UPDATE videosAdjuntos SET IDcurso = '.decrypt($IDcursoNew).' WHERE IDtema = '.decrypt($IDtema).' AND IDcurso = '.decrypt($IDcurso));
}

/*
 * adjuntoCambiarIDcursoTemaByVideo: Cambia el ID curso e ID tema asociado a un adjunto
 */
function adjuntoCambiarIDcursoTemaByVideo($IDcurso, $IDcursoNew, $IDtema, $IDtemaNew, $IDvideo) {
	global $db;
	
	$db->exec('UPDATE videosAdjuntos SET IDcurso = '.decrypt($IDcursoNew).', IDtema = '.decrypt($IDtemaNew).' WHERE IDvideo = '.decrypt($IDvideo).' AND IDtema = '.decrypt($IDtema).' AND IDcurso = '.decrypt($IDcurso));
}

?>