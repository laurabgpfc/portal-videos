<?php

/*
 createCategoria: Crea una categoria a un video con los parámetros que se facilitan
 */
function createCategoria($IDcurso, $IDtema, $IDvideo, $nombre) {
	global $db;

	$SQL = 'INSERT INTO categorias (IDcurso, IDtema, IDvideo, nombre) ';
	$SQL .= 'VALUES ('.decrypt($IDcurso).', '.decrypt($IDtema).', '.decrypt($IDvideo).', "'.$nombre.'")';
	
	$db->exec($SQL);
	
	// Una vez creado la categoria, obtener su ID y encriptarlo:
	$IDcategoria = $db->querySingle('SELECT ID FROM categorias WHERE nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo));

	// Actualizar el registro:
	$db->exec('UPDATE categorias SET IDencriptado = "'.encrypt($IDcategoria).'" WHERE ID = '.$IDcategoria);
}

/*
 updateCategoria: Actualiza una categoria existente
 */
function updateCategoria($IDcategoria, $IDcurso, $IDtema, $IDvideo, $nombre) {
	global $db;

	$SQL = 'UPDATE categorias SET ';
	$SQL .= 'IDcurso = '.decrypt($IDcurso);
	$SQL .= ', IDtema = '.decrypt($IDtema);
	$SQL .= ', IDvideo = '.decrypt($IDvideo);
	$SQL .= ', nombre = "'.$nombre.'"';
	$SQL .= ' WHERE ID = '.decrypt($IDcategoria);
	
	$db->exec($SQL);
}

/*
 deleteCategoria: Elimina una categoria
 */
function deleteCategoria($IDcategoria) {
	global $db;
	
	$db->exec('DELETE FROM categorias WHERE ID = '.decrypt($IDcategoria));
}

/*
 checkCategoria: Devuelve true si la categoria existe, y false si no.
 */
function checkCategoria($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM categorias WHERE '.$condicion) > 0);
}

/*
 getIDcategoria: Devuelve el ID de la categoria.
 */
function getIDcategoria($IDcurso, $IDtema, $IDvideo, $nombre, $crearCategoria) {
	global $db;

	if ( ($crearCategoria == 1)&&(checkCategoria('nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo)) == 0) ) {
		createCategoria($IDcurso, $IDtema, $IDvideo, $nombre);
	}

	return $db->querySingle('SELECT IDencriptado FROM categorias WHERE nombre = "'.$nombre.'" AND IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo));
}

/*
 * getAllCategorias: devuelve un array con todos las categorias de un video:
 */
function getAllCategorias($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$listaCategorias = array();

	$res = $db->query('SELECT * FROM categorias WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' ORDER BY nombre');
	
	while ($row = $res->fetchArray()) {
		array_push($listaCategorias, array(
			'IDcurso' => $IDcurso,
			'IDtema' => $IDtema,
			'IDvideo' => $IDvideo,
			'IDcategoria' => $row['IDencriptado'],
			'nombre' => $row['nombre']
		));
	}

	return $listaCategorias;
}

/*
 * getCategoriaData: devuelve un array con toda la información de una categoria:
 */
function getCategoriaData($IDcurso, $IDtema, $IDvideo, $IDcategoria) {
	global $db;
	
	$categoria = array();

	$res = $db->query('SELECT * FROM categorias WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' AND ID = '.decrypt($IDcategoria).' ORDER BY nombre');
	while ($row = $res->fetchArray()) {
		$categoria = array(
			'IDcurso' => $IDcurso,
			'IDtema' => $IDtema,
			'IDvideo' => $IDvideo,
			'IDcategoria' => $row['IDencriptado'],
			'nombre' => $row['nombre']
		);
	}

	return $categoria;
}

/*
 * getListaCategoriasByCurso: devuelve un array con todas las categorias de un curso:
 */
function getListaCategoriasByCurso($IDcurso) {
	global $db;
	
	$listaCategorias = array();

	$res = $db->query('SELECT * FROM categorias WHERE IDcurso = '.decrypt($IDcurso).' ORDER BY nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaCategorias, array($row['IDencriptado'], $row['nombre']));
	}

	return $listaCategorias;
}

/*
 * getListaCategoriasByVideoTemaCurso: devuelve un array con todas las categorias de un tema, video y un curso:
 */
function getListaCategoriasByVideoTemaCurso($IDcurso, $IDtema, $IDvideo) {
	global $db;
	
	$listaCategorias = array();

	$res = $db->query('SELECT * FROM categorias WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' ORDER BY nombre');
	while ($row = $res->fetchArray()) {
		array_push($listaCategorias, array($row['IDencriptado'], $row['nombre']));
	}

	return $listaCategorias;
}

/*
 * encriptarCategorias: Encripta todos los IDs de las categorias:
 */
function encriptarCategorias($encriptarForzado = 0) {
	global $db;
	
	$res = $db->query('SELECT ID FROM categorias');
	while ($row = $res->fetchArray()) {
		if ($encriptarForzado == 0) {
			$db->exec('UPDATE categorias SET IDencriptado = "'.$row['ID'].'" WHERE ID = '.$row['ID']);
		} else {
			$db->exec('UPDATE categorias SET IDencriptado = "'.encrypt($row['ID'], $encriptarForzado).'" WHERE ID = '.$row['ID']);
		}
	}
}
?>