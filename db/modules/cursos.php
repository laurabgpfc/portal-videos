<?php


/*
 createCurso: Crea un curso con los parámetros que se facilitan
 */
function createCurso($nombre, $descripcion, $ruta, $ubicacion, $orden, $ocultar, $IDcursoMoodle, $fechaIni, $fechaFin, $publico, $archivar = 0, $anoAcademico = "") {
	global $db;

	$SQL = 'INSERT INTO cursos (nombre, descripcion, ruta, ubicacion, orden, ocultar, IDcursoMoodle, fechaIni, fechaFin, publico, archivar, anoAcademico) ';
	$SQL .= 'VALUES ("'.$nombre.'", "'.$descripcion.'", "'.$ruta.'", '.$ubicacion.', '.$orden.', '.$ocultar.', '.( $IDcursoMoodle == "" ? 0 : $IDcursoMoodle ).', "'.$fechaIni.'", "'.$fechaFin.'", '.$publico.', '.$archivar.', "'.$anoAcademico.'")';

	$db->exec($SQL);

	// Una vez creado el curso, obtener su ID y encriptarlo:
	$IDcurso = $db->querySingle('SELECT ID FROM cursos WHERE ruta = "'.$ruta.'" AND ubicacion = '.$ubicacion);

	// Actualizar el registro:
	$db->exec('UPDATE cursos SET IDencriptado = "'.encrypt($IDcurso).'" WHERE ID = '.$IDcurso);
}

/*
 updateCurso: Crea un curso con los parámetros que se facilitan
 */
function updateCurso($IDcurso, $nombre, $descripcion, $ruta, $ubicacion, $orden, $ocultar, $IDcursoMoodle, $fechaIni, $fechaFin, $publico, $archivar = 0, $anoAcademico = "") {
	global $db;

	$SQL = 'UPDATE cursos SET ';
	$SQL .= 'nombre = "'.$nombre.'"';
	$SQL .= ', descripcion = "'.$descripcion.'"';
	$SQL .= ', ruta = "'.$ruta.'"';
	$SQL .= ', ubicacion = '.$ubicacion;
	$SQL .= ', orden = '.$orden;
	$SQL .= ', ocultar = '.$ocultar;
	$SQL .= ( ($IDcursoMoodle != "") ? ', IDcursoMoodle = '.$IDcursoMoodle : '' );
	$SQL .= ', fechaIni = "'.$fechaIni.'"';
	$SQL .= ', fechaFin = "'.$fechaFin.'"';
	$SQL .= ', publico = '.$publico;
	$SQL .= ( ($archivar != "") ? ', archivar = '.$archivar : '' );
	$SQL .= ( ($anoAcademico != "") ? ', anoAcademico = "'.$anoAcademico.'"' : '' );
	$SQL .= ' WHERE ID = '.decrypt($IDcurso);
	
	$db->exec($SQL);
}

/*
 updateCursoOrden: Actualiza el orden de un curso
 */
function updateCursoOrden($IDcurso, $orden) {
	global $db;

	$db->exec('UPDATE cursos SET orden = '.$orden.' WHERE ID = '.decrypt($IDcurso));
}

/*
 deleteFullCurso: Elimina un curso completo, con sus temas y videos
 */
function deleteFullCurso($IDcurso) {
	global $db;

	desregistrarUsuariosCurso($IDcurso);
	
	$db->exec('DELETE FROM videosAdjuntos WHERE IDcurso = '.decrypt($IDcurso));
	$db->exec('DELETE FROM videos WHERE IDcurso = '.decrypt($IDcurso));
	$db->exec('DELETE FROM temas WHERE IDcurso = '.decrypt($IDcurso));
	$db->exec('DELETE FROM cursos WHERE ID = '.decrypt($IDcurso));
}

/*
 checkCurso: Devuelve true si el curso existe, y false si no.
 */
function checkCurso($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM cursos WHERE '.$condicion) > 0);
}

/*
 getIDcurso: Devuelve el ID encriptado del curso por nombre y ruta
 */
function getIDcurso($nombre, $ruta, $IDubicacion, $crearCurso) {
	global $db;

	if ( ($crearCurso == 1)&&(checkCurso('ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion) == 0) ) {
		$orden = getNextOrdenCurso();

		createCurso($nombre, '', $ruta, $IDubicacion, $orden, _OCULTO, 0, '', '', 0);
	}
	
	return $db->querySingle('SELECT IDencriptado FROM cursos WHERE ruta = "'.$ruta.'" AND ubicacion = '.$IDubicacion);
}

/*
 * getNextOrdenCurso: devuelve el siguiente orden en la tabla cursos
 */
function getNextOrdenCurso() {
	global $db;

	$orden = $db->querySingle('SELECT MAX(orden)+1 FROM cursos');
	if (!$orden) {
		$orden = 1;
	}
	return $orden;
}

/*
 getIDcursoByIDcursoMoodle: Devuelve el ID encriptado del curso asociado al ID de moodle
 */
function getIDcursoByIDcursoMoodle($IDcursoMoodle) {
	global $db;

	return $db->querySingle('SELECT IDencriptado FROM cursos WHERE IDcursoMoodle = '.$IDcursoMoodle);
}

/*
 * getCursoData: devuelve un array con toda la información de un curso:
 */
function getCursoData($IDcurso) {
	global $db;
	
	$curso = array();
	
	$res = $db->query('SELECT * FROM cursos WHERE ID = '.decrypt($IDcurso));

	while ($row = $res->fetchArray()) {
		$curso = array(
			'IDcurso' => $row['IDencriptado'],
			'nombre' => $row['nombre'],
			'descripcion' => $row['descripcion'],
			'ruta' => $row['ruta'],
			'ubicacion' => $row['ubicacion'],
			'orden' => $row['orden'],
			'ocultar' => $row['ocultar'],
			'IDcursoMoodle' => $row['IDcursoMoodle'],
			'fechaIni' => $row['fechaIni'],
			'fechaFin' => $row['fechaFin'],
			'publico' => $row['publico'],
			'archivar' => $row['archivar']
		);
	}

	return $curso;
}

/*
 * getListaCursos: devuelve un array con todos los cursos ordenados:
 */
function getListaCursos($anoAcademico = "") {
	global $db;
	
	$listaCursos = array();

	$res = $db->query('SELECT * FROM cursos WHERE '.( $anoAcademico != "" ?  'archivar = 1 AND anoAcademico = "'.$anoAcademico.'"' : 'archivar = 0' ).' ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaCursos, array($row['IDencriptado'], $row['nombre'], $row['IDcursoMoodle']));
	}

	return $listaCursos;
}

/*
 * getListaAnosAcademicos: devuelve un array con todos los años academicos distintos que hay:
 */
function getListaAnosAcademicos() {
	global $db;
	
	$listaAnosAcademicos = array();

	$res = $db->query('SELECT DISTINCT anoAcademico FROM cursos WHERE anoAcademico != "" ORDER BY anoAcademico');
	while ($row = $res->fetchArray()) {
		array_push($listaAnosAcademicos, $row['anoAcademico']);
	}

	return $listaAnosAcademicos;
}
/*
 * getListaCursosArchivados: devuelve un array con todos los cursos que estan archivados ordenados:
 */
function getListaCursosArchivados() {
	global $db;
	
	$listaCursos = array();

	$res = $db->query('SELECT * FROM cursos WHERE archivar = 1 ORDER BY anoAcademico, orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaCursos, array($row['IDencriptado'], $row['nombre'], $row['IDcursoMoodle'], $row['anoAcademico']));
	}

	return $listaCursos;
}

/*
 * getListaCursosDisponibles: Devuelve la lista de cursos disponibles, salvo el dado
 */
function getListaCursosDisponibles($IDcurso) {
	global $db;
	
	$listaUbicaciones = array();

	$res = $db->query('SELECT * FROM cursos WHERE ID != '.decrypt($IDcurso).' AND archivar = 0 ORDER BY orden, nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaUbicaciones, array(
			'ID' => $row['IDencriptado'], 
			'nombre' => $row['nombre']
		));
	}

	return $listaUbicaciones;
}

/*
 * encriptarCursos: Encripta todos los IDs de los cursos:
 */
function encriptarCursos($encriptarForzado = 0) {
	global $db;
	
	$res = $db->query('SELECT ID FROM cursos');
	while ($row = $res->fetchArray()) {
		if ($encriptarForzado == 0) {
			$db->exec('UPDATE cursos SET IDencriptado = "'.$row['ID'].'" WHERE ID = '.$row['ID']);
		} else {
			$db->exec('UPDATE cursos SET IDencriptado = "'.encrypt($row['ID'], $encriptarForzado).'" WHERE ID = '.$row['ID']);
		}
	}
}

/*
 * archivarCurso: Archiva el curso dado:
 */
function archivarCurso($IDcurso, $anoAcademico) {
	global $db;
	
	$db->exec('UPDATE cursos SET archivar = 1, anoAcademico = "'.$anoAcademico.'", IDcursoMoodle = 0 WHERE ID = '.decrypt($IDcurso));
}

/*
 * recuperarCursoArchivado: Recupera un curso archivado:
 */
function recuperarCursoArchivado($IDcurso) {
	global $db;
	
	$db->exec('UPDATE cursos SET archivar = 0, anoAcademico = "" WHERE ID = '.decrypt($IDcurso));
}

?>