<?php

include_once('modules/config.php');
include_once('modules/cursos.php');
include_once('modules/temas.php');
include_once('modules/videos.php');
include_once('modules/adjuntos.php');
include_once('modules/categorias.php');
include_once('modules/usuarios.php');


// Inicio: Crear la base de datos y la conexión a ésta:
dbCreate(_BBDD);
dbAnalyticsCreate(_BBDDANALYTICS);


/*
 dbCreate: Crea la base de datos y una conexión a ésta:
 */
function dbCreate($dbName) {
	global $db;
	
	if (!file_exists($dbName)) {
	//	echo 'Creando bbdd...<br />';
		$db = new SQLite3($dbName);
		chmod($dbName, 0777);

	//	echo 'Creando tablas...<br />';
		crearTablas();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$db = new SQLite3($dbName);
	}
}

function dbConfigCreate($dbConfigName) {
	global $dbConfig;

	if (!file_exists($dbConfigName)) {
	//	echo 'Creando bbddLog...<br />';
		$dbConfig = new SQLite3($dbConfigName);
		chmod($dbConfigName, 0777);

	//	echo 'Creando tablasLog...<br />';
		crearTablasConfig();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$dbConfig = new SQLite3($dbConfigName);
	}
}

function dbLogCreate($dbLogName) {
	global $dbLog;

	if (!file_exists($dbLogName)) {
	//	echo 'Creando bbddLog...<br />';
		$dbLog = new SQLite3($dbLogName);
		chmod($dbLogName, 0777);

	//	echo 'Creando tablasLog...<br />';
		crearTablasLog();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$dbLog = new SQLite3($dbLogName);
	}
}

function dbAnalyticsCreate($dbAnName) {
	global $dbAn;

	if (!file_exists($dbAnName)) {
	//	echo 'Creando bbddLog...<br />';
		$dbAn = new SQLite3($dbAnName);
		chmod($dbAnName, 0777);

	//	echo 'Creando tablasLog...<br />';
		crearTablasAnalytics();
	} else {
	//	echo 'La base de datos ya existe<br />';
		$dbAn = new SQLite3($dbAnName);
	}
}

function crearTablas() {
	global $db;

	$db->exec('CREATE TABLE cursos (
		ID INTEGER PRIMARY KEY, 
		IDencriptado TEXT,
		nombre TEXT, 
		descripcion TEXT,
		ruta TEXT,
		ubicacion INTEGER,
		orden INTEGER,
		ocultar INTEGER,
		archivar INTEGER,
		anoAcademico TEXT,
		IDcursoMoodle INTEGER,
		fechaIni DATE,
		fechaFin DATE,
		publico TEXT);'
	);
	$db->exec('CREATE TABLE temas (
		ID INTEGER PRIMARY KEY, 
		IDencriptado TEXT,
		IDcurso INTEGER,
		nombre TEXT, 
		descripcion TEXT, 
		ruta TEXT,
		orden INTEGER,
		ocultar INTEGER);'
	);
	$db->exec('CREATE TABLE videos (
		ID INTEGER PRIMARY KEY, 
		IDencriptado TEXT,
		IDcurso INTEGER,
		IDtema INTEGER, 
		nombre TEXT, 
		descripcion TEXT, 
		ruta TEXT, 
		img TEXT, 
		fechaCaducidad DATE,
		orden INTEGER,
		ocultar INTEGER);'
	);
	$db->exec('CREATE TABLE videosAdjuntos (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		IDtema INTEGER, 
		IDvideo INTEGER, 
		nombre TEXT, 
		descripcion TEXT, 
		ruta TEXT, 
		fechaCaducidad DATE,
		orden INTEGER,
		ocultar INTEGER);'
	);
	$db->exec('CREATE TABLE categorias (
		ID INTEGER PRIMARY KEY, 
		IDencriptado TEXT,
		IDcurso INTEGER,
		IDtema INTEGER, 
		IDvideo INTEGER, 
		nombre TEXT);'
	);
	$db->exec('CREATE TABLE usuarios (
		ID INTEGER PRIMARY KEY, 
		fullname TEXT, 
		email TEXT,
		bloqueado INTEGER,
		username TEXT,
		esAdmin INTEGER);'
	);
	
	createUsuario('Administrador', 'admin@localhost.com', 0, 'admin', 1);

	$db->exec('CREATE TABLE cursosUsuarios (
		ID INTEGER PRIMARY KEY, 
		IDusuario INTEGER,
		IDcurso INTEGER,
		IDcursoMoodle INTEGER);'
	);
}


function crearTablasConfig() {
	global $dbConfig;

	$dbConfig->exec('CREATE TABLE adminvars (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT,
		valor TEXT);'
	);

	createAdminvar('showErrors',1);
	createAdminvar('_WSTOKEN','');
	createAdminvar('_MOODLEURL','');
	createAdminvar('_DIRCURSOS','data/');
	createAdminvar('_OCULTO',0);
	createAdminvar('_ADMINDEF','estadisticas');
	createAdminvar('_ADMINPASS','#Admin1');
	createAdminvar('_ALLOWFILEUPLOAD', 1);
	createAdminvar('_ALLOWIMGUPLOAD', 1);
	createAdminvar('_ALLOWVIDEOUPLOAD', 1);
	createAdminvar('_ENCRIPTAR', 0);
	createAdminvar('_EKEY', '4243bcdce4ffdb41b613');
	createAdminvar('_AKEY', 'ef515dff755448e12100');
	createAdminvar('cronProgramado', 0);
	createAdminvar('cronTime', '00:00');
	createAdminvar('cronRepeat', 'daily');
	createAdminvar('cronConfig', '');

	$dbConfig->exec('CREATE TABLE ubicaciones (
		ID INTEGER PRIMARY KEY, 
		ruta TEXT);'
	);

	createUbicacion('cursos/');

	$dbConfig->exec('CREATE TABLE extensionesValidas (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT);'
	);

	createExtension('mp4');


	$dbConfig->exec('CREATE TABLE moodleRoles (
		ID INTEGER PRIMARY KEY, 
		nombre TEXT,
		esAdmin INTEGER,
		importar INTEGER);'
	);

	createMoodleRol('student',0,1);
	createMoodleRol('teacher',0,0);
	createMoodleRol('editingteacher',1,0);
	createMoodleRol('manager',1,0);
}

function crearTablasLog() {
	global $dbLog;

	$dbLog->exec('CREATE TABLE log (
		ID INTEGER PRIMARY KEY, 
		descripcion TEXT,
		"timestamp" DATETIME DEFAULT CURRENT_TIMESTAMP);'
	);
}

function crearTablasAnalytics() {
	global $dbAn;

	$dbAn->exec('CREATE TABLE analytics (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		IDtema INTEGER,
		IDvideo INTEGER,
		IDusuario INTEGER,
		"timestamp" DATETIME DEFAULT CURRENT_TIMESTAMP);'
	);

	$dbAn->exec('CREATE TABLE descargas (
		ID INTEGER PRIMARY KEY, 
		IDcurso INTEGER,
		IDtema INTEGER,
		IDvideo INTEGER,
		IDadjunto INTEGER,
		IDusuario INTEGER,
		"timestamp" DATETIME DEFAULT CURRENT_TIMESTAMP);'
	);

	$dbAn->exec('CREATE TABLE accesos (
		ID INTEGER PRIMARY KEY, 
		IDusuario INTEGER,
		tipo TEXT,
		action TEXT,
		"timestamp" DATETIME DEFAULT CURRENT_TIMESTAMP);'
	);
}

function resetDB() {
	global $db;

	$db->exec('DELETE FROM videosAdjuntos');
	$db->exec('DELETE FROM videos');
	$db->exec('DELETE FROM temas');
	$db->exec('DELETE FROM cursos');
}

function resetDBLog() {
	global $dbLog;

	$dbLog->exec('DELETE FROM log');
}

function resetDBAnalytics() {
	global $dbAn;

	$dbAn->exec('DELETE FROM analytics');
	$dbAn->exec('DELETE FROM descargas');
	$dbAn->exec('DELETE FROM accesos');
}




/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------    LOG    ------------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

function logAction($action) {
	global $dbLog;

	$dbLog->exec('INSERT INTO log (descripcion) VALUES ("'.$action.'")');
}




/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ---------------------------------------------------    ANALYTICS    ---------------------------------------------------- */
/* ------------------------------------------------------------------------------------------------------------------------ */
/* ------------------------------------------------------------------------------------------------------------------------ */

function videoPlayed($IDcurso, $IDtema, $IDvideo, $IDusuario) {
	global $dbAn;

	$dbAn->exec('INSERT INTO analytics (IDcurso, IDtema, IDvideo, IDusuario) VALUES ('.decrypt($IDcurso).','.decrypt($IDtema).','.decrypt($IDvideo).','.$IDusuario.')');
}


function descargarArchivo($IDcurso, $IDtema, $IDvideo, $IDadjunto, $IDusuario) {
	global $dbAn;
	echo 'INSERT INTO descargas (IDcurso, IDtema, IDvideo, IDadjunto, IDusuario) VALUES ('.decrypt($IDcurso).','.decrypt($IDtema).','.decrypt($IDvideo).','.$IDadjunto.','.$IDusuario.')';
	$dbAn->exec('INSERT INTO descargas (IDcurso, IDtema, IDvideo, IDadjunto, IDusuario) VALUES ('.decrypt($IDcurso).','.decrypt($IDtema).','.decrypt($IDvideo).','.$IDadjunto.','.$IDusuario.')');
}

function getTotalDescargas($IDcurso, $IDtema, $IDvideo, $IDadjunto) {
	global $dbAn;
	
	return $dbAn->querySingle('SELECT COUNT(*) FROM descargas WHERE IDcurso = '.decrypt($IDcurso).' AND IDtema = '.decrypt($IDtema).' AND IDvideo = '.decrypt($IDvideo).' AND IDadjunto = '.$IDadjunto);
}

function logAcceso($IDusuario, $tipo, $action) {
	global $dbAn;

	if ($IDusuario != '') {
		$dbAn->exec('INSERT INTO accesos (IDusuario, tipo, action) VALUES ('.$IDusuario.',"'.$tipo.'","'.$action.'")');
	} else {
		$dbAn->exec('INSERT INTO accesos (tipo, action) VALUES ("'.$tipo.'","'.$action.'")');
	}
}

?>