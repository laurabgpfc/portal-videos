<?php

// Definición de constantes
define('_PORTALROOT', '/portal-videos/');
define('_DOCUMENTROOT', __DIR__.'/');
define('_BBDD', _DOCUMENTROOT.'db/dbportalvideos.db');
define('_BBDDCONFIG', _DOCUMENTROOT.'db/dbconfig.db');
define('_BBDDLOG', _DOCUMENTROOT.'db/dblog.db');
define('_BBDDANALYTICS', _DOCUMENTROOT.'db/dbanalytics.db');

include_once(_DOCUMENTROOT.'db/db.php');
include_once(_DOCUMENTROOT.'util/encrypt-decrypt.php');

$dbConfig = null;

dbConfigCreate(_BBDDCONFIG);

if (getAdminvar('showErrors') == 1) {
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

define('_WSTOKEN', getAdminvar('_WSTOKEN'));
define('_MOODLEURL', getAdminvar('_MOODLEURL'));
define('_DIRCURSOS', getAdminvar('_DIRCURSOS'));
define('_OCULTO', getAdminvar('_OCULTO'));
define('_ADMINDEF', getAdminvar('_ADMINDEF'));
define('_ADMINPASS', getAdminvar('_ADMINPASS'));
define('_ALLOWFILEUPLOAD', getAdminvar('_ALLOWFILEUPLOAD'));
define('_ALLOWIMGUPLOAD', getAdminvar('_ALLOWIMGUPLOAD'));
define('_ALLOWVIDEOUPLOAD', getAdminvar('_ALLOWVIDEOUPLOAD'));
define('_ENCRIPTAR', getAdminvar('_ENCRIPTAR'),true);
define('_EKEY', getAdminvar('_EKEY'));
define('_AKEY', getAdminvar('_AKEY'));


// Lista de extensiones válidas:
$extensionesValidas = listaExtensiones(1);

// Lista de directorios desde los que leer los cursos:
$listaDirs = listaUbicaciones(1);

// Valor desencriptado de la cookie MoodleUserSession (si no existe, será vacío):
$MoodleUserSession = decrypt($_COOKIE['MoodleUserSession'],1);

// Definición de funnciones, por si no existen:
if (!function_exists("array_column")) {
	function array_column($array,$column_name) {
		return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
	}
}
?>