<?php

include_once(__DIR__.'/../config.php');
include_once(_DOCUMENTROOT.'util/file-functions.php');

$renombrarAdjunto = 0;
$dir = '';
$rsp = '';
$error = 'success';


//foreach ($_POST as $key => $value)
//	print "Field ".($key)." is ".($value)."<br>";

if ($_POST['form'] == 'archivarCurso') {
	if ($_POST['btn-arch'] == 'archivar') {
		$error = 'warning';
		$rsp = '';

		desregistrarUsuariosCurso($_POST['IDcurso']);
		archivarCurso($_POST['IDcurso'], $_POST['anoAcademico']);

	} else if ($_POST['btn-arch'] == 'cancel') {
		$error = 'warning';
		$rsp = '';

	} else {
		$rsp = 'No se conoce la opci&oacute;n '.$_POST['btn-arch'];
		$error = 'danger';
	}

	if ($error == 'success') {
		$rsp = '?opt='.$_POST['opt'];
	}
}

echo $rsp;
?>