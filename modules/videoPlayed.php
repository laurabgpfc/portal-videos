<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'db/db.php');

//foreach ($_POST as $key => $value)
//	print "Field ".$key." is ".$value."<br>";

$cursoData = getCursoData($_POST['IDcurso']);

if ( (isset($_COOKIE['MoodleUserSession']))&&(decrypt($_COOKIE['MoodleUserSession'],1)['esAdmin'] == 0) ) {
	videoPlayed($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], decrypt($_COOKIE['MoodleUserSession'],1)['IDusuario']);
} else if ($cursoData['publico'] == 1) {
	videoPlayed($_POST['IDcurso'], $_POST['IDtema'], $_POST['IDvideo'], 0);
} 
?>