<?php
	
	include_once(__DIR__.'/../config.php');
	
	if ( (isset($_GET['username']))&&(isset($_GET['email'])) ) {
		if (checkUsuario('email = "'.$_GET['email'].'" AND username = ""') > 0) {
			asociarUsernameEmail($_GET['email'], $_GET['username']);
		}
		$usuario = getUserData('', $_GET['username'], '');
		setcookie('MoodleUserSession', encrypt($usuario,1), time() + (86400 * 30), _PORTALROOT);
		
		// Añadir registro al log de accesos:
		if (isset($_COOKIE['MoodleUserSession'])) {
			logAcceso($usuario['IDusuario'], 'login-moodle', 'Acceso desde Moodle de '.$usuario['fullname']);
		}
		
		header('Location: http://'.$_SERVER['HTTP_HOST']._PORTALROOT.'?IDcurso='.$_GET['IDcurso']);
		die();
	}
	
	if ( (isset($_GET['opt'])) ) {
		setcookie('listMode', $_GET['opt'], time() + (86400 * 30), _PORTALROOT);

		$params = str_replace('opt='.$_GET['opt'].'&', '', $_SERVER['QUERY_STRING']);
		header('Location: http://'.$_SERVER['HTTP_HOST']._PORTALROOT.'?'.$params);
		die();
	}

	if ( (isset($_GET['cat'])) ) {
		setcookie('cat', $_GET['cat'], time() + (86400 * 30), _PORTALROOT);

		$params = str_replace('&cat='.$_GET['cat'], '', $_SERVER['QUERY_STRING']);
		header('Location: http://'.$_SERVER['HTTP_HOST']._PORTALROOT.'?'.$params);
		die();
	}
	
	// Listado principal |-----------------------------------------------
	if (!isset($_GET['IDcurso'])) {
		include_once(_DOCUMENTROOT.'modules/mainList.php');

	// Listado contenido cursos |------------------------------------------------
	} elseif ( (isset($_GET['IDcurso']))&&(!isset($_GET['IDvideo'])) ) {
		include_once(_DOCUMENTROOT.'modules/detalleCurso.php');

	// Detalle vídeo |------------------------------------------------
	} elseif ( (isset($_GET['IDcurso']))&&(isset($_GET['IDtema']))&&(isset($_GET['IDvideo'])) ) {
		include_once(_DOCUMENTROOT.'modules/detalleVideo.php');
	}

	// Añadir registro al log de accesos:
	if (isset($_COOKIE['MoodleUserSession'])) {
		logAcceso(decrypt($_COOKIE['MoodleUserSession'],1)['IDusuario'], 'visita', 'http://'.$_SERVER['HTTP_HOST']._PORTALROOT.'?'.$_SERVER['QUERY_STRING']);
	} else {
		logAcceso(0, 'visita', 'http://'.$_SERVER['HTTP_HOST']._PORTALROOT.'?'.$_SERVER['QUERY_STRING']);
	}
?>