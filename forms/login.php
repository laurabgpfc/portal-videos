<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'util/login-moodle.php');

//foreach ($_POST as $key => $value)
//	print "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";


if (isset($_POST['login'])) {
	if ( ($_POST['userName'] != '')&&($_POST['userPass'] != '') ) {
		if ( ($_POST['userName'] == 'admin')&&($_POST['userPass'] != _ADMINPASS) ) {
			echo 'Los datos de acceso son incorrectos';

		} else if ( ($_POST['userName'] == 'admin')&&($_POST['userPass'] == _ADMINPASS) ) {
			$usuario = getUserData('', $_POST['userName'], '');
			setcookie('MoodleUserSession', encrypt($usuario,1), time() + (86400 * 30), '/');
			
			if ($usuario['bloqueado'] == 1) {
				echo 'Este usuario tiene ha sido bloqueado';
			} else {
				// Añadir registro al log de accesos:
				if ($usuario['IDusuario'] > 0) {
					logAcceso($usuario['IDusuario'], 'login', 'Acceso de '.$usuario['fullname']);
				}
			}
		} else {
			$rsp = login($_POST['userName'], $_POST['userPass']);

			if ($rsp == '') {
				$usuario = getUserData('', $_POST['userName'], '');
				setcookie('MoodleUserSession', encrypt($usuario,1), time() + (86400 * 30), '/');
				
				// Comprobar si el usuario esta asociado al email:
				if (checkUsuario('username = "'.$_POST['userName'].'"') == 0) {
					setcookie('MoodleUserFaltaCorreo', encrypt($_POST['userName'],1), time() + (86400 * 30), '/'); // 86400 = 1 day

				} else {
					if ($usuario['bloqueado'] == 1) {
						echo 'Este usuario tiene ha sido bloqueado';
					} else {
						// Añadir registro al log de accesos:
						if ($usuario['IDusuario'] > 0) {
							logAcceso($usuario['IDusuario'], 'login', 'Acceso de '.$usuario['fullname']);
						}
					}
				}
			}

			echo $rsp;
		}

	} else {
		echo 'Los datos de acceso son obligatorios';
	}

} else if (isset($_POST['asociar-correo'])) {
	if ($_POST['email'] == '') {
		echo 'El email es obligatorio';

	} else if ( ($_POST['email'] != '')&&(isset($_COOKIE['MoodleUserFaltaCorreo'])) ) {
		asociarUsernameEmail($_POST['email'], decrypt($_COOKIE['MoodleUserFaltaCorreo'],1));
		$usuario = getUserData('', '', $_POST['email']);
		
		setcookie('MoodleUserSession', encrypt($usuario,1), time() + (86400 * 30), '/');

		// Añadir registro al log de accesos:
		if ($usuario['IDusuario'] > 0) {
			logAcceso($usuario['IDusuario'], 'login', 'Asociado correo de '.$usuario['fullname']);
		}
		
		unset($_COOKIE['MoodleUserFaltaCorreo']);
		setcookie('MoodleUserFaltaCorreo', null, -1, '/');
	}

} else if (isset($_POST['logout'])) {
	// Añadir registro al log de accesos:
	if (isset($_COOKIE['MoodleUserSession'])) {
		logAcceso($MoodleUserSession['IDusuario'], 'logout', 'Desconexion de '.$MoodleUserSession['fullname']);
	}

	logout();

	if (isset($_COOKIE['MoodleUserFaltaCorreo'])) {
		unset($_COOKIE['MoodleUserFaltaCorreo']);
		setcookie('MoodleUserFaltaCorreo', null, -1, '/');
	}
	
	if (isset($_COOKIE['MoodleUserAdmin'])) {
		unset($_COOKIE['MoodleUserAdmin']);
		setcookie('MoodleUserAdmin', null, -1);
	}
}


?>