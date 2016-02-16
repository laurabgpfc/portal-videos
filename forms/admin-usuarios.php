<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

global $db;

$msgError = '';
$error = 'success';

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".($value)."<br>";

if ($_POST['form'] == 'usuarios') {
	if (isset($_POST['block-access-user'])) {
		$msgError = 'Usuario bloqueado';
		$error = 'warning'; 

		bloquearUsuario($_POST['block-access-user'], 1);

	} else if (isset($_POST['unblock-access-user'])) {
		$msgError = 'Usuario desbloqueado';
		$error = 'success'; 

		bloquearUsuario($_POST['unblock-access-user'], 0);

	} else if (isset($_POST['del-user'])) {
		$msgError = 'Usuario eliminado';
		$error = 'danger'; 

		deleteUsuario($_POST['del-user']);
		
	} else if (isset($_POST['save-cursos-user'])) {
		$msgError = 'Datos actualizados correctamente'; 
		$error = 'success'; 

		if ( (isset($_POST['check-curso-user']))&&(sizeof($_POST['check-curso-user']) > 0) ) {
			foreach ($_POST['check-curso-user'] as $IDcursoMoodle => $valor) {
				$IDcurso = getIDcursoByIDcursoMoodle($IDcursoMoodle);

				if ($valor == 'on') {
					crearCursoUsuario($IDcurso, $IDcursoMoodle, $_POST['save-cursos-user']);
				} else {
					deleteCursoUsuario($IDcurso, $IDcursoMoodle, $_POST['save-cursos-user']);
				}
			}
		}

	} else if (isset($_POST['add-user'])) {
		if ( ($_POST['fullname'] != '')&&($_POST['email'] != '') ) {
			// Comprobar que el email es valido:
			if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				$msgError = 'Invalid email format'; 
				$error = 'warning';

			} else {
				crearUsuario($_POST['fullname'], $_POST['email'], 0, '', 0);

				// Crear el usuario:
				$msgError = 'Datos guardados correctamente'; 
			}
		} else {
			$msgError = 'Los datos son obligatorios'; 
			$error = 'warning';
		}
	}

}

?>