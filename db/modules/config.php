<?php

/*
 createAdminvar: Crea una nueva variable
 */
function createAdminvar($nombre, $valor) {
	global $dbConfig;

	$dbConfig->exec('INSERT INTO adminvars (nombre, valor) VALUES ("'.$nombre.'", "'.$valor.'");');
}

/*
 updateAdminvar: Actualiza una variable
 */
 function updateAdminvar($nombre, $valor) {
	global $dbConfig;

	$dbConfig->exec('UPDATE adminvars SET valor = "'.$valor.'" WHERE nombre = "'.$nombre.'";');
}

/*
 deleteAdminvar: Elimina una variable
 */
function deleteAdminvar($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM adminvars WHERE ID = '.$ID.';');
}

/*
 getConfigData: Obtiene un array con todas las variables de configuracion
 */
function getConfigData() {
	global $dbConfig;
	
	$config = array();
	
	$res = $dbConfig->query('SELECT * FROM adminvars');
	
	while ($row = $res->fetchArray()) {
		$config[$row['nombre']] = $row['valor'];
	}
	
	$config['listaUbicaciones'] = listaUbicaciones(1);
	$config['listaExtensiones'] = listaExtensiones(1);
	$config['listaMoodleRoles'] = listaMoodleRoles();
	
	return $config;
}

/*
 getAdminvar: Obtiene el valor de una variable dada
 */
function getAdminvar($varName) {
	global $dbConfig;

	return $dbConfig->querySingle('SELECT valor FROM adminvars WHERE nombre = "'.$varName.'"');
}




/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* --------------------------------------------------    UBICACIONES    --------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 createUbicacion: Crea una nueva ubicacion
 */
function createUbicacion($ruta) {
	global $dbConfig;

	if (checkUbicacion('ruta = "'.$ruta.'"') == 0) {
		$dbConfig->exec('INSERT INTO ubicaciones (ruta) VALUES ("'.$ruta.'");');
	}
}

/*
 updateUbicacion: Actualiza una ubicacion
 */
function updateUbicacion($ID, $ruta) {
	global $dbConfig;

	$dbConfig->exec('UPDATE ubicaciones SET ruta = "'.$ruta.'" WHERE ID = '.$ID.';');
}

/*
 deleteUbicacion: Elimina una ubicacion
 */
function deleteUbicacion($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM ubicaciones WHERE ID = '.$ID.';');
}

/*
 checkUbicacion: Devuelve true si la ubicacion existe, y false si no
 */
function checkUbicacion($condicion) {
	global $dbConfig;
	
	return ($dbConfig->querySingle('SELECT COUNT(*) FROM ubicaciones WHERE '.$condicion) > 0);
}

/*
 listaUbicaciones: Obtiene un array con las ubicaciones existentes
 */
 function listaUbicaciones($returnID) {
	global $dbConfig;

	$ubicaciones = array();

	$res = $dbConfig->query('SELECT * FROM ubicaciones');
	while ($row = $res->fetchArray()) {
		if ($returnID == 1) {
			array_push($ubicaciones, array( 'ID' => $row['ID'], 'ruta' => $row['ruta']));
		} else {
			array_push($ubicaciones, $row['ruta']);
		}
	}

	return $ubicaciones;
}





/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* --------------------------------------------------    EXTENSIONES    --------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 createExtension: Crea una nueva extension
 */
function createExtension($nombre) {
	global $dbConfig;

	if (checkExtension('nombre = "'.$nombre.'"') == 0) {
		$dbConfig->exec('INSERT INTO extensionesValidas (nombre) VALUES ("'.$nombre.'");');
	}
}

/*
 updateExtension: Actualiza una extension
 */
function updateExtension($ID, $nombre) {
	global $dbConfig;

	$dbConfig->exec('UPDATE extensionesValidas SET nombre = "'.$nombre.'" WHERE ID = '.$ID.';');
}

/*
 deleteExtension: Elimina una extension
 */
function deleteExtension($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM extensionesValidas WHERE ID = '.$ID.';');
}

/*
 checkExtension: Devuelve true si la extension existe, y false si no
 */
function checkExtension($condicion) {
	global $dbConfig;

	return ($dbConfig->querySingle('SELECT COUNT(*) FROM extensionesValidas WHERE '.$condicion) > 0);
}

/*
 listaExtensiones: Obtiene un array con las extensiones existentes
 */
function listaExtensiones($returnID) {
	global $dbConfig;

	$extensiones = array();

	$res = $dbConfig->query('SELECT * FROM extensionesValidas');
	while ($row = $res->fetchArray()) {
		if ($returnID == 1) {
			array_push($extensiones, array( 'ID' => $row['ID'], 'nombre' => $row['nombre']));
		} else {
			array_push($extensiones, $row['ruta']);
		}
	}

	return $extensiones;
}






/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* --------------------------------------------------    MOODLE ROLES    -------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 createMoodleRol: Crea un nuevo rol
 */
function createMoodleRol($nombre, $esAdmin, $importar) {
	global $dbConfig;

	if (checkMoodleRol('nombre = "'.$nombre.'"') == 0) {
		$dbConfig->exec('INSERT INTO moodleRoles (nombre, esAdmin, importar) VALUES ("'.$nombre.'", '.$esAdmin.', '.$importar.');');
	}
}

/*
 updateMoodleRol: Actualiza un rol
 */
function updateMoodleRol($ID, $esAdmin, $importar) {
	global $dbConfig;

	$dbConfig->exec('UPDATE moodleRoles SET esAdmin = '.$esAdmin.', importar = '.$importar.' WHERE ID = '.$ID.';');
}


/*
 deleteMoodleRol: Elimina un rol
 */
function deleteMoodleRol($ID) {
	global $dbConfig;

	$dbConfig->exec('DELETE FROM moodleRoles WHERE ID = '.$ID.';');
}

/*
 checkMoodleRol: Devuelve true si el rol existe, y false si no
 */
function checkMoodleRol($condicion) {
	global $dbConfig;
	
	return ($dbConfig->querySingle('SELECT COUNT(*) FROM moodleRoles WHERE '.$condicion) > 0);
}

/*
 listaMoodleRoles: Obtiene un array con los roles existentes
 */
 function listaMoodleRoles() {
	global $dbConfig;

	$moodleRoles = array();

	$res = $dbConfig->query('SELECT * FROM moodleRoles');
	
	while ($row = $res->fetchArray()) {
		array_push($moodleRoles, array(
			'ID' => $row['ID'],
			'nombre' => $row['nombre'],
			'esAdmin' => $row['esAdmin'],
			'importar' => $row['importar']
		));
	}

	return $moodleRoles;
}


?>