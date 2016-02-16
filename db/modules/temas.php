<?php


/*
 createTema: Crea un tema con los parámetros que se facilitan
 */
function createTema($IDcurso, $nombre, $descripcion, $ruta, $orden, $ocultar) {
	global $db;

	$SQL = 'INSERT INTO temas (IDcurso, nombre, descripcion, ruta, orden, ocultar) ';
	$SQL .= 'VALUES ('.decrypt($IDcurso).', "'.$nombre.'", "'.$descripcion.'", "'.$ruta.'", '.$orden.', '.$ocultar.')';
	
	//print $SQL;
	$db->exec($SQL);

	// Una vez creado el tema, obtener su ID y encriptarlo:
	$IDtema = $db->querySingle('SELECT ID FROM temas WHERE ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso));

	// Actualizar el registro:
	$db->exec('UPDATE temas SET IDencriptado = "'.encrypt($IDtema).'" WHERE ID = '.$IDtema);
}


/*
 updateTema: Actualiza un tema existente
 */
function updateTema($IDtema, $IDcurso, $nombre, $descripcion, $ruta, $orden, $ocultar) {
	global $db;

	$SQL = 'UPDATE temas SET ';
	$SQL .= 'IDcurso = '.decrypt($IDcurso);
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ', descripcion = "'.$descripcion.'"';
	$SQL .= ', ruta = "'.$ruta.'"';
	$SQL .= ', orden = '.$orden;
	$SQL .= ', ocultar = '.$ocultar;
	$SQL .= ' WHERE ID = '.decrypt($IDtema);
	
	$db->exec($SQL);
}

/*
 updateTemaOrden: Actualiza el orden de un tema
 */
function updateTemaOrden($IDtema, $orden) {
	global $db;

	$db->exec('UPDATE temas SET orden = '.$orden.' WHERE ID = '.decrypt($IDtema));
}
/*
 deleteFullTema: Elimina un tema completo, con sus videos
 */
function deleteFullTema($IDtema) {
	global $db;

	$db->exec('DELETE FROM videosAdjuntos WHERE IDtema = '.decrypt($IDtema));
	$db->exec('DELETE FROM videos WHERE IDtema = '.decrypt($IDtema));
	$db->exec('DELETE FROM temas WHERE ID = '.decrypt($IDtema));
}

/*
 duplicateTemas: Duplica los registros de temas de un curso en otro
 */
function duplicateTemas($IDcursoORI, $IDcurso) {
	global $db;

	$res = $db->query('SELECT ID, '.decrypt($IDcurso).' AS IDcurso, nombre, descripcion, ruta, orden, ocultar FROM temas WHERE IDcurso = '.decrypt($IDcursoORI));
	while ($row = $res->fetchArray()) {
		createTema($row['IDcurso'], $row['nombre'], $row['descripcion'], $row['ruta'], $row['orden'], $row['ocultar']);

		$IDnewTema = getIDtema($row['IDcurso'], $row['nombre'], $row['ruta'], 0);
		
		duplicateVideosByCurso($row['IDcurso'], $row['ID'], $IDnewTema);
	}
}

/*
 checkTema: Devuelve true si el tema existe, y false si no.
 */
function checkTema($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM temas WHERE '.$condicion) > 0);
}

/*
 getIDtema: Devuelve ID encriptado tema por ruta e IDcurso.
 */
function getIDtema($IDcurso, $nombre, $ruta, $crearTema) {
	global $db;

	if ( ($crearTema == 1)&&(checkTema('ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso)) == 0) ) {
		$orden = getNextOrdenTema($IDcurso);

		createTema($IDcurso, $nombre, '', $ruta, $orden, _OCULTO);
	}

	return $db->querySingle('SELECT IDencriptado FROM temas WHERE ruta = "'.$ruta.'" AND IDcurso = '.decrypt($IDcurso));
}

/*
 * getNextOrdenTema: devuelve el siguiente orden en la tabla temas para un curso
 */
function getNextOrdenTema($IDcurso) {
	global $db;

	$orden = $db->querySingle('SELECT MAX(orden)+1 FROM temas WHERE IDcurso = '.decrypt($IDcurso));
	if (!$orden) {
		$orden = 1;
	}
	return $orden;
}

/*
 * getTemaData: devuelve un array con toda la información de un tema:
 */
function getTemaData($IDcurso, $IDtema) {
	global $db;
	
	$tema = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.decrypt($IDcurso).' AND ID = '.decrypt($IDtema).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		$tema = array(
			'IDcurso' => $IDcurso,
			'IDtema' => $row['IDencriptado'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar']
		);
	}

	return $tema;
}

/*
 * getListaTemasByCurso: devuelve un array con todos los temas de un curso:
 */
function getListaTemasByCurso($IDcurso) {
	global $db;
	
	$listaTemas = array();

	$res = $db->query('SELECT * FROM temas WHERE IDcurso = '.decrypt($IDcurso).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaTemas, array($row['IDencriptado'], $row['nombre']));
	}

	return $listaTemas;
}

/*
 * getListaTemasDisponibles: Devuelve la lista de cursos y temas disponibles, salvo el dado
 */
function getListaTemasDisponibles($IDtema) {
	global $db;
	
	$listaUbicaciones = array();

	$res = $db->query('SELECT a.IDencriptado AS IDcurso, a.nombre AS curso, b.IDencriptado AS IDtema, b.nombre AS tema FROM cursos a JOIN temas b ON a.ID = b.IDcurso WHERE b.ID != '.decrypt($IDtema).' AND a.archivar = 0 ORDER BY a.orden, a.nombre, b.orden, b.nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaUbicaciones, array(
			'IDcurso' => $row['IDcurso'], 
			'IDtema' => $row['IDtema'], 
			'nombre' => $row['curso'].': '.$row['tema']
		));
	}

	return $listaUbicaciones;
}

/*
 * encriptarTemas: Encripta todos los IDs de los temas:
 */
function encriptarTemas($encriptarForzado = 0) {
	global $db;
	
	$res = $db->query('SELECT ID FROM temas');
	while ($row = $res->fetchArray()) {
		if ($encriptarForzado == 0) {
			$db->exec('UPDATE temas SET IDencriptado = "'.$row['ID'].'" WHERE ID = '.$row['ID']);
		} else {
			$db->exec('UPDATE temas SET IDencriptado = "'.encrypt($row['ID'], $encriptarForzado).'" WHERE ID = '.$row['ID']);
		}
	}
}

/*
 * temaCambiarIDcurso: Cambia el ID curso asociado a un tema
 */
function temaCambiarIDcurso($IDcurso, $IDcursoNew, $IDtema) {
	global $db;
	
	$db->exec('UPDATE temas SET IDcurso = '.decrypt($IDcursoNew).' WHERE ID = '.$IDtema.' AND IDcurso = '.decrypt($IDcurso));
}
?>