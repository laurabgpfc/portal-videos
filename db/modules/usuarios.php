<?php

/*
 createUsuario: Crea un usuario
 */
function createUsuario($fullname, $email, $bloqueado, $username, $esAdmin) {
	global $db;

	$SQL = 'INSERT INTO usuarios (';
	$SQL .= 'fullname';
	$SQL .= ($email != '')?',email':'';
	$SQL .= ($bloqueado != '')?',bloqueado':'';
	$SQL .= ',username';
	$SQL .= ',esAdmin';
	$SQL .= ') VALUES (';
	$SQL .= '"'.$fullname.'"';
	$SQL .= ($email != '')?',"'.$email.'"':'';
	$SQL .= ($bloqueado != '')?','.$bloqueado:'';
	$SQL .= ',"'.$username.'"';
	$SQL .= ','.$esAdmin;
	$SQL .= ')';
	
	$db->exec($SQL);
}

/*
 updateUsuarioEsAdmin: Actualiza un usuario a administrador o no
 */
function updateUsuarioEsAdmin($IDusuario, $esAdmin) {
	global $db;
	
	$db->exec('UPDATE usuarios SET esAdmin = '.$esAdmin.' WHERE ID = '.$IDusuario);
}
/*
 deleteUsuario: Elimina un usuario
 */
function deleteUsuario($IDusuario) {
	global $db;
	
	$db->exec('DELETE FROM cursosUsuarios WHERE IDusuario = '.$IDusuario);
	$db->exec('DELETE FROM usuarios WHERE ID = '.$IDusuario);
}

/*
 checkUsuario: Devuelve true si el usuario existe, y false si no.
 */
function checkUsuario($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM usuarios WHERE '.$condicion) > 0);
}

/*
 bloquearUsuario: Cambia el estado de bloqueo de un usuario
 */
function bloquearUsuario($IDusuario, $bloqueado) {
	global $db;

	$db->exec('UPDATE usuarios SET bloqueado = '.$bloqueado.' WHERE ID = '.$IDusuario);
}

/*
 asociarUsernameEmail: Asocia un correo a un username
 */
function asociarUsernameEmail($email, $username) {
	global $db;
	
	if (checkUsuario('email = "'.$email.'"') > 0) {
		$db->exec('UPDATE usuarios SET username = "'.$username.'" WHERE email = "'.$email.'"');
	}
}

/*
 getUserFullname: Obtiene el fullname de un usuario
 */
function getUserFullname($username) {
	global $db;
	
	return $db->querySingle('SELECT fullname FROM usuarios WHERE username = "'.$username.'"');
}

/*
 getUserID: Obtiene el ID de un usuario
 */
function getUserID($username) {
	global $db;
	
	return $db->querySingle('SELECT IDencriptado FROM usuarios WHERE username = "'.$username.'"');
}


/*
 getAllUsuarios: Obtener la lista completa de usuarios
 */
function getAllUsuarios($returnAdmin = 0) {
	global $db;
	
	$listaUsuarios = array();

	$res = $db->query('SELECT * FROM usuarios'.( $returnAdmin==0 ? ' WHERE fullname != "Administrador"' : '' ).' ORDER BY fullname');
	while ($row = $res->fetchArray()) {
		$cursos = array();

		$resCursos = $db->query('SELECT * FROM cursosUsuarios WHERE IDusuario = '.$row['ID']);
		while ($rowCursos = $resCursos->fetchArray()) {
			array_push($cursos, $rowCursos['IDcursoMoodle']);
		}
		
		array_push($listaUsuarios, array( 'ID' => $row['ID'], 'fullname' => $row['fullname'], 'email' => $row['email'], 'bloqueado' => $row['bloqueado'], 'esAdmin' => $row['esAdmin'], 'username' => $row['username'], 'cursos' => $cursos ));
	}

	return $listaUsuarios;
}

/*
 * getUserData: devuelve un array con toda la información de un curso:
 */
function getUserData($IDusuario, $username, $email) {
	global $db;
	
	$usuario = array(
		'IDusuario' => 0,
		'fullname' => $username,
		'email' => $email,
		'bloqueado' => 0,
		'username' => $username,
		'esAdmin' => 0
	);

	if ( ($IDusuario > 0)||($username != '')||($email != '') ) {
		$SQL = 'SELECT * FROM usuarios WHERE ';
		( ($IDusuario != '') ? $SQL .= 'ID = '.$IDusuario : ( ($username != '') ? $SQL .= 'username = "'.$username.'"' : ( ($email != '') ? $SQL .= 'email = "'.$email.'"' : '' ) ) );
		
		$res = $db->query($SQL);
		while ($row = $res->fetchArray()) {
			$usuario = array(
				'IDusuario' => $row['ID'],
				'fullname' => $row['fullname'],
				'email' => $row['email'],
				'bloqueado' => $row['bloqueado'],
				'username' => $row['username'],
				'esAdmin' => $row['esAdmin']
			);
		}
	}

	return $usuario;
}

/*
 getCursoUsuarios: Obtiene una lista de los usuarios inscritos a un curso
 */
function getUsuariosByCurso($IDcurso) {
	global $db;
	
	$listaUsuarios = array();

	$res = $db->query('SELECT * FROM usuarios WHERE ID IN (SELECT IDusuario FROM cursosUsuarios WHERE IDcurso = '.decrypt($IDcurso).')');
	
	while ($row = $res->fetchArray()) {
		array_push($listaUsuarios, array(
			'ID' => $row['ID'],
			'fullname' => $row['fullname'],
			'email' => $row['email'],
			'esAdmin' => $row['esAdmin']
		));
	}

	return $listaUsuarios;
}




/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* -------------------------------------------------    CURSO-USUARIO    -------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

/*
 createCursoUsuario: Crea un registro en la tabla cursosUsuarios
 */
function createCursoUsuario($IDcurso, $IDcursoMoodle, $IDusuario) {
	global $db;
	
	if ($db->querySingle('SELECT COUNT(*) FROM cursosUsuarios WHERE IDcurso = '.decrypt($IDcurso).' AND IDcursoMoodle = '.$IDcursoMoodle.' AND IDusuario = '.$IDusuario) == 0) {
		$db->exec('INSERT INTO cursosUsuarios (IDcurso, IDcursoMoodle, IDusuario) VALUES ('.decrypt($IDcurso).', '.$IDcursoMoodle.', '.$IDusuario.')');
	}
}

/*
 deleteCursoUsuario: Elimina un registro de cursosUsuarios
 */
function deleteCursoUsuario($IDcurso, $IDcursoMoodle, $IDusuario) {
	global $db;

	$db->exec('DELETE FROM cursosUsuarios WHERE IDcurso = '.decrypt($IDcurso).' AND IDcursoMoodle = '.$IDcursoMoodle.' AND IDusuario = '.$IDusuario);
}

/*
 checkCursoUsuario: Devuelve true si el curso-usuario existe, y false si no.
 */
function checkCursoUsuario($condicion) {
	global $db;
	
	return ($db->querySingle('SELECT COUNT(*) FROM cursosUsuarios WHERE '.$condicion) > 0);
}

/*
 registrarUsuarioCurso: Registra una serie de usuarios en un curso
 */
function registrarUsuarioCurso($IDcurso, $IDcursoMoodle, $fullname, $email, $esAdmin) {
	global $db;

	// Comprobar si el usuario existe en la BBDD:
	if (checkUsuario('email = "'.$email.'"') == 0) {
		createUsuario($fullname, $email, 0, '', $esAdmin);
	}

	// Obtener el ID de usuario:
	$IDusuario = $db->querySingle('SELECT ID FROM usuarios WHERE email = "'.$email.'"');

	// Comprobar si el usuario existe y tiene el rol de admin bien puesto:
	if ( (checkUsuario('email = "'.$email.'"') > 0)&&(checkUsuario('email = "'.$email.'" AND esAdmin = '.$esAdmin) == 0) ) {
		updateUsuarioEsAdmin($IDusuario, $esAdmin);
	}

	// Añadir usuario a curso:
	createCursoUsuario($IDcurso, $IDcursoMoodle, $IDusuario);
}

/*
 desregistrarUsuariosCurso: Registra una serie de usuarios en un curso
 */
function desregistrarUsuariosCurso($IDcurso) {
	global $db;

	$db->exec('DELETE FROM cursosUsuarios WHERE IDcurso = '.decrypt($IDcurso));
}
?>