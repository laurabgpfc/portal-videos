<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

//foreach ($_POST as $key => $value)
//	print "Field ".$key." is ".$value."<br>";

$cursoData = getCursoData($_POST['IDcurso']);

if ( (isset($_COOKIE['MoodleUserSession']))&&($MoodleUserSession['esAdmin'] == 0) ) {
	descargarArchivo($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['IDadjunto'], $MoodleUserSession['IDusuario']);
} else if ($cursoData['publico'] == 1) {
	descargarArchivo($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], $_POST['IDadjunto'], 0);
}

?>